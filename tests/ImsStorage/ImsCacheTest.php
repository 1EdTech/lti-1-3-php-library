<?php

namespace Tests\ImsStorage;

use Packback\Lti1p3\ImsStorage\ImsCache;
use PHPUnit\Framework\TestCase;

class ImsCacheTest extends TestCase
{
    public function testItInstantiates()
    {
        $cache = new ImsCache();

        $this->assertInstanceOf(ImsCache::class, $cache);
    }
}
