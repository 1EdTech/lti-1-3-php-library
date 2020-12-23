<?php namespace Tests;

use PHPUnit\Framework\TestCase;
use Mockery;

use LTI\Interfaces\LtiRegistrationInterface;
use LTI\LtiServiceConnector;

class LtiServiceConnectorTest extends TestCase
{

    public function testItInstantiates()
    {
        $registration = Mockery::mock(LtiRegistrationInterface::class);

        $connector = new LtiServiceConnector($registration);

        $this->assertInstanceOf(LtiServiceConnector::class, $connector);
    }
}
