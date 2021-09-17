<?php

namespace Tests;

use Mockery;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\LtiAssignmentsGradesService;
use PHPUnit\Framework\TestCase;

class LtiAssignmentsGradesServiceTest extends TestCase
{
    public function setUp(): void
    {
        $this->connector = Mockery::mock(ILtiServiceConnector::class);
        $this->registration = Mockery::mock(ILtiRegistration::class);
    }

    public function testItInstantiates()
    {
        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, []);

        $this->assertInstanceOf(LtiAssignmentsGradesService::class, $service);
    }

    /*
     * @todo Test this
     */
}
