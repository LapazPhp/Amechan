<?php
namespace Lapaz\Amechan;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AssetManagerTest extends TestCase
{
    public function testNewBundle()
    {
        $manager = new AssetManager();

        $asset = $manager->newBundle([]);
        $this->assertInstanceOf(Asset::class, $asset);

        // single file
        $asset = $manager->newBundle([
            'baseUrl' => '/js',
            'file' => 'jquery.js',
        ]);
        $this->assertEquals([
            '/js/jquery.js',
        ], $asset->collectUrls());

        // multiple files
        $asset = $manager->newBundle([
            'baseUrl' => '/js',
            'files' => ['jquery.js', 'bootstrap.js'],
        ]);
        $this->assertEquals([
            '/js/jquery.js',
            '/js/bootstrap.js',
        ], $asset->collectUrls());

        // section
        $asset = $manager->newBundle([
            'baseUrl' => '/js',
            'file' => 'jquery.js',
            'section' => 'js',
        ]);
        $this->assertEquals([
            '/js/jquery.js',
        ], $asset->collectUrls('js'));
        $this->assertEmpty($asset->collectUrls('css'));
    }

    public function testNewBundleWithDependencyByObject()
    {
        $manager = new AssetManager();

        $jqueryAsset = $this->createMock(Asset::class);
        $jqueryAsset->method('collectUrls')->willReturn(['/js/jquery.js']);

        // single dependency
        $bootstrapAsset = $manager->newBundle([
            'baseUrl' => '/js',
            'file' => 'bootstrap.js',
            'dependency' => $jqueryAsset,
        ]);
        $this->assertEquals([
            '/js/jquery.js',
            '/js/bootstrap.js',
        ], $bootstrapAsset->collectUrls());

        // nested dependencies
        $asset = $manager->newBundle([
            'baseUrl' => '/js',
            'file' => 'app.js',
            'dependency' => $bootstrapAsset,
        ]);
        $this->assertEquals([
            '/js/jquery.js',
            '/js/bootstrap.js',
            '/js/app.js',
        ], $asset->collectUrls());

        // multiple dependencies
        $asset = $manager->newBundle([
            'baseUrl' => '/js',
            'file' => 'app.js',
            'dependencies' => [
                $jqueryAsset,
                $bootstrapAsset,
            ],
        ]);
        $this->assertEquals([
            '/js/jquery.js',
            '/js/bootstrap.js',
            '/js/app.js',
        ], $asset->collectUrls());
    }

    public function testNewBundleWithDependencyByName()
    {
        $manager = new AssetManager();

        $jqueryAsset = $this->createMock(Asset::class);
        $jqueryAsset->method('collectUrls')->willReturn(['/js/jquery.js']);
        assert($jqueryAsset instanceof Asset);
        $manager->set('jquery', $jqueryAsset);

        // single dependency
        $bootstrapAsset = $manager->newBundle([
            'baseUrl' => '/js',
            'file' => 'bootstrap.js',
            'dependency' => 'jquery',
        ]);
        $this->assertEquals([
            '/js/jquery.js',
            '/js/bootstrap.js',
        ], $bootstrapAsset->collectUrls());

        $manager->set('bootstrap', $bootstrapAsset);

        // nested dependencies
        $asset = $manager->newBundle([
            'baseUrl' => '/js',
            'file' => 'app.js',
            'dependency' => 'bootstrap',
        ]);
        $this->assertEquals([
            '/js/jquery.js',
            '/js/bootstrap.js',
            '/js/app.js',
        ], $asset->collectUrls());

        // multiple dependencies
        $asset = $manager->newBundle([
            'baseUrl' => '/js',
            'file' => 'app.js',
            'dependencies' => [
                'jquery',
                'bootstrap',
            ],
        ]);
        $this->assertEquals([
            '/js/jquery.js',
            '/js/bootstrap.js',
            '/js/app.js',
        ], $asset->collectUrls());
    }

    public function testNewSubBundleAggregationBundle()
    {
        $manager = new AssetManager();

        $jqueryAsset = $this->createMock(Asset::class);
        $jqueryAsset->method('collectUrls')->willReturnMap([
            ['css', []],
            ['js', ['/js/jquery.js']],
        ]);

        $bootstrapAsset = $manager->newBundle([
            'bundles' => [
                [
                    'baseUrl' => '/css',
                    'file' => 'bootstrap.css',
                    'section' => 'css',
                ],
                [
                    'baseUrl' => '/js',
                    'file' => 'bootstrap.js',
                    'section' => 'js',
                ]
            ],
            'dependency' => $jqueryAsset,
        ]);
        $this->assertEquals([
            '/css/bootstrap.css',
        ], $bootstrapAsset->collectUrls('css'));

        $this->assertEquals([
            '/js/jquery.js',
            '/js/bootstrap.js',
        ], $bootstrapAsset->collectUrls('js'));
    }

    public function testInvalidSubBundle()
    {
        $this->expectException(\InvalidArgumentException::class);

        $manager = new AssetManager();

        $manager->newBundle([
            'bundles' => "bad param",
        ]);
    }

    public function testInvalidSubBundleContent()
    {
        $this->expectException(\InvalidArgumentException::class);
        $manager = new AssetManager();

        $manager->newBundle([
            'bundles' => [
                "bad param",
            ],
        ]);
    }

    public function testNewCollection()
    {
        $manager = new AssetManager();
        $collection = $manager->newCollection();
        $this->assertInstanceOf(AssetCollection::class, $collection);
    }

    public function testGetterSetter()
    {
        $manager = new AssetManager();

        $jqueryAsset = $this->createMock(Asset::class);
        $jqueryAsset->method('collectUrls')->willReturn(['/js/jquery.js']);
        assert($jqueryAsset instanceof Asset);
        $manager->set('jquery', $jqueryAsset);

        $this->assertTrue($manager->has('jquery'));
        $this->assertFalse($manager->has('non-existence'));

        $this->assertNotNull($manager->get('jquery'));
        $this->assertNull($manager->get('non-existence'));

        $asset = $manager->get('jquery');
        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertEquals(['/js/jquery.js'], $asset->collectUrls());
    }

    public function testDefineAsset()
    {
        $manager = new AssetManager();

        $manager->asset('jquery', [
            'file' => '/js/jquery.js',
        ]);

        $this->assertTrue($manager->has('jquery'));
        $asset = $manager->get('jquery');
        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertEquals(['/js/jquery.js'], $asset->collectUrls());
    }

    public function testSingleUrlMapping()
    {
        /** @var UrlMapperInterface|MockObject $mapper */
        $mapper = $this->createMock(UrlMapperInterface::class);
        $mapper->method('apply')->willReturnMap([
            ['/js/jquery.js', '/js/jquery.min.js'],
        ]);

        $manager = new AssetManager();
        $manager->mapping($mapper);
        $this->assertEquals('/js/jquery.min.js', $manager->url('/js/jquery.js'));
    }

    public function testMultipleUrlMapping()
    {
        /** @var UrlMapperInterface|MockObject $mapper */
        $mapper = $this->createMock(UrlMapperInterface::class);
        $mapper->method('apply')->willReturnMap([
            ['/js/jquery.js', '/js/jquery.min.js'],
        ]);

        /** @var UrlMapperInterface|MockObject $mapper2 */
        $mapper2 = $this->createMock(UrlMapperInterface::class);
        $mapper2->method('apply')->willReturnMap([
            ['/js/jquery.min.js', '/js/jquery-0123456789.min.js'],
        ]);

        $manager = new AssetManager();
        $manager->mapping($mapper);
        $manager->mapping($mapper2);
        $this->assertEquals('/js/jquery-0123456789.min.js', $manager->url('/js/jquery.js'));
    }
}
