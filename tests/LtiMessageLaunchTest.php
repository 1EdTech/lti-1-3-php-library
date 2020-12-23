<?php namespace Tests;

use Mockery;
use PHPUnit\Framework\TestCase;

use LTI\Interfaces\Cache;
use LTI\Interfaces\Cookie;
use LTI\Interfaces\Database;
use LTI\LtiMessageLaunch;

class LtiMessageLaunchTest extends TestCase
{

    public function testItInstantiates()
    {
        $cache = Mockery::mock(Cache::class);
        $cookie = Mockery::mock(Cookie::class);
        $database = Mockery::mock(Database::class);

        $messageLaunch = new LtiMessageLaunch($database, $cache, $cookie);

        $this->assertInstanceOf(LtiMessageLaunch::class, $messageLaunch);
    }
}
