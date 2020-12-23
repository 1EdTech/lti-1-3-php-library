<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use Packback\Lti1p3\LtiRegistration;

class LtiRegistrationTest extends TestCase
{

    public function testItInstantiates()
    {
        $registration = new LtiRegistration();

        $this->assertInstanceOf(LtiRegistration::class, $registration);
    }

    /**
     * TODO: Finish testing
     */
}
