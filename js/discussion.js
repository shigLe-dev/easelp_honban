

let post_btn = document.querySelector("#make_discussion")


post_btn.addEventListener("click",make_discussion,false)


function make_discussion(){
    let title_text = document.querySelector("#title_input_discuss").value

    let md_text = document.querySelector("#editor_main").value

    var data = { title: title_text, comment: md_text ,user: user_name}; // POSTメソッドで送信するデータ


    var xmlHttpRequest = new XMLHttpRequest();
    xmlHttpRequest.onreadystatechange = function()
    {
        var READYSTATE_COMPLETED = 4;

        if( this.readyState == READYSTATE_COMPLETED && this.status == 200 )
        {
            // レスポンスの表示
            let res = this.responseText
            console.log(res);
            console.log("hoge")

            res_list = res.split(",")

            location.href="./discussion.php?u="+res_list[0]+"&fid="+res_list[1]
        }else{
            save_error.style.display = "block"
        }
    }

    xmlHttpRequest.open( 'POST', './make_discussion.php?csrf_token='+csrf_token);

    // サーバに対して解析方法を指定する
    xmlHttpRequest.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );

    // データをリクエスト ボディに含めて送信する
    xmlHttpRequest.send( EncodeHTMLForm( data ) );
}