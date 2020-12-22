<?php
namespace LTI;

interface Database {
    public function find_registration_by_issuer($iss, $client_id = null);
    public function find_deployment($iss, $deployment_id, $client_id = null);
}

