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

// Check if this is an OIDC launch.
if (!empty($_REQUEST) && !empty($_REQUEST['iss']) && !empty($_REQUEST['login_hint'])) {
    // Check if the requested issuer has been registered.
    if (empty($_SESSION['issuers'][$_REQUEST['iss']])) {
        // If there is no key set url, go to registration
        $register_details = [
            'iss' => $_REQUEST['iss'],
            'client_id' => 'testing12345',    // What are the implications of this? We can only have one authorization url per issuer?
                                    // Would we not want a url per deployment?
        ];
        include('registerform.php');
        die;
    }

    // Return redirect page.
    include('authorize.php');
    die;
}

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
$jwt_head = json_decode(JWT::urlsafeB64Decode($jwt_parts[0]), true);
$jwt_body = json_decode(JWT::urlsafeB64Decode($jwt_parts[1]), true);

// Find client_id from the aud field in the JWT (could be an array)
$client_id = is_array($jwt_body['aud']) ? $jwt_body['aud'][0] : $jwt_body['aud'];
if (empty($client_id)) {
    header('Content-Type: text/plain');
    var_dump($jwt_body);
    echo "\n\nClient id:$client_id\n\n";
    echo is_array($jwt_body['aud']);
    die_with("Client id missing");
}

// Find key set URL to fetch the JWKS
if (empty($_SESSION['issuers'][$jwt_body['iss']]['clients'][$client_id]['deployments'][$jwt_body['https://purl.imsglobal.org/spec/lti/claim/deployment_id']])) {
    // If there is no key set url, go to registration
    $register_details = [
        'iss' => $jwt_body['iss'],
        'client_id' => $client_id,
    ];
    include('registerform.php');
    die;
}
$key_set_url = $_SESSION['issuers'][$jwt_body['iss']]['clients'][$client_id]['key_set_url'];

// Download key set

$public_key_set = json_decode(get_url_content($key_set_url), true);

// Find key used to sign the JWT (matches the KID in the header)
$public_key;
foreach ($public_key_set['keys'] as $key) {
    if ($key['kid'] == $jwt_head['kid'] && $key['alg'] == $jwt_head['alg']) {
        $public_key = openssl_pkey_get_details(JWK::parseKey($key));
        break;
    }
}

// Make sure we found the correct key
if (empty($public_key)) {
    die_with("Failed to find KID: " . $jwt_head['kid'] . " using alg ". $jwt_head['alg'] ." in keyset from " . $key_set_url);
}

// Validate JWT signature
try {
    JWT::decode($raw_jwt, $public_key['key'], array('RS256'));
} catch(Exception $e) {
    die_with($e->getMessage());
}


// Are we a deep linking request?
// if ($jwt_body['https://purl.imsglobal.org/spec/lti/claim/message_type'] == 'LtiDeepLinkingRequest') {
    //     // Go to deep linking setup form
    //     include('setupform.php');
//     die;
// }

$fe_session_data = [
    'be_session_id' => uniqid('session-', true),
    'state'         => $_REQUEST['state'],
    'message_type'  => $jwt_body['https://purl.imsglobal.org/spec/lti/claim/message_type'],
    'iss'           => $jwt_body['iss'],
    'client_id'     => $client_id,
    'deployment_id' => $jwt_body['https://purl.imsglobal.org/spec/lti/claim/deployment_id'],
    'name'          => $jwt_body['name'],
    'difficulty'    => $jwt_body['https://purl.imsglobal.org/spec/lti/claim/custom']['difficulty'] ?: 'normal',
];
// Store a copy of the launch so we can refer back to it
$_SESSION[$fe_session_data['be_session_id']] = $jwt_body;
$_SESSION[$fe_session_data['be_session_id']]['current_request_url'] = ($_SERVER['HTTP_X-Forwarded-Proto'] ?: $_SERVER['REQUEST_SCHEME']) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];



include('launchredirect.php');

?>