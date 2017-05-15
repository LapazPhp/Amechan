<?php
namespace Lapaz\Amechan;

class AssetCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AssetManager|\PHPUnit_Framework_MockObject_MockObject
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

    /**
     * @expectedException \RuntimeException
     */
    public function testAddMissingName()
    {
        $collection = new AssetCollection($this->manager);
        $collection->add('jquery');
    }

    protected function setUp()
    {
        $this->manager = $this->createMock(AssetManager::class);
    }
}
