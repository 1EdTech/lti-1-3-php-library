<?php
namespace LTI;

interface Cookie {
    public function getCookie($name);
    public function setCookie($name, $value, $exp = 3600, $options = []);
}
