<?php


include("./sql_info.php");


$root_url = $root_url_header;

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


//private modeの場合はアクセスできないようにする
$lines_setting;

if($user_found){
    global $lines_setting;
    global $user_name;
    $lines_setting = file("./setting/".$user_name.".csv");

    foreach($lines_setting as $line){
      $data = explode(',',$line);

      $data[1] = str_replace("\n","",$data[1]);
      
      if($data[0] === "visibility"){
        if($data[1] === "0"){
            if(isset($_SESSION["id"])){
                if($_SESSION["id"] !== $user_name){
                    allow_user_check_func();
                }
            }else{
                $user_found = false;
                $user_name = "";
            }
        }
      }
    }
}

//このuserにアクセスできるuserかどうかを確認する
function allow_user_check_func(){
    global $user_name;
    global $lines_setting;
    global $user_found;

    $allow_user_check = false;

    foreach($lines_setting as $line){
      $data = explode(',',$line);

      $data[1] = str_replace("\n","",$data[1]);
      if($data[0] == "allow_u"){
        foreach(explode('($s)',$data[1]) as $user_line){
            if($_SESSION["id"] === $user_line){
                $allow_user_check = true;
            }
        }

        if($allow_user_check  === false){
            $user_found = false;
            $user_name = "";
        }
      }
    }
}


//ログイン状態と
if(isset($_SESSION['id'])){
    echo('<span class="logo_text">easeLp</span>');
    echo('<a class="dash menu" href="./dash.php" class="menu">dash</a>');
    echo('<a class="your_site menu" href="./help.php?u='.h($_SESSION['id']).'" class="menu">Your help site</a>');
    echo('<a class="chat menu" class="menu" href="./chat.php?u='.h($_SESSION['id']).'">Chat help</a>');
    echo('<a href="./help.php?u=easelp" id="help_btn" class="menu">help</a>');
    echo('<a href="./logout.php" class="login" style="margin-right:3em;">Logout</a>');
}else{
    echo('<span class="logo_text">easeLp</span>');
    if($user_found) echo('<a class="your_site menu" href="./help.php?u='.$user_name.'">top</a>');
    if($user_found) echo('<a class="chat menu" href="./chat.php?u='.$user_name.'">Chat help</a>');
    echo('<a href="./help.php?u=easelp" id="help_btn" class="menu">help</a>');
    echo('<a href="./login_form.php" class="login">login</a>');
    echo('<a id="sign_up_btn" href="./signup_form.php" class="login" style="margin-right:2em;">Sign up</a>');
}

?>