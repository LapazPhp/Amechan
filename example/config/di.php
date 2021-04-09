<?php
use Lapaz\Amechan\AssetManager;

return [
    'view' => function ($container) {
        $amechan = $container->get('amechan');
        assert($amechan instanceof AssetManager);

        $plates = new \League\Plates\Engine(__DIR__ . '/../templates');

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

        return $plates;
    },
    'amechan' => function () {
        $am = new AssetManager();
        require __DIR__ . '/assets.php';
        return $am;
    },
];
