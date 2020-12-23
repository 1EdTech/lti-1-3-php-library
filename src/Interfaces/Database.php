<?php
namespace Packback\Lti1p3\Interfaces;

interface Database
{
    public function findRegistrationByIssuer($iss, $client_id = null);
    public function findDeployment($iss, $deployment_id, $client_id = null);
}
