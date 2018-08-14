<?php
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

$_SESSION['issuers'][$_REQUEST['iss']]['clients'][$_REQUEST['client_id']]['key_set_url'] = $_REQUEST['key_set_url'];
$_SESSION['issuers'][$_REQUEST['iss']]['clients'][$_REQUEST['client_id']]['auth_token_url'] = $_REQUEST['auth_token_url'];

//$_SESSION['key_set_urls'][$_REQUEST['iss'].':'.$_REQUEST['client_id']] = $_REQUEST['key_set_url'];
//$_SESSION['auth_token_urls'][$_REQUEST['iss'].':'.$_REQUEST['client_id']] = $_REQUEST['auth_token_url'];
//echo "public key added for " .$_REQUEST['iss'].':'.$_REQUEST['client_id'];

//$_SESSION['deployments'][$_REQUEST['deployment_id']] = $_REQUEST['account'];
echo "\n<br>Deployment " . $_REQUEST['deployment_id'] . ' deployed to account '.$_REQUEST['account'];

?>