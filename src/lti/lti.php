<?php
include_once("lti_oidc_login.php");
include_once("lti_message_launch.php");
include_once("database.php");
define("TOOL_HOST", ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?: $_SERVER['REQUEST_SCHEME']) . '://' . $_SERVER['HTTP_HOST']);
Firebase\JWT\JWT::$leeway = 5;
?>