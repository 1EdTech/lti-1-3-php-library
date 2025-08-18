<?php
namespace IMSGlobal\LTI;

use Firebase\JWT\JWT;

class LTI_Service_Connector {

    const NEXT_PAGE_REGEX = "/^Link:.*<([^>]*)>; ?rel=\"next\"/i";

    private LTI_Registration $registration;

    /** @var array<string> $access_tokens */
    private array $access_tokens = [];

    public function __construct(LTI_Registration $registration) {
        $this->registration = $registration;
    }

    /**
     * Get access token for a given set of scopes.
     * @param array<string> $scopes Scopes to look for.
     * @return string Access token.
     */
    public function get_access_token(array $scopes): string {

        // Don't fetch the same key more than once.
        sort($scopes);
        $scope_key = md5(implode('|', $scopes));
        if (isset($this->access_tokens[$scope_key])) {
            return $this->access_tokens[$scope_key];
        }

        // Build up JWT to exchange for an auth token
        $client_id = $this->registration->get_client_id();
        $jwt_claim = [
                "iss" => $client_id,
                "sub" => $client_id,
                "aud" => $this->registration->get_auth_server(),
                "iat" => time() - 5,
                "exp" => time() + 60,
                "jti" => 'lti-service-token' . hash('sha256', random_bytes(64))
        ];

        // Sign the JWT with our private key (given by the platform on registration)
        $jwt = JWT::encode($jwt_claim, $this->registration->get_tool_private_key(), 'RS256', $this->registration->get_kid());

        // Build auth token request headers
        $auth_request = [
            'grant_type' => 'client_credentials',
            'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
            'client_assertion' => $jwt,
            'scope' => implode(' ', $scopes)
        ];

        $url = $this->registration->get_auth_token_url();
        if (strlen($url) == 0) {
            throw new LTI_Exception("Auth token URL was empty.");
        }

        // Make request to get auth token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($auth_request));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $resp = curl_exec($ch);
        if (!is_string($resp)) {
            throw new LTI_Exception("Failed to retrieve access token.");
        }
        $token_data = json_decode($resp, true);
        curl_close ($ch);

        return $this->access_tokens[$scope_key] = $token_data['access_token'];
    }

    /**
     * Make a service request.
     * @param array<string> $scopes Scopes for the request.
     * @param string $method HTTP Method to use.
     * @param string $url Target URL for the request.
     * @param mixed $body Body for the request.
     * @param string $content_type Content type (OPTIONAL, defaults to
     *   application/json).
     * @param string $accept Accept type (OPTIONAL, defaults to
     *   application/json).
     * @return array<string, mixed> Array with 'headers' and 'body' keys.
     */
    public function make_service_request(
        array $scopes,
        string $method,
        string $url,
        mixed $body = null,
        string $content_type = 'application/json',
        string $accept = 'application/json'
    ): array {
        if (strlen($url) == 0) {
            throw new LTI_Exception("Url can not be empty.");
        }

        $ch = curl_init();
        $headers = [
            'Authorization: Bearer ' . $this->get_access_token($scopes),
            'Accept:' . $accept,
        ];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, strval($body));
            $headers[] = 'Content-Type: ' . $content_type;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        if (!is_string($response)){
            throw new LTI_Exception('Request Error:' . curl_error($ch));
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
