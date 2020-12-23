<?php
namespace LTI\MessageValidator;

use LTI\Interfaces\MessageValidator;

class SubmissionReviewMessageValidator implements MessageValidator
{
    public function canValidate($jwt_body)
    {
        return $jwt_body['https://purl.imsglobal.org/spec/lti/claim/message_type'] === 'LtiSubmissionReviewRequest';
    }

    public function validate($jwt_body)
    {
        if (empty($jwt_body['sub'])) {
            throw new LtiException('Must have a user (sub)');
        }
        if ($jwt_body['https://purl.imsglobal.org/spec/lti/claim/version'] !== '1.3.0') {
            throw new LtiException('Incorrect version, expected 1.3.0');
        }
        if (!isset($jwt_body['https://purl.imsglobal.org/spec/lti/claim/roles'])) {
            throw new LtiException('Missing Roles Claim');
        }
        if (empty($jwt_body['https://purl.imsglobal.org/spec/lti/claim/resource_link']['id'])) {
            throw new LtiException('Missing Resource Link Id');
        }
        if (empty($jwt_body['https://purl.imsglobal.org/spec/lti/claim/for_user'])) {
            throw new LtiException('Missing For User');
        }

        return true;
    }
}
