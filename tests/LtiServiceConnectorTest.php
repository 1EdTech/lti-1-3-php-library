<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\LtiRegistration;
use LTI\LtiServiceConnector;

class LtiServiceConnectorTest extends TestCase
{

    public function testItInstantiates()
    {
        $registration = new LtiRegistration;
        $connector = new LtiServiceConnector($registration);

        $this->assertInstanceOf(LtiServiceConnector::class, $connector);
    }
}
