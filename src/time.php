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

$time_line_item = get_line_item('timescore');

// Build grade book request
$grade_call = [
    "scoreGiven" => $_REQUEST['time'],
    "activityProgress" => "Completed",
    "gradingProgress" => "Completed",
    "timestamp" => "2017-02-07T12:34:56+00:00",
    "userId" => $_SESSION['current_request']['sub']
];

// Call grade book line item endpoint to send back a grade
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $time_line_item['id'] . '/scores');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($grade_call));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer '. $access_token,
    'Content-Type: application/vnd.ims.lis.v1.score+json'
]);
$line_item = curl_exec($ch);
curl_close ($ch);

echo "\n\nSubmit score\n\n";

echo $access_token;
echo $line_item;

?>