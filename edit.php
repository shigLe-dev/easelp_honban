
<?php

session_start();

function h($s){
    return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
  }

if(!isset($_SESSION['id'])){
  header('Location: ./login_form.php');
  exit();
}



//ファイルがあるなら編集
$file_title = "";
$file_content = "";

$file_id = "";


if(isset($_GET["u"]) && isset($_GET["fid"])){

  $file_id = $_GET["fid"];
  $user = $_GET["u"];

  if(!preg_match("/^[0-9A-Za-z]+$/", $user)){
    exit();
  }

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

  if($file_title === ""){
    echo("404 Not found :(");
    exit();
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


?>




<html>
    <head>
        <meta charset="UTF-8">
        <title>easeLp - editor</title>
        <link rel="stylesheet" href="./css/style.css">
        <link rel="stylesheet" href="./css/edit.css">
        <link rel="stylesheet" href="./css/modal.css">
        <link rel="stylesheet" href="./css/load_anime.css">

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
      <input id="title_input" type="text" placeholder="here title.">

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
                <span id="save_btn" class="save_btn" href="./save.php">save</span>
                <span id="delete_btn"  href="./delete.php">delete</span>
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

  <a id="gpt_help_button" href="#modal">
    <div id="gpt_help_svg">
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

          <h2>Chat-GPT writing assistant</h2>
          <br><br>
          Please write in bullet points what you want to write in the document.<br>
          <textarea id="gpt_write_inp" rows="8"></textarea>
          <br><br>
          <div class="btn" onclick="gpt_write_send();" style="font-size:1.2em;">Generate</div>
          <br><br>
          words: <input id="word_num" type="number" value="50">
          <br>
          <br>
          copy and paste this to your editor.
          <textarea id="gpt_write_out" rows="8"></textarea>
          <br><br>

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

    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <script>
      const get_file_id = <?php echo('"'.h($file_id).'"'); ?>;
      const file_content = <?php echo('`'.h(str_replace('`','\`',$file_content)).'`'); ?>;
      const file_title =  <?php echo('"'.h($file_title).'"'); ?>;

      const csrf_token = <?php echo('"'.$csrf_token.'"') ?>
    </script>
    <script src="./js/editor.js"></script>
    </body>
</html>