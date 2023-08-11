<?php

if(isset($_POST["md"])){
    $url = "http://localhost:3000/summarize";

    $data = array(
        'md' => $_POST["md"]
    );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if ($response === false) {
        // エラーハンドリング
        echo("500 Error");
    } else {
        // レスポンスを処理
        echo($response);
    }

    curl_close($ch);
}

?>