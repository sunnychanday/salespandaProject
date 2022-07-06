<?php
	include("../includes/global.php");
	include("includes/check_login2.php");
	include("../includes/connect.php");
	include("../includes/function.php");
	include("includes/global-url.php");
	include("pagehitcounter.php");
	
	$url = 'https://api.sendgrid.com/';
	$user = $sndGdUser;
	$pass = $snGdPassword;
	 
       $sql_analytic = mysql_query("select * from sp_subdomain where client_id='".$c_lient_Id."'");
       $data_analytic = mysql_fetch_array($sql_analytic);
       $set_analytic = str_replace("<q>","'",$data_analytic['google_analytics']);
       $get_analytic  = htmlspecialchars_decode($set_analytic);
       $set_master = htmlspecialchars_decode($data_analytic['google_webmaster']);

	$page_hitssk ='http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$ip= get_remote_user_ip();
	$agent=$_SERVER['HTTP_USER_AGENT'];
	
	if(isset($_POST["source"]) && $_POST["source"]=='Document-Download')
	{
		/*echo "<pre>";
		print_r($_POST);
		echo "<pre>";
		die();*/
		function test_input($data) 
		{
 			 $data = trim($data);
 			 $data = stripslashes($data);
 			 $data = htmlspecialchars($data);
			 return $data;
		}
		 $caseId=$_REQUEST['caseids'];
		if($caseId!='')
		{
			$chkcs="select * from sp_case_study where id='".$caseId."'";
			$chkres=mysql_query($chkcs) or die(mysql_error());
			$caseData=mysql_fetch_array($chkres);
			$clientId=$caseData['client_id'];
			$company_case=$caseData['comp_id'];
			$categoryCs=$caseData['category'];
			$documentName=$caseData['case_study_title'];
			$weburl=getweburl($clientId);
			$contentType=$caseData['content_type'];
		}

		else
		{
			header("location:index.php");
		}
         
                $pathqry="select * from sp_subdomain where client_id='".$c_lient_Id."'";
	        $pthqry=mysql_query($pathqry) or die(mysql_error());
	        $pathData=mysql_fetch_array($pthqry);
	        $defaultwebPath='http://'.$pathData['subdomain_url'];
	
		$fname = test_input($_POST['fname']);
		$lname = test_input($_POST['lname']);
		$fullName = test_input($fname." ".$lname);
		$email = test_input($_POST['email']);
		$phone = test_input($_POST['phone']);
		$company =test_input($_POST['company']);
		$source = $_POST['source'];
	
		$city = test_input($_POST['city']);
		$city_count=explode('-', $city);
		$cityName=$city_count[0];
		$countryName=$city_count[1];
	
		if($countryName!='')
		{
			$chkcntry="select * from sp_country where client_id='".$clientId."' and country_name='".$countryName."'";
			$rescnt=mysql_query($chkcntry) or die(mysql_error());
			$countryCount=mysql_num_rows($rescnt);
			if($countryCount==0)
			{
				$addcountry="insert into sp_country set country_name='".$countryName."', client_id='".$clientId."', doe='".$doe."'";
				$countRes=mysql_query($addcountry) or die(mysql_error());
				$countryId=mysql_insert_id();
			}
			else
			{
				$countryData=mysql_fetch_array($rescnt);
				$countryId=$countryData['country_id'];
			}
		}
		if($cityName!='')
		{
			if($countryId!='')
			{
				$chkcity="select * from sp_indian_districs where client_id='".$clientId."' and country_id='".$countryId."' and distrcit_name='".$cityName."'"; 
			} 
			else 
			{ 
				$chkcity="select * from sp_indian_districs where client_id='".$clientId."' and distrcit_name='".$cityName."'"; 
			}
			$cityres=mysql_query($chkcity) or die(mysql_error());
			$extCity=mysql_num_rows($cityres);
			if($extCity==0)
			{
				$addcity="insert into sp_indian_districs set client_id='".$clientId."',
															 country_id='".$countryId."',
															 distrcit_name='".$cityName."',
															 doe='".$doe."'";
				$cityres=mysql_query($addcity) or die(mysql_error());
				$cityId=mysql_insert_id();
			}
			else
			{
				$cityData=mysql_fetch_array($cityres);
				$cityId=$cityData['id'];
			}
		}
		$mdesignation = test_input($_POST['designation']);
		$leadRemark = test_input($_POST['remark']);
		$captcha = $_POST['captcha'];
		//$pagesource = $_POST['pagesource'];
		if($captcha=='')
		{
			if($fname!='' && $email!='' && $company!='')
			{
				if($mdesignation!='')
				{
					$chkDesg="select * from sp_designation where client_id='".$clientId."' and designation='".$mdesignation."'";
					$resdeg=mysql_query($chkDesg) or die(mysql_error());
					$chkDegn=mysql_num_rows($resdeg);
					if($chkDegn==0)
					{
						$adddeg="insert into sp_designation set client_id='".$clientId."', 
																designation='".$mdesignation."',
																creation_mode='c',
																doe='".$doe."'";
						$adegres=mysql_query($adddeg) or die(mysql_error());
						$designationId=mysql_insert_id();
						$degflag=1;
					}
					else
					{
						$desgiData=mysql_fetch_array($resdeg);
						$designationId=$desgiData['id'];
					}
				}
				$chkcont="select * from sp_contact where email_id='".$email."' and client_id='".$clientId."'";
				$rescont=mysql_query($chkcont) or die(mysql_error());
				$contactCount=mysql_num_rows($rescont);
				if($contactCount==0)
				{
				 	$addcont="insert into sp_contact set client_id='".$clientId."',
														 source='".$source."',
														 first_name='".$fname."',
														 last_name='".$lname."',
														 email_id='".$email."',
														 designation='".$designationId."',
														 mobile='".$phone."'";
					$addres=mysql_query($addcont) or die(mysql_error());
					$contactId=mysql_insert_id();
				
					if($degflag==1)
					{
						$upddeg="update sp_designation set createdby='".$contactId."' where id='".$designationId."'";
						$resd=mysql_query($upddeg) or doe(mysql_error());
					}
				}
				else
				{
					$contactData=mysql_fetch_array($rescont);
					$contactId=$contactData['id'];
					$companyId=$contactData['comp_id'];
					if($degflag==1)
					{
						$upddeg="update sp_designation set createdby='".$contactId."' where id='".$designationId."'";
						$resd=mysql_query($upddeg) or doe(mysql_error());
					}
				}
			
				$chkcomp="select * from sp_company where company_name='".$company."' and client_id='".$clientId."'";
				$resc=mysql_query($chkcomp) or die(mysql_error());
				$compCount=mysql_num_rows($resc);
				if($compCount==0)
				{
					 //$esource='Document Popup';
					 $addcomp="insert into sp_company set company_name='".$company."',
					 									  client_id='".$clientId."',	
														  company_city='".$cityId."',
														  doe='".$doe."',
														  added_by='".$contactId."',
														  entry_source='".$source."'";
					$rescmp=mysql_query($addcomp) or die(mysql_error());
					$companyId=mysql_insert_id();
				
					$updatecont="update sp_contact set comp_id='".$companyId."', designation='".$designationId."' where id='".$contactId."'";
					$rescp=mysql_query($updatecont) or die(mysql_error());
				}
				else
				{	
					$companyData=mysql_fetch_array($resc);
					$companyId=$companyData['comp_id'];
				}
			}
		
			if($companyId!='' and $contactId!='')
			{
				$lead_group_id = time()."-".$contactId;
				$categorylead=explode(',',$categoryCs);
				for($i = 0; $i < count($categorylead); ++$i)
				{	
					$categ = $categorylead[$i];
					if($categ!='')
					{
						$addlead="insert into sp_lead_generate set client_id='".$clientId."',
																   lead_group_id='".$lead_group_id."',
																   content_id='".$caseId."',
																   client_comp='".$company_case."',
																   lead_request='".$contactId."',
																   request_comp='".$companyId."',
																   city='".$cityId."',
																   category='".$categ."',
																   vertical='".$verticalCs."',
																   get_remark='".$leadRemark."',
																   source='".$source."',
																   doe='".$doe."'";
						$addldres=mysql_query($addlead) or die(mysql_error());
						$lead_group=mysql_insert_id();
					}
				}
	
	$newsitepath=$weburl;
    
	
		//Space for Mailer
	 $emailCotent="<table border='0' align='center' cellpadding='0' cellspacing='0' style='max-width:600px;'>
  <tr>
    <td bgcolor='#45bcd2'>&nbsp;</td>
    <td bgcolor='#45bcd2'>&nbsp;</td>
    <td bgcolor='#45bcd2'>&nbsp;</td>
  </tr>
  <tr>
    <td width='20' height='57' align='center' valign='top' bgcolor='#45bcd2'>&nbsp;</td>
    <td align='center' valign='top' bgcolor='#45bcd2' style='font-family: Arial, Helvetica, sans-serif;color:#FFFFFF;font-size:1.8em;font-weight:bold;'><img src='".$sitepath."img/mailer/1.jpg' alt='Welcome to ' hspace='0' vspace='0' border='0' align='top' /><img src='".$sitepath."img/mailer/1-1.jpg' alt='SalesPanda' hspace='0' vspace='0' border='0' align='top' /></td>
    <td width='20' align='center' valign='top' bgcolor='#45bcd2'>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor='#ececec'>&nbsp;</td>
    <td align='left' valign='top' bgcolor='#ececec' style='font-family: Arial, Helvetica, sans-serif;color:#363737;font-size:12px;line-height:20px;'>&nbsp;</td>
    <td bgcolor='#ececec'>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor='#ececec'>&nbsp;</td>
    <td align='left' valign='top' bgcolor='#ececec' style='font-family: Arial, Helvetica, sans-serif;color:#363737;font-size:12px;line-height:20px;'>
     <p><strong>Dear ".ucfirst($fname)."</strong></p>
     <p>Thank you for downloading <span style='color:#2293A9;'><strong>".$documentName."</strong></span>.</p>
     <p>To download document please click below:</p>
     <p style='margin:10px 0 10px 0;'><a href='".$defaultwebPath."/download-success.php?wrl=".$clientId."&csd=".$caseId."' style='cursor:pointer;  text-shadow: -1px -1px 0 rgba(0,0,0,0.3);  color: #FFFFFF!important;  border:1px solid #e38012;  background-color: #f69f2e;  background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f69f2e), color-stop(100%, #ed8e14));  background-image: -webkit-linear-gradient(top, #f69f2e, #ed8e14);  background-image: -moz-linear-gradient(top, #f69f2e, #ed8e14);  background-image: -ms-linear-gradient(top, #f69f2e, #ed8e14);  background-image: -o-linear-gradient(top, #f69f2e, #ed8e14);  background-image: linear-gradient(top, #f69f2e, #ed8e14); filter:progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=#f69f2e, endColorstr=#ed8e14);  -webkit-box-shadow: inset 0px 0px 0px 1px rgba(255, 255, 255, 0.3);  box-shadow: inset 0px 0px 0px 1px rgba(255, 255, 255, 0.3); text-decoration:none; padding:0.5em 1em;'><strong>Download</strong></a></p>
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
    <td height='42' align='center' valign='middle' bgcolor='#3e3e3e' style='font-family: Arial, Helvetica, sans-serif;color:#FFFFFF;font-size:0.8em;line-height:20px;font-weight:bold;'><p><img src='".$sitepath."img/mailer/5.jpg' alt='SalesPanda' hspace='0' vspace='0' border='0' align='middle'><img src='".$sitepath."img/mailer/5-1.jpg' alt=': Inbound Marketing Software' hspace='0' vspace='0' border='0' align='middle'></p></td>
    <td height='42' bgcolor='#3e3e3e'>&nbsp;</td>
  </tr>
</table>";

$Message= null;
$Message=$emailCotent;
$ToSubject = "SalesPanda: Download Document";
$from_email  = "info@salespanda.com"; 		

$json_string = array(
  'to' => array($email)
);

$params = array(
    'api_user'  => $user,
    'api_key'   => $pass,
    'x-smtpapi' => json_encode($json_string),
    'to'        => $email,
    'subject'   => $ToSubject,
    'html'      => $Message,
    'text'      => 'demo text 2',
    'from'      => $from_email,
  );
$request =  $url.'api/mail.send.json';

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
$response;

						//End Mailer section
			header("location:$defaultwebPath/download-success.php?csId=".$caseId."&lg=".$lead_group_id."&mail=".$email."");
			exit();
			}
		}
	}
	
	
	
	$urlval=trim($_SERVER['REQUEST_URI']);
	$urlvalexp=explode("/",$urlval);
	//print_r($urlvalexp);
	$url_cstdy=$urlvalexp[2];
	$caseStudyName=str_replace("-"," ",$url_cstdy);
	
	if($caseStudyName!='' && $_REQUEST['csId']=='')
	{
		$csdQ="select id from sp_case_study where case_study_title LIKE '%".$caseStudyName."%' or case_study_title LIKE '%".$url_cstdy."%' and client_id='".$c_lient_Id."' and approve=1 and valid=1 and deleted=0";
		$csdquery=mysql_query($csdQ) or die(mysql_error());
		$csdrow=mysql_fetch_array($csdquery);
		$caseids=$csdrow['id'];
		if($caseids=='')
		{
			header("location:$defaultwebPath");
		}
	}
	else if($caseStudyName=='' && $_REQUEST['csId']!='') 
	{
		$caseids=$_REQUEST['csId'];
	}
	else if($caseStudyName=='' && $_REQUEST['csId']=='') 
	{
		header("location:$defaultwebPath");
	}
	
	//$caseids=$_REQUEST['csId'];
	if($caseids!='' and $c_lient_Id!='')
	{
		//session_start();
		$qry="select * from sp_case_study where id='".$caseids."' and valid=1 and deleted=0 and approve=1";
		$resq=mysql_query($qry) or die(mysql_error());
		$docData=mysql_fetch_array($resq);
		$doctitle=$docData['case_study_title'];
		$docDescription=$docData['case_study_desc'];
		$clientId=$docData['client_id'];
		$_SESSION['c_lient_Id']=$docData['client_id'];
		$compId=$docData['comp_id'];
		$docType=$docData['content_type'];
		$documentType=getarticleName($docType);
		$landingTitle=$docData['landing_page_title'];
		$landingDesc=$docData['landing_page_desc'];		
		
		$ip_content= get_remote_user_ip();
     	$sTcontent=strtotime($doe);
	 	$eTcontent=strtotime("+1 minutes",$sTcontent);
     	$eDcontent = date("Y-m-d");
        
		$pg_content="SELECT * FROM sp_content_hits WHERE content_id='".$caseids."' and client_id='".$c_lient_Id."' and entry_date='".$eDcontent."' and ip_address='".$ip."' and page_content='".$page_hitssk."'";

		$qry_content=mysql_query($pg_content) or die(mysql_error());
		$exContent=mysql_num_rows($qry_content);
		if($exContent==0 && $fake_ip == 0)
		{
    		$insert_content = mysql_query($a="INSERT INTO sp_content_hits set page_content='".$page_hitssk."', content_hits='1',doe='".$doe."',client_id='".$c_lient_Id."',content_id='".$caseids."',content_type='".$contentType."',ip_address='".$ip_content."',entry_date='".$eDcontent ."',start_time='".$sTcontent."',end_time='".$eTcontent."'");
			
		}
		else
		{
	   		$getContent=mysql_fetch_array($qry_content);
       		$edt_caseStudytitle=$getContent['page_content'];
       		$edt_eDcontent=$getContent['entry_date'];
       		$edt_sTcontent=$getContent['start_time'];
	    	$endTimeValue=$getContent['end_time'];
        	$currentTimeValue=strtotime($doe);   
        	$edt_eTcontent=strtotime("+1 minutes",$currentTimeValue);
	  		$edt_ip=$getContent['ip_address'];
 			
        	if($currentTimeValue>=$endTimeValue && $fake_ip == 0)
   			{	
				$update_content="update sp_content_hits set page_content='".$edt_caseStudytitle."', content_hits= content_hits+1, ip_address='".$edt_ip."',start_time='".$edt_sTcontent."',end_time='".$edt_eTcontent."',content_type='".$contentType."' WHERE content_id = '$caseids' and client_id='".$c_lient_Id."' and ip_address='".$edt_ip."' and entry_date='".$edt_eDcontent."'";
				$update_qry=mysql_query($update_content) or die(mysql_error());
			}
		}
	}
	
	
?>

<!DOCTYPE HTML>
<!-- DON'T TOUCH THIS SECTION -->
<html>
<head>
<meta name="google-site-verification" content="5jCFxYfHvuPs3zIap9QWrx5CKhGNk5dLtfohTOC5Owo" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
<!--[if lt IE 9]>
<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<title>Download Free <?php echo ucwords($filename); ?></title>
<meta name="description" content="SalesPanda, a B2B Global marketplace for IT sellers & marketers to promote their offerings using Content Based Inbound Marketing. Companies can also find Leads & technology partners."/>
<meta name="keywords" content="Global marketplace, technology partners, IT, B2B, Cloud Solutions, IT Buying, IT Vendors, IT Product Evaluation, Technology Research, B2B Marketplace, IT Marketplace, Buying, Sales & Marketing Solutions, HR Solutions, Manufacturing Solutions, SCM Solutions, Business Analytics, Mobility, Social Media Solutions, Infrastructure, SAAS, PAAS, IAAS"/>

<?php echo $set_master; ?>
<?php echo $get_analytic; ?>
<link href='http://fonts.googleapis.com/css?family=Raleway|Playfair+Display+SC' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="<?php echo $sitepath;?>css_new/style.css">
<link rel="shortcut icon" href="<?php echo $sitepath;?>images/favicon.ico" />
<!-- END OF DON'T TOUCH -->

<style type="text/css">
body{background:#ffffff none;}
#mask {
  position:absolute;
  left:0;
  top:0;
  z-index:9000;
  background-color:#000;
  display:none;
}  
#boxes .window {
  position:absolute;
  left:0;
  top:0;
  width:440px;
  height:200px;
  display:none;
  z-index:9999;
  padding:20px;
}
#boxes #dialog {
  width:843px; 
  height:403px;
  padding:10px;
  background-color:#ffffff;
}
#boxes .close{right:0; top:0; position:absolute; height:14px; width:14px; background-color:#000000; color:#FFFFFF; font-size:10px; text-align:center;}
/*footer{position:relative; float:left; display:block;}*/

</style>

<script type="text/javascript" src="../js/jquery-1.8.0.js"></script>
<link rel="stylesheet" type="text/css" href="../jquery/jquery.autocomplete.css" />
<script type='text/javascript' src='../jquery/jquery.autocomplete.js'></script>

<!--<script type="text/javascript" src="../js/jquery.min.js"></script>
--><script type="text/javascript" src="../js/regvalidation.js"></script>
<!--<script type="text/javascript" src="../js/regvalidation2.js"></script>-->

<!--<script type="text/javascript">
$().ready(function() {
	$("#company").autocomplete("includes/get_company_list.php", {
		width: 238,
		matchContains: true,
		selectFirst: false
	});
});
</script>-->

<script type="text/javascript">
$().ready(function() {
	$("#designation1").autocomplete("../includes/get-designation.php", {
		width: 238,
		matchContains: true,
		selectFirst: false
	});
});
</script>

<script type="text/javascript">
$(document).ready(function() {
	$("#city").autocomplete("../../includes/get-company-city.php", {
		width: 238,
		matchContains: true,
		selectFirst: false
	});

});

</script>


</head>
<body>
<div class="wrapper">
<?php include("includes/header-new.php"); ?>
<section id="home"> 
<div class="widthFull font150 txtShadowW floatL">
        <p class="mrgA padA alignC lineH20 bdrB"><span class="grayTxt txtShadowW font120">Download <?php echo ucfirst($documentType); ?> <span class="greenTxt"><?php echo ucfirst($doctitle); ?></span></span></p>
    <div class="clear"></div>
    	<p class="mrgA padA alignC lineH bdrB font80"><?php echo ucfirst($landingTitle); ?></p>
    </div>
<div class="widthFull txtShadowW floatL lineH mrgB">
  <div class="left lineH20">
  <div class="padA bdrB font120 alignC lineH20">
            <p class="blackTxt alignL"><?php echo $landingDesc; ?></p>
            <div class="padL mrgL">
            <div class="floatL width99">
               <space for Document Description>
              </div>
<!--<div class="floatR width50 mrgT alignC padT">
                	<img src="<?php echo $sitepath; ?>images/doc.jpg" alt="" class="mrgT floatR" style="width:85%;">
                </div>-->
          </div>
            <div class="clear"></div>
           
    	</div>
 </div>
<div class="right grayTxt font100">
        <div class="alignC floatL widthFull">
<div class="grayLBg padA login radiusA boxshadow floatN alignL">

<form name="regfrm" id="regfrm" method="post" action="">
 
    <div class="font80 alignC error"><?php echo errmsg(); ?></div>

<div class="widthFull lineH floatL mrgT">
    <input class="fitTxtfild" type="text" name="fname" id="fname" value="" placeholder="Name"  />
</div>
<div id="fnameInfo" class=" padN widthFull floatL font80"></div>
<div class="widthFull floatL mrgT">
    <input class="fitTxtfild" type="text" name="email" id="email" value="" placeholder="Email ID"  />
</div>
<div id="aclass" class=" padN widthFull floatL font80"><div id="emailInfo" class=" padN widthFull"></div></div>

<div class="widthFull mrgT floatL">
  <div id="mNumber">  <input class="fitTxtfild" type="text" value="" name="phone" id="phone" placeholder="Enter Contact number" onKeyPress="return isNumberKey(event)" />
	<div id="mobileInfo" class=" padN widthFull floatL font80"></div>
</div>
</div>
<div id="" class=" padN floatL widthFull"></div>

<div class="widthFull lineH floatL mrgT">
    <input class="fitTxtfild" type="text" name="company" id="company" placeholder="Company name"  />
    
	<div id="companyIfo" class="padN floatL widthFull font80">
	 
	</div>
    
    <div class="widthFull lineH floatL mrgT">
  <input class="fitTxtfild" type="text" name="city" value="" id="city1"  placeholder="City name" />
</div>
<div id="webinfo" class=" padN floatL widthFull font80"></div>

<div class="widthFull lineH floatL mrgT">
  <input class="fitTxtfild" type="text" name="designation1" value="" id="designation11"  placeholder="Designation" />
</div>
<div id="designinfo" class=" padN floatL widthFull font80"></div>

    <div class="widthFull lineH floatL mrgT">
      <textarea name="remark" class="fitTxtfild" id="remark" placeholder="How can we help you?"></textarea>
</div>
<div id="remarkinfo" class=" padN floatL widthFull font80"></div>
</div>
 <input type="hidden" name="caseids" id="caseids" value="<?php echo $caseids; ?>" />
 <input type="hidden" name="pagesource" id="pagesource" value="1" />
 <input name="source" type="hidden" value="Document-Download" id="source" />



<div class="clearfix"></div>
<div class="mrgT floatL widthFull">
<input type="submit" name="button" id="button" value="Download Now" style="width:100%; padding:0%; font-size:110%; margin:0; font-weight:normal; height:32px; cursor:pointer;" class="btn" /></div>

<div class="clearfix"></div>

<div class="clearfix"></div>
</form>

</div>
</div>
</div>
</div>
<div class="clearfix"></div>
    </section>
<?php include("includes/footer-new.php"); ?>
</body>
</html>




<script language="javascript">
function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
             return true;
}

</script>