<?php
namespace IMSGlobal\LTI;

use \Firebase\JWT\JWT;
use \Firebase\JWT\JWK;

class LTI_Service_Connector {
    private $registration;
    private $access_tokens = [];

    public function __construct(LTI_Registration $registration) {
        $this->registration = $registration;
    }

    public function get_access_token($scopes) {

        // Don't fetch the same key more than once.
        sort($scopes);
        $scope_key = md5(implode('|', $scopes));
        if (isset($this->access_tokens[$scope_key])) {
            return $this->access_tokens[$scope_key];
        }

        // Build up JWT to exchange for an auth token
        $client_id = $this->registration->get_client_id();
        $auth_url = $this->registration->get_auth_token_url();
        $jwt_claim = [
                "iss" => $client_id,
                "sub" => $client_id,
                "aud" => $auth_url,
                "iat" => time() - 5,
                "exp" => time() + 60,
                "jti" => uniqid("lti-service-token")
        ];

        // Sign the JWT with our private key (given by the platform on registration)
        $jwt = JWT::encode($jwt_claim, $this->registration->get_tool_private_key(), 'RS256');

        // Build auth token request headers
        $auth_request = [
            'grant_type' => 'client_credentials',
            'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
            'client_assertion' => $jwt,
            'scope' => implode(' ', $scopes)
        ];

        // Make request to get auth token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $auth_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($auth_request));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($ch);
        $token_data = json_decode($resp, true);
        curl_close ($ch);

        return $this->access_tokens[$scope_key] = $token_data['access_token'];
    }

    public function make_service_request($scopes, $method, $url, $body = null, $content_type = 'application/json', $accept = 'application/json') {
        $ch = curl_init();
        $headers = [
            'Authorization: Bearer ' . $this->get_access_token($scopes),
            'Accept:' . $accept,
        ];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, strval($body));
            $headers[] = 'Content-Type: ' . $content_type;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        if (curl_errno($ch)){
            echo 'Request Error:' . curl_error($ch);
        }
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close ($ch);

        $resp_headers = substr($response, 0, $header_size);
        $resp_body = substr($response, $header_size);
        return [
            'headers' => array_filter(explode("\r\n", $resp_headers)),
            'body' => json_decode($resp_body, true),
        ];
    }
}
?>