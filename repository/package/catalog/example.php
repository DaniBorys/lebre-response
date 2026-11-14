<?php
/**
 * Catálogo de pacotes de exemplo do LEBRE.
 *
 * Revisão da documentação em 2026-11-11 20:44:32 UTC−03:00.
 */
declare(strict_types=1);

namespace Repository\Package\Catalog;

use Lebre\Package\CatalogInterface;
/**
 * Fornece os dados de instalação dos pacotes de exemplo do LEBRE.
 *
 * Mantém a raiz de instalação e os caminhos relativos dos manifestos,
 * indexados pelo identificador de cada pacote.
 *
 * @package Lebre\Package\Catalog
 * @version 1.0.0
 * @since 2026-11-01
 */
final class Example implements CatalogInterface
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
            'lebre-example' => 'repository/package/lebre/example/manifest.php',
        ];
    }
}