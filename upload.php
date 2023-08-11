<?php

$tempfile = $_FILES['file1']['tmp_name'];
$file_ext = pathinfo($_FILES['file1']['name'], PATHINFO_EXTENSION); //ファイルの拡張子
$file_path = "";

$accept_up = false; //アップロードが許可されているファイル拡張子か

$accept_ext = ["png","jpg","gif","bmp","svg"];

foreach($accept_ext as $ext){
    if($file_ext === $ext){
        $accept_up = true;
    }
}

//画像の拡張子でなければ、アップロードを許可しない
if($accept_up == false){
    http_response_code(500);
    echo("Error");
    exit();
}


// ランダムな英数字を生成し、それをfile_nameにする
for($i = 0;$i < 10;){
    $str = '1234567890abcdefghijklmnopqrstuvwxyz';
    $str_r = substr(str_shuffle($str), 0, 20);

    $file_path = './upload/'.$str_r.'.'.$file_ext;

    //ファイル名の重複を防ぐため、同じ名前のファイルが存在したらもう一度
    if(file_exists($file_path)){
        
    }else{
        break;
    }
}

if($file_path === ""){
    http_response_code(500);
    echo("Error");
    exit();
}

 
if (is_uploaded_file($tempfile)) {
    if ( move_uploaded_file($tempfile , $file_path )) {
        echo('./upload/'.$str_r.'.'.$file_ext);
    } else {
        http_response_code(500);
        echo("Error");
        exit();
    }
} else {
    http_response_code(500);
    echo("Error");
    exit();
} 
?>