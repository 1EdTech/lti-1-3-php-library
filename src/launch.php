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
    header('Content-Type: text/plain');
    var_dump($jwt_body);
    echo "\n\nClient id:$client_id\n\n";
    echo is_array($jwt_body['aud']);
    die_with("Client id missing");
}

// Find key set URL to fetch the JWKS
if (empty($_SESSION['issuers'][$jwt_body['iss']]['clients'][$client_id]['deployments'][$jwt_body['https://purl.imsglobal.org/spec/lti/claim/deployment_id']])) {
    // If there is no key set url, go to registration
    include('registerform.php');
    die;
}
$key_set_url = $_SESSION['issuers'][$jwt_body['iss']]['clients'][$client_id]['key_set_url'];

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
} catch(Exception $e) {
    die_with($e->getMessage());
}

// Store a copy of the launch so we can refer back to it
$_SESSION['current_request_url'] = ($_SERVER['HTTP_X-Forwarded-Proto'] ?: $_SERVER['REQUEST_SCHEME']) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
$_SESSION['current_request'] = $jwt_body;

// Are we a deep linking request?
if ($jwt_body['https://purl.imsglobal.org/spec/lti/claim/message_type'] == 'LtiDeepLinkingRequest') {
    // Go to deep linking setup form
    include('setupform.php');
    die;
}

// Success! Everything is signed correctly, now load the game
?>
<div style="position:absolute;width:1000px;margin-left:-500px;left:50%; display:block">
    <div id="scoreboard" style="position:absolute; right:0; width:200px">
        <h2 style="margin-left:12px;">Scoreboard</h2>
        <table id="leadertable" style="margin-left:12px;">
        </table>
    </div>
    <canvas id="breakoutbg" width="800" height="500" style="position:absolute;left:0;border:1px solid #000000;">
    </canvas>
    <canvas id="breakout" width="800" height="500" style="position:absolute;left:0;">
    </canvas>
</div>
<link href="https://fonts.googleapis.com/css?family=Gugi" rel="stylesheet">
<style>
    body {
        font-family: 'Gugi', cursive;
    }
    #scoreboard {
        border: solid 1px #000;
        border-left: none;
        border-top-right-radius: 20px;
        border-bottom-right-radius: 20px;
        padding-bottom: 12px;
    }
</style>
<script>
    // Set game difficulty if it has been set in deep linking
    var curr_diff = '<?= $jwt_body['https://purl.imsglobal.org/spec/lti/claim/custom']['difficulty'] ?: 'normal'; ?>';
</script>
<script type="text/javascript" src="js/breakout.js" charset="utf-8"></script>