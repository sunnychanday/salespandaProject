<?php
include("../../../includes/global.php");
include(IN_INCLUDES_PATH."connect-new.php");

include("../../../includes/function.php");
$params = $_POST = @file_get_contents("php://input");
header("Strict-Transport-Security:max-age=63072000");
include(MANAGER_PATH . "common_functions.php");
$requestParams = json_decode($params, true);
include("../../controllers/authController.php");

$email_id = testInput($requestParams['email']);
$url = 'https://api.sendgrid.com/';
$failAttempts = getAttempts('MobileappLoginPage');

$newSDomainpath =$sitepath.'manager';

if ($email_id != '') {

    $time = round((strtotime(date('Y-m-d H:i:s')) - strtotime($failAttempts['attempt_on'])) / 3600, 1);
    if ($time > 3) {
        enableAttempts("'LoginPage','MobileappLoginPage'");
    }

    addAttempts('MobileappLoginPage');
    $getAttempts = getAttempts('MobileappLoginPage');

    // echo $getAttempts['attempts'];die;
    if (isset($getAttempts['attempts']) && $getAttempts['attempts'] <= 3) {
        $sql_row = "select * from sp_members where person_email=:person_email and valid=1 and deleted=0";
        $row = $connPDO->prepare($sql_row);
        $row->bindParam(':person_email', $email_id);
        $row->execute();

        $result = $row->fetch();
        $chk_email_approve = $result['approve'];
        $fname = $result['first_name'];
        $lname = $result['last_name'];
        $firstName = $fname . " " . $lname;
        $newMM = base64_encode($result['pid']);
        $clid = base64_encode($result['client_id']);
        $nnewmail = encode($result['person_email']);
        $tmp = 1;

        /*$mailServerSql = mysqli_query($conn,"SELECT * FROM sp_mail_servers WHERE client_id='" . $result['client_id'] . "' and status=1");
        $mailServerCidNum = mysqli_fetch_array($mailServerSql);        
        if ($mailServerCidNum == 0) {
            $mailServerSql = mysqli_query($conn,"SELECT * FROM sp_mail_servers WHERE client_id='' and status=1");
        }
        $mailServer = mysqli_fetch_array($mailServerSql);
        */
        $user =  $sndGdUser;//$mailServer['server_userid'];
        $pass = $pass = $snGdPassword;//$mailServer['server_password'];

        if ($result) {
            if ($tmp == 1 && $chk_email_approve == 1) {
                $resetkey = md5(2418 * 2 + $nnewmail);
                $addKey = substr(md5(uniqid(rand(), 1)), 3, 10);
                $resetkey = $resetkey . $addKey;
                $expFormat = mktime(date("H"), date("i"), date("s"), date("m"), date("d") + 1, date("Y"));
                $expDate = date("Y-m-d H:i:s", $expFormat);

                enableAttempts("'emailphoneInputPage'");  //'MobileappLoginPage',

                $sql_pwdreset = "insert into sp_password_reset set resetkey=:resetkey, 
                                                                    email=:email,
                                                                    expDate=:expDate";
                $pwdreset = $connPDO->prepare($sql_pwdreset);
                $pwdreset->bindParam(':resetkey', $resetkey);
                $pwdreset->bindParam(':email', $email_id);
                $pwdreset->bindParam(':expDate', $expDate);
                $pwdreset->execute();
                $from_email = "info@salespanda.com";
                $mailSubject = "Reset your password";
                $mailid = $email_id;
                $Message = null;
                $emailCotent = "<table border='0' align='center' cellpadding='0' cellspacing='0' style='max-width:600px;'>
				  <tr>
					<td width='20' bgcolor='#45bcd2'>&nbsp;</td>
					<td bgcolor='#45bcd2'>&nbsp;</td>
					<td width='20' bgcolor='#45bcd2'>&nbsp;</td>
				  </tr>
				  <tr>
					<td height='57' align='center' valign='top' bgcolor='#45bcd2'>&nbsp;</td>
					<td align='center' valign='top' bgcolor='#45bcd2' style='font-family: Arial, Helvetica, sans-serif;color:#FFFFFF;font-size:1.8em;font-weight:bold;'><img src='" . $sitepath . "img/mailer/1.jpg' alt='Welcome to SalesPanda' hspace='0' vspace='0' border='0' align='top' /></td>
					<td align='center' valign='top' bgcolor='#45bcd2'>&nbsp;</td>
				  </tr>
				  <tr>
					<td bgcolor='#ececec'>&nbsp;</td>
					<td align='left' valign='top' bgcolor='#ececec' style='font-family: Arial, Helvetica, sans-serif;color:#363737;font-size:12px;line-height:20px;'>&nbsp;</td>
					<td bgcolor='#ececec'>&nbsp;</td>
				  </tr>
				  <tr>
					<td bgcolor='#ececec'>&nbsp;</td>
					<td align='left' valign='top' bgcolor='#ececec' style='font-family: Arial, Helvetica, sans-serif;color:#363737;font-size:12px;line-height:20px;'><p>Dear " . ucfirst($firstName) . ",
					  </p>
					  <p>Thank you for registering on SalesPanda.</p>
					  <p>You are one step away from completing your SalesPanda signup. Please click on the button below to verify your email id:</p>
					  <p>If you are unable to Click on the button above, please cut and paste the following url on your browser: <br />
					  " . $newSDomainpath . "/sp-reset-password.php?key=" . $resetkey . "&email=" . $nnewmail . "&action=reset
					  
					  </p>
					  <p>You can start using SalesPanda after verifying your email ID and unleash the power of Inbound Marketing.</p><br />
					  <p>Best Regards,<br />
						SalesPanda Team </p></td>
					<td bgcolor='#ececec'>&nbsp;</td>
				  </tr>
				  <tr>
					<td bgcolor='#ececec'>&nbsp;</td>
					<td align='center' valign='middle' bgcolor='#ececec' style='font-family: Arial, Helvetica, sans-serif;color:#FFFFFF;font-size:0.8em;line-height:20px;font-weight:bold;'>&nbsp;</td>
					<td bgcolor='#ececec'>&nbsp;</td>
				  </tr>
				  <tr>
					<td height='42' bgcolor='#3e3e3e'>&nbsp;</td>
					<td height='42' align='center' valign='middle' bgcolor='#3e3e3e' style='font-family: Arial, Helvetica, sans-serif;color:#FFFFFF;font-size:0.8em;line-height:20px;font-weight:bold;'><p><img src='" . $sitepath . "img/mailer/5.jpg' alt='SalesPanda - Inbound Marketing Software' hspace='0' vspace='0' border='0' align='middle' /></p></td>
					<td height='42' bgcolor='#3e3e3e'>&nbsp;</td>
				  </tr>
			</table>";
                $Message = null;
                $Message = $emailCotent;
                $json_string = array(
                    'to' => array($mailid)
                );

                $params = array(
                    'api_user' => $user,
                    'api_key' => $pass,
                    'x-smtpapi' => json_encode($json_string),
                    'to' => $mailid,
                    'subject' => $mailSubject,
                    'html' => $Message,
                    'text' => 'Reset your password',
                    'from' => $from_email,
                );
                if( @$mailServer['mail_server'] == 'sendgrid' ) {
                    $request = $url . 'api/mail.send.json';
                    $session = curl_init($request);
                    curl_setopt($session, CURLOPT_POST, true);
                    curl_setopt($session, CURLOPT_POSTFIELDS, $params);
                    curl_setopt($session, CURLOPT_HEADER, false);
                    curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
                    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

                    // obtain response
                    $response = curl_exec($session);
                    //~ echo '<pre>';
                     //print_r($response);die;
                    curl_close($session);
                  } else {

                      $result = (object) netcore_email($params, $transactional_password);

                      $response = (object) $result->message;
                  }
                // print everything out
                $response;
                
                $msg = "Please check $mailid to get reset password link.";
            } else {
                
                $emailCotent2 = "<table border='0' align='center' cellpadding='0' cellspacing='0' style='max-width:600px;'>
				  <tr>
					<td width='20' bgcolor='#45bcd2'>&nbsp;</td>
					<td bgcolor='#45bcd2'>&nbsp;</td>
					<td width='20' bgcolor='#45bcd2'>&nbsp;</td>
				  </tr>
				  <tr>
					<td height='57' align='center' valign='top' bgcolor='#45bcd2'>&nbsp;</td>
					<td align='center' valign='top' bgcolor='#45bcd2' style='font-family: Arial, Helvetica, sans-serif;color:#FFFFFF;font-size:1.8em;font-weight:bold;'><img src='" . $sitepath . "img/mailer/1.jpg' alt='Welcome to SalesPanda' hspace='0' vspace='0' border='0' align='top' /></td>
					<td align='center' valign='top' bgcolor='#45bcd2'>&nbsp;</td>
				  </tr>
				  <tr>
					<td bgcolor='#ececec'>&nbsp;</td>
					<td align='left' valign='top' bgcolor='#ececec' style='font-family: Arial, Helvetica, sans-serif;color:#363737;font-size:12px;line-height:20px;'>&nbsp;</td>
					<td bgcolor='#ececec'>&nbsp;</td>
				  </tr>
				  <tr>
					<td bgcolor='#ececec'>&nbsp;</td>
					<td align='left' valign='top' bgcolor='#ececec' style='font-family: Arial, Helvetica, sans-serif;color:#363737;font-size:12px;line-height:20px;'><p>Dear " . ucfirst($firstName) . ",
					  </p>
					  <p>Thank you for registering on SalesPanda.</p>
					  <p>You are one step away from completing your SalesPanda signup. Please click on the button below to verify your email id:</p>
					  <p style='margin:10px 0 10px 0;'><a href='" . $newSDomainpath . "/password-generate.php?userid=" . $newMM . "&email=" . $nnewmail . "&clid=" . $clid . "' rel='nofollow'><img style='cursor:pointer;' src='" . $sitepath . "images/mailer/verify-btn.png' /></a></p>
					  <p>If you are unable to Click on the button above, please cut and paste the following url on your browser: <br />
					  " . $newSDomainpath . "/password-generate.php?userid=" . $newMM . "&email=" . $nnewmail . "&clid=" . $clid . "
					  
					  </p>
					  <p>You can start using SalesPanda after verifying your email ID and unleash the power of Inbound Marketing.</p><br />
					  <p>Best Regards,<br />
						SalesPanda Team </p></td>
					<td bgcolor='#ececec'>&nbsp;</td>
				  </tr>
				  <tr>
					<td bgcolor='#ececec'>&nbsp;</td>
					<td align='center' valign='middle' bgcolor='#ececec' style='font-family: Arial, Helvetica, sans-serif;color:#FFFFFF;font-size:0.8em;line-height:20px;font-weight:bold;'>&nbsp;</td>
					<td bgcolor='#ececec'>&nbsp;</td>
				  </tr>
				  <tr>
					<td height='42' bgcolor='#3e3e3e'>&nbsp;</td>
					<td height='42' align='center' valign='middle' bgcolor='#3e3e3e' style='font-family: Arial, Helvetica, sans-serif;color:#FFFFFF;font-size:0.8em;line-height:20px;font-weight:bold;'><p><img src='" . $sitepath . "img/mailer/5.jpg' alt='SalesPanda - Inbound Marketing Software' hspace='0' vspace='0' border='0' align='middle' /></p></td>
					<td height='42' bgcolor='#3e3e3e'>&nbsp;</td>
				  </tr>
			</table>";

                $Message = null;
                $Message = $emailCotent2;
                $ToSubject = "Please verify email id provided to SalesPanda";
                $from_email = "info@salespanda.com";

                $json_string = array(
                    'to' => array($email_id)
                );

                $params = array(
                    'api_user' => $user,
                    'api_key' => $pass,
                    'x-smtpapi' => json_encode($json_string),
                    'to' => $email_id,
                    'subject' => $ToSubject,
                    'html' => $Message,
                    'text' => 'Please verify email id provided to SalesPanda',
                    'from' => $from_email,
                );

                if( @$mailServer['mail_server'] == 'sendgrid' ) { 
                    $request = $url . 'api/mail.send.json';

                    $session = curl_init($request);
                    curl_setopt($session, CURLOPT_POST, true);
                    curl_setopt($session, CURLOPT_POSTFIELDS, $params);
                    curl_setopt($session, CURLOPT_HEADER, false);
                    curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
                    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

                    // obtain response
                    $response = curl_exec($session);
                    //print_r($response);
                    curl_close($session);
                  } else {

                      $result = (object) netcore_email($params, $transactional_password);

                      $response = (object) $result->message;
                  }
                $msg = "Your email is not approved.Please check your email Id for approval.";
            }
        } else {
          
            $msg = "Email-id entered is not registered on SalesPanda. Please enter a valid email-id.";
        }
        //mysql_close($conn);
        $arr = array("statusCode" => "200", "status" => "Success", "message" => $msg);
    } else {
        $msg = "You have reached maximum login attempt limit. Please try after 30 minutes.";
        $arr = array("statusCode" => "403", "status" => "Failed", "message" => $msg);
    }
} else {
    $arr = array("statusCode" => "403", "status" => "Failed", "message" => "Enter valid data");
}

header('Content-Type: application/json');
echo json_encode($arr, JSON_UNESCAPED_SLASHES);
