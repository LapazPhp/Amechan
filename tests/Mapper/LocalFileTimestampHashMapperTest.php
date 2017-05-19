<?php
namespace Lapaz\Amechan\Mapper;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class LocalFileTimestampHashMapperTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('css'));
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('js'));
    }

    public function testApply()
    {
        file_put_contents(vfsStream::url('js/jquery.js'), 'jQuery implementation');
        $timestamp = filemtime(vfsStream::url('js/jquery.js'));

        $mapper = new LocalFileTimestampHashMapper('/js', vfsStream::url('js'));

        $this->assertEquals('/js/jquery.js?' . md5($timestamp), $mapper->apply('/js/jquery.js'));
    }

    public function testHashFunction()
    {
        file_put_contents(vfsStream::url('js/jquery.js'), 'jQuery implementation');

        $mapper = new LocalFileTimestampHashMapper('/js', vfsStream::url('js'), null, null, function ($file) {
            /** @noinspection PhpExpressionResultUnusedInspection */
            $file;
            return 'hash-hash-hash';
        });

        $this->assertEquals('/js/jquery.js?hash-hash-hash', $mapper->apply('/js/jquery.js'));
    }

    // The false case of filemtime() can not be tested because it always succeeds if is_file() was true.
}
