<?php
/*Author name: <Bizight Solutions Pvt Ltd> 
 * Purpose of document/ page : <This page is used for client microsite where content dispaly , tack end user activity and capture lead.> 
 * Date: 17-02-2021 
 * Copyright info : <Copyright @2021, Bizight Solutions Pvt Ltd>
*/

//If the HTTPS is not found to be "on"
$basepath = $https_status = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on')) ? "https://" : "http://";
if ($https_status == "http://") {
    //Tell the browser to redirect to the HTTPS URL and prevent the rest of the script from executing
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
    exit;
}
// Redirecting users to Admin Login Page
if (strstr($_SERVER['HTTP_HOST'], 'app.') != '' || strstr($_SERVER['HTTP_HOST'], 'app1.') != '') {
    header("location: login.php");
    exit;
}

require realpath(__DIR__ . '/vendor/autoload.php');

include("includes/global.php");

include("includes/function.php");
include("manager/common_functions.php");
include("csrf/csrf-magic.php");
include_once 'helpers/detect-device.php';

header("Access-Control-Allow-Origin: *");

unset($_SESSION['contName']);
unset($_SESSION['solName']);

//~ error_reporting(E_ALL);
//~ ini_set('display_errors', 1);
//~ ini_set('display_startup_errors', 1);

require_once 'includes/connect-new.php';
include_once "manager/classes/admin/settings/achievements.class.php";
include_once "manager/classes/admin/settings/partnertestimonials.class.php";

$obj = new \Microsite\Microsite($connPDO);
if ($obj->microsite_exists === false) {
    header("location: microsite-notfound.php");
    exit;
}

$c_lient_Id = $obj->client_id;
$p_client_id = $obj->parent_id;
$pcmember_pc_type = $obj->account_type;

$domainData = $obj->get_domain_detail();
$subdomain_url = $domainData['subdomain_url'];
if ($pcmember_pc_type === 'C') {
    $sdomainCmsPath = $domainData['cms_subdomain_url'];
    $ptr_category = $obj->partner_category;

    if ($ptr_category != '') {
        $ptr_category_arr = explode(',', $ptr_category);
    }

    $templateArr = $obj->get_template_detail();
    foreach ($templateArr as $template) {
        $cnt_ptr_category = $template['partner_category'];
        $cnt_users_category = $template['users_category'];
        $syndCaseId = $template['id'];

        if ($template['case_study_title'] != "") {
            $csname = str_replace(' ', '-', $template['case_study_title']);
            $cont_publish_url = "https://{$sdomainCmsPath}/showcase/{$csname}";
        } else {
            $cont_publish_url = "";
        }

        if ($cnt_ptr_category != '') {
            $cnt_ptr_category_arr = explode(',', $cnt_ptr_category);
            for ($m = 0; $m < sizeof($ptr_category_arr); $m++) {
                if (in_array($ptr_category_arr[$m], $cnt_ptr_category_arr)) {
                    if ($obj->template_syndicated($template['template_id']) === 0) {
                        $obj->template_syndication($syndCaseId, $template['template_id'], $cont_publish_url);
                    }
                }
            }
        }

        if ($cnt_users_category != '') {
            $cnt_users_category_arr = explode(',', $cnt_users_category);
            if (in_array($c_lient_Id, $cnt_users_category_arr)) {
                if ($obj->template_syndicated($template['template_id']) === 0) {
                    $obj->template_syndication($syndCaseId, $template['template_id'], $cont_publish_url);
                }
            }
        }
    }
    $parent_data = $obj->get_client_detail($p_client_id);
    $parent_microsite_detail = $obj->get_microsite_detail($p_client_id);
}

if (!empty($c_lient_Id)) {
    $QrySelectget = $obj->get_microsite_detail();
    if (count($QrySelectget) === 0 && $p_client_id != '') {
        $obj->insert_microsite();
        $refer = "http://{$_SERVER['HTTP_HOST']}";
        header("location: {$refer}");
        exit;
    }
}

$data = $obj->get_client_detail();
$p_comp_id = $data['comp_id'];

$emailExplode = explode('@', $data['person_email']);

$ext = substr($data['person_contact1'], 0, -10);
$firstPhone = substr($data['person_contact1'], strlen($data['person_contact1']) - 10, -6);
$secondPhone = substr($data['person_contact1'], strlen($data['person_contact1']) - 6);

$aboutcmpny = $data['about_company'];
$Namecmpny = $data['company_name'];

$faviconimg = $data['favicon'];
$company_logo = $data['header_logo'];
$about_me_font = $data['about_me_font'];
$solution_type_font = $data['solution_type_font'];
$header_bg_image = $data['header_bg_image'];
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

$micrositeDetails = $obj->get_micro_detail();
$newMicro = ($micrositeDetails['new_site_flag'] == 1) ? "" : "hidden";
$about_us = (strpos($_SERVER['HTTP_HOST'], 'technochimes.com') !== false);

$navbrand_center = '';
$navvar_center = '';
if (strpos($_SERVER['HTTP_HOST'], 'hdfcergoadvisor.com') !== false) {
//$navbrand_center='navbar-brand-center';
//$navvar_center='navbar-nav-center';   
}
$imgstartVal = explode('.', $subdomain_url);
$policont = (strpos($_SERVER['SERVER_NAME'], 'mutualfundpartner.com') !== false || strpos($_SERVER['SERVER_NAME'], 'nimfpartners.com') !== false || strpos($_SERVER['SERVER_NAME'], 'maxlifeinsurance.agency') !== false ) ? '' : 'hidden';

$lumsum_button = $obj->lumsum_button_url();
$sip_button = $obj->sip_button_url();
$nfo_button = $obj->default_button_url();

$headerFooterColour = $obj->header_footer_design();
$slider_arr = $obj->get_sliders();

$obja = new Achievements($connPDO);
$objt = new PartnerTestimonials($connPDO);
$objt->c_client  = $obja->c_client  = $c_lient_Id;
$objt->user_id   = $obja->user_id   = $obj->user_id;
$objt->sitepath  = $obja->sitepath  = $sitepath;

$achievements    = (isset($QrySelectget['microsite_achievements']) && $QrySelectget['microsite_achievements'] == '1') ? $obja->get_list('micro-site') : [];
$testimonials    = (isset($QrySelectget['microsite_testimonials']) && $QrySelectget['microsite_testimonials'] == '1') ? $objt->get_list('micro-site') : [];

/*---- Fetch Tracking Data ----*/
$track_url_params   = CommonStaticFunctions::get_track_url_params($https_status . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", $conn);
$concat_request_url = $track_url_params['request_uri'] ?? '';
$lang = "en";
if(isset($_REQUEST['lang']) && trim($_REQUEST['lang']) != ""){
	$lang = trim($_REQUEST['lang']);
}
error_reporting(0);
if(MICRO_SITE_DESIGN)
	include("indexnew.php");
else
	include("indexold.php");
?>
