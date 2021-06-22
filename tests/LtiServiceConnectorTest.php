<?php

namespace Tests;

// use Firebase\JWT\JWT;
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
        // $this->jwt = Mockery::mock(JWT::class);
        $this->registration = Mockery::mock(ILtiRegistration::class);
        $this->cache = Mockery::mock(ICache::class);
        $this->client = Mockery::mock(Client::class);

        $this->connector = new LtiServiceConnector($this->registration, $this->cache, $this->client);
    }

    public function testItInstantiates()
    {
        $this->assertInstanceOf(LtiServiceConnector::class, $this->connector);
    }

    public function testItGetsCachedAccessToken()
    {
        $this->registration->shouldReceive('getClientId')
            ->once()
            ->andReturn('client_id');
        $this->registration->shouldReceive('getIssuer')
            ->once()
            ->andReturn('issuer');
        $this->cache->shouldReceive('getAccessToken')
            ->once()
            ->andReturn('TokenOfAccess');

        $result = $this->connector->getAccessToken(['scopeKey']);

        $this->assertEquals($result, 'TokenOfAccess');
    }

    /*
     * @todo Figure out how to test this
     */
    // public function testItGetsAccessToken()
    // {
    //     $this->registration->shouldReceive('getClientId')
    //         ->once()
    //         ->andReturn('client_id');
    //     $this->registration->shouldReceive('getIssuer')
    //         ->once()
    //         ->andReturn('issuer');
    //     $this->cache->shouldReceive('getAccessToken')
    //         ->once()
    //         ->andReturn();
    //     $this->registration->shouldReceive('getAuthServer')
    //         ->once()
    //         ->andReturn('auth_server');
    //     $this->registration->shouldReceive('getToolPrivateKey')
    //         ->once()
    //         ->andReturn('toolprivatekey');
    //     $this->registration->shouldReceive('getKid')
    //         ->once()
    //         ->andReturn('kid');

        // Error: supplied key param cannot be coerced into a private key
    //     $this->jwt->shouldReceive('encode')
    //         ->once()
    //         ->andReturn('jwt');


    //     $this->registration->shouldReceive('getAuthTokenUrl')
    //         ->once()
    //         ->andReturn('auth_token_url');
    //     $this->client->shouldReceive('post')
    //         ->once()
    //         ->andReturn([
    //             'body' => [
    //                 'access_token' => 'accessToken'
    //             ]
    //         ]);
    //     $this->cache->shouldReceive('cacheAccessToken')
    //         ->once()
    //         ->andReturn();

    //     $result = $this->connector->getAccessToken(['scopeKey']);

    //     $this->assertEquals($result, 'TokenOfAccess');
    // }
}
