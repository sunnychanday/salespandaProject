<?php
/*Author name: <Bizight Solutions Pvt Ltd> 
 * Purpose of document/ page : This is used to capture lead from micro-site and send the push notification
 * Note : This file is used to add or update the contact details form Showcase page
 * Date:  16-02-2021 
 * Copyright info : <Copyright @2021, Bizight Solutions Pvt Ltd>
*/

header('Access-Control-Allow-Origin: true');
require realpath(__DIR__ . '/vendor/autoload.php');

include("includes/global.php");
include("includes/function.php");
include("csrf/csrf-magic.php");
include_once("includes/connect-new.php");
include("manager/common_functions.php");

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

if(isset($_POST['c_']) && !empty($_POST['c_'])){
    $addLeadVar = 1;
    $url_contact_id = $_POST['c_'];
}else{
    $googleCaptcha = validate_google_captcha('MicrositePage');
    $message       = $googleCaptcha['message'];
    $addLeadVar    = $googleCaptcha['status'];
}
$source_url = $_SERVER['HTTP_REFERER'];

if ($addLeadVar) {
    $user = $sndGdUser;
    $pass = $snGdPassword;
    $p_email = $p_phone = $filename = '';
    $c_client = $c_lient_Id = testInput($_POST['client_id']);
    $micro_obj  = new \Microsite\Microsite($connPDO, ['referer_url'=>$source_url]);
    if ($micro_obj->microsite_exists === true) {
        $c_lient_Id = $micro_obj->client_id;
    }

    //Get Child Client details
    $pc_member_info = getPCMemberInfo($c_lient_Id);
    $pcmember_pc_type = ($pc_member_info['member_pc_type'] == 'P') ? 'P' : 'C' ;
    $p_client_id = $pc_member_info['p_client_id'];
    $agent_name = $pc_member_info['first_name'];
    $device_token = $pc_member_info['device_token'];
    $login_via = $pc_member_info['login_via'];
    $person_email = $pc_member_info['person_email'];
    $person_contact = $pc_member_info['person_contact1'];

    $getdmnURL = CommonStaticFunctions::get_sub_domain_url($conn, $c_client);
    $ipaddress = testInput(CommonStaticFunctions::get_remote_user_ip());
    $useragent = testInput($_SERVER['HTTP_USER_AGENT']);
    $ref_url = testInput($_REQUEST['ref_url']);
    /********* For tracking *********/
    $contentType = $micro_obj->url_params['content'] ?? testInput($_REQUEST['contentType']);
    $camp_id  = $micro_obj->url_params['camp_id'] ?? testInput($_REQUEST['camp_id']);
    $channel_type  = $micro_obj->url_params['channel_type'] ?? testInput($_REQUEST['channel_type']);
    $user_id = $micro_obj->url_params['pid'] ?? $pc_member_info['pid'] ;
    /*----------------------------*/
    $caseId = testInput($_REQUEST['caseids']);
    if ($caseId != '') {
        $chkres = mysqli_query($conn, "select * from sp_case_study where id='{$caseId}'");
        $caseData = mysqli_fetch_array($chkres);
        $template_id = $caseData['case_study_library'];
        $publish_url = $caseData['content_publish_url'];
        $company_case = $caseData['comp_id'];
        $categoryCs = $caseData['category'];
        $documentName = $caseData['case_study_title'];

        $documentNameAvtar = str_replace("-", " ", $documentName);
        $cta_id = $caseData['call_to_action'];
        $document = $caseData['case_study'];

        $cta_set = mysqli_query($conn, "select * from cta_button where id='{$cta_id}'");
        $ctaData = mysqli_fetch_array($cta_set);
        $cta_name = trim($ctaData['ctaName']);
        $ctaType = $ctaData['ctaType'];

        $ct_result = mysqli_query($conn, "select * from cta_type  where id='{$ctaType}' and valid=1 and deleted=0");
        $ct_row = mysqli_fetch_array($ct_result);
        $ctName = $ct_row['name'];
    }
    $fname = $lname = $city = $email = $phone = $_sanitize_phone = '';
    if ($_POST['fname'] != '' || $_POST['lname'] != '') {
        $fname = bizight_sp_encryption(testInput($_POST['fname']));
        $lname = bizight_sp_encryption(testInput($_POST['lname']));
    }

    if ($_POST['email'] != '') {
        $checkEmailValid = emailValidity($_POST['email']);
        if ($checkEmailValid == 1)
            $email = bizight_sp_encryption(testInput($_POST['email']));
    }

    if ($_POST['phone'] != '') {
        $numlength = strlen((string) $_POST['phone']);
        if ($numlength >= 10 && is_numeric($_POST['phone']))
            $_sanitize_phone = bizight_sp_encryption(CommonStaticFunctions::sanitize_contact_number(testInput($_POST['phone'])));
            $phone = bizight_sp_encryption(testInput($_POST['phone']));
    }

    if ($_POST['city'] != '') {
        $city = testInput($_POST['city']);
        $cityId = CommonStaticFunctions::get_city_id($conn,$c_lient_Id,$city);
    }

    $subQuery = '';
    if(isset($url_contact_id) && !empty($url_contact_id))
        $subQuery = " and id='{$url_contact_id}'";
    elseif($email != '' && $phone != '')
        $subQuery = " and (email_id='{$email}' OR mobile IN ('{$phone}','{$_sanitize_phone}'))";
    else if($email != '')
        $subQuery = " and email_id='{$email}'";
    else if($phone != '')
        $subQuery = " and mobile IN ('{$phone}','{$_sanitize_phone}')";

    $rescont = mysqli_query($conn, "select * from sp_contact where client_id='{$c_lient_Id}' {$subQuery} and valid=1 and deleted=0");
    $contactCount = mysqli_num_rows($rescont);
    if ($contactCount > 0) {

        $contactData = mysqli_fetch_array($rescont);
        $contactId = $contactData['id'];
        $checkmobile = $contactData['mobile'];
        $checkphone1 = $contactData['phone1'];

        $checkemail = $contactData['email_id'];
        $checksecondemail = $contactData['secondaryemail_id'];
        $additionalinfo = $contactData['first_name'] . '/' . $contactData['last_name'] . '/' . date('Y-m-d H:i:s');

        $lname = (!empty($fname)) ? $lname: $contactData['last_name'];
        $fname = (!empty($fname)) ? $fname: $contactData['first_name'];
        $cityId = (isset($cityId) && !empty($cityId)) ? $cityId: $contactData['city'];
        $update_status = 1;
        $update_common_query =  "first_name='{$fname}', last_name='{$lname}', city='{$cityId}', dou = '" . date('Y-m-d H:i:s') . "' where id = '{$contactId}' and valid = 1 and deleted = 0";

        if ($phone != '' && ($checkmobile != $phone) && $checkmobile != '') {
            $cntres = mysqli_query($conn, "update sp_contact set phone1='{$phone}',{$update_common_query}");
            $update_status = 0;
        }else if ($phone != '' && empty($checkmobile)) {
            $cntres = mysqli_query($conn, "update sp_contact set mobile = '{$phone}',{$update_common_query}");
            $update_status = 0;
        }

        if ($email != '' && ($checkemail != $email) && $checkemail != '') {
            $cntres = mysqli_query($conn, "update sp_contact set secondaryemail_id='{$email}',{$update_common_query}");
            $update_status = 0;
        }else if ($email != '' &&  empty($checkemail)) {
            $cntres = mysqli_query($conn, "update sp_contact set email_id='{$email}',{$update_common_query}");
            $update_status = 0;
        }

        //If inserted emailId is similar with contact's present email id then update all the inserted fields
        if($email != '' && ($checkemail == $email) && $checkemail != '' && ($update_status == 1))
            $cntres = mysqli_query($conn, "update sp_contact set {$update_common_query}");

    } else {
        $user_id = ($user_id == '') ? 0 : $user_id;
        $addres = mysqli_query($conn, "insert into sp_contact set client_id='{$c_lient_Id}', source='Showcase : {$cta_name}', first_name='{$fname}', last_name='{$lname}', email_id='{$email}', mobile='{$phone}', city='{$cityId}', added_by='{$user_id}', valid=1, deleted=0, doe='" . date('Y-m-d H:i:s') . "'");
        $contactId = mysqli_insert_id($conn);
    }

    if ($contactId != '') {
        $lead_group_id = time() . "-" . $contactId;
        $companyId = $verticalCs = $leadRemark = $source_platform = '';
        $addldres = mysqli_query($conn, "insert into sp_lead_generate set client_id = '{$c_lient_Id}', lead_group_id = '{$lead_group_id}', content_id = '{$caseId}', camp_id = '{$camp_id}',contentType = '{$contentType}', client_comp = '{$company_case}', lead_request = '{$contactId}', lead_contact_no = '{$phone}', request_comp = '{$companyId}', city = '{$cityId}', category = '{$categoryCs}', vertical = '{$verticalCs}', get_remark = '{$leadRemark}', source = 'Showcase : {$cta_name}', ip_address = '{$ipaddress}', user_agent = '" . mysqli_real_escape_string($conn, $useragent) . "', campaign_source = '" . mysqli_real_escape_string($conn, $channel_type) . "', referal_url = '" . mysqli_real_escape_string($conn, $ref_url) . "', source_platform = '" . mysqli_real_escape_string($conn, $source_platform) . "', engage_form_url = '" . mysqli_real_escape_string($conn, $source_url) . "', lead_date = '" . date('Y-m-d H:i:s') . "', added_by = '{$user_id}', doe = '" . date('Y-m-d H:i:s') . "'");
        $lead_group = mysqli_insert_id($conn);

        //update event track if visit entry is not present regarding this entry
        CommonStaticFunctions::update_event_track_data($conn,['url'=>$source_url,'ipaddress'=>$ipaddress,'contact_id'=>$contactId,'email_id'=>$email,'client_id'=>$c_lient_Id,'referral_url'=>$ref_url,'contentType'=>$contentType,'camp_id'=>$camp_id,'useragent'=>$useragent,'visit_page'=>'showcase','source'=>$channel_type,'userid'=>$user_id]);

        //convert contact into prospect
        convert_into_known_visitor($c_lient_Id, $contactId);

        if ($pcmember_pc_type == "C" && $device_token != '')
            CommonStaticFunctions::send_lead_push($conn, $sitepath, $lead_group_id, $c_lient_Id, $user_id, $device_token, $login_via);

        if ($pcmember_pc_type == 'C') {
            $qry_content = "SELECT T.*,TS.* FROM sp_template_syndication as TS INNER JOIN user_templates as T ON TS.template_id = T.template_id where TS.p_client_id = '{$p_client_id}' and T.template_id = '{$template_id}' and TS.approve = 1 and T.valid = 1 and T.deleted = 0";
        } else {
            $qry_content = "select * from user_templates where client_id = '{$c_lient_Id}' and template_id = '{$template_id}' and valid = 1 and deleted = 0";
        }

        $result_content = mysqli_query($conn, $qry_content);
        $row_content = mysqli_fetch_array($result_content);
        if ($row_content['content_file'] != '')
            $filename = str_replace(' ', '%20', $row_content['content_file']);
        if ($pcmember_pc_type == 'C')
            $pc_client_id = $p_client_id;
        else
            $pc_client_id = $c_lient_Id;

        $pdf_upload_path = "{$_SERVER['DOCUMENT_ROOT']}/upload/casestudy/{$pc_client_id}/{$filename}";
        $emailId = bizight_sp_decryption($email);
        $emailId = explode(',', $emailId);

        if (!empty($filename) && file_exists($pdf_upload_path) && $ctName == "Download") {
            $tpl_url = "{$sitepath}template/showcase/download_content.php";
            $ToSubject = "Content Download From Microsite Showcase";
            $from_email = "info@salespanda.com";

            if ($mailServer['mail_server'] == 'sendgrid') {
                $post_arr = [
                    "fname" => bizight_sp_decryption($fname),
                    "documentNameAvtar" => $documentNameAvtar,
                    "getdmnURL" => $getdmnURL,
                    "pc_client_id" => $pc_client_id,
                    "filename" => $filename
                ];

                $params_transactional = [
                    "to" => $emailId,
                    "from" => [$from_email => "SalesPanda"],
                    "subject" => $ToSubject,
                    "text" => 'Content Download From Microsite Showcase',
                    "html" => get_mailer_template($tpl_url, $post_arr),
                    "custom_args" => ["server_name" => $_SERVER['HTTP_HOST']],
                    "categories" => ['showcase-download']
                ];

                try {
                    $sendgrid = new SPMailer\SendGridMailer($snGdPassword, 'transactional');
                    $status = $sendgrid->send($params_transactional);
                    $response = json_decode($status);
                } catch (Exception $e) {
                    $response = (object) ["code" => $e->getCode(), "message" => $e->getMessage(), "file" => $e->getFile(), "line" => $e->getLine()];
                }
            } else {
                $json_string = array(
                    'to' => $emailId
                );


                $post_arr = [
                    "fname" => bizight_sp_decryption($fname),
                    "documentNameAvtar" => $documentNameAvtar,
                    "getdmnURL" => $getdmnURL,
                    "pc_client_id" => $pc_client_id,
                    "filename" => $filename
                ];

                $params = array(
                    'api_user' => $user,
                    'api_key' => $pass,
                    'x-smtpapi' => json_encode($json_string),
                    'to' => $emailId,
                    'subject' => $ToSubject,
                    'html' => get_mailer_template($tpl_url, $post_arr),
                    'text' => 'Content Download From Microsite Showcase',
                    'from' => $from_email,
                );

                $result = (object) netcore_email($params, $transactional_password);
                $response = (object) $result->message;
            }
        }

        $personEmail = explode(',', $person_email);
        $tpl_url = "{$sitepath}template/mailer/lead_mailer.php";
        $ToSubjectAdmin = "New lead added using microsite showcase";
        $from_emails = "info@salespanda.com";

        if ($mailServer['mail_server'] == 'sendgrid') {
            $post_arr = [
                "agent_name" => $agent_name,
                "client_name" => bizight_sp_decryption($fname) . ' ' . bizight_sp_decryption($lname),
                "client_email" => bizight_sp_decryption($email),
                "client_phone" => bizight_sp_decryption($phone),
                "lead_from" => "Showcase",
                "subject" => $ToSubjectAdmin,
                "getdmnURL" => $getdmnURL,
                "msgadd" => '',
                "lead_group_id" => $lead_group_id
            ];

            $params_transactional = [
                "to" => $personEmail,
                "from" => [$from_emails => "SalesPanda"],
                "subject" => $ToSubjectAdmin,
                "text" => 'New Lead Added using SalesPanda Microsite Showcase',
                "html" => get_mailer_template($tpl_url, $post_arr),
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

            $json_strings = array(
                'to' => $personEmail
            );
            $post_arr = [
                "agent_name" => $agent_name,
                "client_name" => bizight_sp_decryption($fname) . ' ' . bizight_sp_decryption($lname),
                "client_email" => bizight_sp_decryption($email),
                "client_phone" => bizight_sp_decryption($phone),
                "lead_from" => "Showcase",
                "subject" => $ToSubject,
                "getdmnURL" => $getdmnURL,
                "msgadd" => '',
                "lead_group_id" => $lead_group_id
            ];
            $params = array(
                'api_user' => $user,
                'api_key' => $pass,
                'x-smtpapi' => json_encode($json_strings),
                'to' => $personEmail,
                'subject' => $ToSubjectAdmin,
                'html' => get_mailer_template($tpl_url, $post_arr),
                'text' => 'New Lead Added using SalesPanda Microsite Showcase',
                'from' => $from_emails,
            );
            $result = (object) netcore_email($params, $transactional_password);
            $response = (object) $result->message;
        }

        //Send Lead SMS to Child client
        if (isset($person_contact) && $person_contact !== '')
            CommonStaticFunctions::send_lead_sms($conn, $person_contact, $getdmnURL, $lead_group_id, $p_client_id, $c_lient_Id);

        if (file_exists($pdf_upload_path) && $ctName == "Download") {
            $_SESSION['successmsg'] = 'Thank you for the information. Please check your inbox to download the document.';
        } else {
            $_SESSION['successmsg'] = 'Thanks for Submitting, we will get in touch with you soon.';
        }
    }
}else{
    $_SESSION['successmsg'] = $message;
}
header("location: {$source_url}");
exit;
