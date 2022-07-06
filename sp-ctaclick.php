<?php

include("includes/global.php");
include("includes/function.php");
include("manager/common_functions.php");
include("csrf/csrf-magic.php");
$client_id = testInput($_POST['client_id']);

$ctaurl = testInput($_POST['ctaurl']);

$pctype = testInput($_POST['pctype']);

$childedit_status = testInput($_POST['childedit_status']);



$sqry = "select * from sp_subdomain where client_id='" . $client_id . "'";
$resq = mysqli_query($conn, $sqry);
$domianData = mysqli_fetch_array($resq);
$subdomain_url = $domianData['subdomain_url'];
$cms = $domianData['cms_subdomain_url'];

if ($pctype == 'C') {
    $smemqry1 = "SELECT p_client_id FROM sp_sub_members where c_client_id= '" . $client_id . "' and valid=1 and deleted=0";
    $smem_ftch = mysqli_query($conn, $smemqry1);
    $row_smem = mysqli_fetch_array($smem_ftch);
    $pc_client_id = $row_smem['p_client_id'];
} else {
    $pc_client_id = $client_id;
}




$temp_set = mysqli_query($conn, "select cobrand from user_templates where content_file='" . $ctaurl . "' and client_id='" . $pc_client_id . "' and valid=1 and deleted=0");
$temp_num = mysqli_num_rows($temp_set);

$temp_get = mysqli_fetch_array($temp_set);

$cobrand = $temp_get['cobrand'];

if ($pctype == 'C') {
    if ($cobrand == 1) {
        echo $pathPdf = 'https://' . $cms . "/upload/casestudy/$client_id/$ctaurl";
    } else if ($temp_num == 0) {
        echo $pathPdf = 'https://' . $cms . "/upload/casestudy/" . $client_id . "/" . $ctaurl;
    } else {
        echo $pathPdf = 'https://' . $cms . "/upload/casestudy/$pc_client_id/$ctaurl";
    }
} else {

    echo $pathPdf = 'https://' . $cms . "/upload/casestudy/" . $client_id . "/" . $ctaurl;
}
?>