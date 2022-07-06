<?php
	
	include("../includes/connect.php");
	include("../includes/function.php");

     
       if(empty($c_lient_Id))
       {
       $subdomainqry=mysql_query("select client_id,client_id,cms_subdomain_url from sp_subdomain where cms_subdomain_url='".$_SERVER['HTTP_HOST']."' and valid=1 and deleted=0 and status=1");
      
       $subdomainget=mysql_fetch_array($subdomainqry);
       $c_lient_Id=$subdomainget['client_id'];
        $sdomainPath=$subdomainget['subdomain_url'];
	    $redirectPath='http://'.$sdomainPath;
	
	    $weburl="http://".$subdomainget['cms_subdomain_url'];
       }
       

        $urlpath=$_SERVER['REQUEST_URI'];
        $urlpath_arr=explode('?channel_type=',$urlpath);   
        $channel_type = $urlpath_arr[1];
        $reqMsg=explode('?reqMsg=',$urlpath); 
        $reqMsg= urldecode($reqMsg[1]);
        
        if($_SERVER['HTTP_REFERER']!='')
        {
          $ref_url = $_SERVER['HTTP_REFERER']; 
        }

       
        $pc_member_info = getPCMemberInfo($c_lient_Id);
        $pcmember_pc_type = $pc_member_info['member_pc_type'];
        $p_client_id = $pc_member_info['p_client_id'];  

        
        $pcompqry = "select pid,comp_id,person_email,first_name,last_name,person_contact1,person_contact2 from sp_members where client_id ='".$c_lient_Id."' and valid=1 and deleted=0 and approve=1 and company_member_type=1";
        
        $pcompres = mysql_query($pcompqry) or die(mysql_error());  
        $pcomp_row = mysql_fetch_array($pcompres);          
        $p_comp_id = $pcomp_row['comp_id']; 
       

        $qry="select favicon,header_logo from sp_company where comp_id='".$p_comp_id."' and valid=1 and deleted=0"; 
        $res=mysql_query($qry) or die(mysql_error());  
        $lData=mysql_fetch_array($res);
        $industryLabel=$lData['target_vertical_level'];
        $faviconimg=$lData['favicon'];       
         

    

     
    
	 
	
	
	$page_hitssk ='http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$ip= get_remote_user_ip();
	$agent=$_SERVER['HTTP_USER_AGENT'];
	$urlval=$_SERVER['REQUEST_URI'];
	
	$urlvalexp=explode("/",$urlval);
	
	$url_cstdy=$urlvalexp[2];
	
	$url_cstdy_tw=explode("?",$url_cstdy);
	
	$url_cstdy_tt=urldecode($url_cstdy_tw[0]);

	$caseStudyName=str_replace("-"," ",$url_cstdy_tt); 

        $caseStudyName=urldecode($caseStudyName);
        
	
	$url1=$weburl."/showcase/".$url_cstdy_tt;
	
  
            if($pcmember_pc_type=='C')
            {
                
             $csdquery = mysql_query($t="select CS.id,CS.case_study_title,TS.id as syndid from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".$c_lient_Id."' and CS.valid=1 and CS.deleted=0 and TS.approve=1 and (CS.case_study_title = '".mysql_real_escape_string($caseStudyName)."' or CS.case_study_title = '".mysql_real_escape_string($url_cstdy_tt)."')");
               
            }
            else{
              
                  $csdquery = mysql_query($t="select id,case_study_title from sp_case_study where client_id='".$c_lient_Id."' and approve=1 and valid=1 and deleted=0 and (case_study_title = '".mysql_real_escape_string($caseStudyName)."' or case_study_title = '".mysql_real_escape_string($url_cstdy_tt)."')");
              
           }
           
          

		$csdrow=mysql_fetch_array($csdquery);
		$caseId=$csdrow['id'];
		$caseSTitle = str_replace(" ","-",$csdrow['case_study_title']);
		
	
	

	
		
	$flag=$_REQUEST['flag'];
	$leadgroupId=$_REQUEST['lg'];
	if($leadgroupId!='')
	{
		$resld=mysql_query("select lead_group_id from sp_lead_generate where valid=1 and deleted=0") or die(mysql_error());
		$leadCount=mysql_num_rows($resld);
	}
	
        if($pcmember_pc_type=='C')
        {
            
        //$template_doc=mysql_query("select case_study_library,id from sp_case_study where client_id='".$p_client_id."' and case_study_title='".$url_cstdy_tt."' and valid=1 and deleted=0"); 
        //$template_html=mysql_fetch_array($template_doc); 
      
        //$sqlsyndtemplate = "SELECT template_id FROM sp_template_syndication where c_client_id = '".$c_lient_Id."' and p_client_id='".$p_client_id."' and template_id = '".$template_html['case_study_library']."' and valid=1 and deleted=0 ";
        //$rssyndtemplate =mysql_query($sqlsyndtemplate);
        //$syndtemprecordcount = mysql_num_rows($rssyndtemplate); 
        
        //if($syndtemprecordcount==0)
        //{ 
            //$addsyndcont="insert into sp_template_syndication set p_client_id='".$p_client_id."',	
                //c_client_id='".$c_lient_Id."',									
        		//template_id='".$template_html['case_study_library']."',
        		//case_id='".$template_html['id']."',
        		//approve=1,
        		//doe='".date('Y-m-d H:i:s')."'";
            //$ressyndcont=mysql_query($addsyndcont) or die(mysql_error());
        //}    
            
		$cres = mysql_query("select CS.*,TS.id as syndid, TS.p_client_id, TS.c_client_id, TS.submem_content_publish_url from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.case_id='".$caseId."' and TS.c_client_id='".$c_lient_Id."' and CS.valid=1 and CS.deleted=0 and TS.approve=1") or die(mysql_error());
        }
        else
        {
	       $cres = mysql_query("select * from sp_case_study where id='".$caseId."' and client_id='".$c_lient_Id."' and approve=1 and valid=1 and deleted=0") or die(mysql_error());
        }

	$casecount = mysql_num_rows($cres);
	if($casecount!=0)  
	{
		$caseData = mysql_fetch_array($cres);
		$caseStudyFile = $caseData['case_study'];
                $cs_mode = $caseData['cs_mode'];
                $pcaseStudyApprove = $caseData['approve'];
                $template_id = $caseData['case_study_library'];
		$caseStudyUrl = $caseData['case_study_url'];
		$referenceSite_Link= $caseData['case_study_url'];
		$thumbimage=$caseData['image_thumb1'];
		$cropImage=$caseData['crop_Image'];
		$caseStudyContent = $caseData['case_study_content'];
		$caseStudytitle = $caseData['case_study_title']; 
		$caseStudyActualtitle = ($caseData['case_study_actual_title']!='') ? $caseData['case_study_actual_title'] : $caseData['case_study_title'];
		$caseStudyDesc1 = $caseData['case_study_desc'];
		$caseStudyDesc=str_replace("\\","",$caseStudyDesc1);
		
		$caseStudyDescforGA = substr($caseStudyDesc, 0, 165);	//for Google Analytics DESCRIPTION
		$casestudyMember =$caseData['member_id']; 
		$contentType=$caseData['content_type'];
		$docTypeName=ucwords(getarticleName($contentType));
		$docuType =str_replace(' ', '-', $docTypeName);
		
                $facebook_image=$caseData['facebook_image'];
                $facebook_desc=$caseData['facebook_desc'];
                $facebook_title=$caseData['facebook_title'];


		$solution=explode(',',$caseData["category"]);
		$industry=explode(',',$caseData["vertical"]);
		if($industry!='')
		{
			foreach($industry as $val)
			{
				$industryName.=segmentName($val).', ';
			}
		}
		$industry_name= substr($industryName,0,-2);
		
		$keyword=$caseData['category_keyword'];
		$solkey=explode(',',$keyword);
		if($solkey!='')
		{
			foreach($solkey as $keyval)
			{
				$keyName.=keywordName($keyval).', ';
			}
		}
		$solKeyword= substr($keyName,0,-2);
		$metaDescription=$caseData['meta_description'];
		$calltoaction=$caseData['call_to_action'];
	
		
		if($calltoaction!='')
		{
		    if($pcmember_pc_type=='C')
           {	
			$qryscp="select * from cta_button where id='".$calltoaction."' and client_id='".$p_client_id."' and valid=1 and deleted=0";
           }
           else
           {
           	$qryscp="select * from cta_button where id='".$calltoaction."' and client_id='".$c_lient_Id."' and valid=1 and deleted=0";    
           }
			$resp=mysql_query($qryscp) or die(mysql_error());
			$scpData=mysql_fetch_array($resp);
			$ctaurl211=$scpData['button_script'];
			$scrptDetail12= str_replace("<q>","'",$ctaurl211);
			$scrptDetail14= str_replace('&quot;','"',$scrptDetail12);
			$cta_name=trim($scpData['ctaName']);
			$scrptDetail13= str_replace('"$"','$',$scrptDetail14);
			$ctaType=$scpData['ctaType'];
			
			$btncolor=$scpData['buttonColor'];
			$btnLbl=$scpData['buttonLabel'];
			$btnbgColor=$scpData['btn_background_color'];
			$btntextcolor=$scpData['btn_text_color'];
			
			
			$bqry="select * from cta_type where id='".$ctaType."'";
			$resb=mysql_query($bqry) or die(mysql_error());
			$btntpdata=mysql_fetch_array($resb);
			$buttonProprty=$btntpdata['required_url'];
		}
		
		
		$verifybyMail=$caseData['verify_by_email'];
		$pageTitle=$caseData['page_title_seo'];
		
		$entrydate = dateFormat1($caseData['doe']);
	
	}
	




if($pcmember_pc_type=='C')
   {
    $query_content_preview = mysql_query("select T.content_file, T.showcasepdfImage,T.video_file,T.cobrand,T.VideoId, TS.id as synid from user_templates as T INNER JOIN sp_template_syndication as TS ON  T.template_id=TS.template_id where T.template_id='".$template_id."' and TS.c_client_id='".$c_lient_Id."' and T.valid=1 and T.deleted=0 and TS.approve=1");
    }
 else
 {  
   $query_content_preview = mysql_query("select content_file,showcasepdfImage,video_file,VideoId,cobrand from user_templates where template_id='".$template_id."' and client_id='".$c_lient_Id."' and valid=1 and deleted=0");
  }


$row_content_preview=mysql_fetch_array($query_content_preview);




            $selqry="select * from sp_design_table where client_id='".$c_lient_Id."'";
            $selres=mysql_query($selqry) or die(mysql_error());
            $degData=mysql_fetch_array($selres);
            
            
              
		      if($degData['pdfcobrandname']!='')
                 {
		        $distri_name = $degData['pdfcobrandname'];
                 }
                 else
                 {
                  $distri_name = $pcomp_row['first_name'].' '.$pcomp_row['last_name'];   
                 }
            
            
            if($degData['pdfcobrandphone']!='')
                 {
		         $distri_contact=$degData['pdfcobrandphone'];
                 }
                 else
                 {
		
                if($pcomp_row['person_contact1']!='')
                {
                  $distri_contact = $pcomp_row['person_contact1'];
                }
                else
                {
                  $distri_contact = $pcomp_row['person_contact2'];
                }
                
                 }
                 
                 
                if($degData['pdfcobrandemail']!='')
                {
                $distri_email=$degData['pdfcobrandemail'];
                }
                else
                {
                $distri_email=$pcomp_row['person_email'];   
                }
                
                
                if($degData['pdfcobrandImage']!='')
                {
		      $compLogo = $degData['pdfcobrandImage'];
                }
                else
                {
              $compLogo = $logoData['header_logo'];         
                }
		


?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $caseStudyActualtitle; ?>: <?php echo $docTypeName; ?></title>
    <meta name="description" content="<?php echo $metaDescription;?>"/>
    <meta property="og:image" content="<?php echo $facebook_image; ?>"/>
    <meta name="keywords" content="<?php echo $solKeyword; ?>"/>
    <meta property="og:type" content="article"/>
   <meta property="og:title" content="<?php echo $facebook_title; ?>" />
   <meta property="og:description" content="<?php echo substr($facebook_desc,0,150);?>" />
   <meta property="og:url" content="<?php echo $url1; ?>?channel_type=facebook"/>

    <!-- Bootstrap core CSS -->
   
     <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
     <link rel="stylesheet" href="<?php echo $weburl; ?>/css/style.css">
     <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
 
   
  <style>

  
  header.white-transparent {
    background: #003868;
    height: 75px;
}


.carousel-control .icon-prev, .carousel-control .icon-next, .carousel-control .glyphicon-chevron-left, .carousel-control .glyphicon-chevron-right {
 color:#272727;
}

.carousel-control.left, .carousel-control.right {
   background-image:none !important;
   filter:none !important;
}

.sp-footer .sp-contact i {
    color: #ffffff;
    float: left;
    display: table-cell;
    width: 30px;
    line-height: 23px;
    font-size: 32px;
}

#crestashareicon {
  position: fixed;
  top: 50%;
  -webkit-transform: translateY(-50%);
  -ms-transform: translateY(-50%);
  transform: translateY(-50%);
}

#crestashareicon a {
  display: block;
  text-align: center;
  padding: 16px;
  transition: all 0.3s ease;
  color: white;
  font-size: 14px;
}

#crestashareicon a:hover {
  background-color: #000;
}

.facebook {
  background: #3B5998;
  color: white;
}

.twitter {
  background: #55ACEE;
  color: white;
}

.google {
  background: #dd4b39;
  color: white;
}

.linkedin {
  background: #007bb5;
  color: white;
}

.youtube {
  background: #bb0000;
  color: white;
}

h5 {
    font-size: 18px;
    line-height: 35px;
}


.text-block 
{   color: white;
    margin-top:12px;
}


.btn{
    color: #fff;
    background-color: #C7222A;
    border-color: #C7222A;
}

.mbottom10 {
    margin-bottom: 10px;
}

.h170 {
    height: 170px;
}

@media all and (max-width: 768px) {
canvas {
max-width: 100%;
height: auto;
}
#myCarousel
{
max-width: 100%;
height: auto; 
}

}



@media all and (max-width: 768px) 
{
.cobrandstrip
{
display: inline-block;
}
}


@media all and (max-width: 460px) 
{
.embed-responsive-item
{
width:100%;
}
.h170 {
    height: 240px;
}

}




@media all and (max-width: 768px) {
	#crestashareicon {
		bottom: 0 !important;
		top: inherit !important;
		left: 0 !important;
		right: inherit !important;
		float: none !important;
		width: 100%;
		margin: 0 !important;
		background: #ffffff;
		text-align: center;
		display: none !important;
	}
	#crestashareicon .sbutton {
		clear: none !important;
		float: none !important;
		display: inline-block !important;
	}
	#crestashareicon .cresta-the-button {
		display: none !important;
	}
	#crestashareicon.cresta-share-icon .sbutton i {
		width: 30px!important;
		height: 30px !important;
		padding: 0 !important;
		line-height: 30px !important;
	}
	#crestashareicon.cresta-share-icon .sbutton, #crestashareicon.cresta-share-icon{
		margin: 3px 1px !important;
	}
}


@media only screen and (min-device-width : 320px) and (max-device-width : 480px) 
{
body
{
font-family:"Open Sans",sans-serif;
}
.alpha a
{
//pointer-events:auto !important;
}

#content_child
{
width:90% !important;
margin:0 5%!important;
}
#content_child > img {
    height: 50px;
}
#content_child > p
{
font-size:12px !important;
}

.resizable:first-child
{
overflow: hidden!important;
}
.resizable 
{
height: auto;
overflow: hidden!important;
}
.paragraph
{
margin:0% 5%!important;
line-height:18px;
font-size:14px!important;
}
.resizable img
{
height: 100%;
width:auto;
}
.headbox , .headbox span
{

width:90%!important;
}
.paragraph ul li
{
margin:0px!important;
padding:0px 10px 10px 0px!important;
line-height:18px!important;
font-size:14px;
}
.paragraph ul li span{line-height:18px!important;}
.paragraph ul
{
    margin: 0px;
   padding: 10px 10px 10px 25px;
}
.footer_branding
{width:100%!important;}
#work {
    width: 97%;
}
.pad-left {
    padding: 0px 10px!important;
}

#sp_mobile_template
{
    width:0px !important;
}
#logo_img
   {
    display:block !important;   
   }
   
   .nav-item a 
   {
    color:#272727 !important;   
   }
}


#logo_img
   {
    display:block !important;   
   }

#search-suggest{float:left;list-style:none;margin-top:-3px;padding:0;width:400px;position: absolute;z-index:1;}
#search-suggest li{padding: 3px 0 0 13px;
    background: #f5f5f5;
    border: 1px solid #23292C;
    border-top: 0px solid #23292C;
    color: #23292C;
    font-size: 14px;
    text-align: left;}
#search-suggest li:hover{background:#C7222A;cursor: pointer;color: #ffffff;}
#search-box{padding: 10px;border: #a8d4b1 1px solid;border-radius:4px;}

li.nav-title {
    display: block;
    text-transform: uppercase;
    font-size: 22px;
    color: #ffffff;
    padding: 15px 0 0;
    min-width: 238px;
    margin-top: -53px;
    font-weight:bold;
}

  </style>
  
  
  
  <div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=1614773665402559";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

  </head>

  <body>
  <div id="crestashareicon" class="cresta-share-icon">
        <div class="sbutton"><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url1; ?>" class="facebook"><i class="fa fa-facebook"></i></a></div>
          <div class="sbutton"><a href="https://twitter.com/intent/tweet?url=<?php echo $url1; ?>" class="twitter"><i class="fa fa-twitter"></i></a> </div>
          <div class="sbutton"><a href="https://plus.google.com/share?url=<?php echo $url1; ?>" class="google"><i class="fa fa-google"></i></a> </div>
          <div class="sbutton"><a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url1; ?>" class="linkedin"><i class="fa fa-linkedin"></i></a></div>
          <div class="sbutton"><a href="#" class="youtube"><i class="fa fa-youtube"></i></a></div> 
   </div>       
      
      

  <header id="main-header" class="white-transparent ng-scope menu-sticky">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <nav class="navbar navbar-expand-lg navbar-light">
                   <a class="navbar-brand" href="">
                        <?php if($company_logo!='') {  ?>
                        <img src="<?php echo $weburl; ?>/company_logo/<?php echo $company_logo; ?>" id="logo_img" class="img-fluid" alt="">
                        <?php } else { ?>
                        <img src="<?php echo $weburl; ?>/company_logo/0rI9SP11374.png" id="logo_img" class="img-fluid" alt="">
                        <?php 
                        } 
                        ?>
                    </a>
                   <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                       <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto w-100 justify-content-end">
                            <li class="nav-item">
                                <a class="nav-link active" href="<?php echo $weburl; ?>">Home</a>
                            </li>
                             <li class="nav-item">
                                <a class="nav-link" href="#sp-about">About Us</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#sp-blog">Content</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#sp-contact">Contact</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>
  </header>
  
 
  
  
     
  <div class="container-fluid" style="padding-top: 120px;">
  <div class="row content">
    <div class="container">
     <div class="col-md-12">
      <div class="row">

<div class="col-md-8">
        
       
         
          <h5 class="mt-4" style="margin-left: 16px;"><strong><?php echo ucfirst($caseStudyActualtitle); ?></strong></h5>
          
          <?php

    if($buttonProprty==1)
    { 
    
    if($row_content_preview['VideoId']!='0')
    { 
    ?>
    
     <button type="button" class="btn pull-right" data-toggle="modal" data-target="#showcase_page" style="margin-top:-35px;"><?php echo $btnLbl; ?></button>
 
  <?php 
    } 
   else 
   {  
   ?>
    
    <button type="button" class="btn pull-right" data-toggle="modal" data-target="#showcase_page" style="margin-top:-35px;"><?php echo $btnLbl; ?></button>
     <?php 
     } 
     } 
     else if($buttonProprty==2 or $buttonProprty==3) 
     {  
     ?>
 
     <button type="button" class="btn pull-right" data-toggle="modal" data-target="#showcase_page" style="margin-top:-35px;"><?php echo $btnLbl; ?></button>
      
     <?php 
     } 
     ?> 
          
          
          

         
          <hr>
         
          <p style="margin-left: 16px;font-size: 14px;">Posted on <?php echo $entrydate; ?></p>

         
          
<?php if($cs_mode=="library" && $cs_mode!="video")
{ 
       
?>
<div  id="sp_mobile_template" style="width:700px;"></div>	
<?php
}
else if($cs_mode!="library" && $cs_mode!="video")
{   
   $contentCheck=(explode('.', $row_content_preview['content_file']));
   
   $showcasepdfImage=(explode('|', $row_content_preview['showcasepdfImage']));
   $content_file=$row_content_preview['content_file']; 
if(strtolower($contentCheck[1])=="pdf") 
{
?>


<div id="myCarousel" class="carousel slide" data-ride="carousel" style="width: 700px;margin: 0 auto">
 
  <div class="carousel-inner">
    <?php 
      $i=0;
    foreach($showcasepdfImage as $getshowcasepdfImage) 
    { ?>
    <div class="item <?php if($i==0) { echo "active"; } ?>">
        <?php if($pcmember_pc_type=='C')
        {
        ?>
        <img src="<?php echo $weburl.'/'.'upload/casestudy/'.$p_client_id.'/'.$getshowcasepdfImage; ?>" style="width:700px;" class="img-responsive">
        <?php 
        }
        else
        {
        ?>
        <img src="<?php echo $weburl.'/'.'upload/casestudy/'.$c_lient_Id.'/'.$getshowcasepdfImage; ?>" style="width:700px;" class="img-responsive">
        <?php
        }
        ?>
      </div>  
      
   <?php 
   $i++;
   } 
   ?>
   
   
  
  </div>

 
  <a class="left carousel-control" href="#myCarousel" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#myCarousel" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right"></span>
    <span class="sr-only">Next</span>
  </a>
</div>


<?php 
}
else
{
if($pcmember_pc_type=='C')
{
if($row_content_preview['cobrand']==1)
{
include("manager/sp-imagecobrand.php");
imgcobrandListener($template_id,$c_lient_Id); 

?>
<img style="width:700px;" class="img-responsive" src="<?php echo $weburl; ?>/upload/casestudy/<?php echo $c_lient_Id; ?>/<?php echo $content_file; ?>">
<?php    
}
else
{
?>
<img style="width:700px;" class="img-responsive" src="<?php echo $weburl; ?>/upload/casestudy/<?php echo $p_client_id; ?>/<?php echo $content_file; ?>">
<?php
}
}
else
{
?>
<img style="width:700px;" class="img-responsive" src="<?php echo $weburl; ?>/upload/casestudy/<?php echo $c_lient_Id; ?>/<?php echo $content_file; ?>">
<?php    
}
}
?>
       
<?php
} 
else if($cs_mode!="library" && $cs_mode=="video")
{

   if(!empty($row_content_preview['video_file']))
   {
    $vurl=explode('/',$row_content_preview['video_file']);
		                                         $urlVideo=end($vurl);
                                                         
                                                         
                                                         if(preg_match('/watch/',$urlVideo)) 
                                                          {
                                                          $videopath=explode('watch?v=',$urlVideo);       
                                                          $urlVideo1=$videopath[1];
                                                          }
                                                          else 
                                                           { 
                                                         $urlVideo1= $urlVideo;
                                                           }

?>

<iframe class="embed-responsive-item" width="700px" height="500px" src="http://www.youtube.com/embed/<?php echo $urlVideo1; ?>?rel=0" frameborder="0" allowfullscreen></iframe>


<?php 
} 
else if(!empty($row_content_preview['VideoId']) && $row_content_preview['cobrand']==1) 
{
                    
                         
                         if($pcmember_pc_type=='C')
                         {
                         $videocount="select * from sp_video where client_id='".$p_client_id."' and VideoId='".$row_content_preview['VideoId']."'"; 
                         $videocountget=mysql_query($videocount) or die(mysql_error());
                         $videocountset=mysql_fetch_array($videocountget);
                         
                         $videochild="select * from sp_video where client_id='".$c_lient_Id."' and VideoId='".$row_content_preview['VideoId']."'";
                         $videochildget=mysql_query($videochild) or die(mysql_error());
                         $videochildset=mysql_fetch_array($videochildget);
                         $videoURL=$videochildset['DownloadVideoLink'];
                         $videocount=mysql_num_rows($videochildget);
                         if($videocount==0)
                         {
                             
                           $clienttrim=substr($c_lient_Id,2);
                            $pmemqry1="SELECT * FROM sp_members where client_id='".$c_lient_Id."' and valid=1 and deleted=0"; 
                            
                            $pmem_ftch=mysql_query($pmemqry1);
                            $row_pmem=mysql_fetch_array($pmem_ftch);
                            $companyId=$row_pmem['comp_id'];
                            
                            
                            $smemqry1="SELECT * FROM sp_sub_members where c_client_id= '".$c_lient_Id."' and valid=1 and deleted=0"; 
                            $smem_ftch=mysql_query($smemqry1);
                            $row_smem=mysql_fetch_array($smem_ftch);
                            
                            
                            $qry="select * from sp_company where comp_id='".$companyId."'"; 
                            $res=mysql_query($qry) or die(mysql_error());  
                            $cdata=mysql_fetch_array($res);
                           
                           $videochild_response="insert into sp_video set VideoId='".$videocountset['VideoId']."',
                                        							 VideoTitle='".mysql_real_escape_string($videocountset['VideoTitle'])."',
                                        							 VideoCategoryName ='".mysql_real_escape_string($videocountset['VideoCategoryName'])."',
                                                                     CreateorDownload='".mysql_real_escape_string($videocountset['CreateorDownload'])."',
                                        							 DownloadVideoLink='".mysql_real_escape_string($videocountset['DownloadVideoLink'])."',
                                        							 ThumbnailImage='".mysql_real_escape_string($videocountset['ThumbnailImage'])."',
                                        							 client_id='".mysql_real_escape_string($c_lient_Id)."',
                                        							 status='".mysql_real_escape_string($videocountset['Status'])."'";
                                        	   
                                        	                         mysql_query($videochild_response) or die(mysql_error());     
                             
                          $videoName1=urlencode($row_pmem['first_name'].' '.$row_pmem['last_name']);
                          
                           $arntrim=substr($row_smem['urn_no'],4);
                          
                                if(!empty($row_pmem['person_contact1'])) 
                                {
                                  $numlength = strlen($row_pmem['person_contact1']);
                                  
                                  if($numlength=='13' || $numlength=='12')
                                  {
                                   $mbletrim=substr($row_pmem['person_contact1'],3);      
                                   $videoMobile=$mbletrim;   
                                  }
                                  else if($numlength=='10')
                                  {
                                    $videoMobile=$row_pmem['person_contact1'];  
                                  }
                                } 
                                else 
                                { 
                                 $videoMobile="99999999"; 
                                }
	
                               $compLogo=$cdata['header_logo'];
                               $compLogoExp=explode('.', $compLogo);
	                          if($compLogo!='')
	                           {
		                       $logodisplay=$weburl.'/'.company_logo.'/'.$compLogo;
	                           }
	                           else
	                           {
	                            $logodisplay="http://hdfcsystest.salespanda.com/manager/images/hdfcmutualfund.jpg";  
	                           }
                         
                         $video_create= file_get_contents('http://hdfcmfpartners.anchoredge.in/api/VideoAPI/CreateVideo/?Clientid='.$clienttrim.'&Videoid='.$row_content_preview['VideoId'].'&Displayname='.$videoName1.'&Displayemailid='.$row_pmem['person_email'].'&Displaymobileno='.$videoMobile.'&Website='.$lData['company_website'].'&Emailid='.$row_pmem['person_email'].'&Colorcodefordisplayname=&Colorcodefordisplaymobileno=&Logodisplay=Enable&BottomDisplay&DisplaymobilenoInSlide=True&DisplayemailidInSlide=True&Logourl='.$logodisplay.'&LogoShape=Rectangle&Arnno='.$arntrim.'');    
                          $videoCreate = json_decode($video_create , true); 
                         
                          //mysql_query("insert into users set facebook_id='1',facebook_access_token='".mysql_real_escape_string($videoCreate)."'");
                             if($videoCreate=="success" || $videoCreate="Video is already created first call delete API")
                             {
                             
                             $video_arr = array(); 
                             $video_json = file_get_contents('http://hdfcmfpartners.anchoredge.in/api/VideoAPI/GetAllVideos/?ClientId='.$clienttrim.'&EmailId='.$row_pmem['person_email'].'');
                             $video_arr = json_decode($video_json, true);
                             
                             
                             for($i=0; $i < sizeof($video_arr); $i++)
                              {   
                                  
                                  if($video_arr[$i]['CreateorDownload']=="Created" && $video_arr[$i]['VideoId']==$row_content_preview['VideoId'])
                                  {
                                 $video_response="update sp_video set 
								 CreateorDownload='".mysql_real_escape_string($video_arr[$i]['CreateorDownload'])."',
								 DownloadVideoLink='".mysql_real_escape_string($video_arr[$i]['DownloadVideoLink'])."',
								 status='".mysql_real_escape_string($video_arr[$i]['Status'])."' where VideoId='".$row_content_preview['VideoId']."' and  client_id='".mysql_real_escape_string($c_lient_Id)."'";
	                             mysql_query($video_response) or die(mysql_error()); 
	                             
	                             unset($video_arr);
	                             //sleep(1);
                                  }
                         
                              }
                              
                            }  
                             
                            $page = 'http://'.$data_analytic['cms_subdomain_url'].$_SERVER['REQUEST_URI'];
                            $sec = "1";
                            header("Refresh: $sec; url=$page");  
                          
		                   } 
                         }
                         else
                         {
                           $videocount="select * from sp_video where client_id='".$c_lient_Id."' and VideoId='".$row_content_preview['VideoId']."'"; 
                            $videocountget=mysql_query($videocount) or die(mysql_error());
                           $videocountset=mysql_fetch_array($videocountget);
                           $videoURL=$videocountset['DownloadVideoLink'];
                         }
 
 
 
 ?>

<video width="100%" height="100%" controls><source src="<?php echo $videoURL; ?>" type="video/mp4"></video>
     
<?php } } if($pcmember_pc_type=='C')
{
if($row_content_preview['cobrand']==1)
{
    ?>
        <div class="text-block text-center">
        
         <span class="cobrandstrip" style="color:#414042;font-size:14px;">For more information contact:</span></br>
         <span class="cobrandstrip" style="color:#414042;font-size:14px;font-weight:bold;padding: 12px;"><i class="fa fa-user" style="color:#414042;font-size:18px;padding: 12px;" aria-hidden="true"></i><?php echo $distri_name; ?></span>
         <span class="cobrandstrip" style="color:#414042;font-size:14px;font-weight:bold;padding: 12px;"><i class="fa fa-envelope" style="color:#414042;font-size:18px;padding: 12px;" aria-hidden="true"></i><?php echo $distri_email; ?></span>
         <span class="cobrandstrip" style="color:#414042;font-size:14px;font-weight:bold;padding: 12px;"><i class="fa fa-phone" style="color:#414042;font-size:18px;padding: 12px;" aria-hidden="true"></i><?php echo $distri_contact; ?></span>
        
        </div>
         <?php } } ?> 
          
          <div class="card my-4">
            <h5 class="card-header">Leave a Comment:</h5>
            <div class="card-body">
              <div class="fb-comments" data-href="<?php echo $url1; ?>" data-numposts="5" data-colorscheme="light" data-width="100%"></div>
            </div>
          </div>
          
          

        </div>

    <div class="col-md-4">

          <div class="card my-4">
            <h5 class="card-header">Search</h5>
            <div class="card-body">
              <div class="input-group" style="width: 85%;">
                <input type="text" id="showcsearch-name" class="form-control" autocomplete="off" placeholder="Search for Content" onkeyup="ShowcaseSearchKeyup();">
              
              </div>
              <div id="showsrch" style="display:none;float: right;margin-right: 354px;margin-top: 13px;"></div>
            </div>
          </div>

         
          <div class="card my-4">
            <h5 class="card-header">Similar Content</h5>
            <div class="card-body">
              
	      <?php include("cs-sidebar-hdfc.php"); ?>
  
            </div>
          </div>

        </div>
    </div>
    </div>
     

    </div>
   </div>
  </div>    
   
   

   
<div ui-view="footer" class="ng-scope">
<footer class="dark-bg ng-scope">
    <div class="sp-footer sp-pt-70 sp-pb-20">
        <div class="container">
            <div class="row overview-block-ptb2">
                <div class="col-lg-6 col-md-12 sp-mtb-20">
                    <div class="logo">
                        
                        <div class="sp-font-white sp-mt-15 sp-mr-60">
                       
                      <?php if($QrySelectget['microsite_about']!='') { echo $QrySelectget['microsite_about']; } else { 
                      
                      echo $pcomp_row['first_name'].' '.$pcomp_row['last_name']." is a distributor of HDFC Mutual Fund. HDFC Mutual Fund has been constituted as a trust in accordance with the provisions of the Indian Trusts Act, 1882, as per the terms of the trust deed dated June 8, 2000 with Housing Development Finance Corporation Limited (HDFC) and Standard Life Investments Limited as the Sponsors / Settlors and HDFC Trustee Company Limited, as the Trustee. The Trust Deed has been registered under the Indian Registration Act, 1908. The Mutual Fund has been registered with SEBI, under registration code MF/044/00/6 on June 30, 2000.";
                      
                      }?> 
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 sp-mtb-20">
                    <ul class="menu">
                        <li><a href="<?php echo $weburl; ?>">Home</a></li>
                        <li><a href="#sp-blog">Content</a></li>
                        <li><a href="#sp-about">About Us</a></li>
                        <li><a href="#sp-contact">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 sp-mtb-20">
                    <div class="contact-bg">
                        <ul class="sp-contact">
                            <li>
                                <i class="fa fa-map-marker"></i>
                                 <?php if($QrySelectget['microsite_address']!='') { ?>
                                <p><?php echo $QrySelectget['microsite_address']; ?></p>
                                <?php } else { ?>
                                 <p>HDFC House, 2nd Floor, H. T. Parekh Marg, 165-166, Backbay Reclamation, Churchgate, Mumbai - 400020.</p>
                                <?php } ?>
                            </li>
                            <li>
                                <i class="fa fa-phone"></i>
                                <p><?php echo $pcomp_row['person_contact1']; ?></p>
                            </li>
                            <li>
                                <i class="fa fa-envelope"></i>
                                <p>&nbsp;<?php echo $pcomp_row['person_email']; ?></p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright-box">
        <div class="container">
            <div class="row flex-row-reverse">
                <div class="col-md-6 col-sm-12 text-right">
                    <ul class="info-share">
                        <li><a href=""><i class="fa fa-twitter"></i></a></li>
                        <li><a href=""><i class="fa fa-facebook"></i></a></li>
                        <li><a href=""><i class="fa fa-google"></i></a></li>
                        <li><a href=""><i class="fa fa-linkedin"></i></a></li>
                    </ul>
                </div>
                <div class="col-md-6 col-sm-12 text-left align-self-center">
                </div>
            </div>
        </div>
    </div>
</footer>

</div>

  

<!---------------------------------------Start Download popup ----------------------------->


<div id="showcase_page" class="modal fade" role="dialog" style="margin-top:100px;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" style="font-size:20px;font-weight:bold;"><strong>Please fill the form below to begin.</strong></h4>
				</div>
                                <div class="modal-body h170">	
								<div class="portlet-body form">
								<form class="page-name" method="post" class="form-horizontal" id="frmcmp"  method="post" action="../add-lead-proces.php" enctype="multipart/form-data" onsubmit="return checkfrm();">
								    <div class="form-body">
									  
									  <div class="form-group">										
											<div class="col-md-6 mbottom10">											
											<input name="fname" type="text" value="<?php echo $mfName; ?>" placeholder="First Name" class="form-control"/>
										    </div>
									  </div>


                                    <div class="form-group">										
									<div class="col-md-6 mbottom10">								
											<input name="lname" type="text" value="<?php echo $mlName; ?>" placeholder="Last Name" class="form-control"/>
										    </div>
									  </div>


                                      <div class="form-group">										
											<div class="col-md-6 mbottom10">											
											<input name="email" id="scpemail" type="text" value="<?php echo $mofficialEMail; ?>" placeholder="Email Address*" class="form-control" required />
										    </div>
									  </div> 

                                       <div class="form-group">										
											<div class="col-md-6 mbottom10">											
											<input name="phone" type="text" value="<?php echo $mPhone; ?>" placeholder="Phone Number*" class="form-control" required />
										    </div>
									  </div> 


                                      <div class="form-group">										
											<div class="col-md-6 mbottom10">											
											<input name="city" id="city" value="<?php echo $mCompname; ?>" type="text" placeholder="City" class="form-control"/>
										    </div>
									  </div>      

                                            <input name="source" type="hidden" value="Document Popup" id="source" />
                                            <input name="caseids" type="hidden" value="<?php echo $caseId; ?>" id="caseids" />
								            <input name="channel_type" type="hidden" value="<?php echo $channel_type ?>" id="channel_type" /> 
                                            <input name="ref_url" type="hidden" value="<?php echo $ref_url ?>"  />     
                                                                       
									</div>
								  
								</div>														
						
					
				</div>
				<div class="modal-footer">
				    <input name="add" type="submit" value="Submit" id="showcase-subtn" class="btn btn-primary showcase-btn" />					
					<button type="button" class="btn btn-primary showcase-btn" data-dismiss="modal">Close</button>
                    					
				</div>
                            </form>
			</div>
		</div>
	</div>


<!-------------------------------------End Download popup-------------------------------------------------------------------->

  

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script>var microsite=1;</script>
<?php include("includes/footer-event.php"); ?>
<script>
$(document).ready(function(){
 
  $("a[href='#sp-contact'],a[href='#sp-about'],a[href='#sp-blog'],a[href='#sp-banner'],a[href='#categories']").on('click', function(event) {
   if (this.hash !== "") 
    {
     event.preventDefault();
      var hash = this.hash;
       $('html, body').animate({
        scrollTop: $(hash).offset().top
      }, 900, function(){
   
       window.location.hash = hash;
      });
    } 
  });
});
  
  
 function ShowcaseSearchKeyup() 
	  {
	     var srch=jQuery.noConflict(); 
		 var showsearchName=srch("#showcsearch-name").val();
        var c_lient_Id='<?php echo $c_lient_Id; ?>';
        var p_client_id='<?php echo $p_client_id; ?>';
        var pcType='<?php echo $pcmember_pc_type; ?>';

		srch.ajax({
		type: "POST",
		url: "<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>/get_document_title_list.php",
		data: {showsearchName: showsearchName,c_lient_Id: c_lient_Id,p_client_id: p_client_id,pcType: pcType},
		beforeSend: function(){
		   srch("#showcsearch-name").css("background","#FFF url(images/LoaderIcon.gif) no-repeat 240px");
		   
		  },
		success: function(data)
		{
		 srch("#showsrch").html(data).show();
		 srch("#showcsearch-name").css("background","#FFF");	
		}
		});
    }
    

    
function srchshowcaseClick(val1) 
{
var srchclick=jQuery.noConflict();     
srchclick("#showcsearch-name").val(val1)
window.location.href = '<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>/showcase/'+window.encodeURIComponent(val1)+''; 
}
 
  
var xSeconds = 5000;
$("#showcase-subtn").click(function() 
{
setTimeout(function() {
   
   $('#showcase_page').modal('hide');
   
}, xSeconds);
});


setTimeout(function() 
{
   $('#successmsg').hide();
}, 5000);


function Videodownload() 
{
   videoURL='<?php echo $videoURL; ?>';
 
    bootbox.confirm("The videos are being provided only for promotion of schemes in your capacity as a distributor empanelled with HDFC Asset Management Co. Ltd. The copyright on the materials continues to vest with HDFC Asset Management Co. Ltd. There shall be no misuse / tampering of the videos by the distributor.", function(result) {
  	if(result == true)
  	    {
  	    $('<a/>',{
     "href":videoURL,
    "download":"video.mp4",
    id:"videoDownloadLink"
  }).appendTo(document.body);
  
  $('#videoDownloadLink').get(0).click().remove();
  	    
  	    }
   });
	
}

$(document).ready(function() {
var temp_id='<?php echo $template_id; ?>';
var clientId= '<?php echo $c_lient_Id; ?>';
var screen_width= $(window).width();
var cobrandExp= parseInt('<?php echo $cobrandExp; ?>');
$("#sp_mobile_template").html('');
$.ajax({url:"<?php echo $weburl; ?>/sp-mobile-template.php",
        type: "post",
        data: {screen_width:screen_width,temp_id:temp_id,clientId:clientId,cobrandExp: cobrandExp}, 
        cache: false,
         success:function(result){
         $("#sp_mobile_template").html(result);
        
    }
});

}); 

</script>
  
  </body>

</html>
