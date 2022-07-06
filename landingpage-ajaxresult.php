<?php
/*Author name: <Bizight Solutions Pvt Ltd> 
 * Purpose of document/ page : <This page is used to capture lead from landing page.> 
 * Date: 02-07-2021 
 * Copyright info : <Copyright @2021, Bizight Solutions Pvt Ltd>
*/
require realpath(__DIR__ . '/vendor/autoload.php');

include("includes/global.php");
include("includes/function.php");
include_once("includes/connect-new.php");
include("manager/common_functions.php");
include("csrf/csrf-magic.php");
include("includes/common_php_functions.php");


if($_REQUEST['wgtype'] == 'ergo'){

    $wg_age = $_REQUEST['ergoage'];
    $wg_family = $_REQUEST['ergofamily'];
    $wg_city = $_REQUEST['ergocity'];
    $wg_income = $_REQUEST['ergoincome'];

    $Getrow = mysqli_query($conn, "SELECT wg_result FROM sp_widget_store_results  WHERE wg_family LIKE '{$wg_family}'  and wg_age ='{$wg_age}' and wg_city LIKE '{$wg_city}'  and wg_income ='{$wg_income}' and wg_type='Ergowig'");
    $GetData = mysqli_fetch_array($Getrow);
    $response = 0;
    if(!empty($GetData)){
        $response =$GetData['wg_result'];
    }
   echo $response; exit();
}