<?php

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Mockery;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\IServiceRequest;
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
     * @var LtiServiceConnector
     */
    private $connector;

    public function setUp(): void
    {
        $this->registration = Mockery::mock(ILtiRegistration::class);
        $this->request = Mockery::mock(IServiceRequest::class);
        $this->cache = Mockery::mock(ICache::class);
        $this->client = Mockery::mock(Client::class);
        $this->response = Mockery::mock(ResponseInterface::class);

        $this->scopes = ['scopeKey'];
        $this->token = 'TokenOfAccess';
        $this->method = LtiServiceConnector::METHOD_POST;
        $this->url = 'https://example.com';
        $this->body = json_encode(['post' => 'body']);
        $this->requestHeaders = [
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        $this->responseHeaders = [
            'Content-Type' => ['application/json'],
            'Server' => ['nginx'],
        ];
        $this->requestPayload = [
            'headers' => $this->requestHeaders,
            'body' => $this->body,
        ];
        $this->responseBody = ['some' => 'response'];
        $this->responseStatus = 200;

        $this->connector = new LtiServiceConnector($this->cache, $this->client);
    }

    public function testItInstantiates()
    {
        $this->assertInstanceOf(LtiServiceConnector::class, $this->connector);
    }

    public function testItGetsCachedAccessToken()
    {
        $this->mockCacheHasAccessToken();

        $result = $this->connector->getAccessToken($this->registration, ['scopeKey']);

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
        $connector = new LtiServiceConnector($this->cache, $this->client);

        $this->cache->shouldReceive('getAccessToken')
            ->once()->andReturn(false);
        $this->client->shouldReceive('post')
            ->once()->andReturn($this->response);
        $this->response->shouldReceive('getBody')
            ->once()->andReturn(json_encode(['access_token' => $this->token]));
        $this->cache->shouldReceive('cacheAccessToken')->once();

        $result = $connector->getAccessToken($registration, ['scopeKey']);

        $this->assertEquals($result, $this->token);
    }

    public function testItMakesAServiceRequest()
    {
        $expected = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Server' => 'nginx',
            ],
            'body' => $this->responseBody,
            'status' => $this->responseStatus,
        ];

        $this->request->shouldReceive('setAccessToken')
            ->once()->andReturn($this->request);
        $this->request->shouldReceive('getMethod')
            ->once()->andReturn($this->method);
        $this->request->shouldReceive('getUrl')
            ->once()->andReturn($this->url);
        $this->request->shouldReceive('getPayload')
            ->once()->andReturn($this->requestPayload);

        $this->mockCacheHasAccessToken();
        $this->client->shouldReceive('request')
            ->with($this->method, $this->url, $this->requestPayload)
            ->once()->andReturn($this->response);
        $this->response->shouldReceive('getHeaders')
            ->once()->andReturn($this->responseHeaders);
        $this->response->shouldReceive('getBody')
            ->once()->andReturn(json_encode($this->responseBody));
        $this->response->shouldReceive('getStatusCode')
            ->once()->andReturn($this->responseStatus);

        $result = $this->connector->makeServiceRequest($this->registration, $this->scopes, $this->request);

        $this->assertEquals($expected, $result);
    }

    public function testItRetriesServiceRequestOn401Error()
    {
        $this->method = LtiServiceConnector::METHOD_POST;
        $this->url = 'https://example.com';
        $this->body = json_encode(['post' => 'body']);
        $this->requestHeaders = [
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        $this->responseHeaders = [
            'Content-Type' => ['application/json'],
            'Server' => ['nginx'],
        ];
        $this->requestPayload = [
            'headers' => $this->requestHeaders,
            'body' => $this->body,
        ];
        $this->responseBody = ['some' => 'response'];
        $this->responseStatus = 200;
        $expected = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Server' => 'nginx',
            ],
            'body' => $this->responseBody,
            'status' => $this->responseStatus,
        ];

        $this->request->shouldReceive('setAccessToken')
            ->once()->andReturn($this->request);
        $this->request->shouldReceive('getMethod')
            ->once()->andReturn($this->method);
        $this->request->shouldReceive('getUrl')
            ->once()->andReturn($this->url);
        $this->request->shouldReceive('getPayload')
            ->once()->andReturn($this->requestPayload);

        $this->mockCacheHasAccessToken();

        // Mock the response failing on the first request
        $mockError = Mockery::mock(ClientException::class);
        $mockResponse = Mockery::mock(ResponseInterface::class);
        $this->client->shouldReceive('request')
            ->with($this->method, $this->url, [
                'headers' => $this->requestHeaders,
                'body' => $this->body,
            ])->once()
            ->andThrow($mockError);
        $mockError->shouldReceive('getResponse')
            ->once()->andReturn($mockResponse);
        $mockResponse->shouldReceive('getStatusCode')
            ->once()->andReturn(401);
        $this->cache->shouldReceive('clearAccessToken')->once();

        // Mock the response succeeding on the retry
        $this->client->shouldReceive('request')
            ->with($this->method, $this->url, [
                'headers' => $this->requestHeaders,
                'body' => $this->body,
            ])->once()->andReturn($this->response);
        $this->response->shouldReceive('getHeaders')
            ->once()->andReturn($this->responseHeaders);
        $this->response->shouldReceive('getBody')
            ->once()->andReturn(json_encode($this->responseBody));
        $this->response->shouldReceive('getStatusCode')
            ->once()->andReturn($this->responseStatus);

        $result = $this->connector->makeServiceRequest($this->registration, $this->scopes, $this->request);

        $this->assertEquals($expected, $result);
    }

    public function testItThrowsOnRepeated401Errors()
    {
        $this->method = LtiServiceConnector::METHOD_POST;
        $this->url = 'https://example.com';
        $this->body = json_encode(['post' => 'body']);
        $this->requestHeaders = [
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        $this->responseHeaders = [
            'Content-Type' => ['application/json'],
            'Server' => ['nginx'],
        ];
        $this->requestPayload = [
            'headers' => $this->requestHeaders,
            'body' => $this->body,
        ];
        $this->responseBody = ['some' => 'response'];
        $expected = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Server' => 'nginx',
            ],
            'body' => $this->responseBody,
        ];

        $this->request->shouldReceive('setAccessToken')
            ->once()->andReturn($this->request);
        $this->request->shouldReceive('getMethod')
            ->once()->andReturn($this->method);
        $this->request->shouldReceive('getUrl')
            ->once()->andReturn($this->url);
        $this->request->shouldReceive('getPayload')
            ->once()->andReturn($this->requestPayload);

        $this->mockCacheHasAccessToken();

        // Mock the response failing twice
        $mockError = Mockery::mock(ClientException::class);
        $mockResponse = Mockery::mock(ResponseInterface::class);
        $this->client->shouldReceive('request')
            ->with($this->method, $this->url, [
                'headers' => $this->requestHeaders,
                'body' => $this->body,
            ])->twice()
            ->andThrow($mockError);
        $mockError->shouldReceive('getResponse')
            ->twice()->andReturn($mockResponse);
        $mockResponse->shouldReceive('getStatusCode')
            ->twice()->andReturn(401);

        $this->cache->shouldReceive('clearAccessToken')->once();

        $this->expectException(ClientException::class);

        $this->connector->makeServiceRequest($this->registration, $this->scopes, $this->request);
    }

    public function testItGetsAll()
    {
        $method = LtiServiceConnector::METHOD_GET;
        $key = 'lineitems';
        $lineitems = ['lineitem'];
        $firstResponseHeaders = [
            'Link' => ['Something<'.$this->url.'>;rel="next"'],
            'Content-Type' => ['application/json'],
            'Server' => ['nginx'],
        ];
        $responseBody = json_encode([$key => $lineitems]);
        $expected = array_merge($lineitems, $lineitems);

        $this->request->shouldReceive('setAccessToken')
            ->once()->andReturn($this->request);
        $this->request->shouldReceive('getMethod')
            ->twice()->andReturn($method);
        $this->request->shouldReceive('getUrl')
            ->twice()->andReturn($this->url);
        $this->request->shouldReceive('getPayload')
            ->twice()->andReturn($this->requestPayload);
        $this->request->shouldReceive('setUrl')
            ->once()->andReturn($this->request);

        $this->mockCacheHasAccessToken();
        $this->client->shouldReceive('request')
            ->with($method, $this->url, $this->requestPayload)
            ->twice()->andReturn($this->response);
        $this->response->shouldReceive('getHeaders')
            ->once()->andReturn($firstResponseHeaders);
        $this->response->shouldReceive('getHeaders')
            ->once()->andReturn($this->responseHeaders);
        $this->response->shouldReceive('getBody')
            ->twice()->andReturn($responseBody);
        $this->response->shouldReceive('getStatusCode')
            ->twice()->andReturn($this->responseStatus);

        $result = $this->connector->getAll($this->registration, $this->scopes, $this->request, $key);

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
