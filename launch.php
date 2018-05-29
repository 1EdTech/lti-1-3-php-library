<?php
require_once('util.php');
require_once 'jwt/src/BeforeValidException.php';
require_once 'jwt/src/ExpiredException.php';
require_once 'jwt/src/SignatureInvalidException.php';
require_once 'jwt/src/JWT.php';
require_once 'jwt/src/JWK.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\JWK;

session_start();

// Make sure request is a post
if (empty($_POST)) {
    die_with("Launch must be a POST");
}

// Make sure JWT has been passed in the request
$raw_jwt = $_REQUEST['jwt'] ?: $_REQUEST['id_token'];
if (empty($raw_jwt)) {
    die_with("JWT not found");
}

// Decode JWT Head and Body
$jwt_parts = explode('.', $raw_jwt);
$jwt_head = json_decode(base64_decode($jwt_parts[0]), true);
$jwt_body = json_decode(base64_decode($jwt_parts[1]), true);

// Find client_id from the aud field in the JWT (could be an array)
$client_id = is_array($jwt_body['aud']) ? $jwt_body['aud'][0] : $jwt_body['aud'];
if (empty($client_id)) {
    die_with("Client id missing");
}

// Find key set URL to fetch the JWKS
$key_set_url = $_SESSION['key_set_urls'][$jwt_body['iss'].':'.$client_id];
if (empty($key_set_url)) {
    // If there is no key set url, go to registration
    include('registerform.php');
    die;
}

// Download key set
$public_key_set = json_decode(file_get_contents($key_set_url), true);

// Find key used to sign the JWT (matches the KID in the header)
$public_key;
foreach ($public_key_set['keys'] as $key) {
    if ($key['kid'] == $jwt_head['kid']) {
        $public_key = openssl_pkey_get_details(JWK::parseKey($key));
        break;
    }
}

// Make sure we found the correct key
if (empty($public_key)) {
    die_with("Failed to find KID: " . $jwt_head['kid'] . " in keyset from " . $public_key_url);
}

// Validate JWT signature
try {
    JWT::decode($raw_jwt, $public_key['key'], array('RS256'));
    $_SESSION['current_request'] = $jwt_body;
} catch(Exception $e) {
    die_with($e->getMessage());
}

// Success! Everything is signed correctly, now load the game
?>
<canvas id="breakout" width="800" height="500" style="border:1px solid #000000;">
</canvas>

<script type="text/javascript" src="js/breakout.js" charset="utf-8"></script>