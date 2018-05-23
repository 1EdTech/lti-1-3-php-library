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
    "iss": "https:\/\/platform.example.edu",
    "sub": "a6d5c443-1f51-4783-ba1a-7686ffe3b54a",
    "aud": [
        "962fa4d8-bcbf-49a0-94b2-2de05ad274af"
    ],
    "exp": 1527823048544,
    "iat": 1510185228,
    "azp": "962fa4d8-bcbf-49a0-94b2-2de05ad274af",
    "nonce": "8f424063-1b82-4344-ae1c-8fe1090be62b",
    "name": "Ms Jane Marie Doe",
    "given_name": "Jane",
    "family_name": "Doe",
    "middle_name": "Marie",
    "picture": "https:\/\/platform.example.edu\/jane.jpg",
    "email": "jane@platform.example.edu",
    "locale": "en-US",
    "http:\/\/imsglobal.org\/lti\/deployment_id": "07940580-b309-415e-a37c-914d387c1150",
    "http:\/\/imsglobal.org\/lti\/message_type": "LtiResourceLinkRequest",
    "http:\/\/imsglobal.org\/lti\/version": "3.0.0",
    "http:\/\/imsglobal.org\/lti\/roles": [
        "http:\/\/purl.imsglobal.org\/vocab\/lis\/v2\/institution\/person#Student",
        "http:\/\/purl.imsglobal.org\/vocab\/lis\/v2\/membership#Learner",
        "http:\/\/purl.imsglobal.org\/vocab\/lis\/v2\/membership#Mentor"
    ],
    "http:\/\/imsglobal.org\/lti\/role_scope_mentor": [
        "http:\/\/purl.imsglobal.org\/vocab\/lis\/v2\/institution\/person#Administrator"
    ],
    "http:\/\/imsglobal.org\/lti\/tokenendpoint": "https:\/\/platform.example.edu\/tokenrequest",
    "http:\/\/imsglobal.org\/lti\/context": {
        "id": "c1d887f0-a1a3-4bca-ae25-c375edcc131a",
        "label": "ECON 1010",
        "title": "Economics as a Social Science",
        "type": [
            "http:\/\/purl.imsglobal.org\/vocab\/lis\/v2\/course#CourseOffering"
        ]
    },
    "http:\/\/imsglobal.org\/lti\/resource_link": {
        "id": "200d101f-2c14-434a-a0f3-57c2a42369fd",
        "description": "Assignment to introduce who you are",
        "title": "Introduction Assignment"
    },
    "http:\/\/imsglobal.org\/lti\/tool_platform": {
        "guid": "https:\/\/platform.example.edu",
        "contact_email": "support@platform.example.edu",
        "description": "An Example Tool Platform",
        "name": "Example Tool Platform",
        "url": "https:\/\/platform.example.edu",
        "product_family_code": "ExamplePlatformVendor-Product",
        "version": "1.0"
    },
    "http:\/\/imsglobal.org\/lti\/launch_presentation": {
        "document_target": "iframe",
        "height": 320,
        "width": 240,
        "return_url": "https:\/\/platform.example.edu\/terms\/201601\/courses\/7\/sections\/1\/resources\/2"
    },
    "http:\/\/imsglobal.org\/lti\/custom": {
        "xstart": "2017-04-21T01:00:00Z"
    },
    "http:\/\/imsglobal.org\/lti\/lis": {
        "person_sourcedid": "example.edu:71ee7e42-f6d2-414a-80db-b69ac2defd4",
        "course_offering_sourcedid": "example.edu:SI182-F16",
        "course_section_sourcedid": "example.edu:SI182-001-F16"
    },
    "http:\/\/www.ExamplePlatformVendor.com\/session": {
        "id": "89023sj890dju080"
    }
}
EOD;

$jwt = JWT::encode(json_decode($jwt, true), $privateKey, 'RS256');

?>

<form action="launch.php" method="POST">
    <input type="hidden" name="id_token" value="<?= $jwt ?>" />
    <input type="submit" value="Go!" />
</form>