<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\LtiRegistration;

class LtiRegistrationTest extends TestCase
{

    public function testItInstantiates()
    {
        $jwks = new LtiRegistration();

        $this->assertInstanceOf(LtiRegistration::class, $jwks);
    }
}
