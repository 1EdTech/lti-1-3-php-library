<?php

namespace Packback\Lti1p3;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Interfaces\IServiceRequest;

class LtiServiceConnector implements ILtiServiceConnector
{
    const NEXT_PAGE_REGEX = '/<([^>]*)>; ?rel="next"/i';

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    private $cache;
    private $client;

    public function __construct(ICache $cache, Client $client)
    {
        $this->cache = $cache;
        $this->client = $client;
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
        $response = $this->client->post($url, [
            'form_params' => $authRequest,
        ]);

        $body = (string) $response->getBody();
        $tokenData = json_decode($body, true);

        // Cache access token
        $this->cache->cacheAccessToken($accessTokenKey, $tokenData['access_token']);

        return $tokenData['access_token'];
    }

    public function makeServiceRequest(
        ILtiRegistration $registration,
        array $scopes,
        IServiceRequest $request,
        bool $shouldRetry = true
    ) {
        $request->setAccessToken($this->getAccessToken($registration, $scopes));

        try {
            $response = $this->client->request(
                $request->getMethod(),
                $request->getUrl(),
                $request->getPayload()
            );
        } catch (ClientException $e) {
            $status = $e->getResponse()->getStatusCode();

            // If the error was due to invalid authentication and the request
            // should be retried, clear the access token and retry it.
            if ($status === 401 && $shouldRetry) {
                $key = $this->getAccessTokenCacheKey($registration, $scopes);
                $this->cache->clearAccessToken($key);

                return $this->makeServiceRequest($registration, $scopes, $request, false);
            }

            throw $e;
        }
        $respHeaders = $response->getHeaders();
        array_walk($respHeaders, function (&$value) {
            $value = $value[0];
        });
        $respBody = $response->getBody();

        return [
            'headers' => $respHeaders,
            'body' => json_decode($respBody, true),
            'status' => $response->getStatusCode(),
        ];
    }

    private function getAccessTokenCacheKey(ILtiRegistration $registration, array $scopes)
    {
        sort($scopes);
        $scopeKey = md5(implode('|', $scopes));

        return $registration->getIssuer().$registration->getClientId().$scopeKey;
    }
}
