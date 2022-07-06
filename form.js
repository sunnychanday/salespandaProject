$(window).load(function() {
var params=window.location.href;
var HTTP_REFERER = $('#frmone').attr('data-referer');
var client_id = $('#frmone').attr('data-client');
//alert(params);
$('#frmone').load("http://www.technochimes.com/salespanda/webcontent/form-to-action.php?url="+params+"&HTTP_REFERER="+HTTP_REFERER+"&client_id="+client_id);
});
