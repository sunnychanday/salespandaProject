<?php
	
	include("../includes/global.php");
	include("../csrf/csrf-magic.php");
    //unset($_COOKIE['email_list']);
	setcookie("email_list", "", time() - 3600);
	setcookie("vtoken", "", time() - 3600);
	setcookie("PHPSESSID", "", time() - 3600);
	session_destroy();
?>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Logout</title>

</head>
<body onLoad="javascript:location='login.php'">

</body>
</html>