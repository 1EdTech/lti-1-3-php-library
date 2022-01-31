<?php

namespace Tests;

use Mockery;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\LtiAssignmentsGradesService;
use Packback\Lti1p3\LtiConstants;

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

    public function testItGetsSingleLineItem()
    {
        $ltiLineitemData = [
            'id' => 'testId',
        ];

        $serviceData = [
            'scope' => [LtiConstants::AGS_SCOPE_LINEITEM],
        ];

        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, $serviceData);

        $response = [
            'body' => $ltiLineitemData,
        ];

        $this->connector->shouldReceive('makeServiceRequest')
            ->once()->andReturn($response);

        $expected = $ltiLineitemData;

        $result = $service->getLineItem('someUrl');

        $this->assertEquals($expected, $result);
    }

    /*
     * @todo Test this
     */
}
