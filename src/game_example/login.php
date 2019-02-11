<?php
include_once("../lti/lti.php");
include_once("db/example_database.php");

use \IMSGlobal\LTI\LTI_OIDC_Login;

LTI_OIDC_Login::new(new Example_Database())
    ->do_oidc_login_redirect(TOOL_HOST . "/game_example/game.php")
    ->do_redirect();
?>