<?php
/* Author name: <Bizight Solutions Pvt Ltd> 
 * Purpose of document/ page : <This is page is used to show the content libarary data on microsite.> 
 * Date: 17-02-2021 
 * Copyright info : <Copyright @2021, Bizight Solutions Pvt Ltd>
 */

$https_status = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://");
if ($https_status == "http://") {
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
    exit;
}

session_start();
header('Access-Control-Allow-Origin: true');

require realpath(__DIR__ . '/vendor/autoload.php');

include("includes/global.php");
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
//If the HTTPS is not found to be "on"
$basepath = $https_status = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on')) ? "https://" : "http://";
/* Trackable URL Related Variables */
$track_url_params = CommonStaticFunctions::get_track_url_params($https_status . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", $conn);
$download_attachment_link = 0;
$source = 'Document Popup';
if (isset($track_url_params['attachment']) && !empty($track_url_params['attachment'])) {
    $download_attachment_link = 1;
    $source = 'Download Link';
}
$concat_request_url = $track_url_params['request_uri'] ?? '';
$channel_type = $track_url_params['channel_type'] ?? '';
$camp_id = $track_url_params['camp_id'] ?? 0;
$contentType = $track_url_params['content'] ?? '';
$contact_id = $track_url_params['c'] ?? 0;
$mofficialEMail = ($track_url_params['semail']) ?? '';
$mfName = $track_url_params['contact_details']['first_name'] ?? '';
$mlName = $track_url_params['contact_details']['last_name'] ?? '';
$mPhone = $track_url_params['contact_details']['mobile'] ?? '';
/* --------------------------------------------------------------------------- */

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

//$url_cstdy_tw = explode("?", $url_cstdy);
//$url_cstdy_tt = urldecode($url_cstdy_tw[0]);

$url_cstdy_tt = $_REQUEST['case_study_id'];

$caseStudyName = str_replace("-", " ", $url_cstdy_tt);

$caseStudyName = urldecode($caseStudyName);

$url1 = $weburl . "/showcase/" . $url_cstdy_tt . $concat_request_url;

if ($pcmember_pc_type == 'C') {
    $csdquery = mysqli_query($conn, $t = "select CS.id, CS.case_study_title,CS.category,CS.case_study_desc, TS.id as syndid from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id = CS.id where TS.c_client_id = '{$c_lient_Id}' and CS.valid = 1 and CS.deleted = 0 and TS.approve = 1 and (CS.case_study_title = '" . mysqli_real_escape_string($conn, $caseStudyName) . "' or CS.case_study_title = '" . mysqli_real_escape_string($conn, $url_cstdy_tt) . "')");
} else {
    $csdquery = mysqli_query($conn, $t = "select id, case_study_title,case_study_desc,category from sp_case_study where client_id = '{$c_lient_Id}' and approve = 1 and valid = 1 and deleted = 0 and (case_study_title = '" . mysqli_real_escape_string($conn, $caseStudyName) . "' or case_study_title = '" . mysqli_real_escape_string($conn, $url_cstdy_tt) . "')");
}
$csdrow = mysqli_fetch_array($csdquery);

$caseId = $csdrow['id'];
$catNameQ = array();
$x = 0;
foreach (explode(",", $csdrow['category']) as $cat) {
    $catNameQ[$x] = categoryName($cat);
    $x++;
}

$cateList = implode(',', $catNameQ);
$desc = $csdrow['case_study_desc'];  // showcase desciption



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
    $query = "select T.content_file, T.showcasepdfImage, T.video_file, T.cobrand, T.VideoId, TS.id as synid from user_templates as T INNER JOIN sp_template_syndication as TS ON  T.template_id = TS.template_id where T.template_id = '{$template_id}' and TS.c_client_id = '{$c_lient_Id}' and T.valid = 1 and T.deleted = 0 and TS.approve = 1";
    $query_content_preview = mysqli_query($conn, $query);
} else {
    $query_content_preview = mysqli_query($conn, "select content_file, showcasepdfImage, video_file, VideoId, cobrand from user_templates where template_id = '{$template_id}' and client_id = '{$c_lient_Id}' and valid = 1 and deleted = 0");
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

//~ For social icon
$obj = new \Microsite\Microsite($connPDO);
$obj->LinkedInPAge;
$obj->facebookPage;



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
$lang = "en";
if (isset($_REQUEST['lang']) && trim($_REQUEST['lang']) != "") {
    $lang = trim($_REQUEST['lang']);
}

//print_r($lang);exit;
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
        <link rel="stylesheet" href="<?php echo $weburl; ?>/css/newassets/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo $weburl; ?>/css/newassets/css/all.min.css">
        <link rel="stylesheet" href="<?php echo $weburl; ?>/css/newassets/css/owl.carousel.min.css">
        <link rel="stylesheet" href="<?php echo $weburl; ?>/css/newassets/css/aos.css">
        <link rel="stylesheet" href="<?php echo $weburl; ?>/css/newassets/css/animate.css">
        <link rel="stylesheet" href="<?php echo $weburl; ?>/css/newassets/css/main.css">
        <link rel="stylesheet" href="<?php echo $weburl; ?>/css/newassets/css/responsive.css">




        <!--
                <link rel="stylesheet" href="<?php echo $weburl; ?>/css/style.css?v=1.0.1">
        -->
        <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
        <!--
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
        -->

        <!-- Custom Showcase CSS -->
        <!--
                <link rel="stylesheet" href="<?php echo $sitepath; ?>css/showcase-style.css" />
        -->
        <?php $variable = '#f6971f'; ?>
        <style>
            :root {
                --payment-link-color: <?php echo htmlentities($QrySelectget['form_textcolor']) ? $QrySelectget['form_textcolor'] : $variable; ?>;

                --primary-color: <?php echo htmlentities($QrySelectget['headerMenucolor'] ? $QrySelectget['headerMenucolor'] : $variable); ?>;
                --secondary-color: #323f48;
                --white: #fff;
                --black: #000;
                --placeholder: #979aa9;
                --dark-blue: #58595b;
                --light-grey: #f1f1f1;
                --whatsapp: #25d266;
                --grey: #c2c2c2;
                --grey-back: #f6f6f7;
                --ribbon-dark: #ad6a15;
            }
        </style>
        <script src='https://www.google.com/recaptcha/api.js'></script>
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
    <div class="topbar">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <ul class="left-top">
                        <li>
                            <a href="mailto:<?php echo $pcomp_row['person_email']; ?>"><i class="fa fa-envelope"></i><span><?php echo $pcomp_row['person_email']; ?></span></a>
                        </li>
                        <li>
                            <a href="tel:<?php echo htmlentities($firstPhone) . htmlentities($secondPhone); ?>"><i class="fa fa-phone-alt"></i><?php echo htmlentities($firstPhone) . htmlentities($secondPhone); ?></a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="right-top text-right">
                        <li>
                            <?php if (!empty($obj->facebookPage)) { ?>
                                <a target="_blank" href="<?php echo $obj->facebookPage; ?>"><i class="fab fa-facebook-f"></i></a>
                            <?php } ?>
                        </li>
                        <!--
                                                                                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                        -->
                        <li>
                            <?php if (!empty($obj->LinkedInPAge)) { ?>
                                <a target="_blank" href="<?php echo $obj->LinkedInPAge; ?>"><i class="fab fa-linkedin-in"></i></a>
                            <?php } ?>
                        </li>
                        <!--
                                                                                        <li><a  href="#" onClick = "return share(event);"><i class="fab fa-whatsapp"></i></a></li>
                                                                                        
                        -->
                        <?php if (multilingual) { ?>
                            <li> <select name="lang" id="languages" onchange="changeLang(this.value)">
                                    <option value="en">English</option>
                                    <?php foreach ($obj->microsite_languages as $langObj) { ?>
                                        <option value="<?php echo $langObj['lang_unique_code']; ?>"><?php echo $langObj['partner_category']; ?></option>

                                    <?php } ?>
                                </select> </li>

                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
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
    $should_checkout = $obj->getMultilingualContent('should_checkout');
    $category = $obj->getMultilingualContent('category');
    $date = $obj->getMultilingualContent('date');
    ?>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <?php if ($company_logo != '') { ?>
                <a class="navbar-brand" href="<?php echo htmlentities($weburl) . $concat_request_url; ?>"><img src=<?php echo $_REQUEST['siteUrl'] . '/company_logo/' . htmlentities($company_logo); ?> class="img img-fluid"></a>
            <?php } ?>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="<?php echo htmlentities($weburl) . $concat_request_url; ?>"><?php echo (isset($menus->home->{$lang})) ? $menus->home->{$lang} : $menus->home->en; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#content"><?php echo (isset($menus->content->{$lang})) ? $menus->content->{$lang} : $menus->content->en; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact"><?php echo (isset($menus->contact_us->{$lang})) ? $menus->contact_us->{$lang} : $menus->contact_us->en; ?></a>
                    </li>
                    <!--
                                                      <li class="nav-item dropdown">
                                                             <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80.15 60.61">
                                                                            <title>Asset 2</title>
                                                                            <g id="Layer_2" data-name="Layer 2">
                                                                                    <g id="Insuarance_icon" data-name="Insuarance icon">
                                                                                            <path d="M3.93,18.79c.29.94.64,2,1,3.07A34,34,0,0,0,6.4,25.21a31,31,0,0,0,1.85,3.28,14.76,14.76,0,0,0,2.37,2.82,11.07,11.07,0,0,0,3,2,8.62,8.62,0,0,0,3.6.74,8.4,8.4,0,0,0,3.29-.57,5.69,5.69,0,0,0,3.41-3.78A9.93,9.93,0,0,0,24.24,27,5.77,5.77,0,0,0,22,22.14q-2.24-1.74-6.46-1.74-.51,0-1,0t-1,.09V16L14,16h.22a12.18,12.18,0,0,0,3.73-.52,8.26,8.26,0,0,0,2.74-1.43A6.07,6.07,0,0,0,22.32,12a5.8,5.8,0,0,0,.57-2.52,5.12,5.12,0,0,0-.42-2.13,4.5,4.5,0,0,0-1.14-1.56,4.78,4.78,0,0,0-1.7-.94,7.09,7.09,0,0,0-2.16-.32,11.23,11.23,0,0,0-1.56.11,13.29,13.29,0,0,0-1.59.33,18.27,18.27,0,0,0-1.78.58c-.63.24-1.34.54-2.14.9L8.69,2.31A25.87,25.87,0,0,1,13.18.58,16.79,16.79,0,0,1,17.59,0a11.32,11.32,0,0,1,4,.69,9.57,9.57,0,0,1,3.17,1.9,8.61,8.61,0,0,1,2.09,2.89,8.8,8.8,0,0,1,.75,3.63q0,5.36-5.13,8.63v.12a11.74,11.74,0,0,1,3.36,2.06c.58.11,1.19.18,1.84.24s1.24.09,1.78.09a13.85,13.85,0,0,0,1.77-.12,9.12,9.12,0,0,0,1.9-.44,10.35,10.35,0,0,0,2-.93,11.82,11.82,0,0,0,2-1.56V.6H48.33V5H42V45.27H37.12V22.5l-.12,0A10,10,0,0,1,33.8,24a13.19,13.19,0,0,1-3.09.36c-.34,0-.69,0-1.05,0l-1.11-.1,0,.12a8.37,8.37,0,0,1,.48,3,12.23,12.23,0,0,1-.81,4.49,10.16,10.16,0,0,1-2.32,3.58,10.48,10.48,0,0,1-3.65,2.35,13.08,13.08,0,0,1-4.81.84,12.67,12.67,0,0,1-5.14-1A16.32,16.32,0,0,1,8.13,35a19.54,19.54,0,0,1-3.22-3.58,33.42,33.42,0,0,1-2.36-4A34.49,34.49,0,0,1,.93,23.6c-.42-1.21-.73-2.22-.93-3Z"></path>
                                                                                            <path d="M39,60.61,55.91,16.69h6.26l18,43.92H73.53L68.4,47.31H50l-4.83,13.3Zm12.68-18H66.61L62,30.41q-2.1-5.53-3.11-9.11a55.61,55.61,0,0,1-2.37,8.39Z"></path>
                                                                                    </g>
                                                                            </g>
                                                                    </svg>
                                                            </a>
                                                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                                              <a class="dropdown-item" href="#">English</a>
                                                              <a class="dropdown-item" href="#">Hindi</a>
                                                              <a class="dropdown-item" href="#">Spanish</a>
                                                            </div>
                                                      </li>
                    -->
                </ul>

                <div class="menu-btn">
                    <?php
                    $otherbuttonShow = 0;
                    if (isset($nfo_button['url']) && $nfo_button['url'] !== '') {
                        $otherbuttonShow = 1;
                        ?>
                        <button class="btn main-btn payment-link" data-url="<?php echo $nfo_button['url']; ?>">Buy NFO</button>
                    <?php }if (isset($sip_button['url']) && $sip_button['url'] !== '' && $otherbuttonShow == 0) { ?>
                        <button class="btn main-btn payment-link" data-url="<?php echo $sip_button['url']; ?>">Start SIP</button>
                        <?php
                    }

                    if (isset($lumsum_button['url']) && $lumsum_button['url'] !== '' && $otherbuttonShow == 0) {
                        ?>
                        <button class="btn main-btn payment-link" data-url="<?php echo $lumsum_button['url']; ?>">Buy Now</button>
                    <?php } ?>

                </div>
            </div>
        </div>
    </nav>


    <!-- pdf -->
    <div class="pdf-section">
        <div class="container">
            <?php
            successmsg();
            errmsg();
            ?>
            <?php if ($cs_mode == "library" && $cs_mode != "video") { ?>
                <div class="pdf-wrap condition1">
                <?php } else { ?>
                    <div class="pdf-wrap">
                    <?php } ?>


                    <div class="row">
                        <div class="col-md-8">
                            <?php
                            if ($cs_mode == "library" && $cs_mode != "video") {
                                ?>
                                <div  id="sp_mobile_template" class="" style="width:100%; overflow-y: auto;"></div>
                                <?php
                            } else if ($cs_mode != "library" && $cs_mode != "video") {
                                $contentCheck = (explode('.', $row_content_preview['content_file']));
                                $showcasepdfImage = (explode('|', $row_content_preview['showcasepdfImage']));
                                $content_file = $row_content_preview['content_file'];
                                if (strtolower($contentCheck[1]) == "pdf") {
                                    ?>
                                    <div class="owl-carousel owl-theme pdf-carousel">
                                        <?php
                                        // iteration for slider
                                        foreach ($showcasepdfImage as $getshowcasepdfImage) {
                                            ?>
                                            <div class="item">
                                                <div class="pdf-box">
                                                    <?php if ($pcmember_pc_type == 'C') { ?>
                                                        <img src="<?php echo htmlentities($weburl) . '/' . 'upload/casestudy/' . $p_client_id . '/' . $getshowcasepdfImage; ?>" class="img img-fluid">
                                                    <?php } else { ?>
                                                        <img src="<?php echo htmlentities($weburl) . '/' . 'upload/casestudy/' . $c_lient_Id . '/' . $getshowcasepdfImage; ?>"  class="img img-fluid">
                                                    <?php } ?>

                                                </div>

                                            </div>
                                        <?php } ?>
                                    </div>
                                    <?php
                                } else {
                                    if ($pcmember_pc_type == 'C') {
                                        if ($row_content_preview['cobrand'] == 1) {
                                            ?>
                                            <div class="pdf-box">

                                                <img class="img img-fluid" src="<?php echo htmlentities($weburl); ?>/upload/casestudy/<?php echo $c_lient_Id; ?>/<?php echo $content_file; ?>">
                                            </div>
                                            <?php
                                        } else {
                                            ?>
                                            <div class="pdf-box">

                                                <img class="img img-fluid" src="<?php echo htmlentities($weburl); ?>/upload/casestudy/<?php echo $p_client_id; ?>/<?php echo $content_file; ?>">
                                            </div>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <div class="pdf-box">

                                            <img class="img img-fluid" src="<?php echo htmlentities($weburl); ?>/upload/casestudy/<?php echo $c_lient_Id; ?>/<?php echo $content_file; ?>">
                                        </div>
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
                                    <div class="pdf-box">
                                        <iframe class="embed-responsive-item" width="100%" height="500px" src="https://www.youtube.com/embed/<?php echo htmlentities($urlVideo1); ?>?rel=0" frameborder="0" allowfullscreen></iframe>
                                    </div>

                                    <?php
                                } else if (!empty($row_content_preview['VideoId']) && $row_content_preview['cobrand'] == 1) {
                                    ?>
                                    <div class="pdf-box">
                                        <video width="100%" height="100%" controls><source src="<?php echo htmlentities($videoURL); ?>" type="video/mp4"></video>
                                    </div>
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
                        </div>
                        <div class="col-md-4">
                            <div class="inner-pdf">
                                <?php
                                if ($download_attachment_link) {
                                    $btnLbl_1 = $btnLbl_1 ?? 'Download Now';
                                    echo '<a class="btn main-btn download_attachment_btn" href="' . $sitepath . '/upload/casestudy/' . $p_client_id . '/' . $track_url_params['attachment'] . '" download>' . htmlentities($btnLbl_1) . '</a>';
                                } elseif (($buttonProprty == 1 && $row_content_preview['VideoId'] != '0') || in_array($buttonProprty, ['2', '3'])) {
                                    echo '<button class="btn main-btn" data-toggle="modal" data-target="#downloadModal" style="background:' . htmlentities($btncolor) . ';color:' . htmlentities($btntextcolor) . ';box-shadow: 1px 1px 1px -2px rgba(115,115,115,0.5); -webkit-box-shadow: 1px 1px 1px 1px rgba(115,115,115,0.5); -moz-box-shadow: 1px 1px 1px 1px rgba(115,115,115,0.5);  border:1px solid ' . htmlentities($btnbgColor) . '">' . htmlentities($btnLbl) . '</button>';
                                }
                                ?>

                                <h4><?php echo htmlentities(ucfirst($caseStudyActualtitle)); ?></h4>
                                <p> <?php echo $desc ?></p>
                                <h6><span><?php echo (isset($category->{$lang})) ? $category->{$lang} : $category->en; ?>:</span><?php echo $cateList ?></h6>
                                <h6><span><?php echo (isset($date->{$lang})) ? $date->{$lang} : $date->en; ?>:</span>
                                    <?php
                                    if (strtotime($caseData['dou']) == '62169955200 ' || strtotime($caseData['dou']) == 'FALSE') {
                                        echo date('l jS \of F Y', strtotime($caseData['doe']));
                                    } else {
                                        echo date('l jS \of F Y', strtotime($caseData['dou']));
                                    }
                                    ?>
                                </h6>
                                <ul class="right-top text-right">
                                    <li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo htmlentities($url1); ?>"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="https://twitter.com/intent/tweet?url=<?php echo htmlentities($url1); ?>"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo htmlentities($url1); ?>"><i class="fab fa-linkedin-in"></i></a></li>
                                    <li><a  href="https://wa.me/?phone=<?php echo urlencode((substr($firstPhone . '' . $secondPhone, 0, 3) == '+91') ? htmlentities($firstPhone . '' . $secondPhone) : htmlentities('+91' . $firstPhone . '' . $secondPhone) ); ?>&text=<?php echo urlencode(htmlentities(ucfirst($caseStudyActualtitle)) . " " . ($url1)); ?>"><i class="fab fa-whatsapp"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- testimonial -->
        <?php include("cs-sidebarnew.php"); ?>

        <!-- contact -->
        <div class="contact-section" id="contact" style="background-image: url('<?php echo $https_status . $_SERVER['HTTP_HOST']; ?>/css/newassets/images/contact-bg.jpg');">
            <div class="container">
                <div class="head">
                    <h3><?php echo (isset($contact_me_now->{$lang})) ? $contact_me_now->{$lang} : $contact_me_now->en; ?></h3>
                </div>
                <form class="aos-init aos-animate" id ="contact_frm" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                    <div class="row">
                        <div class="col form-group">
                            <input type="text" name="name" id="your-name" placeholder="<?php echo (isset($name->{$lang})) ? $name->{$lang} : $name->en; ?>*" class="form-control">
                        </div>
                        <div class="col form-group">
                            <input type="text" name="email" id="your-email" placeholder="<?php echo (isset($email->{$lang})) ? $email->{$lang} : $email->en; ?>*" value="<?php echo $mofficialEMail; ?>" class="form-control">

                        </div>
                        <div class="col form-group">
                            <input type="text" name="phone" id="number-339" placeholder="Phone*" class="form-control">
                        </div>
                    </div>
                    <button name="submit" type="button" value="Send" onclick="return submit_formbuilder();" id="send-value" class=" btn main-btn"><?php echo (isset($submit_now->{$lang})) ? $submit_now->{$lang} : $submit_now->en; ?></button>
                </form>
                <div id="formbuilder_lead" style="display:none;font-size:16px;color:red;"></div>

            </div>
        </div>
    </div>

    <!-- address -->
    <div class="address-section">
        <div class="container">
            <ul>
                <li>
                    <i class="fa fa-phone-alt"></i>
                    <h5><?php echo (isset($call_us->{$lang})) ? $call_us->{$lang} : $call_us->en; ?></h5>
                    <p>+91 <?php echo htmlentities($firstPhone) . htmlentities($secondPhone); ?></p>
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" width="30" height="30">
                    <defs>
                    <image width="30" height="30" id="img1" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACIAAAAtCAMAAAD8z0klAAAAAXNSR0IB2cksfwAAAY9QTFRF9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcf9pcfYSc5fwAAAIV0Uk5TAApMk8Hd7ejLqGwgG4T/865HA17kmx04zOyDL+Z+K+d6MN97xTxC/qzSS1vGAbnwmVVKddD8LR/1oyYGan+wQ8OG/cflpMli7ryUM7o2Ze+ATQTU4FFcHKkqFPT6vvgjSbLKPzT5mvdyOW8IOzXxjgInEdNYULic6x4OcdcLveGqkhcP4obOUosAAAGXSURBVHicfdRnV8JAEAXQsYF1BSsW7BgsKPYudqwodsXee8fe9YebgMlMkk3m27xzTzY72Q0AVkxsXHyCxWJNTEpOAV6lpiUwpdJtdh3IyMxi6srOUYtcB9NVXj4VBYV6IZYTRVExVzBWIovSMgPBWPk/qTAUjLkiolIwIe7IwKpMBBOqRVJjJhir9QDU0aDe29DY1EyTllZoayd9R6e0dFc3HUKPap1eeQw+EvZBPzYDLmWYg5gOwTA2IzhvP6ajMIbNOJIJTC0wiY0XyRQlHdg4PAoJYDoNM9gIQVnMzmE6Dwt0f/JjFkk4CUvppF1ekcBqIEQy8Ty4ScvW1vOd2VaabGwCbDHT2pZebcdMCLvS0nv7JuQg+v42Y3G4EiVtVkNyJE/quN1AxOEnOeELxym5jvx7ckYEnF9wxCWoyq8XfVdqAklacXGtEXBzqyF3WgEQVot7vQB4oOLxiUeeyc3YCPIEwBL+q174AqBbFq/a/Sr19h4VIZ+REE/tR4QEjAWAXfpjfZoJgC9xv9/m5Gf9N6yJ/gAlJlmoD6xzcwAAAABJRU5ErkJggg=="></image>
                    </defs>
                    <style>
                        tspan {
                            white-space: pre
                        }
                    </style>
                    <use id="Layer 92 copy" href="#img1" x="0" y="0"></use>
                    </svg>
                    <h5><?php echo (isset($address->{$lang})) ? $address->{$lang} : $address->en; ?></h5>
                    <?php
                    if ($QrySelectget['microsite_address'] != '') {
                        echo '<p>' . htmlentities($QrySelectget['microsite_address']) . '</p>';
                    } else {
                        echo '<p ' . $footer_font_color . '>One Indiabulls Centre Tower 1, 16th Floor, Jupiter Mill Compound, 841, Senapati Bapat Marg, Elphinstone Road, Mumbai - 400013</p>';
                    }
                    ?>
                </li>
                <li>
                    <i class="fa fa-envelope"></i>
                    <h5><?php echo (isset($email_us->{$lang})) ? $email_us->{$lang} : $email_us->en; ?></h5>
                    <a href="mailto:<?php echo $pcomp_row['person_email'] ?>"><p><?php echo $pcomp_row['person_email'] ?></p></a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Beetle Form -->
    <form method="post" style="display:none;">
        <input type="text" name="hide_form">
    </form>

    <a class="whatsapp" data-toggle="modal" data-target="#whatsappModal"><i class="fab fa-whatsapp"></i></a>
    <!-- Modal -->
    <div class="modal fade" id="whatsappModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered whats-modal" role="document">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="modal-body text-center">
                    <i class="fab fa-whatsapp"></i>
                    <h5>Hi!! Type your Whatsapp Message</h5>
                    <form id="whtsapp_contact" method="post">
                        <div class="form-group">
                            <textarea class="form-control" id = "whatsapp_msg" placeholder="Type a message"></textarea>
                        </div>
                        <div class="form-group">
                            <!-- <label>Share Your Email Id</label> -->
                            <input type="email" id="whatsapp_email" placeholder="Share Your Email Id*" value="<?php echo $mofficialEMail; ?>" class="form-control">
                        </div>
                        <button name="submit" type="button" value="Send" onclick="return submit_formbuilder_whstapp();" id="send-valuewhtsapp" class=" btn main-btn">Share Message</button>
                    </form>
                    <div id="formbuilder_whstapp" style="display:none;font-size:16px;color:red;"></div>

                </div>
            </div>
        </div>
    </div>

    <!-- Download Modal -->
    <div class="modal fade" id="downloadModal" tabindex="-1" role="dialog" aria-labelledby="downloadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered whats-modal modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Please fill the form below to begin.</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="page-name showcaseLeadPopup" method="post"  id="frmcmp" method="post" action="<?php echo ($download_attachment_link) ? $sitepath : '../'; ?>add-lead-proces.php" enctype="multipart/form-data" >
                        <div class="row">
                            <div class="form-group col-md-6">
                                <input name="fname" type="text" value="<?php echo $mfName; ?>" placeholder="First Name" class="form-control"/>
                            </div>
                            <div class="form-group col-md-6">
                                <input name="lname" type="text" value="<?php echo $mlName; ?>" placeholder="Last Name" class="form-control"/>

                            </div>
                            <div class="form-group col-md-6">
                                <input name="email" id="scpemail" type="email" value="<?php echo $mofficialEMail; ?>" placeholder="Email Address*" class="form-control" required />
                            </div>
                            <div class="form-group col-md-6">
                                <input name="phone" type="text" id="check_phone" maxlength="12" value="<?php echo (isset($mPhone)) ? $mPhone : ''; ?>" placeholder="Phone Number*" maxlength="12" class="form-control" required />
                            </div>
                            <div class="form-group col-md-6">
                                <input name="city" id="city" value="<?php echo $mCompname; ?>" type="text" placeholder="City" class="form-control"/>
                            </div>
                        </div>
                        <div>
                            <span style="color: red;margin-left: 4px;" id="validation_error"></span>
                        </div>
                        <p><input id="lead-popup" type="checkbox"><label> I understand that I would be contacted with regards to this request placed and consent for the same in spite of being registered with the National Customer Preference Registry (NCPR) with TRAI. I understand that there is a de-registration facility ( for not receiving such calls) which I may avail if required in future.</label></p>
                        <button id="showcase-subtn" class="btn main-btn">Submit</button>


                        <input name="source" type="hidden" value="<?php echo $source; ?>" id="source" />
                        <input name="caseids" type="hidden" value="<?php echo ($download_attachment_link) ? $camp_id : $caseId; ?>" id="caseids" />
                        <input name="channel_type" type="hidden" value="<?php echo $channel_type ?>" id="channel_type" />
                        <input name="camp_id" type="hidden" value="<?php echo $camp_id ?>" id="camp_id" />
                        <input name="contentType" type="hidden" value="<?php echo $contentType ?>" id="contentType" />
                        <input name="ref_url" type="hidden" value="<?php echo $ref_url ?>"  />
                        <input name="microsite_captcha" type="hidden" value="MCR123" id="microsite_captcha" />
                        <input name="client_id" type="hidden" value="<?php echo $c_lient_Id ?>"  />
                        <input name="c_" type="hidden" value="<?php echo $contact_id; ?>" />
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php if ($QrySelectget['microsite_footernavigationmenu'] == '1' || $QrySelectget['microsite_footeraboutme'] == '1' || $QrySelectget['microsite_footercontactdetails'] == '1') { ?>
        <!-- Footer -->
        <footer>
            <div class="container">
                <ul class="social">
                    <!--
                                                    <li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo htmlentities($url1); ?>"><i class="fab fa-facebook-f"></i></a></li>
                                                    <li><a href="https://twitter.com/intent/tweet?url=<?php echo htmlentities($url1); ?>"><i class="fab fa-twitter"></i></a></li>
                                                    <li><a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo htmlentities($url1); ?>"><i class="fab fa-linkedin-in"></i></a></li>
                    -->
                    <!--
                                                    <li><a  href="#" onClick = "return share(event);"><i class="fab fa-whatsapp"></i></a></li>
                    -->
                </ul>

            </div>
        </footer>
    <?php } ?>



    <script src="<?php echo $https_status . $_SERVER['HTTP_HOST']; ?>/css/newassets/js/jquery.js"></script>
    <script src="<?php echo $https_status . $_SERVER['HTTP_HOST']; ?>/css/newassets/js/popper.min.js"></script>
    <script src="<?php echo $https_status . $_SERVER['HTTP_HOST']; ?>/css/newassets/js/bootstrap.min.js"></script>
    <script src="<?php echo $https_status . $_SERVER['HTTP_HOST']; ?>/css/newassets/js/aos.js"></script>
    <script src="<?php echo $https_status . $_SERVER['HTTP_HOST']; ?>/css/newassets/js/owl.carousel.min.js"></script>
    <script src="<?php echo $https_status . $_SERVER['HTTP_HOST']; ?>/js/mailcrypt.js"></script>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo G_RECAPTCHA_KEY; ?>"></script>
    <script>var microsite = 1;</script>
    <?php include("includes/footer-event.php"); ?>
    <script>
        jQuery(document).ready(function () {
            //Code added for set selected language, Rahul Khan 15 07 2021
            var currentLang = "<?php echo $lang; ?>"
            $("#languages option[value='" + currentLang + "']").attr("selected", "selected");
            //End
        });
        function share(e) {
            e.preventDefault();
            var meesage = "<?php echo htmlentities(ucfirst($caseStudyActualtitle)) . " " . htmlentities($url1); ?>";
            encodeURIComponent();
            var URL = ' https://wa.me/?text=' + encodeURIComponent(meesage);
            window.location.href = URL;
        }
        var swJ = jQuery.noConflict();
        swJ(document).ready(function () {

            var prefix = "+91" + "<?php echo ($track_url_params['contact_details']['mobile']) ? trim(str_replace('+91', '', $track_url_params['contact_details']['mobile'])) : ''; ?>";
            swJ('#number-339').val(prefix);
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
<?php if ($download_attachment_link) { ?>
            frrm('.download_attachment_btn').click(function () {
                frrm('form.showcaseLeadPopup').submit();
            });
<?php } else { ?>
            frrm('#frmcmp').submit(function (event) {
                event.preventDefault();
                let checkPhone = /^[6789]\d{9}$/.test(frrm('#check_phone').val());
                frrm('#validation_error').text('');
                if (!checkPhone) {
                    frrm('#validation_error').text("*Please enter a valid mobile number.");
                    return false;
                }
                if (frrm('#lead-popup').prop("checked") == false) {
                    frrm('#validation_error').text("*Please enable the checkbox");
                    return false;
                }
                grecaptcha.ready(function () {
                    grecaptcha.execute('<?php echo G_RECAPTCHA_KEY; ?>', {action: 'create_second_form'}).then(function (token) {
                        frrm('#frmcmp').prepend('<input type="hidden" name="g-recaptcha-response" value="' + token + '">');
                        document.getElementById("frmcmp").submit();
                    });
                });
            });
<?php } ?>


        function getLinkWhastapp(number, message) {
            if (number.substr(0, 3) == '+91') {
                var url = 'https://api.whatsapp.com/send?phone=' + number + '&text=' + encodeURIComponent(message)
                return url
            } else {
                var url = 'https://api.whatsapp.com/send?phone=+91' + number + '&text=' + encodeURIComponent(message)
                return url
            }
        }

        function submit_formbuilder_whstapp() {
            var frm = jQuery.noConflict();
            var c_lient_Id = '<?php echo $c_lient_Id; ?>';
            var ref_url = "<?php echo htmlentities($_SERVER['HTTP_REFERER']); ?>";
            var whatsapp_msg = frm("#whatsapp_msg").val();
            var whatsapp_email = frm("#whatsapp_email").val();
            var form_refer = "Microsite request page";
            var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
            var intRegex = /[0-9 -()+]+$/;
            var beetle = frm('input[name="beetle"]').val();

            var isValid = true;
            frm("#whatsapp_email").each(function () {
                if (frm.trim(frm(this).val()) == '' || (!emailReg.test(whatsapp_email))) {
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

            frm("#whatsapp_msg").each(function () {
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

            if (isValid == false) {
                return false;
            } else {
                let ajxPath = location.origin + "/sp-formbuilder-submit.php";
                grecaptcha.ready(function () {
                    grecaptcha.execute('<?php echo G_RECAPTCHA_KEY; ?>', {action: 'create_second_form'}).then(function (token) {
                        frm('#whtsapp_contact').prepend('<input type="hidden" name="g-recaptcha-response" value="' + token + '">');
                        frm.ajax({
                            type: "post",
                            url: ajxPath,
                            data: {"beetle": beetle, "message": whatsapp_msg, "email1": whatsapp_email, "c_lient_Id": c_lient_Id, "form_refer": form_refer, "ref_url": ref_url, "g-recaptcha-response": token},
                            cache: false,
                            crossDomain: true,
                            beforeSend: function () {
                                frm('#send-valuewhtsapp').attr("disabled", true).val('Please wait ...');
                            },
                            success: function (result) {
                                window.location.href = getLinkWhastapp("<?php echo $firstPhone . '' . $secondPhone; ?>", whatsapp_msg);

                            }
                        });
                    });
                });

            }
        }



        function submit_formbuilder() {
            var frm = jQuery.noConflict();
            var c_lient_Id = '<?php echo $c_lient_Id; ?>';
            var ref_url = "<?php echo htmlentities($_SERVER['HTTP_REFERER']); ?>";
            var hide339 = frm("#hide-339").val();
            var full_name = hide339 ? frm("#your-second-name").val() : frm("#your-name").val();
            var email = hide339 ? frm("#your-second-email").val() : frm("#your-email").val();
            var contact = hide339 ? frm("#your-second-number-339").val() : frm("#number-339").val();
            var form_refer = "Microsite request page";
            var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
            var intRegex = /[0-9 -()+]+$/;
            var beetle = frm('input[name="beetle"]').val();

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
                var numberslen = frm.trim(frm(this).val());
                if ((frm.trim(frm(this).val()) == '' || (!intRegex.test(contact))) || numberslen.length < 13) {
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

            if (isValid == false) {
                return false;
            } else {
                let ajxPath = location.origin + "/sp-formbuilder-submit.php";
                grecaptcha.ready(function () {
                    grecaptcha.execute('<?php echo G_RECAPTCHA_KEY; ?>', {action: 'create_second_form'}).then(function (token) {
                        frm('#contact_frm').prepend('<input type="hidden" name="g-recaptcha-response" value="' + token + '">');
                        frm.ajax({
                            type: "post",
                            url: ajxPath,
                            data: {"beetle": beetle, "full_name1": full_name, "email1": email, "c_lient_Id": c_lient_Id, "form_refer": form_refer, "ref_url": ref_url, "contact1": contact, "g-recaptcha-response": token},
                            cache: false,
                            crossDomain: true,
                            beforeSend: function () {
                                frm('#send-value').attr("disabled", true).val('Please wait ...');
                            },
                            success: function (result) {
                                if (result.status) {
                                    frm("#formbuilder_lead").css('color', 'green');
                                    frm("#formbuilder_lead").show().text('Thank you for submitting. We will get back to you soon.').fadeIn(300).delay(3000).fadeOut(800);
                                } else {
                                    frm("#formbuilder_lead").css('color', 'red');
                                    frm("#formbuilder_lead").show().text(result.message).fadeIn(300).delay(3000).fadeOut(800);
                                    frm('#send-value').attr("disabled", false).val("Submit");
                                }
                                window.setTimeout(function () {
                                    window.location.reload();
                                }, 2000);
                            }
                        });
                    });
                });
            }
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
    $baseUrl = base_url();
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
        if(Is_chatbot == 1){
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

        <?php require("chatbot/js/comman_function1.php"); 
        }else if(Is_chatbot == 2) { ?>
            <!-- Start of HubSpot Embed Code -->
            <script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/8262976.js"></script>
        <!-- End of HubSpot Embed Code -->
        <?php } ?>
        <script>

                        function changeLang(val) {
                            //alert(val);
                            var currentUrl = window.location.href;
                            var currentLang = "<?php echo $_REQUEST['lang'] ?>";

                            var currentBase = "<?php echo $basepath . $_SERVER['HTTP_HOST'] ?>/";

                            if ((currentLang != undefined) && (currentLang != "")) {
                                var currentUrl = currentUrl.replace(currentLang, val);
                            } else {
                                var NewHost = "<?php echo $basepath . $_SERVER['HTTP_HOST'] ?>/" + val + "/";
                                var currentUrl = currentUrl.replace(currentBase, NewHost);
                            }
                            window.location.href = currentUrl;
                        }

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

                        function disableEditableContent() {
                            var contentLen = jQuery("div").length;
                            if (contentLen > 0) {
                                jQuery("div").each(function () {
                                    var attr = jQuery(this).attr('contenteditable');
                                    if (typeof attr !== typeof undefined && attr !== false) {
                                        jQuery(this).attr('contenteditable', false);
                                    }
                                });
                            }
                        }

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

    jQuery(document).ready(function () {
        jQuery(function () {
            AOS.init();
        });
        // owl-carousel
        jQuery('.landing-carousel').owlCarousel({
            loop: true,
            margin: 10,
            dots: false,
            responsiveClass: true,
            autoplay: false,
            nav: true,
            responsive: {
                0: {
                    items: 1,
                },
                768: {
                    items: 1,
                },
                1000: {
                    items: 1,
                }
            }
        });

        // owl-carousel
        jQuery('.test-carousel').owlCarousel({
            loop: true,
            margin: 10,
            dots: true,
            responsiveClass: true,
            dotsEach: 2,
            autoplay: true,
            responsive: {
                0: {
                    items: 1,
                    dots: true
                },
                768: {
                    items: 2,
                    dots: true
                },
                1000: {
                    items: 2,
                    dots: true
                }
            }
        });

        // owl-carousel
        jQuery('.content-carousel').owlCarousel({
            loop: true,
            margin: 10,
            dots: true,
            autoplay: true,
            smartSpeed: 800,
            responsiveClass: true,
            dotsEach: 2,

            responsive: {
                0: {
                    items: 1,
                    dots: true
                },
                768: {
                    items: 2,
                    dots: true
                },
                1000: {
                    items: 3,
                    dots: true
                }
            }
        });

        // owl-carousel
        jQuery('.pdf-carousel').owlCarousel({
            loop: true,
            margin: 10,
            dots: false,
            nav: true,
            autoplay: true,
            responsiveClass: true,
            dotsEach: 2,
            responsive: {
                0: {
                    items: 1,
                },
                768: {
                    items: 1,
                },
                1000: {
                    items: 1,
                }
            }
        });


        // card-responsive

        function isScrolledIntoView(elem) {
            var docViewTop = jQuery(window).scrollTop();
            var docViewBottom = docViewTop + jQuery(window).height();

            var elemTop = jQuery(elem).offset().top;
            var elemBottom = elemTop + jQuery(elem).height();

            return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
        }

        jQuery(window).scroll(function () {
            jQuery('.flip-card').each(function () {
                if (isScrolledIntoView(this) === true) {
                    jQuery(this).addClass('showcard');
                } else {
                    jQuery(this).removeClass('showcard');
                }
            });

        });

        // fix header
        jQuery(window).scroll(function () {
            var sticky = jQuery('.navbar'),
                    scroll = jQuery(window).scrollTop();

            if (scroll >= 100)
                sticky.addClass('fixed-header');
            else
                sticky.removeClass('fixed-header');
        });

        jQuery("nav li").click(function () {
            jQuery("nav li").removeClass("active");
            // $(".tab").addClass("active"); // instead of this do the below 
            jQuery(this).addClass("active");
        });
        jQuery(document).ready(function () {
            //  if ($('#routeName').data('route') === 'homepage') {
            // Add smooth scrolling to all links
            jQuery("nav li a").on('click', function (event) {

                // Make sure this.hash has a value before overriding default behavior
                if (this.hash !== "") {
                    // Prevent default anchor click behavior
                    event.preventDefault();
                    // Store hash
                    var hash = this.hash;

                    // Using jQuery's animate() method to add smooth page scroll
                    // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
                    jQuery('html, body').animate({
                        scrollTop: jQuery(hash).offset().top
                    }, 800, function () {

                        // Add hash (#) to URL when done scrolling (default click behavior)
                        window.location.hash = hash;

                    });
                } // End if
                jQuery('#navbarSupportedContent').removeClass('show');
            });
            //  }
        });
    });

</script>

</html>
<?php

function base_url() {
    return sprintf(
            "%s://%s", isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http', $_SERVER['SERVER_NAME']
    );
}
?>
