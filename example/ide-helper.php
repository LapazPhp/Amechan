<?php

// IDE helper for Plates functions
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
