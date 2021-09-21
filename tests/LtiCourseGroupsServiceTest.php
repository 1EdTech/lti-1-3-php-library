<?php

namespace Tests;

use Mockery;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\LtiCourseGroupsService;

class LtiCourseGroupsServiceTest extends TestCase
{
    public function setUp(): void
    {
        $this->connector = Mockery::mock(ILtiServiceConnector::class);
        $this->registration = Mockery::mock(ILtiRegistration::class);
    }

    public function testItInstantiates()
    {
        $service = new LtiCourseGroupsService($this->connector, $this->registration, []);

        $this->assertInstanceOf(LtiCourseGroupsService::class, $service);
    }

    /*
     * @todo Test this
     */
}
