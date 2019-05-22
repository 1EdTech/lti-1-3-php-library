<?php
require_once __DIR__ . '/../vendor/autoload.php';
include_once("../db/example_database.php");

use \IMSGlobal\LTI;
$launch = LTI\LTI_Message_Launch::from_cache($_REQUEST['launch_id'], new Example_Database());
if (!$launch->has_nrps()) {
    throw new Exception("Don't have names and roles!");
}
if (!$launch->has_ags()) {
    throw new Exception("Don't have grades!");
}
$ags = $launch->get_ags();

$score_lineitem = LTI\LTI_Lineitem::new()
    ->set_tag('score')
    ->set_score_maximum(100)
    ->set_label('Score')
    ->set_resource_id($launch->get_launch_data()['https://purl.imsglobal.org/spec/lti/claim/resource_link']['id']);;
$scores = $ags->get_grades($score_lineitem);

$time_lineitem = LTI\LTI_Lineitem::new()
    ->set_tag('time')
    ->set_score_maximum(999)
    ->set_label('Time Taken')
    ->set_resource_id('time'.$launch->get_launch_data()['https://purl.imsglobal.org/spec/lti/claim/resource_link']['id']);
$times = $ags->get_grades($time_lineitem);

$members = $launch->get_nrps()->get_members();

$scoreboard = [];

foreach ($scores as $score) {
    $result = ['score' => $score['resultScore']];
    foreach ($times as $time) {
        if ($time['userId'] === $score['userId']) {
            $result['time'] = $time['resultScore'];
            break;
        }
    }
    foreach ($members as $member) {
        if ($member['user_id'] === $score['userId']) {
            $result['name'] = $member['name'];
            break;
        }
    }
    $scoreboard[] = $result;
}
echo json_encode($scoreboard);
?>