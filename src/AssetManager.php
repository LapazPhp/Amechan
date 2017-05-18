<?php
namespace Lapaz\Amechan;

/**
 * AssetManager is the top level object of asset management.
 *
 * AssetManager contains all application assets and its mappings.
 *
 * ```
 * $assetManager->asset('foo', [...]);
 * $assetManager->asset('bar', [...]);
 *
 * $assets = $assetManager->newCollection();
 * $assets->add('foo');
 *
 * $assets->collectUrls('css'); // CSSes included by 'foo' and its dependencies
 * ```
 */
class AssetManager
{
    /**
     * Named and registered assets under management
     *
     * @var UrlCollectableInterface[]
     */
    protected $assets = [];

    /**
     * URL mapper pipeline
     *
     * @var UrlMapperInterface[]
     */
    protected $mapperPipeline = [];

    /**
     * Config array based Asset factory.
     *
     * `$config` elements:
     * - baseUrl: (string) URL prefix
     * - files: (array) Resources included
     * - file: (string) Singular form of `files`
     * - section: (string) Section name in HTML
     * - bundles: (array[]) Config array list describing nested assets
     * - dependencies: (string[]|UrlCollectable[]) Dependencies to pre-loaded assets
     * - dependency: (string|UrlCollectable) Singular form of `dependencies`
     *
     * @param array $config Asset creation config.
     * @return Asset New configured Asset instance.
     */
    public function newBundle(array $config = [])
    {
        $baseUrl = isset($config['baseUrl']) ? $config['baseUrl'] : '';

        if (isset($config['files'])) {
            $files = $config['files'];
        } elseif (isset($config['file'])) {
            $files = $config['file'];
        } else {
            $files = [];
        }
        if (!is_array($files)) {
            $files = [$files];
        }

        $section = isset($config['section']) ? $config['section'] : null;

        if (isset($config['dependencies'])) {
            $dependencies = $config['dependencies'];
        } elseif (isset($config['dependency'])) {
            $dependencies = $config['dependency'];
        } else {
            $dependencies = [];
        }
        if (!is_array($dependencies)) {
            $dependencies = [$dependencies];
        }
        foreach ($dependencies as $dependency) {
            if (!($dependency instanceof UrlCollectableInterface || is_scalar($dependency))) {
                throw new \InvalidArgumentException('Asset dependency must be string or Asset object');
            }
        }

        $bundles = [];
        if (isset($config['bundles'])) {
            if (!is_array($config['bundles'])) {
                throw new \InvalidArgumentException('Asset bundles must be array');
            }
            foreach ($config['bundles'] as $bundleConfig) {
                if (!is_array($bundleConfig)) {
                    throw new \InvalidArgumentException('Asset bundles definition must be array');
                }
                if (empty($bundleConfig['baseUrl']) || !is_string($bundleConfig['baseUrl'])) {
                    $bundleConfig['baseUrl'] = $baseUrl;
                }
                if (empty($bundleConfig['section']) || !is_string($bundleConfig['section'])) {
                    $bundleConfig['section'] = $section;
                }
                if (!isset($bundleConfig['dependencies']) && !isset($bundleConfig['dependency'])) {
                    $bundleConfig['dependencies'] = [];
                }
                if (isset($bundleConfig['dependency'])) {
                    $bundleConfig['dependencies'] = [$bundleConfig['dependency']];
                }
                $bundleConfig['dependencies'] = array_merge($dependencies, $bundleConfig['dependencies']);

                $bundles[] = $this->newBundle($bundleConfig);
            }
        }

        return new Asset($this, $baseUrl, $files, $section, array_merge($dependencies, $bundles));
    }

    /**
     * AssetCollection factory.
     *
     * @return AssetCollection Empty collection of assets.
     */
    public function newCollection()
    {
        // should be locked here
        return new AssetCollection($this);
    }

    /**
     * Registers an asset (or some UrlCollectableInterface) to the manager.
     *
     * @param string $name Registration name of asset.
     * @param UrlCollectableInterface $source Url source in most case Asset object.
     */
    public function set($name, UrlCollectableInterface $source)
    {
        $this->assets[$name] = $source;
    }

    /**
     * Returns the named asset is registered or not.
     *
     * @param string $name Asset name to be checked.
     * @return bool Existence of named asset.
     */
    public function has($name)
    {
        return isset($this->assets[$name]);
    }

    /**
     * Returns registered asset (or some UrlCollectableInterface) by name.
     *
     * @param string $name Asset name to be fetched.
     * @return UrlCollectableInterface Url source in most case Asset object.
     */
    public function get($name)
    {
        return $this->has($name) ? $this->assets[$name] : null;
    }

    /**
     * Defines and registers Asset by config array.
     * This is utility method to combine `newBundle()` and `set()`.
     *
     * @param string $name Registration name of asset.
     * @param array $config Asset creation config.
     * @see AssetManager::newBundle()
     * @see AssetManager::set()
     */
    public function asset($name, array $config = [])
    {
        $this->set($name, $this->newBundle($config));
    }

    /**
     * @param UrlMapperInterface $mapper
     */
    public function mapping(UrlMapperInterface $mapper)
    {
        $this->mapperPipeline[] = $mapper;
    }

    /**
     * Returns single URL applied managed mappings.
     *
     * @param string $url URL to source resource.
     * @return string Alternative URL if mapping applied.
     */
    public function url($url)
    {
        foreach ($this->mapperPipeline as $mapper) {
            $url = $mapper->apply($url);
        }
        return $url;
    }
}
