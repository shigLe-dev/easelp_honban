let root_url = "http://localhost/pages/easelp"

var d = document;
var link = d.createElement('link');
link.href = root_url+'/stream/embed.css';
link.rel = 'stylesheet';
link.type = 'text/css';
var h = d.querySelector('head');
h.appendChild(link);

var easelp_iframe_p = d.createElement("div");
easelp_iframe_p.innerHTML = `
<iframe id="easelp_iframe" src="http://localhost/pages/easelp/embed.php?u=`+user_name+`" style="display:none;" width="350" height="500" scrolling marginwidth="no">
</iframe>
`
document.body.appendChild(easelp_iframe_p)

var easelp_embed_button = d.createElement("div");
easelp_embed_button.id = "easelp_embed_button"
easelp_embed_button.innerHTML = `<div id="easelp_chat_svg">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
</svg>
</div>
`

let easelp_iframe = document.querySelector("#easelp_iframe")

easelp_embed_button.addEventListener("click",function(){
    if(easelp_iframe.style.display == "none"){
        easelp_iframe.style.display = "block"
    }else{
        easelp_iframe.style.display = "none"
    }
})

document.body.appendChild(easelp_embed_button)

