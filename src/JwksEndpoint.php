<?php

namespace Packback\Lti1p3;

use Firebase\JWT\JWT;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use phpseclib\Crypt\RSA;

class JwksEndpoint
{
    private $keys;

    public function __construct(array $keys)
    {
        $this->keys = $keys;
    }

    public static function new(array $keys)
    {
        return new JwksEndpoint($keys);
    }

    public static function fromIssuer(IDatabase $database, $issuer)
    {
        $registration = $database->findRegistrationByIssuer($issuer);

        return new JwksEndpoint([$registration->getKid() => $registration->getToolPrivateKey()]);
    }

    public static function fromRegistration(ILtiRegistration $registration)
    {
        return new JwksEndpoint([$registration->getKid() => $registration->getToolPrivateKey()]);
    }

    public function getPublicJwks()
    {
        $jwks = [];
        foreach ($this->keys as $kid => $private_key) {
            $key = new RSA();
            $key->setHash('sha256');
            $key->loadKey($private_key);
            $key->setPublicKey(false, RSA::PUBLIC_FORMAT_PKCS8);
            if (!$key->publicExponent) {
                continue;
            }
            $components = [
                'kty' => 'RSA',
                'alg' => 'RS256',
                'use' => 'sig',
                'e' => JWT::urlsafeB64Encode($key->publicExponent->toBytes()),
                'n' => JWT::urlsafeB64Encode($key->modulus->toBytes()),
                'kid' => $kid,
            ];
            $jwks[] = $components;
        }

        return ['keys' => $jwks];
    }

    public function outputJwks()
    {
        echo json_encode($this->getPublicJwks());
    }
}
