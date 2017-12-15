<?php
/** @var \Slim\Container $container */

use Lapaz\Amechan\AssetManager;

$container['view'] = function () use ($container) {

    $amechan = $container->get('amechan');
    assert($amechan instanceof AssetManager);

    $view = new \Carbontwelve\SlimPlates\PlatesRenderer(__DIR__ . '/../templates', 'php');

    $plates = $view->getEngine();
    $plates->addData([
        'container' => $container,
    ]);
    $plates->registerFunction('assetUrl', function ($url) use ($amechan) {
        return $amechan->url($url);
    });
    $assets = null;
    $plates->registerFunction('assets', function () use ($amechan, &$assets) {
        if ($assets === null) {
            $assets = $amechan->newCollection();
        }
        return $assets;
    });

    return $view;
};

$container['amechan'] = function () {
    $am = new AssetManager();
    require __DIR__ . '/assets.php';
    return $am;
};

return $app;
