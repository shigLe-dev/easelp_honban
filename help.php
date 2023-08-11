<?php 

include("./sql_info.php");

session_start();

function h($s){
    return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
  }

$user_found = false; //ユーザーが見つかったか
//ユーザー名の存在を確認
if(isset($_GET["u"])){
    $user_name = $_GET["u"]; //search keyword

    if(!preg_match("/^[0-9A-Za-z]+$/", $user_name)){
        exit();
    }

    try{
        $dbh = new PDO($dsn, $sql_user, $sql_password);

        //userが存在しているか
        $result = $dbh->query("select * from users where id='".$user_name."';");
        
        foreach($result as $row){
            if($row["id"] === $user_name){
                $user_found = true;
            }
        }

    }catch (PDOException $e){
        print('Error:'.$e->getMessage());
        die();
    }

}
    
?>

<html>
    <head>
        <meta charset="UTF-8">
        <title>easeLp - <?php 
                    //ユーザー名を表示
                    if($user_found === false){
                        //userが存在しなかったら
                        echo('The user was not found');
                    }else{
                        //存在したら
                        echo($user_name);
                    }?></title>
        <link rel="stylesheet" href="./css/style.css">

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

        <div class="search_area">
            <div class="help_name">
                <span class="help">Help</span>
                <span class="name">
                <?php 
                    //ユーザー名を表示
                    if($user_found === false){
                        //userが存在しなかったら
                        echo('<span style="color:#ff4055">The user was not found :(</span>');
                    }else{
                        //存在したら
                        echo($user_name);
                    }?>
                </span>
            </div>

            <?php 
            if($user_found){
                echo('
                <div style="margin-top:3vh;text-align: left;display: inline-block;position:relative;">
                <!--<span style="margin-left:0.5em;">Enter here what you want to solve.</span><br>-->
                <input id="search_box" placeholder="Enter search keywords...">
                <img src="./img/search.svg" id="search_btn" style="position:absolute;top:20px;width:1.5em;padding-left:0.5em;">
            </p>
            </div>

            <div id="ask_question">
            <a class="btn" style="margin-right:0;font-size:1.2em;" href="./q_form.php?u='.$user_name.'">
            Ask a question.
        </a>
                </div>

            <div id="ask_question">
            <a class="btn" style="margin-right:0;font-size:1.2em;background:#86b2ea;" href="./new_discussion.php?u='.$user_name.'">
            New discussion.
        </a>
                </div>
        </div>

        <div id="help_area">

        <!--
            <div class="help_content">
                <span class="title">result 1</span>
                <img src="./img/close.svg" class="open">
                <div class="close_hide" style="display:block !important;">
                    <div class="description">
                        (description of this)<br>
                        markdown
                    </div>
                    <div class="more"></div>
                </div>
            </div>

                -->

        </div>

        <div class="discussion_text">discussion:</div>

        <div id="discussion_area">

        </div>

        ');
    }
    ?>

        <script>
            <?php
            if($user_found === false){
                //userが存在しなかったら空
                echo('const user_name = "";');
            }else{
                //存在したら変数に追加
                echo('const user_name = "'.$user_name.'";');
            }

            //ログイン状態
            if(isset($_SESSION['id'])){
                echo('const user_id = "'.h($_SESSION['id']).'";');
            }else{
                echo('const user_id = "";');
            }

            if(!isset($_SESSION["csrf_token"])){
                // ランダムな英数字を生成し、それをcsrf_tokenにする
                $str = '1234567890abcdefghijklmnopqrstuvwxyz';
                $csrf_token = substr(str_shuffle($str), 0, 10);
              
              
                $_SESSION["csrf_token"] = $csrf_token;
              }else{
                $csrf_token = $_SESSION["csrf_token"];
              }

            ?>
            const csrf_token = <?php echo('"'.$csrf_token.'"') ?>
        </script>
        <script src="main.js"></script>
    </body>
</html>