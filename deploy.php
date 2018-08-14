<?php
require_once 'jwt/src/BeforeValidException.php';
require_once 'jwt/src/ExpiredException.php';
require_once 'jwt/src/SignatureInvalidException.php';
require_once 'jwt/src/JWT.php';
require_once 'jwt/src/JWK.php';

use \Firebase\JWT\JWK;

session_start();

if (!$_REQUEST['deployment']) {
    die;
}

$_SESSION['issuers'][$_REQUEST['iss']]['clients'][$_REQUEST['client_id']]['deployments'][$_REQUEST['deployment_id']] = $_REQUEST['account'];
echo '{"success":true}';

?>