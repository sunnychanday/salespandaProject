<?php
/*Author name: <Bizight Solutions Pvt Ltd> 
 * Purpose of document/ page : <This page is used to logout from web.> 
 * Date: 17-02-2021 
 * Copyright info : <Copyright @2021, Bizight Solutions Pvt Ltd>
*/
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
  /* $cokierarr= explode("_",base64_decode($_COOKIE['PHPSESSID']));
   if(is_array($cokierarr) && count($cokierarr) == 2){
       unset($_COOKIE['PHPSESSID']);
       setcookie('PHPSESSID', $cokierarr[0], time() + (86400 * 30), "/");
   }*/
   session_start();
	include("includes/global.php");
	include("includes/connect-db.php");
    include("includes/connect-new.php");
	$authuser = "update auth_user set access_token='1-0' where uid='{$_SESSION['userid']}' and client_id = '{$_SESSION['c_lient_Id']}' ";
	$authuserupdate = mysqli_query($conn, $authuser);
	
    //Delete Database stored session using this code of section [Dinesh @19_July_2021]
    if(isset($_COOKIE[DB_SESSION_NAME]) && !empty($_COOKIE[DB_SESSION_NAME])){
        include_once "Class/custom-session-manager.php";
        $sess_token = $_COOKIE[DB_SESSION_NAME];
        $session_handler = new DbSessionHandler($connPDO);
        $session_handler->destroy($sess_token);

        unset($_COOKIE[DB_SESSION_NAME]);
        setcookie(DB_SESSION_NAME, null, -1, '/');
    }
    setcookie("email_list", "", time() - 3600);
	//setcookie("vtoken", "", time() - 3600);
	setcookie("PHPSESSID", "", time() - 3600);
    unset($_COOKIE['email_list']);
    session_regenerate_id(true);
    // remove all session variables
    session_unset();
    session_destroy();
?>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Logout</title>

</head>
<body onLoad="javascript:location='login.php'">

</body>
</html>