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
if (!empty($_REQUEST['pdfname'])) {
    echo trim(testInput($_REQUEST['pdfname']));
}


$ToSubjectAdmin = '';
$fn = '';
$ln = '';
$el = '';
$mle = '';
$cmpny = '';
$cty = '';
$profession = '';
$education_level = '';
$form_refer = '';
$p_email = '';
$p_phone = '';
$person_contact = '';
$getdmnURL = '';

$googleCaptcha = validate_google_captcha('MicrositePage');
$message       = $googleCaptcha['message'];
$addLeadVar    = $googleCaptcha['status'];

if ($addLeadVar) {
    $user = $sndGdUser;
    $pass = $snGdPassword;
    $doe1 = date("Y-m-d");

    $client_id  = testInput($_REQUEST['c_lient_Id']);

    $form_refer = testInput($_REQUEST['form_refer']);
    $ref_url    = testInput($_REQUEST['ref_url']);
    $source_url = $_SERVER['HTTP_REFERER'];
    /********* For tracking *********/
    $content_type = $channel_type = '';
    $pid = $camp_id = 0;
    $campUrlData = [$ref_url,$source_url];
    foreach($campUrlData as $urlData){
        $urlData = str_replace('&amp;','&',$urlData);
        $tokens = parse_url($urlData);
        parse_str($tokens['query'], $query);
        if(!empty($query)){
            $content_type = isset($query['content'])?testInput($query['content']):$content_type;
            $camp_id = isset($query['camp_id'])?((!is_numeric($query['camp_id']))?decode(testInput($query['camp_id'])):testInput($query['camp_id'])):$camp_id;
            $channel_type = isset($query['channel_type'])?testInput($query['channel_type']):$channel_type;
            $pid = isset($query['pid'])?((!is_numeric($query['pid']))?decode(testInput($query['pid'])):testInput($query['camp_id'])):$pid;
            break;
        }
    }
    $forTrackingPurpose = ",contentType='".mysqli_real_escape_string($conn,$content_type)."',camp_id='{$camp_id}',added_by='{$pid}',campaign_source='".mysqli_real_escape_string($conn,$channel_type)."'";
    /*------------*/

    $pc_member_info = getPCMemberInfo($client_id);
    $pcmember_pc_type = $pc_member_info['member_pc_type'];
    $p_client_id = $pc_member_info['p_client_id'];
    $c_lient_Id = $_POST['c_lient_Id'];

    if ($client_id != '') {
        $get_email = "select * from sp_members where client_id = '{$client_id}' and company_member_type = '1' and approve = '1' and valid = '1'and deleted = '0'";
        $set_email = mysqli_query($conn, $get_email);
        while ($find_email = mysqli_fetch_assoc($set_email)) {
            if ($find_email["person_email"] != '') {
                $p_email .= $find_email["person_email"] . ',';
            }

            if ($find_email["person_contact1"] != '') {
                $p_phone .= $find_email["person_contact1"] . ',';
            }
        }

        $person_email = substr($p_email, 0, -1);
        $person_contact = substr($p_phone, 0, -1);

        $subdomain_qry = "select subdomain_url from sp_subdomain where client_id = '{$client_id}'";
        $qrydmn = mysqli_query($conn, $subdomain_qry);
        $getdmn = mysqli_fetch_assoc($qrydmn);
        $getdmnURL = "https://{$getdmn['subdomain_url']}";
    }

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
        $source_url = $_SERVER['HTTP_REFERER'];
        $useragent = $_SERVER['HTTP_USER_AGENT'];

        if ($ref_url != '') {
            $ref_url_arr = explode("/", $ref_url);
            $ref_dom_url = testInput($ref_url_arr[0]) . "//" . testInput($ref_url_arr[2]);

            $sqlsourceurl = "select * from sp_source_reference where url_pattern='" . $ref_dom_url . "' ";
            $rssourceurl = mysqli_query($conn, $sqlsourceurl);
            $rowsourceurl = mysqli_fetch_array($rssourceurl);
            $source_platform = $rowsourceurl['platform'];

            if ($source_platform == '') {
                $source_platform = "Other";
            }
        }

        // if ($_POST['full_name1'] != '') {
        //     if ($_POST['full_name1'] != 'undefined') {
        //         $fn = bizight_sp_encryption(testInput($_POST['full_name1']));
        //     } else {
        //         $fn = '';
        //     }
        // } else {
        //     header("location:index.php?successmsg=Please fill the name.");
        //     exit;
        // }

        if (isset($_POST['city']) && ($_POST['city'] != '' or $_POST['city'] != 'undefined')) {
            if ($_POST['city'] != 'undefined') {
                $cty = testInput($_POST['city']);
            } else {
                $cty = '';
            }
        }

        if (isset($_POST['state']) && ($_POST['state'] != '' or $_POST['state'] != 'undefined')) {
            if ($_POST['state'] != 'undefined') {
                $state = testInput($_POST['state']);
            } else {
                $state = '';
            }
        }

        if (isset($_POST['pincode']) && ($_POST['pincode'] != '' or $_POST['pincode'] != 'undefined')) {
            if ($_POST['pincode'] != 'undefined') {
                $pincode = testInput($_POST['pincode']);
            } else {
                $pincode = '';
            }
        }

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

        // if ($_POST['contact1'] != '') {
        //     $numlength = strlen((string) $_POST['contact1']);
        //     if ($numlength >= 10 && is_numeric($_POST['contact1'])) {
        //         $mle = bizight_sp_encryption(testInput($_POST['contact1']));
        //     } else {
        //         header("location:index.php?successmsg=Please fill the contact.");
        //         exit;
        //     }
        // } else {
        //     header("location:index.php?successmsg=Please fill the contact.");
        //     exit;
        // }

        if (isset($_POST['company_name1']) && ($_POST['company_name1'] != '' or $_POST['company_name1'] != 'undefined')) {
            if ($_POST['company_name1'] != 'undefined') {
                $cmpny = testInput($_POST['company_name1']);
            } else {
                $cmpny = '';
            }
        }

        if (isset($_POST['plan_segment']) && ($_POST['plan_segment'] != '' or $_POST['plan_segment'] != 'undefined')) {
            if ($_POST['plan_segment'] != 'undefined') {
                $plan_segment = testInput($_POST['plan_segment']);
            } else {
                $plan_segment = '';
            }
        }

        if (isset($_POST['address1']) && ($_POST['address1'] != '' or $_POST['address1'] != 'undefined')) {
            if ($_POST['address1'] != 'undefined') {
                $address = testInput($_POST['address1']);
            } else {
                $address = '';
            }
        }

        if (isset($_POST['your_message']) && ($_POST['your_message'] != '' or $_POST['your_message'] != 'undefined')) {
            if ($_POST['your_message'] != 'undefined') {
                $your_message = testInput($_POST['your_message']);
            } else {
                $your_message = '';
            }
        }

        if (isset($_POST['subarea']) && ($_POST['subarea'] != '' or $_POST['subarea'] != 'undefined')) {
            if ($_POST['subarea'] != 'undefined') {
                $plan_subarea = testInput($_POST['subarea']);
            } else {
                $plan_subarea = '';
            }
        }

        if (isset($_POST['plan_name']) && ($_POST['plan_name'] != '' or $_POST['plan_name'] != 'undefined')) {
            if ($_POST['plan_name'] != 'undefined') {
                $plan_name = testInput($_POST['plan_name']);
            } else {
                $plan_name = '';
            }
        }

        if (isset($_POST['building']) && ($_POST['building'] != '' or $_POST['building'] != 'undefined')) {
            if ($_POST['building'] != 'undefined') {
                $building_name = testInput($_POST['building']);
            } else {
                $building_name = '';
            }
        }
        $building_no = '';
        if (isset($_POST['building_no']) && ($_POST['building_no'] != '' or $_POST['building_no'] != 'undefined')) {
            if ($_POST['building_no'] != 'undefined') {
                $building_no = testInput($_POST['building_no']);
            }
        }

        if ($client_id == 'SP1542096255' && $form_refer == 'Form Customer Registration') {
            $adr_latitude = '';
            if ($_POST['adr_latitude'] != '' or $_POST['adr_latitude'] != 'undefined') {
                if ($_POST['adr_latitude'] != 'undefined') {
                    $adr_latitude = testInput($_POST['adr_latitude']);
                }
            }
            $adr_longitude = '';
            if ($_POST['adr_longitude'] != '' or $_POST['adr_longitude'] != 'undefined') {
                if ($_POST['adr_longitude'] != 'undefined') {
                    $adr_longitude = testInput($_POST['adr_longitude']);
                }
            }

            if ($adr_latitude == '' && $adr_longitude == '' && $cty != '' && $state != '') {
                $faddress = "India " . $state . " " . $cty . " " . $plan_subarea . " " . $building_name;
                $full_address = str_replace(" ", "+", $faddress);

                $gmapurl = "http://maps.google.com/maps/api/geocode/json?address=$full_address&sensor=false&region=India";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $gmapurl);
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

        if ($cty != '' or $cty != 'undefined') {
            $chkcity = "select * from sp_indian_districs where client_id='" . $client_id . "' and distrcit_name='" . $cty . "'";
            $cityres = mysqli_query($conn, $chkcity);
            $extCity = mysqli_num_rows($cityres);
            if ($extCity == 0) {
                $addcity = "insert into sp_indian_districs set client_id='" . $client_id . "',distrcit_name='" . $cty . "',doe='" . $doe . "'";
                $cityres = mysqli_query($conn, $addcity);
                $cityId = mysqli_insert_id($conn);
            } else {
                $cityData = mysqli_fetch_array($cityres);
                $cityId = $cityData['id'];
            }
        }

        if ($cmpny != '' or $cmpny != 'undefined') {
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

        if (isset($state) && ($state != '' or $state != 'undefined')) {
            $chkstate = "select * from sp_state where state_name='" . $state . "'";
            $stateres = mysqli_query($conn, $chkstate);
            $extstate = mysqli_num_rows($stateres);
            if ($extstate == 0) {
                $addstate = "insert into sp_state set state_name='" . $state . "'";
                $stateres = mysqli_query($conn, $addstate);
                $stateId = mysqli_insert_id($conn);
            } else {
                $stateData = mysqli_fetch_array($stateres);
                $stateId = $stateData['id'];
            }
        }

        $subQuery = '';
        if($el != '' && $mle != ''){
            $subQuery .= "(email_id='" . $el . "' OR mobile='" . $mle . "')";
        }else if($el != ''){
            $subQuery .= "email_id='".$el."'";
        }else if($mle != ''){
            $subQuery .= "mobile='".$mle."'";
        }

        $chkm = "select * from sp_contact where client_id='" . $client_id . "' and {$subQuery} and valid=1 and deleted=0";
        $resm = mysqli_query($conn, $chkm);
        $countcont = mysqli_num_rows($resm);
        if ($countcont > 0) {
            $contactData = mysqli_fetch_array($resm);
            $contactId = $contactData['id'];
            $checkmobile = $contactData['mobile'];
            $checkphone1 = $contactData['phone1'];
            $checkemail = $contactData['email_id'];
            $checksecondemail = $contactData['secondaryemail_id'];

            if ($fn == 'undefined') {
                $fn = "";
            }

            if ($ln == 'undefined') {
                $ln = "";
            }
            $fn = (isset($fn) && !empty($fn)) ? $fn: $contactData['first_name'];
            $stateId = (isset($stateId)) ? $stateId: $contactData['state'];
            $address = (isset($address) && !empty($address)) ? $address: $contactData['address'];
            $cityId = (isset($cityId)) ? $cityId: $contactData['city'];
            $pincode = (isset($pincode) && !empty($pincode)) ? $pincode: $contactData['pincode'];
            $compId = (isset($compId)) ? $compId: $contactData['comp_id'];
            $plan_segment = (isset($plan_segment) && !empty($plan_segment)) ? $plan_segment: $contactData['custom_field2'];
            $your_message = (isset($your_message) && !empty($your_message)) ? $your_message: $contactData['custom_field3'];
            $plan_subarea = (isset($plan_subarea) && !empty($plan_subarea)) ? $plan_subarea: $contactData['custom_field4'];
            $plan_name = (isset($plan_name) && !empty($plan_name)) ? $plan_name: $contactData['custom_field5'];
            $building_name = (isset($building_name) && !empty($building_name)) ? $building_name: $contactData['custom_field6'];
            $building_no = (isset($building_no) && !empty($building_no)) ? $building_no: $contactData['custom_field7'];
            $adr_latitude = (isset($adr_latitude) && !empty($adr_latitude)) ? $adr_latitude: $contactData['lat_custom_field'];
            $adr_longitude = (isset($adr_longitude) && !empty($adr_longitude)) ? $adr_longitude: $contactData['lng_custom_field'];

            $update_status = 1;
            $update_common_query =  "first_name='{$fn}',state='{$stateId}', address='{$address}',city='{$cityId}',custom_field1='{$pincode}',custom_field2='{$plan_segment}',custom_field3='{$your_message}', comp_id='{$compId}',custom_field4='{$plan_subarea}', custom_field5='{$plan_name}', custom_field6='{$building_name}', custom_field7='{$building_no}', lat_custom_field='{$adr_latitude}', lng_custom_field='{$adr_longitude}',dou='" . date('Y-m-d H:i:s') . "' where id='{$contactId}' and client_id='{$client_id}' and valid=1 and deleted=0";
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
            if ($fn == 'undefined') {
                $fn = "";
            }
            if ($ln == 'undefined') {
                $ln = "";
            }

            $addc1 = "insert into sp_contact set client_id='" . $client_id . "', address='" . $address . "',first_name='" . $fn . "', state='" . $stateId . "', city='" . $cityId . "',
		comp_id='" . $compId . "',email_id='" . $el . "',mobile='" . $mle . "',custom_field1='" . $pincode . "',custom_field2='" . $plan_segment . "', custom_field3='" . $your_message . "', custom_field4='" . $plan_subarea . "', custom_field5='" . $plan_name . "', custom_field6='" . $building_name . "', custom_field7='" . $building_no . "', lat_custom_field='" . $adr_latitude . "', lng_custom_field='" . $adr_longitude . "', source='" . $form_refer . "', doe='" . $doe . "'";
            $cntres = mysqli_query($conn, $addc1);
            $contactId = mysqli_insert_id($conn);
        }

        if ($contactId != '') {
            $lead_group_id = time() . "-" . $contactId;
        } else {
            $lead_group_id = time();
        }
        $addLead = "insert into sp_lead_generate set lead_group_id='" . $lead_group_id . "',ip_city='" . $ip_city . "',device_type='" . $device_type . "',client_id='" . $client_id . "',    lead_request='" . $contactId . "',lead_contact_no='" . $mle . "',source='" . $form_refer . "',ip_address='" . $ref_ip_addr . "', referal_url='" . $ref_url . "', engage_form_url='" . $source_url . "', source_platform='" . $source_platform . "', user_agent='" . $useragent . "', doe='" . $doe . "',lead_date='" . $doe1 . "' {$forTrackingPurpose}";
        $resld = mysqli_query($conn, $addLead);
        $leadInserted = mysqli_insert_id($conn);
        CommonStaticFunctions::update_event_track_data($conn,['url'=>$source_url,'ipaddress'=>$ref_ip_addr,'contact_id'=>$contactId,'email_id'=>$el,'client_id'=>$client_id,'referral_url'=>$ref_url,'contentType'=>$content_type,'camp_id'=>$camp_id,'useragent'=>$useragent,'visit_page'=>'homepage','source'=>$channel_type,'userid'=>$pid]);
        //convert contact into prospect
        convert_into_known_visitor($client_id, $contactId);

        /// for notification added on 5-08-19 by prem
        $pcmemqry1 = "SELECT P.pid, P.device_token, P.first_name, P.member_pc_type, P.login_via, P.comp_id, PC.* FROM sp_sub_members as PC INNER JOIN  sp_members as P ON PC.c_client_id = P.client_id  where PC.c_client_id = '{$client_id}' and P.valid = 1 and P.deleted = 0 and P.approve = 1";
        $pcmemb_ftch = mysqli_query($conn, $pcmemqry1);
        $row_pcmem = mysqli_fetch_array($pcmemb_ftch);

        $agent_name = $row_pcmem['first_name'];
        $pcmember_pc_type = $row_pcmem['member_pc_type'];
        $p_client_id = $row_pcmem['p_client_id'];
        $token = $row_pcmem['device_token'];
        $source_url = $_SERVER['HTTP_REFERER'];

        if ($pcmember_pc_type == "C" && $token != '') {
            $arrToken = array();
            $arrToken[0] = $token;

            if ($row_pcmem['login_via'] == 1) {

                $pushResult = sendIphonePushNotification($arrToken, 'New Lead', LEAD_MSG, 'lead', $lead_group_id, '', '', $sitepath . 'manager/social_img/lead.png');
            } else {

                //$pushResult = sendAndroidPushNotification($arrToken,'New Lead',LEAD_MSG,'lead',$lead_group_id,'','');
                $pushResult = sendAndroidPushNotification($arrToken, 'New Lead', LEAD_MSG, 'lead', $lead_group_id, '', '', $sitepath . 'manager/social_img/lead.png');
            }
            $finishSql = addPush($pushResult, $client_id, 'lead', '', '', $lead_group_id, $contactId, '', '', LEAD_MSG);
            mysqli_query($conn, $finishSql);
        }
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

        /*  New Lead SMS Code START */
        if (isset($person_contact) && $person_contact !== '')
            CommonStaticFunctions::send_lead_sms($conn, $person_contact, $getdmnURL, $lead_group_id, $p_client_id, $c_lient_Id);
        /************ New Lead SMS Code END **************/
    }

    echo  json_encode(['status'=>1,'message'=>$message]);
}else{
    echo  json_encode(['status'=>0,'message'=>$message]);
}
