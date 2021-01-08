<?php
namespace Packback\Lti1p3\Interfaces;

interface LtiServiceConnectorInterface
{
    public function getAccessToken(array $scopes);
    public function makeServiceRequest(array $scopes, $method, $url, $body, $contentType, $accept);
}
