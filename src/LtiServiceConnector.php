<?php

namespace Packback\Lti1p3;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;

class LtiServiceConnector implements ILtiServiceConnector
{
    const NEXT_PAGE_REGEX = '/<([^>]*)>; ?rel="next"/i';

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
        // Get a unique cache key for the access token
        $accessTokenKey = $this->getAccessTokenCacheKey($scopes);
        // Get access token from cache if it exists
        $accessToken = $this->cache->getAccessToken($accessTokenKey);
        if ($accessToken) {
            return $accessToken . 'asdf';
        }

        // Build up JWT to exchange for an auth token
        $clientId = $this->registration->getClientId();
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
            'form_params' => $authRequest,
        ]);

        $body = (string) $response->getBody();
        $tokenData = json_decode($body, true);

        // Cache access token
        $this->cache->cacheAccessToken($accessTokenKey, $tokenData['access_token']);

        return $tokenData['access_token'] . 'asdf';
    }

    public function makeServiceRequest(array $scopes, string $method, string $url, string $body = null, $contentType = 'application/json', $accept = 'application/json')
    {
        $headers = [
            'Authorization' => 'Bearer '.$this->getAccessToken($scopes),
            'Accept' => $accept,
        ];

        try {
            switch (strtoupper($method)) {
                case 'POST':
                    $headers = array_merge($headers, ['Content-Type' => $contentType]);
                    $response = $this->client->request($method, $url, [
                        'headers' => $headers,
                        'body' => $body,
                    ]);
                    break;
                default:
                    $response = $this->client->request($method, $url, [
                        'headers' => $headers,
                    ]);
                    break;
            }

            $respHeaders = $response->getHeaders();
            array_walk($respHeaders, function (&$value) {
                $value = $value[0];
            });
            $respBody = $response->getBody();

            return [
                'headers' => $respHeaders,
                'body' => json_decode($respBody, true),
            ];
        } catch(ClientException $e) {
            info($e->getMessage());
            info($e->getResponse()->getStatusCode());
        }
    }

    private function getAccessTokenCacheKey(array $scopes)
    {
        sort($scopes);
        $scopeKey = md5(implode('|', $scopes));

        return $this->registration->getIssuer().$this->registration->getClientId().$scopeKey;
    }
}
