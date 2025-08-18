<?PHP
namespace IMSGlobal\LTI;

class Validator_SubmissionReview_Test extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $this->Validator = new Submission_Review_Message_Validator();

        $Message = [
            'https://purl.imsglobal.org/spec/lti/claim/message_type' =>
                "X-INVALID-X",
        ];
        $this->assertFalse(
            $this->Validator->can_validate($Message)
        );

        $Message = [
            'https://purl.imsglobal.org/spec/lti/claim/message_type' =>
                "LtiSubmissionReviewRequest",
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

        $this->validateMessage($Message, "Missing Resource Link Id");

        $Message['https://purl.imsglobal.org/spec/lti/claim/resource_link'] = [
            'id' => "X-LINK-ID-X",
        ];

        $this->validateMessage($Message, "Missing For User");

        $Message['https://purl.imsglobal.org/spec/lti/claim/for_user'] =
            "X-USER-X";

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

    private Submission_Review_Message_Validator $Validator;
}
