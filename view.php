
<?php

session_start();

function h($s){
    return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
  }

$file_title = "";
$file_content = "";


$question_check = false;

$title_find = false;

//titleを探す
if(isset($_GET["u"]) && isset($_GET["fid"])){

  $file_id = $_GET["fid"];
  $user = $_GET["u"];

  $lines = file("./search/".$user.".csv");

  foreach($lines as $line){
    $data = explode('($split)',$line);

    if(count($data) == 3){
      if($data[1] === $file_id){
        $file_title = $data[0];
        $title_find = true;
      }
    }
  }

  $lines_question = file("./question/".$user.".csv");

  foreach($lines_question as $line){
    $data = explode('($split)',$line);

    if(count($data) >= 3){
      if($data[1] === $file_id){
        $question_check = true;
        $file_title = $data[0];
        $title_find = true;
      }
    }
  }

  if($title_find === false){
    echo("404 Not found:(");
    exit();
  }

  $lines_question = file("./discussion/".$user.".csv");

  foreach($lines_question as $line){
    $data = explode('($split)',$line);

    if(count($data) >= 3){
      if($data[1] === $file_id){
        header("Location: ./discussion.php?u=".$user."&fid=".$file_id);
      }
    }
  }

  $file_lines = file("./data/".$file_id.".md");

  foreach($file_lines as $line){
    $file_content .= $line;
  }

}else{
  $file_title = "Not found :(";
  echo("404 Not found :(");
  http_response_code(404);
  exit();
}

?>

<?php 
        function html_escape($text){
          $allow_tags = ["a","b","br","font","p","img","iframe","h1","h2","h3"];
          $escape_text = str_replace('<', '&lt;',$text);

          foreach($allow_tags as $tag){
            $escape_text = str_replace('&lt;'.$tag,'<'.$tag,$escape_text);
            $escape_text = str_replace('&lt;/'.$tag.'>','</'.$tag.'>',$escape_text);
          }

          return $escape_text;
        }

        ?>


<html>
    <head>
        <meta charset="UTF-8">
        <title>
          <?php
          if(isset($_GET["u"])){
            echo(h($_GET["u"]).': '.html_escape($file_title)."- easeLp");
          }
          ?>
        </title>
        <link rel="stylesheet" href="./css/style.css">
        <link rel="stylesheet" href="./css/view.css">
        <link rel="stylesheet" href="./css/edit.css">

        <link rel="preconnect" href="https://rsms.me/">
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
        <link rel="preconnect" href="https://fonts.googleapis.com/">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Quicksand:300,400,500,700">

        <?php
        echo('<meta property="og:title" content="'.h($_GET["u"]).': '.html_escape($file_title)."- easeLp".'">');
        echo('<meta property="og:description" content="'.str_replace('"','',str_replace("<","",substr($file_content,0,60))).'">');
        ?>

        <link rel="icon" type="image/svg+xml" href="./logo.svg" />

        <meta name="viewport" content="width=device-width,initial-scale=1">

        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/a11y-dark.min.css">
        <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
    </head>

    <body>
      <header>
      <?php include("./header.php"); ?>
    </header>

    <div style="display:flex;width:90vw;">

    <div class="preview_main">

    <?php
    if(isset($_SESSION["id"])){
      if($_GET["u"] === $_SESSION["id"]){
        echo('<a href="./edit.php?u='.h($_GET["u"]).'&fid='.h($_GET["fid"]).'" style="color:#888888;margin-right:2em;text-decoration:none;">edit</a>');
        }
    }

    if($_GET["u"]){
      echo('<a href="./help.php?u='.h($_GET["u"]).'" style="color:#888888;text-decoration:none;">'.h($_GET["u"]).' - help</a>');
    }

    ?>

      <h1 id="title">
        <?php echo(html_escape($file_title)) ?>
      </h1>

      <div id="preview_html_content"><?php echo(html_escape($file_content)) ?></div>

      <div class="sns_share">
        <?php 
        $this_url = $root_url.'/view.php?u='.h($_GET["u"]).'&fid='.h($_GET["fid"]);
        ?>
        <a href="https://twitter.com/share?url=<?php echo(urlencode($this_url).'&text='.$file_title) ?>" target="_blank">
          <svg xmlns="http://www.w3.org/2000/svg" id="twitter" class="sns_btn" viewBox="0 0 64 64" stroke-width="0" fill="#000000" stroke="#000000" width="2em"><path d="M54.49,12.3c0-.1-.09-.16-.17-.09-1.57,1.36-5.36,2.46-5.84,2.51a.11.11,0,0,1-.09,0c-2.78-4.44-9.19-3.24-9.19-3.24C29.78,13.48,30.82,23,31,24c0,.05,0,.09-.09.09-10.48.52-19.63-9.22-20.67-10.37a.11.11,0,0,0-.17,0A10.57,10.57,0,0,0,12.78,27a.11.11,0,0,1,0,.19,12.87,12.87,0,0,1-4-.77c-.06,0-.13,0-.13.1.14,6.2,6.22,9,7.63,9.59a.1.1,0,0,1,0,.19,13.4,13.4,0,0,1-3.85.27.11.11,0,0,0-.11.14c1.27,4.78,7.5,6.78,8.62,7.11A.11.11,0,0,1,21,44c-3.85,3.44-11.44,4.35-13,4.51a.11.11,0,0,0-.06.19c5.82,4,21.06,7.32,32.7-2.63A30.3,30.3,0,0,0,51,21.83a.09.09,0,0,1,.05-.08,14.22,14.22,0,0,0,5.06-5.06c0-.1,0-.16-.15-.13a5.63,5.63,0,0,1-3.15.17S54.52,13.77,54.49,12.3Z" stroke-linecap="round"/></svg>
        </a>
        <a href="https://facebook.com/share.php?u=<?php echo(urlencode($this_url)) ?>" target="_blank">
          <svg xmlns="http://www.w3.org/2000/svg" id="facebook" class="sns_btn" viewBox="0 0 64 64" stroke-width="0" fill="#000000" stroke="#000000" width="2em"><path d="M37.49,19.86c0-.07.36-.3,1.51-.3h3.76A2.21,2.21,0,0,0,45,17.34h0V10.78a2.22,2.22,0,0,0-2.19-2.26h-5.6a11.47,11.47,0,0,0-8.43,3.28,12,12,0,0,0-3.19,8.64v5.95H21.24A2.23,2.23,0,0,0,19,28.61v7.07a2.21,2.21,0,0,0,2.21,2.21h4.28V53.24a2.22,2.22,0,0,0,2.18,2.25h7.35a2.21,2.21,0,0,0,2.21-2.22h0V37.94h5.23a2.23,2.23,0,0,0,2.22-2.22V28.64a2.21,2.21,0,0,0-2.22-2.21H37.26V21.22A2.44,2.44,0,0,1,37.49,19.86Z" stroke-linecap="round"/></svg>
        </a>
        <a href="https://linkedin.com/sharing/share-offsite/?url=<?php echo(urlencode($this_url)) ?>" target="_blank">
          <svg xmlns="http://www.w3.org/2000/svg" id="linkedin" class="sns_btn" viewBox="0 0 64 64" stroke-width="0" fill="#000000" stroke="#000000" width="2em"><path d="M34.34,24.1H25.65V55.53h9V41.13c0-3.82.72-7.49,5.43-7.49s4.72,4.34,4.72,7.73V55.53h9V39.46c0-7.76-1.65-14.92-10.81-14.92-3.38-.13-6.65.49-8.47,3.25a.11.11,0,0,1-.21-.06Z" stroke-linecap="round"/><path d="M10.92,24.1H20V55.53H10.92Z" stroke-linecap="round"/><path d="M15.44,8.47a5.27,5.27,0,1,0,5.25,5.29v0A5.25,5.25,0,0,0,15.44,8.47Z" stroke-linecap="round"/></svg>
        </a>

      </div>
    </div>

    <div id="side_bar">
    </div>
  </div>

    <?php 

if(isset($_SESSION["id"])){
  if($_GET["u"] === $_SESSION["id"] && $question_check === true){
    echo('
  <div id="a_form_div">
  <h1>answer</h1>

  <div id="edit_box">
    <div class="edit_head">
      <div class="title">
          <span style="font-size:1.3vw;">markdown</span>

          <img class="menu" src="./img/code.svg" onclick="add_md(`code`)">
          <img class="menu" src="./img/color.svg" onclick="add_md(`color`)">

          <span class="menu" id="image_upload_back">
            <form method="post" enctype="multipart/form-data" id="up_form">
              <input type="file" name="file1" accept=".jpg, .png, .gif, .bmp, .svg" class="menu" id="image_upload">
              <input type="submit" style="display:none;" id="submit_image">
            </form>
          </span>

          <img class="menu" src="./img/link.svg" onclick="add_md(`link`)">
          <span class="menu" onclick="add_md(`h2`)">h2</span>
          <span class="menu" onclick="add_md(`h1`)">h1</span>
          <span id="answer_btn" class="save_btn">answer</span>
        </div>
    </div>
    <textarea id="editor_main"></textarea>
  </div>
</div>
');
    }
}
    ?>
    
    <?php 

if(!isset($_SESSION["csrf_token"])){
  // ランダムな英数字を生成し、それをcsrf_tokenにする
  $str = '1234567890abcdefghijklmnopqrstuvwxyz';
  $csrf_token = substr(str_shuffle($str), 0, 10);


  $_SESSION["csrf_token"] = $csrf_token;
}else{
  $csrf_token = $_SESSION["csrf_token"];
}

    ?>

    <script>
      const get_file_id = <?php echo('"'.h($file_id).'"'); ?>;
      const user_name =  <?php echo('"'.h($user).'"'); ?>;

      const csrf_token = <?php echo('"'.$csrf_token.'"') ?>
    </script>

    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>


    <script src="./js/editor.js"></script>
    <script src="./js/view.js"></script>

    <script>hljs.highlightAll();</script>
    </body>
</html>