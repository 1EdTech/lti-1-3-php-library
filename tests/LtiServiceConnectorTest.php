<?php

namespace Tests;

use GuzzleHttp\Client;
use Mockery;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\LtiServiceConnector;
use PHPUnit\Framework\TestCase;

class LtiServiceConnectorTest extends TestCase
{
    public function testItInstantiates()
    {
        $cache = Mockery::mock(ICache::class);
        $client = Mockery::mock(Client::class);
        $registration = Mockery::mock(ILtiRegistration::class);

        $connector = new LtiServiceConnector($registration, $cache, $client);

        $this->assertInstanceOf(LtiServiceConnector::class, $connector);
    }

    /*
     * @todo Finish testing
     */
}
