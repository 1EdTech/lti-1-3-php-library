<?php namespace Tests;

use PHPUnit\Framework\TestCase;
use Mockery;

use LTI\Interfaces\LtiRegistrationInterface;
use LTI\LtiDeepLink;

class LtiDeepLinkTest extends TestCase
{

    public function testItInstantiates()
    {
        $registration = Mockery::mock(LtiRegistrationInterface::class);

        $deepLink = new LtiDeepLink($registration, 'test', []);

        $this->assertInstanceOf(LtiDeepLink::class, $deepLink);
    }
}
