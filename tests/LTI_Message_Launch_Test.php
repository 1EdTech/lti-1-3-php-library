<?PHP
namespace IMSGlobal\LTI;

use \Firebase\JWT\JWT;

require_once(__DIR__."/TestDatabase.php");
require_once(__DIR__."/TestCache.php");
require_once(__DIR__."/TestCookie.php");

class TestMessage extends LTI_Message_Launch {
    public function getCookie(): Cookie {
        return $this->cookie;
    }

    public static function setup() {
        # create key for tests if we do not yet have one
        if (!file_exists(__DIR__."/lms.key")) {
            exec("openssl genrsa -out ".__DIR__."/lms.key 2>/dev/null");
        }

        self::$testPrivateKey = openssl_pkey_get_private(
            "file://".__DIR__."/lms.key"
        );

        self::$testKid = uniqid("kid_", true);
    }

    public static function encodeJWT(array $payload) {
        return JWT::encode(
            $payload,
            self::$testPrivateKey,
            "RS256",
            self::$testKid
        );
    }

    protected function get_public_key(): array {
        static $KeyDetails = null;
        if (is_null($KeyDetails)) {
            $KeyDetails = openssl_pkey_get_details(
                self::$testPrivateKey
            );
        }

        return $KeyDetails;
    }

    private static $testPrivateKey = null;
    private static $testKid = null;
}

class LTI_Message_Launch_Test extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        TestMessage::setup();

        $Cache = new TestCache();

        $this->Launch = TestMessage::new(
            new TestDatabase(),
            $Cache,
            new TestCookie()
        );

        $State = $this->Launch->get_launch_id();

        $this->assertIsString($State);

        $this->Launch->getCookie()->set_cookie(
            'lti1p3_'.$State,
            $State,
            60
        );

        $this->validateLaunch(
            [],
            "State not provided in request"
        );

        $this->validateLaunch(
            [
                "state" => "X-INVALID-X"
            ],
            "State not found: X-INVALID-X"
        );


        $this->validateLaunch(
            [
                "state" => $State
            ],
            "Missing id_token"
        );

        $this->validateLaunch(
            [
                "state" => $State,
                "id_token" => "X-INVALID-X",
            ],
            "Invalid id_token, JWT must contain 3 parts"
        );

        $this->validateLaunch(
            [
                "state" => $State,
                "id_token" => TestMessage::encodeJWT([]),
            ],
            "No nonce provided"
        );

        $this->validateLaunch(
            [
                "state" => $State,
                "id_token" => TestMessage::encodeJWT([
                    "nonce" => "X-NONCE-X",
                ]),
            ],
            "No iss provided"
        );

        $this->validateLaunch(
            [
                "state" => $State,
                "id_token" => TestMessage::encodeJWT([
                    "nonce" => "X-NONCE-X",
                    "iss" => "X-ISSUER-X",
                ]),
            ],
            "No aud provided"
        );

        $this->validateLaunch(
            [
                "state" => $State,
                "id_token" => TestMessage::encodeJWT([
                    "nonce" => "X-NONCE-X",
                    "iss" => "X-ISSUER-X",
                    "aud" => "X-CLIENT-ID-X",
                ]),
            ],
            "Registration not found",

        );

        $this->validateLaunch(
            [
                "state" => $State,
                "id_token" => TestMessage::encodeJWT([
                    "nonce" => "X-NONCE-X",
                    "iss" => "https://issuer.example",
                    "aud" => "X-INVALID-CLIENT-ID-X",
                ]),
            ],
            "Client id not registered for this issuer"
        );

        $this->validateLaunch(
            [
                "state" => $State,
                "id_token" => TestMessage::encodeJWT([
                    "nonce" => "X-NONCE-X",
                    "iss" => "https://issuer.example",
                    "aud" => "X-CLIENT-ID-X",
                ])."xx",
            ],
            "Invalid signature on id_token",
        );

        $this->validateLaunch(
            [
                "state" => $State,
                "id_token" => TestMessage::encodeJWT([
                    "nonce" => "X-NONCE-X",
                    "iss" => "https://issuer.example",
                    "aud" => "X-CLIENT-ID-X",
                ]),
            ],
            "No deployment provided",
        );

        $this->validateLaunch(
            [
                "state" => $State,
                "id_token" => TestMessage::encodeJWT([
                    "nonce" => "X-NONCE-X",
                    "iss" => "https://issuer.example",
                    "aud" => "X-CLIENT-ID-X",
                    "https://purl.imsglobal.org/spec/lti/claim/deployment_id" =>
                        "X-INVALID-DEPLOYMENT-ID-X",
                ]),
            ],
            "Unable to find deployment"
        );

        $this->validateLaunch(
            [
                "state" => $State,
                "id_token" => TestMessage::encodeJWT([
                    "nonce" => "X-NONCE-X",
                    "iss" => "https://issuer.example",
                    "aud" => "X-CLIENT-ID-X",
                    "https://purl.imsglobal.org/spec/lti/claim/deployment_id" =>
                        "X-DEPLOYMENT-ID-X",
                ]),
            ],
            "Invalid message type"
        );

        # construct a valid LtiResourceLinkRequest
        $Nonce = uniqid("nonce-", true);
        $Message = [
            "state" => $State,
            "id_token" => TestMessage::encodeJWT([
                "nonce" => $Nonce,
                "iss" => "https://issuer.example",
                "aud" => "X-CLIENT-ID-X",
                "https://purl.imsglobal.org/spec/lti/claim/deployment_id" =>
                    "X-DEPLOYMENT-ID-X",
                "https://purl.imsglobal.org/spec/lti/claim/message_type" =>
                    "LtiResourceLinkRequest",
                "sub" =>
                    "X-USER-X",
                "https://purl.imsglobal.org/spec/lti/claim/version" =>
                    "1.3.0",
                "https://purl.imsglobal.org/spec/lti/claim/roles" =>
                    "X-ROLES-X",
                "https://purl.imsglobal.org/spec/lti/claim/resource_link" => [
                    "id" => "X-RESOURCE-ID-X",
                ]
            ]),
        ];

        # should fail if the nonce is unknown
        $this->validateLaunch($Message, "Invalid nonce");

        # should pass if the nonce is known but has not been used
        $Cache->cache_nonce($Nonce);
        $this->validateLaunch($Message);

        $this->assertTrue(
            $this->Launch->is_resource_launch()
        );
        $this->assertFalse(
            $this->Launch->is_deep_link_launch()
        );
        $this->assertFalse(
            $this->Launch->is_submission_review_launch()
        );

        $this->assertFalse(
            $this->Launch->has_nrps()
        );
        $this->assertFalse(
            $this->Launch->has_gs()
        );
        $this->assertFalse(
            $this->Launch->has_ags()
        );

        # replay should fail
        $this->validateLaunch($Message, "Invalid nonce");
    }

    private function validateLaunch(
        array $Request,
        string $ExceptionMessage = null
    ): void {
        try {
            $this->Launch->validate($Request);
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

    private TestMessage $Launch;
}
