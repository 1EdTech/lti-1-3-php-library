<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\LtiLineitem;

class LtiLineitemTest extends TestCase
{

    public function testItInstantiates()
    {
        $lineitem = new LtiLineitem();

        $this->assertInstanceOf(LtiLineitem::class, $lineitem);
    }
}
