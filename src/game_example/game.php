<?php
include_once("../lti/lti.php");
include_once("db/example_database.php");

use \IMSGlobal\LTI;
$launch = LTI\LTI_Message_Launch::new(new Example_Database())
    ->validate();

?><link href="static/breakout.css" rel="stylesheet"><?php

if ($launch->is_deep_link_launch()) {
    ?>
    <div class="dl-config">
        <h1>Pick a Difficulty</h1>
        <ul>
            <li><a href="<?= TOOL_HOST ?>/game_example/configure.php?diff=easy&launch_id=<?= $launch->get_launch_id(); ?>">Easy</a></li>
            <li><a href="<?= TOOL_HOST ?>/game_example/configure.php?diff=normal&launch_id=<?= $launch->get_launch_id(); ?>">Normal</a></li>
            <li><a href="<?= TOOL_HOST ?>/game_example/configure.php?diff=hard&launch_id=<?= $launch->get_launch_id(); ?>">Hard</a></li>
        </ul>
    </div>
    <?php
    die;
}
?>

<div id="game-screen">
    <div style="position:absolute;width:1000px;margin-left:-500px;left:50%; display:block">
        <div id="scoreboard" style="position:absolute; right:0; width:200px">
            <h2 style="margin-left:12px;">Scoreboard</h2>
            <table id="leadertable" style="margin-left:12px;">
            </table>
        </div>
        <canvas id="breakoutbg" width="800" height="500" style="position:absolute;left:0;border:0;">
        </canvas>
        <canvas id="breakout" width="800" height="500" style="position:absolute;left:0;">
        </canvas>
    </div>
</div>
<link href="https://fonts.googleapis.com/css?family=Gugi" rel="stylesheet">
<script>

    <?php
    $launch_data = $launch->get_launch_data();
    $difficulty = 'normal';
    if (array_key_exists('https://purl.imsglobal.org/spec/lti/claim/custom', $launch_data)) {
        if (array_key_exists('difficulty', $launch_data['https://purl.imsglobal.org/spec/lti/claim/custom'])) {
            $difficulty = $launch_data['https://purl.imsglobal.org/spec/lti/claim/custom']['difficulty'];
        }
        
    }
    ?>
    // Set game difficulty if it has been set in deep linking
    var curr_diff = "<?= $difficulty ?>";
    var curr_user_name = "<?= $launch->get_launch_data()['name']; ?>";
    var launch_id = "<?= $launch->get_launch_id(); ?>";
</script>
<script type="text/javascript" src="static/breakout.js" charset="utf-8"></script>