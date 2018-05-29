<?php
require_once 'jwt/src/BeforeValidException.php';
require_once 'jwt/src/ExpiredException.php';
require_once 'jwt/src/SignatureInvalidException.php';
require_once 'jwt/src/JWT.php';
require_once 'jwt/src/JWK.php';

use \Firebase\JWT\JWK;

session_start();

if ($_POST['registration']) {

    $_SESSION['key_set_urls'][$_POST['iss'].':'.$_POST['client_id']] = $_POST['key_set_url'];
    $_SESSION['auth_token_urls'][$_POST['iss'].':'.$_POST['client_id']] = $_POST['auth_token_url'];
    echo "public key added for " .$_POST['iss'].':'.$_POST['client_id'];

    $_SESSION['deployments'][$_POST['deployment_id']] = $_POST['account'];
    echo "\n<br>Deployment " . $_POST['deployment_id'] . ' deployed to account '.$_POST['account'];
    die;
}

?>