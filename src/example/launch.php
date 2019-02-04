<?php
include_once("../lti/lti.php");
include_once("../lti/lti_grade.php");
include_once("example_database.php");

use \IMSGlobal\LTI\LTI_Message_Launch;
use \IMSGlobal\LTI\LTI_Grade;
$launch = LTI_Message_Launch::new(new Example_Database())
    ->validate();
if (!$launch->is_resource_launch()) {
    throw new Exception("Currently only resource launch supported");
}

if ($launch->has_nrps()) {
    echo "We have memberships!<br/>";
    $memberships = $launch->get_nrps();
    $members = $memberships->get_members();
    if ($launch->has_ags()) {
        echo "We have grades!<br/>";
        $grades = $launch->get_ags();
        $grade = LTI_Grade::new()
            ->set_score_given(100)
            ->set_score_maximum(100)
            ->set_user_id($members['members'][0]['user_id']);
        var_dump($grades->put_grade($grade));
    }
}
?>