<?php
/**
 * Declara as dependências e os arquivos de autoload de lebre-package.
 *
 * O pacote depende de lebre-base. A lista de autoload declara
 * CatalogInterface antes de Catalog\System, que a implementa.
 * Os caminhos são relativos à raiz de instalação informada pelo catálogo.
 *
 * Revisão da documentação em 2026-11-11 22:04:14 UTC−03:00.
 *
 * @version 1.0.0
 * @since 2026-11-01
 */

declare(strict_types=1);

return [
    'dependency' => [
        'lebre-response',
    ],
    'autoload' => [
        'src/package/code.php',
        'src/package/cataloginterface.php',
        'src/package/repository.php',
        'src/package/dependency.php',
        'src/package/mapping.php',
        'src/package/autoload.php',
        'service/package.php',
        'repository/package/catalog/system.php',
    ],
];