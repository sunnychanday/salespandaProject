var startTime, timeSite, HTTP_REFERER, params, client_id, bs, mk, vtoken, uemail, visit_page, source, g_recaptcha_key;
var visit_link = 'homepage';//other
var url = window.location.href;
//var host = window.location.hostname;
//var arr = url.split("/");

URLtoLOAD = location.origin +"/event-action.php";
window.onload = function() {
	enableScript();
};

var urlcheck = window.location.href;
var spliturl = urlcheck.split("/");
if(spliturl[3] === "landingpage"){
    visit_page = "landingpage";
	var landing_page_click = 1;
	var myEle_id = '';
	if(document.getElementById("form_btn_sub")){
		myEle_id= 'form_btn_sub';
	}else if(document.getElementById("form_btn")){
		myEle_id = 'form_btn';
	}else{
		landing_page_click = 0;
	}
	if(landing_page_click){
		document.getElementById(myEle_id).onclick = function(){
			var ldpemail = '';
			if(document.getElementById("email")){
				ldpemail = document.getElementById("email").value;
			}
			if(ldpemail != ''){
				uemail = checkSLUemailCookie(ldpemail);
			}

			var start = sessionStorage.getItem('startTime');
			var vars = 'url='+ window.encodeURIComponent(params) +'&HTTP_REFERER='+ window.encodeURIComponent(HTTP_REFERER) +'&client_id='+ window.encodeURIComponent(client_id) +'&vtoken='+ window.encodeURIComponent(vtoken) +'&uemail='+ window.encodeURIComponent(uemail) +'&start='+ window.encodeURIComponent(start);

			loadTemplate(vars,4);
		};
	}
}else if(spliturl[3] === "showcase"){
	visit_page = "showcase";
	if(document.getElementById('showcase-subtn')){
		document.getElementById('showcase-subtn').onclick = function() {
			var scpemail = document.getElementById("scpemail").value;
			if(scpemail != ''){
				uemail = checkSLUemailCookie(scpemail);
			}
		};
	}
}

function enableScript(){
	startTime = new Date(); 
	params = window.location.href;  
	HTTP_REFERER = document.referrer;
	client_id = document.getElementById("sp_form").getAttribute("data-client");
	window.onbeforeunload = function() {
		var endTime = new Date(); 
		sessionStorage.setItem("endTime", endTime);
		var start = sessionStorage.getItem('startTime'); 
		var timeSpent = endTime - startTime;
		var vars = 'url='+ window.encodeURIComponent(params) +'&HTTP_REFERER='+ window.encodeURIComponent(HTTP_REFERER) +'&client_id='+ window.encodeURIComponent(client_id) +'&vtoken='+ window.encodeURIComponent(vtoken) +'&uemail='+ window.encodeURIComponent(uemail) +'&start='+ window.encodeURIComponent(start) +'&end='+ window.encodeURIComponent(endTime) +'&timeSpent='+ window.encodeURIComponent(timeSpent/1000) +'&c='+ window.encodeURIComponent(cont);

		loadTemplate(vars, 3);
		return null;
	}
	
	salespanda_script();
}

//Load function on page load
function salespanda_script(){ 
	if(params.indexOf("landingpage") > -1){
		visit_page = "landingpage"; 
	}
	else if(params.indexOf("showcase") > -1){
		visit_page = "showcase"; 
	}else{
		visit_page = (visit_link === null) ? 'other' : visit_link;
	}
	
	bs = sessionStorage.getItem("base"); 
	if(typeof microsite == 'undefined') {
        microsite = 0;
    }
	
	var vars = 'url='+ window.encodeURIComponent(params) +'&HTTP_REFERER='+ window.encodeURIComponent(HTTP_REFERER) +'&client_id='+ window.encodeURIComponent(client_id) +'&vtoken='+ window.encodeURIComponent(vtoken) +'&uemail='+ window.encodeURIComponent(uemail) +'&start='+ window.encodeURIComponent(startTime) +'&visit_page='+ window.encodeURIComponent(visit_page) +'&mic='+ microsite +'&c='+ window.encodeURIComponent(cont);

    if(bs !== null && bs == params){
		bs = true;
		loadTemplate(vars, 1);
	}
	else
	{
		sessionStorage.setItem("startTime", startTime);
		loadTemplate(vars, 2);
	
	}
	
	sessionStorage.removeItem("base");
	sessionStorage.setItem("base", params);
}


function loadTemplate(vars, tkn){
	var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    xhr.open('POST', URLtoLOAD, true);
    xhr.onreadystatechange = function() {
		if (xhr.readyState > 3 && xhr.status == 200) {}
    };
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

	g_recaptcha_key = document.getElementById("g_recaptcha_key").getAttribute("data-captcha-key");
	grecaptcha.ready(function () {
		grecaptcha.execute(g_recaptcha_key, {action: 'create_second_form'}).then(function (token) {
			xhr.send(vars +'&tkn='+ tkn +'&recaptcha_token='+ token);
		});
	});
}
