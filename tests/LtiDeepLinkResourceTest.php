<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\LtiDeepLinkResource;

class LtiDeepLinkResourceTest extends TestCase
{

    public function testItInstantiates()
    {
        $jwks = new LtiDeepLinkResource();

        $this->assertInstanceOf(LtiDeepLinkResource::class, $jwks);
    }
}
