<?php
include("../includes/global.php");
//include("includes/check_login.php");
include("../includes/connect.php");
include("../includes/function.php");

if(isset($_POST["submitted"]) && $_POST["submitted"] == 1 && trim($_POST['username'])!='' && trim($_POST['password'])!='')
{
    die('sdsdsd');
	session_start();
	$refer="";
	$refer=$_SESSION["refer"];
	
	if(isset($_POST['username']))
	{
		$username=trim($_POST['username']);
		$email=mysql_real_escape_string($username);
	}
	if(isset($_POST['password']))
	{
		$password=trim($_POST['password']);
		$userPassword=mysql_real_escape_string($password);
	}	
	
	'<br>URL= '.$requri=$_SERVER['REQUEST_URI'];
 	$urlpath=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
 	$domianUrl=explode('/', $urlpath);
 	'<br>URL1= '.$firstd=$domianUrl[0];
   
   if($firstd!=''){
   		'<br>Uqry= '.$chkdm="select * from sp_subdomain where cms_subdomain_url='".$firstd."' and valid=1 and deleted=0";
   		$cmpres=mysql_query($chkdm) or die(mysql_error());
		$domaindata=mysql_fetch_array($cmpres);
		$comp_id=$domaindata['comp_id'];
		$clientId=$domaindata['client_id'];
		$useradmin=$domaindata['userid'];
   }
 

/*	if($url!='www.salespanda.com/')
	{
 	    'URL NAME= '.$urlpath;
   		$webf="www";
  		$aaa=explode('.', $urlpath);
  		$first=$aaa[1];
   		$second=$aaa[2];
   		$agSecond=explode('/', $second);
   		$fseconf=$agSecond[0];
   '<br>Frst WEBSITE= '.$first;
  	'<br>FINAL WEBSITE= '.$finalWeb=$webf.".".$first.".".$fseconf;
	
	$qry="select * from sp_company where company_website='".$finalWeb."'"; 
	$res=mysql_query($qry) or die(mysql_error());  
	$lData=mysql_fetch_array($res);
	$headerLogo=$lData['header_logo'];
	$clientId=$lData['client_id'];
	$comp_id=$lData['comp_id'];
	
}	*/
	
	
  $query="SELECT * FROM `sp_members` WHERE `person_email`='".$email."' and `password`='".$userPassword."' and comp_id='".$comp_id."' and client_id='".$clientId."'";

	$rslogin=mysql_query($query) or die(mysql_error());
	$nummember=mysql_num_rows($rslogin);
	$memberdata=mysql_fetch_array($rslogin);
	$email=$memberdata['person_email'];
	$userid=$memberdata['pid'];
	$c_lient_Id=$memberdata['client_id'];
	$valid=$memberdata['valid'];
	$deleted=$memberdata['deleted'];
	$approveMember=$memberdata['approve'];
	$fname=$memberdata['first_name'];
	$lname=$memberdata['last_name'];
	$companyId=$memberdata['comp_id'];
	
	$twitter_id=$memberdata['twitter_id'];
	$TwitterUsername=$memberdata['twitter_username'];
	$twitter_name=$memberdata['twitter_name'];
	$twitter_aouth_key=$memberdata['twitter_aouth_key'];
	$twitter_aouth_secret=$memberdata['twitter_aouth_secret'];
	
	if ($nummember>0)
	{
		if($valid==1 and $deleted==0 and $approveMember==1 and $comp_id==$companyId and $clientId==$c_lient_Id)
		{
			$_SESSION['email'] = $email;
			$_SESSION['userid'] = $userid;
			$_SESSION['c_lient_Id'] = $c_lient_Id;
			$_SESSION['comp_id'] = $comp_id;
			
			$_SESSION['twitter_id'] = $twitter_id;
			$_SESSION['TwitterUsername'] = $TwitterUsername;
			$_SESSION['twitter_name'] = $twitter_name;
			$_SESSION['twitter_aouth_key'] = $twitter_aouth_key;
			$_SESSION['twitter_aouth_secret'] = $twitter_aouth_secret;
			
			
			
			//update table for last login information 
			$qry_last_lgn = "update sp_login_record set login_date='".$doe."' where member_id='".$userid."' and login_email='".$email."'";
			$res_last_login = mysql_query($qry_last_lgn) or die(mysql_error());
			

                        '<br>Validity Qry= '.$qry="select * from sp_order where client_id='".$c_lient_Id."' and order_type='pkg' and valid=1 and deleted=0 and payment_status='SUCCESS' and DATE(purchase_date) <= '".$doe."' and DATE(expiry_date) >= '".$doe."'";
	                  $result1=mysql_query($qry)or die(mysql_error());
	                 '<br>AC= '.$activeCount=mysql_num_rows($result1);
	                 $rowdata=mysql_fetch_array($result1);
	                   '<br>PKID= '.$packnewId=$rowdata['package_id'];

                       '<br>PCKQRY= '.$conQryd1="select * from sp_package where id='".$packnewId."' and valid=1 and deleted=0";
		        $conresd1=mysql_query($conQryd1) or die(mysql_error());
		        $conData1=mysql_fetch_array($conresd1);
		       '<br>ENGACT= '.$engmntWinActive=$conData1['engement_on'];
		       '<br>CMSACT= '.$cmsActive=$conData1['cms_on'];


                      
			//$page="<meta http-equiv='refresh' content='0;URL=manager/dashboard.php'>";
			$page="<meta http-equiv='refresh' content='0;URL=manager/dashboard-engagement-windows.php'>";

                         /*	if($cmsActive==1 && $engmntWinActive==1)
                         {
                             $page="<meta http-equiv='refresh' content='0;URL=manager/dashboard.php'>";
                         } 
                         if($cmsActive==0 && $engmntWinActive==1)
                         {
                             $page="<meta http-equiv='refresh' content='0;URL=manager/dashboard-engagement-windows.php'>";
                         }    
                         if($cmsActive==1 && $engmntWinActive==0)
                         {
                             $page="<meta http-equiv='refresh' content='0;URL=manager/dashboard.php'>";
                         } */
			
			if($refer!='')
			{
				$refer="http://".$refer;
				echo "<meta http-equiv='refresh' content='0;URL=$refer'>";
			}
			else
			{
				echo $page;
			}	
		
		}
		
		else if($valid==0 and $deleted==0 and $approveMember==0)
		{
				//Deactivated  members
				$_SESSION['errmsg']='Account is Disabled.Please contact Support Team';
			header("Location:login.php");
		}
		elseif($valid==1 and $deleted==1)
		{
				//Deleted   members
				$_SESSION['errmsg']='Your Account Deleted Please contact Support Team.';
			header("Location:login.php");
		}
		elseif($valid==0 and $deleted==1 and $approveMember==1)
		{
			$_SESSION['errmsg']='You Registration has been Blocked.';
			header("Location:login.php");
		}
		elseif($valid==1 and $deleted==0 and $approveMember==0)
		{
			$_SESSION['errmsg']='You account not approved, please check your email and approve.';
			header("Location:login.php");
		}
		elseif($comp_id!=$companyId or $clientId!=$c_lient_Id)
		{
			$_SESSION['errmsg']='Not a valid Domain Manager';
			header("Location:login.php");
		}
	}
	else
	{
	    $_SESSION['errmsg']='Not a valid Domain Manager.';
		header("Location:login.php");		
	}

}else{
    $_SESSION['errmsg']="Email or password can't be blank" ;
    header("Location:login.php");
}


?>
