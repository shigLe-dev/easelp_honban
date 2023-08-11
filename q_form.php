
<?php

session_start();

function h($s){
    return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
  }



//ファイルがあるなら編集
$file_title = "";
$file_content = "";

$file_id = "";


if(isset($_GET["u"]) && isset($_GET["fid"])){

  $file_id = $_GET["fid"];
  $user = $_GET["u"];

  $lines = file("./search/".$user.".csv");

  foreach($lines as $line){
    $data = explode('($split)',$line);

    if(count($data) == 3){
      if($data[1] === $file_id){
        $file_title = $data[0];
        break;
      }
    }
  }

  $file_lines = file("./data/".$file_id.".md");

  foreach($file_lines as $line){
    $file_content .= $line;
  }

}else{
  //なかったら新規作成
  $file_title = "";
  $file_content = "";
}



if(!isset($_SESSION["csrf_token"])){
  // ランダムな英数字を生成し、それをcsrf_tokenにする
  $str = '1234567890abcdefghijklmnopqrstuvwxyz';
  $csrf_token = substr(str_shuffle($str), 0, 10);


  $_SESSION["csrf_token"] = $csrf_token;
}else{
  $csrf_token = $_SESSION["csrf_token"];
}


$user_name = "";

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
    


?>




<html>
    <head>
        <meta charset="UTF-8">
        <title>question: <?php 
                    //ユーザー名を表示
                    if($user_found === false){
                        //userが存在しなかったら
                        echo('Not Not found this user');
                    }else{
                        //存在したら
                        echo($user_name);
                    }?> - easeLp</title>
        <link rel="stylesheet" href="./css/style.css">
        <link rel="stylesheet" href="./css/edit.css">

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
      <header>
      <?php include("./header.php"); ?>
    </header>
    
    <div class="editor_contents">
      <input id="title_input" type="text" placeholder="here your question.">

      <div class="edit_preview">
        <div id="edit_box">
          <div class="edit_head">
            <div class="title">
                <span style="font-size:1.3vw;">markdown</span>

                <img class="menu" src="./img/code.svg" onclick="add_md('code')">
                <img class="menu" src="./img/color.svg" onclick="add_md('color')">

                <span class="menu" id="image_upload_back">
                  <form method="post" enctype="multipart/form-data" id="up_form">
                    <input type="file" name="file1" accept=".jpg, .png, .gif, .bmp, .svg" class="menu" id="image_upload">
                    <input type="submit" style="display:none;" id="submit_image">
                  </form>
                </span>

                <img class="menu" src="./img/link.svg" onclick="add_md('link')">
                <span class="menu" onclick="add_md('h2')">h2</span>
                <span class="menu" onclick="add_md('h1')">h1</span>
                <span id="post_btn" class="save_btn">post</span>
              </div>
          </div>

          <textarea id="editor_main"></textarea>
        </div>

        <div id="preview_box">
          <div class="edit_head">
            <div class="title">
              <span style="font-size:1.3vw;">html preview</span>
              <span class="menu" style="margin-right:1em;">help</span>
            </div>
          </div>
          <div id="preview_html"></div>
        </div>
      </div>
      </div>
    </div>


    <div id="saved_window">
      saved!
  </div>

  <div id="save_error_window">
      save error
  </div>


    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <script>
      const get_file_id = <?php echo('"'.h($file_id).'"'); ?>;
      const file_content = <?php echo('`'.h(str_replace('`','\`',$file_content)).'`'); ?>;
      const file_title =  <?php echo('"'.h($file_title).'"'); ?>;

      const csrf_token = <?php echo('"'.$csrf_token.'"') ?>;

      const user_name = <?php echo('"'.$user_name.'"') ?>;

    </script>
    <script src="./js/editor.js"></script>
    <script src="./js/q_form.js"></script>
    </body>
</html>