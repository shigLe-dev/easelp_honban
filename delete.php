<?php
session_start();

if(isset($_GET["file_id"]) && isset($_GET["kind"]) && isset($_SESSION["id"])){

    $user_id = $_SESSION["id"];

    $file_id = $_GET["file_id"]; //file id

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


    $all_content = "";

    $file_delete = false;

    if($_GET["kind"] == "doc"){
      $search_folder = "search";
    }elseif($_GET["kind"] == "question"){
      $search_folder = "question";
    }elseif($_GET["kind"] == "discussion"){
      $search_folder = "discussion";
    }

    //検索結果を更新
    //削除するファイルの情報を消す
    $lines = file("./".$search_folder."/".$user_id.".csv");

    foreach($lines as $line){
      $data = explode('($split)',$line);
      
      if(count($data) == 3){
        if($data[1] === $file_id){
          //ファイル削除
          unlink("./data/".$file_id.".md");
          $file_delete = true;
        }else{
            $all_content .= $line;
        }
      }
    }

    $filename = './'.$search_folder.'/'.$user_id.'.csv';
    $fp = fopen($filename, 'w');
    fputs($fp, $all_content);
    fclose($fp);

    if($file_delete == false){
      http_response_code(403);
    }

}

?>