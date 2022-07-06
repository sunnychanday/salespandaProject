<?php
	session_destroy(); 

	include("includes/global.php");
	include("includes/function.php");

	$token 	= $_GET['stoken'];

	$gatePass 	= array('token' => $token);

	$ch = curl_init();
	//curl_setopt($ch, CURLOPT_URL, trim('http://hdfc-dev-kong.tothenew.com/integration/v1/verifyGatePass')); 
	//curl_setopt($ch, CURLOPT_URL, trim('http://hdfc-uat-kong.tothenew.net/integration/v1/verifyGatePass')); 
	//curl_setopt($ch, CURLOPT_URL, trim('https://api.hdfcfund.com/integration/v1/verifyGatePass'));  
	curl_setopt($ch, CURLOPT_URL, trim('https://api.hdfcfund.com/distributor/v2/dashboard/campaign/validate/stats'));
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','TP-Secret-Key:HDFC_TP_KEY','TP-Name:SALES_PANDA'));
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($gatePass));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	curl_close($ch);

	//$response = '{"isVerifiedSource": true, "arnCode": "ARN-34976" , "email": "basheer3@gmail.com", "phone": "8549648540"}';
	//$response = '{"statusCode":"OK","message":"SUCCESS","data":{"arnCode":"ARN-0411","distributorName":"Richa Tripathi","email":"richa.apurva@gmail.com","isVerifiedSource":true},"status":true}';
	//$response = '{"statusCode":"OK","message":"SUCCESS","data":{"arnCode":"ARN-34976","distributorName":"Basheer3","email":"basheer3@gmail.com","isVerifiedSource":true},"status":true}';

	//echo $response; exit;
    
	$source_ip = get_remote_user_ip();
	$ref_url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	//$responseArray->data->isVerifiedSource == true;
	$responseArray = json_decode($response);

	if($responseArray->data->isVerifiedSource == true){
    	$appQuery 	= "select * from sp_admin where valid=1 and deleted=0 and password='apptech'";
		$appRes		= mysqli_query($conn, $appQuery);
		$appExt		= mysqli_fetch_array($appRes);
		$appname	= trim($appExt['username']);
		$userEmail 	= trim($responseArray->data->email);
		$mobileNo   = trim($responseArray->data->phone);
		$arnCode 	= trim($responseArray->data->arnCode);
		
		if($_GET['camp_id']!=''){
		    $camp_id = $_GET['camp_id'];
		}
		
		//$camp_id    = trim($responseArray->camp_id); 
		
		if($userEmail=='' || $arnCode=='' || $camp_id==''){
		   $get_array = array("status"=>"502", "message"=>"Please check parameters, it's value may be empty.");
		}
		else
		{ 
		    $sub_mem_sql = "SELECT c_client_id FROM `sp_sub_members` WHERE urn_no = '".$arnCode."' and valid = 1 and deleted = 0 "; 
         	$sub_mem_rs	= mysqli_query($conn, $sub_mem_sql);
         	$sub_mem_num = mysqli_num_rows($sub_mem_rs);
         	
         	if($sub_mem_num > 0){
         	    $sub_mem_row = mysqli_fetch_array($sub_mem_rs);
         	    
         	    $mem_sql = "SELECT pid,client_id,person_email,person_contact1 FROM sp_members WHERE client_id='".$sub_mem_row['c_client_id']."' AND company_member_type = 1 AND approve = 1 AND valid = 1 AND deleted = 0"; 
         	    $mem_rs = mysqli_query($conn, $mem_sql);
         	    $mem_num = mysqli_num_rows($mem_rs);
         	    $mem_row = mysqli_fetch_array($mem_rs);
         	   
         	    if($mem_row['person_email']!=$userEmail || ($mobileNo!='' && $mem_row['person_contact1']!=$mobileNo)){
     	           //UPDATE SP MEMBERS INFO IF email and contact changes
        			$updmem="update sp_members set person_email='".$userEmail."', person_contact1='".$mobileNo."' where pid='".$mem_row['pid']."' AND client_id='".$mem_row['client_id']."' AND valid = 1 AND deleted = 0 "; 
        			mysqli_query($conn, $updmem);
    			
     	        }
         	}
		
    		$query = "SELECT * FROM `sp_members` as m LEFT JOIN sp_sub_members as sm ON m.client_id = sm.c_client_id WHERE m.person_email ='".$userEmail."' and sm.urn_no = '".$arnCode."' and sm.valid = 1 and sm.deleted = 0 and m.valid = 1 and m.deleted = 0 and m.approve = 1";
    		$rslogin	= mysqli_query($conn, $query);
    		$nummember	= mysqli_num_rows($rslogin);
    		
    		if ($nummember > 0){
    			session_start();
    			$refer 	= "";
    			$refer	= $_SESSION["refer"];
    			$memberdata 	= mysqli_fetch_array($rslogin);
    			
    			$email			= $memberdata['person_email'];
    			$userid         = $memberdata['pid'];
    			$valid			= $memberdata['valid'];
    			$deleted		= $memberdata['deleted'];
    			$approveMember	= $memberdata['approve'];
    			$fname			= $memberdata['first_name'];
    			$lname			= $memberdata['last_name'];
    			$comp_ids		= $memberdata['comp_id'];
    			
    			$twitter_id		= $memberdata['twitter_id'];
    			$TwitterUsername= $memberdata['twitter_username'];
    			$twitter_name	= $memberdata['twitter_name'];
    			$twitter_aouth_key		= $memberdata['twitter_aouth_key'];
    			$twitter_aouth_secret	= $memberdata['twitter_aouth_secret'];
    		
    			$aboutme		= $memberdata['about_me'];
    			$userType 		= $memberdata['member_pc_type'];
    			//End
    		    
    
    			$chksdq		= "select * from sp_subdomain where subdomain_url='".$appname."' and comp_id='".$comp_ids."' and valid=1 and deleted=0 and status=1";
    			$ressd		= mysqli_query($conn, $chksdq);
    			$sdData		= mysqli_fetch_array($ressd);
    		
    			$c_lient_Id 	= $sdData['client_id']; 
    			$comp_id			= $sdData['comp_id'];
    		
    			if($c_lient_Id == '' && $comp_id == ''){
    				header("location:login.php?errmsg=Invalid_User");
    				exit;		
    			}
    		
    			if($valid==1 and $deleted==0 and $approveMember==1 and $comp_id!='' and $c_lient_Id!=''){  
    				session_start();
    				$_SESSION['email'] 			= $email;
    				$_SESSION['userid'] 		= $userid;
    				$_SESSION['c_lient_Id'] 	= $c_lient_Id;
    				$_SESSION['comp_id'] 		= $comp_id;
    				$_SESSION['u_name'] 		= $fname;
    				$_SESSION['twitter_id'] 	= $twitter_id;
    				$_SESSION['TwitterUsername']= $TwitterUsername;
    				$_SESSION['twitter_name'] 	= $twitter_name;
    				$_SESSION['twitter_aouth_key'] 		= $twitter_aouth_key;
    				$_SESSION['twitter_aouth_secret'] 	= $twitter_aouth_secret;
    				$_SESSION['user_type'] 		= $userType;
    				$_SESSION['about_me'] 		= $aboutme;
    				
    				$pc_member_info = getPCMemberInfo($c_lient_Id);
                    $pcmember_pc_type = $pc_member_info['member_pc_type'];
                    $p_client_id = $pc_member_info['p_client_id'];
    				
    				//insert table for last login information 
    			    $qry_last_lgn = "insert into sp_login_record set client_id='".$c_lient_Id."', member_id='".$userid."', login_email='".$email."', login_date='".date('Y-m-d h:i:s')."', from_status='api', ip_address='".$source_ip."'";
    		        $res_last_login = mysqli_query($conn, $qry_last_lgn);
    		        
    		        // api logs insert entries
		            $loginlogsql = "insert into sp_login_api_logs set p_client_id='".$p_client_id."', soruce_ip='".$source_ip."', status_type='Existing User', stoken='".$token."', response1='".$response."', ref_url='".$ref_url."'";
            
                    $loginlog_rs = mysqli_query($conn, $loginlogsql);
    		        
    		        // check campaign exists or not
        		    $chkCamp = "select mail_id from sp_maildetails where mail_id='".$camp_id."' and allocated_to ='".$c_lient_Id."' AND main_campaign = 2 AND status=1 AND deleted=0 ";
        		    $resCamp = mysqli_query($conn, $chkCamp);
        		    $campNum = mysqli_num_rows($resCamp);
    		        
    		        //Added on Feb 05, 2018
    		        $chkCampsend = "select * from sendgrid_events where msg_id='".$camp_id."'";
        		    $resCampsend = mysqli_query($conn, $chkCampsend);
        		    $campNumEvent = mysqli_num_rows($resCampsend);
    		        //End
    		    	if($camp_id!='' && $campNum > 0){ 
    				    if($campNumEvent>0){
							//$CampPage = "http://".$sdData['subdomain_url']."/manager/campaign-state-waitpage.php?camp_id=".encode($camp_id)."";
							//$CampPage = "http://".$sdData['subdomain_url']."/manager/campaign-individual-dashboard.php?camp_id=".encode($camp_id);
							$get_array = array("status"=>"303", "message"=>"Success");
							
    				    //echo "<meta http-equiv='refresh' content='0;URL=$CampPage'>";
							//header("Location:".$CampPage.""); exit;
						}else{
							$get_array = array("status"=>"506", "message"=>"Data is Currently not Available to View");
						}
    				}
    				else{
    				    $get_array = array("status"=>"501", "message"=>"Invalid Campaign");
    				}
    					
    			}
    			
    		}
    		else{
    			//header("Location:login.php?errmsg=Invalid_User");
    			$get_array = array("status"=>"503", "message"=>"Invalid Domain Manager");
    		}
    	
        }
}
else{
	//echo 'Something Getting Wrong!';
	$get_array = array("status"=>"504", "message"=>"Something Getting Wrong!");
}

header('Content-type: application/json');
echo json_encode($get_array, JSON_UNESCAPED_SLASHES);
die;
