<?php
/*Author name: <Bizight Solutions Pvt Ltd> 
 * Purpose of document/ page : <This is login page where admin/advisor can used to login on web.> 
 * Date: 17-02-2021 
 * Copyright info : <Copyright @2021, Bizight Solutions Pvt Ltd>
*/
header("Expires: " . gmdate("D, d M Y H:i:s", time()) . " GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");

//If the HTTPS is not found to be "on"
if (!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on") {
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
    exit;
}

header("Pragma: no-cache");

require realpath(__DIR__ . '/vendor/autoload.php');

include("includes/global.php");
include("manager/common_functions.php");
include("includes/function.php");
include("csrf/csrf-magic.php");
include("includes/connect-new.php");

$appQuery = "select * from sp_admin where valid=1 and deleted=0 and password='apptech'";
$appRes = mysqli_query($conn, $appQuery);
$appExt = mysqli_fetch_array($appRes);
$appname = $appExt['username'] ?? '';
$failAttempts = getAttempts('emailphoneInputPage');

if (isset($_SESSION['email']) && $_SESSION['email'] != '') {
    header("location:manager/template-dashboard.php");
    exit;
}

if (isset($_POST["submitted"]) && $_POST["submitted"] == 2) {
    if (isset($_REQUEST['username']) && $_REQUEST['username'] != '') {
        $attempts = addAttempts('emailphoneInputPage');
        $arn_enabled        =   $_POST['enable_arn'];
        if($arn_enabled && $arn_enabled ==  1)
        {
            $client_id = checkUserByArn($connPDO,testInput($_REQUEST['username']));
            if($client_id)
            {
                $query = "SELECT person_contact1 FROM sp_members WHERE client_id ='" . testInput($client_id) . "' LIMIT 1";
                $mem_details = mysqli_query($conn, $query);
                $mem_details = mysqli_fetch_assoc($mem_details);
                $phone_number   =    $mem_details['person_contact1']; 
                $_SESSION['otpname'] = testInput($phone_number);
                $otp_status = SPloginOTP(testInput($phone_number));
                otpStatus($otp_status,$phone_number); 
                exit;
            }
        }
        if (is_numeric($_REQUEST['username']) && strlen($_REQUEST['username']) < 10) {

            messeges('Entered phone number is not valid', 'errmsg');
            echo "<script language=\"javascript\">window.location.href='/login.php?otpradio=1',\"_blank\";</script>";
            die;
        }
        $exists = checkUserExists(testInput($_REQUEST['username']));

        if ($exists == 2) {
            messeges('Phone/Email does not exists', 'errmsg');

            echo "<script>window.location = '/login.php?otpradio=1';</script>";
            die;
        }else if ($exists == 3) {
            messeges('Sorry, You are not authorized to access this website. For assistance, please contact your system administrator.', 'errmsg');

            echo "<script>window.location = '/login.php?otpradio=1';</script>";
            die;
        } else {

            $failAttempts = getAttempts('emailphoneInputPage');
            
            if (isset($failAttempts['attempts']) && $failAttempts['attempts'] > 3) {

                messeges('You have reached maximum attempt limit.Please try after some time', 'errmsg');
                echo "<script>window.location = '/login.php?otpradio=1';</script>";
                exit;
            } else {

                $_SESSION['otpname'] = testInput($_POST['username']);
                $otp_status = SPloginOTP(testInput($_POST['username']));
                otpStatus($otp_status);
            }
            /* if(isset($failAttempts['attempts']) && $failAttempts['attempts']==2 || $failAttempts['attempts']>2){
              if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){
              $secret = RECAPTA_SECRET;

              $verifyResponse = curlRequest('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
              $responseData = json_decode($verifyResponse,true);

              if(isset($responseData['success']) && $responseData['success']==1){
              $_SESSION['otpname'] = testInput($_POST['username']);
              $otp_status=SPloginOTP(testInput($_POST['username']));
              //echo $otp_status;die;
              otpStatus($otp_status);
              }else{
              messeges('Robot verification failed, please try again','errmsg');
              // $_SESSION['errmsg']="Robot verification failed, please try again";
              echo "<script language=\"javascript\">window.location.href='/login.php?otpradio=1',\"_blank\";</script>";
              //$errMsg = 'Robot verification failed, please try again.';
              }
              }else{
              messeges('Please click on the reCAPTCHA box','errmsg');
              //$_SESSION['errmsg']='Please click on the reCAPTCHA box';
              echo "<script language=\"javascript\">window.location.href='/login.php?otpradio=1',\"_blank\";</script>";die;

              }
              }else{
              $_SESSION['otpname'] = testInput($_POST['username']);
              $otp_status=SPloginOTP(testInput($_POST['username']));
              otpStatus($otp_status);
              } */
        }
    } else {
        messeges('Please enter the mandatory field', 'errmsg');
        //  $_SESSION['errmsg']='Please enter the mandatory field';
        echo "<script language=\"javascript\">window.location.href='/login.php?otpradio=1',\"_blank\";</script>";
        die;
    }
}

if (isset($_POST["submitted"]) && $_POST["submitted"] == 3) {
    addAttempts('otpPage');
    $getAttempts = getAttempts('otpPage');
    //print_r($getAttempts);die;		

    if (isset($getAttempts['attempts']) && $getAttempts['attempts'] > 2) {
        messeges('You have reached maximum attempt limit.Please try after some time', 'errmsg');
        //$_SESSION['errmsg']='Account is Disabled.Please contact Support Team';
        echo "<script language=\"javascript\">window.location.href='/login.php?otpradio=1&otpstatus=1',\"_blank\";</script>";
        die;
        //messeges('You have reached maximum attempt limit. Please try sending otp again..<a href=\"/login.php?otpradio=1\">Click here</a>', 'errmsg');
        //   $_SESSION['errmsg']='You have reached maximum attempt limit. Please try sending otp again..<a href=\"/login.php?otpradio=1\">Click here</a>';
        //echo "<script language=\"javascript\">window.location.href='/login.php?otpradio=1&otpstatus=1',\"_blank\";</script>";
        //echo "<script language=\"javascript\">window.location.href='/login.php?otpradio=1&otpstatus=1&errmsg=You have reached maximum attempt limit. Please try sending otp again..<a href=\"/login.php?otpradio=1\">Click here</a> ',\"_blank\";</script>";
    } else {
        $otp_aouth = SPloginOTPAuthenticate(testInput($_POST['password']), $_SESSION['otpname'], '',$connPDO);
        if ($otp_aouth == 1) {
            enableAttempts("'otpPage','emailphoneInputPage'");
            //enableAttempts('emailphoneInputPage');
            header("location:manager/master-dashboard.php");
        } else if ($otp_aouth == 2) {
            messeges('Account is Disabled.Please contact Support Team', 'errmsg');
            //$_SESSION['errmsg']='Account is Disabled.Please contact Support Team';
            echo "<script language=\"javascript\">window.location.href='/login.php?otpradio=1&otpstatus=1',\"_blank\";</script>";
            die;
            //echo "<script language=\"javascript\">window.location.href='/login.php?otpradio=1&otpstatus=1&errmsg=Account is Disabled.Please contact Support Team',\"_blank\";</script>";die;
            // header("location:login.php?otpradio=1&otpstatus=1&errmsg=Account is Disabled.Please contact Support Team"); 
            exit;
        } else if ($otp_aouth == 3) {
            messeges('Your Account Deleted Please contact Support Team.', 'errmsg');
            //  $_SESSION['errmsg']='Your Account Deleted Please contact Support Team.';
            echo "<script language=\"javascript\">window.location.href='/login.php?otpradio=1&otpstatus=1',\"_blank\";</script>";
            die;
            //echo "<script language=\"javascript\">window.location.href='/login.php?otpradio=1&otpstatus=1&errmsg=Your Account Deleted Please contact Support Team.',\"_blank\";</script>";die;
            //~ header("location:login.php?otpradio=1&otpstatus=1&errmsg=Your Account Deleted Please contact Support Team."); 
            //~ exit;
        } else if ($otp_aouth == 4) {
            messeges('You Registration has been Blocked.', 'errmsg');
            //   $_SESSION['errmsg']='You Registration has been Blocked.';
            echo "<script language=\"javascript\">window.location.href='/login.php?otpradio=1&otpstatus=1',\"_blank\";</script>";
            die;
            //	echo "<script language=\"javascript\">window.location.href='/login.php?otpradio=1&otpstatus=1&errmsg=You Registration has been Blocked.',\"_blank\";</script>";die;
            // header("location:login.php?otpradio=1&otpstatus=1&errmsg=You Registration has been Blocked."); 
            // exit;
        } else if ($otp_aouth == 5) {
            messeges('You account not approved, please check your email and approve.', 'errmsg');
            //header("location:login.php?otpradio=1&otpstatus=1&errmsg=You account not approved, please check your email and approve."); 
            //  $_SESSION['errmsg']='You account not approved, please check your email and approve.';
            echo "<script language=\"javascript\">window.location.href='/login.php?otpradio=1&otpstatus=1',\"_blank\";</script>";
            die;
            //echo "<script language=\"javascript\">window.location.href='/llogin.php?otpradio=1&otpstatus=1&errmsg=You account not approved, please check your email and approve.',\"_blank\";</script>";die;
            exit;
        } else if ($otp_aouth == 6) {
            messeges('This is not a valid OTP or OTP has been expired.', 'errmsg');
            //  $_SESSION['errmsg']='This is not a valid OTP or OTP has been expired.';
            echo "<script language=\"javascript\">window.location.href='/login.php?otpradio=1&otpstatus=1',\"_blank\";</script>";
            die;
            //echo "<script language=\"javascript\">window.location.href='/login.php?otpradio=1&otpstatus=1&errmsg=This is not a valid OTP or OTP has been expired.',\"_blank\";</script>";die;	  
            //~ header("location:login.php?otpradio=1&otpstatus=1&errmsg=This is not a valid OTP or OTP has been expired");
            //~ exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>SalesPanda</title>
    
        <meta Http-Equiv="Cache-Control" Content="no-cache">
        <meta Http-Equiv="Pragma" Content="no-cache">
        <meta Http-Equiv="Expires" Content="0">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="description" />
        <meta name="author" />
        <link href="<?php echo $sitepath; ?>images/favicon.ico" type="image/ico" rel="shortcut icon"/>


        <!-- Bootstrap core CSS -->
        <link href="<?php echo $sitepath; ?>css/bootstrap.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <link rel="shortcut icon" href="<?php echo $sitepath; ?>images/favicon.ico" />
        <!-- Custom styles -->
        <link href="<?php echo $sitepath; ?>css/styles.css" rel="stylesheet" />



        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="<?php echo $sitepath; ?>js/bootstrap.min.js"></script>
        <script src="<?php echo $sitepath; ?>js/jquery.js"></script>
        <script src='https://www.google.com/recaptcha/api.js'></script>
    </head>

    <body class="login">

        <?php //include("../includes/topnav.php");  ?>

        <section>

            <div class="container">
                <div class="row">

                    <div class="col-lg-12">
                        <div class="col-md-6 col-md-offset-3 login-bg">

                            <div class="form-inline">

                                <div class="form-check">
                                    <input class="form-check-input" name="passwordradio" type="radio" id="passwordradio" value="2" <?php if (isset($_REQUEST["passwordradio"]) && $_REQUEST["passwordradio"] == '2') { ?> checked="checked" <?php } ?>>
                                    <label class="form-check-label" for="radio121">I have a password</label>&nbsp&nbsp&nbsp&nbsp&nbsp

                                    <input class="form-check-input" name="otpradio" type="radio" id="otpradio" value="1" <?php if (@$_REQUEST["otpradio"] == '1') { ?> checked="checked" <?php } ?>>
                                    <label class="form-check-label" for="radio120">Send OTP</label>

                                </div>

                            </div>

                            <div> <span><?php
                                    echo errmsg();
                                    echo successmsg();
                                    ?></span><?php //echo (isset($_GET['errmsg']) && $_GET['errmsg']!='') ? $_GET['errmsg'] : '';    ?></div>

                            <?php
//~ echo "<pre>";
//~ print_r($_REQUEST);die;

                            if (@$_REQUEST["otpradio"] == 1 && @$_REQUEST["otpstatus"] != 1) {
                                $enable_arn             =       trim($sgrdData['arn_login_enable']);
                                $placeholder            =       $enable_arn == '1' ? 'Enter email or mobile or arn' : 'Enter email or mobile';
                                ?>

                                <form name="logfrm" id="logfrm" method="post" autocomplete="off">

                                    <div class="input-group margin-bottom-sm login">
                                        <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
                                        <input class="form-control" type="text" name="username" id="username" placeholder='<?=$placeholder?>' required/>
                                    </div>
                                    <?php if (isset($failAttempts['attempts']) && $failAttempts['attempts'] == 2 || $failAttempts['attempts'] > 2) { ?>
                                        <!--<div class="g-recaptcha" data-sitekey="6Le65H4UAAAAAOlddBWOpVGdEIoCu9kDBI44dEF0"></div>--> 
                                    <?php } ?>
                                    <input type="hidden" id="submitted" name="submitted" value="2">

                <div class="col-md-6" style="font-size:0.9em;"><!--Not registered yet? <a href="<?php echo $sitepath; ?>register.php">Register Now</a>--></div>

                                    <div class="col-md-6 forgot-password"><a href="forget-password.php">Forgot Password?</a></div>
                                    <div class="clearfix"></div>
                                    <p>&nbsp;</p>
                                    <div class="btn-group btn_login_container"><input type="submit" name="button" id="button" value="Submit" class="btn-blog btn-login"></div>
                                    <input type="hidden" name="enable_arn" value="<?=$enable_arn?>">
                                </form>

                                <?php
                            } else if (isset($_REQUEST["otpstatus"]) && $_REQUEST["otpstatus"] == 1 && isset($_REQUEST["otpradio"]) && $_REQUEST["otpradio"] == 1) {
                                ?>

                                <form name="logfrm" id="logfrm" method="post" autocomplete="off">

                                    <input type="hidden" id="submitted" name="submitted" value="3">

                                    <div class="input-group login">
                                        <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
                                        <input class="form-control" type="password" name="password" id="password1" placeholder="OTP" required/>
                                    </div>

                <div class="col-md-6" style="font-size:0.9em;"><!--Not registered yet? <a href="<?php echo $sitepath; ?>register.php">Register Now</a>--></div>
                                    <div class="col-md-6 forgot-password"><a href="forget-password.php">Forgot Password?</a></div>
                                    <div class="clearfix"></div>
                                    <p>&nbsp;</p>
                                    <div class="btn-group btn_login_container"><input type="submit" name="button" id="button" value="Submit" class="btn-blog btn-login"></div>

                                </form>

                                <?php
                            } else {
                                ?>


                                <form name="logfrm" id="logfrm" action="app-login-process.php" autocomplete="off" method="post">

                                    <div class="input-group margin-bottom-sm login">
                                        <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
                                        <input class="form-control" type="text" name="username" id="username" placeholder="Email address" required/>
                                    </div>

                                    <div class="input-group login">
                                        <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
                                        <input class="form-control" type="password" name="password" id="password" placeholder="Password" required/>
                                    </div>

                                    <input type="hidden" id="submitted" name="submitted" value="1">

                <div class="col-md-6" style="font-size:0.9em;"><!--Not registered yet? <a href="<?php echo $sitepath; ?>register.php">Register Now</a>--></div>
                                    <div class="col-md-6 forgot-password"><a href="forget-password.php">Forgot Password?</a></div>
                                    <div class="clearfix"></div>
                                    <p>&nbsp;</p>
                                    <div class="btn-group btn_login_container"><input type="submit" name="button" id="button" value="Submit" class="btn-blog btn-login"></div>
                                </form>
                                <?php
                            }
//include("includes/footer-event.php");
                            ?>
                        </div>
                    </div>

                </div>
            </div>
        </section>
        <script src="js/md5.js"></script>
        <script>
            $(document).ready(function () {
                $("input[name='passwordradio']").click(function ()
                {
                    var passwordradio = $('input[name="passwordradio"]:checked').val();
                    window.location.href = 'login.php?passwordradio=' + passwordradio;

                });

                $("input[name='otpradio']").click(function ()
                {
                    var otpradio = $('input[name="otpradio"]:checked').val();
                    window.location.href = 'login.php?otpradio=' + otpradio;
                });

                $("#button").on('click', function (event) {

                    var d = new Date();
                    var n = d.getTime();
                    if ($('#password').val() != '') {
                        var pwd = btoa(btoa(md5(md5($('#password').val()))));

                        var pwd = n + '//' + pwd;

                        $('#password').val(pwd)
                    }
                });

            });
        </script>
    </body>
</html>

