<?PHP
namespace IMSGlobal\LTI;

class LTI_Deployment_Test extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $deployment = LTI_Deployment::new();

        $deployment->set_deployment_id("X-DEPLOYMENT-ID-X");

        $this->assertEquals(
            "X-DEPLOYMENT-ID-X",
            $deployment->get_deployment_id()
        );
    }
}
