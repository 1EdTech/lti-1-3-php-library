<?PHP
namespace IMSGlobal\LTI;

require_once(__DIR__."/TestDatabase.php");
require_once(__DIR__."/TestCache.php");
require_once(__DIR__."/TestCookie.php");

class LTI_ODIC_Login_Test extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $login = LTI_OIDC_Login::new(
            new TestDatabase(),
            new TestCache(),
            new TestCookie()
        );

        try {
            $login->do_oidc_login_redirect("", []);
            $this->fail(
                "Exception not thrown."
            );
        } catch (OIDC_Exception $Ex) {
            $this->assertEquals(
                "No launch URL configured",
                $Ex->getMessage()
            );
        }

        try {
            $login->do_oidc_login_redirect(
                "https://tool.example/launch",
                []
            );
        } catch (OIDC_Exception $Ex) {
            $this->assertEquals(
                "Could not find issuer",
                $Ex->getMessage()
            );
        }

        try {
            $login->do_oidc_login_redirect(
                "https://tool.example/launch",
                [
                    "iss" => "https://issuer.example",
                ]
            );
        } catch (OIDC_Exception $Ex) {
            $this->assertEquals(
                "Could not find login hint",
                $Ex->getMessage()
            );
        }

        try {
            $login->do_oidc_login_redirect(
                "https://tool.example/launch",
                [
                    "iss" => "https://issuer.invalid",
                    "login_hint" => "X-LOGIN-HINT-X",
                    "client_id" => "X-CLIENT-ID-X",
                ]
            );
        } catch (OIDC_Exception $Ex) {
            $this->assertEquals(
                "Could not find registration details",
                $Ex->getMessage()
            );
        }

        $result = $login->do_oidc_login_redirect(
            "https://tool.example/launch",
            [
                "iss" => "https://issuer.example",
                "login_hint" => "X-LOGIN-HINT-X",
                "client_id" => "X-CLIENT-ID-X",
            ]
        );
        $this->assertInstanceOf(
            'IMSGlobal\LTI\Redirect',
            $result
        );

        $result = $login->do_oidc_login_redirect(
            "https://tool.example/launch",
            [
                "iss" => "https://issuer.example",
                "login_hint" => "X-LOGIN-HINT-X",
                "client_id" => "X-CLIENT-ID-X",
                "lti_message_hint" => "X-LTI-MESSAGE-HINT-X",
            ]
        );
        $this->assertInstanceOf(
            'IMSGlobal\LTI\Redirect',
            $result
        );
    }
}
