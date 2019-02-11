<?php
namespace IMSGlobal\LTI;

include_once("registration.php");
include_once("deployment.php");

interface Database {
    public function find_registration_by_issuer($iss);
    public function find_deployment($iss, $deployment_id);
}

?>