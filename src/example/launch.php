<?php
include_once("../lti/lti.php");
include_once("example_database.php");

use \IMSGlobal\LTI\LTI_Message_Launch;
$launch = LTI_Message_Launch::new(new Example_Database())
    ->validate();
if ($launch->is_deep_link_launch()) {
    ?><a href="<?= TOOL_HOST ?>/example/deep_link_response.php?launch_id=<?= $launch->get_launch_id(); ?>">Return Deep Link</a><?php
    die;
}
if (!$launch->is_resource_launch()) {
    throw new Exception("Currently only deep link and resource launches are supported");
}

?>
<img style="position:absolute; left:50%; top:20px; margin-left:-200px;" src="https://66.media.tumblr.com/addb47b81e8d8c33c0c9a2abd7b442e6/tumblr_p1bnydcv9t1toamj8o1_500.gif" id="spin-img" />
<script>
    var xhttp = new XMLHttpRequest();
    xhttp.addEventListener("load", function() {
        var members = JSON.parse(this.responseText);
        var xhttp2 = new XMLHttpRequest();
        xhttp2.open("GET", "grade.php?score=15&user_id=" + members[0]['user_id'] + "&launch_id=<?= $launch->get_launch_id() ?>", true);
        xhttp2.send();
    });
    xhttp.open("GET", "members.php?launch_id=<?= $launch->get_launch_id() ?>", true);
    xhttp.send();
</script>
