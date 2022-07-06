<?php

/* Author name: <Bizight Solutions Pvt Ltd> 
 * Purpose of document/ page : This was uses to track visitors activity 
 * Date:  16-02-2021 
 * Copyright info : <Copyright @2021, Bizight Solutions Pvt Ltd>
 */
header("Access-Control-Allow-Origin: *");
header("Expires: " . gmdate("D, d M Y H:i:s", time()) . " GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

include("includes/global.php");
include("includes/function.php");
include("manager/common_functions.php");
include_once("includes/connect-new.php");
include("geoiploc.php");

/**
 * Start A New Prospect Engagement Process /*** (: (: Hurray!!! :) :)
 * ProspectEngagement is the library to handle all the prospects related events
 * Added By : Dinesh Kashyap [02 March 2021]
 * */
require realpath(__DIR__ . '/vendor/autoload.php');
include_once "manager/classes/microsite/prospectengagement.class.php";

$_captcha_token = ($_REQUEST['recaptcha_token']) ?? '';
if(!empty($_captcha_token)){
    $_responseKeys = google_captcha_validate_request($_captcha_token);
    if($_responseKeys["success"] != '1'  && $_responseKeys["score"] < 0.5)
        die('Invalid Captcha Request!');
}

$prospect_obj = new ProspectEngagement($connPDO); //need to include file "includes/connect-new.php" to get $connPDO object
$microsite_obj  = new \Microsite\Microsite($connPDO, ['referer_url'=>$_REQUEST['url']]);
$microsite_exist = false;
if ($microsite_obj->microsite_exists) {
    $microsite_exist = true;
    $prospect_obj->user_id = $microsite_obj->user_id;
    $prospect_obj->c_client = $microsite_obj->client_id;
    $prospect_obj->user_type = $microsite_obj->account_type;
}

$c_lient_Id = $microsite_obj->client_id;
$time = $visitTime = $emailId = $campId = $endTime = $pid = $content = $source = '';
$prospect_notification_sent = $prospect_notification_already_sent = 0;
//$prospect_obj->keep_nudges_event_log('testing_log',json_encode(['IP Address >>> '=>$_SERVER['REMOTE_ADDR'],'Request >>> '=>$_REQUEST]));
if (isset($_REQUEST['tkn']) && $_REQUEST['tkn'] != 1) {
    if (isset($_REQUEST['start']) && $_REQUEST['start'] != '') {
        $datumUhrzeit = substr($_REQUEST['start'], 0, strpos($_REQUEST['start'], '('));
        $time = strtotime($datumUhrzeit);
        if ($time !== false)
            $visitTime = date('Y-m-d H:i:s', $time);
    }

    if (isset($_REQUEST['end']) && $_REQUEST['end'] != '') {
        $datumUend = substr($_REQUEST['end'], 0, strpos($_REQUEST['end'], '('));
        $end = strtotime($datumUend);
        $endTime = date('Y-m-d H:i:s', $end);
    }

    if (isset($_REQUEST['timeSpent']) && $_REQUEST['timeSpent'] != '')
        $timeSpent = testInput($_REQUEST['timeSpent']);

    $contact = 0;
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $content = $microsite_obj->url_params['content'] ?? '';
    $campId  = $microsite_obj->url_params['camp_id'] ?? 0;
    $source  = $microsite_obj->url_params['channel_type'] ?? '';
    $pid = $microsite_obj->user_id ?? 0;

    if(isset($_REQUEST['c']) && $_REQUEST['c'] != '')
        $contact = $prospect_obj->checkContact($_REQUEST['c']);
    if (isset($_REQUEST['vtoken']) && $_REQUEST['vtoken'] != '')
        $vtoken = testInput($_REQUEST['vtoken']);
    if (isset($_REQUEST['uemail']) && $_REQUEST['uemail'] != '') {
        $emailId = bizight_sp_encryption(testInput($_REQUEST['uemail']));
        // updated to get contact id if not exist 17-7-19 by prem
        if (!empty($emailId)) {
            $email_contact = $prospect_obj->get_active_contact_id($emailId);
            $contact = ($email_contact == $contact) ? $contact : 0 ;
        }
    }

    if ($_REQUEST['tkn'] == 4)
        $res = mysqli_query($conn, "UPDATE sp_eventtrack set emailid = '{$emailId}',contact_id='{$contact}' WHERE visitdate = '{$visitTime}' AND visitor_token = '{$vtoken}' and client_id = '{$c_lient_Id}'");
    elseif ($endTime != '')
        $res = mysqli_query($conn, "UPDATE sp_eventtrack set emailid = '{$emailId}',contact_id='{$contact}', end_time = '{$endTime}', timeSpent = '{$timeSpent}' WHERE visitdate = '{$visitTime}' and visitor_token = '{$vtoken}' and client_id = '{$c_lient_Id}'");
    else {

        $multi_visit_check = CommonStaticFunctions::protect_malicious_multi_visits($conn,['emailid'=>$emailId,'contact_id'=>$contact,'camp_id'=>$campId,'contentType'=>$content,'client_id'=>$c_lient_Id,'source'=>$source,'visit_page'=>$_REQUEST['visit_page']]);
        if(!$multi_visit_check){

            $mic = (isset($_REQUEST['mic']) && $_REQUEST['mic'] == 1) ? 1 : 0;
            if ($microsite_exist) {
                if (isset($contact) && $contact != 0) {
                    $prospect_obj->prospect_id = $contact;
                    $contact_details = $prospect_obj->get_sp_contact_details();
                    if (isset($contact_details['known']) && $contact_details['known'] != '1')
                        $prospect_notification_already_sent = 1;
                }
            }

            $res = mysqli_query($conn, "insert into sp_eventtrack set client_id = '{$c_lient_Id}', emailid = '{$emailId}', contentType='{$content}',camp_id = '{$campId}', useragent = '{$userAgent}', visitdate = '{$visitTime}', visit_page = '" . testInput($_REQUEST['visit_page']) . "', source = '{$source}', url = '" . urldecode($_REQUEST['url']) . "', ip_address = '" . testInput(CommonStaticFunctions::get_remote_user_ip()) . "', visitor_token = '{$vtoken}', referring = '" . testInput($_REQUEST['HTTP_REFERER']) . "', contact_id = {$contact}, microsite = {$mic}, userid='{$pid}'");
            $event_track_id = mysqli_insert_id($conn);

            if (isset($contact) && $contact != 0) {
                mysqli_query($conn, "update sp_contact set known = 1, known_date = '" . date('Y-m-d H:i:s') . "', vtoken = '{$vtoken}' where id = '{$contact}' and client_id = '{$c_lient_Id}' and vtoken != '{$vtoken}'");

                if (mysqli_affected_rows($conn) == 1) {
                    mysqli_query($conn, "insert into known_visitors set event_track_id='{$event_track_id}', client_id = '{$c_lient_Id}', known_date = '" . date('Y-m-d H:i:s') . "',  vtoken = '{$vtoken}', contact_id = {$contact}");

                    if ($microsite_exist && ($prospect_notification_already_sent == '1')) {
                        $push_status = $prospect_obj->initiate_prospect_engagement_push($contact);
                        $prospect_notification_sent = ($push_status) ? 1 : 0;
                    }
                }
            }

            // code segment for visit notification
            if (!empty($emailId) || $contact == '0') {
                $sentNotification = 1; $sub_query = '';

                if(!empty($emailId))
                    $sub_query .= " and emailid = '{$emailId}'";
                if($contact !='0')
                    $sub_query .= " and contact_id='{$contact}'";

                $eventSQL = mysqli_query($conn, "select contact_id, eventid, notificationSent from  sp_eventtrack where client_id = '{$c_lient_Id}' {$sub_query} and visitdate like '" . date("Y-m-d H") . "%' group by notificationSent");
                if (mysqli_num_rows($eventSQL) > 0) {   //send after first visit by user
                    while ($eventData = mysqli_fetch_assoc($eventSQL)) {
                        if ($eventData['notificationSent'] == 1) { //sent push site visit push notification status
                            $sentNotification = 0;
                            break;
                        }
                    }
                }
                if ($sentNotification == 1 && $prospect_notification_sent == 0) {
                    if ($microsite_exist) {
                        $prospect_obj->prospect_id = $contact;
                        $push_status = $prospect_obj->contact_visit_push_notification($contact); //send micro-site visit notification
                    }
                    mysqli_query($conn, "update sp_eventtrack set notificationSent = 1 where client_id = '{$c_lient_Id}' and notificationSent = 0 {$sub_query} and visitdate like '" . date("Y-m-d") . "%'");
                }
            }
        }
    }
}
