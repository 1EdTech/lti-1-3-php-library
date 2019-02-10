<?php
require_once("../lti/database.php");
session_start();
use \IMSGlobal\LTI\Database;
use \IMSGlobal\LTI\LTI_Registration;
use \IMSGlobal\LTI\LTI_Deployment;
$_SESSION['iss'] = [
    'http://localhost/' => [
        'client_id' => 'testing12345',
        'auth_login_url' => TOOL_HOST . '/example/platform/return.php',
        'key_set_url' => 'http://localhost/example/platform/jwks.json',
        'private_key_file' => 'private.key',
        'deployment' => [
            '1234' => '1234'
        ]
    ],
    'http://imsglobal.org' => [
        'client_id' => 'testing12345',
        'auth_login_url' => 'https://lti-ri.imsglobal.org/platforms/7/authorizations/new',
        'auth_token_url' => 'https://lti-ri.imsglobal.org/platforms/7/access_tokens',
        'key_set_url' => 'https://lti-ri.imsglobal.org/platforms/7/platform_keys/6.json',
        'private_key_file' => 'private.key',
        'deployment' => [
            '1234' => '1234'
        ]
    ],
    'ltiadvantagevalidator.imsglobal.org' => [
        'client_id' => 'imstestuser',
        'auth_login_url' => 'https://ltiadvantagevalidator.imsglobal.org/ltitool/oidcauthurl.html',
        'auth_token_url' => 'https://oauth2server.imsglobal.org/oauth2server/authcodejwt',
        'key_set_url' => 'https://oauth2server.imsglobal.org/jwks',
        'private_key_file' => 'cert_suite_private.key',
        'deployment' => [
            'testdeploy' => 'testdeploy'
        ]
    ],
];
class Example_Database implements Database {
    public function find_registration_by_issuer($iss) {
        if (empty($_SESSION['iss']) || empty($_SESSION['iss'][$iss])) {
            return false;
        }
        return LTI_Registration::new()
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
        return LTI_Deployment::new()
            ->set_deployment_id($deployment_id);
    }

    private function private_key($iss) {
        return file_get_contents($_SESSION['iss'][$iss]['private_key_file']);
    }
}
?>