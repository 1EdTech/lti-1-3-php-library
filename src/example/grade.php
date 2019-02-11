<?php
include_once("../lti/lti.php");
include_once("example_database.php");

use \IMSGlobal\LTI\LTI_Message_Launch;
use \IMSGlobal\LTI\LTI_Lineitem;
use \IMSGlobal\LTI\LTI_Grade;
$launch = LTI_Message_Launch::from_cache($_REQUEST['launch_id'], new Example_Database());
if (!$launch->has_ags()) {
    throw new Exception("Don't have grades!");
}
$grades = $launch->get_ags();
$grade = LTI_Grade::new()
    ->set_score_given($_REQUEST['score'])
    ->set_score_maximum(100)
    ->set_user_id($_REQUEST['user_id']);
echo json_encode($grades->put_grade($grade));
?>