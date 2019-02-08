<?php
namespace IMSGlobal\LTI;

class Cache {
    public function get_launch_data($key) {
        return $_SESSION[$key];
    }

    public function cache_launch_data($key, $jwt_body) {
        $_SESSION[$key] = $jwt_body;
        return $this;
    }
}
?>