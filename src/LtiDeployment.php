<?php
namespace LTI;

class LtiDeployment {

    private $deployment_id;

    public static function new() {
        return new LtiDeployment();
    }

    public function get_deployment_id() {
        return $this->deployment_id;
    }

    public function set_deployment_id($deployment_id) {
        $this->deployment_id = $deployment_id;
        return $this;
    }

}

