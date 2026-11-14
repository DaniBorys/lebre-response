<?php
/**
 * Repositório de manifestos dos pacotes do LEBRE.
 *
 * Revisão da documentação em 2026-11-11 20:50:34 UTC−03:00.
 */
declare(strict_types=1);

namespace Lebre\Package;

use Lebre\Response\Error;
use Lebre\Response\Response;
use RuntimeException;
use Throwable;
/**
 * Reúne os registros dos catálogos e carrega os manifestos sob demanda.
 *
 * Mantém em cache, por caminho completo e durante a vida da instância,
 * os manifestos que retornam um array.
 *
 * @package Lebre\Package
 * @version 1.0.0
 * @since 2026-11-02
 */
final class Repository
{
    /**
     * Registros indexados pelo identificador do pacote. Cada registro
     * contém a raiz do catálogo e o caminho relativo do manifesto.
     *
     * @var array<string, array{root: string, manifest: string}>
     */
    private array $packages = [];

    /**
     * Manifestos carregados, indexados pelo caminho completo do arquivo.
     *
     * @var array<string, array<array-key, mixed>>
     */
    private array $manifests = [];

    /**
     * Reúne os pacotes dos catálogos e associa cada pacote à raiz do seu catálogo.
     *
     * @param CatalogInterface ...$catalogs Catálogos de pacotes a registrar.
     * @throws RuntimeException Quando um identificador de pacote aparece mais de uma vez.
     */
    public function __construct(CatalogInterface ...$catalogs) {
        foreach ($catalogs as $catalog) {
            $root = $catalog->getRoot();

            foreach ($catalog->getPackages() as $package => $manifest) {
                if (isset($this->packages[$package])) {
                    throw new RuntimeException(Code::PACKAGE_DUPLICATE . ': ' . $package);
                }
                $this->packages[$package] = ['root' => $root, 'manifest' => $manifest];
            }
        }
    }

    /**
     * Obtém o manifesto de um pacote registrado.
     *
     * Reutiliza o manifesto em cache quando disponível. Na ausência de uma
     * entrada em cache, executa o arquivo PHP e armazena o resultado se for um array.
     *
     * @param string $package Identificador do pacote.
     * @return Response Resposta com o array do manifesto ou com um dos erros
     *                  package-not-found, package-file-not-found ou package-invalid-manifest.
     * @throws Throwable Se a inclusão ou a execução do manifesto lançar uma exceção ou um erro.
     */
    public function get(string $package): Response
    {
        if (!isset($this->packages[$package])) {
            return Response::error(new Error(Code::PACKAGE_NOT_FOUND, ['package' => $package]));
        }

        $record = $this->packages[$package];
        $file   = $record['root'] . $record['manifest'];

        if (array_key_exists($file, $this->manifests)) {
            return Response::success($this->manifests[$file]);
        }

        if (!is_file($file)) {
            return Response::error(new Error(Code::PACKAGE_FILE_NOT_FOUND, ['package' => $package, 'file' => $file]));
        }

        try {
            $manifest = self::read($file);
        } catch (Throwable $exception) {
            throw $exception;
        }

        if (!is_array($manifest)) {
            return Response::error(new Error(Code::PACKAGE_INVALID_MANIFEST, ['package' => $package, 'file' => $file]));
        }

        $this->manifests[$file] = $manifest;
        return Response::success($manifest);
    }

    /**
     * Consulta a raiz do catálogo ao qual o pacote pertence.
     *
     * @param string $package Identificador do pacote.
     * @return Response Resposta com o caminho da raiz ou com o erro package-not-found.
     */
    public function getRoot(string $package): Response
    {
        if (!isset($this->packages[$package])) {
            return Response::error(new Error(Code::PACKAGE_NOT_FOUND, ['package' => $package]));
        }
        return Response::success($this->packages[$package]['root']);
    }

    /**
     * Retorna os registros dos pacotes indexados pelo identificador.
     *
     * @return Response Resposta de sucesso com um array de registros. Cada registro
     *                  contém a raiz do catálogo em `root` e o caminho relativo
     *                  do manifesto em `manifest`. O array pode estar vazio.
     */
    public function getPackages(): Response
    {
        return Response::success($this->packages);
    }

    /**
     * Inclui e executa o arquivo PHP do manifesto no escopo deste método.
     *
     * @param string $file Caminho completo do manifesto.
     * @return mixed Valor produzido pela expressão require.
     * @throws Throwable Se a inclusão ou a execução do manifesto lançar uma exceção ou um erro.
     */
    private static function read(string $file): mixed
    {
        return require $file;
    }
}