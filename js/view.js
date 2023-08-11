


let answer_btn = document.querySelector("#answer_btn")

if(answer_btn != undefined){
    answer_btn.addEventListener("click",answer_click,false)
}



let preview_html_content = document.querySelector("#preview_html_content")

preview_html_content.innerHTML = marked.parse(preview_html_content.innerHTML)


view_summarize()


function view_summarize(){

    let md_text = document.querySelector("#preview_html_content").textContent

    var data = {md: md_text };


    var xmlHttpRequest = new XMLHttpRequest();
    xmlHttpRequest.onreadystatechange = function()
    {
        var READYSTATE_COMPLETED = 4;

        if( this.readyState == READYSTATE_COMPLETED && this.status == 200 )
        {
            let res = this.responseText
            console.log(res);

            preview_html_content.innerHTML = "<div style='padding-bottom:1em;'><span style='font-size:1.5em;'>Summarize by AI</span><br><br>" + res.replaceAll("\n","<br>") + "</div><hr><br><br>"+ preview_html_content.innerHTML
        }else{
            console.log(this.responseText)
        }
    }

    xmlHttpRequest.open( 'POST', './summarize.php');

    xmlHttpRequest.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );

    xmlHttpRequest.send( EncodeHTMLForm( data ) );
}



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

            location.reload()
        }else{
            save_error.style.display = "block"
        }
    }

    xmlHttpRequest.open( 'POST', './answer.php?csrf_token='+csrf_token);

    // サーバに対して解析方法を指定する
    xmlHttpRequest.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );

    // データをリクエスト ボディに含めて送信する
    xmlHttpRequest.send( EncodeHTMLForm( data ) );
}


let side_bar = document.querySelector("#side_bar")
let h_list = document.querySelectorAll("h1,h2,h3")

side_bar.innerHTML = ""

for(var i = 0;i < h_list.length;i++){
    side_bar.innerHTML += '<a href="#'+h_list[i].id+'">'+h_list[i].textContent+'</a>'
}