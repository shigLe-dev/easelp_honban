<?php

include("./sql_info.php");

if(isset($_POST["user_id"]) && isset($_POST["password"]) && isset($_POST["re_password"])){

    $user_id =  $_POST["user_id"]; //user id
    $password =  $_POST["password"]; //password
    $re_password =  $_POST["re_password"]; //the re-enterd password


    $accounts = file("./account.csv"); //account data

    //パスワードと再入力のものが違うならおしまい
    if($password !== $re_password){
        echo("<html><head><meta http-equiv='refresh' content='3;URL=./signup_form.php' /></head>");
        echo("<span style='color:#ff0000;'>Your password isn't same as re-enter password.</span><br>");
        echo("Redirect to the login page 3 seconds later....</html>");
        exit();
    }

    //user idに記号はダメ
    if(!preg_match("/^[0-9A-Za-z]+$/", $user_id)){
        echo("<html><head><meta http-equiv='refresh' content='3;URL=./signup_form.php' /></head>");
        echo("<span style='color:#ff0000;'>You can't include mark in User ID.</span><br>");
        echo("Redirect to the login page 3 seconds later....</html>");
        exit();
    }

    //user idの重複がないか調べる。重複があったらおしまい
    foreach($accounts as $line){
        $data = explode(',',$line);

        if($data[0] === $user_id){
            echo("<html><head><meta http-equiv='refresh' content='3;URL=./signup_form.php' /></head>");
            echo("<span style='color:#ff0000;'>Found the id that's same as yours.</span><br>");
            echo("<span style='color:#ff0000;'>You must enter a different id.</span><br>");
            echo("Redirect to the login page 3 seconds later....</html>");
            exit();
        }
    }

    //アカウント登録

    //mysql

    try{
        $dbh = new PDO($dsn, $sql_user, $sql_password);

        $hash = password_hash($password, \PASSWORD_BCRYPT);

        $result = $dbh->query("insert into users values ('".$user_id."','".$hash."');");
        

    }catch (PDOException $e){
        print('Error:'.$e->getMessage());
        die();
    }

    $dbh = null;


    //投稿検索情報のファイルを作成
    $file_handle = fopen( "./search/".$user_id.".csv", "w");
    fwrite( $file_handle, "");
    fclose($file_handle);

    //質問機能のファイルを作成
    $file_handle = fopen( "./question/".$user_id.".csv", "w");
    fwrite( $file_handle, "");
    fclose($file_handle);

    //質問機能のファイルを作成
    $file_handle = fopen( "./discussion/".$user_id.".csv", "w");
    fwrite( $file_handle, "");
    fclose($file_handle);

    //質問機能のファイルを作成
    $file_handle = fopen( "./setting/".$user_id.".csv", "w");
    fwrite( $file_handle, "webhook,0\nvisibility,1\nallow_u,\ngpt_org,\ngpt_key,\n");
    fclose($file_handle);

    //login
    $_SESSION['id'] = $user_id;

    //ログインページにリダイレクト
    echo("<html><head><meta http-equiv='refresh' content='0;URL=./login_form.php' /></head></html>");
}

?>