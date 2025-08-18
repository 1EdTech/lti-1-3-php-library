<?php
// Import all
$filenames = glob(__DIR__ . "/*.php");
if ($filenames === false) {
    throw new \Exception("glob() failed.");
}
foreach ($filenames as $filename) {
    require_once $filename;
}
define(
    "TOOL_HOST",
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ?
     "" :
     $_SERVER['REQUEST_SCHEME']) . '://' . $_SERVER['HTTP_HOST']
);
Firebase\JWT\JWT::$leeway = 5;
