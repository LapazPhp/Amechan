<?php
namespace Lapaz\Amechan;

interface UrlCollectableInterface
{
    /**
     * @param string $section
     * @return array
     */
    public function collectUrls($section = null);
}
