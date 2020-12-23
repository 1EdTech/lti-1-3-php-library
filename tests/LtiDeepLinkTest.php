<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\LtiDeepLink;
use LTI\LtiRegistration;

class LtiDeepLinkTest extends TestCase
{

    public function testItInstantiates()
    {
        $registration = new LtiRegistration;

        $deepLink = new LtiDeepLink($registration, 'test', []);

        $this->assertInstanceOf(LtiDeepLink::class, $deepLink);
    }
}
