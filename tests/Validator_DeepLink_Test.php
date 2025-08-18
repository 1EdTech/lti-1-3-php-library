<?PHP
namespace IMSGlobal\LTI;

class Validator_DeepLink_Test extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $this->Validator = new Deep_Link_Message_Validator();

        $Message = [
            'https://purl.imsglobal.org/spec/lti/claim/message_type' =>
                "X-INVALID-X",
        ];
        $this->assertFalse(
            $this->Validator->can_validate($Message)
        );

        $Message = [
            'https://purl.imsglobal.org/spec/lti/claim/message_type' =>
                "LtiDeepLinkingRequest",
        ];
        $this->assertTrue(
            $this->Validator->can_validate($Message)
        );

        $this->validateMessage($Message, "Must have a user (sub)");

        $Message += [
            'sub' =>
                "X-USER-X",
            'https://purl.imsglobal.org/spec/lti/claim/version' =>
                "X-INVALID-X",
        ];

        $this->validateMessage($Message, "Incorrect version, expected 1.3.0");

        $Message['https://purl.imsglobal.org/spec/lti/claim/version'] = "1.3.0";

        $this->validateMessage($Message, "Missing Roles Claim");

        $Message['https://purl.imsglobal.org/spec/lti/claim/roles'] =
            "X-ROLES-X";

        $this->validateMessage($Message, "Missing Deep Linking Settings");

        $Message['https://purl.imsglobal.org/spec/lti-dl/claim/deep_linking_settings'] = [
            'test'
        ];

        $this->validateMessage($Message, "Missing Deep Linking Return URL");

        $Message['https://purl.imsglobal.org/spec/lti-dl/claim/deep_linking_settings'] = [
            'deep_link_return_url' => 'https://issuer.example/dl_return_url'
        ];

        $this->validateMessage($Message, "Must support resource link placement types");
        $Message['https://purl.imsglobal.org/spec/lti-dl/claim/deep_linking_settings'] += [
            'accept_types' => ['ltiResourceLink'],
        ];
        $this->validateMessage($Message, "Must support a presentation type");

        $Message['https://purl.imsglobal.org/spec/lti-dl/claim/deep_linking_settings'] += [
            'accept_presentation_document_targets' => "X-DOC-TARGETS-X",
        ];

        $this->assertTrue(
            $this->Validator->validate($Message)
        );

    }

    private function validateMessage(array $Message, string $ExceptionMessage = null)
    {
        try {
            $this->Validator->validate($Message);
            if ($ExceptionMessage !== null) {
                $this->fail("Exception not thrown when one was expected.");
            }
        } catch (LTI_Exception $Ex) {
            if ($ExceptionMessage === null) {
                $this->fail(
                    "Exception thrown when none was expected."
                );
                return;
            }

            $this->assertEquals($ExceptionMessage, $Ex->getMessage());
        }
    }

    private Deep_Link_Message_Validator $Validator;
}
