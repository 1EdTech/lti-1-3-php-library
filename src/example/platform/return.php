<?php
require_once('../keys.php');
require_once 'jwt/src/BeforeValidException.php';
require_once 'jwt/src/ExpiredException.php';
require_once 'jwt/src/SignatureInvalidException.php';
require_once 'jwt/src/JWT.php';
require_once 'jwt/src/JWK.php';

use \Firebase\JWT\JWT;
use \IMSGlobal\LTI;

$jwt = <<<EOD
{
    "https:\/\/purl.imsglobal.org\/spec\/lti\/claim\/message_type": "LtiResourceLinkRequest",
    "given_name": "Novella",
    "family_name": "Krajcik",
    "middle_name": "Jayde",
    "picture": "http:\/\/example.org\/Novella.jpg",
    "email": "Novella.Krajcik@example.org",
    "name": "Novella Jayde Krajcik",
    "https:\/\/purl.imsglobal.org\/spec\/lti\/claim\/roles": [
        "http:\/\/purl.imsglobal.org\/vocab\/lis\/v2\/institution\/person#Instructor"
    ],
    "https:\/\/purl.imsglobal.org\/spec\/lti\/claim\/role_scope_mentor": [
        "http:\/\/purl.imsglobal.org\/vocab\/lis\/v2\/institution\/person#Administrator"
    ],
    "https:\/\/purl.imsglobal.org\/spec\/lti\/claim\/resource_link": {
        "id": "5",
        "title": "poiuytrewq",
        "description": ""
    },
    "https:\/\/purl.imsglobal.org\/spec\/lti\/claim\/context": {
        "id": "6",
        "label": "12345",
        "title": "qwertyuio",
        "type": [
            "0987654321"
        ]
    },
    "https:\/\/purl.imsglobal.org\/spec\/lti\/claim\/tool_platform": {
        "name": "LILI Hackathon game thing",
        "contact_email": "",
        "description": "",
        "url": "",
        "product_family_code": "",
        "version": "1.0"
    },
    "https:\/\/purl.imsglobal.org\/spec\/lti-ags\/claim\/endpoint": {
        "scope": [
            "https:\/\/purl.imsglobal.org\/spec\/lti-ags\/scope\/lineitem",
            "https:\/\/purl.imsglobal.org\/spec\/lti-ags\/scope\/result.readonly",
            "https:\/\/purl.imsglobal.org\/spec\/lti-ags\/scope\/score"
        ],
        "lineitems": "https:\/\/lti-ri.imsglobal.org\/platforms\/7\/contexts\/6\/line_items",
        "lineitem": "https:\/\/lti-ri.imsglobal.org\/platforms\/7\/contexts\/6\/line_items\/9"
    },
    "https:\/\/purl.imsglobal.org\/spec\/lti-nrps\/claim\/namesroleservice": {
        "context_memberships_url": "https:\/\/lti-ri.imsglobal.org\/platforms\/7\/contexts\/6\/memberships.json",
        "service_version": "2.0"
    },
    "iss": "http:\/\/localhost\/",
    "aud": "testing12345",
    "iat": 1533744151,
    "exp": 1533744451,
    "sub": "42b5c037e5155b7bef54",
    "nonce": "0a84ebbe58ad32a8c69f",
    "https:\/\/purl.imsglobal.org\/spec\/lti\/claim\/version": "1.3.0",
    "locale": "en-US",
    "https:\/\/purl.imsglobal.org\/spec\/lti\/claim\/launch_presentation": {
        "document_target": "iframe",
        "height": 320,
        "width": 240,
        "return_url": "https:\/\/lti-ri.imsglobal.org\/platforms\/7\/returns"
    },
    "https:\/\/www.example.com\/extension": {
        "color": "violet"
    },
    "https:\/\/purl.imsglobal.org\/spec\/lti\/claim\/custom": {
        "myCustomValue": 123
    },
    "https:\/\/purl.imsglobal.org\/spec\/lti\/claim\/deployment_id": "1234"
}
EOD;

$jwt_body = json_decode($jwt, true);
$jwt_body['iat'] = time();
$jwt_body['exp'] = time()+60;
$jwt = JWT::encode($jwt_body, $privateKey, 'RS256', 'C_nvQUsUmo04et4OJpDyv3ztbGNyGGkMCgCsbD1Y2cM');

?>

<form id="launch" action="launch.php" method="POST">
    <input type="hidden" name="id_token" value="<?= $jwt ?>" />
    <input type="hidden" name="state" value="<?= $_REQUEST['state'] ?>" />
</form>
<script>
    document.getElementById('launch').submit()
</script>