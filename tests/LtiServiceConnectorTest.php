<?php

namespace Tests;

use Mockery;
use GuzzleHttp\Client;
use Packback\Lti1p3\Interfaces\Cache;
use Packback\Lti1p3\Interfaces\LtiRegistrationInterface;
use Packback\Lti1p3\LtiServiceConnector;
use PHPUnit\Framework\TestCase;

class LtiServiceConnectorTest extends TestCase
{
    public function testItInstantiates()
    {
        $registration = Mockery::mock(LtiRegistrationInterface::class);
        $cache = Mockery::mock(Cache::class);
        $client = Mockery::mock(Client::class);

        $connector = new LtiServiceConnector($registration, $cache, $client);

        $this->assertInstanceOf(LtiServiceConnector::class, $connector);
    }

    /*
     * @todo Finish testing
     */
}
