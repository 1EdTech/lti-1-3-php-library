<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use Packback\Lti1p3\LtiLineitem;

class LtiLineitemTest extends TestCase
{

    public function testItInstantiates()
    {
        $lineitem = new LtiLineitem();

        $this->assertInstanceOf(LtiLineitem::class, $lineitem);
    }

    /**
     * TODO: Finish testing
     */
}
