<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\LtiMessageLaunch;

class LtiMessageLaunchTest extends TestCase
{

    public function testItInstantiates()
    {
        $jwks = new LtiMessageLaunch();

        $this->assertInstanceOf(LtiMessageLaunch::class, $jwks);
    }
}
