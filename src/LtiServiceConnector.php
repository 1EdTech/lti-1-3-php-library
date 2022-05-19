<?php

namespace Packback\Lti1p3;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Interfaces\IServiceRequest;

class LtiServiceConnector implements ILtiServiceConnector
{
    public const NEXT_PAGE_REGEX = '/<([^>]*)>; ?rel="next"/i';

    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';

    // Supported request types which map to an error log message
    public const UNSUPPORTED_REQUEST = 0;
    public const SYNC_GRADE_REQUEST = 1;
    public const CREATE_LINEITEM_REQUEST = 2;
    public const GET_LINEITEMS_REQUEST = 3;
    public const AUTH_REQUEST = 3;

    private $cache;
    private $client;
    private $debuggingMode = false;
    private $errorMessages;

    public function __construct(
        ICache $cache,
        Client $client
    ) {
        $this->cache = $cache;
        $this->client = $client;

        $this->errorMessages = [
            static::UNSUPPORTED_REQUEST => 'Logging request data: ',
            static::SYNC_GRADE_REQUEST => 'Syncing grade for this lti_user_id: ',
            static::CREATE_LINEITEM_REQUEST => 'Creating lineitem: ',
            static::GET_LINEITEMS_REQUEST => 'Getting lineitems: ',
            static::AUTH_REQUEST => 'Authenticating: ',
        ];
    }

    public function setDebuggingMode(bool $enable): void
    {
        $this->debuggingMode = $enable;
    }

    public function getAccessToken(ILtiRegistration $registration, array $scopes)
    {
        // Get a unique cache key for the access token
        $accessTokenKey = $this->getAccessTokenCacheKey($registration, $scopes);
        // Get access token from cache if it exists
        $accessToken = $this->cache->getAccessToken($accessTokenKey);
        if ($accessToken) {
            return $accessToken;
        }

        // Build up JWT to exchange for an auth token
        $clientId = $registration->getClientId();
        $jwtClaim = [
            'iss' => $clientId,
            'sub' => $clientId,
            'aud' => $registration->getAuthServer(),
            'iat' => time() - 5,
            'exp' => time() + 60,
            'jti' => 'lti-service-token'.hash('sha256', random_bytes(64)),
        ];

        // Sign the JWT with our private key (given by the platform on registration)
        $jwt = JWT::encode($jwtClaim, $registration->getToolPrivateKey(), 'RS256', $registration->getKid());

        // Build auth token request headers
        $authRequest = [
            'grant_type' => 'client_credentials',
            'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
            'client_assertion' => $jwt,
            'scope' => implode(' ', $scopes),
        ];

        $url = $registration->getAuthTokenUrl();

        // Get Access
        $request = new ServiceRequest(static::METHOD_POST, $url);
        $request->setBody(json_encode([
            'form-params' => $authRequest,
        ]));
        $response = $this->makeRequest($request);

        $tokenData = $this->getResponseBody($response);

        // Cache access token
        $this->cache->cacheAccessToken($accessTokenKey, $tokenData['access_token']);

        return $tokenData['access_token'];
    }

    public function makeRequest(IServiceRequest $request)
    {
        $response = $this->client->request(
            $request->getMethod(),
            $request->getUrl(),
            $request->getPayload()
        );

        if ($this->debuggingMode) {
            $this->logRequest(
                static::AUTH_REQUEST,
                $request,
                $this->getResponseHeaders($response),
                $this->getResponseBody($response)
            );
        }

        return $response;
    }

    public function getResponseHeaders(Response $response): ?array
    {
        $responseHeaders = $response->getHeaders();
        array_walk($responseHeaders, function (&$value) {
            $value = $value[0];
        });

        return $responseHeaders;
    }

    public function getResponseBody(Response $response): ?array
    {
        $responseBody = (string) $response->getBody();

        return json_decode($responseBody, true);
    }

    public function makeServiceRequest(
        ILtiRegistration $registration,
        array $scopes,
        IServiceRequest $request,
        ?int $requestType = null,
        bool $shouldRetry = true
    ): array {
        // Set $requestType here, since static properties cannot be evaluated
        // as parameters
        if (!isset($requestType)) {
            $requestType = self::UNSUPPORTED_REQUEST;
        }

        $request->setAccessToken($this->getAccessToken($registration, $scopes));

        try {
            $response = $this->makeRequest($request);
        } catch (ClientException $e) {
            $status = $e->getResponse()->getStatusCode();

            // If the error was due to invalid authentication and the request
            // should be retried, clear the access token and retry it.
            if ($status === 401 && $shouldRetry) {
                $key = $this->getAccessTokenCacheKey($registration, $scopes);
                $this->cache->clearAccessToken($key);

                return $this->makeServiceRequest($registration, $scopes, $request, $requestType, false);
            }

            throw $e;
        }

        return [
            'headers' => $this->getResponseHeaders($response),
            'body' => $this->getResponseBody($response),
            'status' => $response->getStatusCode(),
        ];
    }

    public function getAll(
        ILtiRegistration $registration,
        array $scopes,
        IServiceRequest $request,
        string $key = null,
        ?int $requestType = null
    ): array {
        if ($request->getMethod() !== static::METHOD_GET) {
            throw new \Exception('An invalid method was specified by an LTI service requesting all items.');
        }

        $results = [];
        $nextUrl = $request->getUrl();

        while ($nextUrl) {
            $response = $this->makeServiceRequest($registration, $scopes, $request, $requestType);

            $page_results = $key === null ? ($response['body'] ?? []) : ($response['body'][$key] ?? []);
            $results = array_merge($results, $page_results);

            $nextUrl = $this->getNextUrl($response['headers']);
            if ($nextUrl) {
                $request->setUrl($nextUrl);
            }
        }

        return $results;
    }

    private function logRequest(
        int $requestType,
        IServiceRequest $request,
        array $responseHeaders,
        ?array $responseBody
    ): void {
        $contextArray = [
            'request_method' => $request->getMethod(),
            'request_url' => $request->getUrl(),
            'response_headers' => $responseHeaders,
            'response_body' => json_encode($responseBody),
        ];

        $requestBody = $request->getPayload()['body'] ?? '';

        if (!empty($requestBody)) {
            $contextArray['request_body'] = $requestBody;
        }

        $userId = json_decode($requestBody)->userId ?? '';

        $logMsg = $this->errorMessages[$requestType];

        error_log($logMsg.$userId.' '.print_r($contextArray, true));
    }

    private function getAccessTokenCacheKey(ILtiRegistration $registration, array $scopes)
    {
        sort($scopes);
        $scopeKey = md5(implode('|', $scopes));

        return $registration->getIssuer().$registration->getClientId().$scopeKey;
    }

    private function getNextUrl(array $headers)
    {
        $subject = $headers['Link'] ?? '';
        preg_match(LtiServiceConnector::NEXT_PAGE_REGEX, $subject, $matches);

        return $matches[1] ?? null;
    }
}
