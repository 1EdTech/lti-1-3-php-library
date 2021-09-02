<?php

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
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
        $expected = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Server' => 'nginx',
            ],
            'body' => $responseBody,
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
        $expected = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Server' => 'nginx',
            ],
            'body' => $responseBody,
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

        $result = $this->connector->makeServiceRequest($scopes, $method, $url);

        $this->assertEquals($expected, $result);
    }

    public function testItRetriesServiceRequestOn401Error()
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
        $expected = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Server' => 'nginx',
            ],
            'body' => $responseBody,
        ];

        $this->mockCacheHasAccessToken();

        // Mock the response failing on the first request
        $mockError = Mockery::mock(ClientException::class);
        $mockResponse = Mockery::mock(ResponseInterface::class);
        $this->client->shouldReceive('request')
            ->with($method, $url, [
                'headers' => $requestHeaders,
                'body' => $body,
            ])->once()
            ->andThrow($mockError);
        $mockError->shouldReceive('getResponse')
            ->once()->andReturn($mockResponse);
        $mockResponse->shouldReceive('getStatusCode')
            ->once()->andReturn(401);
        $this->cache->shouldReceive('clearAccessToken')->once();

        // Mock the response succeeding on the retry
        $this->client->shouldReceive('request')
            ->with($method, $url, [
                'headers' => $requestHeaders,
                'body' => $body,
            ])->once()->andReturn($this->response);
        $this->response->shouldReceive('getHeaders')
            ->once()->andReturn($responseHeaders);
        $this->response->shouldReceive('getBody')
            ->once()->andReturn(json_encode($responseBody));

        $result = $this->connector->makeServiceRequest($scopes, $method, $url, $body);

        $this->assertEquals($expected, $result);
    }

    public function testItThrowsOnRepeated401Errors()
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
        $expected = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Server' => 'nginx',
            ],
            'body' => $responseBody,
        ];

        $this->mockCacheHasAccessToken();

        // Mock the response failing twice
        $mockError = Mockery::mock(ClientException::class);
        $mockResponse = Mockery::mock(ResponseInterface::class);
        $this->client->shouldReceive('request')
            ->with($method, $url, [
                'headers' => $requestHeaders,
                'body' => $body,
            ])->twice()
            ->andThrow($mockError);
        $mockError->shouldReceive('getResponse')
            ->twice()->andReturn($mockResponse);
        $mockResponse->shouldReceive('getStatusCode')
            ->twice()->andReturn(401);

        $this->cache->shouldReceive('clearAccessToken')->once();

        $this->expectException(ClientException::class);

        $this->connector->makeServiceRequest($scopes, $method, $url, $body);
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
