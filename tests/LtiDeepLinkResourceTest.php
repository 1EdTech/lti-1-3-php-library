<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use Packback\Lti1p3\LtiDeepLinkResource;

class LtiDeepLinkResourceTest extends TestCase
{

    public function testItInstantiates()
    {
        $deepLinkResource = new LtiDeepLinkResource();

        $this->assertInstanceOf(LtiDeepLinkResource::class, $deepLinkResource);
    }
}
