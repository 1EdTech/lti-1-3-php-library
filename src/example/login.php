<?php
include_once("../lti/lti.php");
include_once("example_database.php");

use \IMSGlobal\LTI\LTI_OIDC_Login;

LTI_OIDC_Login::new(new Example_Database())
    ->do_oidc_login_redirect("http://localhost/example/launch.php")
    ->do_js_redirect();

?>