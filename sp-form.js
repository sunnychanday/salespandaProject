function salespanda_engmt_script()
{ 
mk = jQuery.noConflict() || $.noConflict();    
jQuery(window).load(function(){ 
var params=window.location.href; 
params_arr = params.split('?');
params = params_arr[0];
var HTTP_REFERER =document.referrer;
var client_id = jQuery('#sp_form').attr('data-client'); 
var URLtoLOAD1 = "https://app.mutualfundpartner.com/sp-form-action.php";
var host = window.location.hostname;
if(host.search("maxlifeinsurance.agency")>0){
	URLtoLOAD1 = "https://app.maxlifeinsurance.agency/sp-form-action.php";
}else if(host.search("technochimes.com")>0){
	URLtoLOAD1 = "https://app.technochimes.com/sp-form-action.php";
}else if(host.search("mutualfundpartner.com")>0){
	URLtoLOAD1 = "https://app.mutualfundpartner.com/sp-form-action.php";
}
var URLtoLOAD = URLtoLOAD1+"?url="+params+"&HTTP_REFERER="+HTTP_REFERER+"&client_id="+client_id; 
jQuery('#sp_form').load(URLtoLOAD);
});
}
if(typeof jQuery=='undefined') 
{
    var headTag = document.getElementsByTagName("head")[0];
    var jqTag = document.createElement('script');
    jqTag.type = 'text/javascript';
    jqTag.src = 'https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js';
    jqTag.onload = salespanda_engmt_script;
    headTag.appendChild(jqTag);
} 
else 
{
salespanda_engmt_script();
}