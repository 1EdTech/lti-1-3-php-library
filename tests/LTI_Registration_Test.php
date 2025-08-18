<?PHP
namespace IMSGlobal\LTI;

class LTI_Registration_Test extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $registration = LTI_Registration::new();

        $registration
            ->set_issuer("https://issuer.example")
            ->set_client_id("X-TEST-CLIENT-ID-X")
            ->set_key_set_url("https://issuer.example/keyset")
            ->set_auth_token_url("https://issuer.example/auth_token")
            ->set_auth_login_url("https://issuer.example/login")
            ->set_tool_private_key("X-TEST-PRIVATE-KEY-X");

        $this->assertEquals(
            "https://issuer.example",
            $registration->get_issuer()
        );
        $this->assertEquals(
            "X-TEST-CLIENT-ID-X",
            $registration->get_client_id()
        );
        $this->assertEquals(
            "https://issuer.example/keyset",
            $registration->get_key_set_url()

        );
        $this->assertEquals(
            "https://issuer.example/auth_token",
            $registration->get_auth_token_url()
        );
        $this->assertEquals(
            "https://issuer.example/login",
            $registration->get_auth_login_url()
        );
        $this->assertEquals(
            "X-TEST-PRIVATE-KEY-X",
            $registration->get_tool_private_key()
        );
        $this->assertEquals(
            '4a32c86c8b616875153795f79550891b2a0b683dd9c403a1d135bd45b0137ae8',
            $registration->get_kid()
        );

        $this->assertEquals(
            "https://issuer.example/auth_token",
            $registration->get_auth_server()
        );

        $registration->set_auth_server("https://issuer.example/auth_server");
        $this->assertEquals(
            "https://issuer.example/auth_server",
            $registration->get_auth_server()
        );

        $registration->set_kid("X-TEST-KID-X");
        $this->assertEquals(
            "X-TEST-KID-X",
            $registration->get_kid()
        );
    }
}
