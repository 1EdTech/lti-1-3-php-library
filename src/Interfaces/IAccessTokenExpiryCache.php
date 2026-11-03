<?php

namespace Packback\Lti1p3\Interfaces;

/**
 * A cache that can keep an access token for as long as the platform says it
 * is valid. When the cache given to LtiServiceConnector implements this, the
 * connector passes the token response's expires_in here instead of calling
 * ICache::cacheAccessToken().
 */
interface IAccessTokenExpiryCache
{
    /**
     * @param  int  $expiresInSeconds  The expires_in from the platform's token
     *                                 response, unadjusted; leave a margin for
     *                                 clock skew and request time.
     */
    public function cacheAccessTokenWithExpiry(
        string $key,
        string $accessToken,
        int $expiresInSeconds
    ): void;
}
