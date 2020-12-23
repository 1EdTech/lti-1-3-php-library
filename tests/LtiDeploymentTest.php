<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use Packback\Lti1p3\LtiDeployment;

class LtiDeploymentTest extends TestCase
{

    public function testItInstantiates()
    {
        $deployment = new LtiDeployment();

        $this->assertInstanceOf(LtiDeployment::class, $deployment);
    }

    /**
     * TODO: Finish testing
     */
}
