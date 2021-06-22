<?php

namespace Tests;

use Mockery;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\LtiMessageLaunch;
use PHPUnit\Framework\TestCase;

class LtiMessageLaunchTest extends TestCase
{
    public function setUp(): void
    {
        $this->cache = Mockery::mock(ICache::class);
        $this->cookie = Mockery::mock(ICookie::class);
        $this->database = Mockery::mock(IDatabase::class);

        $this->messageLaunch = new LtiMessageLaunch(
            $this->database,
            $this->cache,
            $this->cookie
        );
    }

    public function testItInstantiates()
    {
        $this->assertInstanceOf(LtiMessageLaunch::class, $this->messageLaunch);
    }

    public function testItCreatesANewInstance()
    {
        $messageLaunch = LtiMessageLaunch::new(
            $this->database,
            $this->cache,
            $this->cookie
        );

        $this->assertInstanceOf(LtiMessageLaunch::class, $messageLaunch);
    }

    /*
     * @todo Finish testing
     */
}
