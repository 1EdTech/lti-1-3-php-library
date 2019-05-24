<?php
include_once("lti_oidc_login.php");
include_once("lti_message_launch.php");
include_once("database.php");
if(array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER)) {
  define("TOOL_HOST", ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?: $_SERVER['REQUEST_SCHEME']) . '://' . $_SERVER['HTTP_HOST']);
} else {
  define("TOOL_HOST", $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']);
}

?>