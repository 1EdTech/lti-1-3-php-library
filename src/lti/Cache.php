<?php
namespace IMSGlobal\LTI;

interface Cache {
    public function get_launch_data($key);
    public function cache_launch_data($key, $jwt_body);
    public function cache_nonce($nonce);
    public function check_nonce($nonce);
}
