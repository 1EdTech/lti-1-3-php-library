<?PHP
namespace IMSGlobal\LTI;

class LTI_Deep_Link_Resource_Test extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $deep_link = LTI_Deep_Link_Resource::new();

        $this->assertEquals(
            "ltiResourceLink",
            $deep_link->get_type()
        );
        $this->assertEquals(
            'iframe',
            $deep_link->get_target()
        );
        $this->assertEquals(
            [],
            $deep_link->get_custom_params()
        );
        $this->assertNull($deep_link->get_lineitem());

        $deep_link->set_title("Test Title");
        $this->assertEquals(
            "Test Title",
            $deep_link->get_title()
        );

        $deep_link->set_url("http://issuer.example/test_url");
        $this->assertEquals(
            "http://issuer.example/test_url",
            $deep_link->get_url()
        );

        $ExpectedArray = [
            "type" => "ltiResourceLink",
            "title" => "Test Title",
            "url" => "http://issuer.example/test_url",
            "presentation" => [
                "documentTarget" => "iframe"
            ]
        ];
        $this->assertEquals(
            $ExpectedArray,
            $deep_link->to_array()
        );

        $deep_link->set_custom_params([
            "X-KEY-X" => "X-VAL-X",
        ]);

        $ExpectedArray["custom"] = [
            "X-KEY-X" => "X-VAL-X",
        ];
        $this->assertEquals(
            $ExpectedArray,
            $deep_link->to_array()
        );
    }
}
