<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\JwksEndpoint;

class JwksEndpointTest extends TestCase
{

    public function testItInstantiates()
    {
        $jwks = new JwksEndpoint([]);

        $this->assertInstanceOf(JwksEndpoint::class, $jwks);
    }

    public function testCreatesANewInstance()
    {
        $jwks = JwksEndpoint::new([]);

        $this->assertInstanceOf(JwksEndpoint::class, $jwks);
    }
}
