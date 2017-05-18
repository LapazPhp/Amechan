<?php
namespace Lapaz\Amechan\Mapper;

use Lapaz\Amechan\UrlMapperInterface;
use Webmozart\PathUtil\Path;

/**
 * Maps URLs which compiled JSes/CSSes are built from.
 *
 * ```
 * $mapping = [
 *     // 'compiled'     => ['sources', ...]
 *     'css/all.min.css' => ['css/bootstrap.css'],
 *     'js/all.min.js'   => ['js/jquery.js', 'js/bootstrap.js'],
 * ]
 * ```
 */
class UnifiedResourceMapper implements UrlMapperInterface
{
    /**
     * URL mappings of public source to compiled one
     *
     * @var array
     */
    protected $mapping;

    /**
     * UnifiedResourceMapper constructor.
     *
     * @param string $baseUrl URL prefix.
     * @param array $mapping Compiled to sources map.
     */
    public function __construct($baseUrl, $mapping)
    {
        $this->mapping = [];
        foreach ($mapping as $unified => $sources) {
            $sources = (array)$sources; // Ensure array not to be string

            if (!empty($baseUrl)) {
                $unified = Path::join([$baseUrl, $unified]);
                $sources = array_map(function ($s) use ($baseUrl) {
                    return Path::join([$baseUrl, $s]);
                }, $sources);
            }

            foreach ($sources as $s) {
                $this->mapping[$s] = $unified;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function apply($url)
    {
        if (isset($this->mapping[$url])) {
            return $this->mapping[$url];
        } else {
            return $url;
        }
    }
}
