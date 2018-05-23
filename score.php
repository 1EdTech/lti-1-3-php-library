<?php
require_once('keys.php');
require_once 'jwt/src/BeforeValidException.php';
require_once 'jwt/src/ExpiredException.php';
require_once 'jwt/src/SignatureInvalidException.php';
require_once 'jwt/src/JWT.php';
require_once 'jwt/src/JWK.php';

use \Firebase\JWT\JWT;

$jwt_claim = [
        "iss" => "http://martinscooltools.example.com",
        "sub" => $_REQUEST['client_id'],
        "aud" => $_REQUEST['auth_url'],
        "iat" => time(),
        "exp" => time()+60,
        "jti" => uniqid("testing")
];

$jwt = JWT::encode(json_decode($jwt_claim, true), $privateKey, 'RS256');

$auth_request = [
    'grant_type' => 'client_credentials',
    'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
    'client_assertion' => $jwt,
    'scope' => "http://imsglobal.org/ags/lineitem http://imsglobal.org/ags/result/read"
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,$_REQUEST['auth_url']);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($auth_request));

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$token_data = json_decode(curl_exec($ch), true);

curl_close ($ch);

// Call grade book

$ch = curl_init();

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

curl_setopt($ch, CURLOPT_URL, "http://lti-ri.imsglobal.org/platforms/7/line_items/9/scores");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($grade_call));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer '. $token_data['access_token'],
    'Content-Type: application/vnd.ims.lis.v1.score+json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$line_item = curl_exec($ch);

curl_close ($ch);

echo $token_data['access_token'];

echo $line_item;

?>