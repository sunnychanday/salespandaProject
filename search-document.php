<?php 
	include("includes/global.php");
	include("includes/check_login.php");
	include("includes/connect.php");
	include("includes/function.php");
	include("includes/global-url.php");
	//echo $defaultwebPath;
        	
	if($c_lient_Id=='')
	{
		$c_lient_Id=$clientId;
	}
	
	 if($c_lient_Id!=SP108098)
	 {
	    $tblank="target='_blank'";
	 }    
        

        $pc_member_info = getPCMemberInfo($c_lient_Id);
        $pcmember_pc_type = $pc_member_info['member_pc_type'];
        $p_client_id = $pc_member_info['p_client_id']; 

        if($pcmember_pc_type=='C'){ 
           $pcompqry = "select pid,comp_id from sp_members where client_id ='".$p_client_id ."' and valid=1 and deleted=0 and approve=1";
        }
        else{
           $pcompqry = "select pid,comp_id from sp_members where client_id ='".$c_lient_Id."' and valid=1 and deleted=0 and approve=1";
        }

        $pcompres  = mysql_query($pcompqry) or die(mysql_error());  
	$pcomp_row = mysql_fetch_array($pcompres);          
        $p_comp_id = $pcomp_row['comp_id'];

        if($pcmember_pc_type=='C'){ 
           $sql_cms_subdomain = mysql_query("select cms_subdomain_url from sp_subdomain where client_id='".$p_client_id."'");
	       $data_cms_subdomain = mysql_fetch_array($sql_cms_subdomain);
           $pcmsPath = $data_cms_subdomain['cms_subdomain_url'];
        }
 
        $sql_analytic = mysql_query("select google_analytics,google_webmaster,userid from sp_subdomain where client_id='".$c_lient_Id."' and valid=1 and deleted=0");
        $data_analytic = mysql_fetch_array($sql_analytic);
	    $puserid = $data_analytic['userid'];
        
        if($pcmember_pc_type=='C'){
           $qry2="select favicon,company_name from sp_company where comp_id='".$c_comp_id."'";
           $res2=mysql_query($qry2) or die(mysql_error());  
           $lData2=mysql_fetch_array($res2);
           $companyId=$c_comp_id;
        }
        else{
           $companyId=$p_comp_id;
        }

        $qry="select * from sp_company where comp_id='".$p_comp_id."'"; 
        $res=mysql_query($qry) or die(mysql_error());  
        $lData=mysql_fetch_array($res);
        $industryLabel=$lData['target_vertical_level'];

        if($pcmember_pc_type=='C'){
           $faviconimg=$lData2['favicon'];
           $company_name=$lData2['company_name'];
        }
        else{
           $faviconimg=$lData['favicon'];
           $company_name=$lData['company_name'];
        }   
                

        if($pcmember_pc_type=='C'){
	    $hashQuery="select label from sp_hashtag where valid=1 and deleted=0 and client_id='".$p_client_id."'";
        }
        else{
            $hashQuery="select label from sp_hashtag where valid=1 and deleted=0 and client_id='".$c_lient_Id."'";
        } 

	$hashres=mysql_query($hashQuery) or die("Error in category table".mysql_error());
	$hashcountCat=mysql_num_rows($hashres);
	$exthashsol=mysql_fetch_array($hashres);
	$labelName=$exthashsol['label'];	
	
	
	if($companyId!='')
	{
	    $cqry="select company_name,company_website from sp_company where comp_id='".$companyId."'";
	    $resc=mysql_query($cqry) or die(mysql_error());
	    $compData=mysql_fetch_array($resc);
	    $companyName=$compData['company_name'];
	    $companyWebsite=$compData['company_website'];	
	}
	
	if($_REQUEST['content']!='')
	{ 
	    $content=$_REQUEST['content'];
	    $contentId=$content;
	}
	if($_REQUEST['solution']!='')
	{
	    $solution=$_REQUEST['solution'];
	    $solution=$solution;
	}
	
	$urlval=$_SERVER['REQUEST_URI']; 
	$urlvalexp=explode("/",$urlval);
	$url_catg=$urlvalexp[2];
        $browse_type = $urlvalexp[3]; 
        
        
        if($browse_type=='S'){
          $_SESSION['solName'] = $url_catg;          
        }        
        else if($browse_type=='C'){
          $_SESSION['contName'] = $url_catg;          
        }
        
        if($url_catg=='' && $_SESSION['solName']=='' && $_SESSION['contName']=='' ){  
           header("location:http://".$_SERVER['HTTP_HOST']); exit; 
        }
        
        
	$catgSName=str_replace("-"," ", $_SESSION['solName']);
        $catgCName=str_replace("-"," ", $_SESSION['contName']);	

        if($pcmember_pc_type=='C'){ 
           $c_lient_Id = $p_client_id;
        }

        if($catgSName!=''){        
            

               $ctquery=mysql_query("select id from sp_category where it_type = '".$catgSName."' and valid=1 and deleted=0 and (client_id='".$c_lient_Id."')") or die(mysql_error());
		$count1=mysql_num_rows($ctquery);
		if($count1==0)
		{
		$ctquery=mysql_query("select id from sp_category where it_type = '".$catgSName."' and valid=1 and deleted=0 and (client_id='".$c_lient_Id."' or client_id='SP_INTERAL')") or die(mysql_error());
		}
		
		$ctrowdata=mysql_fetch_array($ctquery);
		'<br>SID= '.$solution=$ctrowdata['id']; 

        }
        
        if($catgCName!=''){
         
            $ctquery=mysql_query("select id from sp_article_type where article_type ='".$catgCName."' and valid=1 and deleted=0 and (client_id='".$c_lient_Id."' or client_id='SP_INTERAL')") or die(mysql_error());
	    $ctrowdata=mysql_fetch_array($ctquery);
	    '<br>CID= '.$content=$ctrowdata['id']; 
 
        }
         
        

	/*if($catgName!='')
	{
		$ctquery=mysql_query("select id from sp_category where it_type = '".$catgName."' and valid=1 and deleted=0 and (client_id='".$c_lient_Id."')") or die(mysql_error());
		$count1=mysql_num_rows($ctquery);
		if($count1==0)
		{
		$ctquery=mysql_query("select id from sp_category where it_type = '".$catgName."' and valid=1 and deleted=0 and (client_id='".$c_lient_Id."' or client_id='SP_INTERAL')") or die(mysql_error());
		}
		
		$ctrowdata=mysql_fetch_array($ctquery);
		'<br>CID= '.$solution=$ctrowdata['id']; 
		 
		 if($solution=='')
		 {
			$ctquery=mysql_query("select id from sp_article_type where article_type ='".$catgName."' and valid=1 and deleted=0 and (client_id='".$c_lient_Id."' or client_id='SP_INTERAL')") or die(mysql_error());
			$ctrowdata=mysql_fetch_array($ctquery);
			$content=$ctrowdata['id'];
		}
	}*/
	
	
	if($solution!='' && $content=='')
	{ 
		$solution=checkInjection($solution,'id');
		if($solution!='')
		{

                         if($pcmember_pc_type=='C'){
                             
                            $cjkcatseo = "select DISTINCT(CS.content_type) from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".$c_lient_Id."' and CS.valid=1 and CS.deleted=0 and TS.approve=1 and FIND_IN_SET('".$solution."',CS.category)";
                            
                            $cjkcatseo2 = "select DISTINCT(content_type) from sp_child_case_study where client_id='".$c_lient_Id."' and valid=1 and deleted=0 and approve=1 and FIND_IN_SET('".$solution."',category)";
 
                         }
                         else{ 
			    $cjkcatseo = "select DISTINCT(content_type) from sp_case_study where client_id='".$c_lient_Id."' and valid=1 and deleted=0 and approve=1 and FIND_IN_SET('".$solution."',category)";
                         
                         }  

			$filterFlag='s';
		}
		else
		{
			//header("location:$defaultwebPath"); exit;
		}
	}
	
	 if($content!='' && $solution=='')
	 { 
		$content=checkInjection($content,'id');
		if($content!='')
		{
                         if($pcmember_pc_type=='C'){                             
                            $cjkcatseo = "select DISTINCT(CS.content_type) from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".$c_lient_Id."' and CS.valid=1 and CS.deleted=0 and TS.approve=1 and FIND_IN_SET('".$content."',CS.content_type)"; 
                            
                            $cjkcatseo2 = "select DISTINCT(content_type) from sp_child_case_study where client_id='".$c_lient_Id."' and valid=1 and deleted=0 and approve=1 and FIND_IN_SET('".$content."',content_type)";
                         }
                         else{
			     $cjkcatseo = "select DISTINCT(content_type) from sp_case_study where client_id='".$c_lient_Id."' and valid=1 and deleted=0 and approve=1 and FIND_IN_SET('".$content."',content_type)";
                         }

			$filterFlag='c';
		}
		else
		{
			//header("location:$defaultwebPath"); exit;
		}
	 }
	 if($content!='' && $solution!='')
	 {
		$content=checkInjection($content,'id');
		$solution=checkInjection($solution,'id');
		
                if($pcmember_pc_type=='C'){                             
                    $cjkcatseo = "select DISTINCT CS.content_type, CS.category, TS.id as syndid from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".$c_lient_Id."' and CS.valid=1 and CS.deleted=0 and TS.approve=1 and (FIND_IN_SET('".$content."', CS.content_type) and FIND_IN_SET('".$solution."', CS.category))"; 
                    
                    $cjkcatseo2 = "select DISTINCT content_type,category from sp_child_case_study where client_id='".$c_lient_Id."' and valid=1 and deleted=0 and approve=1 and (FIND_IN_SET('".$content."',content_type) and FIND_IN_SET('".$solution."',category))"; 
                }
                else{
		    $cjkcatseo = "select DISTINCT content_type,category from sp_case_study where client_id='".$c_lient_Id."' and valid=1 and deleted=0 and approve=1 and (FIND_IN_SET('".$content."',content_type) and FIND_IN_SET('".$solution."',category))"; 
                }

		$filterFlag='sc';
		
	 }
	 else if($content=='' && $_REQUEST['solution']=='')
	 {
		 //header("location:$defaultwebPath"); exit;
	 }
	
	$reschkseo = mysql_query($cjkcatseo) or die(mysql_error());
	$rescscount = mysql_num_rows($reschkseo);
	if($rescscount!=0)
	{
		while($catCountcstseo=mysql_fetch_array($reschkseo))
		{
		 	$csTypeIdseo.= getarticleName($catCountcstseo['content_type']).',';
		}
		$casestudytype=substr($csTypeIdseo,0,-1); 
		
	}
	else
	{
		//header("location:$defaultwebPath"); exit;
	}


$pcnmemqry = mysql_query("select pid,member_pc_type from sp_members where pid ='".$puserid."' and valid=1 and deleted=0");
$data_memqry = mysql_fetch_array($pcnmemqry);
 
?>
<!DOCTYPE HTML>
<!-- DON'T TOUCH THIS SECTION -->
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
<!--[if lt IE 9]>
<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<title><?php if($solution!=''){ echo categoryName($solution)." | "; } if($content!='') {echo getarticleName($content)." | "; } ?><?php echo $company_name; ?></title>
<meta name="description" content="<?php echo categoryName($solution); ?>: <?php echo $labelName; ?> from <?php echo $companyName; ?>"/>
<meta name="keywords" content="<?php if($solution!=''){ echo categoryName($solution); ?>,<?php } else if($industry!='') { echo segmentName($industry); ?>,<?php  } else if($product!='') { echo itdetailName($product); ?>,<?php  }?> <?php if($rescscount!=0){ echo $casestudytype;} ?>"/>

<?php if($pcmember_pc_type=='C'){ ?>
   <meta name="robots" content="noindex, follow" />
   <link rel="canonical" href="http://<?php echo $pcmsPath; ?>" />  
<?php } else if($data_memqry['member_pc_type']=='N'){  ?>
   <?php if($_SERVER['HTTP_HOST']=='technochimes.salespanda.com' || $_SERVER['HTTP_HOST']=='newwave.salespanda.com'){ ?> 
        <meta name="robots" content="NOINDEX, NOFOLLOW">
    <?php }else{ ?>
        <meta name="robots" content="INDEX, FOLLOW">
    <?php } ?>
    
  <?php }else{ ?>
  <meta name="robots" content="INDEX,FOLLOW" />
  <link rel="canonical" href="http://<?php echo $urlpath; ?>" />
<?php } ?>

<link href='http://fonts.googleapis.com/css?family=Raleway|Playfair+Display+SC' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="<?php echo $sitepath; ?>css_new/style.css">
<link rel="shortcut icon" href="<?php echo $sitepath; ?>webcontent/favicon_icon/<?php echo $faviconimg; ?>" />
<!-- END OF DON'T TOUCH -->

<?php /*?><script type="text/javascript" src="<?php echo $sitepath; ?>scripts/jquery.form.js"></script> 
<script type="text/javascript" src="<?php echo $sitepath; ?>scripts/scripts.js"></script> <?php */?>
<script type="text/javascript" src="<?php echo $sitepath; ?>js/jquery-1.8.0.js"></script>

<script type="text/javascript">
function srch(){
	var datastring = $("#frm").serialize();	
	var url = "<?php echo $defaultwebPath; ?>/search_showcase.php";	
	//alert(url);
	//alert(datastring);
	$.ajax({		
		type: 'POST',
		url: url,
		data: datastring,		
		dataType: "html",
		cache: false,
		success: function(message) {
			//alert(message);
			$(".result1").html(message);
		}
	});
}

function srchcross(){
	var datastring = $("#frm").serialize();	
	var url = "<?php echo $defaultwebPath; ?>/search_showcase.php";	
	//alert(datastring);
	$.ajax({		
		type: 'POST',
		url: url,
		data: datastring,		
		dataType: "html",
		cache: false,
		success: function(message) {
			//alert(message);
			$(".result1").html(message);
		}
	});
}
$(function() { 
    $("#show-more-segment").click(function(evt) {
    	$(".segment_block").toggle();
		$("#show-less-segment").css('display','');
	    $("#show-more-segment").css('display','none');
    }); 
	
});
$(function() { 
    $("#show-less-segment").click(function(evt) {
    	$(".segment_block").toggle();
		$("#show-more-segment").css('display','');
	    $("#show-less-segment").css('display','none');
    }); 
	
});

$(function() {
    $("#show-more-dtype").click(function(evt) {
    	$(".dtype_block").toggle();
		$("#show-less-dtype").css('display','');
	    $("#show-more-dtype").css('display','none');
    }); 
}); 	

$(function() {  
    $("#show-less-dtype").click(function(evt) {
    	$(".dtype_block").toggle();
		$("#show-more-dtype").css('display','');
	    $("#show-less-dtype").css('display','none');
    });
});

$(function() { 
    $("#show-more-cat").click(function(evt) {
    	$(".cat_block").toggle();
		$("#show-less-cat").css('display','');
	    $("#show-more-cat").css('display','none');
    }); 
	
});
$(function() { 
    $("#show-less-cat").click(function(evt) {
    	$(".cat_block").toggle();
		$("#show-more-cat").css('display','');
	    $("#show-less-cat").css('display','none');
    }); 
});

$(function() { 
    $("#show-more-segment").click(function(evt) {
    	$(".segment_block").toggle();
		$("#show-less-segment").css('display','');
	    $("#show-more-segment").css('display','none');
    }); 
	
});
$(function() { 
    $("#show-less-segment").click(function(evt) {
    	$(".segment_block").toggle();
		$("#show-more-segment").css('display','');
	    $("#show-less-segment").css('display','none');
    }); 
});
</script>

</head>
<!-- END OF DON'T TOUCH -->
<body>
<div class="wrapper main-bg-search">
	<?php include("includes/header-new.php"); ?> 
    <div class="clearfix"></div>
    
    <?php  
	$btitle='';
	$bsubtitle='';
	if($solution!='')
	{
		$demotitle=$solutionLabel;
		$btitle='Solution'; $bsubtitle=categoryName($solution);
	}
    if($industry!='')
	{
		$demotitle=$industryLabel;
		$btitle='Industry'; $bsubtitle=segmentName($industry);
	}
	if($content!='')
	{
		$demotitle=$docTypeLevel;
		$btitle='Product'; $bsubtitle=getarticleName($content);
	}
	
	
	?>
    <section id="cStud" style="padding-top:0px;"> <!-- Work Links Section Start -->
   
   <?php if($_SESSION['contName']=='' || $_SESSION['solName']==''){?>
    
    <div class="filter">
    <?php include("search-left-filter.php");?>
    </div>
   <?php } ?>
 
<div class="result <?php if($_SESSION['contName']!='' && $_SESSION['solName']!=''){ echo "result2"; } ?>">
<?php if($_SESSION['contName']=='' || $_SESSION['solName']==''){?>

<p class="grayLBg padA5 radiusA grayTxt font80 letterSpace"><strong>Currently browsing</strong> <span class="orangeTxt">&gt;</span> <?php echo $demotitle;?> <span class="orangeTxt">&gt;</span> <?php echo $bsubtitle; ?></p>
<?php  } ?>
<div class="result1">
    <?php
			 
	
		if($content!='' && $solution=='')
		{
                      if($pcmember_pc_type=='C'){                             
                            $csqry = "select CS.*, TS.id as syndid from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".$c_lient_Id."' and CS.valid=1 and CS.deleted=0 and TS.approve=1 AND FIND_IN_SET('".trim($content)."', CS.content_type) order by CS.id desc"; 
                            
                            $csqry2 = "select id,member_id,image_thumb1,crop_Image,case_study_title,case_study_actual_title,case_study_desc,content_type,landingpage_status,landingpage_id from sp_child_case_study where client_id='".$c_lient_Id."' and valid=1 and deleted=0 and approve=1 AND FIND_IN_SET('".trim($content)."',content_type) order by id desc";
                      }
                      else{
		 	 '<br><br><br>A7='.$csqry = "select id,member_id,image_thumb1,crop_Image,case_study_title,case_study_actual_title,case_study_desc,content_type,landingpage_status,landingpage_id from sp_case_study where client_id='".$c_lient_Id."' and valid=1 and deleted=0 and approve=1 AND FIND_IN_SET('".trim($content)."',content_type) order by id desc";
                      }
		}
		else if($solution!='' && $content=='')
		{
                     if($pcmember_pc_type=='C'){                             
                            $csqry = "select CS.*, TS.id as syndid from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".$c_lient_Id."' and CS.valid=1 and CS.deleted=0 and TS.approve=1 AND FIND_IN_SET('".trim($solution)."',CS.category) order by CS.id desc"; 
                            
                            $csqry2 = "select id,member_id,image_thumb1,crop_Image,case_study_title,case_study_actual_title,case_study_desc,content_type,landingpage_status,landingpage_id from sp_child_case_study where client_id='".$c_lient_Id."' and valid=1 and deleted=0 and approve=1 AND FIND_IN_SET('".trim($solution)."',category) order by id desc";
                      }
                      else{
		 	 '<br><br><br>A8='.$csqry = "select id,member_id,image_thumb1,crop_Image,case_study_title,case_study_actual_title,case_study_desc,content_type,landingpage_status,landingpage_id from sp_case_study where client_id='".$c_lient_Id."' and valid=1 and deleted=0 and approve=1 AND FIND_IN_SET('".trim($solution)."',category) order by id desc";
                      }
		}
                else if($content!='' && $solution!=''){
                    
                    if($pcmember_pc_type=='C'){                             
                         $csqry = "select CS.*, TS.id as syndid from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".$c_lient_Id."' and CS.valid=1 and CS.deleted=0 and TS.approve=1 AND ( FIND_IN_SET('".trim($solution)."',CS.category) and FIND_IN_SET('".trim($content)."',CS.content_type)) order by CS.id desc"; 
                         
                         $csqry2 = "select id,member_id,image_thumb1,crop_Image,case_study_title,case_study_actual_title,case_study_desc,content_type,category,landingpage_status,landingpage_id from sp_child_case_study where client_id='".$c_lient_Id."' and valid=1 and deleted=0 and approve=1 and ( FIND_IN_SET('".trim($solution)."',category) and FIND_IN_SET('".trim($content)."',content_type)) order by id desc";
                    }
                    else{
                        $csqry = "select id,member_id,image_thumb1,crop_Image,case_study_title,case_study_actual_title,case_study_desc,content_type,category,landingpage_status,landingpage_id from sp_case_study where client_id='".$c_lient_Id."' and valid=1 and deleted=0 and approve=1 and ( FIND_IN_SET('".trim($solution)."',category) and FIND_IN_SET('".trim($content)."',content_type)) order by id desc";
                    }  

                    $csres1 = mysql_query($csqry) or die(mysql_error());                    
                    $cs_total_record=mysql_num_rows($csres1);
                    if($cs_total_record==0){
                      
                      if($pcmember_pc_type=='C'){                             
                            $csqry = "select CS.*, TS.id as syndid from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".$c_lient_Id."' and CS.valid=1 and CS.deleted=0 and TS.approve=1 AND ( FIND_IN_SET('".trim($solution)."',CS.category) OR FIND_IN_SET('".trim($content)."',CS.content_type)) order by CS.id desc"; 
                         
                            $csqry2 = "select id,member_id,image_thumb1,crop_Image,case_study_title,case_study_actual_title,case_study_desc,content_type,category,landingpage_status,landingpage_id from sp_child_case_study where client_id='".$c_lient_Id."' and valid=1 and deleted=0 and approve=1 and ( FIND_IN_SET('".trim($solution)."',category) OR FIND_IN_SET('".trim($content)."',content_type)) order by id desc";
                      }
                      else{
                         $csqry = "select id,member_id,image_thumb1,crop_Image,case_study_title,case_study_actual_title,case_study_desc,content_type,category,landingpage_status,landingpage_id from sp_case_study where client_id='".$c_lient_Id."' and valid=1 and deleted=0 and approve=1 and ( FIND_IN_SET('".trim($solution)."',category) OR FIND_IN_SET('".trim($content)."',content_type)) order by id desc";
                      } 

                       echo '<p class="bold orangeTxt">No search data found for '.$_SESSION['solName'].(($_SESSION['solName']!="" && $_SESSION['contName']!="") ? " and " : "").$_SESSION['contName'].'.However you might be interested in the following information. </p>';

                    }

                }
		else 
		{
                      if($pcmember_pc_type=='C'){                             
                            $csqry = "select CS.*, TS.id as syndid from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".$c_lient_Id."' and CS.valid=1 and CS.deleted=0 and TS.approve=1 order by CS.id desc"; 
                            
                            $csqry2 = "select id,member_id,image_thumb1,crop_Image,case_study_title,case_study_actual_title,case_study_desc,content_type,landingpage_status,landingpage_id from sp_child_case_study where client_id='".$c_lient_Id."' and approve=1 and valid=1 and deleted=0 order by id desc";
                      }
                      else{
		 	                $csqry = "select id,member_id,image_thumb1,crop_Image,case_study_title,case_study_actual_title,case_study_desc,content_type,landingpage_status,landingpage_id from sp_case_study where client_id='".$c_lient_Id."' and approve=1 and valid=1 and deleted=0 order by id desc";
                      }
		}
				
		$csres = mysql_query($csqry) or die(mysql_error());
		$total_record=mysql_num_rows($csres);
		
		$csres2 = mysql_query($csqry2);
		$total_record2=mysql_num_rows($csres2);
		
		if($total_record!=0 || $total_record2!=0)
		{
		    
		   if($pcmember_pc_type=='C'){ 
		        while($caseStudy = mysql_fetch_array($csres2))
			    { 
    				$case_study_id=$caseStudy["id"];
    				$casestudyMember=$caseStudy["member_id"];
    				$thumbimage = $caseStudy['image_thumb1'];
    				$crop_image=$caseStudy['crop_Image'];
                                    $caseLandStatus=$caseStudy['landingpage_status']; 
    			    $caseLandId=$caseStudy['landingpage_id'];
    
    				$caseStudyTitle11 = $caseStudy["case_study_title"];
    				$caseStudyActualTitle = ($caseStudy['case_study_actual_title']!='') ? $caseStudy['case_study_actual_title'] : $caseStudy['case_study_title']; 
                                    
    				$caseStudyTitleLength=strlen($caseStudyActualTitle);
    				if($caseStudyTitleLength > 35)
    				{
    					$case_study_title = substr($caseStudyActualTitle, '0', '35')."...";
    				}
    				else
    				{
    					$case_study_title=$caseStudyActualTitle;
    				}
    				$case_study_desc1=$caseStudy["case_study_desc"];
    				$case_study_desc = substr("$case_study_desc1", '0', '260')."...";
    				
    				$castStudyType =getarticleName($caseStudy['content_type']);
    				
    				
    				$csname =str_replace(' ', '-', $caseStudyTitle11);
    
                    if($caseLandId!=0){
        			    $child_edit_status = getChildSyndLpageEditStatus($c_lient_Id,$caseLandId);
        			}
    
                if($pcmember_pc_type=='C'){
                    
                    if($child_edit_status==1){
                        $cslandquery=mysql_query("select publish_page_id,landingpage_title,publish_page_name,landingpage_desc,page_title_seo,meta_description,approve from sp_child_landingpage_publish where publish_page_id='".$caseLandId."' and client_id='".$c_lient_Id."' ");
                    }
                    else{
    				    $cslandquery=mysql_query("select LS.*, LP.publish_page_id,LP.landingpage_title,LP.publish_page_name,LP.landingpage_desc,LP.page_title_seo,LP.meta_description from sp_landingpage_publish as LP INNER JOIN sp_landingpage_syndication as LS ON LP.publish_page_id = LS.landingpage_id where LP.publish_page_id='".$caseLandId."' and LP.client_id='".$p_client_id."' ");
                    }    
    			}
    			else{
    				$cslandquery=mysql_query("select  publish_page_id,landingpage_title,publish_page_name,landingpage_desc,page_title_seo,meta_description,approve from sp_landingpage_publish where publish_page_id='".$caseLandId."' and client_id='".$c_lient_Id."' ");
    			}
    
                $cslandget=mysql_fetch_array($cslandquery);
                $cslandname=$cslandget['publish_page_name'];
                $cslandApprove= $cslandget['approve'];			
    				
    ?>
          <div class="item <?php if($_SESSION['contName']!='' && $_SESSION['solName']!=''){ echo "item2"; } ?>"> 
          
    			
    <?php if($thumbimage!='' && $crop_image==''){?>
    							<img class="thumb" src="<?php echo $sitepath; ?>webcontent/manager/uploads/thumb_img/<?php echo $thumbimage; ?>" alt="<?php echo $caseStudyTitle11; ?>" />
    <?php } else if($thumbimage=='' && $crop_image!='') {?>
    
                                                       <?php if($caseLandStatus==1 && $cslandApprove==1) {?> 
     							<a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>"><img id='base64image' src='data:image/png;base64,<?php echo $crop_image; ?>' alt="<?php echo $caseStudyTitle11; ?>" /></a>
                                                           <?php }  else { ?>
                                                            <a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>"><img id='base64image' src='data:image/png;base64,<?php echo $crop_image; ?>' alt="<?php echo $caseStudyTitle11; ?>" /></a>
                                                           <?php } ?>
    <?php }  else if($thumbimage!='' && $crop_image!='') {?>
    
                                                       <?php if($caseLandStatus==1 && $cslandApprove==1) {?> 
     							<a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>"><img class="thumb" src="<?php echo $sitepath; ?>webcontent/manager/uploads/thumb_img/<?php echo $thumbimage; ?>" alt="<?php echo $caseStudyTitle11; ?>" /></a>
                                                           <?php }  else { ?>
                                                            <a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>"><img class="thumb" src="<?php echo $sitepath; ?>webcontent/manager/uploads/thumb_img/<?php echo $thumbimage; ?>" alt="<?php echo $caseStudyTitle11; ?>" /></a>
                                                           <?php } ?>
    <?php } else { ?>
    							<img src="<?php echo $sitepath; ?>upload/casestudy/nopic/nopic.jpg" class="thumb" alt="<?php echo $caseStudyTitle11; ?>" />
    <?php } ?>
    			<!-- Image must be 400px by 300px -->
                <div class="itemSlideTxt"><div class="itemH3 b">
                  <?php if($caseLandStatus==1 && $cslandApprove==1) {?> 
                    <a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>"><?php echo ucfirst($case_study_title); ?></a>
                  <?php }  else { ?>
                    <a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>"><?php echo ucfirst($case_study_title); ?></a> 
                  <?php } ?>
                 &nbsp;</div>
                <div class="itemH4 grayLTxt"><?php echo $castStudyType; ?>&nbsp;</div>
                <p><?php echo $case_study_desc; ?></p>
                <?php if($caseLandStatus==1 && $cslandApprove==1) {?> 
                 <div class="alignC itemSlideBtn"><a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>" target="_blank" class="btn whiteTxt padL padR normal" <?php echo $tblank; ?>>Read more &raquo;</a></div>
                
                
                <?php }  else { ?>
               <div class="alignC itemSlideBtn"><a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>" class="btn whiteTxt padL padR normal" <?php echo $tblank; ?> >Read more &raquo;</a></div>               
                 <?php } ?>
                </div>
            </div>
        <?php
		 }
		    
	  }	    
		    
			 while($caseStudy = mysql_fetch_array($csres))
			 {
				$case_study_id=$caseStudy["id"];
				$casestudyMember=$caseStudy["member_id"];
				$thumbimage = $caseStudy['image_thumb1'];
				$crop_image=$caseStudy['crop_Image'];
                                $caseLandStatus=$caseStudy['landingpage_status']; 
			    $caseLandId=$caseStudy['landingpage_id'];

				$caseStudyTitle11 = $caseStudy["case_study_title"];
				$caseStudyActualTitle = ($caseStudy['case_study_actual_title']!='') ? $caseStudy['case_study_actual_title'] : $caseStudy['case_study_title']; 
                                
				$caseStudyTitleLength=strlen($caseStudyActualTitle);
				if($caseStudyTitleLength > 35)
				{
					$case_study_title = substr($caseStudyActualTitle, '0', '35')."...";
				}
				else
				{
					$case_study_title=$caseStudyActualTitle;
				}
				$case_study_desc1=$caseStudy["case_study_desc"];
				$case_study_desc = substr("$case_study_desc1", '0', '260')."...";
				
				$castStudyType =getarticleName($caseStudy['content_type']);
				
				
				$csname =str_replace(' ', '-', $caseStudyTitle11);

                                if($pcmember_pc_type=='C'){
				$cslandquery=mysql_query("select LS.*, LP.publish_page_id,LP.landingpage_title,LP.publish_page_name,LP.landingpage_desc,LP.page_title_seo,LP.meta_description from sp_landingpage_publish as LP INNER JOIN sp_landingpage_syndication as LS ON LP.publish_page_id = LS.landingpage_id where LP.publish_page_id='".$caseLandId."' and LP.client_id='".$p_client_id."' ");
			}
			else{
				$cslandquery=mysql_query("select  publish_page_id,landingpage_title,publish_page_name,landingpage_desc,page_title_seo,meta_description,approve from sp_landingpage_publish
			where publish_page_id='".$caseLandId."' and client_id='".$c_lient_Id."' ");
			}

            $cslandget=mysql_fetch_array($cslandquery);
            $cslandname=$cslandget['publish_page_name'];
            $cslandApprove= $cslandget['approve'];			
				
?>
      <div class="item <?php if($_SESSION['contName']!='' && $_SESSION['solName']!=''){ echo "item2"; } ?>"> 
      
			
<?php if($thumbimage!='' && $crop_image==''){?>
							<img class="thumb" src="<?php echo $sitepath; ?>webcontent/manager/uploads/thumb_img/<?php echo $thumbimage; ?>" alt="<?php echo $caseStudyTitle11; ?>" />
<?php } else if($thumbimage=='' && $crop_image!='') {?>

                                                   <?php if($caseLandStatus==1 && $cslandApprove==1) {?> 
 							<a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>"><img id='base64image' src='data:image/png;base64,<?php echo $crop_image; ?>' alt="<?php echo $caseStudyTitle11; ?>" /></a>
                                                       <?php }  else { ?>
                                                        <a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>"><img id='base64image' src='data:image/png;base64,<?php echo $crop_image; ?>' alt="<?php echo $caseStudyTitle11; ?>" /></a>
                                                       <?php } ?>
<?php }  else if($thumbimage!='' && $crop_image!='') {?>

                                                   <?php if($caseLandStatus==1 && $cslandApprove==1) {?> 
 							<a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>"><img class="thumb" src="<?php echo $sitepath; ?>webcontent/manager/uploads/thumb_img/<?php echo $thumbimage; ?>" alt="<?php echo $caseStudyTitle11; ?>" /></a>
                                                       <?php }  else { ?>
                                                        <a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>"><img class="thumb" src="<?php echo $sitepath; ?>webcontent/manager/uploads/thumb_img/<?php echo $thumbimage; ?>" alt="<?php echo $caseStudyTitle11; ?>" /></a>
                                                       <?php } ?>
<?php } else { ?>
							<img src="<?php echo $sitepath; ?>upload/casestudy/nopic/nopic.jpg" class="thumb" alt="<?php echo $caseStudyTitle11; ?>" />
<?php } ?>
			<!-- Image must be 400px by 300px -->
            <div class="itemSlideTxt"><div class="itemH3 b">
              <?php if($caseLandStatus==1 && $cslandApprove==1) {?> 
                <a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>"><?php echo ucfirst($case_study_title); ?></a>
              <?php }  else { ?>
                <a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>"><?php echo ucfirst($case_study_title); ?></a> 
              <?php } ?>
             &nbsp;</div>
            <div class="itemH4 grayLTxt"><?php echo $castStudyType; ?>&nbsp;</div>
            <p><?php echo $case_study_desc; ?></p>
            <?php if($caseLandStatus==1 && $cslandApprove==1) {?> 
             <div class="alignC itemSlideBtn"><a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>" target="_blank" class="btn whiteTxt padL padR normal" <?php echo $tblank; ?>>Read more &raquo;</a></div>
            
            
            <?php }  else { ?>
           <div class="alignC itemSlideBtn"><a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>" class="btn whiteTxt padL padR normal" <?php echo $tblank; ?> >Read more &raquo;</a></div>               
             <?php } ?>
            </div>
        </div>
        <?php
		 }
	}
	else
	{
?>
<p class="bold orangeTxt" style="margin-bottom:230px;" >No search data found for <?php echo $_SESSION['solName'] ?>  <?php echo ($_SESSION['solName']!="" && $_SESSION['contName']!="") ? "and" : "" ?> <?php echo $_SESSION['contName'] ?>.</p>
<?php
 } 
?>
</div>
</div>
</section>
<div class="clearfix"></div> 
</br></br>
<?php 
include("pagehitcounter.php");
include("includes/footer-new.php");
?>
</div>
</body>
</html>