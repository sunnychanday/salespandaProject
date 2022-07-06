<?php

/* Author name: <Bizight Solutions Pvt Ltd> 
 * Purpose of document/ page : This is usesd to validate user for login in web
 * Date:  16-02-2021 
 * Copyright info : <Copyright @2021, Bizight Solutions Pvt Ltd>
 */

include("includes/global.php");
include("includes/function.php");
include("manager/common_functions.php");
include("includes/connect-new.php");
include("csrf/csrf-magic.php");
include_once(MANAGER_PATH.'classes/microsite/prospectengagement.class.php');
include_once "Class/custom-session-manager.php";
//setcookie('PHPSESSID', base64_encode($_COOKIE['PHPSESSID'].'_'.(time() * 30)), time() + (86400 * 30), "/");

$ipaddress = CommonStaticFunctions::get_remote_user_ip();
if (isset($_POST["submitted"]) && $_POST["submitted"] == 1 && trim($_POST['username']) != '' && trim($_POST['password']) != '') {
    $diffmin = 0;
    if (isset($_POST['username'])) {
        $username = testInput($_POST['username']);
        $email = $username;
    }

    if (isset($_POST['password'])) {
        $password = explode('//', testInput($_POST['password']));
        $diff = time() - ($password[0] / 1000);
        $diffmin = round($diff / 60);
        $userPassword = $password[1];
    }

    if ($diffmin < 1) {
        $requri = $_SERVER['REQUEST_URI'];
        $urlpath = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $domianUrl = explode('/', $urlpath);
        $firstd = $domianUrl[0];

        $pwdadmin = 'V1ZoQ2QyUkhWbXBoUVQwOQ';
        $sql_appQuery = "select * from sp_admin where valid=1 and deleted=0 and password=:password";
        $appQuery = $connPDO->prepare($sql_appQuery);
        $appQuery->bindParam(':password', $pwdadmin);
        $appQuery->execute();
        $appExt = $appQuery->fetch();

        $appname = trim($appExt['username']);
        if ($userPassword != '' && $email != '') {
            $getAttempts = getAttempts('LoginPage');

            if ($getAttempts['attempts'] == '' || $getAttempts['attempts'] <= 3) {
                
                $sql_query = "SELECT * FROM sp_members WHERE person_email=:person_email";
                $loginqry = $connPDO->prepare($sql_query);

                $loginqry->bindParam(':person_email', $email);
                $loginqry->execute();
                //session_start();

                if ($loginqry->fetchColumn() > 0) {
                    $loginqry->execute();
                    $memberdata = $loginqry->fetch();
                    $pwdcompare = $memberdata['password'];
                    if($memberdata['member_pc_type'] == 'B'){
                        $_SESSION['errmsg'] = 'Sorry, You are not authorized to access this website. For assistance, please contact your system administrator.';
                        header("location: login.php");
                        exit;
                    }
                    if (strstr($pwdcompare, '//')) {
                        $userPassword = base64_encode(base64_encode(md5(md5($memberdata['client_id'])))) . '//' . $userPassword;
                    }

                    if ($userPassword == $pwdcompare) {
                        enableAttempts('LoginPage');
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

                        $aboutme = $memberdata['about_me'];
                        $userType = $memberdata['member_pc_type'];

                        $sql_chksdq = "select * from sp_subdomain where subdomain_url=:subdomain_url and comp_id=:comp_id and valid=1 and deleted=0 and status=1";
                        $chksdq = $connPDO->prepare($sql_chksdq);
                        $chksdq->bindParam(':subdomain_url', $appname);
                        $chksdq->bindParam(':comp_id', $comp_ids);
                        $chksdq->execute();
                        $sdData = $chksdq->fetch();

                        $c_lient_Id = $sdData['client_id'];
                        $comp_id = $sdData['comp_id'];

                        if ($c_lient_Id == '' || $comp_id == '') {
                            $_SESSION['errmsg'] = 'Invalid User!';
                            header("location: login.php");
                            exit;
                        }

                        if ($valid == 1 and $deleted == 0 and $approveMember == 1 and $comp_id != '' and $c_lient_Id != '') {
                            if ($userType == "C") {
                                //check login and update for login track
                                $login_qry = "SELECT *, (SELECT COUNT(`client_id`) FROM `sp_login_record` WHERE `client_id` = '{$c_lient_Id}' GROUP BY `client_id`) AS `total_login_record` FROM `sp_login_record` WHERE `client_id` = '{$c_lient_Id}' ORDER BY `login_date` DESC LIMIT 0, 1";
                                $login_handler = mysqli_query($conn, $login_qry);
                                if ($login_handler) {
                                    $fetch_login = mysqli_fetch_assoc($login_handler);

                                    $email = $memberdata['person_email'];
                                    $ipaddress = CommonStaticFunctions::get_remote_user_ip();
                                    $app_version = (isset($requestParams['app_version']) && $requestParams['app_version'] != '') ? testInput($requestParams['app_version']) : (isset($fetch_login['app_version']) ? $fetch_login['app_version'] : '');
                                    $from_status = (isset($fetch_login['from_status']) ? $fetch_login['from_status'] : 'login');
                                    $application_status = (isset($fetch_login['application_status']) ? $fetch_login['application_status'] : 1);

                                    if (isset($fetch_login['total_login_record']) && $fetch_login['total_login_record'] >= 2) {
                                        //update here
                                        $login_update_qry = "UPDATE `sp_login_record` SET `login_date` = '{$doe}', `ip_address` = '{$ipaddress}', `app_version` = '{$app_version}', `from_status` = '{$from_status}', `application_status` = '{$application_status}' WHERE id = '{$fetch_login['id']}' AND `client_id` = '{$c_lient_Id}'";
                                        mysqli_query($conn, $login_update_qry);
                                    } else {
                                        //insert here
                                        $login_insert_qry = "INSERT INTO `sp_login_record` SET `client_id` = '{$c_lient_Id}', `member_id` = '{$userid}', `login_email` = '{$email}', `login_date` = '{$doe}', `ip_address` = '{$ipaddress}', `from_status` = '{$from_status}', `application_status` = '{$application_status}', `app_version` = '{$app_version}'";
                                        mysqli_query($conn, $login_insert_qry);
                                    }
                                }
                                // End login Tracking

                                //////// Creating New Contact List //////////
                                $prospect_obj = new ProspectEngagement($connPDO);
                                $prospect_obj->c_client = $c_lient_Id;
                                $prospect_obj->user_id  = $userid;

                                $prospect_obj->create_contact_list('Auto-Email');
                                //////// End of Contact List creating block /////////
                            }

                            if ($userType == "P") {
                                $Catname = "AutoPost";
                                $ptrCatcheckList = "Select * from sp_partner_category where client_id=:client_id and partner_category=:partner_category";
                                $ptrcatcount = $connPDO->prepare($ptrCatcheckList);
                                $ptrcatcount->bindParam(':client_id', $c_lient_Id);
                                $ptrcatcount->bindParam(':partner_category', $Catname);
                                $ptrcatcount->execute();

                                if ($ptrcatcount->fetchColumn() == 0) {
                                    $sqlautopost = "insert into sp_partner_category set client_id=:client_id,partner_category=:partner_category,doe=:doe,modify_date=:modify_date";
                                    $setautopost = $connPDO->prepare($sqlautopost);
                                    $setautopost->bindParam(':client_id', $c_lient_Id);
                                    $setautopost->bindParam(':partner_category', $Catname);
                                    $setautopost->bindParam(':doe', $doe);
                                    $setautopost->bindParam(':modify_date', $doe);
                                    $setautopost->execute();
                                }
                            }

                            $allowedEmail = getallowedEmailList($connPDO, $c_lient_Id, $email, $userType);
                            if ($allowedEmail != '') {
                                header("Location: maintenance.php");
                                exit;
                            }

                            /*Object initializer to store session params withs its token to use for load balancer*/

                            session_destroy();
                            $session_handler = new DbSessionHandler($connPDO);
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
                            $_SESSION['timestamp'] = time();

                            $cookieParams = @session_get_cookie_params();

                            // Set the parameters
                            @session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], true, true);
                            $Sqltoken = "select access_token from auth_user where client_id=:client_id and uid=:uid";
                            $Sqltokenset = $connPDO->prepare($Sqltoken);
                            $Sqltokenset->bindParam(':client_id', $c_lient_Id);
                            $Sqltokenset->bindParam(':uid', $userid);
                            $Sqltokenset->execute();
                            $rowtoken = $Sqltokenset->fetch();
                            enableAttempts("'LoginPage','emailphoneInputPage'");
                            if (!empty($rowtoken['access_token'])) {
                                $token = $token = $userid . "" . time() . "" . $c_lient_Id;
                                $_SESSION['Contoken'] = $token;
                                $sqlinsertlgnToken = "update auth_user set access_token=:access_token, access_token_date=:access_token_date, user_agent=:user_agent, status=1 where client_id=:client_id and uid=:uid";
                                $insertlgnToken = $connPDO->prepare($sqlinsertlgnToken);
                                $insertlgnToken->bindParam(':access_token', $token);
                                $insertlgnToken->bindParam(':access_token_date', $doe);
                                $insertlgnToken->bindParam(':user_agent', $_SERVER ['HTTP_USER_AGENT']);
                                $insertlgnToken->bindParam(':client_id', $c_lient_Id);
                                $insertlgnToken->bindParam(':uid', $userid);
                                $insertlgnToken->execute();
                            } else {
                                $token = $userid . "" . time() . "" . $c_lient_Id;
                                $_SESSION['Contoken'] = $token;
                                $sqlinsertlgnToken = "insert into auth_user set client_id=:client_id, uid=:uid, access_token=:access_token, access_token_date=:access_token_date, user_agent=:user_agent, status=1";
                                $insertlgnToken = $connPDO->prepare($sqlinsertlgnToken);

                                $insertlgnToken->bindParam(':access_token', $token);
                                $insertlgnToken->bindParam(':access_token_date', $doe);
                                $insertlgnToken->bindParam(':user_agent', $_SERVER ['HTTP_USER_AGENT']);
                                $insertlgnToken->bindParam(':client_id', $c_lient_Id);
                                $insertlgnToken->bindParam(':uid', $userid);
                                $insertlgnToken->execute();
                            }

                            //check login and update
                            $login_qry = "SELECT *, (SELECT COUNT(`client_id`) FROM `sp_login_record` WHERE `client_id` = '{$c_lient_Id}' GROUP BY `client_id`) AS `total_login_record` FROM `sp_login_record` WHERE `client_id` = '{$c_lient_Id}' ORDER BY `login_date` DESC LIMIT 0, 1";
                            $login_handler = mysqli_query($conn, $login_qry);
                            if ($login_handler) {
                                $fetch_login = mysqli_fetch_assoc($login_handler);

                                $app_version = (isset($requestParams['app_version']) && $requestParams['app_version'] != '') ? testInput($requestParams['app_version']) : (isset($fetch_login['app_version']) ? $fetch_login['app_version'] : '');
                                $from_status = (isset($fetch_login['from_status']) ? $fetch_login['from_status'] : 'login');
                                $application_status = (isset($fetch_login['application_status']) ? $fetch_login['application_status'] : 1);

                                if (isset($fetch_login['total_login_record']) && $fetch_login['total_login_record'] >= 2) {
                                    //update here
                                    $login_update_qry = "UPDATE `sp_login_record` SET `login_date` = '{$doe}', `ip_address` = '{$ipaddress}', `app_version` = '{$app_version}', `from_status` = '{$from_status}', `application_status` = '{$application_status}' WHERE id = '{$fetch_login['id']}' AND `client_id` = '{$c_lient_Id}'";
                                    mysqli_query($conn, $login_update_qry);
                                } else {
                                    //insert here
                                    $login_insert_qry = "INSERT INTO `sp_login_record` SET `client_id` = '{$c_lient_Id}', `member_id` = '{$userid}', `login_email` = '{$email}', `login_date` = '{$doe}', `ip_address` = '{$ipaddress}', `from_status` = '{$from_status}', `application_status` = '{$application_status}', `app_version` = '{$app_version}'";
                                    mysqli_query($conn, $login_insert_qry);
                                }
                            }
                            if($userType == "C")
                            {
                                header("location: manager/template-dashboard.php");
                            }else {
                                header("location: manager/template-dashboard.php");
                            }

                            exit;
                        } elseif ($valid == 0 and $deleted == 0 and $approveMember == 0) {
                            $_SESSION['errmsg'] = 'Account is Disabled.Please contact Support Team';
                            header("Location:login.php");
                        } elseif ($valid == 1 and $deleted == 1) {
                            $_SESSION['errmsg'] = 'Your Account Deleted Please contact Support Team.';
                            header("Location:login.php");
                        } elseif ($valid == 0 and $deleted == 1 and $approveMember == 1) {
                            $_SESSION['errmsg'] = 'You Registration has been Blocked.';
                            header("Location:login.php");
                        } elseif ($valid == 1 and $deleted == 0 and $approveMember == 0) {
                            $_SESSION['errmsg'] = 'You account not approved, please check your email and approve.';
                            header("Location:login.php");
                        } elseif ($companyId != $sd_compId or $sd_clientID != $c_l_ient_Id) {
                            $_SESSION['errmsg'] = 'Not a valid Domain Manager.';
                            header("Location:login.php");
                        }
                    } else {
                        addAttempts('LoginPage');
                        $_SESSION['errmsg'] = 'Password is not correct';
                        header("Location:login.php");
                    }
                } else {
                    $_SESSION['errmsg'] = 'Not a valid email';
                    header("Location:login.php");
                }
            } else {
                $_SESSION['errmsg'] = 'You have reached maximum login attempt limit. Please try after 30 minutes.';
                header("Location:login.php");
            }
        }
    } else {
        $_SESSION['errmsg'] = 'Not a valid credentials .';
        header("Location:login.php");
    }
} else {
    $_SESSION['errmsg'] = "Email or password can't be blank";
    header("Location:login.php");
}
