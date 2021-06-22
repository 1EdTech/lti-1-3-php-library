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
    public function setUp(): void
    {
        $registration = Mockery::mock(ILtiRegistration::class);
        $cache = Mockery::mock(ICache::class);
        $client = Mockery::mock(Client::class);

        $this->connector = new LtiServiceConnector($registration, $cache, $client);
    }

    public function testItInstantiates()
    {
        $this->assertInstanceOf(LtiServiceConnector::class, $this->connector);
    }

    /*
     * @todo Finish testing
     */
}
