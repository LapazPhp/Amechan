<?php
use Lapaz\Amechan\AssetManager;

$app = new Silex\Application();

$app['plates'] = $app->factory(function () use ($app) {
    $plates = new League\Plates\Engine(__DIR__ . '/../templates');

    $amechan = $app['amechan'];
    assert($amechan instanceof AssetManager);

    $plates->addData([
        'app' => $app,
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
});

// IDE helper
interface IdeAssetSupportHelperInterface
{
    /**
     * @return \Lapaz\Amechan\AssetCollection
     */
    public function assets();

    /**
     * @param string $url
     * @return string
     */
    public function assetUrl($url);
}

$app['amechan'] = function () {
    $am = new AssetManager();
    require __DIR__ . '/assets.php';
    return $am;
};

return $app;
