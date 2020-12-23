<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\LtiDeployment;

class LtiDeploymentTest extends TestCase
{

    public function testItInstantiates()
    {
        $deployment = new LtiDeployment();

        $this->assertInstanceOf(LtiDeployment::class, $deployment);
    }
}
