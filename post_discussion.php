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


if(isset($_POST["md"]) && isset($_POST["file_id"]) && isset($_POST["user"])){

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

    $user_comment_id = "";

    if(!isset($_COOKIE["user_comment_id"])){
        // ランダムな英数字を生成し、それをuser_comment_idにする
        $str = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $user_comment_id = substr(str_shuffle($str), 0, 10);

        setcookie("user_comment_id",$user_comment_id);
    }else{
        $user_comment_id = $_COOKIE["user_comment_id"];
    }


    $user_name = $_POST["user"];

    $user_id = str_replace('.', '', $user_id);
    $user_id = str_replace('/', '', $user_id);

    $comment = $_POST["md"]; //内容
    $file_id = $_POST["file_id"]; //file id

    $comment = html_escape($comment);


    //保存に成功したか
    $file_save_succeed = false;

    $lines = file("./discussion/".$user_name.".csv");


    $image_color = "9090ff";
    $user_comment_name = 'talker: '.$user_comment_id;

    //運営の色を変える
    if(isset($_SESSION["id"])){
        if($_SESSION["id"] === $user_name){
            $image_color = "90ff90";
        }
    }

    //スレ主の色を変える
    if(isset($_COOKIE["owner_list"])){
        $data = explode(',',$_COOKIE["owner_list"]);

        foreach($data as $line){
            if($line === $file_id){
                $image_color = "ff9090";
                $user_comment_name = 'thread owner: '.$user_comment_id;
            }
        }
    }

    if(isset($_SESSION["id"])){
        $user_comment_name =  html_escape($_SESSION["id"]).' : '.$user_comment_id;
    }

    foreach($lines as $line){
      $data = explode('($split)',$line);

      if(count($data) >= 3){
        if($data[1] === $file_id){

            //ファイルに回答を追加
            $add_content = $image_color.'($split)'.str_replace("<",'&lt;',$user_comment_name).'($split)'.str_replace("\n",'($n)',html_escape($comment))."\n";

            $file_handle = fopen( "./data/".$file_id.".md", "a");
            fputs($file_handle,$add_content);
            fclose($file_handle);

        }
      }
    }

}


/*           <div class="post_box">
            <div class="user_icon" style="background:#'.$image_color.';"></div>
            <div class="post_main">
                <div class="u">
                </div>
                <div class="text">
                '.str_replace('<', '&lt;',$comment).'
                </div>
            </div>
        </div>
 */
?>