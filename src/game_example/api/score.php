<?php
include_once("../../lti/lti.php");
include_once("../db/example_database.php");

use \IMSGlobal\LTI\LTI_Message_Launch;
use \IMSGlobal\LTI\LTI_Lineitem;
use \IMSGlobal\LTI\LTI_Grade;
$launch = LTI_Message_Launch::from_cache($_REQUEST['launch_id'], new Example_Database());
if (!$launch->has_ags()) {
    throw new Exception("Don't have grades!");
}
$grades = $launch->get_ags();

$score = LTI_Grade::new()
    ->set_score_given($_REQUEST['score'])
    ->set_score_maximum(100)
    ->set_user_id($launch->get_launch_data()['sub']);
$score_lineitem = LTI_Lineitem::new()
    ->set_tag('score')
    ->set_score_maximum(100)
    ->set_label('Score');
var_dump($grades->put_grade($score, $score_lineitem));


$time = LTI_Grade::new()
    ->set_score_given($_REQUEST['time'])
    ->set_score_maximum(999)
    ->set_user_id($launch->get_launch_data()['sub']);
$time_lineitem = LTI_Lineitem::new()
    ->set_tag('time')
    ->set_score_maximum(999)
    ->set_label('Time Taken');
var_dump($grades->put_grade($time, $time_lineitem));
echo '{"success" : true}';
?>