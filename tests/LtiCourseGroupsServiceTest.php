<?php namespace Tests;

use PHPUnit\Framework\TestCase;
use Mockery;

use LTI\Interfaces\LtiServiceConnectorInterface;
use LTI\LtiCourseGroupsService;

class LtiCourseGroupsServiceTest extends TestCase
{

    public function testItInstantiates()
    {
        $connector = Mockery::mock(LtiServiceConnectorInterface::class);

        $service = new LtiCourseGroupsService($connector, []);

        $this->assertInstanceOf(LtiCourseGroupsService::class, $service);
    }
}
