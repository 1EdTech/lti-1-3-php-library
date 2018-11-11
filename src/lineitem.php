<?php
require_once('serviceauth.php');

session_start();

function get_line_item($tag, $max_score = 9999) {

    // Getting access token with the scopes for the service calls we want to make
    // so they are all authenticated (see serviceauth.php)
    $access_token = get_access_token([
        "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem"
    ]);

    $be_session_id = $_COOKIE['be_session_id'];

    // Line items GET
    $ch = curl_init();
    $line_items_url = $_SESSION[$be_session_id]['https://purl.imsglobal.org/spec/lti-ags/claim/endpoint']['lineitems'];
    curl_setopt($ch, CURLOPT_URL, $line_items_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer '. $access_token,
        'Accept: application/vnd.ims.lis.v2.lineitemcontainer+json'
    ]);
    $resp = curl_exec($ch);
    $line_items = json_decode($resp, true);
    curl_close ($ch);

    $found_line_item = [];
    foreach ($line_items as $line_item) {
        if ($line_item['tag'] == $tag) {
            $found_line_item = $line_item;
            break;
        }
    }

    // if we can't find it, create it
    if (empty($found_line_item)) {
        // Build line item book request
        $new_line_item = [
            "label" => $tag,
            "tag" => $tag,
            "resourceId" => "" . $_SESSION[$be_session_id]['https://purl.imsglobal.org/spec/lti/claim/resource_link']['id'],
            "scoreMaximum" => $max_score,
        ];

        // Call grade book line item endpoint to send back a grade
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $_SESSION[$be_session_id]['https://purl.imsglobal.org/spec/lti-ags/claim/endpoint']['lineitems']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($new_line_item));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '. $access_token,
            'Content-Type: application/vnd.ims.lis.v2.lineitem+json',
            'Accept: application/vnd.ims.lis.v2.lineitem+json'
        ]);
        $line_item = curl_exec($ch);
        curl_close ($ch);

        $found_line_item = $line_item;
    }

    return $found_line_item;
}


?>