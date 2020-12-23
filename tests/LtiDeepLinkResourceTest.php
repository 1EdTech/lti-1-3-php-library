<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\LtiDeepLinkResource;

class LtiDeepLinkResourceTest extends TestCase
{

    public function testItInstantiates()
    {
        $deepLinkResource = new LtiDeepLinkResource();

        $this->assertInstanceOf(LtiDeepLinkResource::class, $deepLinkResource);
    }
}
