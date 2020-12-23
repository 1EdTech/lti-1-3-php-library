<?php
namespace LTI\Interfaces;

interface Cache
{
    public function getLaunchData($key);
    public function cacheLaunchData($key, $jwt_body);
    public function cacheNonce($nonce);
    public function checkNonce($nonce);
}
