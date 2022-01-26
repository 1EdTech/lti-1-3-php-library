<?php

namespace Packback\Lti1p3\Interfaces;

use GuzzleHttp\Psr7\Response;
use Packback\Lti1p3\LtiLineitem;

interface ILtiServiceConnector
{
    public function getAccessToken(ILtiRegistration $registration, array $scopes);

    public function makeRequest(IServiceRequest $request);

    public function getResponseBody(Response $request): ?array;

    public function makeServiceRequest(
        ILtiRegistration $registration,
        array $scopes,
        IServiceRequest $request,
        bool $shouldRetry = true
    ): array;

    public function getAll(
        ILtiRegistration $registration,
        array $scopes,
        IServiceRequest $request,
        string $key
    ): array;

    public function get(
        ILtiRegistration $registration,
        array $scopes,
        IServiceRequest $request,
        string $key
    ): LtiLineitem;

    public function setDebuggingMode(bool $enable): void;
}
