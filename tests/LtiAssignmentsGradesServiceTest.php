<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\LtiAssignmentsGradesService;

class LtiAssignmentsGradesServiceTest extends TestCase
{

    public function testItInstantiates()
    {
        $jwks = new LtiAssignmentsGradesService();

        $this->assertInstanceOf(LtiAssignmentsGradesService::class, $jwks);
    }
}
