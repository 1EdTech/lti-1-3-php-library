<?php
include_once("../lti/lti.php");
include_once("example_database.php");

use \IMSGlobal\LTI\LTI_Message_Launch;
$launch = LTI_Message_Launch::from_cache($_REQUEST['launch_id'], new Example_Database());
if (!$launch->has_nrps()) {
    throw new Exception("Don't have names and roles!");
}
echo json_encode($launch->get_nrps()->get_members());
?>