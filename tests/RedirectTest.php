<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\Redirect;

class RedirectTest extends TestCase
{

    public function testItInstantiates()
    {
        $redirect = new Redirect('test');

        $this->assertInstanceOf(Redirect::class, $redirect);
    }
}
