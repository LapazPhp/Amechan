<?php
namespace Lapaz\Amechan;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AssetTest extends TestCase
{
    /**
     * @var AssetManager|MockObject
     */
    protected $manager;

    public function testCollectUrls()
    {
        $asset = new Asset($this->manager, '/js', [
            'jquery.js',
            'bootstrap.js',
        ]);

        $urls = $asset->collectUrls();
        $this->assertEquals([
            '/js/jquery.js',
            '/js/bootstrap.js',
        ], $urls);
    }

    public function testCollectUrlsWithSection()
    {
        $asset = new Asset($this->manager, '/css', [
            'bootstrap.css',
        ], 'css');

        $urls = $asset->collectUrls('css');
        $this->assertEquals([
            '/css/bootstrap.css',
        ], $urls);

        $urls = $asset->collectUrls('js');
        $this->assertEmpty($urls);
    }

    public function testCollectUrlsWithDependenciesByObject()
    {
        $jqueryJs = new Asset($this->manager, '/js', [
            'jquery.js'
        ], 'js');

        $bootstrapJs = new Asset($this->manager, '/js', [
            'bootstrap.js',
        ], 'js', [
            $jqueryJs
        ]);

        $urls = $bootstrapJs->collectUrls('js');
        $this->assertEquals([
            '/js/jquery.js',
            '/js/bootstrap.js',
        ], $urls, "Urls must be ordered according to dependency chain");
    }

    public function testCollectUrlsWithDependenciesByName()
    {
        $jqueryJs = new Asset($this->manager, '/js', [
            'jquery.js'
        ], 'js');

        $this->manager->method('has')->willReturn(true);
        $this->manager->method('get')->willReturnMap([
            ['jquery', $jqueryJs],
        ]);


        $bootstrapJs = new Asset($this->manager, '/js', [
            'bootstrap.js',
        ], 'js', [
            'jquery'
        ]);

        $urls = $bootstrapJs->collectUrls('js');
        $this->assertEquals([
            '/js/jquery.js',
            '/js/bootstrap.js',
        ], $urls, "Urls must be ordered according to dependency chain");
    }

    public function testMissingDependencyName()
    {
        $this->expectException(\RuntimeException::class);

        $this->manager->method('has')->willReturn(false);

        $bootstrapJs = new Asset($this->manager, '/js', [
            'bootstrap.js',
        ], 'js', [
            'jquery'
        ]);

        $bootstrapJs->collectUrls('js');
    }

    public function testCollectUrlsWithSectionAndDependencies()
    {
        $jqueryJs = new Asset($this->manager, '/js', [
            'jquery.js'
        ], 'js');

        $bootstrapJs = new Asset($this->manager, '/js', [
            'bootstrap.js',
        ], 'js', [
            $jqueryJs
        ]);

        $bootstrapCss = new Asset($this->manager, '/css', [
            'bootstrap.css',
        ], 'css');

        $bootstrapBundle = new Asset($this->manager, '/', [], 'css', [
            $bootstrapJs,
            $bootstrapCss,
        ]);

        $cssUrls = $bootstrapBundle->collectUrls('css');
        $this->assertEquals([
            '/css/bootstrap.css',
        ], $cssUrls);

        $jsUrls = $bootstrapBundle->collectUrls('js');
        $this->assertEquals([
            '/js/jquery.js',
            '/js/bootstrap.js',
        ], $jsUrls);
    }

    protected function setUp(): void
    {
        $this->manager = $this->createMock(AssetManager::class);

        $this->manager->method('url')->willReturnCallback(function ($url) {
            return $url;
        });
    }
}
