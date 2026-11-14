<?php
/**
 * Declara as dependências e os arquivos de autoload de lebre-base.
 *
 * O pacote fornece Error e Response e tem uma lista de dependências vazia.
 * Os caminhos são relativos à raiz de instalação informada pelo catálogo.
 *
 * Revisão da documentação em 2026-11-11 22:04:14 UTC−03:00.
 *
 * @version 1.0.0
 * @since 2026-11-01
 */
declare(strict_types=1);

return [
    'dependency' => [],
    'autoload' => [
        'src/response/error.php',
        'src/response/response.php'
    ],
];