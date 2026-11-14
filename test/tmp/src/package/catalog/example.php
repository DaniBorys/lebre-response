<?php

declare(strict_types=1);

function dump(string $title, mixed $value)
{
    echo '<pre>';
    echo $title . "\n";
    var_dump($value);
    echo '</pre>';
}

$root = dirname(__DIR__, 5) . '/';

require_once $root . 'src/package/cataloginterface.php';

require_once $root . 'src/package/catalog/example.php';

$catalog = new \Lebre\Package\Catalog\Example();

dump('catalog (ROOT)', $catalog->getRoot());
dump('catalog (PACKAGE)', $catalog->getPackages());
