<?php
namespace IMSGlobal\LTI;

class LTI_Deployment {

    private string $deployment_id;

    public static function new(): self {
        return new LTI_Deployment();
    }

    public function get_deployment_id(): string {
        return $this->deployment_id;
    }

    public function set_deployment_id(string $deployment_id): self {
        $this->deployment_id = $deployment_id;
        return $this;
    }
}
