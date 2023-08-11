

let chat_main = document.querySelector("#chat_main")

let chat_textbox = document.querySelector("#chat_textbox")


let word_num = word_num_box = document.querySelector("#word_num")

chat_textbox.onkeydown = function(e){
    if(e.key == "Enter"){
        chat_main.innerHTML += `
        <div class="chat_text me">
        `+chat_textbox.value+`
    </div>
    `
    
    q_send()

    chat_textbox.value = ""
    }
}

function q_send(){

    var data = { user: user_name, q: chat_textbox.value,w: word_num.value}; // POSTメソッドで送信するデータ


    var xmlHttpRequest = new XMLHttpRequest();
    xmlHttpRequest.onreadystatechange = function()
    {
        var READYSTATE_COMPLETED = 4;

        if( this.readyState == READYSTATE_COMPLETED && this.status == 200 )
        {
            // レスポンスの表示
            console.log( this.responseText );

            chat_main.innerHTML += `
            <div class="chat_text bot">
            `+marked.parse(this.responseText.replace("\n","<br>"))+`
        </div>
        `

        let a_tags = chat_main.querySelectorAll("a")

        for(var i = 0;i < a_tags.length;i++){
            a_tags[i].target = "_blank"
        }

        hljs.highlightAll();


        var bottom = chat_main.scrollHeight - chat_main.clientHeight;
        chat_main.scroll(0, bottom);

        }
    }

    xmlHttpRequest.open( 'POST', "./chat_api.php?mode=chat");

    // サーバに対して解析方法を指定する
    xmlHttpRequest.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );

    // データをリクエスト ボディに含めて送信する
    xmlHttpRequest.send( EncodeHTMLForm( data ) );
}


// HTMLフォームの形式にデータを変換する
function EncodeHTMLForm( data )
{
    var params = [];

    for( var name in data )
    {
        var value = data[ name ];
        var param = encodeURIComponent( name ) + '=' + encodeURIComponent( value );

        params.push( param );
    }

    return params.join( '&' ).replace( /%20/g, '+' );
}
