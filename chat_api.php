<?php

if(isset($_POST["user"]) && isset($_POST["q"]) && isset($_GET["mode"])){

    if($_GET["mode"] == "chat"){
        $url = "http://localhost:5000/chat";
    }elseif($_GET["mode"] == "write"){
        $url = "http://localhost:5000/mode/write";
    }elseif($_GET["mode"] == "talk"){
        $url = "http://localhost:5000/mode/talk";
    }

    $data = array(
        'user' => $_POST["user"],
        'q' => $_POST["q"]
    );

    if(isset($_POST["w"])){
        $data = array(
            'user' => $_POST["user"],
            'q' => $_POST["q"],
            'w' => $_POST["w"]
        );
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if ($response === false) {
        // error
        echo("500 Error");
    } else {
        // レスポンスを処理
        echo($response);
    }

    curl_close($ch);
}

?>