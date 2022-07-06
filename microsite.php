<?php 

      include("../includes/connect.php");
      include("../includes/function.php");
	 
        unset($_SESSION['contName']);
        unset($_SESSION['solName']);
       
       
        $_COOKIE["uemail"];
       if(empty($c_lient_Id))
       {
       $subdomainqry=mysql_query("select client_id from sp_subdomain where cms_subdomain_url='".$_SERVER['HTTP_HOST']."' and valid=1 and deleted=0 and status=1");
       $subdomainget=mysql_fetch_array($subdomainqry);
       $c_lient_Id=$subdomainget['client_id'];
       }
       $pc_member_info = getPCMemberInfo($c_lient_Id);


        $pcmember_pc_type = $pc_member_info['member_pc_type'];
        $p_client_id = $pc_member_info['p_client_id']; 
        
         
         
       
        
        
            $QrySelect="select * from sp_microsite where client_id='".$c_lient_Id."'";
            $QrySelectset=mysql_query($QrySelect) or die(mysql_error());
            $QryselectCount=mysql_num_rows($QrySelectset);
            $QrySelectget=mysql_fetch_array($QrySelectset);
            
            if($QryselectCount==0)
            {
             $ins_sql3  = "INSERT into sp_microsite
                        (`client_id`, `p_client_id`, `slide1_img`,`slide2_img`, `slide3_img`, `slide1_headline`, `slide2_headline`, `slide3_headline`, `slide1_paragraph`, `slide2_paragraph`, `slide3_paragraph`,`theme_bg`,`doe`)
                        select '".$c_lient_Id."', '".$p_client_id."', `slide1_img`,`slide2_img`, `slide3_img`, `slide1_headline`, `slide2_headline`, `slide3_headline`, `slide1_paragraph`, `slide2_paragraph`, `slide3_paragraph` , `theme_bg` , '".date('Y-m-d H:i:s')."' from sp_microsite where p_client_id = '".$p_client_id."'";
                        mysql_query($ins_sql3);   
            }
        
        

        $pcompqry = "select pid,comp_id,person_contact1,person_email,first_name,last_name from sp_members where client_id ='".$c_lient_Id."' and valid=1 and deleted=0 and approve=1 and company_member_type=1";
        $pcompres = mysql_query($pcompqry) or die(mysql_error());  
	    $pcomp_row = mysql_fetch_array($pcompres);          
        $p_comp_id = $pcomp_row['comp_id']; 

        
      
	
	
        $res=mysql_query("select header_logo from sp_company where comp_id='".$p_comp_id."' and valid=1 and deleted=0");  
        $lData=mysql_fetch_array($res);
        $aboutcmpny=$lData['about_company'];
        $Namecmpny=$lData['company_name'];
        $faviconimg=$lData['favicon'];
        $company_logo=$lData['header_logo'];



?>




<!DOCTYPE html>
<html lang="en">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="http://www.salespanda.com/images/favicon.ico" />
   
    <title></title>
     <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
 

  
 <link rel="stylesheet" href="css/microsite.css">

<style>
.loader{
 position: fixed;
  left: 0px;
  top: 0px;
  width: 100%;
  height: 100%;
  z-index: 9999;
  background: url('http://systest1.technochimes.com/manager/images/ajaxLoader.gif') 50% 50% no-repeat rgb(249,249,249);
}

.btn
{
 background-color:#23292C;
    color:#ffffff;    
}

.btn.active, .btn:active {
    background-color:#2152a2;
    color:#ffffff;
}

.imgcircle{
   border: 24px solid #d7d7d7;
   margin-left: 100px;
}

input[name=search] 
{
background-image: url('searchicon.png');
}


@media only screen and (max-width: 600px) 
{
    .carousel-caption 
    {
        display:none;
    }
    
    .imgcircle
    {
        margin-left:auto;
    }
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

.btn.focus, .btn:focus, .btn:hover
{
    color: #fff;
    text-decoration: none;
}

.input-group-addon {
    padding: .375rem 1.75rem;
   
}
.btn-space {
    margin: 5px 0px 2px 2px;
}
</style>
</head>
<body>
    
 

  <header id="main-header" class="white-transparent ng-scope menu-sticky">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <a class="navbar-brand" href="">
                        <img src="company_logo/<?php echo $company_logo; ?>" id="logo_img" class="img-fluid" alt="">
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="ion-navicon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto w-100 justify-content-end">
                            <li class="nav-item">
                                <a class="nav-link active" href="#sp-banner">Home</a>
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

  
  

  <div class="sp-banner awesome ng-scope" style="margin-top:100px;" id="sp-banner">
    <div class="carousel slide" id="myCarousel" data-ride="carousel">
        
   

        <div class="carousel-inner">
            <div class="item active">
                <img src="company_banner/<?php echo $QrySelectget['slide1_img']; ?>" alt="First slide">
                <div class="carousel-caption">
                    <h3 class="sp-tw-6 sp-font-white text-uppercase"><span class="sp-font-green"><?php echo $QrySelectget['slide1_headline']; ?></span></h3>
                    <h4 class="sp-tw-1 sp-font-white"><?php echo $QrySelectget['slide1_paragraph']; ?></h4>
                    <a class="button sp-mt-15" href="#sp-contact">GET ADVICE</a>
                </div>
            </div>
            
            
             <div class="item">
                <img src="company_banner/<?php echo $QrySelectget['slide2_img']; ?>" alt="Second slide">
                <div class="carousel-caption">
                      <h3 class="sp-tw-6 sp-font-white text-uppercase"><span class="sp-font-green"><?php echo $QrySelectget['slide2_headline']; ?></span></h3>
                    <h4 class="sp-tw-1 sp-font-white"><?php echo $QrySelectget['slide2_paragraph']; ?></h4>
                    <a class="button sp-mt-15" href="#sp-contact">GET ADVICE</a>
                </div>
            </div>
            
            
            <div class="item">
                <img src="company_banner/<?php echo $QrySelectget['slide3_img']; ?>" alt="Second slide">
                <div class="carousel-caption">
                      <h3 class="sp-tw-6 sp-font-white text-uppercase"><span class="sp-font-green"><?php echo $QrySelectget['slide3_headline']; ?></span></h3>
                    <h4 class="sp-tw-1 sp-font-white"><?php echo $QrySelectget['slide3_paragraph']; ?></h4>
                    <a class="button sp-mt-15" href="#sp-contact">GET ADVICE</a>
                </div>
            </div>
         
            
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
</div>


<div class="main-content ng-scope">
    <!-- === About Us === -->
    <section id="sp-about" class="sp-about overview-block-ptb">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12 align-self-start">
                    <div class="heading-title left">
                        <small class="sp-font-green">ABOUT ME</small>
                        <h2 class="sp-tw-8"><?php echo $pcomp_row['first_name'].' '.$pcomp_row['last_name']; ?></h2>
                        <h6 class="sp-font-green"><?php echo $pcomp_row['person_email']; ?></h6>
                    </div>
                    <p>
                      <?php echo $QrySelectget['microsite_about']; ?> 
                    </p>
                    
                   
                    <a class="button sp-mt-15" href="#sp-contact">Contact Me</a>
                </div>
                
               
                
                <div class="col-lg-6 col-md-12 align-self-center sp-re-9-mt-50">
                    <?php if($QrySelectget['profile_img']!='') { ?>
                   <img class="img-fluid wow fadeIn img-circle imgcircle" src="images/<?php echo $QrySelectget['profile_img']; ?>" alt="#" width="304" height="236">
                   <?php } else { ?>
                    <img class="img-fluid wow fadeIn img-circle imgcircle" src="images/dummy-profile.jpg" alt="#" width="304" height="236">
                   <?php
                   }
                   ?>
                </div>
            </div>
        </div>
    </section>
   

     <!-- === Latest Blog Post === -->
     
     <section id="sp-blog" class="overview-block-ptb grey-bg sp-blog">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="heading-title text-center">
                        <!-- <small class="">recent talks</small> -->
                        <h2 class="title sp-tw-6 sp-font-green">Content Showcase</h2>
                        
                        <h6>The Content Showcase feature works give you the option to showcase the content created or uploaded in your Content Library</h6>
                    </div>
                </div>
            </div>
            
               
            <div class="row" id="categories">
                <div class="col-sm-8">
                    <div class="heading-title text-center">
                        <div class="form-group">
                      <button class="btn btn-space active" id="activeShow_0" onclick="showcasefilter(0)">Show all</button>
                        <?php 	
                        if($pcmember_pc_type=='C')
                         { 
                        $ctpQ=mysql_query($a="SELECT * FROM sp_article_type WHERE valid=1 AND deleted=0 and client_id='".$p_client_id."' ORDER BY article_type");
                         }
                         else
                         {
                         $ctpQ=mysql_query($a="SELECT * FROM sp_article_type WHERE valid=1 AND deleted=0 and client_id='".$c_lient_Id."' ORDER BY article_type");    
                         }
                        
                        
                        while($ctperow=mysql_fetch_array($ctpQ))
                        {
                        ?>	  
                        <button class="btn btn-space" id="activeShow_<?php echo $ctperow["id"]; ?>" onclick="showcasefilter(<?php echo $ctperow["id"]; ?>)"><?php echo $ctperow["article_type"]; ?></button>
                       
                         <?php }?>
                         
                        </div>
                        
                    </div>
                </div>
                
                
                
                <div class="col-sm-4">
                <div class="input-group">
                <input type="text" class="form-control showcaseSRH" id="showcsearch-name" placeholder="Search content by title" autocomplete="off" onkeyup="ShowcaseSearchKeyup();" width="48" height="48">
                <span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
                </div>
                <div id="showsrch" style="display:none;float: right;margin-right: 354px;margin-top: 13px;"></div>
                </div>
                
                
            </div>
            
           
               
            
            
            
            <div class="row" id="showcasefilter">
                
                 <?php
        
        	if($pcmember_pc_type=='C')
        	{
           
          $csres = mysql_query($a="select CS.*,TS.id as syndid, TS.p_client_id, TS.c_client_id, TS.submem_content_publish_url from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".$c_lient_Id."' and CS.approve!='3' and CS.valid=1 and CS.deleted=0 and TS.approve=1 ORDER BY CS.item_order, CS.id desc limit 6") or die(mysql_error());
            } 
           else
           {
            $csres = mysql_query($a="select id,member_id,case_study,video_image,doc_mode,case_study_desc,image_thumb1,crop_Image,case_study_title,case_study_actual_title,content_type,attach_company,item_order,landingpage_status,landingpage_id from sp_case_study where client_id='".$c_lient_Id."' and valid=1 and deleted=0 and approve=1 ORDER BY item_order, id desc limit 6") or die(mysql_error());
            } 
          
        while($caseStudy = mysql_fetch_array($csres))
		{
			$caseStudyId = $caseStudy['id'];
            $caseStudyItem = $caseStudy['item_order']; 
			$casestudyMember=$caseStudy["member_id"];
			$caseStudyName = $caseStudy['case_study'];
			$documentMode=$caseStudy['doc_mode'];
			$caseLandStatus=$caseStudy['landingpage_status']; 
			$caseLandId=$caseStudy['landingpage_id']; 
			$caseStudyDescription1 = $caseStudy['case_study_desc'];
			$caseStudyDescription = substr("$caseStudyDescription1", '0', '200')."..";
			$castStudyUrl =  $caseStudy['case_study_url'];
		    $crop_image=$caseStudy['crop_Image'];
		    
		    
		
			if($caseStudy['video_image']!='')
			{
			  $showcaseThumb=$caseStudy['video_image'];  
			}
			else
			{
			  $showcaseThumb='http://'.$_SERVER['HTTP_HOST'].'/manager/uploads/thumb_img/'.$caseStudy['image_thumb1'];  
			}
			
			$caseStudyTitle11 = $caseStudy['case_study_title'];
			$caseStudyActualTitle = ($caseStudy['case_study_actual_title']!='') ? $caseStudy['case_study_actual_title'] : $caseStudy['case_study_title'];
			$csname =str_replace(' ', '-', $caseStudyTitle11 );
			$contentType=$caseStudy['content_type'];
			if($contentType!='')
			{
			   $contentTypeName=getarticleName($caseStudy['content_type']);
			}	
			
			if($contentType!='')
			{
				$casestd="select article_type from sp_article_type where id='".$article."'";
				$casequery=mysql_query($casestd) or die(mysql_error());
				$caserow=mysql_fetch_array($casequery);
				$articleName=$caserow['article_type'];
				$articleId=$caserow['id'];
			}	
				if($articleId==$contentType){
					$urlName=$articleName;
				}
				
			$caseStudyTitleLength=strlen($caseStudyActualTitle);
			$attachforcompany = $caseStudy['attach_company'];
			$filterFlag='s';
		
			//htaccess title convert
			$csname =str_replace(' ', '-', $caseStudyTitle11);
		 
	
			if($caseStudyTitleLength > 35)
			{
				$caseStudyTitle = substr($caseStudyActualTitle, '0', '35')."..";
			}
			else
			{
				$caseStudyTitle=$caseStudyActualTitle;
			}


            if($pcmember_pc_type=='C')
            {
				$cslandquery=mysql_query("select LS.*, LP.publish_page_id,LP.landingpage_title,LP.publish_page_name,LP.landingpage_desc,LP.page_title_seo,LP.meta_description from sp_landingpage_publish as LP INNER JOIN sp_landingpage_syndication as LS ON LP.publish_page_id = LS.landingpage_id where LP.publish_page_id='".$caseLandId."' and LP.client_id='".$p_client_id."' ");
			}
			else
			{
				$cslandquery=mysql_query("select  publish_page_id,landingpage_title,publish_page_name,landingpage_desc,page_title_seo,meta_description,approve from sp_landingpage_publish
			where publish_page_id='".$caseLandId."' and client_id='".$clientId."' ");
			}

            $cslandget=mysql_fetch_array($cslandquery);
            $cslandname=$cslandget['publish_page_name'];
            $cslandApprove= $cslandget['approve'];		
                
		?>	 
        
        
                <div class="col-sm-4" style="margin-top: 20px;">
                    <div class="item">
                        <div class="sp-blog-box">
                            <div class="sp-blog-image clearfix">
                               
                                
                                <?php if($caseLandStatus==1 && $cslandApprove==1) 
                                { 
                                ?>
                                <a target="_blank" href="<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>/landingpage/<?php echo $cslandname; ?>">
                                <img class="img-fluid center-block" src="<?php echo $showcaseThumb; ?>" alt="<?php echo $cslandname; ?>">
                                </a>
                                <?php 
                                } 
                                else
                                { 
                                ?>  
                                <a target="_blank" href="<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>/showcase/<?php echo $csname; ?>">
                                <img class="img-fluid center-block" src="<?php echo $showcaseThumb; ?>" alt="<?php echo $csname; ?>">
                                </a>
                                <?php 
                                }
                                ?>
                                
                                
                            </div>
                            <div class="sp-blog-detail">
                                <div class="blog-title"><h5 class="sp-tw-4 sp-mb-10"><?php echo ucfirst($caseStudyTitle); ?></h5></div>
                                <div class="blog-content">
                                    <p><?php echo $caseStudyDescription; ?></p>
                                </div>
                                <div class="social-icons">
                                    <ul class="info-share">
                                    <li>
                                    <?php if($caseLandStatus==1 && $cslandApprove==1) 
                                    { 
                                    ?>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>/landingpage/<?php echo $cslandname; ?>?channel_type=Facebook"><i class="fa fa-facebook"></i></a>
                                    <?php 
                                    } 
                                    else 
                                    { 
                                    ?>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>/showcase/<?php echo $csname; ?>?channel_type=Facebook"><i class="fa fa-facebook"></i></a>
                                    <?php 
                                    } 
                                    ?>
                                    
                                    </li>
                                    
                                    <li>
                                    <?php if($caseLandStatus==1 && $cslandApprove==1) 
                                    { 
                                    ?>
                                    <a href="https://twitter.com/intent/tweet?text=<?php echo $caseStudyDescription; ?>&url=<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>/landingpage/<?php echo $cslandname; ?>?channel_type=twitter"><i class="fa fa-twitter"></i></a>
                                    <?php 
                                    } 
                                    else 
                                    { 
                                    ?>
                                    <a href="https://twitter.com/intent/tweet?text=<?php echo $caseStudyDescription; ?>&url=<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>/showcase/<?php echo $csname; ?>?channel_type=twitter"><i class="fa fa-twitter"></i></a>
                                    <?php 
                                    } 
                                    ?>
                                    
                                    </li>
                                    
                                    
                                    <li>
                                    <?php if($caseLandStatus==1 && $cslandApprove==1) 
                                    { 
                                    ?>
                                    <a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>/landingpage/<?php echo $cslandname; ?>?channel_type=linkedin"><i class="fa fa-linkedin"></i></a>
                                    <?php 
                                    } 
                                    else 
                                    { 
                                    ?>
                                    <a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>/showcase/<?php echo $csname; ?>?channel_type=linkedin"><i class="fa fa-linkedin"></i></a>
                                    <?php 
                                    } 
                                    ?>
                                    
                                    </li>
                                    <li>
                                    <?php if($caseLandStatus==1 && $cslandApprove==1) 
                                    { 
                                    ?>
                                    <a href="https://plus.google.com/share?url=<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>/landingpage/<?php echo $cslandname; ?>?channel_type=googleplus"><i class="fa fa-google"></i></a>
                                    <?php 
                                    } 
                                    else 
                                    { 
                                    ?>
                                    <a href="https://plus.google.com/share?url=<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>/showcase/<?php echo $csname; ?>?channel_type=googleplus"><i class="fa fa-google"></i></a>
                                    <?php 
                                    } 
                                    ?>
                                    
                                    </li>
                                    
                                    <li>
                                    <?php if($caseLandStatus==1 && $cslandApprove==1) 
                                    { 
                                    ?>
                                    <a onclick="whatsappShare();" style="cursor: pointer;" data-link="<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>/landingpage/<?php echo $cslandname; ?>?channel_type=whatsapp"><i class="fa fa-whatsapp"></i></a>
                                    <?php 
                                    } 
                                    else 
                                    { 
                                    ?>
                                    <a onclick="whatsappShare();" style="cursor: pointer;" data-link="<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>/showcase/<?php echo $csname; ?>?channel_type=whatsapp"><i class="fa fa-whatsapp"></i></a>
                                    <?php 
                                    } 
                                    ?>
                                    
                                    </li>
                                  </ul>
                            </div>
                                <div class="sp-blog-meta">
                                    <ul class="list-inline">
                                        <li class="list-inline-item"><a href="javascript:void(0);"><i class="fa fa-edit"></i>&nbsp;<?php echo $contentTypeName; ?></a></li>
                                        <li class="list-inline-item"><a href="javascript:void(0);"><i class="fa fa-calendar"></i>&nbsp;<?php if(strtotime($caseStudy['dou'])=='62169955200 ' || strtotime($caseStudy['dou'])=='FALSE') { echo date('l jS \of F Y',strtotime($caseStudy['doe'])); } else { echo date('l jS \of F Y',strtotime($caseStudy['dou'])); }?></a></li>
                                    </ul>        
                                      
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                
         <?php } ?>       
                
            </div>
        </div>
    </section>
   
    <div id="sp-contact" class="contact-us">
        <div class="row no-gutters">
            <div class="col-lg-6 col-md-12">
                
                <iframe class="map" src="https://maps.google.com/maps?q=HDFC%20House%2C%202nd%20Floor%2C%20H.%20T.%20Parekh%20Marg%2C%20165-166%2C%20Backbay%20Reclamation%2C%20Churchgate%2C%20Mumbai%20-%20400020&t=&z=13&ie=UTF8&iwloc=&output=embed" style="border:0" allowfullscreen=""></iframe>
            </div>
            <div class="col-lg-6 col-md-12 align-self-center">
                <div class="sp-mlr-60 sp-ptb-80">
                    <div class="heading-title left">
                        <h3 class="sp-tw-6">Get in Touch</h3>
                    </div>
                    <p>Leave your contact details and my team will get in touch with you.</p>
                    <form id="contact" method="post" class="ng-pristine ng-valid">
                        <div class="contact-form">
                            <div class="section-field">
                                <input class="require" id="your-name" type="text" placeholder="Name*" name="name">
                            </div>
                            <div class="section-field">
                                <input class="require" id="your-email" type="email" placeholder="Email*" name="email">
                            </div>
                            <div class="section-field">
                                <input class="require" id="number-339" type="text" placeholder="Phone*" name="phone">
                            </div>
                            <div class="section-field textarea">
                                <textarea id="contact_message" class="input-message require" placeholder="Comment*" rows="5" name="message"></textarea>
                            </div>
                            <div class="section-field sp-mt-20">
                                
                            </div>
                            <button name="submit" type="submit" value="Send" onclick="return submit_formbuilder();" id="send-value" class="button sp-mt-15">Send Message</button>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- === contact-us END=== -->
</div>
<!-- === Main Content End === --></div></div>

<div ui-view="footer" class="ng-scope">
<footer class="dark-bg ng-scope">
    <div class="sp-footer sp-pt-70 sp-pb-20">
        <div class="container">
            <div class="row overview-block-ptb2">
                <div class="col-lg-6 col-md-12 sp-mtb-20">
                    <div class="logo">
                        
                        <div class="sp-font-white sp-mt-15 sp-mr-60"><?php echo $QrySelectget['microsite_about']; ?> </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 sp-mtb-20">
                    <ul class="menu">
                        <li><a href="#sp-banner">Home</a></li>
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
                                <p><?php echo $QrySelectget['microsite_address']; ?></p>
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
</div>
</div>
</div>





<script>var microsite=1;</script>
 <?php include("includes/footer-event.php"); ?>
<script>
//$.noConflict();
$(document).ready(function($){
  $('[data-toggle="tooltip"]').tooltip(); 
  
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
  
})




function submit_formbuilder()
{
var frm=jQuery.noConflict();
var c_lient_Id='<?php echo $c_lient_Id; ?>';
var full_name = frm("#your-name").val(); 
var email = frm("#your-email").val();
var contact = frm("#number-339").val();
var form_refer="Microsite request page";
var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/; 
var intRegex = /[0-9 -()+]+$/; 

var isValid = true;
        frm("#your-email").each(function() {
           if (frm.trim(frm(this).val()) == '' || (!emailReg.test(email))) {
                isValid = false;
                frm(this).css({
                    "border": "1px solid red",
                   
                });
            }
           else {
                
                frm(this).css({
                    "border": "",
                    "background": ""
                });
            }
        });
   
    
     
      frm("#your-name").each(function() {
            if (frm.trim(frm(this).val()) == '') {
                isValid = false;
                frm(this).css({
                    "border": "1px solid red",
                });
            }
            else {
                frm(this).css({
                    "border": "",
                    "background": ""
                });
            }
        });
        
        
       
       

      frm("#number-339").each(function() {
            if (frm.trim(frm(this).val()) == '' || (!intRegex.test(contact))) {
                isValid = false;
                frm(this).css({
                    "border": "1px solid red",
                });
            }
            else {
                frm(this).css({
                    "border": "",
                    "background": ""
                });
            }
        });
       
        if (isValid == false) 
        {
           return false;
        }
        else 
           {

frm.ajax({url:"https://www.hdfcmfpartners.com/webcontent/sp-formbuilder-submit.php",
        type: "post",
        data: {full_name1: full_name,email1: email,c_lient_Id: c_lient_Id,form_refer: form_refer,contact1: contact},
        cache: false,
        crossDomain : true,
   
        beforeSend: function() {
         frm('#send-value').attr("disabled", true).val('Please wait ...');	
        },
        success:function(result){
          frm("#formbuilder_lead").show().text('Thank you for submitting. We will get back to you soon.').fadeIn( 300 ).delay( 3000 ).fadeOut( 800 );
           frm('#send-value').attr("disabled", false).val("Submit");
		window.setTimeout(function() {
         window.location.reload(); 
        }, 2000);
        
      }
});
} 
}


function showcasefilter(ContentType)
{
 
var ctr=jQuery.noConflict();
ctr("#activeShow_"+ContentType).addClass('active').siblings().removeClass('active');   
var c_lient_Id='<?php echo $c_lient_Id; ?>';
var p_client_id='<?php echo $p_client_id; ?>';
var pcType='<?php echo $pcmember_pc_type; ?>';
var siteURL='<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>';

ctr.ajax({url:"https://www.hdfcmfpartners.com/webcontent/sp-showcasefilter.php",
        type: "post",
        data: {c_lient_Id: c_lient_Id,p_client_id: p_client_id,pcType: pcType,ContentType: ContentType,siteURL: siteURL},
        cache: false,
        crossDomain : true,
   
        beforeSend: function() 
        {
         ctr('#showcasefilter').html('<div class="loader"></div>');
        },
        success:function(result)
        {
        ctr("#showcasefilter").html(result);
        }
});

}


 function ShowcaseSearchKeyup() 
	  {
	     var srch=jQuery.noConflict(); 
		 var showsearchName=srch("#showcsearch-name").val();
        var c_lient_Id='<?php echo $c_lient_Id; ?>';
        var p_client_id='<?php echo $p_client_id; ?>';
        var pcType='<?php echo $pcmember_pc_type; ?>';

		srch.ajax({
		type: "POST",
		url: "https://www.hdfcmfpartners.com/webcontent/get_document_title_list.php",
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
    

function whatsappShare() 
            { 
			if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
			  var weburl = $(this).attr("data-link");
         
                       var whats_app_message =encodeURIComponent(weburl);
				       var whatsapp_url = "whatsapp://send?text="+whats_app_message;
				       window.location.href= whatsapp_url;
               	
			}
			else
			{
				alert('Whatsapp sharing is only available through mobile.');
			}
          }
  




</script>
</body></html>