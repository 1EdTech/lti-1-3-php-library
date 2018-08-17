<?php session_start(); ?>
<div class="box">
    <h3>Set Up Your Game</h3>
    <form method="POST" action="deeplink.php">
    <ul>
        <li>
            <lable>Difficulty</lable>
            <select name="difficulty">
                <option value="easy">Easy</option>
                <option value="normal" selected>Normal</option>
                <option value="hard">Hard</option>
            </select>
        </li>
        <li>
            <input type="submit" value="Go!" />
        </li>
    </ul>
</div>

<style>
    body {
        font-family: 'Tahoma';
    }
    .box {
        border: solid 1px #CCCCFF;
        padding: 16px;
        border-radius: 12px;
        background:white;
        display:block;
        margin-left: -120px;
        width:240px;
        position:absolute;
        left:50%;
        top:30px;
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
</style>
