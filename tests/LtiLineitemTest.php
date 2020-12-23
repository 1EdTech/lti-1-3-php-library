<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\LtiLineitem;

class LtiLineitemTest extends TestCase
{

    public function testItInstantiates()
    {
        $jwks = new LtiLineitem();

        $this->assertInstanceOf(LtiLineitem::class, $jwks);
    }
}
