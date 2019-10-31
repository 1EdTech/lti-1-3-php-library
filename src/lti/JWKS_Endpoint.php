<?php
namespace IMSGlobal\LTI;

use phpseclib\Crypt\RSA;
use \Firebase\JWT\JWT;

class JWKS_Endpoint {

    private $registration;

    public function __construct(LTI_Registration $registration) {
        $this->registration = $registration;
    }

    public static function new(LTI_Registration $registration) {
        return new LTI_Registration($registration);
    }

    public static function from_issuer(Database $database, $issuer) {
        $registration = $database->find_registration_by_issuer($issuer);
        return new LTI_Registration($registration);
    }

    public function get_public_jwks() {
        $key = new RSA();
        $key->setPrivateKey($this->registration->get_tool_private_key());
        $key->setPublicKey();
        if ( !$key->publicExponent ) {
            return [];
        }
        $components = array(
            'kty' => 'RSA',
            'alg' => 'RS256',
            'e' => JWT::urlsafeB64Encode($key->publicExponent->toBytes()),
            'n' => JWT::urlsafeB64Encode($key->modulus->toBytes()),
            'kid' => $this->registration->get_kid(),
        );
        if ($key->exponent != $key->publicExponent) {
            $components = array_merge($components, array(
            'd' => JWT::urlsafeB64Encode($key->exponent->toBytes())
            ));
        }
        $jwks[] = $components;
        return ['keys' => $jwks];
    }

    public function output_jwks() {
        echo json_encode($this->get_public_jwks());
    }

}