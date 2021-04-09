<?php
namespace Lapaz\Amechan;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AssetCollectionTest extends TestCase
{
    /**
     * @var AssetManager|MockObject
     */
    protected $manager;

    public function testCollectUrlsFromEmptyCollection()
    {
        $collection = new AssetCollection($this->manager);

        $urls = $collection->collectUrls();
        $this->assertEmpty($urls);
    }

    public function testCollectUrlsByObject()
    {
        $jqueryAsset = $this->createMock(Asset::class);
        $jqueryAsset->method('collectUrls')->willReturn([
            '/js/jquery.js',
        ]);

        $bootstrapAsset = $this->createMock(Asset::class);
        $bootstrapAsset->method('collectUrls')->willReturn([
            '/js/jquery.js',
            '/js/bootstrap.js',
        ]);

        $collection = new AssetCollection($this->manager);
        $collection->add($jqueryAsset);
        $urls = $collection->collectUrls();
        $this->assertEquals([
            '/js/jquery.js',
        ], $urls);

        $collection = new AssetCollection($this->manager);
        $collection->add($bootstrapAsset);
        $collection->add($jqueryAsset);
        $urls = $collection->collectUrls();
        $this->assertEquals([
            '/js/jquery.js',
            '/js/bootstrap.js',
        ], $urls);

        $collection = new AssetCollection($this->manager);
        $collection->add($bootstrapAsset);
        $urls = $collection->collectUrls();
        $this->assertEquals([
            '/js/jquery.js',
            '/js/bootstrap.js',
        ], $urls);
    }

    public function testCollectUrlsByName()
    {
        $jqueryAsset = $this->createMock(Asset::class);
        $jqueryAsset->method('collectUrls')->willReturn([
            '/js/jquery.js',
        ]);

        $bootstrapAsset = $this->createMock(Asset::class);
        $bootstrapAsset->method('collectUrls')->willReturn([
            '/js/jquery.js',
            '/js/bootstrap.js',
        ]);

        $this->manager->method('has')->willReturn(true);
        $this->manager->method('get')->willReturnMap([
            ['jquery', $jqueryAsset],
            ['bootstrap', $bootstrapAsset],
        ]);

        $collection = new AssetCollection($this->manager);
        $collection->add('jquery');
        $urls = $collection->collectUrls();
        $this->assertEquals([
            '/js/jquery.js',
        ], $urls);

        $collection = new AssetCollection($this->manager);
        $collection->add('bootstrap');
        $collection->add('jquery');
        $urls = $collection->collectUrls();
        $this->assertEquals([
            '/js/jquery.js',
            '/js/bootstrap.js',
        ], $urls);

        $collection = new AssetCollection($this->manager);
        $collection->add('bootstrap');
        $urls = $collection->collectUrls();
        $this->assertEquals([
            '/js/jquery.js',
            '/js/bootstrap.js',
        ], $urls);
    }

    public function testAddMissingName()
    {
        $this->expectException(\RuntimeException::class);

        $collection = new AssetCollection($this->manager);
        $collection->add('jquery');
    }

    protected function setUp(): void
    {
        $this->manager = $this->createMock(AssetManager::class);
    }
}
