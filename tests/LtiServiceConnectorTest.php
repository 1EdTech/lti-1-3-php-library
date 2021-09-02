<?php

namespace Tests;

use GuzzleHttp\Client;
use Mockery;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\LtiRegistration;
use Packback\Lti1p3\LtiServiceConnector;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class LtiServiceConnectorTest extends TestCase
{
    /**
     * @var Mockery\MockInterface
     */
    private $registration;
    /**
     * @var Mockery\MockInterface
     */
    private $cache;
    /**
     * @var Mockery\MockInterface
     */
    private $client;
    /**
     * @var Mockery\MockInterface
     */
    private $response;
    /**
     * @var string
     */
    private $token;
    /**
     * @var LtiServiceConnector
     */
    private $connector;

    public function setUp(): void
    {
        $this->registration = Mockery::mock(ILtiRegistration::class);
        $this->cache = Mockery::mock(ICache::class);
        $this->client = Mockery::mock(Client::class);
        $this->response = Mockery::mock(ResponseInterface::class);

        $this->token = 'TokenOfAccess';

        $this->connector = new LtiServiceConnector($this->registration, $this->cache, $this->client);
    }

    public function testItInstantiates()
    {
        $this->assertInstanceOf(LtiServiceConnector::class, $this->connector);
    }

    public function testItGetsCachedAccessToken()
    {
        $this->mockCacheHasAccessToken();

        $result = $this->connector->getAccessToken(['scopeKey']);

        $this->assertEquals($result, $this->token);
    }

    public function testItGetsNewAccessToken()
    {
        $registration = new LtiRegistration([
            'clientId' => 'client_id',
            'issuer' => 'issuer',
            'authServer' => 'auth_server',
            'toolPrivateKey' => file_get_contents(__DIR__.'/data/private.key'),
            'kid' => 'kid',
            'authTokenUrl' => 'auth_token_url',
        ]);
        $connector = new LtiServiceConnector($registration, $this->cache, $this->client);

        $this->cache->shouldReceive('getAccessToken')
            ->once()->andReturn(false);
        $this->client->shouldReceive('post')
            ->once()->andReturn($this->response);
        $this->response->shouldReceive('getBody')
            ->once()->andReturn(json_encode(['access_token' => $this->token]));
        $this->cache->shouldReceive('cacheAccessToken')->once();

        $result = $connector->getAccessToken(['scopeKey']);

        $this->assertEquals($result, $this->token);
    }

    public function testItMakesPostServiceRequest()
    {
        $scopes = ['scopeKey'];
        $method = 'post';
        $url = 'https://example.com';
        $body = json_encode(['post' => 'body']);
        $requestHeaders = [
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        $responseHeaders = [
            'Content-Type' => ['application/json'],
            'Server' => ['nginx'],
        ];
        $responseBody = ['some' => 'response'];
        $responseStatus = 200;
        $expected = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Server' => 'nginx',
            ],
            'body' => $responseBody,
            'status' => $responseStatus,
        ];

        $this->mockCacheHasAccessToken();
        $this->client->shouldReceive('request')
            ->with($method, $url, [
                'headers' => $requestHeaders,
                'body' => $body,
            ])->once()->andReturn($this->response);
        $this->response->shouldReceive('getHeaders')
            ->once()->andReturn($responseHeaders);
        $this->response->shouldReceive('getBody')
            ->once()->andReturn(json_encode($responseBody));
        $this->response->shouldReceive('getStatusCode')
            ->once()->andReturn(json_encode($responseStatus));

        $result = $this->connector->makeServiceRequest($scopes, $method, $url, $body);

        $this->assertEquals($expected, $result);
    }

    public function testItMakesDefaultServiceRequest()
    {
        $scopes = ['scopeKey'];
        $method = 'get';
        $url = 'https://example.com';
        $requestHeaders = [
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ];
        $responseHeaders = [
            'Content-Type' => ['application/json'],
            'Server' => ['nginx'],
        ];
        $responseBody = ['some' => 'response'];
        $responseStatus = 200;
        $expected = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Server' => 'nginx',
            ],
            'body' => $responseBody,
            'status' => $responseStatus,
        ];

        $this->mockCacheHasAccessToken();
        $this->client->shouldReceive('request')
            ->with($method, $url, [
                'headers' => $requestHeaders,
            ])->once()->andReturn($this->response);
        $this->response->shouldReceive('getHeaders')
            ->once()->andReturn($responseHeaders);
        $this->response->shouldReceive('getBody')
            ->once()->andReturn(json_encode($responseBody));
        $this->response->shouldReceive('getStatusCode')
            ->once()->andReturn(json_encode($responseStatus));

        $result = $this->connector->makeServiceRequest($scopes, $method, $url);

        $this->assertEquals($expected, $result);
    }

    private function mockCacheHasAccessToken()
    {
        $this->registration->shouldReceive('getClientId')
            ->once()->andReturn('client_id');
        $this->registration->shouldReceive('getIssuer')
            ->once()->andReturn('issuer');
        $this->cache->shouldReceive('getAccessToken')
            ->once()->andReturn($this->token);
    }
}
