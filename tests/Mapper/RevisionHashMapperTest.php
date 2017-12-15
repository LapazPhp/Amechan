<?php
namespace Lapaz\Amechan\Mapper;

use PHPUnit\Framework\TestCase;

class RevisionHashMapperTest extends TestCase
{
    public function testRevisionUrl()
    {
        $mapper = new RevisionHashMapper('/js', [
            'all.min.js' => 'all-0123456789.min.js',
        ]);

        $this->assertEquals('/js/all-0123456789.min.js', $mapper->apply('/js/all.min.js'));
        $this->assertEquals('/js/standalone.js', $mapper->apply('/js/standalone.js'));
    }
}
