<?php
include("includes/global.php");
include("includes/function.php");
include("manager/common_functions.php");
include("csrf/csrf-magic.php");

header("Access-Control-Allow-Origin: *");
unset($_SESSION['contName']);
unset($_SESSION['solName']);

if (strtoupper(getCountyByIp()) != 'IN') {
    //echo "<script language=\"javascript\">window.location.href='/invalid.html';</script>";die;	
}

if (strstr($_SERVER['HTTP_HOST'], 'app.') != '') {

    header("location:login.php");

    exit;
}


$_COOKIE["uemail"];

if (empty($c_lient_Id)) {
    $subdomainqry = mysqli_query($conn,"select client_id from sp_subdomain where cms_subdomain_url='" . $_SERVER['HTTP_HOST'] . "' and valid=1 and deleted=0 and status=1");
    $countmicrosite = mysqli_num_rows($subdomainqry);
    $subdomainget = mysqli_fetch_array($subdomainqry);
    $c_lient_Id = $subdomainget['client_id'];
} else {
    $subdomainqry = mysqli_query($conn,"select client_id from sp_subdomain where cms_subdomain_url='" . $_SERVER['HTTP_HOST'] . "' and valid=1 and deleted=0 and status=1");
    $countmicrosite = mysqli_num_rows($subdomainqry);
}

if ($countmicrosite > 0) {
    //f($_SERVER['HTTP_HOST']!='amitthanvi.absliadvisors.com')
    //{
    //header("location:microsite-comingsoon.php");   exit;
    //}
    $pc_member_info = getPCMemberInfo($c_lient_Id);


    $pcmember_pc_type = $pc_member_info['member_pc_type'];
    $p_client_id = $pc_member_info['p_client_id'];


    if ($pcmember_pc_type == 'C') {


        $smemqry1 = "SELECT * FROM sp_sub_members where c_client_id= '" . $c_lient_Id . "' and valid=1 and deleted=0";
        $smem_ftch = mysqli_query($conn,$smemqry1);
        $row_smem = mysqli_fetch_array($smem_ftch);
        $p_client_id = $row_smem['p_client_id'];

        $ptr_category = $row_smem['partner_category'];

        if ($ptr_category != '') {
            $ptr_category_arr = explode(',', $ptr_category);
        }

        $sqry = "select id,subdomain_url,cms_subdomain_url from sp_subdomain where client_id='" . $c_lient_Id . "'";
        $resq = mysqli_query($conn,$sqry);
        $domianData = mysqli_fetch_array($resq);
        
        $sdomainCmsPath = $domianData['cms_subdomain_url'];
	    $subdomain_url =$domianData['subdomain_url'];

        $sqltemplate = "SELECT template_id,partner_category,users_category,VideoId FROM user_templates where client_id = '" . $p_client_id . "' and syndication_status=1 and valid=1 and deleted=0";


        $rstemplate = mysqli_query($conn,$sqltemplate);
        while ($rowtemplate = mysqli_fetch_array($rstemplate)) {

            $cnt_ptr_category = $rowtemplate['partner_category'];
            $cnt_users_category = $rowtemplate['users_category'];

            $cnt_temp_arr[] = $rowtemplate['template_id'];

            $qrysyndScase = "SELECT id,approve,content_publish_url,case_study_title FROM sp_case_study where case_study_library = " . $rowtemplate['template_id'] . " and client_id = '" . $p_client_id . "' and valid=1 and deleted=0";
            $showcase_synd_ftch = mysqli_query($conn,$qrysyndScase);
            $row_showcase_synd_arr = mysqli_fetch_array($showcase_synd_ftch);
            $syndCaseId = $row_showcase_synd_arr['id'];

            if ($row_showcase_synd_arr['case_study_title'] != "") {

                $csname = str_replace(' ', '-', mysqli_real_escape_string($conn,$row_showcase_synd_arr['case_study_title']));
                $cont_publish_url = 'https://' . $sdomainCmsPath . '/showcase/' . $csname;
            }



            if ($cnt_ptr_category != '') {

                $cnt_ptr_category_arr = explode(',', $cnt_ptr_category);

                for ($m = 0; $m < sizeof($ptr_category_arr); $m++) {

                    if (in_array($ptr_category_arr[$m], $cnt_ptr_category_arr)) {

                        $sqlsyndtemplate = "SELECT template_id FROM sp_template_syndication where c_client_id = '" . $c_lient_Id . "' and template_id = '" . $rowtemplate['template_id'] . "' and valid=1 and deleted=0";
                        $rssyndtemplate = mysqli_query($conn,$sqlsyndtemplate);
                        $syndtemprecordcount = mysqli_num_rows($rssyndtemplate);

                        if ($syndtemprecordcount == 0) {
                            $addsyndcont = "insert into sp_template_syndication set 
                                        p_client_id='" . $p_client_id . "',	
                                        c_client_id='" . $c_lient_Id . "',									
        								template_id='" . $rowtemplate['template_id'] . "',
        								case_id='" . $syndCaseId . "',
        								approve='1',
        								added_by='" . $userid . "',
        								submem_content_publish_url='" . $cont_publish_url . "',
        								doe='" . $doe . "'";
                            $ressyndcont = mysqli_query($conn,$addsyndcont);
                        }

                        $synd_cnt_arr[] = $rowtemplate['template_id'];
                    }
                }
            }

            if ($cnt_users_category != '') {

                $cnt_users_category_arr = explode(',', $cnt_users_category);


                if (in_array($c_lient_Id, $cnt_users_category_arr)) {

                    $sqlsyndtemplate = "SELECT template_id FROM sp_template_syndication where c_client_id = '" . $c_lient_Id . "' and template_id = '" . $rowtemplate['template_id'] . "' and valid=1 and deleted=0";
                    $rssyndtemplate = mysqli_query($conn,$sqlsyndtemplate);
                    $syndtemprecordcount = mysqli_num_rows($rssyndtemplate);

                    if ($syndtemprecordcount == 0) {
                        $addsyndcont = "insert into sp_template_syndication set 
                                        p_client_id='" . $p_client_id . "',	
                                        c_client_id='" . $c_lient_Id . "',									
        								template_id='" . $rowtemplate['template_id'] . "',
        								case_id='" . $syndCaseId . "',
        								approve='1',
        								added_by='" . $userid . "',
        								submem_content_publish_url='" . $cont_publish_url . "',
        								doe='" . $doe . "'";
                        $ressyndcont = mysqli_query($conn,$addsyndcont);
                    }
                }
            }
        }
    }else {
        $sqry = "select id,subdomain_url,cms_subdomain_url from sp_subdomain where client_id='" . $c_lient_Id . "'";
        $resq = mysqli_query($conn,$sqry);
        $domianData = mysqli_fetch_array($resq);
	    $subdomain_url =$domianData['subdomain_url'];
    }




    if (!empty($c_lient_Id)) {
        $QrySelect = "select * from sp_microsite where client_id='" . $c_lient_Id . "'";
        $QrySelectset = mysqli_query($conn,$QrySelect);
        $QryselectCount = mysqli_num_rows($QrySelectset);
        $QrySelectget = mysqli_fetch_array($QrySelectset);

        if ($QryselectCount == 0 && $p_client_id != '') {
            $ins_sql3 = "INSERT into sp_microsite
                        (`client_id`, `slide1_img`,`tagline_heading`, `disclaimer_tag`, `disclaimer_content`, `disclaimer_color`, `slide3_headline`, `slide1_paragraph`, `slide2_paragraph`, `slide3_paragraph`,`form_textcolor`, `footerColor`, `headerMenucolor`,`microsite_about`,`theme_bg`,`profile_img`,`microsite_address`,`doe`)
                        select '" . $c_lient_Id . "', `slide1_img`,`tagline_heading`, `disclaimer_tag`, `disclaimer_content`, `disclaimer_color`, `slide3_headline`, `slide1_paragraph`, `slide2_paragraph`, `slide3_paragraph` ,`form_textcolor`, `footerColor`, `headerMenucolor`,`microsite_about`,`theme_bg` ,`profile_img`,`microsite_address`, '" . date('Y-m-d H:i:s') . "' from sp_microsite where p_client_id = '" . $p_client_id . "'";
            mysqli_query($conn,$ins_sql3);

            $refer = 'http://' . $_SERVER['HTTP_HOST'];

            echo "<meta http-equiv='refresh' content='0;URL=$refer'>";
        }
    }



    $pcompqry = "select pid,comp_id,person_contact1,person_email,first_name,last_name from sp_members where client_id ='" . $c_lient_Id . "' and valid=1 and deleted=0 and approve=1 and company_member_type=1";

    $pcompres = mysqli_query($conn,$pcompqry);
    $pcomp_row = mysqli_fetch_array($pcompres);
    $p_comp_id = $pcomp_row['comp_id'];

    $emailExplode = explode('@', $pcomp_row['person_email']);

    $phoneExplode = explode('', $pcomp_row['person_contact1']);
    
    
    $ext=substr($pcomp_row['person_contact1'],0,-10);
    
    $firstPhone=substr($pcomp_row['person_contact1'],strlen($pcomp_row['person_contact1'])-10,-6);
    $secondPhone=substr($pcomp_row['person_contact1'],strlen($pcomp_row['person_contact1'])-6);

   //print_r($phoneExplode); exit;


    $res = mysqli_query($conn,"select * from sp_company where comp_id='" . $p_comp_id . "' and valid=1 and deleted=0");
    $lData = mysqli_fetch_array($res);
    $aboutcmpny = $lData['about_company'];
    $Namecmpny = $lData['company_name'];
    $faviconimg = $lData['favicon'];
    $company_logo = $lData['header_logo'];

    $micro = mysqli_query($conn,"SELECT sp_microsite.*,concat(sm.first_name,' ',sm.last_name) as name,sm.person_email  FROM `sp_microsite`
left join sp_members sm on sm.client_id=sp_microsite.client_id
WHERE sp_microsite.`client_id` ='" . $c_lient_Id . "' ");
    $micrositeDetails = mysqli_fetch_array($micro);
    //print_r($micrositeDetails);
   
    $imgstartVal=explode('.',$subdomain_url);
    
    $newMicro = ($micrositeDetails['new_site_flag'] == 1) ? "" : "hidden";
    ?>




    <!DOCTYPE html>
    <html lang="en">
        <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">


            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <link rel="shortcut icon" href="images/favicon.ico" />


            <title></title>
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
            <!--
               <script src="assets/global/plugins/jquery.min.js" type="text/javascript"></script>
            -->
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
           <script src="js/mailcrypt.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">





            <link rel="stylesheet" href="css/style.css">

            <style>


                header.white-transparent 
                {
                    height: 97px;
                    background: #ffffff;
                    padding: 26px;
                }




                .btn
                {
                    background-color:#23292C;
                    color:#ffffff;    
                }

                .btn.active, .btn:active {
                    background-color:#C7222A;
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

                    .menu-resp
                    {
                        z-index:1; 
                    }

                }


                @media only screen and (max-width: 460px) 
                {
                    .sp-blog-detail
                    {
                        height:auto !important; 
                    }

                    .nav-item a 
                    {
                        color:#272727 !important;   
                    }

                    .company-tagline
                    {
                        font-size:14px !important;
                    }

                    .nav-tabs>li 
                    {

                        width:160px!important;
                    }

                    .menu-resp
                    {
                        z-index:1; 
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

                .nav-tabs>li 
                {

                    width:190px;
                }



                .nav-tabs>li>a {

                    color: #272727;
                }

                select.form-control:not([size]):not([multiple]) {
                    height:auto;
                }

                .nav-tabs img {
                    display: block;
                }

                .nav > li > a > img {
                    max-width: none;
                }

                .nav-tabs a {
                    text-align: center;
                    padding: 10px 28px !important;
                }


                .nav-tabs>li>a {

                    height: 130px;
                }



                li.nav-title {
                    display: block;
                    text-transform: uppercase;
                    font-size: 26px;
                    color: #ffffff;
                    min-width: 238px;
                    font-weight:bold;
                    font-family: 'PFHandbookPro-Regular' !important;
                    padding-bottom: 32px;
                    margin-top: -11px;
                    font-weight:bold;
                }
                /*new added*/
                .mrt-0 {
                    float: left;
                    margin: 0 !important;
                    text-align: left;
                    width:10% !important;
                }
                .understand{
                    font-size: 17px;
                }
                .term-set{
                    padding:4px;	
                }
                /*End*/
                #loader {
                    position: absolute;
                    left: 50%;
                    top: 50%;
                    z-index: 1;
                    width: 150px;
                    height: 150px;
                    margin: -75px 0 0 -75px;
                    border: 16px solid #f3f3f3;
                    border-radius: 50%;
                    border-top: 16px solid #3498db;
                    width: 120px;
                    height: 120px;
                    -webkit-animation: spin 2s linear infinite;
                    animation: spin 2s linear infinite;
                }

                @-webkit-keyframes spin {
                    0% { -webkit-transform: rotate(0deg); }
                    100% { -webkit-transform: rotate(360deg); }
                }

                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }

                /* Add animation to "page content" */
                .animate-bottom {
                    position: relative;
                    -webkit-animation-name: animatebottom;
                    -webkit-animation-duration: 1s;
                    animation-name: animatebottom;
                    animation-duration: 1s
                }

                @-webkit-keyframes animatebottom {
                    from { bottom:-100px; opacity:0 } 
                    to { bottom:0px; opacity:1 }
                }

                @keyframes animatebottom { 
                    from{ bottom:-100px; opacity:0 } 
                    to{ bottom:0; opacity:1 }
                }
            </style>
        </head>
        <body>



            <header id="main-header" class="white-transparent ng-scope menu-sticky" style="background: <?php echo $QrySelectget['theme_bg']; ?>">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12">
                            <nav class="navbar navbar-expand-lg navbar-light">
                                <a class="navbar-brand" href=""> 
                                    <?php
									
									
                                    if ($p_client_id!= 'ST18934859435') {
                                    if ($company_logo != '') {
                                        echo '<img src="company_logo/' . htmlentities($company_logo) . '" id="logo_img" class="img-fluid" alt="">';
                                    } else if (strstr($_SERVER['SERVER_NAME'], 'absliadvisors.com') == true) {
                                        echo ' <img src="company_logo/j0TPSP108098.png" id="logo_img" class="img-fluid" alt="">';
                                    } else if (strstr($_SERVER['SERVER_NAME'], 'maxlifeinsurance.agency') == true) {
                                        echo ' <img src="company_logo/max_default.png" id="logo_img" class="img-fluid" alt="">';
                                    } else if (strstr($_SERVER['SERVER_NAME'], 'upickservices.in') == true || $imgstartVal[1]=='upickservices' ) {
                                        echo ' <img src="company_logo/UP-Black.png" id="logo_img" class="img-fluid" alt="">';
                                    } else {
                                        echo ' <img src="company_logo/default-partner.jpg" id="logo_img" class="img-fluid" alt="">';
                                    }
                                    
                                    }
                                    ?>
                                </a>
                                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                    <span class="navbar-toggler-icon"></span>
                                </button>


                                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                    <ul class="navbar-nav mr-auto w-100 justify-content-end">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="#sp-banner" style="color:<?php echo htmlentities($QrySelectget['headerMenucolor']); ?>;">Home</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo $newMicro ?>" href="#sp-about" style="color:<?php echo htmlentities($QrySelectget['headerMenucolor']); ?>;" >About Me</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#sp-blog" style="color:<?php echo htmlentities($QrySelectget['headerMenucolor']); ?>;">Content</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#sp-contact" style="background:<?php echo htmlentities($QrySelectget['headerMenucolor']); ?>;">Contact Me</a>
                                        </li>
                                    </ul>
                                </div>
                            </nav>
                        </div>

                    </div>
                </div>
            </header>


            <?php if ($QrySelectget['headerflag'] == '1') { ?>
                <header id="main-header" class="white-transparent menu-sticky menu-resp" style="margin-top:97px;background:#DA9089 !important;height: 52px;">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                <nav class="navbar navbar-expand-lg navbar-light">
                                    <li class="nav-title">LIFE INSURANCE</li>
                                </nav>
                            </div>

                        </div>
                    </div>
                </header>


                <header id="main-header" class="white-transparent menu-sticky menu-resp" style="margin-top:149px;background:#6A4742 !important;height: 22px;padding-top:0px;">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                <p class="company-tagline" style="color:#ffffff;font-size: 18px;">Aditya Birla Sun Life Insurance Company Limited</p>
                            </div>

                        </div>
                    </div>
                </header>
            <?php } ?> 







            <div class="sp-banner awesome ng-scope" <?php if ($QrySelectget['headerflag'] == '1') { ?>style="margin-top: 174px;"<?php } else { ?>style="margin-top: 99px;"<?php } ?>id="sp-banner">
                <div class="carousel slide" id="myCarousel" data-ride="carousel">
                    <div class="carousel-inner">
                        <div class="item active">
                            <img src="company_banner/<?php echo htmlentities($QrySelectget['slide1_img']); ?>" alt="First slide" style="width:100%;">

                        </div>

                    </div>

                </div>
            </div>


            <div class="main-content ng-scope">

                <section id="sp-about" class="sp-about overview-block-ptb <?php echo $newMicro ?>">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-6 col-md-12 align-self-start">
                                <div class="heading-title left">
                                    <small class="sp-font-green">ABOUT ME</small>
                                    <h5 class="sp-tw-8"><?php echo $micrositeDetails['name'] ?></h5>
                                    <h6 class="sp-font-green"><a href="javascript:void(0);" class="mailcrypt"><?php echo $micrositeDetails['person_email'] ?></a></h6>
                                </div>
                                <p>
                                    <?php echo $micrositeDetails['microsite_about'] ?>
                                </p>


                                <a class="button sp-mt-15" href="#sp-contact" style="background:<?php echo htmlentities($QrySelectget['form_textcolor']); ?>; border-color: <?php echo htmlentities($QrySelectget['form_textcolor']); ?>;">Contact Me</a>
                            </div>



                            <div class="col-lg-6 col-md-12 align-self-center sp-re-9-mt-50">
                                <?php $profile_pic = ($micrositeDetails['profile_img'] == '') ? 'default.png' : $micrositeDetails['profile_img'];
                                ?>

                                <img class="img-fluid wow fadeIn img-circle imgcircle" src="images/<?php echo $profile_pic; ?>" alt="#" width="304" height="236">
                            </div>
                        </div>
                    </div>
                </section>
                <section id="sp-blog" class="overview-block-ptb grey-bg sp-blog">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="heading-title">
                                    <!-- <small class="">recent talks</small> -->
                                    <h2 class="title sp-tw-6 sp-font-green" style="color:<?php echo htmlentities($QrySelectget['form_textcolor']); ?>;">View our solutions below</h2>

                                    <h6><?php echo htmlentities($QrySelectget['tagline_heading']); ?></h6>
                                </div>
                            </div>
                        </div>


                        <div class="row" id="categories">
                            <div class="col-md-12">
                                <div class="heading-title text-center">
                                    <div class="tabbable tabbable-tabdrop">    
                                        <ul class="nav nav-tabs">


                                            <li id="activeShow_0" onclick="showcaseproduct(0)" class="active">
                                                <a data-toggle="tab" aria-expanded="false" href="javascript:void(0)">
                                                    Show All
                                                </a>
                                            </li>

                                            <?php
                                            if ($pcmember_pc_type == 'C') {
                                                $solutionQ = mysqli_query($conn,$a = "SELECT * FROM sp_category WHERE client_id='" . $p_client_id . "' and valid=1 AND deleted=0");
                                            } else {
                                                $solutionQ = mysqli_query($conn,$a = "SELECT * FROM sp_category WHERE client_id='" . $c_lient_Id . "' and valid=1 AND deleted=0");
                                            }
                                            //echo $a;
                                            $i = 1;
                                            while ($solutionQget = mysqli_fetch_array($solutionQ)) {
                                                $catgIMG = str_replace(' ', '-', $solutionQget["it_type"]);
                                                ?>
                                                <li id="activeShow_<?php echo htmlentities($solutionQget["id"]); ?>" onclick="showcaseproduct(<?php echo htmlentities($solutionQget["id"]); ?>)">

                                                    <a data-toggle="tab" aria-expanded="false" href="javascript:void(0)">
                                                        <img style="padding: 0 25px;" id="solutionimg<?php echo htmlentities($solutionQget["id"]); ?>" src="images/<?php echo $imgstartVal[1].'-'.$catgIMG; ?>.png" alt="">
                                                        <img class="activeimg" src="images/<?php echo $imgstartVal[1].'-'.$catgIMG; ?>.png" style="padding: 0 25px;display:none;"> 
                                                        <?php echo htmlentities($solutionQget["it_type"]); ?>
                                                    </a>
                                                </li>

                                                <?php
                                                $i++;
                                            }
                                            ?>

                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">


                                <div class="col-md-6">

                                    <select class="form-control"  onchange="showcasefilter(this.value)">
                                        <option value="">Content Type</option>
                                        <?php
                                        if ($pcmember_pc_type == 'C') {
                                            $ctpQ = mysqli_query($conn,$a = "SELECT * FROM sp_article_type WHERE valid=1 AND deleted=0 and client_id='" . $p_client_id . "' ORDER BY article_type");
                                        } else {
                                            $ctpQ = mysqli_query($conn,$a = "SELECT * FROM sp_article_type WHERE valid=1 AND deleted=0 and client_id='" . $c_lient_Id . "' ORDER BY article_type");
                                        }


                                        while ($ctperow = mysqli_fetch_array($ctpQ)) {
                                            ?>
                                            <option value="<?php echo htmlentities($ctperow["id"]); ?>"><?php echo htmlentities($ctperow["article_type"]); ?></option>
                                        <?php } ?>
                                    </select>


                                </div>        


                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control showcaseSRH" id="showcsearch-name" placeholder="Search by content title" autocomplete="off" onkeyup="ShowcaseSearchKeyup();" width="48" height="48">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
                                    </div>
                                    <div id="showsrch" style="display:none;float: right;margin-right: 354px;margin-top: 13px;"></div>
                                </div>


                            </div>

                        </div>




                        <div class="row" id="showcasefilter">

                            <?php
                            if ($pcmember_pc_type == 'C') {

                                $csres = mysqli_query($conn,$a = "select CS.*,TS.id as syndid, TS.p_client_id, TS.c_client_id, TS.submem_content_publish_url from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='" . $c_lient_Id . "' and CS.approve!='3' and CS.valid=1 and CS.deleted=0 and TS.approve=1 ORDER BY CS.item_order, CS.id desc limit 8");
                            } else {
                                $csres = mysqli_query($conn,$a = "select id,member_id,doe,dou,case_study,video_image,doc_mode,case_study_desc,image_thumb1,crop_Image,case_study_title,case_study_actual_title,content_type,attach_company,item_order,landingpage_status,landingpage_id from sp_case_study where client_id='" . $c_lient_Id . "' and valid=1 and deleted=0 and approve=1 ORDER BY item_order, id desc limit 8");
                            }

                            while ($caseStudy = mysqli_fetch_array($csres)) {
                                $caseStudyId = $caseStudy['id'];
                                $caseStudyItem = $caseStudy['item_order'];
                                $casestudyMember = $caseStudy["member_id"];
                                $caseStudyName = $caseStudy['case_study'];
                                $documentMode = $caseStudy['doc_mode'];
                                $caseLandStatus = $caseStudy['landingpage_status'];
                                $caseLandId = $caseStudy['landingpage_id'];
                                $caseStudyDescription1 = $caseStudy['case_study_desc'];
                                $caseStudyDescription = substr("$caseStudyDescription1", '0', '140') . "..";
                                $castStudyUrl = $caseStudy['case_study_url'];
                                $crop_image = $caseStudy['crop_Image'];



                                if ($caseStudy['video_image'] != '') {
                                    $showcaseThumb = $caseStudy['video_image'];
                                } else {
                                    $showcaseThumb = 'https://' . $_SERVER['HTTP_HOST'] . '/manager/uploads/thumb_img/' . $caseStudy['image_thumb1'];
                                }

                                $caseStudyTitle11 = $caseStudy['case_study_title'];
                                $caseStudyActualTitle = ($caseStudy['case_study_actual_title'] != '') ? $caseStudy['case_study_actual_title'] : $caseStudy['case_study_title'];
                                $csname = str_replace(' ', '-', $caseStudyTitle11);
                                $contentType = $caseStudy['content_type'];
                                if ($contentType != '') {
                                    $contentTypeName = getarticleName($caseStudy['content_type']);
                                }

                                if ($contentType != '') {
                                    $casestd = "select article_type from sp_article_type where id='" . $article . "'";
                                    $casequery = mysqli_query($conn,$casestd);
                                    $caserow = mysqli_fetch_array($casequery);
                                    $articleName = $caserow['article_type'];
                                    $articleId = $caserow['id'];
                                }
                                if ($articleId == $contentType) {
                                    $urlName = $articleName;
                                }

                                $caseStudyTitleLength = strlen($caseStudyActualTitle);
                                $attachforcompany = $caseStudy['attach_company'];
                                $filterFlag = 's';

                                //htaccess title convert
                                $csname = str_replace(' ', '-', $caseStudyTitle11);


                                if ($caseStudyTitleLength > 35) {
                                    $caseStudyTitle = substr($caseStudyActualTitle, '0', '35') . "..";
                                } else {
                                    $caseStudyTitle = $caseStudyActualTitle;
                                }


                                if ($pcmember_pc_type == 'C') {
                                    $cslandquery = mysqli_query($conn,"select LS.*, LP.publish_page_id,LP.landingpage_title,LP.publish_page_name,LP.landingpage_desc,LP.page_title_seo,LP.meta_description from sp_landingpage_publish as LP INNER JOIN sp_landingpage_syndication as LS ON LP.publish_page_id = LS.landingpage_id where LP.publish_page_id='" . $caseLandId . "' and LP.client_id='" . $p_client_id . "' ");
                                } else {
                                    $cslandquery = mysqli_query($conn,"select  publish_page_id,landingpage_title,publish_page_name,landingpage_desc,page_title_seo,meta_description,approve from sp_landingpage_publish
			where publish_page_id='" . $caseLandId . "' and client_id='" . $clientId . "' ");
                                }

                                $cslandget = mysqli_fetch_array($cslandquery);
                                $cslandname = $cslandget['publish_page_name'];
                                $cslandApprove = $cslandget['approve'];
                                ?>	 


                                <div class="col-sm-4" style="margin-top: 20px;">
                                    <div class="item">
                                        <div class="sp-blog-box">
                                            <div class="sp-blog-image clearfix">


                                                <?php
                                                if ($caseLandStatus == 1 && $cslandApprove == 1) {
                                                    ?>
                                                    <a target="_blank" href="<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/landingpage/<?php echo htmlentities($cslandname); ?>">
                                                        <img class="img-fluid center-block" src="<?php echo $showcaseThumb; ?>" alt="<?php echo htmlentities($cslandname); ?>">
                                                    </a>
                                                    <?php
                                                } else {
                                                    ?>  
                                                    <a target="_blank" href="<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/showcase/<?php echo htmlentities($csname); ?>">
                                                        <img class="img-fluid center-block" src="<?php echo htmlentities($showcaseThumb); ?>" alt="<?php echo htmlentities($csname); ?>">
                                                    </a>
                                                    <?php
                                                }
                                                ?>


                                            </div>
                                            <div class="sp-blog-detail" style="height:320px;">
                                                <div class="blog-title"><h6 class="sp-tw-4 sp-mb-10"><?php echo ucfirst(htmlentities($caseStudyTitle)); ?></h6></div>
                                                <div class="blog-content">
                                                    <p><?php echo htmlentities($caseStudyDescription); ?></p>
                                                </div>
                                                <div class="social-icons">
                                                    <ul class="info-share">
                                                        <li>
                                                            <?php
                                                            if ($caseLandStatus == 1 && $cslandApprove == 1) {
                                                                ?>
                                                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/landingpage/<?php echo htmlentities($cslandname); ?>?channel_type=Facebook"><i class="fa fa-facebook"></i></a>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/showcase/<?php echo htmlentities($csname); ?>?channel_type=Facebook"><i class="fa fa-facebook"></i></a>
                                                                <?php
                                                            }
                                                            ?>

                                                        </li>

                                                        <li>
                                                            <?php
                                                            if ($caseLandStatus == 1 && $cslandApprove == 1) {
                                                                ?>
                                                                <a href="https://twitter.com/intent/tweet?text=<?php echo htmlentities($caseStudyDescription); ?>&url=<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/landingpage/<?php echo htmlentities($cslandname); ?>?channel_type=twitter"><i class="fa fa-twitter"></i></a>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <a href="https://twitter.com/intent/tweet?text=<?php echo htmlentities($caseStudyDescription); ?>&url=<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/showcase/<?php echo htmlentities($csname); ?>?channel_type=twitter"><i class="fa fa-twitter"></i></a>
                                                                <?php
                                                            }
                                                            ?>

                                                        </li>


                                                        <li>
                                                            <?php
                                                            if ($caseLandStatus == 1 && $cslandApprove == 1) {
                                                                ?>
                                                                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/landingpage/<?php echo htmlentities($cslandname); ?>?channel_type=linkedin"><i class="fa fa-linkedin"></i></a>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/showcase/<?php echo htmlentities($csname); ?>?channel_type=linkedin"><i class="fa fa-linkedin"></i></a>
                                                                <?php
                                                            }
                                                            ?>

                                                        </li>
                                                        <li>
                                                            <?php
                                                            if ($caseLandStatus == 1 && $cslandApprove == 1) {
                                                                ?>
                                                                <a href="https://plus.google.com/share?url=<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/landingpage/<?php echo htmlentities($cslandname); ?>?channel_type=googleplus"><i class="fa fa-google"></i></a>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <a href="https://plus.google.com/share?url=<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/showcase/<?php echo htmlentities($csname); ?>?channel_type=googleplus"><i class="fa fa-google"></i></a>
                                                                <?php
                                                            }
                                                            ?>

                                                        </li>

                                                        <li>
                                                            <?php
                                                            if ($caseLandStatus == 1 && $cslandApprove == 1) {
                                                                ?>
                                                                <a onclick="whatsappShare();" style="cursor: pointer;" data-link="<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/landingpage/<?php echo htmlentities($cslandname); ?>?channel_type=whatsapp"><i class="fa fa-whatsapp"></i></a>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <a onclick="whatsappShare();" style="cursor: pointer;" data-link="<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/showcase/<?php echo htmlentities($csname); ?>?channel_type=whatsapp"><i class="fa fa-whatsapp"></i></a>
                                                                <?php
                                                            }
                                                            ?>

                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="sp-blog-meta">
                                                    <ul class="list-inline">
                                                        <!--<li class="list-inline-item"><a href="javascript:void(0);"><i class="fa fa-edit"></i>&nbsp;<?php //echo $contentTypeName;    ?></a></li>-->
                                                        <li class="list-inline-item"><i class="fa fa-calendar"></i>&nbsp;<?php
                                                    if (strtotime($caseStudy['dou']) == '62169955200 ' || strtotime($caseStudy['dou']) == 'FALSE') {
                                                        echo date('l jS \of F Y', strtotime($caseStudy['doe']));
                                                    } else {
                                                        echo date('l jS \of F Y', strtotime($caseStudy['dou']));
                                                    }
                                                            ?></li>
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


                <div id="sp-contact" class="contact-us <?php echo $newMicro ?>">
                    <div class="row no-gutters">
                        <div class="col-lg-6 col-md-12">
                            <?php if ($micrositeDetails['microsite_address'] != '') {
                                ?>

                                <span id="address" style="display:none;"><?php echo htmlentities($micrositeDetails['microsite_address']); ?></span>
                                <iframe class="map" id="map" style="border:0" allowfullscreen=""></iframe>
                            <?php } else { ?>

                                <span id="address" style="display:none;">Aditya Birla Capital Limited, Tulsi Pipe Road, Babasaheb Ambedkar Nagar, Lower Parel, Mumbai, Maharashtra</span>
                                <iframe class="map" id="map" style="border:0" allowfullscreen=""></iframe>
                            <?php } ?>
                        </div>
                        <div class="col-lg-6 col-md-12 align-self-center">
                            <div class="sp-mlr-60 sp-ptb-80">
                                <div class="heading-title left">
                                    <h5 class="sp-tw-6">Get in Touch</h5>
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
                                            <input class="require" id="number-339" type="text" placeholder="Phone*" name="phone" maxlength="13">
                                        </div>
                                        <div class="section-field textarea">
                                            <textarea id="contact_message" class="input-message require" placeholder="Comment*" rows="5" name="message"></textarea>
                                        </div>
                                        </br>
                                        </br>
                                        <div class="sp-mt-20 term-set">
                                            <span class="understand"><input type="checkbox" id="termCheck" name="termCheck" value="1"> 
                                                <!--
                                                                                I understand that I would be contacted with regards to this request placed and consent for the same in spite of being registered with the National Customer Preference Registry (NCPR) with TRAI. I understand that there is a de-registration facility ( for not receiving such calls) which I may avail if required in future.
                                                                                
                                                -->
                                                I confirm, agree and accept the  <a href="JavaScript:void(0);" style="color:#1683c2" data-toggle="modal" data-target="#termCodition"><strong>Terms and conditions</strong></a> applicable. 




                                            </span>
                                        </div>
                                        <div class="section-field sp-mt-20">

                                        </div>
                                        <div id="formbuilder_lead" style="display:none;font-size:16px;color:red;"></div>
                                        <button name="submit" type="submit" value="Send" style="background:<?php echo htmlentities($QrySelectget['form_textcolor']); ?>; border-color: <?php echo htmlentities($QrySelectget['form_textcolor']); ?>;" onclick="return submit_formbuilder();" id="send-value" class="button sp-mt-15">Send Message</button>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- === contact-us END=== -->

            </div>
            <!-- === Main Content End === --></div></div>




    <?php
    if ($QrySelectget['disclaimer_tag'] == 1) {
        ?>
        <div ui-view="footer" class="ng-scope">
            <footer class="dark-bg ng-scope" style="background:<?php echo $QrySelectget['disclaimer_color']; ?>">
                <div class="sp-footer sp-pt-70 sp-pb-20">
                    <div class="container">
                        <div class="row overview-block-ptb2">
                            <div class="col-lg-12 col-md-12 sp-mtb-20">
                                <div class="logo">

                                    <div class="sp-mt-15 sp-mr-60" style="color: #7f7e7f; font-size: 16px; margin-top: 10px; padding: 20px">
        <?php echo $QrySelectget['disclaimer_content']; ?>

                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

            </footer>

        </div>

    <?php } ?>
    <div ui-view="footer" class="ng-scope">
        <footer class="dark-bg ng-scope" style="background:<?php echo htmlentities($QrySelectget['footerColor']); ?>;">
            <div class="sp-footer sp-pt-70 sp-pb-20" id='sp-contact1'>
                <div class="container">
                    <div class="row overview-block-ptb2">
                        <div class="col-lg-6 col-md-12 sp-mtb-20">
                            <div class="logo" <?php echo $newMicro; ?>>

                                <div class="sp-font-white sp-mt-15 sp-mr-60">

                                    <?php
                                    if ($QrySelectget['microsite_about'] != '') {
                                        echo htmlentities($QrySelectget['microsite_about']);
                                    } else {

                                        echo htmlentities($micrositeDetails['microsite_about']);
                                    }
                                    ?> 


                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 sp-mtb-20">
                            <ul class="menu">
                                <li><a href="#sp-banner">Home</a></li>
                                <li><a href="#sp-blog">Content</a></li>
                                <li><a class="<?php echo $newMicro ?>" href="#sp-about">About Us</a></li>
                                <li>
                                    <?php
                                    if ($newMicro == 'hidden') {
                                        echo '<a class="" href="#sp-contact1">Contact</a>';
                                    } else {
                                        echo '<a class="" href="#sp-contact1">Contact</a>';
                                    }
                                    ?>
                                </li>
                            </ul>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 sp-mtb-20">
                            <div class="contact-bg">
                                <ul class="sp-contact">
                                    <li class="<?php echo $newMicro ; ?>">
                                        <i class="fa fa-map-marker"></i>
                                        <?php if ($QrySelectget['microsite_address'] != '') { ?>
                                            <p><?php echo htmlentities($QrySelectget['microsite_address']); ?></p>
                                        <?php } else { ?>
                                            <p>One Indiabulls Centre Tower 1, 16th Floor, Jupiter Mill Compound, 841, Senapati Bapat Marg, Elphinstone Road, Mumbai - 400013</p>
                                        <?php } ?>
                                    </li>
                                    <li>
                                        <i class="fa fa-phone"></i>
                                        <p><?php //echo htmlentities($pcomp_row['person_contact1']);    ?>

                                            <?php echo htmlentities($firstPhone); ?><span><span></span></span><?php echo htmlentities($secondPhone); ?><span><span></span></span>

                                        </p>
                                    </li>
                                    <li>
                                        <i class="fa fa-envelope"></i>

                                        <p style="word-break: break-all;"><a href="javascript:void(0);" class="mailcrypt"><?php echo htmlentities($emailExplode[0]); ?><span><span>∂</span></span><?php echo htmlentities($emailExplode[1]); ?></a></p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="copyright-box">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                           <img src="company_logo/<?php echo htmlentities($QrySelectget['mrc_footerLogo']); ?>" class="img-fluid" alt="">
                            <a href="policy.php">Privacy Policy and Terms & Conditions</a>
                        </div>
                        <div class="col-md-6 col-sm-12 text-right">
                            <ul class="info-share">
                                <li><a href=""><i class="fa fa-twitter"></i></a></li>
                                <li><a href=""><i class="fa fa-facebook"></i></a></li>
                                <li><a href=""><i class="fa fa-google"></i></a></li>
                                <li><a href=""><i class="fa fa-linkedin"></i></a></li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </footer>

    </div>



    </div>
    </div>
    </div>

    <div id="termCodition" class="modal fade" role="dialog" style="display:none; padding-top:5%;">
        <div class="modal-dialog" style="width:60%!important;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">Ã—</button>
                    <h4 class="modal-title" style="font-size:20px;font-weight:bold;"><strong>Disclaimer</strong></h4>
                </div>
                <div class="modal-body">

                    Before agreeing to share any information, data or any other details on this website/webpage (â€œ<strong>Site</strong>â€?), the visitor (â€œ<strong>Visitor</strong>â€?) to please note that this is a Site of the distributor (â€œ<strong>Distributor</strong>â€?) who is empanelled and registered with HDFC Asset Management Company Limited (â€œ<strong>AMC</strong>â€?)/HDFC Mutual Fund (â€œ<strong>MF</strong>â€?)/ HDFC Trustee Company Limited (â€œ<strong>Trustee</strong>â€?) and is not a Site of the AMC/ MF/ Trustee. Please also note that this Site is being hosted by a third-party provider viz. Bizight Solutions Private Limited (â€œ<strong>Bizight</strong>â€?). The Privacy Policy of the respective Distributor as given on this Site shall apply to the Visitor. 
                    </br>It shall be at the sole discretion of the Visitor to avail of the services being offered by the Distributor on this Site and the Visitor may choose not to avail of the same. In case of any queries/complaints/grievances including with regard to the products/services being provided, the Visitor may contact the Distributor directly and the AMC/ MF/ Trustee shall not be responsible or liable for any misrepresentation or fraud or any compromise of the Visitorâ€™s information by the Distributor or Bizight. Please note that all electronic medium including this Site could be susceptible to security breach, data theft, frauds, system failures, none of which shall be the responsibility of the AMC/ MF/ Trustee. The interactions, requests, transactions and services availed by the Visitor shall be at his/her own risk and assessment. 
                    </br>Mutual Fund investments are subject to market risk and are governed by the terms of the scheme related documents. The Visitor should consult his/her legal/financial/tax advisors before making any investment decisions. Further the AMC /MF/ Trustee and their representatives, accept no responsibility of contents which are incorporated by the Distributor for the convenience of the Visitor. 

                </div>
            </div>
        </div>
    </div>

    <div id="popup-cookie-2" class="cookies-popup cookies-show bottom-slide-cookies" data-position="bottom-center" data-animation="slide-up">
        <div class="row gap-y align-items-center">
            <div class="col-md">
                This website uses cookies so that we can provide you with the best user experience. By continuing using our website you accept our use of cookies. Further details can be found in our <a target="_blank" href="policy.php">Privacy Policy</a>
            </div>

            <div class="col-md-auto">
                <button class="btn-warning" data-dismiss="cookies-popup" id="declarationbtn">I Accept</button>
            </div>
        </div>
    </div>

    <script>
    //$.noConflict();
        $(document).ready(function ($) {
            var prefix = "+91";
            $('#number-339').val(prefix);
            $('[data-toggle="tooltip"]').tooltip();
            $('.mailcrypt').mailcrypt();
            $("a[href='#sp-contact'],a[href='#sp-about'],a[href='#sp-blog'],a[href='#sp-banner'],a[href='#categories']").on('click', function (event) {
                if (this.hash !== "")
                {
                    event.preventDefault();
                    var hash = this.hash;
                    $('html, body').animate({
                        scrollTop: $(hash).offset().top
                    }, 900, function () {

                        window.location.hash = hash;
                    });
                }
            });

            var q = encodeURIComponent($('#address').text());
            $('#map')
                    .attr('src',
                            'https://www.google.com/maps/embed/v1/place?key=AIzaSyAsIbU83moc8MIocD2dkhaCU90GAoGSspU&q=' + q);


            var ctr = jQuery.noConflict();
            ctr("#activeShow_407").addClass('active').siblings().removeClass('active');
            var imgsol = ctr(this).attr("src");
            var SolutionType = '0';
            var c_lient_Id = '<?php echo $c_lient_Id; ?>';
            var p_client_id = '<?php echo $p_client_id; ?>';
            var pcType = '<?php echo $pcmember_pc_type; ?>';
            var siteURL = '<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>';
            var beetle=$('input[name="beetle"]').val();
            ctr.ajax({url: "<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/sp-showcasesolution.php",
                type: "post",
                data: {beetle: beetle,c_lient_Id: c_lient_Id, p_client_id: p_client_id, pcType: pcType, SolutionType: SolutionType, siteURL: siteURL},
                cache: false,
                crossDomain: true,
                beforeSend: function ()
                {
                    //ctr('#showcasefilter').html('<div class="loader"></div>');
                },
                success: function (result)
                {
                    //alert(result)
                    ctr("#showcasefilter").html(result);
                }
            });




        })




        function submit_formbuilder()
        {
            var frm = jQuery.noConflict();
            var c_lient_Id = '<?php echo $c_lient_Id; ?>';
            var full_name = frm("#your-name").val();
            var email = frm("#your-email").val();
            var contact = frm("#number-339").val();
            var form_refer = "Microsite request page";
            var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
            var intRegex = /[0-9 -()+]+$/;
            var beetle=frm('input[name="beetle"]').val();

            var isValid = true;
            frm("#your-email").each(function () {
                if (frm.trim(frm(this).val()) == '' || (!emailReg.test(email))) {
                    isValid = false;
                    frm(this).css({
                        "border": "1px solid red",
                    });
                } else {

                    frm(this).css({
                        "border": "",
                        "background": ""
                    });
                }
            });



            frm("#your-name").each(function () {
                if (frm.trim(frm(this).val()) == '') {
                    isValid = false;
                    frm(this).css({
                        "border": "1px solid red",
                    });
                } else {
                    frm(this).css({
                        "border": "",
                        "background": ""
                    });
                }
            });





            frm("#number-339").each(function () {
                if (frm.trim(frm(this).val()) == '' || (!intRegex.test(contact))) {
                    isValid = false;
                    frm(this).css({
                        "border": "1px solid red",
                    });
                } else {
                    frm(this).css({
                        "border": "",
                        "background": ""
                    });
                }
            });
            frm("#termCheck").each(function () {
                var getCheck = frm.trim(frm(this).prop('checked'));
                // alert($("#termCheck:checked" ).length);
                // alert($("#termCheck:checked" ).length);
                // $('input[type=checkbox]').attr('checked');
                if (getCheck == 'false')
                {
                    isValid = false;
                    frm("#formbuilder_lead").show().text('Please click on the check box to proceed.').fadeIn(300).delay(3000).fadeOut(800);
                    frm(this).parent('.section-field').css({
                        "border": "1px solid red",
                    });
                } else {
                    frm(this).parent('.section-field').css({
                        "border": "",
                        "background": ""
                    });
                }
            });
            if (isValid == false)
            {
                return false;
            } else
            {

                frm.ajax({url: "<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/sp-formbuilder-submit.php",
                    type: "post",
                    data: {beetle: beetle,full_name1: full_name, email1: email, c_lient_Id: c_lient_Id, form_refer: form_refer, contact1: contact},
                    cache: false,
                    crossDomain: true,
                    beforeSend: function () {
                        frm('#send-value').attr("disabled", true).val('Please wait ...');
                    },
                    success: function (result) {

                        frm("#formbuilder_lead").show().text('Thank you for submitting. We will get back to you soon.').fadeIn(300).delay(3000).fadeOut(800);
                        frm('#send-value').attr("disabled", false).val("Submit");
                        window.setTimeout(function () {
                            window.location.reload();
                        }, 2000);

                    }
                });
            }
        }


        function showcasefilter(ContentType)
        {

            var ctr = jQuery.noConflict();
            ctr("#activeShow_" + ContentType).addClass('active').siblings().removeClass('active');
            var c_lient_Id = '<?php echo $c_lient_Id; ?>';
            var p_client_id = '<?php echo $p_client_id; ?>';
            var pcType = '<?php echo $pcmember_pc_type; ?>';
            var siteURL = '<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>';
             var beetle=ctr('input[name="beetle"]').val();
            ctr.ajax({url: "<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/sp-showcasefilter.php",
                type: "post",
                data: {beetle: beetle,c_lient_Id: c_lient_Id, p_client_id: p_client_id, pcType: pcType, ContentType: ContentType, siteURL: siteURL},
                cache: false,
                crossDomain: true,
                beforeSend: function ()
                {
                    //ctr('#showcasefilter').html('<div class="loader"></div>');
                },
                success: function (result)
                {

                    ctr("#showcasefilter").html(result);
                }
            });

        }




        function showcaseproduct(SolutionType)
        {
            var ctr = jQuery.noConflict();
            ctr("#activeShow_" + SolutionType).addClass('active').siblings().removeClass('active');
            var imgsol = ctr(this).attr("src");

            var c_lient_Id = '<?php echo $c_lient_Id; ?>';
            var p_client_id = '<?php echo $p_client_id; ?>';
            var pcType = '<?php echo $pcmember_pc_type; ?>';
            var siteURL = '<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>';
            var beetle=ctr('input[name="beetle"]').val();
            ctr.ajax({url: "<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/sp-showcasesolution.php",
                type: "post",
                data: {beetle: beetle,c_lient_Id: c_lient_Id, p_client_id: p_client_id, pcType: pcType, SolutionType: SolutionType, siteURL: siteURL},
                cache: false,
                crossDomain: true,
                beforeSend: function ()
                {

                    ctr('#showcasefilter').html('<div id="loader"></div>');
                },
                success: function (result)
                {
                    //alert(result);
                    ctr("#showcasefilter").html(result);
                }
            });

        }

        function ShowcaseSearchKeyup()
        {
            var srch = jQuery.noConflict();
            var showsearchName = srch("#showcsearch-name").val();
            var c_lient_Id = '<?php echo $c_lient_Id; ?>';
            var p_client_id = '<?php echo $p_client_id; ?>';
            var pcType = '<?php echo $pcmember_pc_type; ?>';
            var beetle=srch('input[name="beetle"]').val();
            srch.ajax({
                type: "POST",
                url: "<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/get_document_title_list.php",
                data: {beetle: beetle,showsearchName: showsearchName, c_lient_Id: c_lient_Id, p_client_id: p_client_id, pcType: pcType},
                beforeSend: function () {
                    srch("#showcsearch-name").css("background", "#FFF url(images/LoaderIcon.gif) no-repeat 240px");

                },
                success: function (data)
                {
                    srch("#showsrch").html(data).show();
                    srch("#showcsearch-name").css("background", "#FFF");
                }
            });
        }



        function srchshowcaseClick(val1)
        {
            var srchclick = jQuery.noConflict();
            srchclick("#showcsearch-name").val(val1)
            window.location.href = '<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/showcase/' + window.encodeURIComponent(val1) + '';
        }


        function whatsappShare()
        {
            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                var weburl = $(this).attr("data-link");

                var whats_app_message = encodeURIComponent(weburl);
                var whatsapp_url = "whatsapp://send?text=" + whats_app_message;
                window.location.href = whatsapp_url;

            } else
            {
                alert('Whatsapp sharing is only available through mobile.');
            }
        }





    </script>

    <script>
        var microsite = 1;</script>
        <?php include("includes/footer-event.php"); ?>

    <script type="text/javascript">

        (function () {

            document.getElementById("declarationbtn").onclick = function () {
                setCookieV2("declarationCookies", 1, 1000);
                var r = getCookie('declarationCookies');
                document.getElementById("popup-cookie-2").style.display = "none";

            }
            var k = getCookie('declarationCookies');
            if (k == 1)
            {
                document.getElementById("popup-cookie-2").style.display = "none";

            }
        })();




    </script>

    <!-- Script For Chatbot start here-->

    <?php

    function url() {
        return sprintf(
                "%s://%s", isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http', $_SERVER['SERVER_NAME']
        );
    }

    $baseUrl = url();
   
    
     if (chatbot_color1!='' && chatbot_color2!='' && chatbot_color!='' && chatbot_color!='') {
        $blue = chatbot_color1;
        $text_color = chatbot_color2;
        $chat_1 = chatbot_color3;
        $chat_2 = chatbot_color4;
    } else {
        $blue = "#C7222A";
        $text_color = "#4E4E4E";
        $chat_1 = "#eaf0f6";
        $chat_2 = "#f2f2f2";
    }
    
    if (strstr($baseUrl, 'mutualfundpartner.com') == true || 1 == 1) {
        ?>

        <section chat__bot class="chat__bot">
            <div role="chat__bot_head">SalesPanda Bot
                <span>
                    <a href="#" onclick="chatbotData('', '', 1);"><img src="<?php echo $baseUrl; ?>/chatbot/img/refresh.svg" alt="" /></a>
                    <i class="close1"><img src="<?php echo $baseUrl; ?>/chatbot/img/cancel-music.svg" alt="" /></i>
                </span>
            </div>
            <div role="chat__bot_body" class="chat__bot_body">

            </div>
        </section>

        <div class="css-rkaeus">
            <div class="css-fvs20o e2ujk8f2"><svg width="14" height="14">
                <path
                    d="M13.978 12.637l-1.341 1.341L6.989 8.33l-5.648 5.648L0 12.637l5.648-5.648L0 1.341 1.341 0l5.648 5.648L12.637 0l1.341 1.341L8.33 6.989l5.648 5.648z"
                    fill-rule="evenodd"></path>
                </svg></div>
            <div class="css-dlvlg4 e2ujk8f1"><svg viewBox="0 0 28 32">
                <path
                    d="M28,32 C28,32 23.2863266,30.1450667 19.4727818,28.6592 L3.43749107,28.6592 C1.53921989,28.6592 0,27.0272 0,25.0144 L0,3.6448 C0,1.632 1.53921989,0 3.43749107,0 L24.5615088,0 C26.45978,0 27.9989999,1.632 27.9989999,3.6448 L27.9989999,22.0490667 L28,22.0490667 L28,32 Z M23.8614088,20.0181333 C23.5309223,19.6105242 22.9540812,19.5633836 22.5692242,19.9125333 C22.5392199,19.9392 19.5537934,22.5941333 13.9989999,22.5941333 C8.51321617,22.5941333 5.48178311,19.9584 5.4277754,19.9104 C5.04295119,19.5629428 4.46760991,19.6105095 4.13759108,20.0170667 C3.97913051,20.2124916 3.9004494,20.4673395 3.91904357,20.7249415 C3.93763774,20.9825435 4.05196575,21.2215447 4.23660523,21.3888 C4.37862552,21.5168 7.77411059,24.5386667 13.9989999,24.5386667 C20.2248893,24.5386667 23.6203743,21.5168 23.7623946,21.3888 C23.9467342,21.2215726 24.0608642,20.9827905 24.0794539,20.7254507 C24.0980436,20.4681109 24.0195551,20.2135019 23.8614088,20.0181333 Z">
                </path>
                </svg></div>
        </div>


        <!-- Trigger/Open The Modal -->  
        <link href="<?php echo $baseUrl; ?>/chatbot/css/style1.css" rel="stylesheet">
        <style>

            :root {
                --blue: <?php echo $blue; ?>;
                --text_color: <?php echo $text_color; ?>;
                --chat_1: <?php echo $chat_1; ?>;
                --chat_2: <?php echo $chat_2; ?>;
                --font:'Open Sans', sans-serif; "Calibri";
            }

        </style>
        <link href="<?php echo $baseUrl; ?>/chatbot/css/owl.carousel.min.css" rel="stylesheet">
        <link href="https://aalmiray.github.io/ikonli/css/themify-icons.min.css" rel="stylesheet">
        <script src="<?php echo $baseUrl; ?>/chatbot/js/owl.carousel.min.js"></script>





        <div id="myModal" class="modal right" role="chat__modal">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="close-header">&times;</span>
                    <h2 id="model_header">Modal Header</h2>
                </div>
                <div class="modal-body">
                    <div id="myCarousel1" class="carousel slide"  style="width: 100%;margin: 0 auto">
                        <div class="modal-body">&nbsp;</div>
                    </div>
                </div>
            </div>
        </div>



        <script src="chatbot/js/owl.carousel.js"></script> 
        <script>
        <?php require("chatbot/js/comman_function1.php"); ?>

        </script> 
    <?php } ?>


    </body></html>

    <?php
} else {
    header("location:microsite-notfound.php");
}
?>
