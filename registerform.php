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
            Client Id: <?= $client_id ?>
            <input type="hidden" name="client_id" value="<?= $client_id ?>" />
        </li>
        <li>
            Platform Key Set URL: <input type="text" name="key_set_url" value="https://lti-ri.imsglobal.org/platforms/7/platform_keys/6.json" />
        </li>
        </li>
        <li>
            Auth Token URL: <input type="text" name="auth_token_url" value="https://lti-ri.imsglobal.org/platforms/7/access_tokens" />
        </li>
        <li>
            Deployment Id: <?= $jwt_body['https://purl.imsglobal.org/spec/lti/claim/deployment_id'] ?>
            <input type="hidden" name="deployment_id" value="<?= $jwt_body['https://purl.imsglobal.org/spec/lti/claim/deployment_id'] ?>" />
        </li>
    </ul>
    <input type="hidden" name="registration" value="true" />
    <input type="submit" value="Go!" />
</form>