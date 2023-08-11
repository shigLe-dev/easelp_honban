<?php 

session_start();

function h($s){
    return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
  }


if(isset($_SESSION["id"])){
    header("Location: ./dash.php");
}
?>

<html>
    <head>
        <meta charset="UTF-8">
        <title>easeLp - Let’s create your Help site and Chatbot-GPT easier,fastly,for free.</title>
        <link rel="stylesheet" href="./css/style.css">
        <link rel="stylesheet" href="./css/index.css">

        <link rel="preconnect" href="https://rsms.me/">
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
        <link rel="preconnect" href="https://fonts.googleapis.com/">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Quicksand:300,400,500,700">
        <link rel="icon" type="image/svg+xml" href="./logo.svg" />

        <meta name="viewport" content="width=device-width,initial-scale=1">

    </head>

    <body>
        <header>
            <?php include("./header.php"); ?>
        </header>

        <div class="title_p">
            <div class="main_title">
                <h1>Let’s create your <br>
                Help site<br>
                and Chatbot-GPT easier
                </h1>

                <div class="main">
                    <a href="./signup_form.php" class="btn_n">Get started for free</a>
                    <a href="./help.php?u=easelp" class="btn_d">Demo</a>
                </div>
            </div>
            <img src="./intro_img/undraw_tabs_re_a2bd.svg">
        </div>

        <div class="main_des">
            <div></div>
            <div class="sub_des">
                <img src="./intro_img/undraw_searching_re_3ra9.svg" class="img_right" style="float:right"></img>
                <h2>Create your Help site.</h2>
                <div class="main">
                    Create your own Help site fastly.you can write docs in markdown.<br>
                    You will use time and cost effectively if you use this service.<br>
                    <img src="./intro_img/1.png" class="big_img" style="display:block;padding-top:5vh;">
                </div>
            </div>

            <div class="sub_des" style="background:#333333;">
                <img src="./intro_img/undraw_chat_re_re1u.svg" class="img_right" style="float:left;"></img>
                <div>
                    <h2>Chatbot-GPT</h2>
                    <div class="main">
                        You can set up your Chatbot service by using <span style="color:#86b2ea;font-weight:400;">GPT</span>.<br>
                        And Chatbot read documents and answer a question<br>
                        Chatbot can understand <span style="color:#86b2ea;font-weight:400;">not only english but also many languages</span>.
                        <br>
                        You don't need to set up a difficult. 
                        <div style="padding-top:5vh;">
                            <img src="./intro_img/2.png" style="width:40vw;">
                            <img src="./intro_img/4.png" style="width:40vw;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="sub_des">
                <img src="./intro_img/undraw_searching_re_3ra9.svg" class="img_right" style="float:right"></img>
                <h2>GPT-writing assistant.</h2>
                <div class="main">
                    We have an <span style="color:#86b2ea;font-weight:400;">AI writing and chatting assistant</span>.<br>
                    You can write documents efficiently and have a smooth discussion

                    <img src="./intro_img/3.png" class="big_img" style="display:block;padding-top:5vh;">

                </div>
            </div>

            
            <div class="sub_des" style="background:#333333;">
                <img src="./intro_img/undraw_content_creator_re_pt5b.svg" class="img_right" style="float:left;"></img>
                <div>
                    <h2>Other ....</h2>
                    <div class="main">
                        We have more functions such as <span style="color:#86b2ea;font-weight:400;">discussion</span> ,
                        <span style="color:#86b2ea;font-weight:400;">to embed a chat-bot</span>,
                        <span style="color:#86b2ea;font-weight:400;">private mode</span>,etc.<br>
                    </div>
                </div>
            </div>
            
            <div class="sub_des" style="height:20vh;text-align:center;padding-left:0;">
                <h2>Get started now</h2>
                <a href="./signup_form.php" class="btn_n" style="margin-left:5vw;margin-top:3vh;">Sign up</a>
            </div>

        </div>

        <footer>
            <div style="padding-bottom:3vh;position:relative;">
                <img src="./logo.svg" style="width:2.5em;margin-right:0.5em;">
                <span style="position:absolute;top:1em;">by</span>
                <img src="./shigle_logo.svg" style="width:2.5em;margin-left:2em;">
            </div>
            Copyright @2023 <a href="https://shigle.net/en/" style="color:#fafaff;">shigLe</a> comminuty.
        </footer>


        <script>
            <?php

            //ログイン状態
            if(isset($_SESSION['id'])){
                echo('const user_id = "'.h($_SESSION['id']).'";');
            }else{
                echo('const user_id = "";');
            }

            ?>
        </script>
        <script src="main.js"></script>
    </body>
</html>