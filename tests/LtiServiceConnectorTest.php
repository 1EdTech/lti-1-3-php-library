<?php

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Packback\Lti1p3\Interfaces\IAccessTokenExpiryCache;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\IServiceRequest;
use Packback\Lti1p3\LtiRegistration;
use Packback\Lti1p3\LtiServiceConnector;
use Packback\Lti1p3\ServiceRequest;
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
    private $responseStatus;
    private $responseBody;
    private $responseHeaders;
    private $request;
    private $requestPayload;
    private $requestHeaders;
    private $body;
    private $url;
    private $method;
    private $token;
    private $scopes;
    private $streamInterface;
    protected function setUp(): void
    {
        $this->registration = Mockery::mock(ILtiRegistration::class);
        $this->request = Mockery::mock(IServiceRequest::class);
        $this->cache = Mockery::mock(ICache::class);
        $this->client = Mockery::mock(Client::class);
        $this->response = Mockery::mock(Response::class);
        $this->streamInterface = Mockery::mock(StreamInterface::class);

        $this->scopes = ['scopeKey'];
        $this->token = 'TokenOfAccess';
        $this->method = ServiceRequest::METHOD_POST;
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

    public function test_it_instantiates()
    {
        $this->assertInstanceOf(LtiServiceConnector::class, $this->connector);
    }

    public function test_it_gets_cached_access_token()
    {
        $this->mockCacheHasAccessToken();

        $result = $this->connector->getAccessToken($this->registration, ['scopeKey']);

        $this->assertEquals($result, $this->token);
    }

    public function test_it_gets_new_access_token()
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
            ->once()->andReturn();
        $this->client->shouldReceive('request')
            ->once()->andReturn($this->response);
        $this->response->shouldReceive('getBody')
            ->once()->andReturn($this->streamInterface);
        $this->streamInterface->shouldReceive('__toString')
            ->once()->andReturn(json_encode(['access_token' => $this->token]));
        $this->cache->shouldReceive('cacheAccessToken')->once();

        $result = $this->connector->getAccessToken($registration, ['scopeKey']);

        $this->assertEquals($this->token, $result);
    }

    public function test_it_caches_new_access_token_with_platform_expiry()
    {
        $cache = Mockery::mock(ICache::class, IAccessTokenExpiryCache::class);
        $cache->shouldReceive('getAccessToken')->andReturn();
        $cache->shouldReceive('cacheAccessTokenWithExpiry');
        $this->mockTokenResponse(['access_token' => $this->token, 'expires_in' => 3600]);
        $connector = new LtiServiceConnector($cache, $this->client);

        $result = $connector->getAccessToken($this->tokenRegistration(), ['scopeKey']);

        $this->assertSame($this->token, $result);
        $cache->shouldHaveReceived('cacheAccessTokenWithExpiry')
            ->with(Mockery::type('string'), $this->token, 3600)->once();
        $cache->shouldNotHaveReceived('cacheAccessToken');
    }

    public function test_it_caches_new_access_token_without_expiry_when_platform_omits_it()
    {
        $cache = Mockery::mock(ICache::class, IAccessTokenExpiryCache::class);
        $cache->shouldReceive('getAccessToken')->andReturn();
        $cache->shouldReceive('cacheAccessToken');
        $this->mockTokenResponse(['access_token' => $this->token]);
        $connector = new LtiServiceConnector($cache, $this->client);

        $result = $connector->getAccessToken($this->tokenRegistration(), ['scopeKey']);

        $this->assertSame($this->token, $result);
        $cache->shouldHaveReceived('cacheAccessToken')
            ->with(Mockery::type('string'), $this->token)->once();
        $cache->shouldNotHaveReceived('cacheAccessTokenWithExpiry');
    }

    public function test_it_caches_new_access_token_in_a_cache_without_expiry_support()
    {
        $cache = Mockery::mock(ICache::class);
        $cache->shouldReceive('getAccessToken')->andReturn();
        $cache->shouldReceive('cacheAccessToken');
        $this->mockTokenResponse(['access_token' => $this->token, 'expires_in' => 3600]);
        $connector = new LtiServiceConnector($cache, $this->client);

        $result = $connector->getAccessToken($this->tokenRegistration(), ['scopeKey']);

        $this->assertSame($this->token, $result);
        $cache->shouldHaveReceived('cacheAccessToken')
            ->with(Mockery::type('string'), $this->token)->once();
    }

    public function test_it_makes_a_service_request()
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

    public function test_it_retries_service_request_on401_error()
    {
        $this->method = ServiceRequest::METHOD_POST;
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

    public function test_it_throws_on_repeated401_errors()
    {
        $this->method = ServiceRequest::METHOD_POST;
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

    public function test_it_gets_all()
    {
        $method = ServiceRequest::METHOD_GET;
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
            ->twice()->andReturn($this->request);

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

    public function test_it_builds_log_message()
    {
        $this->mockMakeRequest();
        $this->request->shouldReceive('getErrorPrefix')
            ->once()->andReturn('Logging request data:');
        $this->request->shouldReceive('getMaskResponseLogs')
            ->once()->andReturn(false);

        $result = $this->connector::getLogMessage($this->request, ['foo' => 'bar'], ['baz' => 'bat']);

        $expected = "Logging request data: id {\"request_method\":\"POST\",\"request_url\":\"https:\/\/example.com\",\"response_headers\":{\"foo\":\"bar\"},\"response_body\":{\"baz\":\"bat\"},\"request_body\":\"{\\\"userId\\\":\\\"id\\\"}\"}";

        $this->assertEquals($expected, $result);
    }

    public function test_it_masks_sensitive_data_in_log_message()
    {
        $this->mockMakeRequest();
        $this->request->shouldReceive('getErrorPrefix')
            ->once()->andReturn('Logging request data:');
        $this->request->shouldReceive('getMaskResponseLogs')
            ->once()->andReturn(true);

        $result = $this->connector::getLogMessage($this->request, ['foo' => 'bar'], ['baz' => 'bat']);

        $expected = "Logging request data: id {\"request_method\":\"POST\",\"request_url\":\"https:\/\/example.com\",\"response_headers\":{\"foo\":\"***\"},\"response_body\":{\"baz\":\"***\"},\"request_body\":\"{\\\"userId\\\":\\\"id\\\"}\"}";

        $this->assertEquals($expected, $result);
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

    private function tokenRegistration(): LtiRegistration
    {
        return new LtiRegistration([
            'clientId' => 'client_id',
            'issuer' => 'issuer',
            'authServer' => 'auth_server',
            'toolPrivateKey' => file_get_contents(__DIR__.'/data/private.key'),
            'kid' => 'kid',
            'authTokenUrl' => 'auth_token_url',
        ]);
    }

    private function mockTokenResponse(array $tokenData): void
    {
        $this->client->shouldReceive('request')->andReturn($this->response);
        $this->response->shouldReceive('getBody')->andReturn($this->streamInterface);
        $this->streamInterface->shouldReceive('__toString')
            ->andReturn(json_encode($tokenData));
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
