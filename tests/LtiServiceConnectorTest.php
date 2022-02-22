<?php

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\IServiceRequest;
use Packback\Lti1p3\LtiRegistration;
use Packback\Lti1p3\LtiServiceConnector;
use Psr\Http\Message\StreamInterface;

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
        $this->response = Mockery::mock(Response::class);
        $this->streamInterface = Mockery::mock(StreamInterface::class);

        $this->scopes = ['scopeKey'];
        $this->token = 'TokenOfAccess';
        $this->method = LtiServiceConnector::METHOD_POST;
        $this->url = 'https://example.com';
        $this->body = json_encode(['userId' => 'id']);
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

        $this->cache->shouldReceive('getAccessToken')
            ->once()->andReturn(false);
        $this->client->shouldReceive('post')
            ->once()->andReturn($this->response);
        $this->response->shouldReceive('getBody')
            ->once()->andReturn($this->streamInterface);
        $this->streamInterface->shouldReceive('__toString')
            ->once()->andReturn(json_encode(['access_token' => $this->token]));
        $this->cache->shouldReceive('cacheAccessToken')->once();

        $result = $this->connector->getAccessToken($registration, ['scopeKey']);

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
        $this->mockMakeRequest();

        $this->mockCacheHasAccessToken();
        $this->client->shouldReceive('request')
            ->with($this->method, $this->url, $this->requestPayload)
            ->once()->andReturn($this->response);
        $this->response->shouldReceive('getHeaders')
            ->once()->andReturn($this->responseHeaders);
        $this->response->shouldReceive('getBody')
            ->once()->andReturn($this->streamInterface);
        $this->streamInterface->shouldReceive('__toString')
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
        $this->body = json_encode(['userId' => 'id']);
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

        // It gets an access token
        $this->mockCacheHasAccessToken();
        // It sets it on the request
        $this->request->shouldReceive('setAccessToken')
            ->once()->andReturn($this->request);
        // It makes the request
        $this->mockMakeRequest();

        // The request fails
        $this->mockRequestReturnsA401();

        // It clears the access token from the cache
        $this->mockCacheHasAccessToken();
        $this->cache->shouldReceive('clearAccessToken')->once();

        // It gets a new access token
        $this->mockGetAccessTokenCacheKey();
        $this->request->shouldReceive('setAccessToken')
            ->once()->andReturn($this->request);
        // It makes another request
        $this->mockMakeRequest();

        // Mock the response succeeding on the retry
        $this->client->shouldReceive('request')
            ->with($this->method, $this->url, [
                'headers' => $this->requestHeaders,
                'body' => $this->body,
            ])->once()->andReturn($this->response);
        $this->response->shouldReceive('getHeaders')
            ->once()->andReturn($this->responseHeaders);
        $this->response->shouldReceive('getBody')
            ->once()->andReturn($this->streamInterface);
        $this->streamInterface->shouldReceive('__toString')
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

        // It gets an access token
        $this->mockCacheHasAccessToken();
        // It sets it on the request
        $this->request->shouldReceive('setAccessToken')
            ->once()->andReturn($this->request);
        // It makes the request
        $this->mockMakeRequest();

        // The request fails
        $this->mockRequestReturnsA401();

        // It clears the access token from the cache
        $this->mockCacheHasAccessToken();
        $this->cache->shouldReceive('clearAccessToken')->once();

        // It gets a new access token
        $this->mockGetAccessTokenCacheKey();
        $this->request->shouldReceive('setAccessToken')
            ->once()->andReturn($this->request);
        // It makes another request
        $this->mockMakeRequest();

        // The request fails again
        $this->mockRequestReturnsA401();

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

        // Sets the access token on two requests
        $this->registration->shouldReceive('getClientId')
            ->twice()->andReturn('client_id');
        $this->registration->shouldReceive('getIssuer')
            ->twice()->andReturn('issuer');
        $this->cache->shouldReceive('getAccessToken')
            ->twice()->andReturn($this->token);
        $this->request->shouldReceive('setAccessToken')
            ->twice()->andReturn($this->request);

        // Makes two requests, but gets the method and URL once before making the request
        $this->request->shouldReceive('getMethod')
            ->times(3)->andReturn($method);
        $this->request->shouldReceive('getUrl')
            ->times(3)->andReturn($this->url);
        $this->request->shouldReceive('getPayload')
            ->times(2)->andReturn($this->requestPayload);
        // Doesn't find a matching link in on the second header, so only updates the URL once
        $this->request->shouldReceive('setUrl')
            ->once()->andReturn($this->request);

        // Two responses come back
        $this->client->shouldReceive('request')
            ->with($method, $this->url, $this->requestPayload)
            ->twice()->andReturn($this->response);
        $this->response->shouldReceive('getBody')
            ->twice()->andReturn($this->streamInterface);
        $this->streamInterface->shouldReceive('__toString')
            ->twice()->andReturn($responseBody);
        $this->response->shouldReceive('getStatusCode')
            ->twice()->andReturn($this->responseStatus);
        // The first has a Link header
        $this->response->shouldReceive('getHeaders')
            ->once()->andReturn($firstResponseHeaders);
        // The second doesnt
        $this->response->shouldReceive('getHeaders')
            ->once()->andReturn($this->responseHeaders);

        $result = $this->connector->getAll($this->registration, $this->scopes, $this->request, $key);

        $this->assertEquals($expected, $result);
    }

    public function testItLogsRequests()
    {
        $this->request->shouldReceive('getPayload')
            ->once()
            ->andReturn([
                'body' => json_encode(['userId' => 'id']),
            ]);
        $this->request->shouldReceive('getMethod')
            ->once()
            ->andReturn('GET');
        $this->request->shouldReceive('getUrl')
            ->once()
            ->andReturn('/test.com/test');
        $connector = Mockery::mock(LtiServiceConnector::class)->makePartial();
        $connector->shouldReceive('errorLog')
            ->once();

        $result = $connector->logRequest(
            LtiServiceConnector::UNSUPPORTED_REQUEST,
            $this->request,
            $this->responseHeaders,
            $this->responseBody
        );

        $this->assertNull($result);
    }

    private function mockMakeRequest()
    {
        // It makes another request
        $this->request->shouldReceive('getMethod')
            ->andReturn($this->method);
        $this->request->shouldReceive('getUrl')
            ->andReturn($this->url);
        $this->request->shouldReceive('getPayload')
            ->andReturn($this->requestPayload);
    }

    private function mockRequestReturnsA401()
    {
        $mockError = Mockery::mock(ClientException::class);
        $mockResponse = Mockery::mock(Response::class);
        $mockError->shouldReceive('getResponse')
            ->once()->andReturn($mockResponse);
        $mockResponse->shouldReceive('getStatusCode')
            ->once()->andReturn(401);
        $this->client->shouldReceive('request')
            ->with($this->method, $this->url, [
                'headers' => $this->requestHeaders,
                'body' => $this->body,
            ])->once()
            ->andThrow($mockError);
    }

    private function mockGetAccessTokenCacheKey()
    {
        $this->registration->shouldReceive('getClientId')
            ->once()->andReturn('client_id');
        $this->registration->shouldReceive('getIssuer')
            ->once()->andReturn('issuer');
    }

    private function mockCacheHasAccessToken()
    {
        $this->mockGetAccessTokenCacheKey();
        $this->cache->shouldReceive('getAccessToken')
            ->once()->andReturn($this->token);
    }
}
