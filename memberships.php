<?php
require_once('serviceauth.php');
require_once('lineitem.php');

session_start();

// Getting access token with the scopes for the service calls we want to make
// so they are all authenticated (see serviceauth.php)
$access_token = get_access_token([
    "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem",
    "https://purl.imsglobal.org/spec/lti-ags/scope/score",
    "https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly",
    "https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly"
]);

// Memberships call
$ch = curl_init();
$memberships_url = $_SESSION['current_request']['https://purl.imsglobal.org/spec/lti-nrps/claim/namesroleservice']['context_memberships_url'];
curl_setopt($ch, CURLOPT_URL, $memberships_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer '. $access_token
]);
$members = json_decode(curl_exec($ch), true);
curl_close ($ch);
//echo json_encode($members, JSON_PRETTY_PRINT);

// Find the Score line item
$line_item_url;
if (empty($_SESSION['current_request']['https://purl.imsglobal.org/spec/lti-ags/claim/endpoint']['lineitem'])) {
    // We were't given a line item, so we will find or create it.
    $score_line_item = get_line_item('score', 108);
    $line_item_url = $score_line_item['id'];
} else {
    // We were given the line item url in the launch, use that
    $line_item_url = $_SESSION['current_request']['https://purl.imsglobal.org/spec/lti-ags/claim/endpoint']['lineitem'];
}
// Results call
$ch = curl_init();
$results_url = $line_item_url. '/results';
curl_setopt($ch, CURLOPT_URL, $results_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer '. $access_token,
    'Accept: application/vnd.ims.lis.v2.resultcontainer+json'
]);
$resp = curl_exec($ch);
$results = json_decode($resp, true);
curl_close ($ch);
//echo json_encode($results, JSON_PRETTY_PRINT);


// Line items GET
$time_line_item = get_line_item('timescore');
// Results call for time score
$ch = curl_init();
$time_results_url = $time_line_item['id'] . '/results';
curl_setopt($ch, CURLOPT_URL, $time_results_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer '. $access_token,
    'Accept: application/vnd.ims.lis.v2.resultcontainer+json'
]);
$resp = curl_exec($ch);
$time_results = json_decode($resp, true);
curl_close ($ch);
//echo json_encode($time_results, JSON_PRETTY_PRINT);

$final_members = [];

foreach ($members['members'] as $member) {
    if (!empty($member['status']) && $member['status'] != 'Active') {
        continue;
    }
    // Find their current score
    foreach ($results as $result) {
        if (!empty($final_members[$member['user_id']]) && $final_members[$member['user_id']]['score'] > $result['resultScore']) {
            continue;
        }
        if ($member['user_id'] == $result['userId']) {
            $final_members[$member['user_id']] = [
                'name' => $member['name'],
                'id' => $member['user_id'],
                'score' => $result['resultScore']
            ];
            //break;
        }
    }

    foreach ($time_results as $result) {
        if ($member['user_id'] == $result['userId']) {
            $final_members[$member['user_id']]['time'] = $result['resultScore'];
            //break;
        }
    }

}

function cmp($a, $b) {
    if ($a['score'] == $b['score']) {
        return ($a['time'] < $b['time']) ? -1 : 1;
    }

    return ($a['score'] < $b['score']) ? 1 : -1;
}

usort($final_members, "cmp");

echo json_encode(array_values($final_members), JSON_PRETTY_PRINT);

?>