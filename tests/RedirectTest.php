<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\Redirect;

class RedirectTest extends TestCase
{

    public function testItInstantiates()
    {
        $jwks = new Redirect();

        $this->assertInstanceOf(Redirect::class, $jwks);
    }
}
