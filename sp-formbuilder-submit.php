<?php
/*Author name: <Bizight Solutions Pvt Ltd> 
 * Purpose of document/ page : <This is page is used to capture the lead from microsite form.> 
 * Date: 17-02-2021 
 * Copyright info : <Copyright @2021, Bizight Solutions Pvt Ltd>
*/
require realpath(__DIR__ . '/vendor/autoload.php');

include_once "{$_SERVER['DOCUMENT_ROOT']}/helpers/ip-city-name.php";
include_once "{$_SERVER['DOCUMENT_ROOT']}/helpers/detect-device.php";
include("includes/global.php");
include("includes/function.php");
include("includes/common_php_functions.php");
include_once("includes/connect-new.php");
include("manager/common_functions.php");
include("csrf/csrf-magic.php");

//include("Class/mailgunSmtp.php");
//include("Class/sendMail.php");
header('Access-Control-Allow-Origin: *');
if (!empty($_REQUEST['pdfname']))
    echo trim(testInput($_REQUEST['pdfname']));
$ToSubjectAdmin = $fn = $ln = $el = $mle = $cmpny = $cty = $profession = $education_level = $form_refer = $p_email = $p_phone = $person_contact = $getdmnURL = $state = $pincode = $_sanitize_phone = '';

$googleCaptcha = validate_google_captcha('MicrositePage');
$message       = $googleCaptcha['message'];
$addLeadVar    = $googleCaptcha['status'];

if ($addLeadVar) {
    $user = $sndGdUser;
    $pass = $snGdPassword;
    $doe1 = date("Y-m-d");
    $client_id = $c_lient_Id = testInput($_REQUEST['c_lient_Id']);
    $source_url = $_SERVER['HTTP_REFERER'];
    $micro_obj  = new \Microsite\Microsite($connPDO, ['referer_url'=>$source_url]);
    if ($micro_obj->microsite_exists === true) {
        $c_lient_Id = $micro_obj->client_id;
    }

    //Get Child Client details
    $pc_member_info = getPCMemberInfo($c_lient_Id);
    $pcmember_pc_type = ($pc_member_info['member_pc_type'] == 'P') ? 'P' : 'C' ;
    $agent_name = $pc_member_info['first_name'];
    $p_client_id = $pc_member_info['p_client_id'];
    $device_token = $pc_member_info['device_token'];
    $login_via = $pc_member_info['login_via'];
    $person_email = $pc_member_info['person_email'];
    $person_contact = $pc_member_info['person_contact1'];

    /********* For tracking purpose *********/
    $form_refer = testInput($_REQUEST['form_refer']);
    $ref_url    = testInput($_REQUEST['ref_url']);
    $content_type = $micro_obj->url_params['content'] ?? '';
    $camp_id  = $micro_obj->url_params['camp_id'] ?? 0;
    $channel_type  = $micro_obj->url_params['channel_type'] ?? '';
    $user_id = $micro_obj->url_params['pid'] ?? $pc_member_info['pid'] ;
    $forTrackingPurpose = ",contentType='".mysqli_real_escape_string($conn,$content_type)."',camp_id='{$camp_id}',added_by='{$user_id}',campaign_source='".mysqli_real_escape_string($conn,$channel_type)."'";
    /*--------------------------------------*/

    $getdmnURL = CommonStaticFunctions::get_sub_domain_url($conn, $client_id);
    //helpers function for using third party api to find city name using ip address
    $ip_city = ip_based_city_name(CommonStaticFunctions::get_remote_user_ip());
    //helpers function for detecting device
    $device_type = detectDevice($_SERVER["HTTP_USER_AGENT"]);
    $source_platform = '';
    $captcha = $_REQUEST['captcha'] ?? '';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $leadInserted = '';
        $doe = date("Y-m-d h:i:s");
        $ref_url = $_REQUEST['ref_url1'] ?? '';
        $ref_ip_addr = CommonStaticFunctions::get_remote_user_ip();
        $useragent = $_SERVER['HTTP_USER_AGENT'];

        if ($ref_url != '') {
            $ref_url_arr = explode("/", $ref_url);
            $ref_dom_url = testInput($ref_url_arr[0]) . "//" . testInput($ref_url_arr[2]);

            $rssourceurl = mysqli_query($conn, "select * from sp_source_reference where url_pattern='" . $ref_dom_url . "' ");
            $rowsourceurl = mysqli_fetch_array($rssourceurl);
            $source_platform = $rowsourceurl['platform'];
            if ($source_platform == '')
                $source_platform = "Other";
        }

        if(isset($_POST['full_name1'])){
            if ($_POST['full_name1'] != '') {
                $fn = ($_POST['full_name1'] != 'undefined') ? bizight_sp_encryption(testInput($_POST['full_name1'])) : '' ;
            } else {
                header("location:index.php?successmsg=Please fill the name.");
                exit;
            }
        }

        if(isset($_POST['contact1'])){
            if ($_POST['contact1'] != '') {
                $numlength = strlen((string) $_POST['contact1']);
                if ($numlength >= 10 && is_numeric($_POST['contact1'])) {
                    $_sanitize_phone = bizight_sp_encryption(CommonStaticFunctions::sanitize_contact_number(testInput($_POST['contact1'])));
                    $mle = bizight_sp_encryption(testInput($_POST['contact1']));
                } else {
                    header("location:index.php?successmsg=Please fill the contact.");
                    exit;
                }
            } else {
                header("location:index.php?successmsg=Please fill the contact.");
                exit;
            }
        }

        if (isset($_POST['city']) && ($_POST['city'] != '' or $_POST['city'] != 'undefined'))
            $fn = ($_POST['city'] != 'undefined') ? testInput($_POST['city']) : '' ;

        if (isset($_POST['state']) && ($_POST['state'] != '' or $_POST['state'] != 'undefined'))
            $state = ($_POST['state'] != 'undefined') ? testInput($_POST['state']) : '' ;

        if (isset($_POST['pincode']) && ($_POST['pincode'] != '' or $_POST['pincode'] != 'undefined'))
            $pincode = ($_POST['pincode'] != 'undefined') ? testInput($_POST['pincode']) : '' ;

        if ($_POST['email1'] != '') {
            $checkEmailValid = emailValidity($_POST['email1']);
            if ($checkEmailValid == 1) {
                $el = bizight_sp_encryption(testInput($_POST['email1']));
            } else {
                header("location:index.php?successmsg=Please fill the correct email address.");
                exit;
            }
        } else {
            header("location:index.php?successmsg=Please fill the email address.");
            exit;
        }

        if (isset($_POST['company_name1']) && ($_POST['company_name1'] != '' or $_POST['company_name1'] != 'undefined'))
            $cmpny =  ($_POST['company_name1'] != 'undefined') ? testInput($_POST['company_name1']) : '' ;

        if (isset($_POST['plan_segment']) && ($_POST['plan_segment'] != '' or $_POST['plan_segment'] != 'undefined'))
            $plan_segment = ($_POST['plan_segment'] != 'undefined') ? testInput($_POST['plan_segment']) : '';

        if (isset($_POST['address1']) && ($_POST['address1'] != '' or $_POST['address1'] != 'undefined'))
            $address = ($_POST['address1'] != 'undefined') ? testInput($_POST['address1']) : '';

        if (isset($_POST['your_message']) && ($_POST['your_message'] != '' or $_POST['your_message'] != 'undefined'))
            $your_message = ($_POST['your_message'] != 'undefined') ? testInput($_POST['your_message']) : '';

        if (isset($_POST['subarea']) && ($_POST['subarea'] != '' or $_POST['subarea'] != 'undefined'))
            $plan_subarea = ($_POST['subarea'] != 'undefined') ? testInput($_POST['subarea']) : '';

        if (isset($_POST['plan_name']) && ($_POST['plan_name'] != '' or $_POST['plan_name'] != 'undefined'))
            $plan_name = ($_POST['plan_name'] != 'undefined') ? testInput($_POST['plan_name']) : '' ;

        if (isset($_POST['building']) && ($_POST['building'] != '' or $_POST['building'] != 'undefined'))
            $building_name = ($_POST['building'] != 'undefined') ? testInput($_POST['building']) : '';

        if (isset($_POST['building_no']) && ($_POST['building_no'] != '' or $_POST['building_no'] != 'undefined'))
            $building_no = ($_POST['building_no'] != 'undefined') ? testInput($_POST['building_no']) : '';

        if ($c_lient_Id == 'SP1542096255' && $form_refer == 'Form Customer Registration') {
            $adr_latitude = $adr_longitude = '';
            if ($_POST['adr_latitude'] != '' or $_POST['adr_latitude'] != 'undefined')
                $adr_latitude = ($_POST['adr_latitude'] != 'undefined') ? testInput($_POST['adr_latitude']) : '';
            if ($_POST['adr_longitude'] != '' or $_POST['adr_longitude'] != 'undefined')
                $adr_longitude = ($_POST['adr_longitude'] != 'undefined') ? testInput($_POST['adr_longitude']) : '';

            if (empty($adr_latitude) && empty($adr_longitude) && !empty($cty) && !empty($state)) {
                $full_address = str_replace(" ", "+", "India {$state} {$cty} {$plan_subarea} {$building_name}");
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://maps.google.com/maps/api/geocode/json?address={$full_address}&sensor=false&region=India");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $response = curl_exec($ch);
                curl_close($ch);
                $response_a = json_decode($response);
                $adr_latitude = $response_a->results[0]->geometry->location->lat;
                $adr_longitude = $response_a->results[0]->geometry->location->lng;
            }
        }

        if ($cty != '' or $cty != 'undefined')
            $cityId = CommonStaticFunctions::get_city_id($conn, $c_lient_Id, $cty);
        if ($cmpny != '' or $cmpny != 'undefined')
            $compId = CommonStaticFunctions::get_company_id($conn, $c_lient_Id, $cmpny);
        if (isset($state) && ($state != '' or $state != 'undefined'))
            $stateId = CommonStaticFunctions::get_state_id($conn, $c_lient_Id, $state);

        if($el != '' && $mle != '')
            $subQuery = "(email_id='{$el}' OR mobile IN ('{$mle}','{$_sanitize_phone}'))";
        else if($el != '')
            $subQuery = "email_id='{$el}'";
        else if($mle != '')
            $subQuery = "mobile IN ('{$mle}','{$_sanitize_phone}')";
        else
            $subQuery = '';

        $resm = mysqli_query($conn, "select * from sp_contact where client_id='{$c_lient_Id}' and {$subQuery} and valid=1 and deleted=0");
        $countcont = mysqli_num_rows($resm);
        if ($countcont > 0) {
            $contactData = mysqli_fetch_array($resm);
            $contactId = $contactData['id'];
            $checkmobile = $contactData['mobile'];
            $checkphone1 = $contactData['phone1'];
            $checkemail = $contactData['email_id'];
            $checksecondemail = $contactData['secondaryemail_id'];
            if ($fn == 'undefined')
                $fn = "";
            if ($ln == 'undefined')
                $ln = "";
            $fn = $fn ?? $contactData['first_name'];
            $stateId =  $stateId ?? $contactData['state'];
            $address = $address ?? $contactData['address'];
            $cityId = $cityId ?? $contactData['city'];
            $pincode = $pincode ?? $contactData['pincode'];
            $compId = $compId ?? $contactData['comp_id'];
            $plan_segment = $plan_segment ?? $contactData['custom_field2'];
            $your_message = $your_message ?? $contactData['custom_field3'];
            $plan_subarea = $plan_subarea ?? $contactData['custom_field4'];
            $plan_name = $plan_name ?? $contactData['custom_field5'];
            $building_name = $building_name ?? $contactData['custom_field6'];
            $building_no = $building_no ?? $contactData['custom_field7'];
            $adr_latitude = $adr_latitude ?? $contactData['lat_custom_field'];
            $adr_longitude = $adr_longitude ?? $contactData['lng_custom_field'];

            $update_status = 1;
            $update_common_query =  "first_name='{$fn}',state='{$stateId}', address='{$address}',city='{$cityId}',custom_field1='{$pincode}',custom_field2='{$plan_segment}',custom_field3='{$your_message}', comp_id='{$compId}',custom_field4='{$plan_subarea}', custom_field5='{$plan_name}', custom_field6='{$building_name}', custom_field7='{$building_no}', lat_custom_field='{$adr_latitude}', lng_custom_field='{$adr_longitude}',dou='" . date('Y-m-d H:i:s') . "' where id='{$contactId}' and client_id='{$c_lient_Id}' and valid=1 and deleted=0";
            if ($mle != '' && ($checkmobile != $mle) && $checkmobile != '') {
                $cntres = mysqli_query($conn, "update sp_contact set phone1 = '{$mle}',{$update_common_query}");
                $update_status = 0;
            }else if ($mle != '' && empty($checkmobile)) {
                $cntres = mysqli_query($conn, "update sp_contact set mobile = '{$mle}',{$update_common_query}");
                $update_status = 0;
            }

            if ($el != '' && ($checkemail != $el) && $checkemail != '') {
                $cntres = mysqli_query($conn, "update sp_contact set secondaryemail_id='{$el}',{$update_common_query}");
                $update_status = 0;
            }else if ($el != '' &&  empty($checkemail)) {
                $cntres = mysqli_query($conn, "update sp_contact set email_id='{$el}',{$update_common_query}");
                $update_status = 0;
            }
            if ($el != '' && ($checkemail == $el) && $checkemail != '' && ($update_status == 1))
                $cntres = mysqli_query($conn, "update sp_contact set {$update_common_query}");

        } else {
            if ($fn == 'undefined')
                $fn = "";
            if ($ln == 'undefined')
                $ln = "";

            $cntres = mysqli_query($conn, "insert into sp_contact set client_id='{$c_lient_Id}', address='{$address}',first_name='{$fn}', state='{$stateId}', city='{$cityId}',comp_id='{$compId}',email_id='{$el}',mobile='{$mle}',custom_field1='{$pincode}',custom_field2='{$plan_segment}', custom_field3='{$your_message}', custom_field4='{$plan_subarea}', custom_field5='{$plan_name}', custom_field6='{$building_name}', custom_field7='{$building_no}', lat_custom_field='{$adr_latitude}', lng_custom_field='{$adr_longitude}', source='{$form_refer}', doe='{$doe}'");
            $contactId = mysqli_insert_id($conn);
        }

        if ($contactId != '')
            $lead_group_id = time() . "-" . $contactId;
        else
            $lead_group_id = time();

        $resld = mysqli_query($conn, "insert into sp_lead_generate set lead_group_id='{$lead_group_id}',ip_city='{$ip_city}',device_type='{$device_type}',client_id='{$c_lient_Id}',lead_request='{$contactId}',lead_contact_no='{$mle}',source='{$form_refer}',ip_address='{$ref_ip_addr}', referal_url='{$ref_url}',engage_form_url='{$source_url}', source_platform='{$source_platform}', user_agent='{$useragent}', doe='{$doe}',lead_date='{$doe1}' {$forTrackingPurpose}");
        $leadInserted = mysqli_insert_id($conn);

        CommonStaticFunctions::update_event_track_data($conn,['url'=>$source_url,'ipaddress'=>$ref_ip_addr,'contact_id'=>$contactId,'email_id'=>$el,'client_id'=>$c_lient_Id,'referral_url'=>$ref_url,'contentType'=>$content_type,'camp_id'=>$camp_id,'useragent'=>$useragent,'visit_page'=>'homepage','source'=>$channel_type,'userid'=>$user_id]);

        //convert contact into prospect
        convert_into_known_visitor($c_lient_Id, $contactId);

        /// for notification added on 5-08-19 by prem
        if ($pcmember_pc_type == "C" && $device_token != '')
            CommonStaticFunctions::send_lead_push($conn, $sitepath, $lead_group_id, $c_lient_Id, $user_id, $device_token, $login_via);
        //end notification

        $personEmail = explode(',', $person_email);
        $ToSubjectAdmin = "New Lead Added Alert";
        $from_emails = "info@salespanda.com";
        $json_strings = array(
            'to' => $personEmail
        );
        $post_arr = [
            "agent_name" => $agent_name,
            "client_name" => bizight_sp_decryption($fn),
            "client_email" => bizight_sp_decryption($el),
            "client_phone" => bizight_sp_decryption($mle),
            "lead_from" => "Contact Us Form",
            "subject" => $ToSubjectAdmin,
            "getdmnURL" => $getdmnURL,
            "msgadd" => '',
            "lead_group_id" => $lead_group_id
        ];

        $tpl_url = "{$sitepath}template/mailer/lead_mailer.php";
        $params = array(
            'api_user' => $user,
            'api_key' => $pass,
            'x-smtpapi' => json_encode($json_strings),
            'to' => $personEmail,
            'subject' => $ToSubjectAdmin,
            'html' => get_mailer_template($tpl_url, $post_arr),
            'text' => 'New Lead Added Alert',
            'from' => $from_emails,
        );

        if (@$mailServer['mail_server'] == 'sendgrid') {
            $params_transactional = [
                "to" => $personEmail,
                "from" => [$from_emails => "SalesPanda"],
                "subject" => $ToSubjectAdmin,
                "text" => 'New Lead Added Alert',
                "html" => get_mailer_template($tpl_url, $post_arr),
                "custom_args" => ["server_name" => $_SERVER['HTTP_HOST']],
                "categories" => ['formbuilder']
            ];

            try {
                $sendgrid = new SPMailer\SendGridMailer($snGdPassword, 'transactional');
                $status = $sendgrid->send($params_transactional);
                $response = json_decode($status);
            } catch (Exception $e) {
                $response = (object) ["code" => $e->getCode(), "message" => $e->getMessage(), "file" => $e->getFile(), "line" => $e->getLine()];
            }
        } else {
            $result = (object) netcore_email($params);
            $response = (object) $result->message;
        }
        //Send Lead SMS to Child client
        if (isset($person_contact) && $person_contact !== '')
            CommonStaticFunctions::send_lead_sms($conn, $person_contact, $getdmnURL, $lead_group_id, $p_client_id, $c_lient_Id);
    }
    echo  json_encode(['status'=>1,'message'=>$message]);
}else{
    echo  json_encode(['status'=>0,'message'=>$message]);
}