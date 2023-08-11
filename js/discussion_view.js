
let discussion_area = document.querySelector("#discussion_area")

let answer_btn = document.querySelector("#answer_btn")

if(answer_btn != undefined){
    answer_btn.addEventListener("click",answer_click,false)
}

put_reply()

md_parse()

function answer_click(){

    let md_text = document.querySelector("#editor_main").value

    var data = {md: md_text ,file_id: get_file_id ,user: user_name}; // POSTメソッドで送信するデータ


    var xmlHttpRequest = new XMLHttpRequest();
    xmlHttpRequest.onreadystatechange = function()
    {
        var READYSTATE_COMPLETED = 4;

        if( this.readyState == READYSTATE_COMPLETED && this.status == 200 )
        {
            // レスポンスの表示
            console.log( this.responseText );
            console.log("hoge")

            //location.reload()

            document.querySelector("#editor_main").value = ""
        }
    }

    xmlHttpRequest.open( 'POST', './post_discussion.php?csrf_token='+csrf_token);

    // サーバに対して解析方法を指定する
    xmlHttpRequest.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );

    // データをリクエスト ボディに含めて送信する
    xmlHttpRequest.send( EncodeHTMLForm( data ) );
}


let html_list = ""

//リアルタイムチャット
var es = new EventSource('./discussion_sse.php?fid='+get_file_id+'&user='+user_name);

es.addEventListener('message', function (event) {
    //console.log(event.data);
    //console.log(html_list)
    if(event.data != "0"){
        html_list += event.data+"\n";
    }else if(html_list != ""){
        discussion_area.innerHTML = html_list
        html_list = ""
        put_reply()
        md_parse()
    }
});


function put_reply(){
    let post_main = document.querySelectorAll(".post_main .u")

    for(var i = 0;i < post_main.length;i++){
        post_main[i].innerHTML += '<span class="reply" onclick="post_reply(this)">reply</span>'
    }
}


function post_reply(e){
    let reply_text = e.parentElement.parentElement.querySelector(".text").textContent

    reply_text_list = reply_text.split("\n")

    for(var i = 0;i < reply_text_list.length;i++){
        document.querySelector("#editor_main").value += "\n> "+reply_text_list[i]
    }
}

function md_parse(){
    let text_area = discussion_area.querySelectorAll(".text")
    for(var i = 0;i < text_area.length;i++){
        console.log(text_area[i].textContent)
        text_area[i].innerHTML = marked.parse(text_area[i].textContent)
    }
}


//chat-gpt talking help
function gpt_talk_send(){

    document.querySelector("#assist_load_box").style.display = "block"
    document.querySelector("#overlay_black").style.display = "block"

    // URLを取得
    let url = new URL(window.location.href);
    let params = url.searchParams;
    let user_name = params.get('u')

    let question_s = document.querySelector("#gpt_talk_inp").value+"\n\n It should be within "+document.querySelector("#word_num").value+" words ."

    var data = { user: user_name, q:  question_s}; // POSTメソッドで送信するデータ


    var xmlHttpRequest = new XMLHttpRequest();
    xmlHttpRequest.onreadystatechange = function()
    {

        var READYSTATE_COMPLETED = 4;

        if( this.readyState == READYSTATE_COMPLETED && this.status == 200 )
        {
            // レスポンスの表示
            console.log( this.responseText );

            document.querySelector("#editor_main").value += this.responseText

            location.href = "#!"

            document.querySelector("#assist_load_box").style.display = "none"
            document.querySelector("#overlay_black").style.display = "none"
        }
    }

    xmlHttpRequest.open( 'POST', "./chat_api.php?mode=talk");

    // サーバに対して解析方法を指定する
    xmlHttpRequest.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );

    // データをリクエスト ボディに含めて送信する
    xmlHttpRequest.send( EncodeHTMLForm( data ) );
}