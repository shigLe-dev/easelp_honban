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

    if($user_found == false){
        echo("404 Not found.");
        exit();
    }
    
?>

<html>
    <head>
        <meta charset="UTF-8">
        <title>easeLp Help Chat - <?php 
                    //ユーザー名を表示
                    if($user_found == false){
                        //userが存在しなかったら
                        echo('Not Not found this user');
                    }else{
                        //存在したら
                        echo($user_name);
                    }?></title>
        <link rel="stylesheet" href="./css/style.css">
        <link rel="stylesheet" href="./css/chat.css">

        <link rel="preconnect" href="https://rsms.me/">
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
        <link rel="preconnect" href="https://fonts.googleapis.com/">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Quicksand:300,400,500,700">

        <link rel="icon" type="image/svg+xml" href="./logo.svg" />

        <meta name="viewport" content="width=device-width,initial-scale=1">

        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/a11y-dark.min.css">
        <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>

    </head>

    <body>
        <header>
            <?php include("./header.php"); ?>
        </header>

        <div id="chat_area">

        <?php
        

        if($user_found){
            echo('
                <div class="chat_title">Chat Help - '.$user_name.'</div>
    
                <div id="chat_main">
                    <div class="chat_text bot">
                        Hello,Do you have any questions about '.$user_name.'?<br>
                        words: <input id="word_num" type="number" value="50">
                    </div>
                </div>
    
                <div id="chat_textbox_block">
                    <input id="chat_textbox" placeholder="here your question.">
                </div>
            ');
        }else{
            echo('<div class="chat_title" style="color:#ff0055;font-weight:200;">404 Not found</div>');
        }

        ?>

</div>

        <script>
            <?php
            if($user_found == false){
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

            ?>
        </script>
        <script src="main.js"></script>
        <script src="js/chatbot.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    </body>
</html>