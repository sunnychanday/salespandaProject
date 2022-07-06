<?php
include("includes/global.php");
include("includes/function.php");
include("manager/common_functions.php");
include("csrf/csrf-magic.php");
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Forget Password | SalesPanda</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="description" content="Forget Password - Try new password to login at Salespanda.com" />
<meta name="author" />
<link href="<?php echo $sitepath; ?>images/favicon.ico" type="image/ico" rel="shortcut icon"/>


<!-- Bootstrap core CSS -->
<link href="<?php echo $sitepath; ?>css/bootstrap.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
<link rel="shortcut icon" href="<?php echo $sitepath; ?>images/favicon.ico" />
<!-- Custom styles -->
<link href="<?php echo $sitepath; ?>css/styles.css" rel="stylesheet" />

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="<?php echo $sitepath; ?>js_newwebsite/html5shiv.js"></script>
<script src="<?php echo $sitepath; ?>js_newwebsite/respond.min.js"></script>
<![endif]-->

</head>

<body class="login">

<!-- NAVBAR -->
<nav class="navbar navbar-default hidden" role="navigation">
<div class="container">

	<div class="navbar-header">
	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-nav">
	<span class="sr-only">Toggle Navigation</span>
	<i class="fa fa-bars"></i>
	</button>
	<a href="index.php"><img src="<?php echo $sitepath; ?>images/logo_big.png" alt="SalesPanda" class="img-responsive"></a>
	</div>


</div>
</nav>
<!-- END NAVBAR -->

<section>

<div class="container">
<div class="row">

<div class="col-lg-12">
	<div class="col-md-6 col-md-offset-3 login-bg">
        <span><?php errmsg();successmsg();?></span>
         <span>Enter your registered email-id to reset password.</span>
         <form name="fgpass" id="fgpass" action="forget-password-process.php" method="post" autocomplete="off">        
	<div class="input-group margin-bottom-sm login">
	<span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
	<input class="form-control" type="text" name="email" id="email" placeholder="Email address" required>
	</div>
	<div class="btn-group btn_login_container"><input type="submit" class="btn-blog btn-login" name="button" id="button" value="Submit">
         <a href="login.php" class="btn-blog btn-login">« Back</a>     
      </div>
        </form>
	</div>
</div>

</div>
</div>
</section>
</body></html>
