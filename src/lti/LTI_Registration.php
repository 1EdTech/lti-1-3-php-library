<?php
namespace IMSGlobal\LTI;

use phpseclib\Crypt\RSA;
use \Firebase\JWT\JWT;

class LTI_Registration {

    private $issuer;
    private $client_id;
    private $key_set_url;
    private $auth_token_url;
    private $auth_login_url;
    private $tool_private_key;

    public static function new() {
        return new LTI_Registration();
    }

    public function get_issuer() {
        return $this->issuer;
    }

    public function set_issuer($issuer) {
        $this->issuer = $issuer;
        return $this;
    }

    public function get_client_id() {
        return $this->client_id;
    }

    public function set_client_id($client_id) {
        $this->client_id = $client_id;
        return $this;
    }

    public function get_key_set_url() {
        return $this->key_set_url;
    }

    public function set_key_set_url($key_set_url) {
        $this->key_set_url = $key_set_url;
        return $this;
    }

    public function get_auth_token_url() {
        return $this->auth_token_url;
    }

    public function set_auth_token_url($auth_token_url) {
        $this->auth_token_url = $auth_token_url;
        return $this;
    }

    public function get_auth_login_url() {
        return $this->auth_login_url;
    }

    public function set_auth_login_url($auth_login_url) {
        $this->auth_login_url = $auth_login_url;
        return $this;
    }

    public function get_tool_private_key() {
        return $this->tool_private_key;
    }

    public function set_tool_private_key($tool_private_key) {
        $this->tool_private_key = $tool_private_key;
        return $this;
    }

    public function get_public_jwk() {
        $key = new RSA();
        $key->setPrivateKey($this->get_tool_private_key());
        if ( !$key->publicExponent ){
            return [];
        }
        $kid = hash('sha256', trim($this->issuer . $this->client_id));
        $components = array(
            'kty' => 'RSA',
            'alg' => 'RS256',
            'e' => JWT::urlsafeB64Encode($key->publicExponent->toBytes()),
            'n' => JWT::urlsafeB64Encode($key->modulus->toBytes()),
            'kid' => $kid,
        );
        if ($key->exponent != $key->publicExponent) {
            $components = array_merge($components, array(
            'd' => JWT::urlsafeB64Encode($key->exponent->toBytes())
            ));
        }
        $jwks[] = $components;
        return ['keys' => $jwks];
    }

}

?>