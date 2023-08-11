<?php
session_start();


if(isset($_POST["title"]) && isset($_POST["md"]) && isset($_POST["file_id"]) && isset($_SESSION["id"])){

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

    $title = $_POST["title"];  //title
    $md_content = $_POST["md"]; //内容
    $file_id = $_POST["file_id"]; //file id

    //新規作成false
    $new_create = false;

    if($file_id === ""){
        $new_create = true;
    }

    if($file_id === ""){ //新しいファイル作成

        //新規作成モード
        $new_create = true;

        // ランダムな英数字を生成し、それをfile_idにする
        for($i = 0;$i < 10;){
            $str = '1234567890abcdefghijklmnopqrstuvwxyz';
            $str_r = substr(str_shuffle($str), 0, 20);

            //ファイル名の重複を防ぐため、同じ名前のファイルが存在したらもう一度
            if(file_exists($str_r)){

            }else{
                $file_id = $str_r;
                break;
            }
        }

        if($file_id === ""){
            echo("Error");
            http_response_code(500);
            exit();
        }
    }

    if($title === ""){
        $title = 'No title';
    }

    //新規作成ならファイルを新しく作る
    if($new_create){
        //ファイルを作成
        $file_handle = fopen( "./data/".$file_id.".md", "w");
        fwrite( $file_handle, $md_content);
        fclose($file_handle);

        //ファイルを検索できるようにする
        $filename = './search/'.$user_id.'.csv';
        $fp = fopen($filename, 'a');

        $title = str_replace('($split)','split',$title);
        $begin_part = str_replace('($split)','',substr($md_content,0,60)); //($split)が入力されてはまずいので、消す //冒頭部分
        $begin_part = str_replace("\n",' ',$begin_part);

        $add_data = "\n".$title.'($split)'.$file_id.'($split)'.$begin_part;
        fputs($fp, $add_data);
        fclose($fp);
    }else{

        //保存に成功したか
        $file_save_succeed = false;

        $all_content = "";

        //検索結果を更新
        $lines = file("./search/".$user_id.".csv");

        foreach($lines as $line){
          $data = explode('($split)',$line);
      
          if(count($data) == 3){
            if($data[1] === $file_id){ //そのファイルが今編集している人に作られたなら

                //ファイルを上書き保存
                $file_handle = fopen( "./data/".$file_id.".md", "w");
                fwrite( $file_handle, $md_content);
                fclose($file_handle);

                $title = str_replace('($split)','split',$title);
                $begin_part = str_replace('($split)','',substr($md_content,0,60)); //($split)が入力されてはまずいので、消す //冒頭部分
                $begin_part = str_replace("\n",' ',$begin_part);

              $all_content .= "\n".$title.'($split)'.$file_id.'($split)'.$begin_part ;

              $file_save_succeed = true;
            }else{
                $all_content .= "\n".$line;
            }
          }
        }

        $filename = './search/'.$user_id.'.csv';
        $fp = fopen($filename, 'w');
        fputs($fp, $all_content);
        fclose($fp);

        if($file_save_succeed == false){
            http_response_code(403);
            exit();
        }

    }

    //google search index

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