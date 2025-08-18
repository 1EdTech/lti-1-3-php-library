<?php
namespace IMSGlobal\LTI;

class TestCookie extends Cookie{
    public function get_cookie(string $name) {
        if (isset($this->cookies[$name])) {
            return $this->cookies[$name];
        }
        return false;
    }

    public function set_cookie(string $name, string $value, int $exp = 3600, array $options = []): self {
        $this->cookies[$name] = $value;

        return $this;
    }

    private $cookies = [];
}
