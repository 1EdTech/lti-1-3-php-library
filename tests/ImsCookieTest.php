<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\ImsCookie;

class ImsCookieTest extends TestCase
{

    public function testItInstantiates()
    {
        $cookie = new ImsCookie();

        $this->assertInstanceOf(ImsCookie::class, $cookie);
    }
}
