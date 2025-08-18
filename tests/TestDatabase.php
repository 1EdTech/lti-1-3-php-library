<?PHP
namespace IMSGlobal\LTI;

class TestDatabase implements Database
{
    public function find_registration_by_issuer(
        string $iss,
        ?string $client_id
    ): ?LTI_Registration {
        if ($iss == "https://issuer.example") {
            $result = LTI_Registration::new()
                ->set_client_id("X-CLIENT-ID-X")
                ->set_auth_login_url("https://issuer.example/login");
            return $result;
        }

        return null;
    }

    public function find_deployment(
        string $iss,
        string $deployment_id
    ): ?LTI_Deployment {
        if ($iss == "https://issuer.example" &&
            $deployment_id == "X-DEPLOYMENT-ID-X") {
            return LTI_Deployment::new()
                ->set_deployment_id("X-DEPLOYMENT-ID-X");
        }

        return null;
    }
}
