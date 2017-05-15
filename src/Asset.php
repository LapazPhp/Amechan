<?php
namespace Lapaz\Amechan;

use Webmozart\PathUtil\Path;

/**
 * Asset is a management unit of assets.
 *
 * Asset instance contains 0 or more files and dependencies. Then it can generate
 * an unique set of URLs.
 */
class Asset implements UrlCollectableInterface
{
    /**
     * AssetManager reference as dependency/mapping resolver
     *
     * @var AssetManager
     */
    protected $manager;

    /**
     * Common URL prefix for each files
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Resource files contained this unit
     *
     * @var array
     */
    protected $files;

    /**
     * Which section expanded to in HTML
     *
     * @var string
     */
    protected $section;

    /**
     * Required pre-loaded assets before this unit
     *
     * @var UrlCollectableInterface[]|string[]
     */
    protected $dependencies = [];

    /**
     * Asset constructor.
     *
     * @param AssetManager $manager Reference to AssetManager. This reference used when resolving dependency/mapping.
     * @param string $baseUrl URL prefix for `$files`. If empty specified files are assumed as absolute URLs.
     * @param array $files Linked resources. Empty allowed for virtual asset unit.
     * @param string|null $section HTML section to be expanded. If null the asset evaluated every sections.
     * @param UrlCollectableInterface[]|string[] $dependencies Required assets to be loaded before the asset.
     */
    public function __construct(AssetManager $manager, $baseUrl, array $files, $section = null, array $dependencies = [])
    {
        $this->manager = $manager;
        $this->baseUrl = $baseUrl;
        $this->files = $files;
        $this->section = $section;
        $this->dependencies = $dependencies;
    }

    /**
     * @inheritDoc
     */
    public function collectUrls($section = null)
    {
        $urls = $this->collectDependencyUrls($section);
        if ($this->matchesSectionTo($section)) {
            $urls = array_merge($urls, $this->ownUrls());
        }
        return array_values(array_unique($urls));
    }

    /**
     * Aggregates URLs form all dependencies.
     *
     * @param string $section Section name in HTML
     * @return array URL list of dependencies
     */
    protected function collectDependencyUrls($section)
    {
        $urls = [];
        foreach ($this->dependencies as $dependency) {
            $dependency = $this->ensureObject($dependency);
            $urls = array_merge($urls, $dependency->collectUrls($section));
        }
        return array_unique($urls);
    }

    /**
     * Ensures anything as URL collectable object.
     *
     * @param mixed $dependency Unknown typed asset reference.
     * @return UrlCollectableInterface Object which has `collectUrls()` method.
     */
    private function ensureObject($dependency)
    {
        if (!is_scalar($dependency)) {
            return $dependency;
        }

        if (!$this->manager->has($dependency)) {
            throw new \RuntimeException('Missing asset dependency found: ' . $dependency);
        }

        return $this->manager->get($dependency);
    }

    /**
     * Compare section name with this asset.
     *
     * @param string|null $section Section name. If null it returns `true` anyway.
     * @return bool Which specified section name matched or not.
     */
    private function matchesSectionTo($section)
    {
        return empty($this->section) || empty($section) || $section == $this->section;
    }

    /**
     * Returns URLs contained the asset itself. Dependency URLs are not included.
     *
     * @return array URL list
     */
    private function ownUrls()
    {
        return array_map(function ($file) {
            $url = Path::join($this->baseUrl, $file);
            return $this->manager->url($url);
        }, $this->files);
    }
}
