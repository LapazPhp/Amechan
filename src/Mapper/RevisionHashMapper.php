<?php
namespace Lapaz\Amechan\Mapper;

use Lapaz\Amechan\UrlMapperInterface;
use Webmozart\PathUtil\Path;

/**
 * Maps built resources to its revision hash appended version.
 *
 * ```
 * $manifest = [
 *     'css/all.min.css' => 'css/all-33f4c35457.min.css',
 *     'js/all.min.js' => 'js/all-5d8020ef9b.min.js',
 * ];
 * ```
 *
 * `$manifest` can be loaded from `rev-manifest.json` as:
 *
 * ```
 * json_decode(file_get_contents('local/path/to/rev-manifest.json'), true));
 * ```
 *
 * Hint: Revision hash is effective even for images and fonts not only CSS/JS.
 */
class RevisionHashMapper implements UrlMapperInterface
{
    /**
     * Revision hash mappings
     *
     * @var array
     */
    protected $manifest;

    /**
     * RevisionHashMapper constructor.
     *
     * @param string $baseUrl URL prefix.
     * @param array $manifest Revision hash manifest data.
     */
    public function __construct($baseUrl, $manifest)
    {
        $this->manifest = [];

        foreach ($manifest as $from => $to) {
            if (!empty($baseUrl)) {
                $from = Path::join([$baseUrl, $from]);
                $to = Path::join([$baseUrl, $to]);
            }
            $this->manifest[$from] = $to;
        }
    }

    /**
     * @inheritDoc
     */
    public function apply($url)
    {
        if (isset($this->manifest[$url])) {
            return $this->manifest[$url];
        } else {
            return $url;
        }
    }
}
