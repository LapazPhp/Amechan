<?php
namespace Lapaz\Amechan;

/**
 * Bridge for gap between asset source URL and production URL
 */
interface UrlMapperInterface
{
    /**
     * Converts URL from source to published
     *
     * @param string $url Source URL.
     * @return string Published URL.
     */
    public function apply($url);
}
