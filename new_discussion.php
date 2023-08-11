<?php

session_start();

function h($s){
    return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
  }

if(isset($_GET["u"])){
    $user_name = $_GET["u"];

}


?>


<html>
    <head>
        <meta charset="UTF-8">
        <title>
            New discussion - easelp
        </title>
        <link rel="stylesheet" href="./css/style.css">
        <link rel="stylesheet" href="./css/view.css">
        <link rel="stylesheet" href="./css/edit.css">

        <link rel="preconnect" href="https://rsms.me/">
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
        <link rel="preconnect" href="https://fonts.googleapis.com/">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Quicksand:300,400,500,700">

        <link rel="icon" type="image/svg+xml" href="./logo.svg" />

        <meta name="viewport" content="width=device-width,initial-scale=1">

        <link rel="stylesheet" href="./css/discussion.css">
        <link rel="stylesheet" href="./css/edit.css">

        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/a11y-dark.min.css">
        <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
    </head>

    <body>
      <header>
      <?php include("./header.php"); ?>
    </header>

    <div id="main_discussion">
        <span style="font-size:2em;">#discussion: </span>
        <input type="text" id="title_input_discuss" placeholder="title">

        <div class="btn" id="make_discussion" style="color:#fafaff">
            make
        </div>

        <div id="edit_box" style="margin-top:10vh;">
    <div class="edit_head">
      <div class="title">
          <span style="font-size:1.3vw;">first comment</span>

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
        </div>
    </div>
    <textarea id="editor_main" style="height: 20vh !important;"></textarea>
  </div>
    </div>
    

    <?php 
    // ランダムな英数字を生成し、それをcsrf_tokenにする
    $str = '1234567890abcdefghijklmnopqrstuvwxyz';
    $csrf_token = substr(str_shuffle($str), 0, 10);


    $_SESSION["csrf_token"] = $csrf_token;

    ?>

    <script>
      const user_name =  <?php echo('"'.h($user_name).'"'); ?>;

      const csrf_token = <?php echo('"'.$csrf_token.'"') ?>
    </script>

    <script src="./js/editor.js"></script>
    <script src="./js/discussion.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <script>hljs.highlightAll();</script>
    </body>
</html>