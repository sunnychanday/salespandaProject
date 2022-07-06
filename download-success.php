<?php
	include("includes/global.php");
	include("includes/check_login.php");
	
	include("includes/function.php");
	include("pagehitcounter.php");
	
	$mailer=$_REQUEST['wrl'];
	$case=$_REQUEST['csd'];
	$caseids=$_REQUEST['csId'];
	$email_id=$_REQUEST['mail'];
	

	$pc_member_info = getPCMemberInfo($c_lient_Id);
	$pcmember_pc_type = $pc_member_info['member_pc_type'];
	$p_client_id = $pc_member_info['p_client_id'];  

	if($pcmember_pc_type=='C'){ 
		$pcompqry = "select pid,comp_id from sp_members where client_id ='".$p_client_id ."' and valid=1 and deleted=0 and approve=1";
	}
	else
	{
		$pcompqry = "select pid,comp_id from sp_members where client_id ='".$c_lient_Id."' and valid=1 and deleted=0 and approve=1";
	}

	$pcompres = mysqli_query($conn, $pcompqry);  
	$pcomp_row = mysqli_fetch_array($pcompres);          
	$p_comp_id = $pcomp_row['comp_id'];  

	$page_hitssk ='http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$ip_content= get_remote_user_ip();
   	$sTcontent=strtotime($doe);
	$eTcontent=strtotime("+30 minutes",$sTcontent);
    $eDcontent = date("Y-m-d");
	
	if($case=='' && $caseids!='')
	{
	 	$casestudyId=$caseids;
	}
	else if($case!='' && $caseids=='')
	{
		$casestudyId=$case;
	}
	
	if($casestudyId!='')
	{
		$qry1="select * from sp_case_study where id='".$casestudyId."' and valid=1 and deleted=0";
		$resq1=mysqli_query($conn, $qry1);
		$docData1=mysqli_fetch_array($resq1);
		$docuType=$docData1['content_type'];
		$ctaType=$docData1['call_to_action'];
	}
	
    if($c_lient_Id!='')
    {
		$pg_content="SELECT * FROM sp_content_hits WHERE content_id='".$casestudyId."' and client_id='".$c_lient_Id."' and entry_date='".$eDcontent."' and ip_address='".$ip."' and page_content='".$page_hitssk."'";

		$qry_content=mysqli_query($conn, $pg_content);
		$exContent=mysqli_num_rows($qry_content);
		
		if($exContent==0 && $fake_ip == 0)
		{
		   $insert_content = mysqli_query($conn, $a="INSERT INTO sp_content_hits set page_content='".$page_hitssk."', content_hits='1',doe='".$doe."',client_id='".$c_lient_Id."',content_id='".$casestudyId."', content_type='".$docuType."',call_to_action='".$ctaType."', ip_address='".$ip_content."',entry_date='".$eDcontent ."',start_time='".$sTcontent."',end_time='".$eTcontent."'");
		}
		else
		{
			$getContent=mysqli_fetch_array($qry_content);
			$edt_caseStudytitle=$getContent['page_content'];
			$edt_eDcontent=$getContent['entry_date'];
			$edt_sTcontent=$getContent['start_time'];
			$endTimeValue=$getContent['end_time'];
			$currentTimeValue=strtotime($doe);   
			$edt_eTcontent=strtotime("+30 minutes",$currentTimeValue);
			$edt_ip=$getContent['ip_address'];
			$ctaold=$getContent['call_to_action'];
		
			if($currentTimeValue>=$endTimeValue && $fake_ip == 0)
			{	
			   $update_content="update sp_content_hits set page_content='".$edt_caseStudytitle."', content_hits= content_hits+1, ip_address='".$edt_ip."',start_time='".$edt_sTcontent."',end_time='".$edt_eTcontent."',content_type='".$docuType."',call_to_action='".$ctaType."'  WHERE content_id = '".$casestudyId."' and client_id='".$c_lient_Id."' and ip_address='".$edt_ip."' and entry_date='".$edt_eDcontent."'";
				$update_qry=mysqli_query($conn, $update_content);
			}
		}
	}

	if($mailer!='' && $case!='' && $caseids=='')
	{
		$qry="select * from sp_case_study where id='".$case."' and valid=1 and deleted=0";
		$resq=mysqli_query($conn, $qry);
		$docData=mysqli_fetch_array($resq);
		$doctitle=$docData['case_study_title'];
		$docDescription=$docData['case_study_desc'];
		$clientId=$docData['client_id'];
		$compId=$docData['comp_id'];
		$docType=$docData['content_type'];
		$document=$docData['case_study'];
		//$genpdfDocument=$docData['case_study_genpdf'];  
		$cs_library=$docData['case_study_library'];
		$documentType=getarticleName($docType);
		$verifyMail=$docData['verify_by_email'];
		$csMode=$docData['cs_mode'];
		
		$cta_id=$docData['call_to_action'];
		$cta_get="select * from cta_button where id='".$cta_id."'";
		$cta_set=mysqli_query($conn, $cta_get);
		$ctaData=mysqli_fetch_array($cta_set);
		$cta_name=$ctaData['ctaType'];
		
		$cstudy_title=$docData['case_study_title'];

		$qry_content_pdf="select template_id,generate_template_pdf from user_templates where template_id='".$cs_library."' and content_file='' and valid=1 and deleted=0";
		$result_content_pdf=mysqli_query($conn, $qry_content_pdf);
		$row_content_pdf=mysqli_fetch_array($result_content_pdf);
		$genpdfDocument=$row_content_pdf['generate_template_pdf'];
	}
	else if($caseids!='' && $mailer=='' && $case=='')
	{
		$qry="select * from sp_case_study where id='".$caseids."' and valid=1 and deleted=0";
		$resq=mysqli_query($conn, $qry);
		$docData=mysqli_fetch_array($resq);
		$doctitle=$docData['case_study_title'];
		$docDescription=$docData['case_study_desc'];
		$clientId=$docData['client_id'];
		$compId=$docData['comp_id'];
		$docType=$docData['content_type'];
		$document=$docData['case_study'];
		$genpdfDocument=$docData['case_study_genpdf'];  
		$cs_library=$docData['case_study_library'];
		$documentType=getarticleName($docType);
		$verifyMail=$docData['verify_by_email'];
		$csMode=$docData['cs_mode'];
		
		$cta_id=$docData['call_to_action'];
		$cta_get="select * from cta_button where id='".$cta_id."'";
		$cta_set=mysqli_query($conn, $cta_get);
		$ctaData=mysqli_fetch_array($cta_set);
		$cta_name=$ctaData['ctaType'];

		$cstudy_title=$docData['case_study_title'];

		$qry_content_pdf="select template_id,generate_template_pdf from user_templates where template_id='".$cs_library."' and content_file='' and valid=1 and deleted=0";
		$result_content_pdf=mysqli_query($conn, $qry_content_pdf);
		$row_content_pdf=mysqli_fetch_array($result_content_pdf);
		$genpdfDocument=$row_content_pdf['generate_template_pdf'];
       
	}
	else if($caseids=='' || $mailer=='' || $case=='')
	{
		header("location:index.php");
	}
	
	$requri=$_SERVER['REQUEST_URI'];
	$urlpath=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	
	if($url!='www.technochimes.com/salespanda/')
	{
		$urlpath;
		$webf="www";
		$aaa=explode('.', $urlpath);
		$first=$aaa[1];
		$second=$aaa[2];
		$agSecond=explode('/', $second);
		$fseconf=$agSecond[0];
		$finalWeb=$webf.".".$first.".".$fseconf;
	}
	
	$qry="select * from sp_company where company_website='".$finalWeb."'"; 
	$res=mysqli_query($conn, $qry);  
	$lData=mysqli_fetch_array($res);
	//$headerLogo=$lData['header_logo'];
 	$clientId=$lData['client_id'];
	$_SESSION['c_lient_Id']=$lData['client_id'];
	//$comp_id=$lData['comp_id'];
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
<link href='http://fonts.googleapis.com/css?family=Raleway|Playfair+Display+SC' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="<?php echo $sitepath;?>css_new/style.css">
<link rel="shortcut icon" href="<?php echo $sitepath;?>images/favicon.ico" />
<!-- END OF DON'T TOUCH -->

<!--<style type="text/css">
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
footer{position:relative; float:left; display:block;}

</style>-->

<script type="text/javascript" src="<?php echo $sitepath;?>js/jquery-1.8.0.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.0/jquery.min.js"></script>
<script type="text/javascript">

$(function() {
//More Button
$('.more').live("click",function() 
{
var ID = $(this).attr("id");

if(ID)
{ 
$("#more"+ID).html('<img src="images/moreajax.gif" />');
$.ajax({
type: "POST",
url: "ajax_more.php",
data: "lastmsg="+ ID, 
cache: false,
success: function(html){ 
$("div#updates").append(html);
$("#more"+ID).remove();
}
});
}
else
{
$(".morebox").html('');
}
return false;
});
});

</script>
</head>
<body>
	<div class="wrapper">
		<?php include("includes/header-new.php"); ?>
		
		<section id="home"> 
			<div class="widthFull font150 txtShadowW floatL">
				<?php
				if($csMode!='library')
				{
					if($caseids!='' && $mailer=='' && $case=='' && $cta_name=='2')
					{
						?>

						<p class="mrgA padA alignC lineH20 whiteBg boxshadow radiusA"><span class="grayTxt">Thank you for your request. We will revert back to you soon on</span> <span class="greenTxt"><?php echo $email_id; ?></span></p>
						<div class="clear"></div>

						<?php
					}
				}

				if($csMode!='library')
				{
					if($caseids!='' && $mailer=='' && $case=='' && $cta_name=='1')
					{
						?>
						<p class="mrgA padA alignC lineH20 whiteBg boxshadow radiusA">
							<span class="grayTxt upcase">
								<?php
								if($pcmember_pc_type=='C'){
									?>
									<a href="<?php echo getweburl($clientId);?>upload/casestudy/<?php echo $p_client_id;?>/<?php echo $document; ?>" target="_blank">Click Here</a> to download &lsquo;<?php echo ucfirst($cstudy_title); ?>&rsquo;</span>
									<?php
								} else{
									?>
									<a href="<?php echo getweburl($clientId);?>upload/casestudy/<?php echo $c_lient_Id;?>/<?php echo $document; ?>" target="_blank">Click Here</a> to download &lsquo;<?php echo ucfirst($cstudy_title); ?>&rsquo;</span>
									<?php
								}
								?>
						</p>
						<div class="clear"></div>
						<?php 
					}
				}
				
				if($csMode!='library')
				{
					if($caseids=='' && $mailer!='' && $case!='' && $cta_name=='1')
					{
						?>  
						<p class="mrgA padA alignC lineH20 whiteBg boxshadow radiusA">
						<?php
						if($pcmember_pc_type=='C'){
							?>
							<span class="grayTxt upcase"><a href="<?php echo getweburl($clientId);?>upload/casestudy/<?php echo $p_client_id;?>/<?php echo $document; ?>" target="_blank">Click Here</a> to download &lsquo;<?php echo ucfirst($cstudy_title); ?>&rsquo;</span>
							<?php
						}else{
							?>
							<span class="grayTxt upcase"><a href="<?php echo getweburl($clientId);?>upload/casestudy/<?php echo $c_lient_Id;?>/<?php echo $document; ?>" target="_blank">Click Here</a> to download &lsquo;<?php echo ucfirst($cstudy_title); ?>&rsquo;</span>
							<?php
						}
						?>
						</p>
						<div class="clear"></div>
						<?php
					}
				}
				
				if($csMode!='library')
				{
					if($caseids!='' && $mailer=='' && $case=='' && $cta_name=='Contact')
					{
						?>
						<p class="mrgA padA alignC lineH20 whiteBg boxshadow radiusA">
							<?php
							if($pcmember_pc_type=='C'){
								?>
								<span class="grayTxt upcase"><a href="<?php echo getweburl($clientId);?>upload/casestudy/<?php echo $p_client_id;?>/<?php echo $document; ?>" target="_blank">Click Here</a> to download &lsquo;<?php echo ucfirst($cstudy_title); ?>&rsquo;</span>  
								<?php
							}else{
								?>
								<span class="grayTxt upcase"><a href="<?php echo getweburl($clientId);?>upload/casestudy/<?php echo $c_lient_Id;?>/<?php echo $document; ?>" target="_blank">Click Here</a> to download &lsquo;<?php echo ucfirst($cstudy_title); ?>&rsquo;</span>
								<?php
							}
							?>
						</p>
						<div class="clear"></div>
						<?php 
					}
				} 
 
				if($csMode!='library')
				{
					if($caseids=='' && $mailer!='' && $case!='' && $cta_name=='Contact')
					{
						?>  
      
						<p class="mrgA padA alignC lineH20 whiteBg boxshadow radiusA">
							<?php
							if($pcmember_pc_type=='C'){
								?>
								<span class="grayTxt upcase"><a href="<?php echo getweburl($clientId);?>upload/casestudy/<?php echo $p_client_id;?>/<?php echo $document; ?>" target="_blank">Click Here</a> to download &lsquo;<?php echo ucfirst($cstudy_title); ?>&rsquo;</span>
								<?php
							}else{
								?>
								<span class="grayTxt upcase"><a href="<?php echo getweburl($clientId);?>upload/casestudy/<?php echo $c_lient_Id;?>/<?php echo $document; ?>" target="_blank">Click Here</a> to download &lsquo;<?php echo ucfirst($cstudy_title); ?>&rsquo;</span>
								<?php
							}
							?>
						</p>
						<div class="clear"></div>

						<?php
					}
				}
				
				if($csMode=='library')
				{
					if($mailer=='' && $case=='' && $caseids!='' && $cta_name=='1')
					{
						if($genpdfDocument!=''){  
							?>
							<p class="mrgA padA alignC lineH20 whiteBg boxshadow radiusA">
								<?php
								if($pcmember_pc_type=='C'){
									?>
									<span class="grayTxt upcase"><a href="<?php echo getweburl($clientId);?>upload/casestudy/<?php echo $p_client_id;?>/<?php echo $genpdfDocument; ?>" target="_blank">Click Here</a> to download &lsquo;<?php echo ucfirst($cstudy_title); ?>&rsquo;</span>
									<?php
								}else{
									?>
									<span class="grayTxt upcase"><a href="<?php echo getweburl($clientId);?>upload/casestudy/<?php echo $c_lient_Id;?>/<?php echo $genpdfDocument; ?>" target="_blank">Click Here</a> to download &lsquo;<?php echo ucfirst($cstudy_title); ?>&rsquo;</span>
									<?php
								}
								?>
							</p>
							<?php
						}else{
							?>
							<p class="mrgA padA alignC lineH20 whiteBg boxshadow radiusA"><span class="grayTxt">Thanks for downloading.A download link has been sent to <?php echo $email_id ?> </span></p>  
							<?php
						}	
					}
				}

				if($csMode=='library')
				{
					if($mailer!='' && $case!='' && $caseids=='' && $cta_name=='1')
					{
						if($genpdfDocument!=''){   
							?>
							<p class="mrgA padA alignC lineH20 whiteBg boxshadow radiusA">
							<?php
							if($pcmember_pc_type=='C'){
								?> 
								<span class="grayTxt upcase"><a href="<?php echo getweburl($clientId);?>upload/casestudy/<?php echo $p_client_id;?>/<?php echo $genpdfDocument; ?>" target="_blank">Click Here</a> to download&lsquo;<?php echo ucfirst($cstudy_title); ?>&rsquo;</span>
								<?php
							}else{
								?>
								<span class="grayTxt upcase"><a href="<?php echo getweburl($clientId);?>upload/casestudy/<?php echo $c_lient_Id;?>/<?php echo $genpdfDocument; ?>" target="_blank">Click Here</a> to download&lsquo;<?php echo ucfirst($cstudy_title); ?>&rsquo;</span>     
								<?php
							}
							?> 
							</p>
							<?php
						}else{
							?>
							<p class="mrgA padA alignC lineH20 whiteBg boxshadow radiusA"><span class="grayTxt">Thanks for downloading.A download link has been sent to <?php echo $email_id ?></span></p>
							<?php 
						}
					}
				}
				
				if($csMode=='library')
				{
					if($mailer=='' && $case=='' && $caseids!='' && $cta_name=='Contact')
					{
						?>
						<p class="mrgA padA alignC lineH20 whiteBg boxshadow radiusA"><span class="grayTxt upcase"><a href="create-template-pdf.php?wrl=<?php echo $c_lient_Id;?>&csd=<?php echo $casestudyId; ?>" target="_blank">Click Here</a> to download &lsquo;<?php echo ucfirst($cstudy_title); ?>&rsquo;</span></p>
						<?php 
					}
				}
				
				if($csMode=='library')
				{
					if($mailer!='' && $case!='' && $caseids=='' && $cta_name=='Contact')
					{
						?>
						<p class="mrgA padA alignC lineH20 whiteBg boxshadow radiusA"><span class="grayTxt upcase"><a href="create-template-pdf.php?wrl=<?php echo $c_lient_Id;?>&csd=<?php echo $casestudyId; ?>" target="_blank">Click Here</a> to download &lsquo;<?php echo ucfirst($cstudy_title); ?>&rsquo;</span></p>
						<?php 
					}
				}
				
				if($csMode=='library')
				{
					if($mailer=='' && $case='' && $caseids!='' or $cta_name=='2')
					{
						?>

						<p class="mrgA padA alignC lineH20 whiteBg boxshadow radiusA"><span class="grayTxt">Thank you for your request. We will revert back to you soon on</span> <span class="greenTxt"><?php echo $email_id; ?></span></p>
						<div class="clear"></div>
						<?php 
					}
				}
				?>
			</div>
			<div class="clear"></div>
			
			<div id="updates">
				<?php
				//if($clientId!='')
				//	{	
				if($pcmember_pc_type=='C'){
					$csqry = "select CS.*,TS.id as syndid from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".$clientId."' and CS.valid=1 and CS.deleted=0 and TS.approve=1 ORDER BY CS.id desc limit 4";
				}
				else{
					$csqry = "select * from sp_case_study where client_id='".$clientId."' and valid=1 and deleted=0 and approve=1 ORDER BY id desc limit 4";
				}   
				
				$csres = mysqli_query($conn, $csqry);
				$csCount=mysqli_num_rows($csres);
				//}
				
			if($csCount!=0){
				while($caseStudy = mysqli_fetch_array($csres))
				{
					$caseStudyId = $caseStudy['id'];
					$casestudyMember=$caseStudy["member_id"];
					$caseStudyName = $caseStudy['case_study'];
					$documentMode=$caseStudy['doc_mode'];
					//$total_liked = $caseStudy["total_liked"];
					//$total_tag = $caseStudy["total_tag"];
					$caseLandStatus=$caseStudy['landingpage_status']; 
					$caseLandId=$caseStudy['landingpage_id'];  

					$caseStudyDescription1 = $caseStudy['case_study_desc'];
					$caseStudyDescription = substr("$caseStudyDescription1", '0', '260')."..";
					$castStudyUrl =  $caseStudy['case_study_url'];
					$thumbimage = $caseStudy['image_thumb1'];
					$crop_image=$caseStudy['crop_Image'];
			
					$caseStudyTitle11 = $caseStudy['case_study_title'];
				
					$contentType=$caseStudy['content_type'];
					if($contentType!='')
					{
						$contentTypeName=getarticleName($caseStudy['content_type']);
					}	
			
					if($contentType!='')
					{
						$casestd="select article_type from sp_article_type where id='".$article."'";
						$casequery=mysqli_query($conn, $casestd);
						$caserow=mysqli_fetch_array($casequery);
						$articleName=$caserow['article_type'];
						$articleId=$caserow['id'];
					}
					
					if($articleId==$contentType)
					{
						$urlName=$articleName;
					}

					$caseStudyTitleLength=strlen($caseStudyTitle11);
					$attachforcompany = $caseStudy['attach_company'];
					$filterFlag='s';
		
					//htaccess title convert
					$csname =str_replace(' ', '-', $caseStudyTitle11);
					
					//end
					if($caseStudyTitleLength > 35)
					{
						$caseStudyTitle = substr("$caseStudyTitle11", '0', '35');
					}
					else
					{
						$caseStudyTitle=$caseStudyTitle11;
					}
					
					if($pcmember_pc_type=='C'){
						$cslandquery=mysqli_query($conn, "select LS.*, LP.publish_page_id,LP.landingpage_title,LP.publish_page_name,LP.landingpage_desc,LP.page_title_seo,LP.meta_description from sp_landingpage_publish as LP INNER JOIN sp_landingpage_syndication as LS ON LP.publish_page_id = LS.landingpage_id where LP.publish_page_id='".$caseLandId."' and LP.client_id='".$p_client_id."' ");
					}
					else
					{
						$cslandquery=mysqli_query($conn, "select  publish_page_id,landingpage_title,publish_page_name,landingpage_desc,page_title_seo,meta_description,approve from sp_landingpage_publish where publish_page_id='".$caseLandId."' and client_id='".$clientId."' ");
					}

					$cslandget=mysqli_fetch_array($cslandquery);
					$cslandname=$cslandget['publish_page_name'];
					$cslandApprove= $cslandget['approve'];
					?>	 
					<div id="<?php echo $caseStudyId; ?>" class="item">
						<?php
						if($thumbimage!='' && $crop_image==''){
							?>
			          		<img class="thumb" src="manager/uploads/<?php echo $thumbimage; ?>" id="<?php echo $caseStudyId; ?>"/>
							<input name="articleId" type="hidden" value="<?php echo $caseStudyId ; ?>" id="articleId" />
							<?php
						}
						else if($thumbimage=='' && $crop_image!='') {
							if($caseLandStatus==1 && $cslandApprove==1) {
								?> 
								<a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>"><img id='base64image' src='data:image/png;base64,<?php echo $crop_image; ?>' /></a>
								<?php
							}else{
								?>
								<a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>"><img id='base64image' src='data:image/png;base64,<?php echo $crop_image; ?>' /></a>
								<?php
							}
							?>
							<input name="articleId" type="hidden" value="<?php echo $caseStudyId ; ?>" id="articleId" />
							<?php
						} else {
							?> 
							<img src="<?php echo $sitepath; ?>upload/casestudy/nopic/nopic.jpg" class="thumb">
							<input name="articleId" type="hidden" value="<?php echo $caseStudyId ; ?>" id="articleId" />
							<?php
						}
						?>
			
						<!-- Image must be 400px by 300px -->
						<div class="itemSlideTxt">
							<div class="itemH3">
								<?php
								if($caseLandStatus==1 && $cslandApprove==1) {
									?> 
									<a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>"><?php echo ucfirst($caseStudyTitle11); ?></a>
									<?php
								}else{
									?>
									<a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>"><?php echo ucfirst($caseStudyTitle11); ?></a>
									<?php
								}
								?>
							</div>
							<div class="itemH4"><?php echo $contentTypeName; ?></div>
							<p><?php echo $caseStudyDescription; ?></p>
							<?php
							if($caseLandStatus==1 && $cslandApprove==1) {
								?>  
								<div class="alignC itemSlideBtn"><a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>" class="btn whiteTxt padL padR normal" target="_blank">Read more &raquo;</a></div>
            
								<?php
							}else{
								?>
								<div class="alignC itemSlideBtn"><a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>" class="btn whiteTxt padL padR normal">Read more &raquo;</a></div>
            
								<?php
							}
							?>
						</div>
					</div>
					<?php
				}
				?>
			</div>
			<div class="clearfix"></div>
			<div id="more<?php echo $caseStudyId; ?>" class="morebox widthFull alignC mrgT"> 
				<a href="#" class="more grayLTxt" id="<?php echo $caseStudyId; ?>">show more</a>
			</div>
			<?php
		}
		?>
    
		<div class="clearfix"></div>
	</section>
	</div>
	<?php include("includes/footer-new.php"); ?>
	
	<script language="javascript">
	function isNumberKey(evt)
	{
		var charCode = (evt.which) ? evt.which : event.keyCode
		if (charCode > 31 && (charCode < 48 || charCode > 57))
				 return false;
				 return true;
	}
	</script>
</body>
</html>
