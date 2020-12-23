<?php
namespace LTI\MessageValidator;

use LTI\Interfaces\MessageValidator;

class DeepLinkMessageValidator implements MessageValidator
{
    public function canValidate($jwt_body)
    {
        return $jwt_body[LtiConstants::MESSAGE_TYPE] === 'LtiDeepLinkingRequest';
    }

    public function validate($jwt_body)
    {
        if (empty($jwt_body['sub'])) {
            throw new LtiException('Must have a user (sub)');
        }
        if ($jwt_body[LtiConstants::VERSION] !== LtiConstants::V1_3) {
            throw new LtiException('Incorrect version, expected 1.3.0');
        }
        if (!isset($jwt_body[LtiConstants::ROLES])) {
            throw new LtiException('Missing Roles Claim');
        }
        if (empty($jwt_body[LtiConstants::DL_DEEP_LINK_SETTINGS])) {
            throw new LtiException('Missing Deep Linking Settings');
        }
        $deep_link_settings = $jwt_body[LtiConstants::DL_DEEP_LINK_SETTINGS];
        if (empty($deep_link_settings['deep_link_return_url'])) {
            throw new LtiException('Missing Deep Linking Return URL');
        }
        if (empty($deep_link_settings['accept_types']) || !in_array('ltiResourceLink', $deep_link_settings['accept_types'])) {
            throw new LtiException('Must support resource link placement types');
        }
        if (empty($deep_link_settings['accept_presentation_document_targets'])) {
            throw new LtiException('Must support a presentation type');
        }

        return true;
    }
}
