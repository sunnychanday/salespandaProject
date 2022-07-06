<?php 
        ini_set('allow_url_fopen',1);	

	$pc_lient_Id = 'SP11374';
	
	$ch = curl_init();
	$distridetails 	= array('arnCode' => $responseArray->arnCode);
	//curl_setopt($ch, CURLOPT_URL, trim('http://hdfc-dev-integrationservice.tothenew.com/integration/v1/getDistributorDetails'));
        curl_setopt($ch, CURLOPT_URL, trim('https://api.hdfcfund.com/integration/v1/getDistributorDetails'));
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','TP-Secret-Key:HDFC_TP_KEY','TP-Name:SALES_PANDA'));
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($distridetails));
	//$response = curl_exec($ch);
	
	if(curl_exec($ch) === false){
		header("Location:login.php?errmsg=".curl_error($ch));
		exit;
	}
	else{
		$response = curl_exec($ch);
	}
	
	curl_close($ch);
	/*----End CURL-----*/
        
	
	//Pass Static Data in reponse
	//$response = '{"distributorName": "Basheer Ahmad", "arnCode": "ARN-0436" , "distributorEmailId": "basheergmail16@bizight.com", "phone1": "8549648540", "phone2": "", "phone3": "", "company": "", "website": "", "LogoUrl":"http://res.cloudinary.com/hdfcmf/image/upload/v1481527867/dev/ARN-0411-Logo.png"}';
         
        // remove replace line when upload production server 
        //$response = str_replace('richa.apurva@gmail.com','basheergmail9@bizight.com',$response);
        //$response = str_replace('ARN-0411','ARN-0429', $response);
         
	
          // api logs update entries
          $updloglogin="update sp_login_api_logs set p_client_id='".$pc_lient_Id."', status_type='New User' , response2='".$response."'  where id='".$last_loginlog_id."'";
	  $uplogloginres=mysql_query($updloglogin) or die(mysql_error());

	$responseArray = array();
	$responseArray = json_decode($response);
  
	//VALIDATED RESPONSE
	if(isset($responseArray->status) && $responseArray->status == false){ 
		header("Location:login.php?errmsg=".$responseArray->message);
		exit;
	}
	else{
		
		//GET PackId
                $packgId=4;   // for temporary use

		/*if($responseArray->packId ==''){	
			$packgId==4;
		}else{	$packgId=$responseArray->packId;
		}
		$packg_Sub_Id=$responseArray->dispackId;*/
	
		//GET Subdomain from database
		$sdmnqry="select * from sp_admin where password='apptech' and valid=1 and deleted=0";
		$sdmres=mysql_query($sdmnqry) or die(mysql_error());
		$sdmData=mysql_fetch_array($sdmres);
		$sdmName=trim($sdmData['username']);
		
		$pkg="select * from sp_package where id='".$packgId."' and package_type='pkg' and valid=1 and deleted=0";
		$respk=mysql_query($pkg) or die(mysql_error());
		$packgData=mysql_fetch_array($respk);
		$pkgCmsStatus=$packgData['cms_on'];
			
		$actualAmount=$packgData['package_cost'];
		$payAmount=$packgData['package_cost'];
		$timeDuration=$packgData['validity'];
		$caldays = $timeDuration * 30-1;
		$variabDay='+'.$caldays;
		$entryDate = date("Y-m-d h:i:s");
		$enddate = date("Y-m-d h:i:s", strtotime($entryDate ." $variabDay day") ); 
		
		/*--------SET VALUES----------*/
		
		//SET User fields
		$arnCode = trim($responseArray->arnCode);
                $mRenPassword = substr(md5(uniqid(mt_rand(), true)), 0, 8);
                $flname = explode(' ', $responseArray->distributorName);
                $l_name = array_pop($flname); 
                $f_name  = implode(' ', $flname);

		$f_name=fun_metatag(str_replace("'","&#8217;",trim(mysql_real_escape_string($f_name))));
                $l_name=fun_metatag(str_replace("'","&#8217;",trim(mysql_real_escape_string($l_name))));
                if($responseArray->distributorEmailId!=""){
		   $emailId=fun_metatag(str_replace("'","&#8217;",trim(mysql_real_escape_string($responseArray->distributorEmailId))));
                }
                else{
                   $emailId=$arnCode."@hdfcmfpartners.com";
                }
//		$emailId=fun_metatag(str_replace("'","&#8217;",trim(mysql_real_escape_string($responseArray->distributorEmailId))));
		$nnewmail=base64_encode($emailId);
		$mobileNo=mysql_real_escape_string($responseArray->phone1);
                $phoneNo2=mysql_real_escape_string($responseArray->phone2);
                $phoneNo3=mysql_real_escape_string($responseArray->phone3);
                $compName=mysql_real_escape_string($responseArray->company);
		$compWeb=fun_metatag(str_replace("'","&#8217;",trim(mysql_real_escape_string($responseArray->website))));
                $compLogoUrl=mysql_real_escape_string($responseArray->LogoUrl); 
                
		
		//SET Company Url
		if($compWeb==''){			
		  $compWeb = $responseArray->arnCode;	
		}
		$ajactweb=explode('/', $compWeb);
		$condWeb=$ajactweb[0];
		if (substr($compWeb, 0, 7) === 'http://'){
			$source=$compWeb;
		}else if (substr($compWeb, 0, 8) === 'https://'){
			$source=$compWeb;
		}else{
			$source=$compWeb;
		}
		$srcUrl = $source;
		$ajactweb=explode('.', $srcUrl);
		$webCompanyName=$source;
		
		//SET Domain and Path
		$newSDomain=$sdmName; 									//for development mode
		$newSDomainpath='http://'."$newSDomain".'/'.'manager';	//for development mode
		
		//SET IP Address
		$ipaddress='';
		$doe = date('Y-m-d h:i:s');

		/*------CREATE SUB DOMAIN---------*/
		$ssssss=explode('.', $source);
		$firstword=$ssssss[0];
		$domain1 = preg_replace("/^".$firstword."\./", "", $source);
		$domain2 = explode('.', $domain1);
		$mysubdomain= $domain2[0];
		
		$mydom = explode('.', $_SERVER['HTTP_HOST']);
		$mydomain = preg_replace("/^".$mydom[0]."\./", "", $_SERVER['HTTP_HOST']);  
								 
		$webCompanyName=$source;
		$newSDomaincms=$mysubdomain.'.'.$mydomain;
		if($pkgCmsStatus==1)
		{
			// Create subdomain dynamically on server  
			//createSubdomain($mysubdomain, 'technochimes', 'panda@tech#4321', $mydomain);   
		}
		/*-----END SUB DOMAIN-----------*/
				
		//CHECK SUBDOMAIN
		$sdQry="select * from sp_subdomain where subdomain_url='".$newSDomain."' and extension='".$srcUrl."' and valid=1 and deleted=0";
		$sdRes=mysql_query($sdQry) or die(mysql_error());
		$sdomaincount=mysql_num_rows($sdRes);

		if($sdomaincount==0)
		{
			//Step1	Check Email
			$chkmem="select * from sp_members where person_email='".$emailId."'";
			$resmem=mysql_query($chkmem) or die(mysql_error());
			$memCount=mysql_num_rows($resmem);
			if($memCount!=0)
			{	header("Location:login.php?errmsg=Member already exists");
				//header("location:register.php?emailid=".$emailId."&n=".$f_name."&cw=".$compWeb."&m=".$mobileNo."&errmsg=Member with this email id already exists");
				exit;
			}
			
			//Step1	Check Website

			/*$chkweb="select * from sp_company where company_website='".$srcUrl."'";
			$rescmp=mysql_query($chkweb) or die(mysql_error());
			$compCount=mysql_num_rows($rescmp);
			if($compCount!=0)
			{	
			    $ermsg="This website is associated with a different UserID. Please drop us an email at info@salespanda.com";
				header("Location:login.php?errmsg=Company already exists");	
				//header("location:register.php?emailid=".$emailId."&n=".$f_name."&,m=".$mobileNo."&cw=".$compWeb."&errmsg=".$ermsg."");
				
				exit;
			}*/
			
			//ADD MEMBER
			$addmem="insert into sp_members set person_email='".trim($emailId)."',
							    first_name='".trim($f_name)."',
                                                            last_name='".trim($l_name)."',
							    person_contact1='".trim($mobileNo)."',
							    company_member_type=1,
                                                            member_pc_type='C', 
							    ip_address='".$ipaddress."',
                                                            distribanner_status=2,                                                            
							    doe='".$doe."'";
			$resmem=mysql_query($addmem) or die(mysql_error());
			$membId=mysql_insert_id();
			$newMM= base64_encode($membId);
		
		
			$rand_string = rand(1000,9999);
			//ADD COMPANY
			$addcmp="insert into sp_company set company_website='".trim($srcUrl)."',
							    added_by='".$membId."',
                                                            company_name='".$compName."',
                                                            company_phone='".$phoneNo2."',
                                                            company_phone2='".$phoneNo3."',
							    ip_address='".$ipaddress."',
							    doe='".$doe."'";
			$resadd=mysql_query($addcmp) or die(mysql_error());
			$compId=mysql_insert_id();
				
			//CLIENT ID
			$clietid='SP'."".$compId."".$rand_string;
			$clid=base64_encode($clietid);


                     if($compLogoUrl!=''){
                        $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 4); 
                        $newWidth = '55';
                        $file = $randomString."".$clietid;      //File Name
                        $newFile = 'temp_logo/'.$file.'.png';   //File Path
                        $resizedFile = 'company_logo/'.$file;   //Resized file path

                        $inslogoFile = $file.'.png';

                       /**new code**/
                       $ch = curl_init();
                       curl_setopt ($ch, CURLOPT_URL, $compLogoUrl);
                       curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
                       curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
                       $contents = curl_exec($ch);
                       if (curl_errno($ch)) {
                           echo curl_error($ch);
                           echo "\n<br />";
                           $contents = '';
                       } else{
                               curl_close($ch);
                       }

                       if(!is_string($contents) || !strlen($contents)){
	                   echo "Failed to get contents.";
	                   $contents = '';
                       }

                       $fp = fopen($newFile,'x');
                       fwrite($fp, $contents);
                       fclose($fp);


                       if (!file_exists($newFile)) {   
	                  echo "File Not Exists";
                       }else{
	                  imgResize($newWidth, $resizedFile, $newFile);
	                  //echo "Done";//Insert Data to database
                       }
  
                    }

			
			//UPDATE SP COMPANY
			$updclt="update sp_company set client_id='".$clietid."', header_logo='".$inslogoFile."'  where comp_id='".$compId."'";
			$updcres=mysql_query($updclt) or die(mysql_error());
			
			//UPDATE SP MEMBERS
			$updmem="update sp_members set client_id='".$clietid."', password='".$mRenPassword."', approve=1, comp_id='".$compId."' where pid='".$membId."'";
			$upmemres=mysql_query($updmem) or die(mysql_error());
			
			//SAVE SUB DOMAIN
			$addsdomain="insert into sp_subdomain set client_id='".$clietid."',
													  comp_id='".$compId."',
													  subdomain_url='".trim($newSDomain)."',
													  cms_status='".$pkgCmsStatus."',
													  cms_subdomain_url='".$newSDomaincms."',
													  extension='".trim($webCompanyName)."',
													  userid='".$membId."',
													  status=1,
													  ip_address='".$ipaddress."',
													  doe='".$doe."'";
		   	$sdres=mysql_query($addsdomain) or die(mysql_error());
		   	
		   		   	
		   	//ADD SUB MEMBER
		   	if(isset($responseArray->arnCode) && $responseArray->arnCode!=''){
				$urn_no=$responseArray->arnCode;
			}else{				
				$urn_no='HDFC'."".$compId."".$rand_string;
			}
			
			$addsubmem="insert into sp_sub_members set p_client_id='".$pc_lient_Id."',
												c_client_id='".$clietid."',
												urn_no='".$urn_no."',
												doe='".$doe."'";
			$resaddsmem=mysql_query($addsubmem) or die(mysql_error()); 
				
			//SAVE ORDER SET
			$addord="insert into sp_order set client_id='".$clietid."',
											  order_type='pkg',
											  member_id='".$membId."',
											  payment_status='SUCCESS',
											  package_id='".$packgId."',
											  pay_amount='".$payAmount."',
											  paid_amount='".$payAmount."',
											  purchase_date='".$entryDate."',
											  expiry_date='".$enddate."',
											  doe='".$doe."'";
			$respk=mysql_query($addord) or die(mysql_error());
			
			
			/////////////////Email for Registered User Start///////////////////////////	
			//~ if(mail("simranjit.softprodigy@gmail.com","My subject",'test content')){
				//~ die('success');
			//~ }else{
				//~ die('error');
			//~ }

			$emailCotent="<table border='0' align='center' cellpadding='0' cellspacing='0' style='max-width:600px;'>
			  <tr>
				<td width='20' bgcolor='#45bcd2'>&nbsp;</td>
				<td bgcolor='#45bcd2'>&nbsp;</td>
				<td width='20' bgcolor='#45bcd2'>&nbsp;</td>
			  </tr>
			  <tr>
				<td height='57' align='center' valign='top' bgcolor='#45bcd2'>&nbsp;</td>
				<td align='center' valign='top' bgcolor='#45bcd2' style='font-family: Arial, Helvetica, sans-serif;color:#FFFFFF;font-size:1.8em;font-weight:bold;'><img src='".$sitepath."img/mailer/1.jpg' alt='Welcome to SalesPanda' hspace='0' vspace='0' border='0' align='top' /></td>
				<td align='center' valign='top' bgcolor='#45bcd2'>&nbsp;</td>
			  </tr>
			  <tr>
				<td bgcolor='#ececec'>&nbsp;</td>
				<td align='left' valign='top' bgcolor='#ececec' style='font-family: Arial, Helvetica, sans-serif;color:#363737;font-size:12px;line-height:20px;'>&nbsp;</td>
				<td bgcolor='#ececec'>&nbsp;</td>
			  </tr>
			  <tr>
				<td bgcolor='#ececec'>&nbsp;</td>
				<td align='left' valign='top' bgcolor='#ececec' style='font-family: Arial, Helvetica, sans-serif;color:#363737;font-size:12px;line-height:20px;'><p>Dear ".ucfirst($f_name).",
				  </p>
				  <p>Thank you for registering on SalesPanda.</p>
				  <p>You are one step away from completing your SalesPanda signup. Please click on the button below to verify your email id:</p>
				  <p style='margin:10px 0 10px 0;'><a href='".$newSDomainpath."/password-generate.php?userid=".$newMM."&email=".$nnewmail."&clid=".$clid."' rel='nofollow'><img style='cursor:pointer;' src='".$sitepath."images/mailer/verify-btn.png' /></a></p>
				  <p>If you are unable to Click on the button above, please cut and paste the following url on your browser: <br />
				  '".$newSDomainpath."/password-generate.php?userid=".$newMM."&email=".$nnewmail."&clid=".$clid."'
				  
				  </p>
				  <p>You can start using SalesPanda after verifying your email ID and unleash the power of Inbound Marketing.</p><br />
				  <p>Best Regards,<br />
					SalesPanda Team </p></td>
				<td bgcolor='#ececec'>&nbsp;</td>
			  </tr>
			  <tr>
				<td bgcolor='#ececec'>&nbsp;</td>
				<td align='center' valign='middle' bgcolor='#ececec' style='font-family: Arial, Helvetica, sans-serif;color:#FFFFFF;font-size:0.8em;line-height:20px;font-weight:bold;'>&nbsp;</td>
				<td bgcolor='#ececec'>&nbsp;</td>
			  </tr>
			  <tr>
				<td height='42' bgcolor='#3e3e3e'>&nbsp;</td>
				<td height='42' align='center' valign='middle' bgcolor='#3e3e3e' style='font-family: Arial, Helvetica, sans-serif;color:#FFFFFF;font-size:0.8em;line-height:20px;font-weight:bold;'><p><img src='".$sitepath."img/mailer/5.jpg' alt='SalesPanda - Inbound Marketing Software' hspace='0' vspace='0' border='0' align='middle' /></p></td>
				<td height='42' bgcolor='#3e3e3e'>&nbsp;</td>
			  </tr>
			</table>";

			$Message= null;
			$Message=$emailCotent;
			$ToSubject = "Please verify email id provided to SalesPanda";
			$from_email  = "info@salespanda.com"; 		

			$json_string = array(
			  'to' => array($emailId)
			);
			
			$url = 'https://api.sendgrid.com/';
			$user = $sndGdUser;
			$pass = $snGdPassword;

			$params = array(
				'api_user'  => $user,
				'api_key'   => $pass,
				'x-smtpapi' => json_encode($json_string),
				'to'        =>  $emailId,
				'subject'   =>  $ToSubject,
				'html'      =>  $Message,
				'text'      => 'demo text 2',
				'from'      => $from_email,
			  );
			  
			$request =  trim($url.'api/mail.send.json');

			$session = curl_init($request);
			curl_setopt ($session, CURLOPT_POST, true);
			curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
			curl_setopt($session, CURLOPT_HEADER, false);
			curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

			// obtain response
			$response = curl_exec($session);
			curl_close($session);
			// print everything out

			////////////////////////////Email for Registered User END//////////////////////////////////
				
			//header("Location:login.php?errmsg=Account added successfully. Please check email for verification link");		
			
			
		//////////////////////////// Member Login //////////////////////////////////	
		$query	= "SELECT * FROM `sp_members` as m LEFT JOIN sp_sub_members as sm ON m.client_id = sm.c_client_id WHERE m.person_email ='".$emailId."' and sm.urn_no = '".$arnCode."' and sm.valid = 1 and sm.deleted = 0 and m.valid = 1 and m.deleted = 0 and m.approve = 1";
		$rslogin	= mysql_query($query) or die(mysql_error());
		$nummember	= mysql_num_rows($rslogin);
		if ($nummember > 0){
			session_start();
			$refer 	= "";
			$refer	= $_SESSION["refer"];
			$memberdata 	= mysql_fetch_array($rslogin);
			
			$email			= $memberdata['person_email'];
			$userid			= $memberdata['pid'];
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
			
			//Added By Softprodigy on Nov 21, 2016
			$aboutme		= $memberdata['about_me'];
			$userType 		= $memberdata['member_pc_type'];
			//End
		
		    // check campaign exists or not
		    $chkCamp	= "select * from sp_maildetails where mail_id='".$camp_id."' and client_id ='".$memberdata['p_client_id'] ."' AND main_campaign = 1 AND syndication_status=1 AND status=1 AND deleted = 0";
		    $resCamp	= mysql_query($chkCamp) or die(mysql_error());
		    $campData	= mysql_fetch_array($resCamp);
		    $campNum    = mysql_num_rows($resCamp);
		
		    $campLpageId =  ($campData['publish_page_id']!=0) ? $campData['publish_page_id'] : '';
		
			$chksdq		= "select * from sp_subdomain where subdomain_url='".$appname."' and comp_id='".$comp_ids."' and valid=1 and deleted=0 and status=1";
			$ressd		= mysql_query($chksdq) or die(mysql_error());
			$sdData		= mysql_fetch_array($ressd);
		
			$c_lient_Id = $sdData['client_id']; 
			$comp_id	= $sdData['comp_id'];
		
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
				
				//Added By Softprodigy for cobranding on Nov 30, 2016
				if($userType=='C'){ 
					include("manager/pdf_cobranding.php");
					eventListener();//Added By Softprodigy on Nov 30, 2016
				}
				//End
				
				//insert table for last login information 
				$qry_last_lgn = "insert into sp_login_record set client_id='".$c_lient_Id."',
									 member_id='".$userid."',
									 login_email='".$email."',
									 login_date='".$doe."',
                                                                         from_status='api', 
									 ip_address='".$source_ip."'";
		        $res_last_login = mysql_query($qry_last_lgn) or die(mysql_error());
		      
		        // check and approve landingpage for campaign          
		        if($campLpageId!=0){
		            $chklpagesql="select lsyndid, approve from sp_landingpage_syndication where landingpage_id='".$campLpageId."' AND p_client_id ='".$memberdata['p_client_id']."' and c_client_id='".$c_lient_Id."' ";
					$reslpage=mysql_query($chklpagesql) or die(mysql_error());
                    $numlpage = mysql_num_rows($reslpage);
                                                                    
                    if($numlpage==0){
                         $addsyndcont="insert into sp_landingpage_syndication set p_client_id='".$memberdata['p_client_id']."', c_client_id='".$c_lient_Id."', landingpage_id='".$campLpageId."', approve=1, doe='".date('Y-m-d h:i:s')."'";
                         $ressyndcont=mysql_query($addsyndcont) or die(mysql_error());

                    }
                    else{
					    $rowlpage=mysql_fetch_array($reslpage);                                                                                                     
                        $clpageApprove = $rowlpage['approve'];
                   }
		        }          
		
				$page	= "<meta http-equiv='refresh' content='0;URL=manager/master-dashboard.php'>";
				/*if($refer!=''){ 
					$refer="http://".$refer;
					echo "<meta http-equiv='refresh' content='0;URL=$refer'>";
				}*/
				if($camp_id!='' && $campNum > 0){ 
				    
				    if($clpageApprove==0){
				        $CampPage = "manager/add_new_email.php?camp_id=".encode($camp_id)."&goto=send&tempAction=temp&lpage_id=".$campLpageId."&lstatus=".$clpageApprove.""; 
				    }
				    else{
				        $CampPage = "manager/add_new_email.php?camp_id=".encode($camp_id)."&goto=send&tempAction=temp&lpage_id=".$campLpageId.""; 
				    }
				    
				    echo "<meta http-equiv='refresh' content='0;URL=$CampPage'>";
				}
				else{ 
					echo $page;
				}
                              	
			
			}
                    }
			
		}
		else{
			
			header("location:login.php?errmsg=Domain Already registered!");
			exit;
		}	
		
    }
?>
