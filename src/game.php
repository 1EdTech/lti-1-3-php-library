<div id="game-screen" style="display:none;">
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
<style>
    body {
        font-family: 'Gugi', cursive;
    }
    #scoreboard {
        border: solid 1px #000;
        border-left: none;
        border-top-right-radius: 20px;
        border-bottom-right-radius: 20px;
        padding-bottom: 12px;
        background: linear-gradient(to bottom, rgb(0, 0, 0), rgb(0, 0, 50) 500px);
        color: white;
    }
    th, td {
        color: white;
    }
</style>
<script>
    // Set game difficulty if it has been set in deep linking
    var curr_diff = '<?= $jwt_body['https://purl.imsglobal.org/spec/lti/claim/custom']['difficulty'] ?: 'normal'; ?>';
    var curr_user_name = '<?= $jwt_body['name']; ?>';
</script>
<script type="text/javascript" src="js/breakout.js" charset="utf-8"></script>