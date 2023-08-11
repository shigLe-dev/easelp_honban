<?php

include("./sql_info.php");

session_start();

if(isset($_POST["user_id"]) && isset($_POST["password"])){

    $user_id = $_POST["user_id"];
    $password = $_POST["password"];


    if(!preg_match("/^[0-9A-Za-z]+$/", $user_id)){
        echo("<html><head><meta http-equiv='refresh' content='3;URL=./signup_form.php' /></head>");
        echo("<span style='color:#ff0000;'>You can't include mark in User ID.</span><br>");
        echo("Redirect to the login page 3 seconds later....</html>");
        exit();
    }


    try{
        $dbh = new PDO($dsn, $sql_user, $sql_password);

        $result = $dbh->query("select password from users where id='".$user_id."';");

        foreach($result as $row){
            if(password_verify($password,$row["password"])){
                $_SESSION['id'] = $user_id;
                
                //dashboardにリダイレクト
                echo("<html><head><meta http-equiv='refresh' content='0;URL=./dash.php' /></head><body>Redirect to the dashboard.</body></html>");
                exit();
            }
        }
        

    }catch (PDOException $e){
        print('Error:'.$e->getMessage());
        die();
    }


    $accounts = file("./account.csv");

    foreach($accounts as $line){
        $data = explode(',',$line);

        //パスワードidが同じなら
        if($data[0] === $user_id){
            if(password_verify($password,str_replace("\n","",$data[1]))){
                $_SESSION['id'] = $user_id;

                //dashboardにリダイレクト
                echo("<html><head><meta http-equiv='refresh' content='0;URL=./dash.php' /></head><body>Redirect to the dashboard.</body></html>");
                exit();
            }
        }
    }

    echo("<html><head><meta http-equiv='refresh' content='3;URL=./login_form.php' /></head>");
    echo("<span style='color:#ff0000;'>Login Error.</span><br>");
    echo("Redirect to the login page 3 seconds later....</html>");
    exit();
}

?>