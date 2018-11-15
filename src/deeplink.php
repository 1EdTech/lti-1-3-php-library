<?php
include('util.php');
if (empty($_POST)) {
    die;
}

require_once('keys.php');
require_once 'jwt/src/BeforeValidException.php';
require_once 'jwt/src/ExpiredException.php';
require_once 'jwt/src/SignatureInvalidException.php';
require_once 'jwt/src/JWT.php';

use \Firebase\JWT\JWT;

session_start();

$session = $_SESSION[$_COOKIE['be_session_id']];

$message_jwt = [
    "iss" => "https://platform.example.org",
    "aud" => [$session['iss']],
    "exp" => time() + 600,
    "iat" => time(),
    "nonce" => uniqid("testing"),
    "https://purl.imsglobal.org/spec/lti/claim/deployment_id" => $session['https://purl.imsglobal.org/spec/lti/claim/deployment_id'],
    "https://purl.imsglobal.org/spec/lti/claim/message_type" => "LTIDeepLinkingResponse",
    "https://purl.imsglobal.org/spec/lti/claim/version" => "1.3.0",
    "https://purl.imsglobal.org/spec/lti-dl/claim/content_items" => [
        [
            "type" => "ltiResourceLink",
            "title" => "Breakout ".$_POST['difficulty']." mode",
            "url" => $session['current_request_url'],
            "presentation" => [
                "documentTarget" => "iframe",
                "width" => 500,
                "height" => 600
            ],
            "icon" => [
                "url" => "https://lti.example.com/image.jpg",
                "width" => 100,
                "height" => 100
            ],
            "thumbnail" => [
                "url" => "https://lti.example.com/thumb.jpg",
                "width" => 90,
                "height" => 90
            ],
            "lineItem" => [
                "scoreMaximum" => 108,
                "label" => "Score"
            ],
            "custom" => [
                "difficulty" => $_POST['difficulty']
            ]
        ]
    ],
    "https://purl.imsglobal.org/spec/lti-dl/claim/data" => $session['https://purl.imsglobal.org/spec/lti-dl/claim/deep_linking_settings']['data']
];
$registration = db()->get_registration($session['iss'], is_array($session['aud']) ? $session['aud'][0] : $session['aud'] );
$jwt = JWT::encode($message_jwt, $registration['key']['private'], 'RS256');

?>

<form id="autosubmit" action="<?= $session['https://purl.imsglobal.org/spec/lti-dl/claim/deep_linking_settings']['deep_link_return_url']; ?>" method="POST">
    <input type="hidden" name="JWT" value="<?= $jwt ?>" />
</form>
<script>
    console.log(<?= json_encode($_SESSION, true) ?>);
    document.getElementById('autosubmit').submit();
</script>
