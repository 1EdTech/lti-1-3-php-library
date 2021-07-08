<?php

namespace Packback\Lti1p3\Interfaces;

interface ILtiServiceConnector
{
    public function getAccessToken(array $scopes);

    public function makeServiceRequest(array $scopes, string $method, string $url, string $body = null, $contentType = 'application/json', $accept = 'application/json');
}
