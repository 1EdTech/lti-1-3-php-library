<?php
include_once("../../lti/lti.php");
include_once("../db/example_database.php");

use \IMSGlobal\LTI;
$launch = LTI\LTI_Message_Launch::from_cache($_REQUEST['launch_id'], new Example_Database());
if (!$launch->has_ags()) {
    throw new Exception("Don't have grades!");
}
$grades = $launch->get_ags();

$score = LTI\LTI_Grade::new()
    ->set_score_given($_REQUEST['score'])
    ->set_score_maximum(100)
    ->set_timestamp(date(DateTime::ISO8601))
    ->set_activity_progress('Completed')
    ->set_grading_progress('FullyGraded')
    ->set_user_id($launch->get_launch_data()['sub']);
$score_lineitem = LTI\LTI_Lineitem::new()
    ->set_tag('score')
    ->set_score_maximum(100)
    ->set_label('Score')
    ->set_resource_id($launch->get_launch_data()['https://purl.imsglobal.org/spec/lti/claim/resource_link']['id']);
$grades->put_grade($score, $score_lineitem);


$time = LTI\LTI_Grade::new()
    ->set_score_given($_REQUEST['time'])
    ->set_score_maximum(999)
    ->set_timestamp(date(DateTime::ISO8601))
    ->set_activity_progress('Completed')
    ->set_grading_progress('FullyGraded')
    ->set_user_id($launch->get_launch_data()['sub']);
$time_lineitem = LTI\LTI_Lineitem::new()
    ->set_tag('time')
    ->set_score_maximum(999)
    ->set_label('Time Taken')
    ->set_resource_id('time'.$launch->get_launch_data()['https://purl.imsglobal.org/spec/lti/claim/resource_link']['id']);
$grades->put_grade($time, $time_lineitem);
echo '{"success" : true}';
?>