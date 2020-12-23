<?php namespace Tests;

use PHPUnit\Framework\TestCase;
use Mockery;

use LTI\Interfaces\LtiServiceConnectorInterface;
use LTI\LtiAssignmentsGradesService;

class LtiAssignmentsGradesServiceTest extends TestCase
{

    public function testItInstantiates()
    {
        $connector = Mockery::mock(LtiServiceConnectorInterface::class);

        $service = new LtiAssignmentsGradesService($connector, []);

        $this->assertInstanceOf(LtiAssignmentsGradesService::class, $service);
    }
}
