<?php

namespace Tests;

use Mockery;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\LtiServiceConnector;
use PHPUnit\Framework\TestCase;

class LtiServiceConnectorTest extends TestCase
{
    public function testItInstantiates()
    {
        $registration = Mockery::mock(ILtiRegistration::class);

        $connector = new LtiServiceConnector($registration);

        $this->assertInstanceOf(LtiServiceConnector::class, $connector);
    }

    /*
     * @todo Finish testing
     */
}
