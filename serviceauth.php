<?php
require_once('keys.php');
require_once 'jwt/src/BeforeValidException.php';
require_once 'jwt/src/ExpiredException.php';
require_once 'jwt/src/SignatureInvalidException.php';
require_once 'jwt/src/JWT.php';
require_once 'jwt/src/JWK.php';

use \Firebase\JWT\JWT;

session_start();

function get_access_token($scopes) {
    // Start auth fetching

    // Build up JWT to exchange for an auth token
    $auth_url = $_SESSION['auth_token_urls'][$_SESSION['current_request']['iss'].':'.$_SESSION['current_request']['aud']];
    $jwt_claim = [
            "iss" => "http://martinscooltools.example.com",
            "sub" => $_SESSION['current_request']['aud'],
            "aud" => $auth_url,
            "iat" => time(),
            "exp" => time()+60,
            "jti" => uniqid("testing")
    ];

    // Sign the JWT with our private key (given by the platform on registration)
    $jwt = JWT::encode($jwt_claim, $GLOBALS['privateKey'], 'RS256');

    // Build auth token request headers
    $auth_request = [
        'grant_type' => 'client_credentials',
        'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
        'client_assertion' => $jwt,
        'scope' => implode(' ', $scopes)
    ];

    // Make request to get auth token
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$auth_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($auth_request));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $token_data = json_decode(curl_exec($ch), true);
    curl_close ($ch);

    return $token_data['access_token'];
}
?>