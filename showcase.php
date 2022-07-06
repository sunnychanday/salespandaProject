<?php
/* Author name: <Bizight Solutions Pvt Ltd> 
 * Purpose of document/ page : <This is page is used to show the content libarary data on microsite.> 
 * Date: 17-02-2021 
 * Copyright info : <Copyright @2021, Bizight Solutions Pvt Ltd>
 */

$https_status = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on')) ? "https://" : "http://";
if ($https_status == "http://") {
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
    exit;
}

session_start();
header('Access-Control-Allow-Origin: true');

require realpath(__DIR__ . '/vendor/autoload.php');
include("includes/global.php");

$lang = "en";
if(isset($_REQUEST['lang']) && trim($_REQUEST['lang']) != ""){
	$lang = trim($_REQUEST['lang']);
}

if (MICRO_SITE_DESIGN) {
    include("showcasenew.php");
    exit;
}
include("includes/function.php");
include("manager/common_functions.php");
include("csrf/csrf-magic.php");

/*
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  // */

require_once 'includes/connect-new.php';

$obj = new \Microsite\Microsite($connPDO);
if ($obj->microsite_exists === false) {
    header("location: microsite-notfound.php");
    exit;
}

$c_lient_Id = $obj->client_id;
$p_client_id = $obj->parent_id;
$pcmember_pc_type = $obj->account_type;

/* Trackable URL Related Variables */
$track_url_params = CommonStaticFunctions::get_track_url_params($https_status . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", $conn);
$download_attachment_link =  0;
$source = 'Document Popup';
if(isset($track_url_params['attachment']) && !empty($track_url_params['attachment'])){
    $download_attachment_link = 1;
    $source = 'Download Link';
}
$concat_request_url = $track_url_params['request_uri'] ?? '';
$channel_type = $track_url_params['channel_type'] ?? '';
$camp_id = $track_url_params['camp_id'] ?? 0;
$contentType = $track_url_params['content'] ?? '';
$contact_id  = $track_url_params['c'] ?? 0;
$mofficialEMail = ($track_url_params['semail']) ?? '';
$mfName = $track_url_params['contact_details']['first_name'] ?? '';
$mlName = $track_url_params['contact_details']['last_name'] ?? '';
$mPhone = $track_url_params['contact_details']['mobile'] ?? '';
/*--------------------------------------------------------------------------*/

$mCompname = $buttonProprty = $keyName = $industryName = $defaultwebPath = '';
if (empty($c_lient_Id)) {
    $subdomainqry = mysqli_query($conn, "select userid,client_id, client_id, cms_subdomain_url from sp_subdomain where cms_subdomain_url = '{$_SERVER['HTTP_HOST']}' and valid = 1 and deleted = 0 and status = 1");
    $subdomainget = mysqli_fetch_assoc($subdomainqry);
    $c_lient_Id = $subdomainget['client_id'];
    $sdomainPath = $subdomainget['subdomain_url'];
    $redirectPath = $https_status . $sdomainPath;
    $user_id = $subdomainget['userid'];
    $weburl = $https_status . $subdomainget['cms_subdomain_url'];
} else {
    $subdomainqry = mysqli_query($conn, "select userid,client_id, client_id, cms_subdomain_url from sp_subdomain where client_id = '{$c_lient_Id}' and valid = 1 and deleted = 0 and status = 1");
    $subdomainget = mysqli_fetch_assoc($subdomainqry);
    $c_lient_Id = $subdomainget['client_id'];
    $user_id = $subdomainget['userid'];
    $sdomainPath = isset($subdomainget['subdomain_url']) ? $subdomainget['subdomain_url'] : '';
    $redirectPath = $https_status . $sdomainPath;

    $weburl = $https_status . $subdomainget['cms_subdomain_url'];
}

$reqMsg = explode('?reqMsg=', $_SERVER['REQUEST_URI']);
if (count($reqMsg) > 1)
    $reqMsg = urldecode($reqMsg[1]);

if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '')
    $ref_url = $_SERVER['HTTP_REFERER'];

$pc_member_info = getPCMemberInfo($c_lient_Id);
$pcmember_pc_type = $pc_member_info['member_pc_type'];
$p_client_id = $pc_member_info['p_client_id'];

$pcompqry = "select pid, comp_id, person_email, first_name, last_name, person_contact1, person_contact2 from sp_members where client_id = '{$c_lient_Id}' and valid = 1 and deleted = 0 and approve = 1 and company_member_type = 1";
$pcompres = mysqli_query($conn, $pcompqry);
$pcomp_row = mysqli_fetch_array($pcompres);
$p_comp_id = $pcomp_row['comp_id'];

$emailExplode = explode('@', $pcomp_row['person_email']);
$ext = substr($pcomp_row['person_contact1'], 0, -10);

$firstPhone = substr($pcomp_row['person_contact1'], strlen($pcomp_row['person_contact1']) - 10, -6);
$secondPhone = substr($pcomp_row['person_contact1'], strlen($pcomp_row['person_contact1']) - 6);

$res = mysqli_query($conn, $q = "select * from sp_company where comp_id = '{$p_comp_id}' and valid = 1 and deleted = 0");
$lData = mysqli_fetch_array($res);
$aboutcmpny = $lData['about_company'];
$Namecmpny = $lData['company_name'];
$faviconimg = $lData['favicon'];
$company_logo = $lData['header_logo'];
$about_me_font = $lData['about_me_font'] ?? 0;
$solution_type_font = $lData['solution_type_font'] ?? 0;
$header_bg_image = $lData['header_bg_image'] ?? '';
$parent_data = $obj->get_client_detail($p_client_id);
if (isset($parent_data) && !empty($parent_data)) {
    $faviconimg = $parent_data['favicon'];
    $company_logo = $parent_data['header_logo'];
    $about_me_font = $parent_data['about_me_font'];
    $solution_type_font = $parent_data['solution_type_font'];
    $header_bg_image = $parent_data['header_bg_image'];
}
$header_bg_imageclass = ($header_bg_image != '') ? "background-image: url('/images/" . $header_bg_image . "') !important; background-repeat: repeat;" : "";
$about_me_font = ($about_me_font == 0) ? "font-weight:normal;" : "";
$solution_type_font = ($solution_type_font == 0) ? "font-weight:normal;" : "";


$QrySelect = "select * from sp_microsite where client_id = '{$c_lient_Id}'";
mysqli_set_charset($conn, 'utf8');
$QrySelectset = mysqli_query($conn, $QrySelect);
$QryselectCount = mysqli_num_rows($QrySelectset);
$QrySelectget = mysqli_fetch_array($QrySelectset);

$page_hitssk = $https_status . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
$ip = CommonStaticFunctions::get_remote_user_ip();
$agent = $_SERVER['HTTP_USER_AGENT'];
$urlval = $_SERVER['REQUEST_URI'];

$urlvalexp = explode("/", $urlval);

$url_cstdy = $urlvalexp[2];

$url_cstdy_tw = explode("?", $url_cstdy);

$url_cstdy_tt = urldecode($url_cstdy_tw[0]);

$caseStudyName = str_replace("-", " ", $url_cstdy_tt);

$caseStudyName = urldecode($caseStudyName);

$url1 = $weburl . "/showcase/" . $url_cstdy_tt. $concat_request_url;

if ($pcmember_pc_type == 'C') {
    $csdquery = mysqli_query($conn, $t = "select CS.id, CS.case_study_title,CS.category,CS.case_study_desc, TS.id as syndid from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id = CS.id where TS.c_client_id = '{$c_lient_Id}' and CS.valid = 1 and CS.deleted = 0 and TS.approve = 1 and (CS.case_study_title = '" . mysqli_real_escape_string($conn, $caseStudyName) . "' or CS.case_study_title = '" . mysqli_real_escape_string($conn, $url_cstdy_tt) . "')");
} else {
    $csdquery = mysqli_query($conn, $t = "select id, case_study_title,case_study_desc,category from sp_case_study where client_id = '{$c_lient_Id}' and approve = 1 and valid = 1 and deleted = 0 and (case_study_title = '" . mysqli_real_escape_string($conn, $caseStudyName) . "' or case_study_title = '" . mysqli_real_escape_string($conn, $url_cstdy_tt) . "')");
}

$csdrow = mysqli_fetch_array($csdquery);

$caseId = $csdrow['id'];
$caseSTitle = str_replace(" ", "-", $csdrow['case_study_title']);

$leadgroupId = isset($_REQUEST['lg']) ? $_REQUEST['lg'] : '';
if ($leadgroupId != '') {
    $resld = mysqli_query($conn, "select lead_group_id from sp_lead_generate where valid = 1 and deleted = 0");
    $leadCount = mysqli_num_rows($resld);
}

if ($pcmember_pc_type == 'C') {
    $cres = mysqli_query($conn, "select CS.*, TS.id as syndid, TS.p_client_id, TS.c_client_id, TS.submem_content_publish_url FROM sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id = CS.id where TS.case_id = '{$caseId}' and TS.c_client_id = '{$c_lient_Id}' and CS.valid = 1 and CS.deleted = 0 and TS.approve = 1");
} else {
    $cres = mysqli_query($conn, "select * FROM sp_case_study where id = '{$caseId}' and client_id = '{$c_lient_Id}' and approve = 1 and valid = 1 and deleted = 0");
}

$casecount = mysqli_num_rows($cres);
if ($casecount != 0) {
    $caseData = mysqli_fetch_assoc($cres);
    $caseStudyFile = $caseData['case_study'];
    $cs_mode = $caseData['cs_mode'];
    $pcaseStudyApprove = $caseData['approve'];
    $template_id = $caseData['case_study_library'];
    $caseStudyUrl = isset($caseData['case_study_url']) ? $caseData['case_study_url'] : '';
    $referenceSite_Link = isset($caseData['case_study_url']) ? $caseData['case_study_url'] : '';
    $thumbimage = $caseData['image_thumb1'];
    $cropImage = $caseData['crop_Image'];
    $caseStudyContent = $caseData['case_study_content'];
    $caseStudytitle = $caseData['case_study_title'];
    $caseStudyActualtitle = ($caseData['case_study_actual_title'] != '') ? $caseData['case_study_actual_title'] : $caseData['case_study_title'];
    $caseStudyDesc1 = $caseData['case_study_desc'];
    $caseStudyDesc = str_replace("\\", "", $caseStudyDesc1);

    $caseStudyDescforGA = substr($caseStudyDesc, 0, 165); //for Google Analytics DESCRIPTION
    $casestudyMember = $caseData['member_id'];
    $contentType = $caseData['content_type'];
    $docTypeName = ucwords(getarticleName($contentType));
    $docuType = str_replace(' ', '-', $docTypeName);

    $facebook_image = $caseData['facebook_image'];
    $facebook_desc = $caseData['facebook_desc'];
    $facebook_title = $caseData['facebook_title'];

    $solution = explode(',', $caseData["category"]);
    $industry = explode(',', $caseData["vertical"]);
    if ($industry != '') {
        foreach ($industry as $val) {
            $industryName .= segmentName($val) . ', ';
        }
    }

    $industry_name = substr($industryName, 0, -2);

    $keyword = $caseData['category_keyword'];
    $solkey = explode(',', $keyword);
    if ($solkey != '') {
        foreach ($solkey as $keyval) {
            $keyName .= keywordName($keyval) . ', ';
        }
    }

    $solKeyword = substr($keyName, 0, -2);
    $metaDescription = $caseData['meta_description'];
    $calltoaction = $caseData['call_to_action'];

    if ($calltoaction != '') {
        if ($pcmember_pc_type == 'C') {
            $qryscp = "select * from cta_button where id = '{$calltoaction}' and client_id = '{$p_client_id}' and valid = 1 and deleted = 0";
        } else {
            $qryscp = "select * from cta_button where id = '{$calltoaction}' and client_id = '{$c_lient_Id}' and valid = 1 and deleted = 0";
        }

        $resp = mysqli_query($conn, $qryscp);
        $scpData = mysqli_fetch_array($resp);
        $ctaurl211 = $scpData['button_script'];
        $scrptDetail12 = str_replace("<q>", "'", $ctaurl211);
        $scrptDetail14 = str_replace('&quot;', '"', $scrptDetail12);
        $cta_name = trim($scpData['ctaName']);
        $scrptDetail13 = str_replace('"$"', '$', $scrptDetail14);
        $ctaType = $scpData['ctaType'];

        $btncolor = $scpData['buttonColor'];
        $btnLbl = $scpData['buttonLabel'];
        $btnbgColor = $scpData['btn_background_color'];
        $btntextcolor = $scpData['btn_text_color'];


        $bqry = "select * from cta_type where id='" . $ctaType . "'";
        $resb = mysqli_query($conn, $bqry);
        $btntpdata = mysqli_fetch_array($resb);
        $buttonProprty = $btntpdata['required_url'];
    }

    $verifybyMail = $caseData['verify_by_email'];
    $pageTitle = $caseData['page_title_seo'];
    $entrydate = dateFormat1($caseData['doe']);
}
mysqli_set_charset($conn, 'utf8');
if ($pcmember_pc_type == 'C') {
    $query_content_preview = mysqli_query($conn, $q1 = "select T.content_file, T.showcasepdfImage, T.video_file, T.cobrand, T.VideoId, TS.id as synid from user_templates as T INNER JOIN sp_template_syndication as TS ON  T.template_id = TS.template_id where T.template_id = '{$template_id}' and TS.c_client_id = '{$c_lient_Id}' and T.valid = 1 and T.deleted = 0 and TS.approve = 1");
} else {
    $query_content_preview = mysqli_query($conn, $q1 = "select content_file, showcasepdfImage, video_file, VideoId, cobrand from user_templates where template_id = '{$template_id}' and client_id = '{$c_lient_Id}' and valid = 1 and deleted = 0");
}

$row_content_preview = mysqli_fetch_array($query_content_preview);

$selqry = "select * from sp_design_table where client_id = '{$c_lient_Id}'";
$selres = mysqli_query($conn, $selqry);
$degData = mysqli_fetch_array($selres);

if (isset($degData['pdfcobrandname']) && $degData['pdfcobrandname'] != '') {
    $distri_name = $degData['pdfcobrandname'];
} else {
    $distri_name = $pcomp_row['first_name'] . ' ' . $pcomp_row['last_name'];
}

if (isset($degData['pdfcobrandphone']) && $degData['pdfcobrandphone'] != '') {
    $distri_contact = $degData['pdfcobrandphone'];
} else {
    if ($pcomp_row['person_contact1'] != '') {
        $distri_contact = $pcomp_row['person_contact1'];
    } else {
        $distri_contact = $pcomp_row['person_contact2'];
    }
}

if (isset($degData['pdfcobrandemail']) && $degData['pdfcobrandemail'] != '') {
    $distri_email = $degData['pdfcobrandemail'];
} else {
    $distri_email = $pcomp_row['person_email'];
}

if (isset($degData['pdfcobrandImage']) && $degData['pdfcobrandImage'] != '') {
    $compLogo = $degData['pdfcobrandImage'];
}

$newMicro = ($QrySelectget['new_site_flag'] == 1) ? "" : "hidden";
$policont = (strpos($_SERVER['SERVER_NAME'], 'mutualfundpartner.com') !== false || strpos($_SERVER['SERVER_NAME'], 'nimfpartners.com') !== false || strpos($_SERVER['SERVER_NAME'], 'maxlifeinsurance.agency') !== false ) ? '' : 'hidden';

/* Added for payment link */
$lumsum_button = $obj->lumsum_button_url();
$sip_button = $obj->sip_button_url();
$nfo_button = $obj->default_button_url();
$headerFooterColour = $obj->header_footer_design();
$navbrand_center = '';
$navvar_center = '';
if (strpos($_SERVER['HTTP_HOST'], 'hdfcergoadvisor.com') !== false) {
//$navbrand_center='navbar-brand-center';
//$navvar_center='navbar-nav-center';   
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="shortcut icon" href="<?php echo (isset($faviconimg)) ? 'favicon_icon/' . $faviconimg : 'images/favicon.ico' ?>" />
        <title><?php echo $caseStudyActualtitle; ?>: <?php echo $docTypeName; ?></title>
        <meta name="description" content="<?php echo $metaDescription; ?>"/>
        <meta property="og:image" content="<?php echo $facebook_image; ?>"/>
        <meta name="keywords" content="<?php echo $solKeyword; ?>"/>
        <meta property="og:type" content="article"/>
        <meta property="og:title" content="<?php echo $facebook_title; ?>" />
        <meta property="og:description" content="<?php echo substr($facebook_desc, 0, 150); ?>" />
        <meta property="og:url" content="<?php echo $url1; ?>?channel_type=facebook"/>

        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo $weburl; ?>/css/style.css?v=1.0.1">
        <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">

        <!-- Custom Showcase CSS -->
        <link rel="stylesheet" href="<?php echo $sitepath; ?>css/showcase-style.css" />
        <style>
            :root {
                --payment-link-color:<?php echo htmlentities($QrySelectget['form_textcolor']); ?>;
            }
        </style>
        <script src="https://www.google.com/recaptcha/api.js?render=<?php echo G_RECAPTCHA_KEY;?>"></script>
        <?php echo $site_font_familyArr['micro_site_ff']; ?>
    <div id="fb-root"></div>
    <script>
        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id))
                return;
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=1614773665402559";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>
</head>
<body>
    <div id="crestashareicon" class="cresta-share-icon">
        <div class="sbutton"><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo htmlentities($url1); ?>" class="facebook"><i class="fa fa-facebook"></i></a></div>
        <div class="sbutton"><a href="https://twitter.com/intent/tweet?url=<?php echo htmlentities($url1); ?>" class="twitter"><i class="fa fa-twitter"></i></a> </div>
        <div class="sbutton"><a href="https://plus.google.com/share?url=<?php echo htmlentities($url1); ?>" class="google"><i class="fa fa-google"></i></a> </div>
        <div class="sbutton"><a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo htmlentities($url1); ?>" class="linkedin"><i class="fa fa-linkedin"></i></a></div>
        <!--<div class="sbutton"><a href="#" class="youtube"><i class="fa fa-youtube"></i></a></div>-->
    </div>

    <header id="main-header" class="white-transparent ng-scope menu-sticky" style="background: <?php echo ((isset($headerFooterColour['header_color'])) ? $headerFooterColour['header_color'] : $QrySelectget['theme_bg']) . ';' . $header_bg_imageclass; ?> ">

        <?php
        $otherbuttonShow=0;
        if (isset($nfo_button['url']) && $nfo_button['url'] !== '') {
            $otherbuttonShow=1;
            ?>
            <button type="button" class="payment-link-button payment-link" data-url="<?php echo $nfo_button['url']; ?>">Buy NFO</button>
            <?php
        }
        if (isset($sip_button['url']) && $sip_button['url'] !== '' && $otherbuttonShow==0) {
            ?>
            <button type="button" class="payment-link-button payment-link" data-url="<?php echo $sip_button['url']; ?>">Start SIP</button>
            <?php
        }

        if (isset($lumsum_button['url']) && $lumsum_button['url'] !== '' && $otherbuttonShow==0) {
            ?>
            <button type="button" class="payment-link-button payment-link" data-url="<?php echo $lumsum_button['url']; ?>">Purchase Now</button>
            <?php
        }
        ?>
        <nav class="navbar navbar-expand-lg navbar-light pull-right" style="width: 100%;">
            <a class="navbar-brand <?php echo $navbrand_center; ?> " href="<?php echo $https_status . $_SERVER['HTTP_HOST'].$concat_request_url; ?>">
                <?php if ($company_logo != '') { ?>
                    <img src="<?php echo $https_status . $_SERVER['HTTP_HOST'].$concat_request_url; ?>/company_logo/<?php echo htmlentities($company_logo); ?>" id="logo_img" class="img-fluid" alt="">
                <?php } else { ?>
                    <img src="company_logo/j0TPSP108098.png" id="logo_img" class="img-fluid" alt="">
                    <?php
                }
                ?>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
			<?php 
				$menus = $obj->getMultilingualContent('top-menu');
				$about_us = $obj->getMultilingualContent('about_us');
				$contact_me_now = $obj->getMultilingualContent('contact_me_now');
				$submit_now = $obj->getMultilingualContent('submit_now');
				$view_solutions = $obj->getMultilingualContent('view_solutions');
				$view_solutions_desc = $obj->getMultilingualContent('view_solutions_desc');
				
				$all = $obj->getMultilingualContent('all');
				$call_us = $obj->getMultilingualContent('call_us');
				$address = $obj->getMultilingualContent('address');
				$email_us = $obj->getMultilingualContent('email_us');
				$name = $obj->getMultilingualContent('name');
				$email = $obj->getMultilingualContent('email');
			?>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav <?php echo $navvar_center; ?> mr-auto w-100 justify-content-end">
                    <?php $headerMenueColr = (isset($headerFooterColour['header_text'])) ? htmlentities($headerFooterColour['header_text']) : htmlentities($QrySelectget['headerMenucolor']) ?>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo htmlentities($weburl).$concat_request_url; ?>" style="color:<?php echo $headerMenueColr; ?>;"><?php echo (isset($menus->home->{$lang})) ? $menus->home->{$lang} : $menus->home->en  ;?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo htmlentities($weburl).$concat_request_url; ?>/#sp-about" style="color:<?php echo $headerMenueColr; ?>;"><?php echo (isset($menus->about_us->{$lang})) ? $menus->about_us->{$lang} : $menus->about_us->en  ;?> </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo htmlentities($weburl).$concat_request_url; ?>/#sp-blog" style="color:<?php echo $headerMenueColr; ?>;"><?php echo (isset($menus->content->{$lang})) ? $menus->content->{$lang} : $menus->content->en  ;?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo htmlentities($weburl).$concat_request_url; ?>/#sp-contact" style="color:<?php echo $headerMenueColr; ?>;"><?php echo (isset($menus->contact_us->{$lang})) ? $menus->contact_us->{$lang} : $menus->contact_us->en  ;?></a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <?php if ($QrySelectget['headerflag'] == '1') { ?>
        <header id="main-header" class="white-transparent menu-sticky menu-resp" style="margin-top:97px;background:#DA9089 !important;height:52px;">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <nav class="navbar navbar-expand-lg navbar-light">
                            <li class="nav-title" style="color:<?php
                            if (isset($headerFooterColour['header_text'])) {
                                echo htmlentities($headerFooterColour['header_text']);
                            } else {
                                echo htmlentities($QrySelectget['headerMenucolor']);
                            }
                            ?>;">LIFE INSURANCE</li>
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

    <div class="container-fluid" <?php if ($QrySelectget['headerflag'] == '1') { ?>style="padding-top: 205px;"<?php } else { ?>style="padding-top: 145px;"<?php } ?>>
        <div class="row content">
            <div class="container">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-8"><?php
                            successmsg();
                            errmsg();
                            ?>
                            <div class="row">
                                <div class="col-md-9">
                                    <h5 class="mt-4" style="margin-left: 16px;"><strong><?php echo htmlentities(ucfirst($caseStudyActualtitle)); ?></strong></h5>
                                </div>
                                <div class="col-md-3" style="padding-top: 50px;">
                                    <?php
                                    if($download_attachment_link){
                                        $btnLbl_1 = $btnLbl_1 ?? 'Download Now';
                                        echo '<a class="payment-link-button pull-right contUs download_attachment_btn" style="position:absolute !important; bottom: 11px; padding: 8px 11px !important;color: inherit;" href="'.$sitepath.'upload/casestudy/'.$p_client_id.'/'.$track_url_params['attachment'].'" download>'.htmlentities($btnLbl_1).'</a>';
                                    }elseif(($buttonProprty == 1 && $row_content_preview['VideoId'] != '0') || in_array($buttonProprty,['2','3'])){
                                        echo '<button type="button" class="payment-link-button pull-right contUs" data-toggle="modal" data-target="#showcase_page" style="background:'.htmlentities($btncolor).';color:'.htmlentities($btntextcolor).';box-shadow: 1px 1px 1px -2px rgba(115,115,115,0.5); -webkit-box-shadow: 1px 1px 1px 1px rgba(115,115,115,0.5); -moz-box-shadow: 1px 1px 1px 1px rgba(115,115,115,0.5);  border:1px solid '.htmlentities($btnbgColor).'">'. htmlentities($btnLbl). '</button>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <hr>

                            <p style="margin-left: 16px;font-size: 14px;">Posted on <?php
                                if (strtotime($caseData['dou']) == '62169955200 ' || strtotime($caseData['dou']) == 'FALSE') {
                                    echo date('l jS \of F Y', strtotime($caseData['doe']));
                                } else {
                                    echo date('l jS \of F Y', strtotime($caseData['dou']));
                                }
                                ?></p>

                            <?php
                            if ($cs_mode == "library" && $cs_mode != "video") {
                                ?>
                                <div  id="sp_mobile_template" style="width:700px;"></div>
                                <?php
                            } else if ($cs_mode != "library" && $cs_mode != "video") {
                                $contentCheck = (explode('.', $row_content_preview['content_file']));

                                $showcasepdfImage = (explode('|', $row_content_preview['showcasepdfImage']));
                                $content_file = $row_content_preview['content_file'];
                                if (strtolower($contentCheck[1]) == "pdf") {
                                    ?>
                                    <div id="myCarousel" class="carousel slide" data-ride="carousel" style="width: 700px;margin: 0 auto">

                                        <div class="carousel-inner">
                                            <?php
                                            $i = 0;
                                            foreach ($showcasepdfImage as $getshowcasepdfImage) {
                                                ?>
                                                <div class="item <?php
                                                if ($i == 0) {
                                                    echo "active";
                                                }
                                                ?>">
                                                         <?php
                                                         if ($pcmember_pc_type == 'C') {
                                                             ?>
                                                        <img src="<?php echo htmlentities($weburl) . '/' . 'upload/casestudy/' . $p_client_id . '/' . $getshowcasepdfImage; ?>" style="width:700px;" class="img-responsive">
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <img src="<?php echo htmlentities($weburl) . '/' . 'upload/casestudy/' . $c_lient_Id . '/' . $getshowcasepdfImage; ?>" style="width:700px;" class="img-responsive">
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
                                } else {
                                    if ($pcmember_pc_type == 'C') {
                                        if ($row_content_preview['cobrand'] == 1) {
                                            //include("manager/sp-imagecobrand.php");
                                            //imgcobrandListener($template_id, $c_lient_Id);
                                            ?>
                                            <img style="width:700px;" class="img-responsive" src="<?php echo htmlentities($weburl); ?>/upload/casestudy/<?php echo $c_lient_Id; ?>/<?php echo $content_file; ?>">
                                            <?php
                                        } else {
                                            ?>
                                            <img style="width:700px;" class="img-responsive" src="<?php echo htmlentities($weburl); ?>/upload/casestudy/<?php echo $p_client_id; ?>/<?php echo $content_file; ?>">
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <img style="width:700px;" class="img-responsive" src="<?php echo htmlentities($weburl); ?>/upload/casestudy/<?php echo $c_lient_Id; ?>/<?php echo $content_file; ?>">
                                        <?php
                                    }
                                }
                            } else if ($cs_mode != "library" && $cs_mode == "video") {

                                if (!empty($row_content_preview['video_file'])) {
                                    $vurl = explode('/', $row_content_preview['video_file']);
                                    $urlVideo = end($vurl);


                                    if (preg_match('/watch/', $urlVideo)) {
                                        $videopath = explode('watch?v=', $urlVideo);
                                        $urlVideo1 = $videopath[1];
                                    } else {
                                        $urlVideo1 = $urlVideo;
                                    }
                                    ?>

                                    <iframe class="embed-responsive-item" width="700px" height="500px" src="https://www.youtube.com/embed/<?php echo htmlentities($urlVideo1); ?>?rel=0" frameborder="0" allowfullscreen></iframe>


                                    <?php
                                } else if (!empty($row_content_preview['VideoId']) && $row_content_preview['cobrand'] == 1) {
                                    ?>
                                    <video width="100%" height="100%" controls><source src="<?php echo htmlentities($videoURL); ?>" type="video/mp4"></video>

                                    <?php
                                }
                            }

                            if ($pcmember_pc_type == 'C') {
                                if ($row_content_preview['cobrand'] == 1) {
                                    ?>
                                    <div style="height:54px; padding:13px 27px;font-size:12px;font-weight:bold;">
                                        <span class="cobrandstrip" style="color:#414042;"><i class="fa fa-user" style="color:#414042;font-weight:bold;font-size:12px;padding:3px;" aria-hidden="true"></i><?php echo htmlentities($distri_name); ?></span>
                                        <span class="cobrandstrip" style="color:#414042;"><i class="fa fa-phone" style="color:#414042;font-weight:bold;font-size:12px;padding:3px;" aria-hidden="true"></i><?php echo htmlentities($distri_contact); ?></span>
                                        <span class="cobrandstrip" style="color:#414042;"><i class="fa fa-envelope" style="color:#414042;font-weight:bold;font-size:12px;padding:3px;" aria-hidden="true"></i><?php echo htmlentities($distri_email); ?></span>

                                    </div>
                                    <?php
                                }
                            }
                            ?>

                            <!-- bellow text was for HDFC only -->
                            <!--  <div style="height:54px; font-size:12px;font-weight:bold;">
                                Before investing in a particular mutual fund scheme please make sure that you have read and understood all the important points of that particular scheme including risk associated with the investment.
                            </div>
                            <div style="height:54px; font-size:12px;font-weight:bold;">
                                Mutual Fund investments are subject to market risks, read all scheme related documents carefully.
                            </div> -->

                            <div class="card my-4">
                                <h5 class="card-header">Leave a Comment:</h5>
                                <div class="card-body">
                                    <div class="fb-comments" data-href="<?php echo htmlentities($url1); ?>" data-numposts="5" data-colorscheme="light" data-width="100%"></div>
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
                                    <?php include("cs-sidebar.php"); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    if ($QrySelectget['disclaimer_tag'] == 1) {
        ?>
        <div ui-view="footer" class="ng-scope">
            <footer class="dark-bg ng-scope" style="background:<?php echo $QrySelectget['disclaimer_color']; ?>;">
                <div class="sp-footer sp-pb-20">
                    <div class="container">
                        <div class="row overview-block-ptb2">
                            <div class="col-lg-12 col-md-12 sp-mtb-20">
                                <div class="logo">
                                    <div class="sp-mt-15 sp-mr-60" style="color: #7f7e7f; font-size: 16px; margin-top: 10px; padding: 20px">
                                        <?php echo preg_replace('-arn_no-', $obj->arn_no, $QrySelectget['disclaimer_content']); ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <?php
    }
    $footer_font_color = (isset($headerFooterColour['footer_text'])) ? 'style="color:' . htmlentities($headerFooterColour['footer_text']) . ';"' : '';
    include("includes/footer.php");
    ?>


    <!------------ Start Download popup ------------>
    <div id="showcase_page" class="modal fade" role="dialog" style="margin-top:97px;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" style="font-size:20px;font-weight:bold;"><strong>Please fill the form below to begin.</strong></h4>
                </div>

                <form class="page-name showcaseLeadPopup" method="post" class="form-horizontal" id="frmcmp" method="post" action="../add-lead-proces.php" enctype="multipart/form-data">
                    <div class="modal-body h170">
                        <div class="portlet-body form">
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
                                        <input name="email" id="scpemail" type="email" value="<?php echo $mofficialEMail; ?>" placeholder="Email Address*" class="form-control" required />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6 mbottom10">
                                        <input name="phone" type="text" id="check_phone" maxlength="12" value="<?php echo (isset($mPhone)) ? $mPhone : ''; ?>" placeholder="Phone Number*" maxlength="12" class="form-control" required />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6 mbottom10">
                                        <input name="city" id="city" value="<?php echo $mCompname; ?>" type="text" placeholder="City" class="form-control"/>
                                    </div>
                                </div>

                                <input name="source" type="hidden" value="<?php echo $source;?>" id="source" />
                                <input name="caseids" type="hidden" value="<?php echo ($download_attachment_link) ? $camp_id : $caseId; ?>" id="caseids" />
                                <input name="channel_type" type="hidden" value="<?php echo $channel_type ?>" id="channel_type" />
                                <input name="camp_id" type="hidden" value="<?php echo $camp_id ?>" id="camp_id" />
                                <input name="contentType" type="hidden" value="<?php echo $contentType ?>" id="contentType" />
                                <input name="ref_url" type="hidden" value="<?php echo $ref_url ?>"  />
                                <input name="microsite_captcha" type="hidden" value="MCR123" id="microsite_captcha" />
                                <input name="client_id" type="hidden" value="<?php echo $c_lient_Id ?>"  />
                                <input name="c_" type="hidden" value="<?php echo $contact_id;?>" />
                            </div>
                        </div>
                    </div>
                    <div>
                        <span style="color: red;margin-left: 29px;" id="validation_error"></span>
                    </div>
                    <div class="modal-footer">
                        <div class="section-field sp-mt-5 term-set" style="text-align: justify;font-size:14px;">
                            <input type="checkbox" id="termCheck" name="termCheck" class="mrt-0" required> <span class="understand"> I understand that I would be contacted with regards to this request placed and consent for the same in spite of being registered with the National Customer Preference Registry (NCPR) with TRAI. I understand that there is a de-registration facility ( for not receiving such calls) which I may avail if required in future.</span>
                        </div>
                        <hr />

                        <div class="section-field sp-mt-20"></div>
                        <input name="add" type="submit" value="Submit" id="showcase-subtn" class="payment-link-button" />
                        <button type="button" class="payment-link-button" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- End Download popup -->
    <!-- declearation model for term & condition -->
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="<?php echo $https_status . $_SERVER['HTTP_HOST']; ?>/js/mailcrypt.js"></script>
    <script>var microsite = 1;</script>
    <?php include("includes/footer-event.php"); ?>
    <script>
        var swJ = jQuery.noConflict();
        swJ(document).ready(function () {
            swJ('.mailcrypt').mailcrypt();
            swJ("a[href='#sp-contact'],a[href='#sp-about'],a[href='#sp-blog'],a[href='#categories']").on('click', function (event) {
                if (this.hash !== "")
                {
                    event.preventDefault();
                    var hash = this.hash;
                    swJ('html, body').animate({
                        scrollTop: swJ(hash).offset().top
                    }, 900, function () {

                        window.location.hash = hash;
                    });
                }
            });
        });

        var frrm = jQuery.noConflict();
        <?php if($download_attachment_link) { ?>
            frrm('.download_attachment_btn').click(function(){
                frrm('form.showcaseLeadPopup').submit();
            });
        <?php }else{ ?>
            frrm('#frmcmp').submit(function (event) {
            event.preventDefault();
            let checkPhone = /^[6789]\d{9}$/.test(frrm('#check_phone').val());
            frrm('#validation_error').text('');
            if (!checkPhone) {
                frrm('#validation_error').text("*Please enter a valid mobile number.");
                return false;
            }
            grecaptcha.ready(function () {
                grecaptcha.execute('<?php echo G_RECAPTCHA_KEY;?>', {action: 'create_second_form'}).then(function (token) {
                    frrm('#frmcmp').prepend('<input type="hidden" name="g-recaptcha-response" value="' + token + '">');
                    document.getElementById("frmcmp").submit();
                });
            });
        });
        <?php }?>

        function ShowcaseSearchKeyup() {
            var srch = jQuery.noConflict();
            var showsearchName = srch("#showcsearch-name").val();
            var c_lient_Id = '<?php echo $c_lient_Id; ?>';
            var p_client_id = '<?php echo $p_client_id; ?>';
            var pcType = '<?php echo $pcmember_pc_type; ?>';
            var beetle = srch('input[name="beetle"]').val();

            srch.ajax({
                type: "POST",
                url: "<?php echo $https_status . $_SERVER['HTTP_HOST']; ?>/get_document_title_list.php",
                data: {beetle: beetle, showsearchName: showsearchName, c_lient_Id: c_lient_Id, p_client_id: p_client_id, pcType: pcType},
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

        function srchshowcaseClick(val1) {
            var srchclick = jQuery.noConflict();
            srchclick("#showcsearch-name").val(val1)
            window.location.href = '<?php echo $https_status . $_SERVER['HTTP_HOST']; ?>/showcase/' + window.encodeURIComponent(val1) + '';
        }

        function Videodownload() {
            videoURL = '<?php echo isset($videoURL) ? $videoURL : ''; ?>';

            bootbox.confirm("The videos are being provided only for promotion of schemes in your capacity as a distributor empanelled with HDFC Asset Management Co. Ltd. The copyright on the materials continues to vest with HDFC Asset Management Co. Ltd. There shall be no misuse / tampering of the videos by the distributor.", function (result) {
                if (result == true) {
                    $('<a/>', {"href": videoURL, "download": "video.mp4", id: "videoDownloadLink"}).appendTo(document.body);
                    $('#videoDownloadLink').get(0).click().remove();
                }
            });
        }

        var xman = jQuery.noConflict();
        xman(document).ready(function () {
            var temp_id = '<?php echo $template_id; ?>';
            var clientId = '<?php echo $c_lient_Id; ?>';
            var screen_width = xman(window).width();
            var cobrandExp = parseInt('<?php echo isset($cobrandExp) ? $cobrandExp : ''; ?>');
            var beetle = xman('input[name="beetle"]').val();
            xman("#sp_mobile_template").html('');
            xman.ajax({
                url: "<?php echo $https_status . $_SERVER['HTTP_HOST']; ?>/sp-mobile-template.php",
                type: "post",
                data: {beetle: beetle, screen_width: screen_width, temp_id: temp_id, clientId: clientId, cobrandExp: cobrandExp},
                cache: false,
                success: function (result) {
                    xman("#sp_mobile_template").html(result);
                    disableEditableContent();
                }
            });
        });
    </script>

    <script>
        var himan = jQuery.noConflict();
        var xSeconds = 5000; // 1 second

        setTimeout(function () {
            himan('.alert-dismissable').hide();
        }, xSeconds);
    </script>

    <!-- Script For Chatbot start here-->
    <?php
    $baseUrl = url();
    if (chatbot_color1 != '' && chatbot_color2 != '' && chatbot_color != '' && chatbot_color != '') {
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

    if ($_SERVER['SERVER_NAME'] == 'prem.mutualfundpartner.com' || 1 == 1) {
        ?>
        <section chat__bot class="chat__bot">
            <div role="chat__bot_head"><?php echo chatbot_title; ?>
                <span>
                    <a href="#" onclick="chatbotData('', '', 1);"><img src="<?php echo $baseUrl; ?>/chatbot/img/refresh.svg" alt="" /></a>
                    <i class="close1"><img src="<?php echo $baseUrl; ?>/chatbot/img/cancel-music.svg" alt="" /></i>
                </span>
            </div>
            <div role="chat__bot_body" class="chat__bot_body">

            </div>
        </section>
        <?php if(Is_chatbot == 1){ ?>
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

        <?php }else if(Is_chatbot == 2) { ?>
                <!-- Start of HubSpot Embed Code -->
        <script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/8262976.js"></script>
        <!-- End of HubSpot Embed Code -->
        <?php } ?>

        <!-- Trigger/Open The Modal -->
        <link href="<?php echo $baseUrl; ?>/chatbot/css/style1.css" rel="stylesheet">
        <style>

            :root {
                --blue: <?php echo $blue; ?>;
                --text_color: <?php echo $text_color; ?>;
                --chat_1: <?php echo $chat_1; ?>;
                --chat_2: <?php echo $chat_2; ?>;
                --font:'Open Sans', sans-serif, "Calibri";
            }

        </style>
        <link href="<?php echo $baseUrl; ?>/chatbot/css/owl.carousel.min.css" rel="stylesheet">
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

        <script src="<?php echo $baseUrl; ?>/chatbot/js/owl.carousel.js"></script>

        <?php require("chatbot/js/comman_function1.php"); ?>
        <script>
                        function text_manage(a, b) {
                            return true
                        }

                        function headline_manage(a, b) {
                            return true
                        }

                        $('.contUs').on('click', function () {
                            if (typeof $ === 'undefined') {
                                $ = jQuery;
                            }

                            if ($('.chat__bot').css('display') === 'block') {
                                $('.chat__bot').hide();
                            }
                        });

                        jQuery(document).on('click', function (e) {
                            if (e.target !== 'span.navbar-toggler-icon' && jQuery('#navbarSupportedContent').hasClass('in')) {
                                jQuery('#navbarSupportedContent').collapse('hide');
                            }
                        });

                        jQuery('.download_attachment_btn').click(function(){
                            jQuery('form.showcaseLeadPopup').submit();
                        });

                        /*var func = null;
                         var exit_method = false;*/
                        function disableEditableContent() {
                            var contentLen = jQuery("div").length;
                            if (contentLen > 0) {
                                jQuery("div").each(function () {
                                    var attr = jQuery(this).attr('contenteditable');
                                    if (typeof attr !== typeof undefined && attr !== false) {
                                        jQuery(this).attr('contenteditable', false);
                                        /*stopFuncExecution();
                                         exit_method = true;*/
                                    }
                                });
                            }

                            /*if(!exit_method){
                             func = setTimeout(disableEditableContent, 1000);
                             return true;
                             }
                             stopFuncExecution();
                             return false;*/
                        }

                        /*function stopFuncExecution() {
                         clearTimeout(func);
                         }
                         
                         jQuery(document).ready(function(){
                         func = disableEditableContent();
                         });*/

                        $(".chat__bot a").click(function () {
                            $("body").addClass("intro");
                        });
        </script>
        <?php
    }
    ?>
</body>
<script>
    jQuery(document).on('click', function (e) {
        if (e.target !== 'span.navbar-toggler-icon' && jQuery('#navbarSupportedContent').hasClass('in')) {
            jQuery('#navbarSupportedContent').collapse('hide');
        }
    });

    jQuery('button.payment-link').on('click', function () {
        var href = jQuery(this).data('url');
        var visit_link = jQuery(this).text();
        var vars = 'url=' + window.encodeURIComponent(params) + '&HTTP_REFERER=' + window.encodeURIComponent(HTTP_REFERER) + '&client_id=' + window.encodeURIComponent(client_id) + '&vtoken=' + window.encodeURIComponent(vtoken) + '&uemail=' + window.encodeURIComponent(uemail) + '&start=' + window.encodeURIComponent(startTime) + '&visit_page=' + window.encodeURIComponent(visit_link) + '&mic=' + microsite + '&c=' + window.encodeURIComponent(cont);
        loadTemplate(vars, 5);
        setTimeout(function () {
            window.open(href, '_blank');
        }, 500);
    });


    if (jQuery(window).width() < 767) {
        setTimeout(function () {
            jQuery(".ui-draggable.ui-draggable-handle").each(function () {
                jQuery(this).css('width', '100%');
            });
        }, 500);
    }
</script>

</html>
<?php

function url() {
    return sprintf(
            "%s://%s", isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http', $_SERVER['SERVER_NAME']
    );
}
?>
