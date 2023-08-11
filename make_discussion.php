<?php 
        function html_escape($text){
          $allow_tags = ["a","b","br","font","p","img","iframe"];
          $escape_text = str_replace('<', '&lt;',$text);

          foreach($allow_tags as $tag){
            $escape_text = str_replace('&lt;'.$tag,'<'.$tag,$escape_text);
            $escape_text = str_replace('&lt;/'.$tag.'>','</'.$tag.'>',$escape_text);
          }

          return $escape_text;
        }

        ?>

<?php
session_start();

include("./sql_info.php");

//$root_url = "http://localhost/pages/easelp";
$discord_api_url = "http://localhost:6000/send/";


if(isset($_POST["title"]) && isset($_POST["comment"]) && isset($_POST["user"])){

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

    $user_id = $_POST["user"];

    if(!preg_match("/^[0-9A-Za-z]+$/", $user_id)){
        exit();
    }

    $title = str_replace('<', '&lt;',$_POST["title"]);  //title
    $comment = $_POST["comment"]; //内容

    if($comment === ""){
        $comment = "No comment";
    }

    $file_id = "";

    $image_color = "ff9090";

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
        exit();
    }

    //ファイルを作成
    $file_handle = fopen( "./data/".$file_id.".md", "w");
    fwrite( $file_handle,$image_color.'($split)thread owner($split)'.str_replace("\n",'($n)',html_escape($comment))."\n");
    fclose($file_handle);

    //ファイルを検索できるようにする
    $filename = './discussion/'.$user_id.'.csv';
    $fp = fopen($filename, 'a');

    $title = str_replace('($split)','split','#discussion: '.$title);
    $begin_part = str_replace('($split)','',substr($comment,0,60)); //($split)が入力されてはまずいので、消す //冒頭部分
    $begin_part = str_replace("\n",' ',$begin_part);

    $add_data = "\n".$title.'($split)'.$file_id.'($split)'.$begin_part;
    fputs($fp, $add_data);
    fclose($fp);

    echo($user_id.",".$file_id);

    if(isset($_COOKIE["owner_list"])){
        setcookie("owner_list",$_COOKIE["owner_list"].",".$file_id);
    }else{
        setcookie("owner_list",$file_id);
    }

    //file_get_contents($discord_api_url."?url=".urlencode("A new discussion: \n".$root_url."/discussion.php?u=".$user_id."&fid=".$file_id)."&user=".$user_id);

}

?>