<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Interfaces\IServiceRequest;

class ServiceRequest implements IServiceRequest
{
    public $method;
    public $url;
    public $body;
    public $contentType = 'application/json';
    public $accept = 'application/json';

    public function __construct(string $method, string $url)
    {
        $this->method = $method;
        $this->url = $url;
    }

    public function getMethod(): string
    {
        return strtoupper($this->method);
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getPayload(): array
    {
        $payload = [
            'headers' => $this->getHeaders(),
        ];

        $body = $this->body();
        if ($body) {
            $payload['body'] = $body;
        }

        return $payload;
    }

    public function setAccessToken(string $accessToken): IServiceRequest
    {
        $this->accessToken = 'Bearer '.$accessToken;

        return $this;
    }

    public function setBody(string $body): IServiceRequest
    {
        $this->body = $body;

        return $this;
    }

    public function setAccept(string $accept): IServiceRequest
    {
        $this->accept = $accept;

        return $this;
    }

    public function setContentType(string $contentType): IServiceRequest
    {
        $this->contentType = $contentType;

        return $this;
    }

    private function getHeaders(): array
    {
        $headers = [
            'Authorization' => $this->accessToken,
            'Accept' => $this->accept,
        ];

        if ($this->getMethod() === LtiServiceConnector::METHOD_POST) {
            $headers['Content-Type'] = $this->contentType;
        }

        return $headers;
    }

    private function getBody(): ?string
    {
        return $this->body;
    }
}
