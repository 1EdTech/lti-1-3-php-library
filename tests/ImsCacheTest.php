<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\ImsCache;

class ImsCacheTest extends TestCase
{

    public function testItInstantiates()
    {
        $cache = new ImsCache();

        $this->assertInstanceOf(ImsCache::class, $cache);
    }
}
