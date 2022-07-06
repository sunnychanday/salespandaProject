<?php

require realpath(__DIR__ . '/vendor/autoload.php');

include("includes/global.php");
include("includes/connect-new.php");
include("includes/function.php");
include("manager/common_functions.php");
include("csrf/csrf-magic.php");

$newSDomainpath = "{$sitepath}manager";

$request_email = $_REQUEST['email'] ?? '';
$email_id = testInput($request_email);

$rowCount = 0;
if ($email_id !== '') {
    $sql_row = "select * from sp_members where person_email = :person_email and valid = 1 and deleted = 0";
    $row = $connPDO->prepare($sql_row);
    $row->bindParam(':person_email', $email_id);
    $row->execute();
    $result = $row->fetch();

    $chk_email_approve = $result['approve'];
    $fname = $result['first_name'];
    $lname = $result['last_name'];
    $firstName = "{$fname} {$lname}";
    $newMM = base64_encode($result['pid']);
    $clid = base64_encode($result['client_id']);
    $nnewmail = encode($result['person_email']);
    $tmp = 1;
    $row->execute();
    $rowCount = $row->rowCount();
}

if ($rowCount > 0) {
    $user = $sndGdUser;   //$mailServer['server_userid'];
    $pass = $snGdPassword;  //$mailServer['server_password'];

    if ($tmp == 1 && $chk_email_approve == 1) {
        $resetkey = md5((2418 * 2) + $nnewmail);
        $addKey = substr(md5(uniqid(rand(), 1)), 3, 10);
        $resetkey = $resetkey . $addKey;
        
        $curDate = date("Y-m-d H:i:s");
        $expDate = date("Y-m-d H:i:s", strtotime($curDate . "+30 minutes"));
        $ipaddress = CommonStaticFunctions::get_remote_user_ip();

        $from_email = "info@salespanda.com";
        $mailSubject = "Reset your password";

        $mailid = $email_id;
        $Message = null;

        $sqlattempts = "select * from sp_password_reset where email = :email and ip_address = :ip_address";
        $rowattempts = $connPDO->prepare($sqlattempts);
        $rowattempts->bindParam(':email', $email_id);
        $rowattempts->bindParam(':ip_address', $ipaddress);
        $rowattempts->execute();
        $resultattempts = $rowattempts->fetch(PDO::FETCH_ASSOC);
        addAttempts('ForgotPasswordPage');
        $getotpAttempts = getAttempts('ForgotPasswordPage');
        if ($getotpAttempts['attempts'] >= 3) {

            $_SESSION['errmsg'] = 'You have reached maximum  attempt limit. Please try after 30 minutes.';
            header("location:forget-password.php");
            exit;
        }
        if (!isset($resultattempts['attempts']) || $resultattempts['attempts'] == 0) {
            $sql_pwdreset = "insert into sp_password_reset set attempts = 1, resetkey = :resetkey, email = :email, ip_address = :ip_address, expDate = :expDate";
            $pwdreset = $connPDO->prepare($sql_pwdreset);
            $pwdreset->bindParam(':resetkey', $resetkey);
            $pwdreset->bindParam(':email', $email_id);
            $pwdreset->bindParam(':ip_address', $ipaddress);
            $pwdreset->bindParam(':expDate', $expDate);
            $pwdreset->execute();
        } else {
            $attempt_field = (strtotime('now') >= strtotime($resultattempts['expDate'])) ? "`attempts` = 1" : "`attempts` = `attempts` + 1";
            $expDate = (strtotime('now') >= strtotime($resultattempts['expDate'])) ? $expDate : $resultattempts['expDate'];

            $sql_pwdreset = "UPDATE `sp_password_reset` SET {$attempt_field}, `resetkey` = :resetkey, `expDate` = :expDate WHERE `email` = :email AND `ip_address` = :ip_address";
            $pwdreset = $connPDO->prepare($sql_pwdreset);
            $pwdreset->bindParam(':resetkey', $resetkey);
            $pwdreset->bindParam(':expDate', $expDate);
            $pwdreset->bindParam(':email', $email_id);
            $pwdreset->bindParam(':ip_address', $ipaddress);
            $pwdreset->execute();
        }

        $rowattempts->execute();
        $resultattempts = $rowattempts->fetch(PDO::FETCH_ASSOC);

        if ($resultattempts['attempts'] <= 3 && strtotime('now') <= strtotime($resultattempts['expDate'])) {
            $json_string = array(
                'to' => array($mailid)
            );

            $post_arr = [
                "firstName" => $firstName,
                "newSDomainpath" => $newSDomainpath,
                "resetkey" => $resetkey,
                "nnewmail" => $nnewmail,
                "sitepath" => $sitepath
            ];

            $tpl_url = "{$sitepath}template/mailer/register_user_reset_password_v2.php";
            $params = array(
                'api_user' => $user,
                'api_key' => $pass,
                'x-smtpapi' => json_encode($json_string),
                'to' => $mailid,
                'subject' => $mailSubject,
                'html' => get_mailer_template($tpl_url, $post_arr),
                'text' => 'Reset your password',
                'from' => $from_email
            );

            if ($mailServer['mail_server'] == 'sendgrid') {
                $params_transactional = [
                    "to" => $mailid,
                    "from" => [$from_email => "SalesPanda"],
                    "subject" => $mailSubject,
                    "text" => 'Reset your password',
                    "html" => get_mailer_template($tpl_url, $post_arr),
                    "custom_args" => ["server_name" => $_SERVER['HTTP_HOST']],
                    "categories" => ['reset-password', 'forgot-password']
                ];

                try {
                    $sendgrid = new SPMailer\SendGridMailer($snGdPassword, 'transactional');
                    $status = $sendgrid->send($params_transactional);
                    $response = json_decode($status);
                } catch (Exception $e) {
                    $response = (object) ["code" => $e->getCode(), "message" => $e->getMessage(), "file" => $e->getFile(), "line" => $e->getLine()];
                }
            } else {
                $result = (object) netcore_email($params, $transactional_password);
                $response = (object) $result->message;

                if (isset($response->status) && strtolower($response->status) === 'success') {
                    $response->message = 'success';
                }
            }

            if ((isset($response->message) && strtolower($response->message) === 'success')) {
                $success_status = true;
            }

            if ($success_status === true) {
                $_SESSION['successmsg'] = "You have requested for reset password in your $mailid id with expiry link.";
                header("location:forget-password.php");
                exit;
            } else {
                $_SESSION['errmsg'] = 'Please Try Again Later...';
                header("location:forget-password.php");
                exit;
            }
        } else {
            $_SESSION['successmsg'] = "You have already requested for reset password in your $mailid id with expiry link.";
            header("location:forget-password.php");
            exit;
        }
    } else {
        $ToSubject = "Please verify email id provided to SalesPanda";
        $from_email = "info@salespanda.com";

        $json_string = array(
            'to' => array($email_id),
            'sub' => array("-campId-" => 0)
        );

        $post_arr = array(
            "sitepath" => $sitepath,
            "newSDomainpath" => $newSDomainpath,
            "firstName" => $firstName,
            "nnewmail" => $nnewmail,
            "newMM" => $newMM,
            "clid" => $clid,
            "resetkey" => $resetkey
        );

        $tpl_url = "{$sitepath}template/mailer/reset_password.php";
        $params = array(
            'api_user' => $user,
            'api_key' => $pass,
            'x-smtpapi' => json_encode($json_string),
            'to' => is_array($email_id) ? $email_id : [$email_id],
            'subject' => $ToSubject,
            'html' => get_mailer_template($tpl_url, $post_arr),
            'text' => 'Please verify email id provided to SalesPanda',
            'from' => $from_email
        );

        if ($mailServer['mail_server'] == 'sendgrid') {

            $params_transactional = [
                "to" => $email_id,
                "from" => [$from_email => "SalesPanda"],
                "subject" => $ToSubject,
                "text" => 'Please verify email id provided to SalesPanda',
                "html" => get_mailer_template($tpl_url, $post_arr),
                "custom_args" => ["server_name" => $_SERVER['HTTP_HOST']],
                "categories" => ['reset-password', 'forgot-password']
            ];

            try {
                $sendgrid = new SPMailer\SendGridMailer($snGdPassword, 'transactional');
                $status = $sendgrid->send($params_transactional);
                $response = json_decode($status);
            } catch (Exception $e) {
                $response = (object) ["code" => $e->getCode(), "message" => $e->getMessage(), "file" => $e->getFile(), "line" => $e->getLine()];
            }
        } else {
            $result = (object) netcore_email($params, $transactional_password);
            $response = (object) $result->message;

            if (isset($response->status) && strtolower($response->status) === 'success') {
                $response->message = 'success';
            }
        }

        if ((isset($response->message) && strtolower($response->message) === 'success')) {
            $success_status = true;
        }

        if ($success_status === true) {
            /*** Email for Registered User END ***/
            $_SESSION['errmsg'] = 'Your email is not approved.Please check your email Id for approval.';
            header("location:forget-password.php");
            exit;
        } else {
            $_SESSION['errmsg'] = 'Please Try Again Later...';
            header("location:forget-password.php");
            exit;
        }
    }
} else {
    $getotpAttempts = getAttempts('ForgotPasswordPage');
    if ($getotpAttempts['attempts'] >= 3) {

        $_SESSION['errmsg'] = 'You have reached maximum  attempt limit. Please try after 30 minutes.';
        header("location:forget-password.php");
        exit;
    }
    addAttempts('ForgotPasswordPage');
    $_SESSION['errmsg'] = 'Email-id entered is not registered on SalesPanda. Please enter a valid email-id.';
    header("location:forget-password.php");
    exit;
}
