<?php
namespace IMSGlobal\LTI;

use phpseclib\Crypt\RSA;
use \Firebase\JWT\JWT;

class JWKS_Endpoint {

    /** @var array<mixed> $keys */
    private array $keys;

    /**
     * @param array<mixed> $keys
     */
    public function __construct(array $keys) {
        $this->keys = $keys;
    }

    /**
     * @param array<mixed> $keys
     */
    public static function new(array $keys): self {
        return new JWKS_Endpoint($keys);
    }

    public static function from_issuer(
        Database $database,
        string $issuer,
        string $client_id
    ): self {
        $registration = $database->find_registration_by_issuer($issuer, $client_id);
        if ($registration === null) {
            throw new LTI_Exception("Could not find registration");
        }
        return new self([$registration->get_kid() => $registration->get_tool_private_key()]);
    }

    public static function from_registration(
        LTI_Registration $registration
    ): self {
        return new self([$registration->get_kid() => $registration->get_tool_private_key()]);
    }

    /**
     * @return array<mixed>
     */
    public function get_public_jwks(): array {
        $jwks = [];
        foreach ($this->keys as $kid => $private_key) {
            $key = new RSA();
            $key->setHash("sha256");
            $key->loadKey($private_key);
            $key->setPublicKey("", RSA::PUBLIC_FORMAT_PKCS8);
            if ( !$key->publicExponent ) {
                continue;
            }
            $components = array(
                'kty' => 'RSA',
                'alg' => 'RS256',
                'use' => 'sig',
                'e' => JWT::urlsafeB64Encode($key->publicExponent->toBytes()),
                'n' => JWT::urlsafeB64Encode($key->modulus->toBytes()),
                'kid' => $kid,
            );
            $jwks[] = $components;
        }
        return ['keys' => $jwks];
    }

    public function output_jwks(): void {
        echo json_encode($this->get_public_jwks());
    }

}
