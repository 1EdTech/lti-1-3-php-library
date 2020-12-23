<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use Packback\Lti1p3\ImsCookie;

class ImsCookieTest extends TestCase
{

    public function testItInstantiates()
    {
        $cookie = new ImsCookie();

        $this->assertInstanceOf(ImsCookie::class, $cookie);
    }
}
