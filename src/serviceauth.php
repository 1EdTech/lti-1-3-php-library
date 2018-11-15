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

    $session = $_SESSION[$_COOKIE['be_session_id']];
    // Build up JWT to exchange for an auth token
    //$auth_url = $_SESSION['auth_token_urls'][$session['iss'].':'.$session['aud']];
    $client_id = is_array($session['aud']) ? $session['aud'][0] : $session['aud'];
    $auth_url = $_SESSION['issuers'][$session['iss']]['auth_token_url'];
    $jwt_claim = [
            "iss" => "http://martinscooltools.example.com",
            "sub" => $session['aud'],
            "aud" => $auth_url,
            "iat" => time(),
            "exp" => time()+600,
            "jti" => uniqid("testing")
    ];

    // Sign the JWT with our private key (given by the platform on registration)
    $jwt = JWT::encode($jwt_claim, $_SESSION['issuers'][$session['iss']]['key']['private'], 'RS256');

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