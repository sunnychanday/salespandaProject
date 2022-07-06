//alert(document.location.hostname);
var startTime,timeSite, HTTP_REFERER, params, client_id, bs, mk, vtoken, uemail,visit_page,source;
//var URLtoLOAD2 = "http://systest1.technochimes.com/sp-form-action.php";
var URLtoLOAD = "https://app.mutualfundpartner.com/event-actionV2.php";
var headTag = document.getElementsByTagName("head")[0];

    var jqTag = document.createElement('script');
    jqTag.type = 'text/javascript';
//    jqTag.src = 'https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js';
//jqTag.onload = enableScript;
   // jqTag.beforeunload = beforeunload;
//headTag.appendChild(jqTag);
window.onload = function() {
	enableScript();
};
 
function enableScript(){
	//alert("in enable script");
   // mk = jQuery.noConflict() || $.noConflict();
	startTime = new Date(); 
	params=window.location.href;  
	HTTP_REFERER =document.referrer; 
	//alert("Referer--"+HTTP_REFERER);
	client_id = document.getElementById("sp_form").getAttribute("data-client");
	//client_id = mk('#sp_form').attr('data-client');
	
	window.onbeforeunload = function() {
	 // alert("onbeforeunload"); 
		var endTime = new Date(); 
		sessionStorage.setItem("endTime", endTime);
		var start = sessionStorage.getItem('startTime'); 
		var timeSpent = endTime - startTime;
		var vars = 'url='+window.encodeURIComponent(params)+'&HTTP_REFERER='+window.encodeURIComponent(HTTP_REFERER)+'&client_id='+window.encodeURIComponent(client_id)+'&vtoken='+window.encodeURIComponent(vtoken)+'&uemail='+window.encodeURIComponent(uemail)+'&start='+window.encodeURIComponent(start)+'&end='+window.encodeURIComponent(endTime)+'&timeSpent='+window.encodeURIComponent(timeSpent/1000)+'&c='+window.encodeURIComponent(cont);

		loadTemplate(vars,3);
	  	//console.log(uemail);
		return null;
	}
	//alert("enableScript");
	salespanda_script();
}
//mk = jQuery.noConflict() || $.noConflict();
var urlcheck=window.location.href;
var spliturl=urlcheck.split("/");



if(spliturl[3]=="landingpage"){
	//alert("landing page conditions");
    visit_page = "landingpage";
	document.getElementById('form_btn').onclick = function() {
	//mk(document).on('click','#form_btn', function(){
	//	var ldpemail = mk("#email").val();
	var ldpemail = document.getElementById("email").value;
		if(ldpemail!==''){
			uemail = checkSLUemailCookie(ldpemail); 
		}
		var start = sessionStorage.getItem('startTime'); 
		var vars = 'url='+window.encodeURIComponent(params)+'&HTTP_REFERER='+window.encodeURIComponent(HTTP_REFERER)+'&client_id='+window.encodeURIComponent(client_id)+'&vtoken='+window.encodeURIComponent(vtoken)+'&uemail='+window.encodeURIComponent(uemail)+'&start='+window.encodeURIComponent(start);
		loadTemplate(vars,4);
	}; 
}else if(spliturl[3]=="showcase"){
	//alert("elseif --showcase"+spliturl[3]);
	visit_page = "showcase";
	document.getElementById('showcase-subtn').onclick = function() {
	//mk(document).on('click','#showcase-subtn', function(){
		var scpemail = document.getElementById("scpemail").value;
		//var scpemail = mk("#scpemail").val();
		if(scpemail!==''){ 
			uemail = checkSLUemailCookie(scpemail); 
		}
	};
}
else{
  /*  mk(document).on('click','button[id^="submit_"]', function() 
    {
    var engwemail = mk('input[name="email_id"]').val();
    if(engwemail!=='')
    { 
      uemail = checkSLUemailCookie(engwemail); 
    }  
    });
    
    
    mk(document).on('click','#send-value', function() 
    {
    var formcemail = mk("#your-email").val();
    if(formcemail!=='')
    { 
       uemail = checkSLUemailCookie(formcemail); 
    }  
    });
    */
    
}
//window.addEventListener('beforeunload', beforeunload);
//window.onbeforeunload = function(){ 


//Load function on page load
function salespanda_script(){ 
	//alert("in salespanda_script");
    if(params.indexOf("landingpage") > -1){
		visit_page = "landingpage"; 
	}
	else if(params.indexOf("showcase") > -1){
		visit_page = "showcase"; 
	}else{
		visit_page = "other";
	}
   // alert("visit_page "+visit_page); 
     
	bs = sessionStorage.getItem("base"); 
	if(typeof microsite == 'undefined') {
        //alert("salespanda script");
        microsite=0;
    }
	//alert("salespanda_script microsite"+microsite);
	var vars = 'url='+window.encodeURIComponent(params)+'&HTTP_REFERER='+window.encodeURIComponent(HTTP_REFERER)+'&client_id='+window.encodeURIComponent(client_id)+'&vtoken='+window.encodeURIComponent(vtoken)+'&uemail='+window.encodeURIComponent(uemail)+'&start='+window.encodeURIComponent(startTime)+'&visit_page='+window.encodeURIComponent(visit_page)+'&mic='+microsite+'&c='+window.encodeURIComponent(cont);

    if(bs!==null && bs==params){
		bs = true;
		loadTemplate(vars,1);
	}
	else
	{
		sessionStorage.setItem("startTime", startTime);
		loadTemplate(vars,2);
	
	}
	sessionStorage.removeItem("base");
	sessionStorage.setItem("base", params);
}


function loadTemplate(vars,tkn){
	//alert("loadTemplate=========="+vars+"----------"+tkn) 
	var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    xhr.open('POST', URLtoLOAD, true);//,true
    //xhr.withCredentials = true;
    xhr.onreadystatechange = function() {
        if (xhr.readyState>3 && xhr.status==200) { //alert("Done"); 
        }//success(xhr.responseText);
    };
    //xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    //xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(vars+'&tkn='+tkn);//vars+'&tkn='+tkn
  
  
   /*mk.ajax({
            url: URLtoLOAD,
            crossDomain: true,
            data: vars+'&tkn='+tkn,
            
            type: "POST",
            async: false,
            timeout: 5000, 
            success: function(html) 
            { 
                //mk("#sp_form1").html(html);
                //alert(html);
            }
    });*/
	
}
