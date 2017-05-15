<?php
namespace Lapaz\Amechan;

class AssetCollection implements UrlCollectableInterface
{
    /**
     * AssetManager reference as dependency/mapping resolver
     *
     * @var AssetManager
     */
    protected $manager;

    /**
     * Asset like objects which added the collection
     *
     * @var UrlCollectableInterface[]
     */
    protected $assets;

    /**
     * AssetCollection constructor.
     *
     * @param AssetManager $manager Reference to AssetManager. This reference used when resolving dependency/mapping.
     */
    public function __construct(AssetManager $manager)
    {
        $this->manager = $manager;
        $this->assets = [];
    }

    /**
     * Adds an asset to this collection.
     *
     * @param UrlCollectableInterface|string $asset Asset object or registration name in AssetManager.
     */
    public function add($asset)
    {
        if (!($asset instanceof UrlCollectableInterface)) {
            if (!$this->manager->has($asset)) {
                throw new \RuntimeException('No such asset: ' . $asset);
            }
            $asset = $this->manager->get($asset);
        }

        $this->assets[] = $asset;
    }

    /**
     * @inheritDoc
     */
    public function collectUrls($section = null)
    {
        $urls = [];
        foreach ($this->assets as $asset) {
            $urls = array_merge($urls, $asset->collectUrls($section));
        }
        return array_values(array_unique($urls));
    }
}
