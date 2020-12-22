<?php
namespace LTI;

use \Firebase\JWT\JWT;
class LtiDeepLink {

    private $registration;
    private $deployment_id;
    private $deep_link_settings;

    public function __construct($registration, $deployment_id, $deep_link_settings) {
        $this->registration = $registration;
        $this->deployment_id = $deployment_id;
        $this->deep_link_settings = $deep_link_settings;
    }

    public function get_response_jwt($resources) {
        $message_jwt = [
            "iss" => $this->registration->get_client_id(),
            "aud" => [$this->registration->get_issuer()],
            "exp" => time() + 600,
            "iat" => time(),
            "nonce" => 'nonce' . hash('sha256', random_bytes(64)),
            LtiConstants::DEPLOYMENT_ID => $this->deployment_id,
            LtiConstants::MESSAGE_TYPE => "LtiDeepLinkingResponse",
            LtiConstants::VERSION => LtiConstants::V1_3,
            LtiConstants::DL_CONTENT_ITEMS => array_map(function($resource) { return $resource->to_array(); }, $resources),
            LtiConstants::DL_DATA => $this->deep_link_settings['data'],
        ];
        return JWT::encode($message_jwt, $this->registration->get_tool_private_key(), 'RS256', $this->registration->get_kid());
    }

    public function output_response_form($resources) {
        $jwt = $this->get_response_jwt($resources);
        ?>
        <form id="auto_submit" action="<?= $this->deep_link_settings['deep_link_return_url']; ?>" method="POST">
            <input type="hidden" name="JWT" value="<?= $jwt ?>" />
            <input type="submit" name="Go" />
        </form>
        <script>
            document.getElementById('auto_submit').submit();
        </script>
        <?php
    }
}
