<?php
namespace IMSGlobal\LTI;

require_once('../jwt/src/BeforeValidException.php');
require_once('../jwt/src/ExpiredException.php');
require_once('../jwt/src/SignatureInvalidException.php');
require_once('../jwt/src/JWT.php');
require_once('../jwt/src/JWK.php');

use \Firebase\JWT\JWT;
use \Firebase\JWT\JWK;

class LTI_Service_Connector {
    private $registration;

    public function __construct(LTI_Registration $registration) {
        $this->registration = $registration;
    }

    public function get_access_token($scopes) {
        // Build up JWT to exchange for an auth token
        $client_id = $this->registration->get_client_id();
        $auth_url = $this->registration->get_auth_token_url();
        $jwt_claim = [
                "iss" => "TODO_CHANGE_ME",
                "sub" => $client_id,
                "aud" => $auth_url,
                "iat" => time(),
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

        return $token_data['access_token'];
    }

    public function make_service_request($scopes, $method, $url, $body = null, $content_type = 'application/json') {
        $ch = curl_init();
        $headers = ['Authorization: Bearer '. $this->get_access_token($scopes)];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, strval($body));
            $headers[] = 'Content-Type: ' . $content_type;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $resp_raw = curl_exec($ch);
        $resp = json_decode($resp_raw, true);
        if (curl_errno($ch)){
            echo 'Request Error:' . curl_error($ch);
        }
        curl_close ($ch);
        return $resp;
    }
}
?>