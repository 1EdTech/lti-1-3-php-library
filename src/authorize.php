<?php
include('game.php');
include('setupform.php');

// Auth Stuff

$state = uniqid('state-', true);
$nonce = uniqid('nonce-', true);
?>
<img style="position:absolute; left:50%; top:20px; margin-left:-400px;" src="https://66.media.tumblr.com/addb47b81e8d8c33c0c9a2abd7b442e6/tumblr_p1bnydcv9t1toamj8o1_500.gif" id="spin-img" />
<iframe style="position:absolute; right:0; width:400px; top:0; bottom:0px; height:100%; border:none;" id="auth_frame" name="auth_frame"></iframe>
<form id="auth_form" action="https://lti-ri.imsglobal.org/platforms/7/authorizations/new" method="GET"  target="auth_frame">
    <!-- static fields -->
    <input type="hidden" name="scope" value="openid"/>
    <input type="hidden" name="response_type" value="id_token"/>
    <input type="hidden" name="prompt" value="none"/>

    <input type="hidden" name="client_id" value="<?= $client_id ?>"/>
    <input type="hidden" name="redirect_uri" value=""/>
    <input type="hidden" name="login_hint" value="<?= $_REQUEST['login_hint'] ?>"/>
    <input type="hidden" name="lti_message_hint" value="<?= $_REQUEST['lti_message_hint'] ?>"/>
    <input type="hidden" name="state" value="<?= $state ?>"/>
    <input type="hidden" name="nonce" value="<?= $nonce ?>"/>
</form>

<script>
    var state = '<?= $state ?>';
    var authorized = function(session) {
        if (session.state != state) {
            // Something's wrong
            alert('Invalid state given');
            return;
        }
        window.session = session;
        if (session.message_type == "LtiDeepLinkingRequest") {
            document.getElementById('setup-form').style = '';
        } else {
            document.getElementById('game-screen').style = '';
            //start game
        }
        document.getElementById('spin-img').style = 'display:none';
        document.cookie = 'be_session_id=' + session.be_session_id + '; path=/';
        document.getElementById('auth_frame').style = 'display:none';
    }

    var auth_form = document.getElementById('auth_form');
    auth_form.submit();
</script>