
let search_box = document.querySelector("#search_box")

let help_area = document.querySelector("#help_area")

let disucssion_area = document.querySelector("#discussion_area")


if(search_box != undefined){
    //urlのシャープ部分が入力されていれば、検索する
    if(location.hash != ""){
        search_box.value = decodeURI(location.hash.replace("#",""))
        box_search()
    }else{
        search("($some_question$)","help_area","doc")
        search("($some_discussion$)","discussion_area","discussion")
    }
    search_box.onkeydown = function(e){
        //Enterキーで検索
        if(e.key == "Enter"){
            location.href = encodeURI("#"+search_box.value)
            box_search()
        }
    }
    
    document.querySelector("#search_btn").addEventListener("click",function(){
        location.href = encodeURI("#"+search_box.value)
        box_search()
    })    
}

function search(str,id,k){
    if(user_name != ""){
        var data_url = new XMLHttpRequest();

        //検索リクエスト送信
        data_url.open('GET',encodeURI("./search.php?s="+str+"&u="+user_name+"&kind="+k));
        data_url.send();
        
        data_url.onreadystatechange = function() {
            if(data_url.readyState === 4 && data_url.status === 200) {
                console.log(data_url.responseText);
                display_result(data_url.responseText,id,k)
            }
        }
    }
}

//検索結果を表示
function display_result(csv_data,id,k){
    if(csv_data != ""){
        var csv_lines =  csv_data.split("\n")

        var help_area = document.querySelector("#"+id)
    
    
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
                +'<span class="title">'+line[0].replace("<","&lt;")+'</span>' //title
                +'<img src="./img/close.svg" class="open" onclick="display_content(this)">'

                //ユーザと記事作成者が同じならeditボタンを追加
                if(user_id == user_name){
                    if(k == "doc"){
                        help_content_html += ''
                        +'<a class="setting" href="./edit.php?u='+user_name+'&fid='+line[1]+'">edit</a>'
                    }else{
                        help_content_html += ''
                        +'<span style="cursor:pointer;" class="setting" onclick="delete_click(`'+line[1]+'`,`'+k+'`)">delete</span>'
                    }
                }
                help_content_html += '<div class="close_hide" style="display:none;">'
                +   '<div class="description">'
                +   line[2].replaceAll("#","") //description
                +   '</div>'

                if(k == "doc" || k == "question"){
                    help_content_html +=   '<a class="more" href="./view.php?u='+user_name+'&fid='+line[1]+'"></a>'
                }else{
                    help_content_html +=   '<a class="more" href="./discussion.php?u='+user_name+'&fid='+line[1]+'"></a>'
                }

            help_content_html +=    '</div>'
            +'</div>'
            
            help_area.innerHTML += help_content_html
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


function box_search(){
    help_area.innerHTML = ""
    disucssion_area.innerHTML = ""
    let discussion_text = document.querySelector(".discussion_text")
    discussion_text.innerHTML = ""
    search(search_box.value,"help_area","doc")
    search(search_box.value,"help_area","question")
    search(search_box.value,"help_area","discussion")
}


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





//スクロールでフェードイン(index.php)
let els = document.querySelectorAll('.sub_des');

els.forEach(function(fadeIn) {
  let windowHeight = window.innerHeight;
  
  window.addEventListener('scroll', function() {
    let offset = fadeIn.getBoundingClientRect().top;
    let scroll = window.scrollY;
    
    if(scroll > offset - windowHeight + 250){
       fadeIn.classList.add('is-scrollIn');
    }
  })
})