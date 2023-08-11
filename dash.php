
<?php

session_start();

function h($s){
    return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
}

$user_name = "";
$user_id = "";

if(isset($_SESSION['id'])){
  $user_name = $_SESSION['id'];
  $user_id = $_SESSION['id'];
}else{
  header('Location: ./login_form.php');
  exit();
}  


$webhook_url = "";

$lines_setting = file("./setting/".$user_id.".csv");

$gpt_org = "";
$gpt_key = "";

foreach($lines_setting as $line){
  $data = explode(',',$line);

  $data[1] = str_replace("\n","",$data[1]);
  
  if($data[0] === "webhook"){
    $webhook_url = $data[1];
  }else if($data[0] === "visibility"){
    $post_visibility = $data[1];
  }else if($data[0] == "allow_u"){
    $allow_u = str_replace('"','',str_replace("<","",$data[1]));
  }else if($data[0] == "gpt_org"){
    $gpt_org = $data[1];
  }else if($data[0] == "gpt_key"){
    $gpt_key = $data[1];
  }
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
        <title>easeLp - dashboard</title>
        <link rel="stylesheet" href="./css/style.css">
        <link rel="stylesheet" href="./css/dash.css">

        <link rel="preconnect" href="https://rsms.me/">
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
        <link rel="preconnect" href="https://fonts.googleapis.com/">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Quicksand:300,400,500,700">

        <link rel="icon" type="image/svg+xml" href="./logo.svg" />
        
        <meta name="viewport" content="width=device-width,initial-scale=1">
        
    </head>

    <body>
      <header>
      <?php include("./header.php"); ?>
        <!--<a href="#" class="login" style="margin-right:5em;">Logout</a>-->
    </header>

    <div id="dash_main_area">
      <ul id="user_info">
        <div class="user_info_content">
          <span class="menu_select_p">menu</span>
            <div id="your_post" class="info_choose choose_check chosen_box">your post</div>
            <div id="question_list" class="info_choose choose_check">question</div>
            <div id="discussion_list" class="info_choose choose_check">discussion</div>
            <div id="import_md" class="info_choose choose_check">import docs</div>
            <div id="post_visibility" class="info_choose choose_check">visibility</div>
            <div id="chat_gpt" class="info_choose choose_check">chat gpt</div>
            <div id="page_url" class="info_choose choose_check">other</div>
            <br>
            <a id="new_post" href="edit.php">New post</a>
        </div>
      </ul>

      <ul>


        <div class="choose_bar">
        <span id="tab_title">your post</span>
          <div id="choose_box_list">
          <span id="your_post" class="choose_box choose_check">your post</span>
          <span id="question_list" class="choose_box choose_check">question</span>
          <span id="page_url" class="choose_box choose_check">other</span>
          </div>
        </div>
        <div class="post_content">
          <div id="post_main">
            
          </div>

          <div id="import_md_area" class="dash_setting">
            <h2>Import docs from Github.</h2>
            <br><br>
            Github user name
            <br>
            <input type="text" id="github_user">
            <br>
            repository name
            <br>
            <input type="text" id="github_repo">
            <br>
            <div class="btn" style="font-size:1em;" onclick="import_github()">import</div>
            <br><br>
            <h3>Markdown files and titles</h3>
            <div id="md_name_list">

            </div>

            <div class="btn" style="font-size:1em;" onclick="save_github()">save</div>
          </div>


          <div id="page_url_area" class="dash_setting">
            <h2>Embed your help site in the homepage.</h2>
            <p>copy and paste this code in your html.</p>
            <input type="text" value='<script>let user_name = "<?php echo($user_id); ?>";</script>
    <script src="<?php echo($root_url); ?>/stream/embed.js"></script>' >
            <br>
            <a class="btn" style="font-size:1em;color:#ffffff;" href="./stream/example.html">demo</a>
            <br>
            <h2>Webhook</h2>
            <input type="text" id="webhook_url" value="<?php echo($webhook_url); ?>" style="margin-bottom:0;" placeholder="here webhook url.">
            <div id="webhook_save" class="btn" style="font-size:1em;">save</div>
            <h2>Your Help site:</h2>
            <a href="<?php echo($root_url."/help.php?u=".$user_id) ?>">access</a><br>
            <input value="<?php echo($root_url."/help.php?u=".$user_id) ?>">
            <h2>Your Chat bot:</h2>
            <a href="<?php echo($root_url."/help.php?u=".$user_id) ?>">access</a><br>
            <input value="<?php echo($root_url."/chat.php?u=".$user_id) ?>">

          </div>

          <div id="set_visible_area" class="dash_setting">
            <h2>Post visibility setting</h2>
            <?php

            if($post_visibility === "1"){
              echo('<div id="change_v" class="btn" value="0" style="font-size:1.5em;">Public</div><br><br>');
              echo('anyone can see your posts.<br>');
            }else{
              echo('<div id="change_v" class="btn" value="1" style="font-size:1.5em;background:#888888">Private</div><br><br>');
              echo('specific users can access to your posts.<br> ');
            }

            ?>

            <br><br>

            <?php 
            if($post_visibility !== "1"){
              echo('            <h2>users that\'s allowed access to your posts.</h2>
              <br>
              <div id="allow_user_area">
              <!--
                <div class="allowed_user">test<div class="del">×</div></div>
              -->
  
              </div>
              <br><br>
              here user id you want to allow access.<br>
              <input type="text" id="allow_u_box" style="margin-bottom:0;">
              <div id="allow_u_add" class="btn" style="font-size:1em;">add</div>');
            }
            ?>
          </div>


          <div id="chat_gpt_setting" class="dash_setting">
            <h2>Chat-GPT setting.</h2>
            <br>
            Enter your organization ID.<br>
            <input type="text" id="gpt_org" value="<?php echo($gpt_org) ?>" ><br>
            Enter your Chat-GPT API key.<br>
            <input type="password" id="gpt_key" value="<?php echo($gpt_key) ?>" ><br>
            <div class="btn" id="gpt_save" style="font-size:1em;">save</div>
            <br>

          </div>


      </ul>
    </div>
    
    
    <script>
            <?php
            echo('const user_name = "'.$user_name.'";');
            echo('const user_id = "'.$user_id.'";');
            echo('const csrf_token = "'.$csrf_token.'";');
            echo('const allow_u = "'.$allow_u.'"');
            ?>
    </script>
          
    <script src="./js/dash.js"></script>

    </body>
</html>