<?php

namespace Packback\Lti1p3\Interfaces;

interface ILtiServiceConnector
{
    public function getAccessToken(array $scopes);

    public function makeServiceRequest(array $scopes, $method, $url, $body = null, $contentType = 'application/json', $accept = 'application/json');
}
