<?php
session_start();

if ($_POST['registration']) {
    $public_key = file_get_contents($_POST['pub_key']);

    $_SESSION['public_keys'][$_POST['iss'].':'.$_POST['aud']] = $public_key;
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
            Client Id: <?= $jwt_body['aud'][0] ?>
            <input type="hidden" name="aud" value="<?= $jwt_body['aud'][0] ?>" />
        </li>
        <li>
            Platform Public Key URL: <input type="text" name="pub_key" value="http://localhost/lti13/pubkey.php" />
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