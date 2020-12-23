<?php namespace Tests;

use Mockery;
use PHPUnit\Framework\TestCase;

use LTI\Interfaces\Cache;
use LTI\Interfaces\Cookie;
use LTI\Interfaces\Database;
use LTI\LtiOidcLogin;

class LtiOidcLoginTest extends TestCase
{

    public function testItInstantiates()
    {
        $cache = Mockery::mock(Cache::class);
        $cookie = Mockery::mock(Cookie::class);
        $database = Mockery::mock(Database::class);

        $oidcLogin = new LtiOidcLogin($database, $cache, $cookie);

        $this->assertInstanceOf(LtiOidcLogin::class, $oidcLogin);
    }
}
