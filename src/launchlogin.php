<?php
session_start();

// Check if this is an OIDC launch.
if (!empty($_REQUEST) && !empty($_REQUEST['iss']) && !empty($_REQUEST['login_hint'])) {
    // Check if the requested issuer has been registered.
    if (empty($_SESSION['issuers'][$_REQUEST['iss']])) {
        // If there is no key set url, go to registration
        $register_details = [
            'iss' => $_REQUEST['iss'],
            'client_id' => $_SESSION['issuers'][$_REQUEST['iss']]['client'], // What are the implications of this? We can only have one authorization url per issuer?
                                    // Would we not want a url per deployment?
        ];
        include('registerform.php');
        die;
    }

    // Return redirect page.
    include('authorize.php');
    die;
}
?>