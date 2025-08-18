<?php
namespace IMSGlobal\LTI;

class LTI_Registration {

    private string $issuer = "";
    private string $client_id = "";
    private string $key_set_url = "";
    private string $auth_token_url = "";
    private string $auth_login_url = "";
    private string $auth_server = "";
    private string $tool_private_key = "";
    private string $kid = "";

    public static function new(): self {
        return new LTI_Registration();
    }

    public function get_issuer(): string {
        return $this->issuer;
    }

    public function set_issuer(string $issuer): self {
        $this->issuer = $issuer;
        return $this;
    }

    public function get_client_id(): string {
        return $this->client_id;
    }

    public function set_client_id(string $client_id): self {
        $this->client_id = $client_id;
        return $this;
    }

    public function get_key_set_url(): string {
        return $this->key_set_url;
    }

    public function set_key_set_url(string $key_set_url): self {
        $this->key_set_url = $key_set_url;
        return $this;
    }

    public function get_auth_token_url(): string {
        return $this->auth_token_url;
    }

    public function set_auth_token_url(string $auth_token_url): self {
        $this->auth_token_url = $auth_token_url;
        return $this;
    }

    public function get_auth_login_url(): string {
        return $this->auth_login_url;
    }

    public function set_auth_login_url(string $auth_login_url): self {
        $this->auth_login_url = $auth_login_url;
        return $this;
    }

    public function get_auth_server(): string {
        return empty($this->auth_server) ? $this->auth_token_url : $this->auth_server;
    }

    public function set_auth_server(string $auth_server): self {
        $this->auth_server = $auth_server;
        return $this;
    }

    public function get_tool_private_key(): string {
        return $this->tool_private_key;
    }

    public function set_tool_private_key(string $tool_private_key): self {
        $this->tool_private_key = $tool_private_key;
        return $this;
    }

    public function get_kid(): string {
        return empty($this->kid) ? hash('sha256', trim($this->issuer . $this->client_id)) : $this->kid;
    }

    public function set_kid(string $kid): self {
        $this->kid = $kid;
        return $this;
    }

}
