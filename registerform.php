<?php session_start(); ?>
<div class="container">
<div class="box" id="registerbox">
    <h3>Register</h3>
    <ul>
        <li>
            <lable>Key Set URL</lable>
            <input id="key-set" type="text" name="key_set_url" value="https://lti-ri.imsglobal.org/platforms/7/platform_keys/6.json" />
        </li>
        <li>
            <lable>Auth Token URL</lable>
            <input id="auth-token" type="text" name="auth_token_url" value="https://lti-ri.imsglobal.org/platforms/7/access_tokens" />
        </li>
        <li>
            <input type="submit" value="Go!" onclick="register();" />
        </li>
    </ul>
</div>

<div class="box" id="deploybox">
    <h3>Deploy</h3>
    <ul>
        <li>
            <input id="account-input" type="text" name="account" value="" placeholder="Account" />
            <span class="help-icon" title="Account to link with Deplyment: <?= $jwt_body['https://purl.imsglobal.org/spec/lti/claim/deployment_id'] ?>">?</span>
        </li>
        <li>
            <input type="submit" value="Go!" onclick="deploy();" />
        </li>
    </ul>
</div>

<div class="box" id="donebox">
    <form method="GET" action="<?= $jwt_body['https://purl.imsglobal.org/spec/lti/claim/launch_presentation']['return_url'] ?>">
        <input type="submit" value="Done" />
    </form>
</div>

<div class="box" id="messagebox">
</div>
</div>

<script>
function deploy() {
    var xhttp = new XMLHttpRequest();
    var query = 'deployment_id=<?= $jwt_body['https://purl.imsglobal.org/spec/lti/claim/deployment_id'] ?>';
    query += '&iss=<?= $jwt_body['iss'] ?>';
    query += '&client_id=<?= $client_id ?>';
    query += '&account=' + encodeURIComponent(document.getElementById('account-input').value);
    query += '&deployment=true';
    xhttp.open("POST", "deploy.php?" + query, false);
    xhttp.send();
    document.getElementById('deploybox').style = '';
    document.getElementById('donebox').style = 'display:block';
    document.getElementById('messagebox').innerHTML = '<?= $jwt_body['https://purl.imsglobal.org/spec/lti/claim/deployment_id'] ?> deployed to account '
        + encodeURIComponent(document.getElementById('account-input').value);
}

function register() {
    var xhttp = new XMLHttpRequest();
    var query = 'deployment_id=<?= $jwt_body['https://purl.imsglobal.org/spec/lti/claim/deployment_id'] ?>';
    query += '&iss=<?= $jwt_body['iss'] ?>';
    query += '&client_id=<?= $client_id ?>';
    query += '&key_set_url=' + encodeURIComponent(document.getElementById('key-set').value);
    query += '&auth_token_url=' + encodeURIComponent(document.getElementById('auth-token').value);
    query += '&registration=true';
    xhttp.open("POST", "register.php?" + query, false);
    xhttp.send();
    document.getElementById('registerbox').style = '';
    document.getElementById('deploybox').style = "display:block";
    document.getElementById('messagebox').innerHTML = '<?= $client_id ?> registerd to <?= $jwt_body['iss'] ?>';
}

var registered = <?= empty($_SESSION['issuers'][$_REQUEST['iss']]['clients'][$_REQUEST['client_id']]) ? 'false' : 'true' ?>

if (!registered) {
    document.getElementById('registerbox').style = "display:block";
} else {
    document.getElementById('deploybox').style = "display:block";
}
eval('session = <?= json_encode($_SESSION) ?>');
console.log(session);
</script>

<style>
    body {
        font-family: 'Tahoma';
    }
    .container {
        display:block;
        margin-left: -120px;
        width:240px;
        position:absolute;
        left:50%;
        top:30px;
    }
    .box {
        display:none;
        border: solid 1px #CCCCFF;
        padding: 16px;
        border-radius: 12px;
        background:white;
    }
    .box ul li {
        list-style:none;
        padding-bottom:6px;
    }
    .box ul {
        padding:0;
    }
    .box h3 {
        margin:0;
    }
    .help-icon {
        background: #AAAAFF;
        border-radius:50%;
        display:inline-block;
        width:18px;
        height:18px;
        text-align:center;
        font-size:14px;
    }
    .box input {
        border:solid 1px #aaa;
        border-radius:4px;
        padding: 4px 6px;
    }
    .box lable {
        display:block;
        font-size:14;
        font-weight: bold;
    }
    #messagebox {
        padding: 6px;
        display: block;
        border:none;
    }
</style>
