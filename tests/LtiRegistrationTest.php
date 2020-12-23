<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\LtiRegistration;

class LtiRegistrationTest extends TestCase
{

    public function testItInstantiates()
    {
        $registration = new LtiRegistration();

        $this->assertInstanceOf(LtiRegistration::class, $registration);
    }
}
