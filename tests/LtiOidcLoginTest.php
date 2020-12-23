<?php namespace Tests;

use Mockery;
use PHPUnit\Framework\TestCase;

use Packback\Lti1p3\Interfaces\Cache;
use Packback\Lti1p3\Interfaces\Cookie;
use Packback\Lti1p3\Interfaces\Database;
use Packback\Lti1p3\LtiOidcLogin;

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

    /**
     * TODO: Finish testing
     */
}
