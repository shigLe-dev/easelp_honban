<?php 

session_start();

function h($s){
    return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
  }

$user_found = false; //ユーザーが見つかったか
//ユーザー名の存在を確認
if(isset($_GET["u"])){
    $user_name = $_GET["u"]; //search keyword
    $lines = file("./account.csv");
                        
    foreach($lines as $line){
        $data = explode(',',$line);
    
        //userが存在したらuser名を返す
        if($data[0] == $user_name){
            $user_found = true;
            break;
        }
    }
    }

    if($user_found == false){
        echo("404 Not found.");
        exit();
    }
    
?>

<?php include("./embed_visibility.php"); ?>

<html>
    <head>
        <meta charset="UTF-8">
        <title>easeLp Search - <?php 
                    //ユーザー名を表示
                    if($user_found == false){
                        //userが存在しなかったら
                        echo('Not Not found this user');
                    }else{
                        //存在したら
                        echo($user_name);
                    }?></title>
        <link rel="stylesheet" href="./css/style.css">
        <link rel="stylesheet" href="./css/embed.css">

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
        <div class="search_area_embed">
            <div style="font-size:2.2em;"><?php echo($user_name); ?>
                <a href="./embed.php?u=<?php echo($user_name); ?>"><img class="back_top" src="./img/chevron-left.svg"></a>
            </div>
            <div style="padding-top:6vh;">
                <a class="menu_click_box" href="./q_form.php?u=<?php echo($user_name); ?>">
                    Post your question
                </a>
                <a class="menu_click_box" href="./new_discussion.php?u=<?php echo($user_name); ?>">
                    Make a new discussion
                </a>
            </div>
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

            echo('const user_id = ""')

            ?>
        </script>
        <script src="main.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    </body>
</html>