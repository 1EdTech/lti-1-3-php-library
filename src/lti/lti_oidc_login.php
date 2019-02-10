<?php
namespace IMSGlobal\LTI;

include_once("oidc_exception.php");
include_once("redirect.php");
include_once("cookie.php");
include_once("cache.php");

class LTI_OIDC_Login {

    private $db;
    private $cache;
    private $cookie;

    function __construct(Database $database, Cache $cache = null, Cookie $cookie = null) {
        $this->db = $database;
        if ($cache === null) {
            $cache = new Cache();
        }
        $this->cache = $cache;

        if ($cookie === null) {
            $cookie = new Cookie();
        }
        $this->cookie = $cookie;
    }

    public static function new(Database $database, Cache $cache = null, Cookie $cookie = null) {
        return new LTI_OIDC_Login($database, $cache, $cookie);
    }

    public function do_oidc_login_redirect($launch_url, array $request = null) {

        if ($request === null) {
            $request = $_REQUEST;
        }

        if (empty($launch_url)) {
            throw new OIDC_Exception("No launch URL configured", 1);
        }

        // Validate Request Data.
        $registration = $this->validate_oidc_login($request);

        /*
         * Build OIDC Auth Response.
         */

        // Generate State.
        // Set cookie (short lived)
        $state = str_replace('.', '_', uniqid('state-', true));
        $this->cookie->set_cookie("lti1p3_$state", $state);

        // Generate Nonce.
        $nonce = uniqid('nonce-', true);
        $this->cache->cache_nonce($nonce);

        // Build Response.
        $auth_params = [
            'scope'         => 'openid', // OIDC Scope.
            'response_type' => 'id_token', // OIDC response is always an id token.
            'response_mode' => 'form_post', // OIDC response is always a form post.
            'prompt'        => 'none', // Don't prompt user on redirect.
            'client_id'     => $registration->get_client_id(), // Registered client id.
            'redirect_uri'  => $launch_url, // URL to return to after login.
            'state'         => $state, // State to identify browser session.
            'nonce'         => $nonce, // Prevent replay attacks.
            'login_hint'    => $request['login_hint'] // Login hint to identify platform session.
        ];

        // Pass back LTI message hint if we have it.
        if (isset($request['lti_message_hint'])) {
            // LTI message hint to identify LTI context within the platform.
            $auth_params['lti_message_hint'] = $request['lti_message_hint'];
        }

        $auth_login_return_url = $registration->get_auth_login_url() . "?" . http_build_query($auth_params);

        // Return auth redirect.
        return new Redirect($auth_login_return_url, $state, $nonce);

    }

    protected function validate_oidc_login($request) {

        // Validate Issuer.
        if (empty($request['iss'])) {
            throw new OIDC_Exception("Could not find issuer", 1);
        }

        // Validate Login Hint.
        if (empty($request['login_hint'])) {
            throw new OIDC_Exception("Could not find login hint", 1);
        }

        // Fetch Registration Details.
        $registration = $this->db->find_registration_by_issuer($request['iss']);

        // Check we got something.
        if (empty($registration)) {
            throw new OIDC_Exception("Could not find registration details", 1);
        }

        // Return Registration.
        return $registration;
    }
}