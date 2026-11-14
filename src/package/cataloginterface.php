<?php
/**
 * Contrato dos catálogos de pacotes do LEBRE.
 *
 * Revisão da documentação em 2026-11-11 20:48:32 UTC−03:00.
 */
declare(strict_types=1);

namespace Lebre\Package;
/**
 * Define como cada catálogo fornece sua raiz e associa identificadores
 * de pacotes aos caminhos relativos de seus manifestos.
 *
 * @package Lebre\Package
 * @version 1.0.0
 * @since 2026-11-01
 */
interface CatalogInterface
{
    /**
     * Retorna o diretório raiz para a resolução dos caminhos dos manifestos.
     *
     * @return string Caminho absoluto terminado com um separador de diretório.
     */
    public function getRoot(): string;

    /**
     * Retorna o caminho do manifesto de cada pacote, relativo a getRoot().
     *
     * @return array<string, string> Identificadores dos pacotes como chaves e caminhos relativos dos manifestos como valores.
     */
    public function getPackages(): array;
}