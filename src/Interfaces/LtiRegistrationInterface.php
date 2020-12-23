<?php
namespace Packback\Lti1p3\Interfaces;

interface LtiRegistrationInterface
{
    public function getIssuer();
    public function setIssuer($issuer);
    public function getClientId();
    public function setClientId($client_id);
    public function getKeySetUrl();
    public function setKeySetUrl($key_set_url);
    public function getAuthTokenUrl();
    public function setAuthTokenUrl($auth_token_url);
    public function getAuthLoginUrl();
    public function setAuthLoginUrl($auth_login_url);
    public function getAuthServer();
    public function setAuthServer($auth_server);
    public function getToolPrivateKey();
    public function setToolPrivateKey($tool_private_key);
    public function getKid();
    public function setKid($kid);
}
