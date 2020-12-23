<?php
namespace LTI\Interfaces;

interface Database
{
    public function findRegistrationByIssuer($iss, $client_id = null);
    public function findDeployment($iss, $deployment_id, $client_id = null);
}
