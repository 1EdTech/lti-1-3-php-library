<?php
namespace IMSGlobal\LTI;

class Resource_Message_Validator implements Message_Validator {
    public function can_validate($jwt_body) {
        return $jwt_body['https://purl.imsglobal.org/spec/lti/claim/message_type'] === 'LtiResourceLinkRequest';
    }

    public function validate($jwt_body) {
        return true;
    }
}
?>