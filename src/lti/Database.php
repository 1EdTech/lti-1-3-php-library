<?php
namespace IMSGlobal\LTI;

interface Database {
    public function find_registration_by_issuer(string $iss, ?string $client_id): ?LTI_Registration;
    public function find_deployment(string $iss, string $deployment_id): ?LTI_Deployment;
}
