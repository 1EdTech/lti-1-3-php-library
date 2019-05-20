<?php
include_once("../lti/lti.php");
include_once("example_database.php");

use \IMSGlobal\LTI\LTI_Message_Launch;
use \IMSGlobal\LTI\LTI_Deep_Link_Resource;
$launch = LTI_Message_Launch::from_cache($_REQUEST['launch_id'], new Example_Database());
if (!$launch->is_deep_link_launch()) {
    throw new Exception("Must be a deep link!");
}
$resource = LTI_Deep_Link_Resource::new()
    ->set_url(TOOL_HOST . "/example/launch.php")
    ->set_custom_params(['test' => 'testing'])
    ->set_title('It Lives!');
$launch->get_deep_link()
    ->output_response_form([$resource]);
?>