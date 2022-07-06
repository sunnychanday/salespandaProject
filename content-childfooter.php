<?php 

include("../includes/global.php");
include("../includes/function.php");
include("manager/common_functions.php");
include("csrf/csrf-magic.php");
header('Access-Control-Allow-Origin: *');
$client_id = testInput($_POST['client_id']);
$pc = testInput($_POST['pc']);
$mem_query = "SELECT * FROM `sp_members` WHERE client_id='" . $client_id . "' and valid=1 and deleted=0 and approve=1 and company_member_type=1";
$mem_rs = mysqli_query($conn,$mem_query);
$mem_data = mysqli_fetch_array($mem_rs);
$distri_name = $mem_data['first_name'] . ' ' . $mem_data['last_name'];
if (!empty($mem_data['person_contact1'])) {
    $distri_contact = $mem_data['person_contact1'];
} else {
    $distri_contact = $mem_data['person_contact2'];
}/* $query_csubdomain="select cms_subdomain_url,comp_id from sp_subdomain where valid=1 and deleted=0 and client_id='".$client_id."'";  $query_csubdomain_get=mysql_query($query_csubdomain);  $query_csubdomain_set=mysql_fetch_array($query_csubdomain_get);  $CSubdomain=$query_csubdomain_set['cms_subdomain_url'];  $Ccomp_id=$query_csubdomain_set['comp_id'];  $CheaderlogoPath='http://'.$CSubdomain."/company_logo/";  $Cqry="select * from sp_company where comp_id='".$Ccomp_id."' and valid=1 and deleted=0";  $Cmpres=mysql_query($Cqry) or die(mysql_error());  $CmpData=mysql_fetch_array($Cmpres);  $CcompLogo=$CmpData['header_logo'];  $Caboutcmpny=$CmpData[about_company];  if($CcompLogo!=''){  $Clogodisplay=$CheaderlogoPath.$CcompLogo;  } */if ($pc == 'C') { ?>    <div style="width:450px;position: absolute;bottom: 0px;z-index:0" id="content_child">        <p id="logo_img" style="width:auto;font-weight:bold;font-size:16px;color:#000;bottom: 20px;"><?php echo $distri_name; ?></p>        <p style="font-size:12px;"><?php echo $distri_contact; ?></p>    </div><?php } ?>