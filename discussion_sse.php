<?php

if(isset($_GET["fid"]) && isset($_GET["user"])){
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-store');

    $file_id = $_GET["fid"];
    $user = $_GET["user"];
  
    $lines = file("./discussion/".$user.".csv");
  
    foreach($lines as $line){
      $data = explode('($split)',$line);
  
      if(count($data) >= 3){
        if($data[1] === $file_id){
            sse($file_id);
        }
      }
    }
}


function sse($file_id){

    $before_list_len = count(file("./data/".$file_id.".md"));

    while(true) {

        $lines = file("./data/".$file_id.".md");

        if(count($lines) !== $before_list_len){

            foreach($lines as $line){
                if($line !== ""){
                    $this_list = explode('($split)',$line);
                    $this_html = '<div class="post_box">
    <div class="user_icon" style="background:#'.$this_list[0].';"></div>
    <div class="post_main">
        <div class="u">
'.$this_list[1].'
        </div>
        <div class="text">
'.str_replace('($n)',"\n",$this_list[2]).'
        </div>
    </div>
</div>';
                }
                foreach(explode("\n",$this_html) as $html_line){
                    echo("data:".$html_line."\n\n");
                }
            }
            
            $before_list_len = count(file("./data/".$file_id.".md"));
        }else{
            echo("data:0\n\n");
        }

        //送信
        ob_end_flush();
        flush();
        sleep(0.7);
    }
}

?>