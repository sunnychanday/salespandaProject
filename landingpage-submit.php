<?php
/*Author name: <Bizight Solutions Pvt Ltd> 
 * Purpose of document/ page : <This page is used to capture lead from landing page.> 
 * Date: 17-02-2021 
 * Copyright info : <Copyright @2021, Bizight Solutions Pvt Ltd>
*/
require realpath(__DIR__ . '/vendor/autoload.php');

include("includes/global.php");
include("includes/function.php");
include_once("includes/connect-new.php");
include("manager/common_functions.php");
include("csrf/csrf-magic.php");
include("includes/common_php_functions.php");


$fn = $ln = $el = $mle = $cty = $cmpny = $subject = $p_email = $p_phone = $pincode = $conditions = $profession = $education_level = $landingpage_Url = $_sanitize_phone = '';
$tpl_url = "{$sitepath}template/mailer/lead_mailer.php";
$googleCaptcha = validate_google_captcha('LandingPage::'.$_POST['page_id1']);
$message       = $googleCaptcha['message'];
$addLeadVar    = $googleCaptcha['status'];
if ($addLeadVar){

    $mailServer = getServerDetails();
    $fullLpath = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $client_id = $c_lient_Id = testInput($_POST['client_id']);
    $source_url = $_SERVER['HTTP_REFERER'];
    $micro_obj  = new \Microsite\Microsite($connPDO, ['referer_url'=>$source_url]);
    if ($micro_obj->microsite_exists === true) {
        $client_id = $micro_obj->client_id;
    }

    if(empty($c_lient_Id))
        $c_lient_Id = $client_id;

    $campaign_landingpage = $_REQUEST['campaign_landingpage'];
    $user = $sndGdUser;
    $pass = $snGdPassword;
    $doe1 = date("Y-m-d");

    //get child account details
    $pc_member_info = getPCMemberInfo($client_id);
    $pcmember_pc_type = ($pc_member_info['member_pc_type'] == 'P') ? 'P' : 'C' ;
    $p_client_id  = $pc_member_info['p_client_id'];
    $agent_name   = $pc_member_info['first_name'];
    $device_token = $pc_member_info['device_token'];
    $login_via    = $pc_member_info['login_via'];
    $person_email = $pc_member_info['person_email'];
    $person_contact = $pc_member_info['person_contact1'];

    $ip_city = CommonStaticFunctions::get_city_via_ipaddress();
    $device_type = CommonStaticFunctions::detect_device();
    $captcha = testInput($_REQUEST['captcha']);
    $ftype = testInput($_REQUEST['ftype']);
    $wp_type = $_REQUEST['wp_type'];
    $formdata= $_REQUEST['formdata'];
    if ($_SERVER["REQUEST_METHOD"] == "POST" && $captcha == 'sp1234')
    {
        $leadInserted = '';
        $doe = date("Y-m-d h:i:s");
        $page_id = testInput($_REQUEST['page_id1']);
        //$camp_id = testInput($_REQUEST['camp_id1']);
        //$contentType = testInput($_REQUEST['contentType']);
        $useragent = testInput($_SERVER ['HTTP_USER_AGENT']);
        $ref_url   = testInput($_REQUEST['ref_url1']);
        $ref_ip_addr = testInput(CommonStaticFunctions::get_remote_user_ip());

        /********* For tracking *********/
        $contentType = $micro_obj->url_params['content'] ?? testInput($_REQUEST['contentType']);
        $camp_id  = $micro_obj->url_params['camp_id'] ?? testInput($_REQUEST['camp_id1']);
        $channel_type  = $micro_obj->url_params['channel_type'] ?? $campaign_landingpage;
        $pid = $user_id = $micro_obj->url_params['pid'] ?? $pc_member_info['pid'] ;
        /*------------------------------*/
        $child_edit_status = getChildSyndLpageEditStatus($client_id, $page_id);
        if ($ref_url != '') {
            $ref_url_arr = explode("/", $ref_url);
            $ref_dom_url = $ref_url_arr[0] . "//" . $ref_url_arr[2];
            $rssourceurl = mysqli_query($conn, "select * from sp_source_reference where url_pattern = '{$ref_dom_url}'");
            $rowsourceurl = mysqli_fetch_array($rssourceurl);
            $source_platform = $rowsourceurl['platform'];
            if ($source_platform == '')
                $source_platform = "Other";
        }

        if ($_POST['fname'] != '' or $_POST['fname'] != 'undefined')
            $fn = ($_POST['fname'] != 'undefined') ? bizight_sp_encryption(testInput($_POST['fname'])) : '';

        if ($_POST['city'] != '' or $_POST['city'] != 'undefined')
            $cty = ($_POST['city'] != 'undefined') ? testInput($_POST['city']) : '';

        if ($_POST['age'] != '' or $_POST['age'] != 'undefined')
            $age = ($_POST['age'] != 'undefined') ? testInput($_POST['age']) : '';

        if ($_POST['lname1'] != '' or $_POST['lname1'] != 'undefined')
            $ln = ($_POST['lname1'] != 'undefined') ? bizight_sp_encryption(testInput($_POST['lname1'])) : '';

        if ($_POST['profession'] != '' or $_POST['profession'] != 'undefined')
            $profession = ($_POST['profession'] != 'undefined') ? testInput($_POST['profession']) : '';

        if ($_POST['education_level'] != '' or $_POST['education_level'] != 'undefined')
            $education_level = ($_POST['education_level'] != 'undefined') ? testInput($_POST['education_level']) : '';

        if ($_POST['email'] != '') {
            $checkEmailValid = emailValidity($_POST['email']);
            $el = ($checkEmailValid == 1) ? bizight_sp_encryption(testInput($_POST['email'])) : '' ;
        }

        if ($_POST['contact1'] != '' or $_POST['contact1'] != 'undefined') {
            if ($_POST['contact1'] != 'undefined') {
                $phone = testInput($_POST['contact1']);
                $numlength = strlen((string) $phone);
                $_sanitize_phone = bizight_sp_encryption(CommonStaticFunctions::sanitize_contact_number($phone));
                $mle = ($numlength >= 10 && is_numeric($phone)) ? bizight_sp_encryption(testInput($phone)) : '';
            } else {
                $mle = '';
            }
        }

        if ($_POST['company_name1'] != '' or $_POST['company_name1'] != 'undefined')
            $cmpny = ($_POST['company_name1'] != 'undefined') ? testInput($_POST['company_name1']) : '';

        if ($_POST['plan_ion'] != '' or $_POST['plan_ion'] != 'undefined')
            $plan_ion = ($_POST['plan_ion'] != 'undefined') ? testInput($_POST['plan_ion']) : '';

        $SipResultMsg ='';
        if ($wp_type == 'hlv_calc')
        {
            $amt_expect      = testInput($_REQUEST['amt_expect']);
            $total_liability = testInput($_REQUEST['total_liability']);
            $amt_needed      = testInput($_REQUEST['amt_needed']);
            $financial_asset = testInput($_REQUEST['financial_asset']);
            $life_cover = testInput($_REQUEST['life_cover']);
            $hlv_age    = testInput($_REQUEST['hlv_age']);
            $hlvresult  = $_REQUEST['hlv_result'];
            mysqli_query($conn, "insert into sp_hlvcalulator set client_id = '{$client_id}', amount_exp = '{$amt_expect}', total_liability = '{$total_liability}', amountneeded = '{$amt_needed}', financial_asset = '{$financial_asset}', life_cover = '{$life_cover}', hlv_age = '{$hlv_age}', result = '{$hlvresult}' ");
        }
        //new added wigets
        if ($wp_type == 'sip') {
            $fname = testInput($_REQUEST['fname']);
            $lname = testInput($_REQUEST['lname']);
            $email = testInput($_REQUEST['email']);
            $city = testInput($_REQUEST['city']);
            $phone = testInput($_REQUEST['phone']);
            $whts_appno = testInput($_REQUEST['whts_appno']);
            $sipresult = testInput($_REQUEST['sip_result']);
            $SipResultMsg = 'Calculated Sip: '.$sipresult;
            //$client_id = testInput($_REQUEST['c_lient_Id']);
            $amount = testInput($_REQUEST['amount']);
            $rateinterest = testInput($_REQUEST['rateinterest']);
            $timeperiod = testInput($_REQUEST['timeperiod']);

            $rescmp = mysqli_query($conn, "insert into sp_widget set client_id = '{$client_id}', f_name = '{$fname}', l_name = '{$lname}', email = '{$email}', phone_no = '{$phone}', whts_appno = '{$whts_appno}', wig_type = '{$wp_type}', city = '{$city}', age = '{$age}', education_level = '{$education_level}', profession = '{$profession}', company_name = '{$cmpny}'");
            $AddwigId = mysqli_insert_id($conn);

            if ($wp_type == 'sip')
                $rescmp = mysqli_query($conn, "insert into sp_sipcalulator set sp_wiget_id = '{$AddwigId}', amount = '{$amount}', rate = '{$rateinterest}', time_interval = '{$timeperiod}', fn_result = '{$sipresult}', formula = 'p*r'");
        }
        
        if($wp_type =='quiz')
        {
            $userfilldata = $_POST['user_survey'];
            if(!empty($userfilldata))
            {
                foreach($userfilldata as $userfill)
                {
                    $questiontxt = $userfill['questions'];
                    $answertxt = $userfill['answers'];
                    $iscorrect = $userfill['iscorrect'];
                    $q_id = $op_id = '';
                    $Getquestrow = mysqli_query($conn, "SELECT wq.id,wqopt.question_id, wqopt.id as wqopt_id FROM sp_wiget_questions as wq LEFT JOIN sp_widget_options as wqopt on wq.id = wqopt.question_id WHERE wq.question_txt LIKE '{$questiontxt}'  and wq.page_id ='{$page_id}'");
                    $GetquestData = mysqli_fetch_array($Getquestrow);
                    if(!empty($GetquestData)){
                        $q_id=$GetquestData['id'];
                        $op_id = $GetquestData['wqopt_id'];
                    }

                    mysqli_query($conn, "insert into sp_wig_survey set client_id = '{$client_id}', question_id = '{$q_id}', question = '{$questiontxt}', answer = '{$answertxt}', option_id = '{$op_id}', is_correct = '{$iscorrect}'");
                }
            }
        }

        if($wp_type !='' && $wp_type !='quiz'){
            $reponsedata = json_encode($_POST);
            mysqli_query($conn, "insert into sp_widget_result set client_id = '{$client_id}', result = '{$reponsedata}', wig_type = '{$wp_type}'");
        }

        //////GET FORM DETAIL, CLIENT ID and CTA
        if ($pcmember_pc_type == 'C') {
            $fqry = "select * from cta_button where landingpage_id = '{$page_id}' and client_id = '{$p_client_id}' and button_id = '0' and valid = 1 and deleted = 0";
        } else {
            $fqry = "select * from cta_button where landingpage_id = '{$page_id}' and client_id = '{$client_id}' and button_id = '0' and valid = 1 and deleted = 0";
        }
        $fres = mysqli_query($conn, $fqry);
        $fileData = mysqli_fetch_array($fres);
        $ctaType = $fileData['ctaType'];
        $ctaUrl = $fileData['cta_url_redirect'];
        $ctadownload = $fileData['cta_download_url'];
        $thank_msg = $fileData['cta_info_detail'];

        $cobrandquery = mysqli_query($conn, "select cobrand from user_templates where content_file = '{$ctadownload}' and client_id = '{$p_client_id}' and valid = 1 and deleted = 0");
        $cobrandget = mysqli_fetch_array($cobrandquery);

        if (empty($thank_msg))
            $thank_msg = "Thanks you.. ";
        $thank_msg_download = $fileData['cta_dwnld_thank_msg'];
        if ($pcmember_pc_type == 'C') {
            $fqry1 = "select * from sp_landingpage_manage where id = '{$page_id}' and client_id = '{$p_client_id}' and valid = 1 and deleted = 0";
        } else {
            $fqry1 = "select * from sp_landingpage_manage where id = '{$page_id}' and client_id = '{$client_id}' and valid = 1 and deleted = 0";
        }

        $fres1 = mysqli_query($conn, $fqry1);
        $fileData1 = mysqli_fetch_array($fres1);
        $email_notification = $fileData1['email_notification'];
        $page_name = $fileData1['page_name'];
        if ($pcmember_pc_type == 'C') {
            $fqry_publish = "select publish_page_name from sp_landingpage_publish where publish_page_id = '{$page_id}' and client_id = '{$p_client_id}'";
        } else {
            $fqry_publish = "select publish_page_name from sp_landingpage_publish where publish_page_id = '{$page_id}' and client_id = '{$client_id}'";
        }
        $fres_publish = mysqli_query($conn, $fqry_publish);
        $fileData_publish = mysqli_fetch_array($fres_publish);
        $publish_page_name = $fileData_publish['publish_page_name'];

        $resurl = mysqli_query($conn, "select * from sp_subdomain where client_id = '{$c_lient_Id}' and valid = 1 and deleted = 0");
        $pathData = mysqli_fetch_array($resurl);
        $redirectPath = trim($pathData['subdomain_url']);
        $landingpagePath = $pathData['cms_subdomain_url'];
        $getdmnURL = "https://{$pathData['subdomain_url']}";
        $comp_id = $pathData['comp_id'];
        $companyName = companyName($comp_id);

        $redirect_Url = "https://{$redirectPath}/manager/";
        $landingpage_Url = "https://{$landingpagePath}/landingpage/{$publish_page_name}";
        $user_id = $pathData['userid'];

        if ($cmpny != '' or $cmpny != 'undefined')
            $compId = CommonStaticFunctions::get_company_id($conn, $client_id, $cmpny);

        if ($cty != '' or $cty != 'undefined')
            $cityId = CommonStaticFunctions::get_city_id($conn, $client_id, $cty);


        if ($el != '' && $mle != '')
            $conditions = " and (email_id = '{$el}' OR mobile IN ('{$mle}','{$_sanitize_phone}'))";
        else if ($el != '' && $mle == '')
            $conditions = " and email_id = '{$el}'";
        else if ($el == '' && $mle != '')
            $conditions = " and mobile IN ('{$mle}','{$_sanitize_phone}')";

        $resm = mysqli_query($conn, "select * from sp_contact where client_id = '{$client_id}' and valid = 1 and deleted = 0 {$conditions}");
        $countcont = mysqli_num_rows($resm);
        if ($countcont > 0) {
            $contactData = mysqli_fetch_array($resm);
            $contactId = $contactData['id'];
            $checkmobile = $contactData['mobile'];
            $checkphone1 = $contactData['phone1'];
            $checkemail = $contactData['email_id'];
            $checksecondemail = $contactData['secondaryemail_id'];

            $fn = ($fn == 'undefined') ? "" : $fn;
            $ln = ($ln == 'undefined') ? "" : $ln;
            $fn = (isset($fn) && !empty($fn)) ? $fn: $contactData['first_name'];
            $ln = (isset($fn) && !empty($fn)) ? $ln: $contactData['last_name'];
            $cty = (isset($fn) && !empty($cty)) ? $cty: $contactData['city'];
            $cmpny = (isset($cmpny) && !empty($cmpny)) ? $cmpny: $contactData['comp_id'];
            $pincode = (isset($pincode) && !empty($pincode)) ? $pincode: $contactData['pincode'];
            $profession = (isset($profession) && !empty($profession)) ? $profession: $contactData['profession'];
            $education_level = (isset($education_level) && !empty($education_level)) ? $education_level: $contactData['educate_level'];

            $update_status = 1;
            $update_common_query =  "pincode = '{$pincode}', first_name = '{$fn}', last_name = '{$ln}', custom_field1 = '{$plan_ion}', city = '{$cty}', age = '{$age}', educate_level = '{$education_level}', profession = '{$profession}', comp_id = '{$compId}', dou = '" . date('Y-m-d H:i:s') . "' where id = '{$contactId}' and valid = 1 and deleted = 0";

            if ($mle != '' && ($checkmobile != $mle) && $checkmobile != '') {
                $cntres = mysqli_query($conn, "update sp_contact set phone1 = '{$mle}',{$update_common_query}");
                $update_status = 0;
            }else if($mle != '' && empty($checkmobile)){
                $cntres = mysqli_query($conn, "update sp_contact set mobile = '{$mle}',{$update_common_query}");
                $update_status = 0;
            }

            if ($el != '' && ($checkemail != $el) && $checkemail != '') {
                $cntres = mysqli_query($conn, "update sp_contact set secondaryemail_id = '{$el}',{$update_common_query}");
                $update_status = 0;
            }else if ($el != '' &&  empty($checkemail)) {
                $cntres = mysqli_query($conn, "update sp_contact set email_id='{$el}',{$update_common_query}");
                $update_status = 0;
            }

            //If inserted emailId is similar with contact's present email id then update all the inserted fields
            if($el != '' && ($checkemail == $el) && $checkemail != '' && ($update_status == 1)) {
                $u_mle = (isset($mle) && !empty($mle)) ? $mle: $contactData['mobile'];
                $cntres = mysqli_query($conn, "update sp_contact set mobile = '{$u_mle}',{$update_common_query}");
            }

        } else {
            $cntres = mysqli_query($conn, "insert into sp_contact set client_id = '{$client_id}', pincode = '{$pincode}', first_name = '{$fn}', custom_field1 = '{$plan_ion}', city = '{$cityId}', source = 'Landingpage', age = '{$age}', educate_level = '{$education_level}', profession = '{$profession}', last_name = '{$ln}', comp_id = '{$compId}', email_id = '{$el}', refral_cust_id = '{$_POST['cust_id']}', mobile = '{$mle}', doe = '{$doe}'");
            $contactId = mysqli_insert_id($conn);
        }

        if ($contactId != '')
        {
            $lead_group_id = time() . "-" . $contactId;
            $resld = mysqli_query($conn, "insert into sp_lead_generate set lead_group_id = '{$lead_group_id}', ip_city = '{$ip_city}', device_type = '{$device_type}', client_id = '{$client_id}', client_comp = '{$compId}', lead_request = '{$contactId}', lead_contact_no = '{$mle}', refral_cust_id = '{$_POST['cust_id']}', source = 'Landingpage lead', landingpage_id = '{$page_id}', camp_id = '{$camp_id}', contentType= '{$contentType}', ip_address = '{$ref_ip_addr}', referal_url = '{$ref_url}', engage_form_url = '{$source_url}', source_platform = '{$source_platform}', user_agent = '{$useragent}', doe = '{$doe}', lead_date = '{$doe1}',campaign_source='{$channel_type}',added_by='{$pid}'");
            $leadInserted = mysqli_insert_id($conn);

            //update event track if visit entry is not present regarding this entry
            CommonStaticFunctions::update_event_track_data($conn,['url'=>$source_url,'ipaddress'=>$ref_ip_addr,'contact_id'=>$contactId,'email_id'=>$el,'client_id'=>$client_id,'referral_url'=>$ref_url,'contentType'=>$contentType,'camp_id'=>$camp_id,'useragent'=>$useragent,'visit_page'=>'landingpage','source'=>$channel_type,'userid'=>$user_id]);

            //convert contact into prospect
            convert_into_known_visitor($client_id, $contactId);
        }

        if ($pcmember_pc_type == "C" && $device_token != '')
            CommonStaticFunctions::send_lead_push($conn, $sitepath, $lead_group_id, $client_id, $pid, $device_token, $login_via);
        $checkcnt =1;
        if ($ctaType == 3) {
            $checkcnt =0;
            if ($pcmember_pc_type == 'C')
                $pathpdf = "https://{$landingpagePath}/upload/casestudy/{$p_client_id}/{$ctadownload}";
            else
                $pathpdf = "https://{$landingpagePath}/upload/casestudy/{$client_id}/{$ctadownload}";
            echo "<script language=\"javascript\">window.open('{$pathpdf}',\"_blank\");</script>";
            echo '<div style="font-family:Arial, Helvetica, sans-serif;font-weight:normal;font-size:48px;margin:15% auto; width:30%; color:#ffffff" onclick="window.location.reload();">' . $thank_msg_download . '</div>';
        }

        if ($ctaType == 2) {
            $checkcnt =0;
            if (strpos($ctaUrl, 'https://') !== 0)
                $newurl = "https://{$ctaUrl}";
            else
                $newurl = $ctaUrl;
            echo "<script language=\"javascript\">window.open('{$newurl}',\"_blank\");</script>";
        }

        if ($ctaType == 1) {
            $checkcnt =0;
            echo '<div style="font-family:Arial, Helvetica, sans-serif;font-weight:normal;font-size:22px;line-height:38px;margin:12% auto 5% auto; padding:5% 2%; width:50%; color:#333333;background:#ffffff;border:5px solid #cccccc;border-radius:5px;">' . $thank_msg .'</div>';
        }

        $Email_notification = explode(',', $email_notification);
        $ToSubject = "Download landingpage";
        $from_email = "info@salespanda.com";
        $post_arr = [
            "fn" => bizight_sp_decryption($fn),
            "ln" => bizight_sp_decryption($ln),
            "el" => bizight_sp_decryption($el),
            "mle" => bizight_sp_decryption($mle),
            "cty" => $cty,
            "cmpny" => $cmpny,
            "subject" => $ToSubject,
            "sitepath" => $sitepath,
            "profession" => $profession,
            "education_level" => $education_level,
            "landingpage_Url" => $landingpage_Url
        ];

        $post_arr = [
            "agent_name" => $agent_name,
            "client_name" => bizight_sp_decryption($fn) . ' ' . bizight_sp_decryption($ln),
            "client_email" => bizight_sp_decryption($el),
            "client_phone" => bizight_sp_decryption($mle),
            "lead_from" => "Landing Page",
            "subject" => $ToSubject,
            "getdmnURL" => $getdmnURL,
            "msgadd" => '',
            "lead_group_id" => $lead_group_id
        ];

        if (@$mailServer['mail_server'] == 'sendgrid') {
            $params_transactional = [
                "to" => $Email_notification,
                "from" => [$from_email => "SalesPanda"],
                "subject" => $ToSubject,
                "text" => $ToSubject,
                "html" => get_mailer_template($tpl_url, $post_arr),
                "custom_args" => ["server_name" => $_SERVER['HTTP_HOST']],
                "categories" => ['landing-page']
            ];

            try {
                $sendgrid = new SPMailer\SendGridMailer($snGdPassword, 'transactional');
                $status = $sendgrid->send($params_transactional);
                $response = json_decode($status);
            } catch (Exception $e) {
                $response = (object) ["code" => $e->getCode(), "message" => $e->getMessage(), "file" => $e->getFile(), "line" => $e->getLine()];
            }
        } else {
            $json_string = ['to' => $Email_notification];
            $params = array(
                'api_user' => $user,
                'api_key' => $pass,
                'x-smtpapi' => json_encode($json_string),
                'to' => $Email_notification,
                'subject' => $ToSubject,
                'html' => get_mailer_template($tpl_url, $post_arr),
                'text' => 'Download landingpage',
                'from' => $from_email,
            );

            $result = (object) netcore_email($params, $transactional_password);
            $response = (object) $result->message;
        }

        $personEmail = explode(',', $person_email);
        //Added By Softprodigy for Lead Schedule event
        $resultL = mysqli_query($conn, "select type from lead_schedule where client_id='{$client_id}'");
        $mainCount = mysqli_num_rows($resultL);
        $leadType = '';
        if ($mainCount > 0) {
            $dataL = mysqli_fetch_array($resultL);
            if ($dataL['type'] != '') {
                $dataType = explode(',', $dataL['type']);
                if (in_array('1', $dataType))
                    $leadType = 1;
            }
        } else {
            $leadType = 1;
        }

        if ($leadType == 1)
        {
            $response = '';
            $ToSubjectAdmin = "New Lead Added using SalesPanda landingpage";
            $from_emails = "info@salespanda.com";
            $post_arr = [
                "agent_name" => $agent_name,
                "client_name" => bizight_sp_decryption($fn) . ' ' . bizight_sp_decryption($ln),
                "client_email" => bizight_sp_decryption($el),
                "client_phone" => bizight_sp_decryption($mle),
                "lead_from" => "Landing Page",
                "subject" => $ToSubject,
                "getdmnURL" => $getdmnURL,
                "msgadd" => '',
                "lead_group_id" => $lead_group_id
            ];

            if (@$mailServer['mail_server'] == 'sendgrid') {
                $params_transactional = [
                    "to" => $personEmail,
                    "from" => [$from_emails => "SalesPanda"],
                    "subject" => $ToSubjectAdmin,
                    "text" => $ToSubjectAdmin,
                    "html" => get_mailer_template($tpl_url, $post_arr),
                    "custom_args" => ["server_name" => $_SERVER['HTTP_HOST']],
                    "categories" => ['login-otp']
                ];

                try {
                    $sendgrid = new SPMailer\SendGridMailer($snGdPassword, 'transactional');
                    $status = $sendgrid->send($params_transactional);
                    $response = json_decode($status);
                } catch (Exception $e) {
                    $response = (object) ["code" => $e->getCode(), "message" => $e->getMessage(), "file" => $e->getFile(), "line" => $e->getLine()];
                }
            } else {
                $json_strings = ['to' => $personEmail];
                $params = array(
                    'api_user' => $user,
                    'api_key' => $pass,
                    'x-smtpapi' => json_encode($json_strings),
                    'to' => $personEmail,
                    'subject' => $ToSubjectAdmin,
                    'html' => get_mailer_template($tpl_url, $post_arr),
                    'text' => 'New Lead Added using SalesPanda landingpage',
                    'from' => $from_emails,
                );

                $result = (object) netcore_email($params, $transactional_password);
                $response = (object) $result->message;
            }
        }

        //Send Lead SMS to Child client
        if (isset($person_contact) && $person_contact !== '')
            CommonStaticFunctions::send_lead_sms($conn, $person_contact, $getdmnURL, $lead_group_id, $p_client_id, $client_id);
    }
    if(isset($checkcnt) && ($checkcnt == 1))
        echo  json_encode(['status'=>1,'message'=>$message]);
}else{
    echo  json_encode(['status'=>0,'message'=>$message]);
}
exit;