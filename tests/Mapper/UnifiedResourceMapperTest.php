<?php
namespace Lapaz\Amechan\Mapper;

use PHPUnit\Framework\TestCase;

class UnifiedResourceMapperTest extends TestCase
{
    public function testApply()
    {
        $mapper = new UnifiedResourceMapper('/js', [
            'all.min.js' => [
                'jquery.js',
                'bootstrap.js',
            ]
        ]);

        $this->assertEquals('/js/all.min.js', $mapper->apply('/js/bootstrap.js'));
        $this->assertEquals('/js/standalone.js', $mapper->apply('/js/standalone.js'));
    }
}
