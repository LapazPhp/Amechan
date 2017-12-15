<?php
namespace Lapaz\Amechan\Mapper;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;
use PHPUnit\Framework\TestCase;

class LocalFileHashMapperTest extends TestCase
{
    /**
     * @throws \org\bovigo\vfs\vfsStreamException
     */
    protected function setUp()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('css'));
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('js'));
    }

    public function testApply()
    {
        $content = 'jQuery implementation';
        file_put_contents(vfsStream::url('js/jquery.js'), $content);

        $mapper = new LocalFileHashMapper('/js', vfsStream::url('js'));

        $this->assertEquals('/js/jquery.js?' . md5($content), $mapper->apply('/js/jquery.js'));
        $this->assertEquals('/external/standalone.js', $mapper->apply('/external/standalone.js'));
    }

    public function testMatcherRegexp()
    {
        $content = 'jQuery implementation';
        file_put_contents(vfsStream::url('js/jquery.js'), $content);
        file_put_contents(vfsStream::url('js/bootstrap.js'), 'Bootstrap implementation');

        $mapper = new LocalFileHashMapper('/js', vfsStream::url('js'), '/jquery/');

        $this->assertEquals('/js/jquery.js?' . md5($content), $mapper->apply('/js/jquery.js'));
        $this->assertEquals('/js/bootstrap.js', $mapper->apply('/js/bootstrap.js'));
    }

    public function testMatcherFunction()
    {
        $content = 'jQuery implementation';
        file_put_contents(vfsStream::url('js/jquery.js'), $content);
        file_put_contents(vfsStream::url('js/bootstrap.js'), 'Bootstrap implementation');

        $mapper = new LocalFileHashMapper('/js', vfsStream::url('js'), function ($url) {
            return $url == '/js/jquery.js';
        });

        $this->assertEquals('/js/jquery.js?' . md5($content), $mapper->apply('/js/jquery.js'));
        $this->assertEquals('/js/bootstrap.js', $mapper->apply('/js/bootstrap.js'));
    }

    public function testParameter()
    {
        $content = 'jQuery implementation';
        file_put_contents(vfsStream::url('js/jquery.js'), $content);

        $mapper = new LocalFileHashMapper('/js', vfsStream::url('js'), null, 'v');

        $this->assertEquals('/js/jquery.js?v=' . md5($content), $mapper->apply('/js/jquery.js'));
    }

    public function testHashFunction()
    {
        $content = 'jQuery implementation';
        file_put_contents(vfsStream::url('js/jquery.js'), $content);

        $mapper = new LocalFileHashMapper('/js', vfsStream::url('js'), null, null, function ($file) {
            /** @noinspection PhpExpressionResultUnusedInspection */
            $file;
            return 'hash-hash-hash';
        });

        $this->assertEquals('/js/jquery.js?hash-hash-hash', $mapper->apply('/js/jquery.js'));
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testApplyToNonFile()
    {
        $mapper = new LocalFileHashMapper('/js', vfsStream::url('js'));
        $mapper->apply('/js/missing.js');
    }
}
