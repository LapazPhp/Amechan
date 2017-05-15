<?php
namespace Lapaz\Amechan;

interface UrlCollectableInterface
{
    /**
     * Aggregates URLs under management.
     *
     * @param string $section Which section in HTML to be aggregated. If null all Urls are returned.
     * @return array URL list
     */
    public function collectUrls($section = null);
}
