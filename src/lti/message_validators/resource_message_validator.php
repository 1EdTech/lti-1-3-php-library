<?php
namespace IMSGlobal\LTI;

class Resource_Message_Validator implements Message_Validator {
    public function can_validate($jwt_body) {
        return $jwt_body[LTI_Constants::MESSAGE_TYPE] === 'LtiResourceLinkRequest';
    }

    public function validate($jwt_body) {
        if (empty($jwt_body['sub'])) {
            throw new LTI_Exception('Must have a user (sub)');
        }
        if ($jwt_body[LTI_Constants::VERSION] !== LTI_Constants::V1_3) {
            throw new LTI_Exception('Incorrect version, expected 1.3.0');
        }
        if (!isset($jwt_body[LTI_Constants::ROLES])) {
            throw new LTI_Exception('Missing Roles Claim');
        }
        if (empty($jwt_body[LTI_Constants::RESOURCE_LINK]['id'])) {
            throw new LTI_Exception('Missing Resource Link Id');
        }

        return true;
    }
}
