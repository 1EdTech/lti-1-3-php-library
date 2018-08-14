<?php
require_once('keys.php');
require_once 'jwt/src/BeforeValidException.php';
require_once 'jwt/src/ExpiredException.php';
require_once 'jwt/src/SignatureInvalidException.php';
require_once 'jwt/src/JWT.php';
require_once 'jwt/src/JWK.php';

use \Firebase\JWT\JWT;

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
    "iss": "http:\/\/imsglobal.org",
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
$jwt = JWT::encode($jwt_body, $privateKey, 'RS256');


$dl_jwt = <<<EOD
{
    "https:\/\/purl.imsglobal.org\/spec\/lti\/claim\/message_type": "LtiDeepLinkingRequest",
    "given_name": "Novella",
    "family_name": "Krajcik",
    "middle_name": "Jayde",
    "picture": "http:\/\/example.org\/Novella.jpg",
    "email": "Novella.Krajcik@example.org",
    "name": "Novella Jayde Krajcik",
    "https:\/\/purl.imsglobal.org\/spec\/lti\/claim\/roles": [
        "http:\/\/purl.imsglobal.org\/vocab\/lis\/v2\/institution\/person#Instructor"
    ],
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
    "iss": "http:\/\/imsglobal.org",
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
    "https:\/\/purl.imsglobal.org\/spec\/lti\/claim\/deployment_id": "1234",
    "https:\/\/purl.imsglobal.org\/spec\/lti-dl\/claim\/deep_linking_settings": {
        "deep_link_return_url": "https:\/\/platform.example\/deep_links",
        "accept_types": ["link", "file", "html", "ltiLink", "image"],
        "accept_media_types": "image\/*,text\/html",
        "accept_presentation_document_targets": ["iframe", "window", "embed"],
        "accept_multiple": true,
        "auto_create": true,
        "title": "This is the default title",
        "text": "This is the default text",
        "data": "csrftoken:c7fbba78-7b75-46e3-9201-11e6d5f36f53"
      }
}
EOD;

$dl_jwt_body = json_decode($dl_jwt, true);
$dl_jwt_body['iat'] = time();
$dl_jwt_body['exp'] = time()+60;
$dl_jwt = JWT::encode($dl_jwt_body, $privateKey, 'RS256');

?>

<form action="launch.php" method="POST">
    <input type="hidden" name="id_token" value="<?= $jwt ?>" />
    <input type="submit" value="Go!" />
</form>
<form action="launch.php" method="POST">
    <input type="hidden" name="id_token" value="<?= $dl_jwt ?>" />
    <input type="submit" value="Go Deep!" />
</form>