<?php

namespace Packback\Lti1p3\Interfaces;

interface ILtiServiceConnector
{
    public function getAccessToken(ILtiRegistration $registration, array $scopes);

    public function makeServiceRequest(ILtiRegistration $registration, array $scopes, IServiceRequest $request, bool $shouldRetry = true);
}
