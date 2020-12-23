<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\LtiDeepLink;

class LtiDeepLinkTest extends TestCase
{

    public function testItInstantiates()
    {
        $jwks = new LtiDeepLink();

        $this->assertInstanceOf(LtiDeepLink::class, $jwks);
    }
}
