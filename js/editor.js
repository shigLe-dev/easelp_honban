

let editor_main = document.querySelector("#editor_main")

let preview_html = document.querySelector("#preview_html")

if(editor_main != undefined){
    editor_main.addEventListener("keyup",md_preview,false)
}


let saved_window = document.querySelector("#saved_window")
let save_error_window = document.querySelector("#save_error_window")

/*
marked.setOptions({
    sanitize: true,
  });
*/


//画像アップロード
let image_upload = document.querySelector("#image_upload");
if(image_upload != undefined){
    image_upload.addEventListener("change", up_image_file);
}


//markdownをhtmlに変換
function md_preview(){

    let md = editor_main.value

    preview_html.innerHTML = marked.parse(md)
    console.log(md)

    saved_window.style.display = "none"
    save_error_window.style.display = "none"

    //シンタックスハイライトを更新
    hljs.highlightAll();
}


let save_btn = document.querySelector("#save_btn")

if(save_btn != undefined){
    save_btn.addEventListener("click",save_click,false)

}

//htmlがエスケープされていたのを直す

let escape_list =
[
    ['&quot;','&#039;','&lt;','&gt;','&amp;'],
    ['"',"'",'<','>',"&"]
]

if(typeof(file_content) != "undefined"){

    let view_content = file_content

    for(var i = 0;i < escape_list[0].length;i++){
        view_content = view_content.replaceAll(escape_list[0][i],escape_list[1][i])
    }


    //編集するファイルの内容をセット
    editor_main.value = view_content;
    md_preview();

    if(document.querySelector("#title_input") != undefined){
        document.querySelector("#title_input").value = file_title
    }

}

//ファイルを保存する
function save_click(){
    let title_text = document.querySelector("#title_input").value

    let md_text = document.querySelector("#editor_main").value

    var data = { title: title_text, md: md_text ,file_id: get_file_id}; // POSTメソッドで送信するデータ


    var xmlHttpRequest = new XMLHttpRequest();
    xmlHttpRequest.onreadystatechange = function()
    {
        var READYSTATE_COMPLETED = 4;

        if( this.readyState == READYSTATE_COMPLETED && this.status == 200 )
        {
            // レスポンスの表示
            console.log( this.responseText );

            saved_window.style.display = "block"
        }else{
            save_error.style.display = "block"
        }
    }

    xmlHttpRequest.open( 'POST', './save.php?csrf_token='+csrf_token);

    // サーバに対して解析方法を指定する
    xmlHttpRequest.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );

    // データをリクエスト ボディに含めて送信する
    xmlHttpRequest.send( EncodeHTMLForm( data ) );
}



let delete_btn = document.querySelector("#delete_btn")

if(delete_btn != undefined){
    delete_btn.addEventListener("click",delete_click,false)
}

//このファイルを削除
function delete_click(){
    
    let delete_check = window.confirm("Delete it?");
    if(!delete_check){return 0}

    if(get_file_id != ""){
        var data_url = new XMLHttpRequest();
    
        data_url.open('GET', './delete.php?file_id='+get_file_id+'&kind=doc&csrf_token='+csrf_token);
        data_url.send();
        
        data_url.onreadystatechange = function() {
            if(data_url.readyState === 4 && data_url.status === 200) {
               console.log(data_url.responseText);
               location.href = "./dash.php"
            }else{
                save_error_window.style.display = "block";
            }
        }
    }
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

//画像アップロード

function up_image_file(e){

    // フォーム内容の取得
    let upform_data = document.querySelector("#up_form");
 
    // 送信用データ
    let form_data = new FormData(upform_data);
 
    // 通信用XMLHttpRequestを生成
    let req = new XMLHttpRequest();
 
    // POST形式でサーバ側の「response.php」へデータ通信を行う
    req.open("POST", "./upload.php");

    form_data.append("file1", e.target.files[0]);
 
    // ファイル送信
    req.send(form_data);
    
    req.onreadystatechange = function() {
        if(req.readyState === 4 && req.status === 200) {
           console.log(req.responseText);

           var text = "![image]("+req.responseText+")"

           editor_main.value = editor_main.value.substr(0, editor_main.selectionStart)
           + text
           + editor_main.value.substr(editor_main.selectionStart);

            md_preview()
        }
    }
}


function gpt_write_send(){

    document.querySelector("#assist_load_box").style.display = "block"
    document.querySelector("#overlay_black").style.display = "block"

    // URLを取得
    let url = new URL(window.location.href);
    let params = url.searchParams;
    let user_name = params.get('u')

    var data = { user: user_name, q: document.querySelector("#gpt_write_inp").value+"\n\n It should be within "+document.querySelector("#word_num").value+" words ." }; // POSTメソッドで送信するデータ


    var xmlHttpRequest = new XMLHttpRequest();
    xmlHttpRequest.onreadystatechange = function()
    {
        var READYSTATE_COMPLETED = 4;

        if( this.readyState == READYSTATE_COMPLETED && this.status == 200 )
        {
            // レスポンスの表示
            console.log( this.responseText );

            document.querySelector("#gpt_write_out").value = this.responseText

            document.querySelector("#assist_load_box").style.display = "none"
            document.querySelector("#overlay_black").style.display = "none"
        }
    }

    xmlHttpRequest.open( 'POST', "./chat_api.php?mode=write");

    // サーバに対して解析方法を指定する
    xmlHttpRequest.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );

    // データをリクエスト ボディに含めて送信する
    xmlHttpRequest.send( EncodeHTMLForm( data ) );
}


//markdownを追加
function add_md(inp){
    let text = ""

    switch(inp){
        case "h1":
            text = "# text"
            break;
        case "h2":
            text = "## text"
            break;
        case "link":
            text = "[link title](url)"
            break;
        case "code":
            text = "```\n\n//here your code\n\n```"
            break;
        case "color":
            text = '<span style="color: red;">text</span>'
            break;
        default:
            break;
    }
	//カーソルの位置にmarkdownをおく
	editor_main.value = editor_main.value.substr(0, editor_main.selectionStart)
			+ text
			+ editor_main.value.substr(editor_main.selectionStart);

    md_preview()
}