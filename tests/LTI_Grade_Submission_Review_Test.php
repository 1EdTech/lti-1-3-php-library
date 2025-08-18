<?PHP
namespace IMSGlobal\LTI;

class LTI_Grade_Submission_Review_Test extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $gsr = LTI_Grade_Submission_Review::new();

        $gsr
            ->set_reviewable_status("status")
            ->set_label("label")
            ->set_url("https://issuer.example")
            ->set_custom("custom");

        $this->assertEquals(
            "status",
            $gsr->get_reviewable_status()
        );
        $this->assertEquals(
            "label",
            $gsr->get_label()
        );
        $this->assertEquals(
            "https://issuer.example",
            $gsr->get_url()
        );
        $this->assertEquals(
            "custom",
            $gsr->get_custom()
        );

        $ExpectedString =
            '{"reviewableStatus":"status","label":"label",'
            .'"url":"https:\/\/issuer.example","custom":"custom"}';
        $this->assertEquals($ExpectedString, (string)$gsr);
    }
}
