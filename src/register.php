<?php
require_once 'keys.php';
require_once 'jwt/src/BeforeValidException.php';
require_once 'jwt/src/ExpiredException.php';
require_once 'jwt/src/SignatureInvalidException.php';
require_once 'jwt/src/JWT.php';
require_once 'jwt/src/JWK.php';

use \Firebase\JWT\JWK;

session_start();

if (!$_REQUEST['registration']) {
    die;
}

$_SESSION['issuers'][$_REQUEST['iss']]['key_set_url'] = $_REQUEST['key_set_url'];
$_SESSION['issuers'][$_REQUEST['iss']]['auth_token_url'] = $_REQUEST['auth_token_url'];
$_SESSION['issuers'][$_REQUEST['iss']]['initialization_login_url'] = $_REQUEST['initialization_login_url'];

$key = [
    'private' => empty($_REQUEST['private_key']) ? $GLOBALS['privateKey'] : $_REQUEST['private_key']
];
$_SESSION['issuers'][$_REQUEST['iss']]['key'] = $key;
var_dump($_SESSION);

echo "\n<br>Deployment " . $_REQUEST['deployment_id'] . ' deployed to account '.$_REQUEST['account'];

?>