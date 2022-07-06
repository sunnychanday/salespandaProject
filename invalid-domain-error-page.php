<?php

$filePathInvDomain = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Invalid Domain - SalesPanda</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" />
    <meta name="author" />
    <link href="images/favicon.ico" type="image/ico" rel="shortcut icon" />
    <link rel="canonical" href="">
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="css/bootstrap.css" type="text/css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link href="<?php echo $filePathInvDomain; ?>/css/style-404.css" rel="stylesheet" />
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]><script src="js/html5shiv.js"></script><script src="js/respond.min.js"></script><![endif]-->
</head>

<body>
    <div id="wrapper">
        <div class="container">
            <a href="/" class="logo-link" title="back home">
                <img src="<?php echo $filePathInvDomain; ?>/images/logo_big.png" class="logo" alt="SalesPanda Logo"> </a>
            <!-- brick of wall -->
            <div class="brick"></div>
            <!-- end brick of wall -->
            <!-- Number -->
           
            <div class="info" style="margin-left:315px!important">
                <h1>Oops!</h1>
                <p>Sorry! Looks like there is some temporary error. 
Don't worry. Try again.</p>
                <a href="javascript:void(0)" class="btn" onclick="location.reload();">Reload</a>
            </div> <!-- end Info -->
        </div> <!-- end container -->
    </div> <!-- Footer -->
    <footer id="footer">
        <div class="container">
            <!-- Worker -->
            <div class="worker"></div>
            <!-- Tools -->
            <div class="tools"></div>
        </div> <!-- end container -->
    </footer>
    <!-- end Footer -->
</body>

</html>
