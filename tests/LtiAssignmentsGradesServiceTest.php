<?php

namespace Tests;

use Mockery;
use Packback\Lti1p3\Interfaces\LtiServiceConnectorInterface;
use Packback\Lti1p3\LtiAssignmentsGradesService;
use PHPUnit\Framework\TestCase;

class LtiAssignmentsGradesServiceTest extends TestCase
{
    public function testItInstantiates()
    {
        $connector = Mockery::mock(LtiServiceConnectorInterface::class);

        $service = new LtiAssignmentsGradesService($connector, []);

        $this->assertInstanceOf(LtiAssignmentsGradesService::class, $service);
    }

    /*
     * @todo Test this
     */
}
