<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\LtiCourseGroupsService;

class LtiCourseGroupsServiceTest extends TestCase
{

    public function testItInstantiates()
    {
        $jwks = new LtiCourseGroupsService();

        $this->assertInstanceOf(LtiCourseGroupsService::class, $jwks);
    }
}
