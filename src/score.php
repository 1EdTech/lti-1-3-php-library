<?php
require_once('serviceauth.php');
require_once('lineitem.php');

session_start();

// Getting access token with the scopes for the service calls we want to make
// so they are all authenticated (see serviceauth.php)
$access_token = get_access_token([
    "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem",
    "https://purl.imsglobal.org/spec/lti-ags/scope/score"
]);

$session = $_SESSION[$_COOKIE['be_session_id']];

// Build grade book request
$grade_call = [
    "scoreGiven" => $_REQUEST['grade'],
    "scoreMaximum" => 108,
    "comment" => "This is exceptional work",
    "activityProgress" => "Completed",
    "gradingProgress" => "Completed",
    "timestamp" => "2017-02-07T12:34:56+00:00",
    "userId" => $session['sub']
];

// Call grade book line item endpoint to send back a grade
$line_item_url;
if (empty($session['https://purl.imsglobal.org/spec/lti-ags/claim/endpoint']['lineitem'])) {
    $line_item = get_line_item('score', 108);
    $line_item_url = $line_item['id'];
} else {
    $line_item_url = $session['https://purl.imsglobal.org/spec/lti-ags/claim/endpoint']['lineitem'];
}
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $line_item_url . '/scores');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($grade_call));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer '. $access_token,
    'Content-Type: application/vnd.ims.lis.v1.score+json'
]);
$score = curl_exec($ch);
curl_close ($ch);

echo $access_token;
echo $score;

?>