<?php
/**
 * Exemplo de consulta ao repositório de pacotes do LEBRE.
 *
 * Registra os catálogos System e Example, exibe os pacotes registrados
 * e consulta o manifesto do pacote lebre-base.
 *
 * Revisão da documentação em 2026-11-11 20:52:22 UTC−03:00.
 */
declare(strict_types=1);

function dump(string $title, mixed $value)
{
    echo '<pre>';
    echo $title . "\n";
    var_dump($value);
    echo '</pre>';
}

$root = dirname(__DIR__, 4) . '/';

require_once $root . 'src/response/response.php';
require_once $root . 'src/response/error.php';

require_once $root . 'src/package/cataloginterface.php';
require_once $root . 'src/package/repository.php';

require_once $root . 'src/package/catalog/system.php';
require_once $root . 'src/package/catalog/example.php';

$catalogSystem      = new \Lebre\Package\Catalog\System();
$catalogExample     = new \Lebre\Package\Catalog\Example();

// dump('catalogSystem (ROOT)', $catalogSystem->getRoot());
// dump('catalogSystem (PACKAGE)', $catalogSystem->getPackages());

try {

    $repositorySystem   = new \Lebre\Package\Repository($catalogSystem, $catalogExample);

    dump('repositorySystem->getPackages()', $repositorySystem->getPackages());
    dump('repositorySystem->get()', $repositorySystem->get('lebre-base'));

} catch (Exception $e) {
    dump('e', $e);
}
