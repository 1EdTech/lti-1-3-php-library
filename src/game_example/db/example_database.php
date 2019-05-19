<?php
require_once(__DIR__ . "/../../lti/lti.php");
session_start();
use \IMSGlobal\LTI;
$_SESSION['iss'] = [
    'http://imsglobal.org' => [
        'client_id' => 'testing12345',
        'auth_login_url' => 'https://lti-ri.imsglobal.org/platforms/7/authorizations/new',
        'auth_token_url' => 'https://lti-ri.imsglobal.org/platforms/7/access_tokens',
        'key_set_url' => 'https://lti-ri.imsglobal.org/platforms/7/platform_keys/6.json',
        'private_key_file' => '/private.key',
        'deployment' => [
            '1234' => '1234'
        ]
    ],
    'ltiadvantagevalidator.imsglobal.org' => [
        'client_id' => 'imstestuser',
        'auth_login_url' => 'https://ltiadvantagevalidator.imsglobal.org/ltitool/oidcauthurl.html',
        'auth_token_url' => 'https://oauth2server.imsglobal.org/oauth2server/authcodejwt',
        'key_set_url' => 'https://oauth2server.imsglobal.org/jwks',
        'private_key_file' => '/cert_suite_private.key',
        'deployment' => [
            'testdeploy' => 'testdeploy'
        ]
    ],
    'https://blackboard.com' => [
        'client_id' => 'b3386442-52f9-42b2-81d2-d572900b8cf8',
        'auth_login_url' => 'https://developer.blackboard.com/api/v1/gateway/oauth2/jwttoken',
        'auth_token_url' => 'https://developer.blackboard.com/api/v1/gateway/oauth2/jwttoken',
        'key_set_url' => 'https://developer.blackboard.com/api/v1/management/applications/b3386442-52f9-42b2-81d2-d572900b8cf8/jwks.json',
        'private_key_file' => '/keys/bb_private.key',
        'deployment' => [
            '0eeac994-771c-4609-8d23-708db0c6dbc3' => '0eeac994-771c-4609-8d23-708db0c6dbc3',
            '757eec36-fb36-4f64-8167-594663e2b88d' => '757eec36-fb36-4f64-8167-594663e2b88d'
        ]
    ],
    'https://turnitin.dev/' => [
        'client_id' => 'dev_local_test_client',
        'auth_login_url' => 'http://localhost:9001/oidc/auth/index.php',
        'auth_token_url' => 'http://host.docker.internal:9001/service/auth/index.php',
        'key_set_url' => 'http://host.docker.internal:9001/jwks.json',
        'private_key_file' => '/keys/tii_dev.key',
        'deployment' => [
            'dev_deployment_ml' => 'dev_deployment_ml',
        ]
    ],
];
class Example_Database implements LTI\Database {
    public function find_registration_by_issuer($iss) {
        if (empty($_SESSION['iss']) || empty($_SESSION['iss'][$iss])) {
            return false;
        }
        return LTI\LTI_Registration::new()
            ->set_auth_login_url($_SESSION['iss'][$iss]['auth_login_url'])
            ->set_auth_token_url($_SESSION['iss'][$iss]['auth_token_url'])
            ->set_client_id($_SESSION['iss'][$iss]['client_id'])
            ->set_key_set_url($_SESSION['iss'][$iss]['key_set_url'])
            ->set_issuer($iss)
            ->set_tool_private_key($this->private_key($iss));
    }

    public function find_deployment($iss, $deployment_id) {
        if (empty($_SESSION['iss'][$iss]['deployment'][$deployment_id])) {
            return false;
        }
        return LTI\LTI_Deployment::new()
            ->set_deployment_id($deployment_id);
    }

    private function private_key($iss) {
        return file_get_contents(__DIR__ . $_SESSION['iss'][$iss]['private_key_file']);
    }
}
?>