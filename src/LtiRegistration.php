<?php
namespace Packback\Lti1p3;

use Packback\Lti1p3\Interfaces\LtiRegistrationInterface;

class LtiRegistration implements LtiRegistrationInterface
{

    private $issuer;
    private $client_id;
    private $key_set_url;
    private $auth_token_url;
    private $auth_login_url;
    private $auth_server;
    private $tool_private_key;
    private $kid;

    public function __construct(array $registration = [])
    {
        $this->issuer = $registration['issuer'] ?? null;
        $this->client_id = $registration['clientId'] ?? null;
        $this->key_set_url = $registration['keySetUrl'] ?? null;
        $this->auth_token_url = $registration['authTokenUrl'] ?? null;
        $this->auth_login_url = $registration['authLoginUrl'] ?? null;
        $this->auth_server = $registration['authServer'] ?? null;
        $this->tool_private_key = $registration['toolPrivateKey'] ?? null;
        $this->kid = $registration['kid'] ?? null;
    }

    public static function new(array $registration = []) {
        return new LtiRegistration($registration);
    }

    public function getIssuer()
    {
        return $this->issuer;
    }

    public function setIssuer($issuer)
    {
        $this->issuer = $issuer;
        return $this;
    }

    public function getClientId()
    {
        return $this->client_id;
    }

    public function setClientId($client_id)
    {
        $this->client_id = $client_id;
        return $this;
    }

    public function getKeySetUrl()
    {
        return $this->key_set_url;
    }

    public function setKeySetUrl($key_set_url)
    {
        $this->key_set_url = $key_set_url;
        return $this;
    }

    public function getAuthTokenUrl()
    {
        return $this->auth_token_url;
    }

    public function setAuthTokenUrl($auth_token_url)
    {
        $this->auth_token_url = $auth_token_url;
        return $this;
    }

    public function getAuthLoginUrl()
    {
        return $this->auth_login_url;
    }

    public function setAuthLoginUrl($auth_login_url)
    {
        $this->auth_login_url = $auth_login_url;
        return $this;
    }

    public function getAuthServer()
    {
        return empty($this->auth_server) ? $this->auth_token_url : $this->auth_server;
    }

    public function setAuthServer($auth_server)
    {
        $this->auth_server = $auth_server;
        return $this;
    }

    public function getToolPrivateKey()
    {
        return $this->tool_private_key;
    }

    public function setToolPrivateKey($tool_private_key)
    {
        $this->tool_private_key = $tool_private_key;
        return $this;
    }

    public function getKid()
    {
        return $this->kid ?? hash('sha256', trim($this->issuer . $this->client_id));
    }

    public function setKid($kid)
    {
        $this->kid = $kid;
        return $this;
    }

}

