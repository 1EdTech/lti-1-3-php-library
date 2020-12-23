<?php namespace Tests;

use PHPUnit\Framework\TestCase;
use Mockery;

use Packback\Lti1p3\Interfaces\LtiRegistrationInterface;
use Packback\Lti1p3\LtiDeepLink;

class LtiDeepLinkTest extends TestCase
{

    public function testItInstantiates()
    {
        $registration = Mockery::mock(LtiRegistrationInterface::class);

        $deepLink = new LtiDeepLink($registration, 'test', []);

        $this->assertInstanceOf(LtiDeepLink::class, $deepLink);
    }
}
