<?php
namespace IMSGlobal\LTI;

use phpseclib3\Crypt\RSA;
use \Firebase\JWT\JWT;

class JWKS_Endpoint {

    private $keys;

    public function __construct(array $keys) {
        $this->keys = $keys;
    }

    public static function new($keys) {
        return new JWKS_Endpoint($keys);
    }

    public static function from_issuer(Database $database, $issuer) {
        $registration = $database->find_registration_by_issuer($issuer);
        return new JWKS_Endpoint([$registration->get_kid() => $registration->get_tool_private_key()]);
    }

    public static function from_registration(LTI_Registration $registration) {
        return new JWKS_Endpoint([$registration->get_kid() => $registration->get_tool_private_key()]);
    }

    public function get_public_jwks() {
        $jwks = [];
        foreach ($this->keys as $kid => $private_key) {
			$public_key = RSA::loadPrivateKey( $private_key )->getPublicKey();

			$public_key_reflection = new \ReflectionClass( $public_key );

			$exponent_property = $public_key_reflection->getProperty( 'publicExponent' )->setAccessible( true );
			$public_exponent = $exponent_property->getValue( $public_key );

			$modulus_property = $public_key_reflection->getProperty( 'modulus' )->setAccessible( true );
			$modulus = $modulus_property->getValue( $public_key );

            $components = [
                'kty' => 'RSA',
                'alg' => 'RS256',
                'use' => 'sig',
                'e' => JWT::urlsafeB64Encode( $public_exponent->toBytes() ),
                'n' => JWT::urlsafeB64Encode( $modulus->toBytes() ),
                'kid' => $kid,
            ];
            $jwks[] = $components;
        }
        return ['keys' => $jwks];
    }

    public function output_jwks() {
        echo json_encode($this->get_public_jwks());
    }

}
