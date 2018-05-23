<?php
require_once 'jwt/src/BeforeValidException.php';
require_once 'jwt/src/ExpiredException.php';
require_once 'jwt/src/SignatureInvalidException.php';
require_once 'jwt/src/JWT.php';
require_once 'jwt/src/JWK.php';

use \Firebase\JWT\JWK;

session_start();

if ($_POST['registration']) {

    $_SESSION['public_keys'][$_POST['iss'].':'.$_POST['aud']] = $_POST['pub_key'];
    $_SESSION['auth_token_urls'][$_POST['iss'].':'.$_POST['aud']] = $_POST['auth_token_url'];
    echo "public key added for " .$_POST['iss'].':'.$_POST['aud'];

    $_SESSION['deployments'][$_POST['deployment_id']] = $_POST['account'];
    echo "\n<br>Deployment " . $_POST['deployment_id'] . ' deployed to account '.$_POST['account'];
    die;
}

?>
<form action="register.php" method="POST">
    <ul>
        <li>
            Martin's Super Tool Account: <input type="text" name="account" value="" />
        </li>
        <li>
            Issuer: <?= $jwt_body['iss'] ?>
            <input type="hidden" name="iss" value="<?= $jwt_body['iss'] ?>" />
        </li>
        <li>
            Client Id: <?= $aud ?>
            <input type="hidden" name="aud" value="<?= $aud ?>" />
        </li>
        <li>
            Platform Public Key URL: <input type="text" name="pub_key" value="http://lti-ri.imsglobal.org/platforms/7/platform_keys/6.json" />
        </li>
        </li>
        <li>
            Auth Token URL: <input type="text" name="auth_token_url" value="http://lti-ri.imsglobal.org/platforms/7/access_tokens" />
        </li>
        <li>
            Deployment Id: <?= $jwt_body['http://imsglobal.org/lti/deployment_id'] ?>
            <input type="hidden" name="deployment_id" value="<?= $jwt_body['http://imsglobal.org/lti/deployment_id'] ?>" />
        </li>
    </ul>
    <input type="hidden" name="registration" value="true" />
    <input type="submit" value="Go!" />
</form>
<?php
?>