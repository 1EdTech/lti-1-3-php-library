<?php
require_once('keys.php');
require_once 'jwt/src/BeforeValidException.php';
require_once 'jwt/src/ExpiredException.php';
require_once 'jwt/src/SignatureInvalidException.php';
require_once 'jwt/src/JWT.php';
require_once 'jwt/src/JWK.php';

use \Firebase\JWT\JWT;

session_start();

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
$jwt = JWT::encode($jwt_claim, $privateKey, 'RS256');

// Build auth token request headers
$auth_request = [
    'grant_type' => 'client_credentials',
    'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
    'client_assertion' => $jwt,
    'scope' => "http://imsglobal.org/ags/lineitem http://imsglobal.org/ags/result/read"
];

// Make request to get auth token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$auth_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($auth_request));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$token_data = json_decode(curl_exec($ch), true);
curl_close ($ch);

// Build grade book request
$grade_call = [
    "timestamp" => "2017-04-16T18:54:36.736+00:00",
    "scoreGiven" => $_REQUEST['grade'],
    "scoreMaximum" => 108,
    "comment" => "This is exceptional work",
    "activityProgress" => "Completed",
    "gradingProgress" => "Completed",
    "timestamp" => "2017-02-07T12:34:56+00:00",
    "userId" => "28fcdf957d8a7ebd2ab1"
];

// Call grade book line item endpoint to send back a grade
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $_SESSION['current_request']['https://www.imsglobal.org/lti/ags']['lineitem']. '/scores');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($grade_call));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer '. $token_data['access_token'],
    'Content-Type: application/vnd.ims.lis.v1.score+json'
]);
$line_item = curl_exec($ch);
curl_close ($ch);

echo $token_data['access_token'];
echo $line_item;

?>