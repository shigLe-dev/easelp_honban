

let post_btn = document.querySelector("#post_btn")


post_btn.addEventListener("click",post_click,false)


function post_click(){
    let title_text = document.querySelector("#title_input").value

    let md_text = document.querySelector("#editor_main").value

    var data = { title: title_text, md: md_text ,file_id: get_file_id ,user: user_name}; // POSTメソッドで送信するデータ


    var xmlHttpRequest = new XMLHttpRequest();
    xmlHttpRequest.onreadystatechange = function()
    {
        var READYSTATE_COMPLETED = 4;

        if( this.readyState == READYSTATE_COMPLETED && this.status == 200 )
        {
            // レスポンスの表示
            console.log( this.responseText );
            console.log("hoge")

            saved_window.style.display = "block"
        }else{
            save_error.style.display = "block"
        }
    }

    xmlHttpRequest.open( 'POST', './question.php?csrf_token='+csrf_token);

    // サーバに対して解析方法を指定する
    xmlHttpRequest.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );

    // データをリクエスト ボディに含めて送信する
    xmlHttpRequest.send( EncodeHTMLForm( data ) );
}