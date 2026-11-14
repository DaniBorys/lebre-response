<?php
/**
 * Execução do plano de carregamento dos pacotes do LEBRE.
 *
 * Revisão da documentação em 2026-11-11 21:50:10 UTC−03:00.
 */
declare(strict_types=1);

namespace Lebre\Package;

use Lebre\Response\Error;
use Lebre\Response\Response;
use Throwable;
/**
 * Inclui os arquivos de autoload na ordem definida pelo plano.
 *
 * Mantém o registro de pacotes carregados por instância e o atualiza
 * após concluir todas as inclusões. Se uma inclusão falhar, os efeitos
 * das inclusões anteriores permanecem.
 *
 * @package Lebre\Package
 * @version 1.1.0
 * @since 2026-11-02
 */
final class Autoload
{
    /**
     * Conjunto de pacotes registrados como carregados, indexado pelo identificador.
     *
     * @var array<array-key, true>
     */
    private array $required = [];

    /**
     * Carrega os arquivos de um plano aprovado por Mapping::resolve().
     *
     * Retorna sucesso imediatamente se todos os pacotes do plano já estiverem
     * registrados ou se a lista de pacotes estiver vazia. Caso contrário,
     * verifica e inclui cada arquivo na ordem do plano. Em uma nova tentativa,
     * require_once evita executar novamente arquivos já incluídos.
     *
     * @param array{
     *     mapping: array<array-key, int>,
     *     dependency: list<string>,
     *     autoload: list<string>
     * } $plan Plano de carregamento com caminhos provenientes dos catálogos e manifestos.
     * @return Response Resposta com a lista de pacotes do plano ou com o erro
     *                  `package-file-not-found` ou `package-autoload-file-failed`.
     */
    public function load(array $plan): Response
    {
        $packages = $plan['dependency'];

        $complete = true;

        foreach ($packages as $package) {
            if (!isset($this->required[$package])) {
                $complete = false;
                break;
            }
        }

        if ($complete) {
            return Response::success($packages);
        }

        foreach ($plan['autoload'] as $file) {
            if (!is_file($file)) {
                return Response::error(
                    new Error(Code::PACKAGE_FILE_NOT_FOUND, [
                        'file' => $file,
                        'packages' => $packages,
                    ])
                );
            }

            try {
                self::read($file);
            } catch (Throwable $exception) {
                return Response::error(
                    new Error(Code::PACKAGE_AUTOLOAD_FILE_FAILED, [
                        'file' => $file,
                        'exception' => get_class($exception),
                        'message' => $exception->getMessage(),
                    ])
                );
            }
        }

        foreach ($packages as $package) {
            $this->required[$package] = true;
        }

        return Response::success($packages);
    }

    /**
     * Informa se o pacote está registrado como carregado nesta instância.
     *
     * @param string $package Identificador do pacote.
     * @return bool true quando o pacote está registrado.
     */
    public function isRequired(string $package): bool
    {
        return isset($this->required[$package]);
    }

    /**
     * Retorna os identificadores dos pacotes na ordem do primeiro registro.
     *
     * @return list<array-key> Identificadores conforme armazenados nas chaves do array.
     */
    public function getRequired(): array
    {
        return array_keys($this->required);
    }

    /**
     * Inclui o arquivo uma única vez no escopo deste método.
     *
     * @param string $file Caminho completo do arquivo.
     * @return void
     * @throws Throwable Se a inclusão ou a execução do arquivo lançar uma exceção ou um erro.
     */
    private static function read(string $file): void
    {
        require_once $file;
    }
}