<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\LtiServiceConnector;

class LtiServiceConnectorTest extends TestCase
{

    public function testItInstantiates()
    {
        $jwks = new LtiServiceConnector();

        $this->assertInstanceOf(LtiServiceConnector::class, $jwks);
    }
}
