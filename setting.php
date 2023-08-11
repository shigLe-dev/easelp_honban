<?php

session_start();

if(isset($_POST["content"]) && isset($_POST["name"]) && isset($_SESSION["id"]) && isset($_GET["csrf_token"])){

    if(isset($_GET["csrf_token"]) && isset($_SESSION["csrf_token"])){
        if($_GET["csrf_token"] !== $_SESSION["csrf_token"]){
            echo("403 Error");
            http_response_code(403);
            exit();
        }
    }else{
        echo("403 Error");
        http_response_code(403);
        exit();
    }

    $user_id = $_SESSION["id"];

    $name = $_POST["name"];
    $content = $_POST["content"];


    if(strpos($name,',') === true || strpos($name,"\n") === true){
        exit();
    }

    $all_content = "";

    //検索結果を更新
    $lines = file("./setting/".$user_id.".csv");

    foreach($lines as $line){
      $data = explode(',',$line);
      if($data[0] === $name){
          $all_content .= $name.",".$content."\n";
      }else{
          $all_content .= $line;
      }
    }

    $filename = './setting/'.$user_id.'.csv';
    $fp = fopen($filename, 'w');

    fputs($fp, $all_content);
    fclose($fp);

}

?>