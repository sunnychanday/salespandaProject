<?php

require realpath(__DIR__ . '/vendor/autoload.php');

include("includes/global.php");
include("includes/connect.php");
include("includes/function.php");

session_destroy();

$url = 'https://api.sendgrid.com/';

//$token 	= '26a40516-98da-4773-bd0f-90b62f3586d9';
$token = $_GET['stoken'];
$gatePass = array('token' => $token);
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, trim('https://api.hdfcfund.com/distributor/v2/dashboard/campaign/validate'));
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'TP-Secret-Key:HDFC_TP_KEY', 'TP-Name:SALES_PANDA'));
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($gatePass));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

//$response = '{"isVerifiedSource": true, "arnCode": "ARN-34976" , "email": "basheer3@gmail.com", "ListName": "DistriList-06", "toemails": ["basheer.mca@gmail.com","basheeruddin.sidique@bizight.com"], "camp_id": "1482"}';
//$response = '{"statusCode":"OK","message":"SUCCESS","data":{"arnCode":"ARN-0411","email":"richa.apurva@gmail.com","toemails":["krishnamannu@yahoo.co.in","yogesh.bhowar7@gmail.com","svbetgiri@yahoo.com","kraghu8@yahoo.com"],"camp_id":"1482","verifiedSource":true,"listname":"Campaign_List","isVerifiedSource":true},"status":true}';
//$response = '{"statusCode":"OK","message":"SUCCESS","data":{"arnCode":"ARN-34976","email":"basheer3@gmail.com","phone": "8549648540","toemails":["basheer.mca@gmail.com","basheeruddin.sidique@bizight.com"],"camp_id":"1482","verifiedSource":true,"listname":"Campaign_List-2","isVerifiedSource":true},"status":true}';
//echo $response; exit;

$source_ip = get_remote_user_ip();
$ref_url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

//$responseArray = array();
$responseArray = json_decode($response);

if ($responseArray->data->isVerifiedSource == true) {
    $appQuery = "select * from sp_admin where valid=1 and deleted=0 and password='apptech'";

    $appRes = mysql_query($appQuery) or die("Error in sp_admin" . mysql_error());
    $appExt = mysql_fetch_array($appRes);
    $appname = trim($appExt['username']);
    $userEmail = trim($responseArray->data->email);
    $mobileNo = trim($responseArray->data->phone);
    $arnCode = trim($responseArray->data->arnCode);
    $listName = trim($responseArray->data->listname);
    $camp_id = trim($responseArray->data->camp_id);
    $contactExp = $responseArray->data->toemails;

    $toemails = implode(",", $contactExp);

    if ($userEmail == '' || $arnCode == '' || $listName == '' || $camp_id == '' || $toemails == '') {
        $get_array = array("status" => "502", "message" => "Please check parameters it's value may be empty.");
    } else {


        $sub_mem_sql = "SELECT c_client_id FROM `sp_sub_members` WHERE urn_no = '" . $arnCode . "' and valid = 1 and deleted = 0 ";
        $sub_mem_rs = mysql_query($sub_mem_sql) or die(mysql_error());
        $sub_mem_num = mysql_num_rows($sub_mem_rs);

        if ($sub_mem_num > 0) {
            $sub_mem_row = mysql_fetch_array($sub_mem_rs);

            $mem_sql = "SELECT pid,client_id,person_email,person_contact1 FROM sp_members WHERE client_id='" . $sub_mem_row['c_client_id'] . "' AND company_member_type = 1 AND approve = 1 AND valid = 1 AND deleted = 0";
            $mem_rs = mysql_query($mem_sql);
            $mem_num = mysql_num_rows($mem_rs);
            $mem_row = mysql_fetch_array($mem_rs);

            if ($mem_row['person_email'] != $userEmail || ($mobileNo != '' && $mem_row['person_contact1'] != $mobileNo)) {

                //UPDATE SP MEMBERS INFO IF email and contact changes
                $updmem = "update sp_members set person_email='" . $userEmail . "', person_contact1='" . $mobileNo . "' where pid='" . $mem_row['pid'] . "' AND client_id='" . $mem_row['client_id'] . "' AND valid = 1 AND deleted = 0 ";
                mysql_query($updmem) or die(mysql_error());
            }
        }

        $query = "SELECT * FROM `sp_members` as m LEFT JOIN sp_sub_members as sm ON m.client_id = sm.c_client_id WHERE m.person_email ='" . $userEmail . "' and sm.urn_no = '" . $arnCode . "' and sm.valid = 1 and sm.deleted = 0 and m.valid = 1 and m.deleted = 0 and m.approve = 1";
        $rslogin = mysql_query($query) or die(mysql_error());
        $nummember = mysql_num_rows($rslogin);

        if ($nummember > 0) {
            session_start();
            $refer = "";
            $refer = $_SESSION["refer"];
            $memberdata = mysql_fetch_array($rslogin);

            $email = $memberdata['person_email'];
            $userid = $memberdata['pid'];
            $valid = $memberdata['valid'];
            $deleted = $memberdata['deleted'];
            $approveMember = $memberdata['approve'];
            $fname = $memberdata['first_name'];
            $lname = $memberdata['last_name'];
            $comp_ids = $memberdata['comp_id'];

            $twitter_id = $memberdata['twitter_id'];
            $TwitterUsername = $memberdata['twitter_username'];
            $twitter_name = $memberdata['twitter_name'];
            $twitter_aouth_key = $memberdata['twitter_aouth_key'];
            $twitter_aouth_secret = $memberdata['twitter_aouth_secret'];

            if ($memberdata['person_contact1'] != '') {
                $c_contact = $memberdata['person_contact1'];
            } else {
                $c_contact = $memberdata['person_contact2'];
            }

            $aboutme = $memberdata['about_me'];
            $userType = $memberdata['member_pc_type'];

            // check campaign exists or not
            $chkCamp = "select * from sp_maildetails where mail_id='" . $camp_id . "' and client_id ='" . $memberdata['p_client_id'] . "' AND main_campaign = 1 AND syndication_status=1 AND status=1 AND deleted = 0";
            $resCamp = mysql_query($chkCamp) or die(mysql_error());
            $campData = mysql_fetch_array($resCamp);
            $campNum = mysql_num_rows($resCamp);

            $campLpageId = ($campData['publish_page_id'] != 0) ? $campData['publish_page_id'] : '';

            $chksdq = "select * from sp_subdomain where subdomain_url='" . $appname . "' and comp_id='" . $comp_ids . "' and valid=1 and deleted=0 and status=1";
            $ressd = mysql_query($chksdq) or die(mysql_error());
            $sdData = mysql_fetch_array($ressd);

            $c_lient_Id = $sdData['client_id'];
            $comp_id = $sdData['comp_id'];

            if ($c_lient_Id == '' && $comp_id == '') {
                header("location:login.php?errmsg=Invalid_User");
                exit;
            }

            if ($valid == 1 and $deleted == 0 and $approveMember == 1 and $comp_id != '' and $c_lient_Id != '') {
                session_start();

                $_SESSION['email'] = $email;
                $_SESSION['userid'] = $userid;
                $_SESSION['c_lient_Id'] = $c_lient_Id;
                $_SESSION['comp_id'] = $comp_id;
                $_SESSION['u_name'] = $fname;
                $_SESSION['twitter_id'] = $twitter_id;
                $_SESSION['TwitterUsername'] = $TwitterUsername;
                $_SESSION['twitter_name'] = $twitter_name;
                $_SESSION['twitter_aouth_key'] = $twitter_aouth_key;
                $_SESSION['twitter_aouth_secret'] = $twitter_aouth_secret;
                $_SESSION['user_type'] = $userType;
                $_SESSION['about_me'] = $aboutme;

                $pc_member_info = getPCMemberInfo($c_lient_Id);
                $pcmember_pc_type = $pc_member_info['member_pc_type'];
                $p_client_id = $pc_member_info['p_client_id'];

                //insert table for last login information 
                $qry_last_lgn = "insert into sp_login_record set client_id='" . $c_lient_Id . "',
									 member_id='" . $userid . "',
									 login_email='" . $email . "',
									 login_date='" . date('Y-m-d h:i:s') . "',
                                     from_status='api', 
									 ip_address='" . $source_ip . "'";
                $res_last_login = mysql_query($qry_last_lgn) or die(mysql_error());

                // api logs insert entries
                $loginlogsql = "insert into sp_login_api_logs set p_client_id='" . $p_client_id . "',                        
                        			soruce_ip='" . $source_ip . "',
                        			status_type='Existing User',
                        			stoken='" . $token . "',
                        			response1='" . $response . "',
                        			ref_url='" . $ref_url . "'";

                $loginlog_rs = mysql_query($loginlogsql);
                //$last_loginlog_id = mysql_insert_id();
                // check and approve landingpage for campaign 
                if ($campLpageId != 0) {
                    $chklpagesql = "select lsyndid, approve from sp_landingpage_syndication where landingpage_id='" . $campLpageId . "' AND p_client_id ='" . $memberdata['p_client_id'] . "' and c_client_id='" . $c_lient_Id . "' ";
                    $reslpage = mysql_query($chklpagesql) or die(mysql_error());
                    $numlpage = mysql_num_rows($reslpage);

                    if ($numlpage == 0) {
                        $addsyndcont = "insert into sp_landingpage_syndication set p_client_id='" . $memberdata['p_client_id'] . "', c_client_id='" . $c_lient_Id . "', landingpage_id='" . $campLpageId . "', approve=1, doe='" . date('Y-m-d h:i:s') . "'";
                        $ressyndcont = mysql_query($addsyndcont) or die(mysql_error());
                    } else {
                        $rowlpage = mysql_fetch_array($reslpage);
                        $clpageApprove = $rowlpage['approve'];
                    }
                }


                if ($camp_id != '' && $campNum > 0) {

                    for ($cn = 0; $cn < sizeof($contactExp); $cn++) {
                        $spcontact = "select * from sp_contact where client_id='" . $c_lient_Id . "' and email_id='" . $contactExp[$cn] . "' and valid=1 and deleted=0";
                        $spcontactget = mysql_query($spcontact) or die(mysql_error());
                        $spcontactcount = mysql_num_rows($spcontactget);
                        $spcontactset = mysql_fetch_array($spcontactget);


                        if ($spcontactcount == 0) {
                            $spcontactInsert = "insert into sp_contact set client_id='" . $c_lient_Id . "',email_id='" . $contactExp[$cn] . "',doe='" . date('Y-m-d h:i:s') . "'";
                            mysql_query($spcontactInsert) or die(mysql_error());

                            $spcontactId[] = mysql_insert_id();
                        } else {
                            $spcontactId[] = $spcontactset['id'];
                        }
                    }

                    //$listName="DistriList-".$c_lient_Id;             
                    $splist = "select * from sp_contact_list where client_id='" . $c_lient_Id . "' and contact_list_name='" . $listName . "'";
                    $splistget = mysql_query($splist) or die(mysql_error());
                    $splistset = mysql_fetch_array($splistget);
                    $splistcount = mysql_num_rows($splistget);

                    if ($splistcount == 0) {
                        $splist01 = "insert into sp_contact_list set client_id='" . $c_lient_Id . "',contact_list_name='" . $listName . "',list_create_time='" . date('Y-m-d h:i:s') . "'";
                        $splistget01 = mysql_query($splist01) or die(mysql_error());
                        $listId = mysql_insert_id();
                    } else {
                        $listId = $splistset['list_id'];
                    }

                    for ($ls = 0; $ls < sizeof($spcontactId); $ls++) {
                        $list_fetch = "select * from sp_list_details where contact_id='" . $spcontactId[$ls] . "' and list_id='" . $listId . "' and client_id='" . $c_lient_Id . "' and valid=1 and deleted=0";
                        $list_set = mysql_query($list_fetch);
                        $listcontactcount = mysql_num_rows($list_set);

                        if ($listcontactcount == 0) {
                            $inslistcontact = "insert into sp_list_details set list_id='" . $listId . "',client_id='" . $c_lient_Id . "',contact_id ='" . $spcontactId[$ls] . "'";
                            mysql_query($inslistcontact) or die(mysql_error());
                        }
                    }

                    // get mailserver information
                    $mailServerSql = mysql_query("SELECT * FROM sp_mail_servers WHERE client_id='" . $c_lient_Id . "' and status=1");
                    $mailServerCidNum = mysql_num_rows($mailServerSql);

                    if ($mailServerCidNum == 0) {
                        $mailServerSql = mysql_query("SELECT * FROM sp_mail_servers WHERE client_id='' and status=1");
                    }

                    $mailServer = mysql_fetch_array($mailServerSql);

                    $api_user = $mailServer['server_userid'];
                    $api_pass = $mailServer['server_password'];
                    $mail_server_name = $mailServer['mail_server'];

                    $sqlmail = "INSERT into sp_maildetails (`userid`, `client_id`,`allocated_to`,`camp_name`,`categories`, `attachment_option`,`attachment`,`url_insert`,`internal_hashtag`, `partner_category`, `mail_dtime`, `to_mail`, `from_mail`, `from_name`, `reply_email`, `list_id`, `subject`, `message`, `pemission_reminder`, `reminder_text`, `temp_id`,`created_on`,`main_campaign`,`publish_page_id`,`mail_server`)
	select '" . $userid . "', `client_id`,'" . $c_lient_Id . "',`camp_name`,`categories`, `attachment_option`,`attachment`,`url_insert`,`internal_hashtag`, `partner_category`, '" . date("Y-m-d h:i:sa") . "', '" . $toemails . "', '" . $userEmail . "', '" . $fname . "','" . $userEmail . "', '" . $listId . "',`subject`, `message`, `pemission_reminder`, `reminder_text`, `temp_id`,'" . date('Y-m-d H:i:s') . "','2', `publish_page_id`, '" . $mail_server_name . "' from sp_maildetails where mail_id = '" . $camp_id . "' ";

                    mysql_query($sqlmail);

                    $districamp_id = mysql_insert_id();


                    // get mail information
                    $rs_spmail = mysql_query("SELECT * FROM sp_maildetails WHERE mail_id='" . $districamp_id . "' ");
                    $row_spmail = mysql_fetch_array($rs_spmail);

                    $mailSubject = $row_spmail['subject'];
                    $from_email = $row_spmail['from_mail'];
                    $replyto = $row_spmail['reply_email'];
                    $from_name = $row_spmail['from_name'];
                    $emailCotent = $row_spmail['message'];

                    $emailFooterCotent = $row_spmail['footer_message'];
                    $fullname = $fname . ' ' . $lname;

                    $rscomp = mysql_query("SELECT * FROM sp_company where valid=1 and deleted=0 and client_id='" . $c_lient_Id . "' and comp_id='" . $comp_id . "'");
                    $dataComp = mysql_fetch_array($rscomp);

                    $c_comp_name = $dataComp['company_name'];
                    $c_comp_website = '<a href="' . $dataComp['company_website'] . '" style="text-decorations:none; color:inherit;" target="_blank" >' . $dataComp['company_website'] . '</a>';
                    $c_person_email = '<a href="mailto:' . $email . '" style="text-decorations:none; color:inherit;" target="_blank" >' . $email . '</a>';

                    if ($row_spmail['publish_page_id'] != 0) {
                        $lpage_id = $row_spmail['publish_page_id'];

                        $sqllandingpage = "select publish_page_name from sp_landingpage_publish where publish_page_id='" . $lpage_id . "'";
                        $rslandingpage = mysql_query($sqllandingpage) or die(mysql_error());
                        $rowlandingpage = mysql_fetch_array($rslandingpage);
                        $landingpage_name = $rowlandingpage['publish_page_name'];
                    }


                    $sdqry = "select * from sp_subdomain where client_id='" . $c_lient_Id . "' and valid=1 and deleted=0";
                    $sdres = mysql_query($sdqry) or die(mysql_error());
                    $sdomianData = mysql_fetch_array($sdres);
                    $sdomainUrl = $sdomianData['subdomain_url'];
                    $cmssdomainUrl = $sdomianData['cms_subdomain_url'];
                    $c_logo2 = "http://" . $cmssdomainUrl . "/company_logo/" . $dataComp['header_logo'];
                    $c_logo = '<img src="' . $c_logo2 . '" style="height:80px;width:auto;">';
                    $upload_tinymce_img = 'src="http://' . $sdomainUrl . '/manager/uploads/';
                    $string_to_replace = 'src="/manager/uploads/';


                    // get parent CMS Subdomain url

                    $psdqry = "select cms_subdomain_url from sp_subdomain where client_id='" . $p_client_id . "' and valid=1 and deleted=0";
                    $psdres = mysql_query($psdqry) or die(mysql_error());
                    $psdomianData = mysql_fetch_array($psdres);
                    $p_cmssdomainUrl = $psdomianData['cms_subdomain_url'];

                    $emailCotent = str_replace($p_cmssdomainUrl, $cmssdomainUrl, $emailCotent);

                    if ($pcmember_pc_type == 'C') {
                        $emailCotent = str_replace('-DistriLogo-', $c_logo, $emailCotent);
                        $emailCotent = str_replace('-DistriName-', $fullname, $emailCotent);
                        $emailCotent = str_replace('-DistriContact-', $c_contact, $emailCotent);
                        $emailCotent = str_replace('-DistriEmail-', $c_person_email, $emailCotent);
                        $emailCotent = str_replace('-DistriCompany-', $c_comp_name, $emailCotent);
                        $emailCotent = str_replace('-DistriWebsite-', $c_comp_website, $emailCotent);
                    }

                    $emailCotent = str_replace($string_to_replace, $upload_tinymce_img, $emailCotent);

                    if ($landingpage_name != '') {
                        $landingpage_name_replace = $landingpage_name . "?channel_type=Email&camp_id=" . $row_spmail['mail_id'] . "&semail=-unsubscribeEmail-";
                        $emailCotent = str_replace($landingpage_name, $landingpage_name_replace, $emailCotent);
                    }

                    if ($row_spmail['mail_id'] != '') {
                        $dcamp_id_replace = "&camp_id=" . $row_spmail['mail_id'];
                        $emailCotent = str_replace('&camp_id=-campId-', $dcamp_id_replace, $emailCotent);
                    }

                    $toMailStr = implode("','", $contactExp);
                    $rscontmail = mysql_query("SELECT * FROM sp_contact where email_id IN('" . $toMailStr . "') and client_id='" . $c_lient_Id . "' and valid=1 and deleted=0 group by email_id");

                    $i = 0;
                    while ($mailData = mysql_fetch_array($rscontmail)) {
                        $rscomp2 = mysql_query("SELECT * FROM sp_company where valid=1 and deleted=0 and client_id='" . $c_lient_Id . "' and comp_id='" . $mailData['comp_id'] . "'");
                        $dataComp2 = mysql_fetch_array($rscomp2);

                        $arrfirstname[$i] = ucfirst(getValidString($mailData['first_name']));
                        $arrlastName[$i] = ucfirst(getValidString($mailData['last_name']));
                        $arrCompany[$i] = getValidString($dataComp2['company_name']);
                        $arrWebsite[$i] = getValidString($dataComp2['company_website']);
                        $arrEmails[$i] = $mailData['email_id'];

                        $arrCampId[$i] = $row_spmail['mail_id'];

                        $i++;
                    }

                    $Message = $emailCotent . $emailFooterCotent ."-track-";
                    
                    $arrEmails = array_unique($arrEmails);
                    if($row_spmail['mail_server'] !== 'sendgrid'){
                        $json_string = array(
                            'to' => $arrEmails,
                            'sub' => array(
                                '-firstName-' => $arrfirstname,
                                '-unsubscribeEmail-' => $arrEmails,
                                '-lastName-' => $arrlastName,
                                '-campId-' => $arrCampId,
                                '-CompanyWebsite-' => $arrWebsite,
                                '-companyName-' => $arrCompany
                            ),
                            'unique_args' => array('camp_id' => $row_spmail['mail_id'], 'environment' => $environment,'server_name' => $username . "_{$mailServer['mail_server']}"),
                            'filters' => array('clicktrack' => array('settings' => array('enable' => 1)))
                        );
                            
                        $params = array(
                            'api_user' => $api_user,
                            'api_key' => $api_pass,
                            'x-smtpapi' => json_encode($json_string),
                            'to' => $contactExp,
                            'subject' => $mailSubject,
                            'html' => $Message,
                            'text' => $mailSubject,
                            'from' => $from_email,
                            'replyto' => $replyto,
                            'fromname' => $from_name
                        );
                    }
                    
                    if ($row_spmail['attachment_option'] == 'uplod') {
                        $attachment_arr = explode(",", $row_spmail['attachment']);
                        
                        $attachmentArr = [];
                        for ($z = 0; $z < sizeof($attachment_arr); $z++) {
                            if ($pcmember_pc_type == 'C') {
                                $temp_set = mysql_query("select cobrand from user_templates where content_file='" . mysql_real_escape_string($attachment_arr[$z]) . "' and client_id='" . $p_client_id . "' and valid=1 and deleted=0");
                                $temp_get = mysql_fetch_array($temp_set);
                                $cobrand = $temp_get['cobrand'];
                                if ($cobrand == 1) {
                                    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/upload/casestudy/' . $c_lient_Id . '/';
                                } else {
                                    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/upload/casestudy/' . $p_client_id . '/';
                                }
                            } else {
                                $filePath = $_SERVER['DOCUMENT_ROOT'] . '/upload/casestudy/' . $c_lient_Id . '/';
                            }
                            
                            $fullFilePath = $filePath . $attachment_arr[$z];
                            
                            if ($row_spmail['mail_server'] == 'sendgrid') {
                                $attachmentArr[] = [
                                    "filename" => basename($fullFilePath),
                                    "type" => mime_content_type($fullFilePath),
                                    "base64" => base64_encode(file_get_contents($fullFilePath))
                                ];
                            }else{
                                $params['files[' . $attachment_arr[$z] . ']'] = '@' . $fullFilePath;
                            }
                        }
                    }
                    
                    if ($row_spmail['mail_server'] == 'sendgrid') {
                        $personalized_var = [];
                        foreach ($arrEmails as $key => $value) {
                            $personalized_var[$key] = [
                                '-firstName-' => $arrfirstname[$key] ?? '',
                                '-unsubscribeEmail-' => $arrEmails[$key] ?? '',
                                '-lastName-' => $arrlastName[$key] ?? '',
                                '-campId-' => $arrCampId[$key] ?? '',
                                '-CompanyWebsite-' => $arrWebsite[$key] ?? '',
                                '-companyName-' => $arrCompany[$key] ?? ''
                            ];
                        }

                        $params_promotional = [
                            "to" => $contactExp,
                            "from" => [$from_email => $from_name],
                            "reply" => [$replyto => " "],
                            "subject" => stripslashes($mailSubject),
                            "text" => stripslashes($mailSubject),
                            "html" => $Message,
                            "personalized_tags" => $personalized_var,
                            "categories" => ['run_campaign']
                        ];
                        
                        if(isset($attachmentArr) && count($attachmentArr) > 0){
                            $params_promotional['attachment'] = $attachmentArr;
                        }

                        if (!isset($requestParams['sendTestMail'])) {
                            $params_promotional['custom_args'] = ['camp_id' => $row_spmail['mail_id'], 'environment' => $environment, 'server_name' =>  "{$username}_{$mailServer['mail_server']}"];
                            $params_promotional['tracking'] = ["click" => true, "open" => true];
                        }

                        try {
                            $sendgrid = new SPMailer\SendGridMailer($snGdPassword);
                            $status = $sendgrid->send($params_promotional);
                            $response2 = json_decode($status);
                        } catch (Exception $e) {
                            $response2 = (object) ["code" => $e->getCode(), "message" => $e->getMessage(), "file" => $e->getFile(), "line" => $e->getLine()];
                        }
                        
                        if ($response2->{message} == 'success') {
                            mysql_query("UPDATE sp_maildetails SET status='1' WHERE mail_id='" . $row_spmail['mail_id'] . "'");
                            $camp_url = "http://" . $sdData['subdomain_url'] . "/campaign-report-api.php?stoken=" . $token . "&camp_id=" . $row_spmail['mail_id'] . "";
                            $get_array = array("status" => "200", "message" => "Campaign run successfully", "campUrl" => $camp_url);
                        } else {
                            $get_array = array("status" => "501", "message" => "Campaign failed");
                        }
                    }
                }
            } elseif ($companyId != $sd_compId or $sd_clientID != $c_l_ient_Id) {
                //header("Location:login.php?errmsg=Invalid_Domain_Manager");
                $get_array = array("status" => "504", "message" => "Invalid Domain Manager");
            }
        } else {
            $get_array = array("status" => "505", "message" => "Invalid User");
        }
    }
} else {
    $get_array = array("status" => "506", "message" => "Something Getting Wrong!");
}

header('Content-type: application/json');
echo json_encode($get_array, JSON_UNESCAPED_SLASHES);
die;
