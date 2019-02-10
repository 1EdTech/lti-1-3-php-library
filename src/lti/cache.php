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

    public function cache_nonce($nonce) {
        $_SESSION['nonce'][$nonce] = true;
        return $this;
    }

    public function check_nonce($nonce) {
        if (!isset($_SESSION['nonce'][$nonce])) {
            return false;
        }
        unset($_SESSION['nonce'][$nonce]);
        return true;
    }
}
?>