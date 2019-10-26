<?php
namespace IMSGlobal\LTI;

class Cookie {
    public function get_cookie($name) {
        if (!isset($_COOKIE[$name])) {
            return false;
        }
        return $_COOKIE[$name];
    }

    public function set_cookie($name, $value, $exp = 3600) {
        setcookie($name, $value, time() + $exp);
        return $this;
    }
}
?>
