<?php
function die_with($message) {
    echo $message;
    die;
}


function get_url_content($url) {
    $options = array(
        CURLOPT_RETURNTRANSFER => true,   // return web page
        CURLOPT_HEADER         => false,  // don't return headers
        CURLOPT_FOLLOWLOCATION => true,   // follow redirects
        CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
        CURLOPT_ENCODING       => "",     // handle compressed
        CURLOPT_USERAGENT      => "test", // name of client
        CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
        CURLOPT_TIMEOUT        => 120,    // time-out on response
    );

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);

    $content  = curl_exec($ch);

    curl_close($ch);

    return $content;
}

function db() {
    return new FakerBase();
}

class FakerBase {
    function set_registration($iss, $client_id, $key_set_url, $auth_token_url, $private_key = false) {
        $_SESSION['issuers'][$iss]['clients'][$client_id]['key_set_url'] = $key_set_url;
        $_SESSION['issuers'][$iss]['clients'][$client_id]['auth_token_url'] = $auth_token_url;
        return $this;
    }

    function set_deployment($iss, $client_id, $deployment_id, $account_id) {
        $_SESSION['issuers'][$iss]['clients'][$client_id]['deployments'][$deployment_id] = $account_id;
        return $this;
    }

    function get_registration($iss, $client_id) {
        return $_SESSION['issuers'][$iss]['clients'][$client_id];
    }

    function get_deployment($iss, $client_id, $deployment_id) {
        return $_SESSION['issuers'][$iss]['clients'][$client_id]['deployments'][$deployment_id];
    }
}