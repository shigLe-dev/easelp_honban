
<?php
session_start();

function h($s){
    return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
  }

$file_title = "";
$file_content = "";

$title_find = false;

//titleを探す
if(isset($_GET["u"]) && isset($_GET["fid"])){

  $file_id = $_GET["fid"];
  $user = $_GET["u"];

  if(!preg_match("/^[0-9A-Za-z]+$/", $user)){
    exit();
}

  $lines = file("./discussion/".$user.".csv");

  foreach($lines as $line){
    $data = explode('($split)',$line);

    if(count($data) >= 3){
      if($data[1] === $file_id){
        $file_title = $data[0];
        $title_find = true;
      }
    }
  }

  if($title_find === false){
    echo("404 Not find :(");
    exit();
  }

  $file_lines = file("./data/".$file_id.".md");

  foreach($file_lines as $line){
    $html_list = explode('($split)',$line);
    if(count($html_list) >= 2){
      $file_content .= "\n".'<div class="post_box">
      <div class="user_icon" style="background:#'.$html_list[0].';"></div>
      <div class="post_main">
          <div class="u">
'.$html_list[1].'
          </div>
          <div class="text">
'.str_replace('($n)',"\n",$html_list[2]).'
          </div>
      </div>
  </div>';
    }
  }

}else{
  $file_title = "Not found :(";
  echo("404 Not found :(");
  http_response_code(404);
  exit();
}

?>


<html>
    <head>
        <meta charset="UTF-8">
        <title>
          <?php echo($file_title) ?>  - easelp
        </title>
        <link rel="stylesheet" href="./css/style.css">
        <link rel="stylesheet" href="./css/view.css">
        <link rel="stylesheet" href="./css/edit.css">
        <link rel="stylesheet" href="./css/modal.css">
        <link rel="stylesheet" href="./css/load_anime.css">

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
        <span style="font-size:2em;"><?php echo($file_title) ?></span>
        <div id="discussion_area">

        <!--
            <div class="post_box">
                <div class="user_icon" style="background:#ff9090;"></div>
                <div class="post_main">
                    <div class="u">
                        @user
                    </div>
                    <div class="text">comment</div>
                </div>
            </div>
        -->

        <?php echo($file_content) ?>


        </div>

        <div id="edit_box" style="margin-top:10vh;">
    <div class="edit_head">
      <div class="title">
          <span style="font-size:1.3vw;">markdown</span>

          <img class="menu" src="./img/code.svg" onclick="add_md(`code`)">

          <span class="menu" id="image_upload_back">
            <form method="post" enctype="multipart/form-data" id="up_form">
              <input type="file" name="file1" accept=".jpg, .png, .gif, .bmp, .svg" class="menu" id="image_upload">
              <input type="submit" style="display:none;" id="submit_image">
            </form>
          </span>

          <img class="menu" src="./img/link.svg" onclick="add_md(`link`)">
          <span class="menu" onclick="add_md(`h2`)">h2</span>
          <span class="menu" onclick="add_md(`h1`)">h1</span>
          <span id="answer_btn" class="save_btn">post</span>
        </div>
    </div>
    <textarea id="editor_main" style="height: 20vh !important;"></textarea>
  </div>
    </div>
    <a id="gpt_a_help_button" href="#modal">
    <div id="gpt_a_help_svg">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
</svg>
    </div>
</a>

    <div class="modal" id="modal">
    <a href="#!" class="overlay"></a>
    <div class="modal-wrapper">
      <div class="modal-contents">
        <a href="#!" class="modal-close">✕</a>
        <div class="modal-content">

        <div id="overlay_black"></div>
          <h2>Chat-GPT talking assistant</h2>
          <br><br>
          Please write in bullet points what you want to talk.<br>
          <textarea id="gpt_talk_inp" rows="8"></textarea>
          <br><br>
          words: <input id="word_num" type="number" value="50">
          <br><br>
          <div class="btn" onclick="gpt_talk_send();" style="font-size:1.2em;">Generate</div>
          
          <div id="assist_load_box">
          <div class="spinner-box-as">
            <div class="pulse-container">  
              <div class="pulse-bubble-as pulse-bubble-1"></div>
              <div class="pulse-bubble-as pulse-bubble-2"></div>
              <div class="pulse-bubble-as pulse-bubble-3"></div>
            </div>
          </div>
        </div>

        </div>
      </div>
    </div>
  </div>
    

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
    <script src="./js/discussion_view.js"></script>

    <script>hljs.highlightAll();</script>
    </body>
</html>