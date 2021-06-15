<?php

namespace Packback\Lti1p3;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Packback\Lti1p3\Interfaces\Cache;
use Packback\Lti1p3\Interfaces\LtiRegistrationInterface;
use Packback\Lti1p3\Interfaces\LtiServiceConnectorInterface;

class LtiServiceConnector implements LtiServiceConnectorInterface
{
    const NEXT_PAGE_REGEX = '/^Link:.*<([^>]*)>; ?rel="next"/i';

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    private $cache;
    private $registration;
    private $access_tokens = [];

    public function __construct(LtiRegistrationInterface $registration, Cache $cache = null)
    {
        $this->registration = $registration;
        $this->cache = $cache;
    }

    public function getAccessToken(array $scopes)
    {
        // Don't fetch the same key more than once.
        sort($scopes);
        $scope_key = md5(implode('|', $scopes));

        // Original Code
        // if (isset($this->access_tokens[$scope_key])) {
        //     return $this->access_tokens[$scope_key];
        // }

        // Davo's cache method
        // if (\Cache::get($scope_key)) {
        //     return \Cache::get($scope_key);
        // }

        // New Caching Method
        if ($this->cache->getAccessToken($scope_key)) {
            return $this->cache->getAccessToken($scope_key);
        }

        // Build up JWT to exchange for an auth token
        $client_id = $this->registration->getClientId();
        $jwt_claim = [
                "iss" => $client_id,
                "sub" => $client_id,
                "aud" => $this->registration->getAuthServer(),
                "iat" => time() - 5,
                "exp" => time() + 60,
                "jti" => 'lti-service-token' . hash('sha256', random_bytes(64))
        ];

        // Sign the JWT with our private key (given by the platform on registration)
        $jwt = JWT::encode($jwt_claim, $this->registration->getToolPrivateKey(), 'RS256', $this->registration->getKid());

        // Build auth token request headers
        $auth_request = [
            'grant_type' => 'client_credentials',
            'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
            'client_assertion' => $jwt,
            'scope' => implode(' ', $scopes)
        ];

        // Curl 
        // Make request to get auth token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->registration->getAuthTokenUrl());
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($auth_request));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $resp = curl_exec($ch);
        $token_data = json_decode($resp, true);
        curl_close ($ch);

        // Guzzle OAuth
        // $url = $this->registration->getAuthTokenUrl()
        // $stack = HandlerStack::create();

        // $middleware = new Oauth1([
        //     'consumer_key'    => 'my_key',
        //     'consumer_secret' => 'my_secret',
        //     'token'           => 'my_token',
        //     'token_secret'    => 'my_token_secret'
        // ]);
        // $stack->push($middleware);

        // $client = new Client([
        //     'base_uri' => $url,
        //     'handler' => $stack,
        //     'auth' => 'oauth'
        // ]);

        // // $res = $client->get($url);

        // $response = $client->post($url, [
        //     'headers' => [
        //         "authorization" => "Client-ID " . $client_id
        //         'Content-Type' => 'application/json',
        //     ],
        //     // This will add the necessary Authorization header
        //     'auth' => 'oauth',
        //     'timeout' => 10,
        //     'body' => $auth_request
        //     // 'form_params' => $auth_request
        // ]);

        // // Do I need this?
        // $token_data = json_decode($response, true);

        // Davo's cache method
        // \Cache::put($scope_key, $token_data['access_token']);

        // New Caching 
        $this->cache->cacheAccessToken($scope_key, $token_data['access_token']);

        // Original Code
        // return $this->access_tokens[$scope_key] = $token_data['access_token'];

        // Davo's cache method
        return $token_data['access_token'];
    }

    public function makeServiceRequest(array $scopes, $method, $url, $body = null, $contentType = 'application/json', $accept = 'application/json')
    {
        $ch = curl_init();
        $headers = [
            'Authorization: Bearer '.$this->getAccessToken($scopes),
            'Accept:'.$accept,
        ];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, strval($body));
            $headers[] = 'Content-Type: '.$contentType;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Request Error:'.curl_error($ch);
        }
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        $resp_headers = substr($response, 0, $header_size);
        $resp_body = substr($response, $header_size);

        return [
            'headers' => array_filter(explode("\r\n", $resp_headers)),
            'body' => json_decode($resp_body, true),
        ];
    }
}
