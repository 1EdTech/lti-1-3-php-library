<?php

namespace Packback\Lti1p3\Interfaces;

interface ICache
{
    public function getLaunchData($key);

    public function cacheLaunchData($key, $jwtBody);

    public function cacheNonce($nonce);

    public function checkNonce($nonce);

    public function cacheAccessToken($key, $accessToken);

    public function getAccessToken($key);

    public function clearAccessToken($key);
}
