


search()

function search(){
    var data_url = new XMLHttpRequest();


    //検索リクエスト送信
    data_url.open('GET',encodeURI("./search.php?s=($all_search$)&u="+user_name));
    data_url.send();

    data_url.onreadystatechange = function() {
        if(data_url.readyState === 4 && data_url.status === 200) {
            console.log(data_url.responseText);
            display_result(data_url.responseText,false,"","doc")
        }
    }
}

//検索結果を表示
function display_result(csv_data,question,d,k){
    if(csv_data != ""){
        var csv_lines =  csv_data.split("\n")

        var post_main = document.querySelector("#post_main")
        post_main.innerHTML = ""
    
    
        for(var i = 0;i < csv_lines.length;i++){
            var line = csv_lines[i].split("($split)")

            //escape
            for(var j = 0;j < line.length;j++){
                line[j] = line[j].replace("<","&lt;").replace(">","&gt;").replace('"',"&quot;")
            }

            //line[0] : title
            //line[1] : file id
            //line[2] : description

            if(line.length != 1){
                var help_content_html = ""
    
                help_content_html += '<div class="help_content">'
                +'<span class="title">'+line[0]+'</span>' //title
                +'<img src="./img/close.svg" class="open" onclick="display_content(this)">'
    
                //ユーザと記事作成者が同じならeditボタンを追加
                if(user_id == user_name){
                    if(question){
                        if(d != "d"){
                            help_content_html += ''
                            +'<a class="setting" href="./view.php?u='+user_name+'&fid='+line[1]+'">answer</a>'
                        }else{
                            help_content_html += ''
                            +'<span style="cursor:pointer;" class="setting" onclick="delete_click(`'+line[1]+'`,`'+k+'`)">delete</span>'
                        }
                    }else{
                        help_content_html += ''
                        +'<a class="setting" href="./edit.php?u='+user_name+'&fid='+line[1]+'">edit</a>'
                    }
                }
                help_content_html += '<div class="close_hide" style="display:none;">'
                +   '<div class="description">'
                +   line[2] //description
                +   '</div>'
                if(d == "d"){
                    help_content_html +=   '<a class="more" href="./discussion.php?u='+user_name+'&fid='+line[1]+'"></a>'
                }else{
                    help_content_html +=   '<a class="more" href="./view.php?u='+user_name+'&fid='+line[1]+'"></a>'
                }
                help_content_html += '</div>'
            +'</div>'
            
            post_main.innerHTML += help_content_html
            }

        }
    }
}


//右上ボタンが押されたら、
//隠れていたら表示
//表示されていたら隠す
function display_content(e){
    var close_hide = e.parentNode.querySelector(".close_hide")

    console.log(close_hide)

    //隠れていたらコンテンツを表示
    if(close_hide.style.display == "none"){
        //表示して画像を変更
        close_hide.style.display = "block"
        e.src = "./img/open.svg"
    }else{//表示されていたら隠す
        close_hide.style.display = "none"
        e.src = "./img/close.svg"
    }
}



//question list
function display_question(){
    console.log("piyo")
    var data_url = new XMLHttpRequest();


    //検索リクエスト送信
    data_url.open('GET',encodeURI("./search.php?s=($all_question$)&u="+user_name+"&kind=question"));
    data_url.send();

    data_url.onreadystatechange = function() {
        if(data_url.readyState === 4 && data_url.status === 200) {
            console.log(data_url.responseText);
            display_result(data_url.responseText,true,"")
        }
    }
}

//discussion list
function display_discussion(){
    console.log("piyo")
    var data_url = new XMLHttpRequest();


    //検索リクエスト送信
    data_url.open('GET',encodeURI("./search.php?s=($all_discussion$)&u="+user_name+"&kind=discussion"));
    data_url.send();

    data_url.onreadystatechange = function() {
        if(data_url.readyState === 4 && data_url.status === 200) {
            console.log(data_url.responseText);
            display_result(data_url.responseText,true,"d","discussion")
        }
    }
}


function hidden_all(){
    post_main.style.display = "none"
    page_url_area.style.display = "none"
    import_md_area.style.display = "none"
    set_visible_area.style.display = "none"
    chat_gpt_setting.style.display = "none"
}


document.querySelector("#webhook_save").addEventListener("click",() => {
    let webhook_url = document.querySelector("#webhook_url").value
    setting_post("webhook",webhook_url)
})

//change visibility
document.querySelector("#change_v").addEventListener("click",() => {
    let change_v_content = document.querySelector("#change_v").getAttribute("value")
    setting_post("visibility",change_v_content)
    location.href = "#visibility";
    location.reload()
})

//change visibility
document.querySelector("#gpt_save").addEventListener("click",() => {
    let gpt_org = document.querySelector("#gpt_org").value
    setting_post("gpt_org",gpt_org)
    let gpt_key = document.querySelector("#gpt_key").value
    setting_post("gpt_key",gpt_key)
    
})

//user setting
function setting_post(data_name,send_data){

    var data = {content:send_data,name:data_name}; // POSTメソッドで送信するデータ


    var xmlHttpRequest = new XMLHttpRequest();
    xmlHttpRequest.onreadystatechange = function()
    {
        var READYSTATE_COMPLETED = 4;

        if( this.readyState == READYSTATE_COMPLETED && this.status == 200 )
        {
            // レスポンスの表示
            console.log( this.responseText );
        }
    }

    xmlHttpRequest.open( 'POST', './setting.php?csrf_token='+csrf_token);

    // サーバに対して解析方法を指定する
    xmlHttpRequest.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );

    // データをリクエスト ボディに含めて送信する
    xmlHttpRequest.send( EncodeHTMLForm( data ) );
}


let md_list = []
let md_file_contents = []

function import_github(){
    let github_user = document.querySelector("#github_user")
    let github_repo = document.querySelector("#github_repo")

    var data_url = new XMLHttpRequest();

    //検索リクエスト送信
    data_url.open('GET',encodeURI("https://api.github.com/repos/"+github_user.value+"/"+github_repo.value+"/contents/"));
    data_url.send();

    data_url.onreadystatechange = function() {
        if(data_url.readyState === 4 && data_url.status === 200) {
            let md_name_list = document.querySelector("#md_name_list")

            let file_json = JSON.parse(data_url.responseText);

            console.log(file_json);

            for(var i = 0;i < file_json.length;i++){
                var extension = file_json[i].name.split(".").slice(-1)[0]
                if(extension == "md"){
                    if(file_json[i].size != 0){
                        md_list.push(file_json[i].download_url)
                        md_name_list.innerHTML += "<br>File: "+file_json[i].name+"<br>Title:<input class='github_title' value='"+file_json[i].name+"'><br>"

                        var data_url_md = new XMLHttpRequest();

                        //検索リクエスト送信
                        data_url_md.open('GET',file_json[i].download_url);
                        data_url_md.send();
                    
                        data_url_md.onreadystatechange = function() {
                            if(data_url_md.readyState === 4 && data_url.status === 200) {
                                console.log(data_url_md.responseText);
                                md_file_contents.push(data_url_md.responseText)
                            }
                        }
                    }
                }
            }
            
            console.log(md_list)

        }
    }
}

function save_github(){

    let github_title =  document.querySelectorAll(".github_title")

    for(var i = 0;i < github_title.length;i++){
        let title_text = github_title[i].value
        let md_text = md_file_contents
        let get_file_id = ""
    
        var data = { title: title_text, md: md_text ,file_id: get_file_id}; // POSTメソッドで送信するデータ
    
    
        var xmlHttpRequest = new XMLHttpRequest();
        xmlHttpRequest.onreadystatechange = function()
        {
            var READYSTATE_COMPLETED = 4;
            document.querySelector("#your_post").click();
        }
    
        xmlHttpRequest.open( 'POST', './save.php?csrf_token='+csrf_token);
    
        // サーバに対して解析方法を指定する
        xmlHttpRequest.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
    
        // データをリクエスト ボディに含めて送信する
        xmlHttpRequest.send( EncodeHTMLForm( data ) );
    }
}



//choose_boxが選択されたときの処理
const choose_list = document.querySelectorAll(".choose_check")

let post_main = document.querySelector("#post_main")
let page_url_area = document.querySelector("#page_url_area")
let import_md_area = document.querySelector("#import_md_area")
let set_visible_area = document.querySelector("#set_visible_area")
let chat_gpt_setting = document.querySelector("#chat_gpt_setting")

let tab_title = document.querySelector("#tab_title")

console.log(choose_list)

for(var i = 0;i < choose_list.length;i++){
    choose_list[i].addEventListener("click",function(){

        console.log("hoge")
        
        let chosen_box = document.querySelector(".chosen_box")
        chosen_box.classList.remove("chosen_box");
    
        this.classList.add("chosen_box")

        hidden_all()

        if(this.id == "your_post"){
            search()
            post_main.style.display = "block"
            tab_title.innerHTML = "your post"
        }else if(this.id == "page_url"){
            page_url_area.style.display = "block"
            tab_title.innerHTML = "urls of your pages"
        }else if(this.id == "question_list"){
            console.log("hoge")
            display_question()
            post_main.style.display = "block"
            tab_title.innerHTML = "question"
        }else if(this.id == "discussion_list"){
            display_discussion()
            post_main.style.display = "block"
            tab_title.innerHTML = "discussion"
        }else if(this.id == "import_md"){
            import_md_area.style.display = "block"
            tab_title.innerHTML = "import docs"
        }else if(this.id == "chat_gpt"){
            chat_gpt_setting.style.display = "block"
            tab_title.innerHTML = "Chat gpt setting"
        }else if(this.id == "post_visibility"){
            console.log("pengin")
            set_visible_area.style.display = "block"
            tab_title.innerHTML = "visilibity setting"
            if(allow_u_area != undefined){
                display_allow_u()
            }
        }

    })
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


//delete a discussion or a question
function delete_click(this_file_id,k){
    
    let delete_check = window.confirm("Delete it?");
    if(!delete_check){return 0}

    if(this_file_id != ""){
        var data_url = new XMLHttpRequest();
    
        data_url.open('GET', './delete.php?file_id='+this_file_id+'&kind='+k+'&csrf_token='+csrf_token);
        data_url.send();
        
        data_url.onreadystatechange = function() {
            if(data_url.readyState === 4 && data_url.status === 200) {
               console.log(data_url.responseText);
               location.reload()
            }else{
                save_error_window.style.display = "block";
            }
        }
    }
}


//user that's allowed ....
let allow_u_area = document.querySelector("#allow_user_area")
let allow_u_box = document.querySelector("#allow_u_box")
let allow_u_list = []

allow_u_list = allow_u.split("($s)")

function display_allow_u(){
    allow_u_area.innerHTML = ""

    for(var i = 0;i < allow_u_list.length;i++){
        if(allow_u_list[i] != ""){
            allow_u_area.innerHTML += '<div class="allowed_user" value="'+allow_u_list[i]+'">'+allow_u_list[i]+'<div class="del" onclick="del_allow_u(this)">×</div></div>'
        }else{
            allow_u_list = []
        }
    }
}

let allow_u_add = document.querySelector("#allow_u_add")

if(allow_u_add != undefined){
    allow_u_add.addEventListener("click",add_allow_u)
}

function add_allow_u(){
    if(allow_u_box.value != "" && allow_u_box.value.match(/^[A-Za-z0-9]*$/))
    allow_u_list.push(allow_u_box.value)
    save_allow_u()
    allow_u_box.value = ""
}

function del_allow_u(e){
    console.log(e.parentElement)
    e.parentElement.remove()
    let allow_u_ele = allow_u_area.querySelectorAll(".allowed_user")

    allow_u_list = []

    for(var i = 0;i < allow_u_ele.length;i++){
        allow_u_list.push(allow_u_ele[i].getAttribute("value"))
    }

    display_allow_u()

    save_allow_u()
}

function save_allow_u(){
    let data_text = ""

    for(var i = 0;i < allow_u_list.length;i++){
        data_text += allow_u_list[i]

        if(i != allow_u_list.length-1){
            data_text += "($s)"
        }
    }

    display_allow_u()

    setting_post("allow_u",data_text)
}

if(location.hash == "#visibility"){
    document.querySelector("#post_visibility").click()
}


let logo_text_ele = document.querySelector(".logo_text")
let user_info_ele = document.querySelector("#user_info")

logo_text_ele.addEventListener("click",function(){
    if(user_info_ele.style.display == "none" || user_info_ele.style.display == ""){
        user_info_ele.style.display = "block"
    }else{
        user_info_ele.style.display = "none"
    }
    
})