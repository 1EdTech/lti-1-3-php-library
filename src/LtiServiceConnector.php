<?php

namespace Packback\Lti1p3;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;

class LtiServiceConnector implements ILtiServiceConnector
{
    const NEXT_PAGE_REGEX = '/^Link:.*<([^>]*)>; ?rel="next"/i';

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    private $cache;
    private $client;
    private $registration;
    private $access_tokens = [];

    public function __construct(ILtiRegistration $registration, ICache $cache, Client $client)
    {
        $this->registration = $registration;
        $this->cache = $cache;
        $this->client = $client;
    }

    public function getAccessToken(array $scopes)
    {
        // Don't fetch the same key more than once.
        sort($scopes);

        // Build up JWT to exchange for an auth token
        $clientId = $this->registration->getClientId();

        // Store access token with a unique key
        $accessTokenKey = $this->getAccessTokenCacheKey($scopes);

        // Get Access Token from cache if it exists and is not expired.
        if ($this->cache->getAccessToken($accessTokenKey)) {
            return $this->cache->getAccessToken($accessTokenKey);
        }

        $jwtClaim = [
                'iss' => $clientId,
                'sub' => $clientId,
                'aud' => $this->registration->getAuthServer(),
                'iat' => time() - 5,
                'exp' => time() + 60,
                'jti' => 'lti-service-token'.hash('sha256', random_bytes(64)),
        ];

        // Sign the JWT with our private key (given by the platform on registration)
        $jwt = JWT::encode($jwtClaim, $this->registration->getToolPrivateKey(), 'RS256', $this->registration->getKid());

        // Build auth token request headers
        $authRequest = [
            'grant_type' => 'client_credentials',
            'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
            'client_assertion' => $jwt,
            'scope' => implode(' ', $scopes),
        ];

        $url = $this->registration->getAuthTokenUrl();

        // Get Access
        $response = $this->client->post($url, [
            'timeout' => 10,
            'form_params' => $authRequest,
        ]);

        $tokenData = json_decode($response->getBody()->__toString(), true);

        // Cache access token
        $this->cache->cacheAccessToken($accessTokenKey, $tokenData['access_token']);

        return $tokenData['access_token'];
    }

    public function makeServiceRequest(array $scopes, $method, $url, $body = null, $contentType = 'application/json', $accept = 'application/json')
    {
        $headers = [
            'Authorization' => 'Bearer '.$this->getAccessToken($scopes),
            'Accept' => $accept,
        ];

        switch ($method) {
            case 'POST':
                $headers = array_merge($headers, ['Content-Type' => $contentType]);
                $response = $this->client->request($method, $url, [
                    'headers' => $headers,
                    'json' => $body,
                    'timeout' => 60,
                ]);
                break;
            default:
                $response = $this->client->request($method, $url, [
                    'timeout' => 60,
                    'headers' => $headers,
                ]);
                break;
        }

        $respHeaders = $response->getHeaders();
        $respBody = $response->getBody();

        return [
            'headers' => array_filter(explode("\r\n", $respHeaders)),
            'body' => json_decode($respBody, true),
        ];
    }

    private function getAccessTokenCacheKey(array $scopes)
    {
        $scopeKey = md5(implode('|', $scopes));

        return $this->registration->getIssuer() . $this->registration->getClientId() . $scopeKey;
    }
}
