<?php
session_start();


if(isset($_POST["md"]) && isset($_POST["file_id"]) && isset($_SESSION["id"])){

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

    $md_answer = $_POST["md"]; //内容
    $file_id = $_POST["file_id"]; //file id


    //保存に成功したか
    $file_save_succeed = false;

    $lines = file("./question/".$user_id.".csv");

    $all_content = "";

    foreach($lines as $line){
      $data = explode('($split)',$line);

      if(count($data) >= 3){
        if($data[1] === $file_id){ //そのファイルが今編集している人に作られたなら

            //ファイルに回答を追加
            $add_content = "\n\n<br><br><p class='answer_title'>answer:</p><br><br>".$md_answer;

            $file_handle = fopen( "./data/".$file_id.".md", "a");
            fputs($file_handle,$add_content);
            fclose($file_handle);

            $file_save_succeed = true;

            $all_content .= $data[0].'($split)'.$data[1].'($split)'.$data[2].'($split)1'."\n" ;
        }else{
            $all_content .= $line;
        }
      }
    }

    $filename = './question/'.$user_id.'.csv';
    $fp = fopen($filename, 'w');
    fputs($fp, $all_content);
    fclose($fp);

    if($file_save_succeed == false){
        http_response_code(403);
    }

    //$root_url = "http://localhost/pages/easelp/";

    include("./sql_info.php");

    $file_contents = '<?xml version="1.0" encoding="UTF-8" ?>'."\n".'<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
    $file_contents .= '<url><loc>'.$root_url.'view.php?u='.$user_id.'&fid='.$file_id.'</loc></url>'."\n";
    $file_contents .= '</urlset>';

    $file_contents = str_replace("&","&amp;",$file_contents);

    $sitemap_name = './sitemap/'.$user_id.'.xml';
    $fp = fopen($sitemap_name, 'w');
    fputs($fp, $file_contents);
    fclose($fp);

    //file_get_contents("https://www.google.com/ping?sitemap=".urlencode($root_url."sitemap/".$user_id."xml"));


}

?>