<?php

$lines_setting;

if($user_found){
    global $lines_setting;
    global $user_name;
    $lines_setting = file("./setting/".$user_name.".csv");

    foreach($lines_setting as $line){
      $data = explode(',',$line);

      $data[1] = str_replace("\n","",$data[1]);
      
      if($data[0] === "visibility"){
        if($data[1] === "0"){
            if(isset($_SESSION["id"])){
                if($_SESSION["id"] !== $user_name){
                    allow_user_check_func();
                }
            }else{
                $user_found = false;
                $user_name = "";
            }
        }
      }
    }
}

function allow_user_check_func(){
    global $user_name;
    global $lines_setting;
    global $user_found;

    $allow_user_check = false;

    foreach($lines_setting as $line){
      $data = explode(',',$line);

      $data[1] = str_replace("\n","",$data[1]);
      if($data[0] == "allow_u"){
        foreach(explode('($s)',$data[1]) as $user_line){
            if($_SESSION["id"] === $user_line){
                $allow_user_check = true;
            }
        }

        if($allow_user_check  === false){
            $user_found = false;
            $user_name = "";
        }
      }
    }
}

if($user_found === false){
    echo("404 Not found :(");
    exit();
}

?>