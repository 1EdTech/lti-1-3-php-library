<?php

namespace Tests\ImsStorage;

use Packback\Lti1p3\ImsStorage\ImsCookie;
use PHPUnit\Framework\TestCase;

class ImsCookieTest extends TestCase
{
    public function testItInstantiates()
    {
        $cookie = new ImsCookie();

        $this->assertInstanceOf(ImsCookie::class, $cookie);
    }
}
