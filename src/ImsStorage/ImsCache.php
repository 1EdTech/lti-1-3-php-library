<?php

namespace Packback\Lti1p3\ImsStorage;

use Packback\Lti1p3\Interfaces\ICache;

class ImsCache implements ICache
{
    private $cache;

    public function getLaunchData($key)
    {
        $this->loadCache();

        return $this->cache[$key] ?? null;
    }

    public function cacheLaunchData($key, $jwtBody)
    {
        $this->loadCache();

        $this->cache[$key] = $jwtBody;
        $this->saveCache();
    }

    public function cacheNonce($nonce)
    {
        $this->loadCache();

        $this->cache['nonce'][$nonce] = true;
        $this->saveCache();
    }

    public function checkNonce($nonce)
    {
        $this->loadCache();

        return isset($this->cache['nonce'][$nonce]);
    }

    public function cacheAccessToken($key, $accessToken)
    {
        $this->loadCache();

        $this->cache[$key] = $accessToken;
        $this->saveCache();
    }

    public function getAccessToken($key)
    {
        $this->loadCache();

        return $this->cache[$key] ?? null;
    }

    public function clearAccessToken($key)
    {
        $this->loadCache();

        unset($this->cache[$key]);
        $this->saveCache();

        return $this->cache;
    }

    private function loadCache()
    {
        $cache = file_get_contents(sys_get_temp_dir().'/lti_cache.txt');
        if (empty($cache)) {
            file_put_contents(sys_get_temp_dir().'/lti_cache.txt', '{}');
            $this->cache = [];
        }
        $this->cache = json_decode($cache, true);
    }

    private function saveCache()
    {
        file_put_contents(sys_get_temp_dir().'/lti_cache.txt', json_encode($this->cache));
    }
}
