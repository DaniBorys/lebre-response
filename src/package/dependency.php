<?php
/**
 * Resolução da ordem de carregamento dos pacotes do LEBRE.
 *
 * Revisão da documentação em 2026-11-11 21:16:02 UTC−03:00.
 */
declare(strict_types=1);

namespace Lebre\Package;

use Lebre\Response\Error;
use Lebre\Response\Response;
/**
 * Ordena os pacotes solicitados e suas dependências a partir dos manifestos
 * fornecidos. Cada pacote aparece uma única vez, depois das dependências
 * que declara.
 *
 * @package Lebre\Package
 * @version 1.0.0
 * @since 2026-11-02
 */
final class Dependency
{
    /**
     * Impede a instanciação da classe utilitária.
     */
    private function __construct() {}

    /**
     * Calcula a ordem de carregamento por busca em profundidade.
     *
     * Percorre os pacotes solicitados e as dependências de cada manifesto na
     * ordem em que foram fornecidos. Valida os manifestos à medida que os visita.
     * Cada manifesto visitado deve conter a chave `dependency` com uma lista
     * de strings não vazias. A lista pode estar vazia.
     *
     * @param list<string> $packages Identificadores dos pacotes solicitados.
     * @param array<array-key, mixed> $manifests Manifestos indexados pelo identificador do pacote.
     * @return Response Resposta com a lista ordenada de identificadores, incluindo
     *                  os pacotes solicitados, ou com o primeiro erro encontrado.
     *                  Uma lista de pacotes vazia produz uma lista de saída vazia.
     */
    public static function resolve(
        array $packages,
        array $manifests
    ): Response {
        $resolved = [];
        $visiting = [];
        $path = [];
        $order = [];

        foreach ($packages as $package) {
            $error = self::visit(
                $package,
                $manifests,
                $resolved,
                $visiting,
                $path,
                $order
            );

            if ($error !== null) {
                return Response::error($error);
            }
        }

        return Response::success($order);
    }

    /**
     * Visita um pacote e acrescenta seu identificador à ordem de carregamento
     * após concluir a visita a todas as suas dependências.
     *
     * Retorna `package-dependency-cycle` quando o pacote já está no percurso ativo.
     * O contexto `cycle` contém o trecho cíclico, com o identificador inicial
     * repetido no fim. Retorna `package-dependency-not-found` quando o manifesto
     * está ausente, `package-dependency-invalid-manifest` quando o manifesto ou
     * a estrutura de `dependency` é inválida e `package-dependency-invalid-package`
     * quando um item da lista de dependências é inválido.
     *
     * @param string $package Identificador do pacote em avaliação.
     * @param array<array-key, mixed> $manifests Manifestos indexados pelo identificador do pacote.
     * @param array<array-key, true> $resolved Conjunto dos pacotes já concluídos.
     * @param array<array-key, int> $visiting Posição de cada pacote ativo em $path.
     * @param list<string> $path Identificadores dos pacotes no percurso ativo.
     * @param list<string> $order Ordem de carregamento acumulada.
     * @return Error|null Primeiro erro encontrado ou null quando o pacote já está resolvido ou é concluído com sucesso.
     */
    private static function visit(
        string $package,
        array $manifests,
        array &$resolved,
        array &$visiting,
        array &$path,
        array &$order
    ): ?Error {
        // Reutiliza a resolução de uma dependência compartilhada.
        if (isset($resolved[$package])) {
            return null;
        }

        // Uma nova visita ao percurso ativo identifica um ciclo.
        if (isset($visiting[$package])) {
            $cycle = array_slice($path, $visiting[$package]);
            $cycle[] = $package;

            return new Error(Code::PACKAGE_DEPENDENCY_CYCLE, [
                'package' => $package,
                'cycle' => $cycle,
            ]);
        }

        if (!array_key_exists($package, $manifests)) {
            return new Error(Code::PACKAGE_DEPENDENCY_NOT_FOUND, [
                'package' => $package,
            ]);
        }

        $manifest = $manifests[$package];

        if (
            !is_array($manifest) ||
            !isset($manifest['dependency']) ||
            !is_array($manifest['dependency']) ||
            !array_is_list($manifest['dependency'])
        ) {
            return new Error(Code::PACKAGE_DEPENDENCY_INVALID_MANIFEST, [
                'package' => $package,
            ]);
        }

        $visiting[$package] = count($path);
        $path[] = $package;

        foreach ($manifest['dependency'] as $dependency) {
            if (!is_string($dependency) || $dependency === '') {
                return new Error(Code::PACKAGE_DEPENDENCY_INVALID_PACKAGE, [
                    'package' => $package,
                    'dependency' => $dependency,
                ]);
            }

            $error = self::visit(
                $dependency,
                $manifests,
                $resolved,
                $visiting,
                $path,
                $order
            );

            if ($error !== null) {
                return $error;
            }
        }

        // Registra o pacote somente após concluir suas dependências.
        array_pop($path);
        unset($visiting[$package]);

        $resolved[$package] = true;
        $order[] = $package;

        return null;
    }
}