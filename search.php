<?php 
        function html_escape($text){
            $allow_tags = ["a","b","br","font","p","img","iframe"];
            $escape_text = str_replace('<', '&lt;',$text);
  
            foreach($allow_tags as $tag){
              $escape_text = str_replace('&lt;'.$tag,'<'.$tag,$escape_text);
              $escape_text = str_replace('&lt;/'.$tag.'>','</'.$tag.'>',$escape_text);
            }
  
            return $escape_text;
          }

        ?>

<?php

//header("Access-Control-Allow-Origin: *");
header('Content-Type: text/plain');

//ユーザー名、検索キーワードが入力されている
if(isset($_GET["u"]) && isset($_GET["s"])){
    $search_word = $_GET["s"]; //search keyword
    $user = $_GET["u"]; //user name

    if(!preg_match("/^[0-9A-Za-z]+$/", $user)){
        exit();
    }


    //すべて表示
    if($search_word === '($all_search$)'){
        $lines = file("./search/".$user.".csv");

        foreach($lines as $line){
            echo(html_escape($line));
        }
        exit();
        
    }elseif($search_word === '($some_question$)'){
        $lines = file("./search/".$user.".csv");

        $i = 0;

        foreach($lines as $line){
            if($i < 10){
                echo(html_escape($line));
                $i += 1;
            }else{
                break;
            }
        }
        exit();
    }
    elseif($search_word === '($all_question$)'){
        $lines = file("./question/".$user.".csv");

        foreach($lines as $line){
            echo(html_escape($line));
        }
        exit();
    }elseif($search_word === '($some_discussion$)'){
        $lines = file("./discussion/".$user.".csv");

        $i = 0;

        foreach($lines as $line){
            if($i < 10){
                echo(html_escape($line));
                $i += 1;
            }else{
                break;
            }
        }
        exit();
    }elseif($search_word === '($all_discussion$)'){
        $lines = file("./discussion/".$user.".csv");

        foreach($lines as $line){
            echo(html_escape($line));
        }
        exit();
    }


    if(isset($_GET["kind"])){

        $kind = $_GET["kind"];

        $lines = [];


        //キーワードから検索
        //search
        if($kind === "doc"){
            $lines = file("./search/".$user.".csv");
        }elseif($kind === "question"){
            $lines = file("./question/".$user.".csv");
        }elseif($kind === "discussion"){
            $lines = file("./discussion/".$user.".csv");
        }

        //question
        /*
        $lines = array_merge($lines,$lines_question);

        $lines = array_merge($lines,$lines_discussion);
        */

        //検索結果(csv) ($split)と言う文字列で区切るcsvファイル
        $results = "";
        $result_list = []; //検索結果のリスト
        $word_num = []; //その検索結果に対応している 検索結果に含まれていた検索した単語の数

        foreach($lines as $line){
            $data = explode('($split)',$line);

            $word_split = explode(" ",$search_word);

            //このタイトルに含まれている 検索した文に含まれる単語の数
            //e.x.
            //search:test text
            //title:testtextexample
            //タイトルに検索した単語が2つ含まれているので
            //this_word_num = 2

            $this_word_num = 0;

            foreach($word_split as $word){
                if($word != ""){
                    //titleに検索した文字が含まれていたら検索結果に追加
                    //data[0] → title
                    //大文字、小文字の区別をなくすためstrtolowerを入れる
                    if(strpos(strtolower($data[0]),strtolower($word)) !== false){
                        $this_word_num += 1;
                    }
                }
            }



            //検索した単語数が多く含まれる検索結果が上に来るようにする
            if($this_word_num != 0){
                if(count($word_num) != 0){
                    for($i = 0;$i < count($word_num);$i++){
                            //次に含まれてる単語数が少ないものの直前におく
                            //そうすることによって多い順に並べることができる
                            if($word_num[$i] < $this_word_num || $word_num[$i] == $this_word_num){
                                array_splice($result_list,$i,0,$line);
                                array_splice($word_num,$i,0,$this_word_num);
                                break;
                            }
                        }
                    }else{
                        //追加
                        array_push($result_list,$line);
                        array_push($word_num,$this_word_num);
                    }
                }
            }

        //追加
        foreach($result_list as $line){
            $results .= $line."\n";
        }

        //表示
        echo(html_escape($results));
    }
}

?>