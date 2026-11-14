<?php
/**
 * Planejamento do carregamento dos pacotes do LEBRE.
 *
 * Revisão da documentação em 2026-11-11 21:21:35 UTC−03:00.
 */
declare(strict_types=1);

namespace Lebre\Package;

use Lebre\Response\Error;
use Lebre\Response\Response;
use Throwable;
/**
 * Reúne os manifestos dos pacotes solicitados, contabiliza suas referências
 * e prepara a ordem de carregamento e os caminhos de autoload.
 *
 * A execução dos arquivos de autoload cabe ao consumidor do plano.
 *
 * @package Lebre\Package
 * @version 1.1.0
 * @since 2026-11-02
 */
final class Mapping
{
    /**
     * Repositório consultado para obter os manifestos e as raízes dos pacotes.
     */
    private readonly Repository $repository;

    /**
     * Inicializa o mapeamento com o repositório de pacotes.
     *
     * @param Repository $repository Origem dos manifestos e das raízes dos catálogos.
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Prepara o plano de carregamento para os pacotes solicitados.
     *
     * O conteúdo da resposta aprovada tem três chaves.
     *
     * - `mapping` contém a contagem de visitas por identificador de pacote.
     *   Conta os pedidos diretos e as visitas durante a coleta de dependências.
     * - `dependency` contém os identificadores na ordem de carregamento,
     *   incluindo os pacotes solicitados.
     * - `autoload` contém os caminhos formados pela raiz de cada pacote e
     *   pelos itens declarados no manifesto. Caminhos textualmente iguais
     *   aparecem uma única vez, na posição da primeira ocorrência.
     *
     * A coleta antecede a detecção de ciclos em Dependency::resolve().
     * Uma dependência cíclica pode manter a coleta recursiva em execução
     * até o ambiente interrompê-la.
     *
     * @param string ...$packages Identificadores dos pacotes solicitados.
     * @return Response Resposta com o plano completo ou com o primeiro erro
     *                  retornado durante a coleta, a ordenação ou a montagem
     *                  dos caminhos de autoload.
     * @throws Throwable Se a inclusão ou a execução de um manifesto lançar
     *                   uma exceção ou um erro.
     */
    public function resolve(string ...$packages): Response
    {
        $manifests = [];
        $mapping = [];

        foreach ($packages as $package) {
            $failure = $this->collect($package, $manifests, $mapping);

            if ($failure !== null) {
                return $failure;
            }
        }

        $response = Dependency::resolve($packages, $manifests);

        if (!$response->isApproved()) {
            return $response;
        }

        $order = $response->getContent();
        $files = [];
        $included = [];

        foreach ($order as $package) {
            $manifest = $manifests[$package];

            if (!isset($manifest['autoload']) || !is_array($manifest['autoload']) || !array_is_list($manifest['autoload'])) {
                return Response::error(new Error(Code::PACKAGE_INVALID_AUTOLOAD, ['package' => $package]));
            }

            $response = $this->repository->getRoot($package);

            if (!$response->isApproved()) {
                return $response;
            }

            $root = $response->getContent();

            foreach ($manifest['autoload'] as $file) {
                if (!is_string($file) || $file === '') {
                    return Response::error(new Error(Code::PACKAGE_INVALID_AUTOLOAD, ['package' => $package, 'file' => $file]));
                }

                $path = $root . $file;

                if (!isset($included[$path])) {
                    $included[$path] = true;
                    $files[] = $path;
                }
            }
        }

        return Response::success(['mapping' => $mapping, 'dependency' => $order, 'autoload' => $files]);
    }

    /**
     * Conta a visita ao pacote, registra seu manifesto e coleta suas dependências.
     *
     * A verificação de um manifesto já registrado cria uma resposta de erro
     * que é descartada. A coleta prossegue pela mesma lista de dependências
     * a cada nova visita ao pacote.
     *
     * @param string $package Identificador do pacote visitado.
     * @param array<array-key, array<array-key, mixed>> $manifests Manifestos acumulados por identificador.
     * @param array<array-key, int> $mapping Contagem acumulada de visitas por identificador.
     * @return Response|null Resposta de falha do repositório, erro de validação
     *                       das dependências ou null quando a coleta é concluída.
     * @throws Throwable Se a inclusão ou a execução de um manifesto lançar
     *                   uma exceção ou um erro.
     */
    private function collect(string $package, array &$manifests, array &$mapping): ?Response {
        $mapping[$package] = ($mapping[$package] ?? 0) + 1;

        if (array_key_exists($package, $manifests)) {
            Response::error(new Error(Code::PACKAGE_DEPENDENCY_NOT_FOUND, ['package' => $package]));
        }

        $response = $this->repository->get($package);

        if (!$response->isApproved()) {
            return $response;
        }

        $manifest = $response->getContent();

        if (!isset($manifest['dependency']) || !is_array($manifest['dependency']) || !array_is_list($manifest['dependency'])) {
            return Response::error(new Error(Code::PACKAGE_DEPENDENCY_INVALID_MANIFEST, ['package' => $package]));
        }

        $manifests[$package] = $manifest;

        foreach ($manifest['dependency'] as $dependency) {
            if (!is_string($dependency) || $dependency === '') {
                return Response::error(new Error(Code::PACKAGE_DEPENDENCY_INVALID_PACKAGE, ['package' => $package, 'dependency' => $dependency]));
            }

            $failure = $this->collect($dependency, $manifests, $mapping);

            if ($failure !== null) {
                return $failure;
            }
        }

        return null;
    }
}