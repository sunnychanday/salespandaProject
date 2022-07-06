<?php

require realpath(__DIR__ . '/vendor/autoload.php');

include("includes/global.php");
include("includes/function.php");
include("includes/global-url.php");
include("includes/common_php_functions.php");

include("../includes/netcore-email.php");

//include("Class/mailgunSmtp.php");
//include("Class/sendMail.php");

$mailServer = getServerDetails();
header('Access-Control-Allow-Origin: *');
$url = 'https://api.sendgrid.com/';
$user = $sndGdUser;
$pass = $snGdPassword;
$doe1 = date("Y-m-d");

//echo $_COOKIE['VTOKEN']; exit;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $leadInserted = '';
    $doe = date("Y-m-d h:i:s");
    $form_id = $_REQUEST['form_id'];
    $referral_url = $_REQUEST['referral_url1'];
    $pcmember_type = $_REQUEST['pcmember_type1'];
    $c_client_id = $_REQUEST['c_client_id1'];

    if ($_POST['fname1'] != '' or $_POST['fname1'] != 'undefined') {
        if ($_POST['fname1'] != 'undefined') {
            $fn = mysqli_real_escape_string($conn, $_POST['fname1']);
            $fn = bizight_sp_encryption($fn);
        } else {
            $fn == '';
        }
    }

    if ($_POST['lname1'] != '' or $_POST['lname1'] != 'undefined') {
        if ($_POST['lname1'] != 'undefined') {
            $ln = mysqli_real_escape_string($conn, $_POST['lname1']);
            $ln = bizight_sp_encryption($ln);
        } else {
            $ln == '';
        }
    }

    if ($_POST['email1'] != '' or $_POST['email1'] != 'undefined') {
        if ($_POST['email1'] != 'undefined') {
            $el = mysqli_real_escape_string($conn, $_POST['email1']);
            $el = bizight_sp_encryption($el);
        } else {
            $el == '';
        }
    }

    if ($_POST['contact1'] != '' or $_POST['contact1'] != 'undefined') {
        if ($_POST['contact1'] != 'undefined') {
            $mle = mysqli_real_escape_string($conn, $_POST['contact1']);
            $mle = bizight_sp_encryption($mle);
        } else {
            $mle == '';
        }
    }

    if ($_POST['cmpny_name1'] != '' or $_POST['cmpny_name1'] != 'undefined') {
        if ($_POST['cmpny_name1'] != 'undefined') {
            $cmpny = mysqli_real_escape_string($conn, $_POST['cmpny_name1']);
        } else {
            $cmpny == '';
        }
    }

    //////GET FORM DETAIL, CLIENT ID and CTA
    '<br>As= ' . $fqry = "select * from form_to_action where id='" . $form_id . "'";
    $fres = mysqli_query($conn, $fqry);
    $fileData = mysqli_fetch_array($fres);

    if ($pcmember_type == 'C') {
        $client_id = $c_client_id;
    } else {
        $client_id = $fileData['client_id'];
    }

    $tempId = $fileData['template_id'];
    $ctaType = $fileData['cta_id'];
    $ctaInfo = $fileData['cta_info_detail'];
    $ctaUrl = $fileData['cta_url_redirect'];
    $ctadownload = $fileData['cta_download_url'];
    $mainfrm = $fileData['form_default_id'];
    $thank_msg = $fileData['cta_info_detail'];
    $thank_msg_download = $fileData['cta_dwnld_thank_msg'];
    $form_name = $fileData['form_name'];

    $urlPathq = "select subdomain_url from sp_subdomain where client_id='" . $client_id . "'";
    $resurl = mysqli_query($conn, $urlPathq);
    $pathData = mysqli_fetch_array($resurl);
    $redirectPath = trim($pathData['subdomain_url']);
    //$redirect_Url='http://'.'$redirectPath'.'/manager/';
    $redirect_Url = 'http://' . $redirectPath . '/' . manager . '/';

    $qery_manager = "SELECT person_email FROM  sp_members WHERE company_member_type=1 and client_id='" . $client_id . "' and valid=1 and deleted=0";
    $fres_manager = mysqli_query($conn, $qery_manager);

    while ($Data_manager = mysqli_fetch_array($fres_manager)) {
        $manager_email .= $Data_manager['person_email'] . ',';
    }

    $all_email = substr($manager_email, 0, -1);
    $all_email = explode(',', $all_email);

    if (($cmpny != '' or $cmpny != 'undefined')) {
        $chkcmp = "select * from sp_company where client_id='" . $client_id . "' and company_name='" . $cmpny . "' and valid=1 and deleted=0";
        $cmpres = mysqli_query($conn, $chkcmp);
        $compCount = mysqli_num_rows($cmpres);

        if ($compCount > 0) {
            $compData = mysqli_fetch_array($cmpres);
            $compId = $compData['comp_id'];
        } else {
            $addcmp = "insert into sp_company set client_id='" . $client_id . "',company_name='" . $cmpny . "',doe='" . $doe . "'";
            $rescmp = mysqli_query($conn, $addcmp);
            $compId = mysqli_insert_id($conn);
        }
    }

    if (($el != '' or $el != 'undefined')) {
        $chkm = "select * from sp_contact where client_id='" . $client_id . "' and email_id='" . $el . "' and valid=1 and deleted=0";
        $resm = mysqli_query($conn, $chkm);
        $countcont = mysqli_num_rows($resm);
        if ($countcont > 0) {
            $contactData = mysqli_fetch_array($resm);
            $contactId = $contactData['id'];
        } else {
            if ($fn == 'undefined') {
                $fn = "";
            }
            if ($ln == 'undefined') {
                $ln = "";
            }
            $addc = "insert into sp_contact set client_id='" . $client_id . "', first_name='" . $fn . "', last_name='" . $ln . "',comp_id='" . $compId . "', email_id='" . $el . "',mobile='" . $mle . "',source='Engagement Window',doe='" . $doe . "'";
            $cntres = mysqli_query($conn, $addc);
            $contactId = mysqli_insert_id($conn);
        }
    } else {
        if ($fn == 'undefined') {
            $fn = "";
        }
        if ($ln == 'undefined') {
            $ln = "";
        }
        $addc1 = "insert into sp_contact set client_id='" . $client_id . "', first_name='" . $fn . "', last_name='" . $ln . "',
			comp_id='" . $compId . "',email_id='" . $el . "',mobile='" . $mle . "', doe='" . $doe . "'";
        $cntres = mysqli_query($conn, $addc1);
        $contactId = mysqli_insert_id($conn);
    }

    if ($contactId != '' && ($compId == '' || $compId != '')) {
        $lead_group_id = time() . "-" . $contactId;
    } else if ($compId != '' && $contactId == '') {
        $lead_group_id = time() . "-" . $compId;
    } else {
        $lead_group_id = time();
    }

    //ADD LEAD
    if ($ctaType == 2 && $fn == '' && $ln == '' && $el == '' && $mle == '' && $cmpny == '') {
        '<br>' . $set_counter = "update form_click_counter set click_through =click_through+1 where form_id='" . $form_id . "' and form_url='" . $referral_url . "' and client_id='" . $client_id . "' and doe='" . $doe1 . "'";
        $updatecounter = mysqli_query($conn, $set_counter);
    } else {
        $addLead = "insert into sp_lead_generate set lead_group_id='" . $lead_group_id . "', client_id='" . $client_id . "', client_comp='" . $compId . "',    lead_request='" . $contactId . "',lead_contact_no='" . $mle . "',source='Engagement Window',engage_form_id='" . $form_id . "', engage_form_url='" . $referral_url . "',doe='" . $doe . "',lead_date='" . $doe1 . "'";
        $resld = mysqli_query($conn, $addLead);
        $leadInserted = mysqli_insert_id($conn); //Added By softprodigy

        $query_lead = "select count(engage_form_id) as form_lead from sp_lead_generate where engage_form_id='" . $form_id . "' and client_id='" . $client_id . "' and lead_date='" . $doe1 . "'";
        $result_lead = mysqli_query($conn, $query_lead);
        $row_lead = mysqli_fetch_array($result_lead);
        $form_lead = $row_lead['form_lead'];
        '<br>' . $set_counter = "update form_click_counter set lead_count='" . $form_lead . "' where form_id='" . $form_id . "' and form_url='" . $referral_url . "' and client_id='" . $client_id . "' and doe='" . $doe1 . "'";
        $updatecounter = mysqli_query($conn, $set_counter);
    }

    if ($ctaType == 3) {

        //echo "CASE1";
        $query_content_temp = mysqli_query($conn, "select cobrand from user_templates where template_id='" . $tempId . "' and client_id='" . $client_id . "' and valid=1 and deleted=0");
        $row_content_temp = mysqli_fetch_array($query_content_temp);
        if ($pcmember_type == 'C' && $row_content_temp['cobrand'] == 1) {
            $pathpdf = "$sitepath" . "webcontent/upload/casestudy/$c_client_id/$ctadownload";
        } else {
            $pathpdf = "$sitepath" . "webcontent/upload/casestudy/$client_id/$ctadownload";
        }

        echo "<script language=\"javascript\">window.open('$pathpdf',\"_blank\");</script>";
        echo '<span style="font-family:Arial, Helvetica, sans-serif;font-size:18px;font-weight:bold;">' . $thank_msg_download . '</span>';
    }

    if ($ctaType == 2) {
        if (strpos($ctaUrl, 'http://') !== 0) {
            $newurl = 'http://' . $ctaUrl;
        } else {
            $newurl = $ctaUrl;
        }
        echo "<script language=\"javascript\">window.open('" . $newurl . "',\"_blank\");</script>";
    }

    if ($ctaType == 1) {
        //echo "CASE3";
        echo '<span style="font-family:Arial, Helvetica, sans-serif;font-size:18px;font-weight:bold;">' . $thank_msg . '</span>';
    }

    //Added By Softprodigy for Lead Schedule event
    $queryL = "select lead_schedule.type from lead_schedule where lead_schedule.client_id='" . $client_id . "'";
    $resultL = mysqli_query($conn, $queryL);
    $mainCount = mysqli_num_rows($resultL);
    $leadType = '';

    if ($mainCount > 0) {
        $dataL = mysqli_fetch_array($resultL);

        if ($dataL['type'] != '') {
            $dataType = explode(',', $dataL['type']);
            if (in_array('1', $dataType)) {
                $leadType = 1;
            }
        }
    }

    if ($leadType == 1) {
        $Message = "<html>";
        $Message .= "<head></head>";
        $Message .= "<body bgcolor='#e6e5e5'>";
        $Message .= "<div style='max-width:600px; margin:0 auto; background-color:#ffffff;padding:10px; '>";
        $Message .= "<p><a href='javascript:void(0)'><img src='" . $sitepath . "webcontent/images/salespanda-logo-t.png' alt=" . $sitepath . " width='220' height='54' align='left' /></a><a href='" . $sitepath . "' target='_new'><img src='" . $sitepath . "webcontent/images/login-btn.png' width='100' height='41' align='right' /></a></p>";
        $Message .= "<p><br /><br /><br /><br /><img src='" . $sitepath . "webcontent/images/line.png' width='100%' /></p>";

        if ($ctaType == '2') {
            $Message .= "<p style='font-family:Arial, Helvetica, sans-serif; font-size:18px; text-align:left; font-weight:bold; color:#272727; '>Congratulations! You got a new click through.</p>";
        } else {
            $Message .= "<p style='font-family:Arial, Helvetica, sans-serif; font-size:18px; text-align:left; font-weight:bold; color:#272727; '>Congratulations! You just acquired a new lead.</p>";
        }

        $Message .= "<table width='100%' border='0' cellspacing='0' cellpadding='8>";
        $Message .= "<tr>
			<td width='40%' align='left' valign='middle' bgcolor='#45bcd2' style='font-family:Arial, Helvetica, sans-serif; font-size:17px; text-align:left; font-weight:bold; color:#fff;'>Details</td>
			<td width='60%' align='left' valign='middle' bgcolor='#45bcd2' style='font-family:Arial, Helvetica, sans-serif; font-size:17px; text-align:left; font-weight:bold; color:#272727;padding:10px;'>&nbsp;</td>
			</tr>";

        if ($fn != '') {
            $Message .= "<tr>
				<td align='left' valign='middle' bgcolor='#f1f2f2'><span style='font-family:Arial, Helvetica, sans-serif; font-size:17px; text-align:left; font-weight:bold; color:#272727; '>Name:</span></td>
				<td align='left' valign='middle' bgcolor='#f1f2f2'><span style='font-family:Arial, Helvetica, sans-serif; font-size:17px; text-align:left;color:#272727; '>" . bizight_sp_decryption($fn) . "</span><br /></td>
				</tr>";
        }

        if ($el != '') {
            $Message .= "<tr>
				<td align='left' valign='middle' bgcolor='#f1f2f2'><span style='font-family:Arial, Helvetica, sans-serif; font-size:17px; text-align:left; font-weight:bold; color:#272727; '>Email:</span></td>
				<td align='left' valign='middle' bgcolor='#f1f2f2'><span style='font-family:Arial, Helvetica, sans-serif; font-size:17px; text-align:left; color:#272727; '>" . bizight_sp_decryption($el) . "</span></td>
				</tr>";
        }

        if ($mle != '') {
            $Message .= "<tr>
				<td align='left' valign='middle' bgcolor='#f1f2f2'><span style='font-family:Arial, Helvetica, sans-serif; font-size:17px; text-align:left; font-weight:bold; color:#272727;display:none;'>Phone Number:</span></td>
				<td align='left' valign='middle' bgcolor='#f1f2f2'><span style='font-family:Arial, Helvetica, sans-serif; font-size:17px; text-align:left; color:#272727;display:none;'>" . bizight_sp_decryption($mle) . "</span></td>
				</tr>";
        }

        if ($cmpny != '') {
            $Message .= "<tr>
				<td align='left' valign='middle' bgcolor='#f1f2f2'><span style='font-family:Arial, Helvetica, sans-serif; font-size:17px; text-align:left; font-weight:bold; color:#272727; '>Company Name:</span></td>
				<td align='left' valign='middle' bgcolor='#f1f2f2'><span style='font-family:Arial, Helvetica, sans-serif; font-size:17px; text-align:left; color:#272727; '>$cmpny</span></td>
				</tr>";
        }

        $Message .= "<tr>
			<td align='left' valign='middle' bgcolor='#CCCCCC'><span style='font-family:Arial, Helvetica, sans-serif; font-size:17px; text-align:left; color:#272727; '>Engagement Window: $form_name</span></td>";

        $Message .= "<td height='30' align='left' valign='middle' bgcolor='#CCCCCC'><span style='font-family:Arial, Helvetica, sans-serif; font-size:17px; text-align:left;color:#272727;'>Url: $referral_url</span></td>
			</tr>";

        if ($ctaType == '2') {
            $Message .= "<tr>
				<td align='left' valign='middle' bgcolor='#5B5B5C'><span style='font-family:Arial, Helvetica, sans-serif; font-size:17px; text-align:left; color:#ffffff; '>Destination URL:</span></td>";

            $Message .= "<td height='30' align='left' valign='middle' bgcolor='#5B5B5C'><span style='font-family:Arial, Helvetica, sans-serif; font-size:17px; text-align:left;color:#ffffff;'>$ctaUrl</span></td></tr>";
        }

        $Message .= "<tr>
			<td align='left' valign='middle' bgcolor='#FFFFFF'>&nbsp;</td>
			<td height='30' align='left' valign='middle' bgcolor='#FFFFFF'>&nbsp;</td>
			</tr>";

        $Message .= "</table>";
        $Message .= "</div>";
        $Message .= "</body>";
        $Message .= "</html>";

        $MessageAdmin = null;
        $MessageAdmin = $Message;
        $ToSubjectAdmin = "New Lead Added using SalesPanda Engagement Window";
        $from_emails = "info@salespanda.com";

        if ($mailServer['mail_server'] == 'mailgun') {
					
					/*
            $email_array['to'] = $all_email;
            $email_array['subject'] = $ToSubjectAdmin;
            $email_array['from'] = $from_emails;
            $email_array['html'] = $MessageAdmin;
            $email_array['attachment'] = '';
            $email_array['my_custom_data'] = json_encode('');
            $result = $sendMail->sendSimpleMail($email_array);
					// */
					
        } else if ($mailServer['mail_server'] == 'sendgrid') {
            $js = array(
                'sub' => array(':name' => array('Sachindra'))
            );

            $params = array(
                'api_user' => $user,
                'api_key' => $pass,
                'x-smtpapi' => json_encode($js),
                'to' => $all_email,
                'subject' => $ToSubjectAdmin,
                'html' => $MessageAdmin,
                'text' => 'demo text',
                'from' => $from_emails,
            );

            if (@$mailServer['mail_server'] == 'sendgrid') {

                $params_transactional = [
                    "to" => $all_email,
                    "from" => [$from_emails => "SalesPanda"],
                    "subject" => $ToSubjectAdmin,
                    "text" => $ToSubjectAdmin,
                    "html" => $MessageAdmin,
                    "custom_args" => ["server_name" => $_SERVER['HTTP_HOST']],
                    "categories" => ['new-lead']
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
            }
        }
    }
}
