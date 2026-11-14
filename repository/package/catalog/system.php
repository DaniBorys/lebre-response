<?php
/**
 * Catálogo dos pacotes fundamentais do LEBRE.
 *
 * Revisão da documentação em 2026-11-11 20:46:39 UTC−03:00.
 */
declare(strict_types=1);

namespace Repository\Package\Catalog;

use Lebre\Package\CatalogInterface;
/**
 * Define a raiz de instalação e os caminhos dos manifestos
 * dos pacotes fundamentais do LEBRE.
 *
 * @package Lebre\Package\Catalog
 * @version 1.0.0
 * @since 2026-11-01
 */
final class System implements CatalogInterface
{
    /**
     * Retorna o diretório raiz para a resolução dos caminhos dos manifestos.
     *
     * @return string Caminho absoluto no Windows, terminado em barra (/).
     */
    public function getRoot(): string
    {
        return 'E:/xampp/htdocs/LEBRE_026/';
    }

    /**
     * Retorna o caminho do manifesto de cada pacote, relativo a getRoot().
     *
     * @return array<string, string> Identificadores dos pacotes como chaves e caminhos dos manifestos como valores.
     */
    public function getPackages(): array
    {
        return [
            'lebre-response'    => 'repository/package/lebre/response/manifest.php',
            'lebre-package'     => 'repository/package/lebre/package/manifest.php'
        ];
    }
}