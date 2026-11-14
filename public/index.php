<?php

declare(strict_types=1);

function dump(string $title, mixed $value)
{
    echo '<pre>';
    echo $title . "\n";
    var_dump($value);
    echo '</pre>';
}

$root = dirname(__DIR__) . '/';

require_once $root . 'src/response/response.php';
require_once $root . 'src/response/error.php';

require_once $root . 'src/package/code.php';
require_once $root . 'src/package/cataloginterface.php';
require_once $root . 'src/package/repository.php';
require_once $root . 'src/package/dependency.php';
require_once $root . 'src/package/mapping.php';
require_once $root . 'src/package/autoload.php';

require_once $root . 'service/package.php';

require_once $root . 'repository/package/catalog/system.php';
require_once $root . 'repository/package/catalog/example.php';

$catalogSystem      = new \Repository\Package\Catalog\System();
$catalogExample     = new \Repository\Package\Catalog\Example();

try {

    $service    = new \Service\Package($catalogSystem, $catalogExample);
    $resolved   = $service->resolve('lebre-package', 'lebre-example');

    if (!$resolved->isApproved()) {
        dump('Erro na resolução', $resolved);
        exit();
    }

    $plan       = $resolved->getContent();

    // dump('Mapeamento', $plan['mapping']);
    // dump('Ordem de carregamento', $plan['dependency']);
    // dump('Arquivos de autoload', $plan['autoload']);

    // Chame esta etapa quando quiser executar o plano.
    $loaded     = $service->load($plan);

    if (!$loaded->isApproved()) {
        dump('Erro no autoload', $loaded->getErrors());
        exit();
    }

    dump('Pacotes carregados', $loaded->getContent());

} catch (Exception $e) {
    dump('e', $e);
}
