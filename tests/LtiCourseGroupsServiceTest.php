<?php

namespace Tests;

use Mockery;
use Packback\Lti1p3\Interfaces\LtiServiceConnectorInterface;
use Packback\Lti1p3\LtiCourseGroupsService;
use PHPUnit\Framework\TestCase;

class LtiCourseGroupsServiceTest extends TestCase
{
    public function testItInstantiates()
    {
        $connector = Mockery::mock(LtiServiceConnectorInterface::class);

        $service = new LtiCourseGroupsService($connector, []);

        $this->assertInstanceOf(LtiCourseGroupsService::class, $service);
    }

    /*
     * @todo Test this
     */
}
