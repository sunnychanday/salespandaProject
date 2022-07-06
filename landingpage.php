<?php
/* Author name: <Bizight Solutions Pvt Ltd> 
 * Purpose of document/ page : <This page is used to display landing page content.> 
 * Date: 17-02-2021 
 * Copyright info : <Copyright @2021, Bizight Solutions Pvt Ltd>
 */
//If the HTTPS is not found to be "on"
if (!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on") {
    //Tell the browser to redirect to the HTTPS URL.
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
    //Prevent the rest of the script from executing.
    exit;
}

header('Access-Control-Allow-Origin: *');
require realpath(__DIR__ . '/vendor/autoload.php');
include("includes/global.php");
include("includes/function.php");
include("manager/common_functions.php");
include("csrf/csrf-magic.php");
require_once 'includes/connect-new.php';
$obj = new \Microsite\Microsite($connPDO);
$default_Payment_Link = $obj->default_button_url();
$NFO_pay_link = (isset($default_Payment_Link['url']) && $default_Payment_Link['url'] !== '') ? $default_Payment_Link['url'] : '';


if (empty($c_lient_Id)) {
    $subdomainqry = mysqli_query($conn, "select client_id from sp_subdomain where cms_subdomain_url='" . $_SERVER['HTTP_HOST'] . "' and valid=1 and deleted=0 and status=1");
    $countmicrosite = mysqli_num_rows($subdomainqry);
    $subdomainget = mysqli_fetch_array($subdomainqry);
    $c_lient_Id = $subdomainget['client_id'];
}


$curl_handle = curl_init();
curl_setopt($curl_handle, CURLOPT_URL, 'http://ipinfo.io/' . (CommonStaticFunctions::get_remote_user_ip()));
curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
$ip_cty = curl_exec($curl_handle);
curl_close($curl_handle);
$ip_city = json_decode($ip_cty, true);

$ip_city = $ip_city['city'];

$fullLpath = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$urlpath = $_SERVER['REQUEST_URI'];

$urlpath_arr = explode('?channel_type=', $urlpath);
$channel_type = $urlpath_arr[1];
$channel_type_arr = explode('&camp_id=', $channel_type);
$contenttype_arr = explode('&content=', $channel_type);
$semail_type_arr = explode('&semail=', $channel_type);
$customerid_type_arr = explode('&customerID=', $channel_type);

if (count($channel_type_arr) > 1) {
    $channel_type = $channel_type_arr[0];
    $camp_id = explode('&', $channel_type_arr[1]);
    $camp_id = array_shift($camp_id);
    $camp_id = is_numeric($camp_id) ? $camp_id : decode($camp_id) ;
}

if (count($contenttype_arr) > 1) {
    $contentType = explode('&', $contenttype_arr[1]);
    $contentType = array_shift($contentType);
}

$semail = explode('&', urldecode($semail_type_arr[1]));
$semail = decode(array_shift($semail));

$customerID = urldecode($customerid_type_arr[1]);
$customerID = is_numeric($customerID) ? $customerID : decode($customerID);
$campaign_landingpage = $channel_type;

if ($_SERVER['HTTP_REFERER'] != '') {
    $ref_url = $_SERVER['HTTP_REFERER'];
}



$sqry = "select * from sp_subdomain where client_id='" . $c_lient_Id . "'";
$resq = mysqli_query($conn, $sqry);
$domianData = mysqli_fetch_array($resq);
$subdomain_url = $domianData['subdomain_url'];
$cms_subdomain = $domianData['cms_subdomain_url'];
$redirectPath = 'https://' . $subdomain_url;
$cmsUrl = 'https://' . $cms_subdomain;

$pc_member_info = getPCMemberInfo($c_lient_Id);

$pcmember_pc_type = $pc_member_info['member_pc_type'];
$p_client_id = $pc_member_info['p_client_id'];

$urlval = $_SERVER['REQUEST_URI'];
$urlvalexp = explode("/", $urlval);
$url_landpg = $urlvalexp[2];

$url_landpg_tw = explode("?", $url_landpg);
$url_landpg_tt = $url_landpg_tw[0];
$url_landpg_tt = $_REQUEST['case_study_id'];

$landingPageName = urldecode($url_landpg_tt);


if ($pcmember_pc_type == 'C') {

    $sqllandpage = "SELECT publish_page_id FROM sp_landingpage_publish where client_id='" . $p_client_id . "' and publish_page_name ='" . $landingPageName . "' and syndication_status=1";
    $rslandpage = mysqli_query($conn, $sqllandpage);
    $getlpVal = $rowlandpage = mysqli_fetch_array($rslandpage);


    $sqlsyndlandpage = "SELECT lsyndid, landingpage_id FROM sp_landingpage_syndication where p_client_id = '" . $p_client_id . "' and c_client_id='" . $c_lient_Id . "' and landingpage_id='" . $getlpVal['publish_page_id'] . "' and valid=1 and deleted=0";
    $rssyndlandpage = mysqli_query($conn, $sqlsyndlandpage);
    $syndlandrecordcount = mysqli_num_rows($rssyndlandpage);

    if ($syndlandrecordcount == 0) {

        $addsyndlandcont = "insert into sp_landingpage_syndication set p_client_id='" . $p_client_id . "',	
                                            c_client_id='" . $c_lient_Id . "',									
            								landingpage_id='" . $getlpVal['publish_page_id'] . "',
            								approve=1,
            								doe='" . $doe . "'";
        $ressyndlandcont = mysqli_query($conn, $addsyndlandcont);

        $refer = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        echo "<meta http-equiv='refresh' content='0;URL=$refer'>";
    }
}




if ($pcmember_pc_type == 'C') {


    $pagequery = mysqli_query($conn, "select LS.*, LP.publish_page_id,LP.publish_content,LP.publish_page_name,LP.publish_content,LP.lp_googlecode,LP.lp_embedcode,LP.landingpage_title,LP.publish_page_name,LP.landingpage_desc,LP.page_title_seo,LP.meta_description from sp_landingpage_publish as LP INNER JOIN sp_landingpage_syndication as LS ON LP.publish_page_id = LS.landingpage_id where LP.client_id='" . $p_client_id . "' and LS.c_client_id='" . $c_lient_Id . "' and LS.approve=1 and LP.publish_page_name='" . mysqli_real_escape_string($conn, $landingPageName) . "'");
} else {

    $pagequery = mysqli_query($conn, "select publish_page_id,publish_page_name,publish_content,lp_googlecode,lp_embedcode,publish_content,landingpage_title,landingpage_desc,page_title_seo,meta_description from sp_landingpage_publish where client_id='" . $c_lient_Id . "' and approve=1 and publish_page_name='" . mysqli_real_escape_string($conn, $landingPageName) . "'");
}
//Added on Aug 24,2018 for existing known capturing
if (isset($semail) && $semail != '') {
    $update_content = "update sp_contact set known=1,known_date='" . date('Y-m-d H:i:s') . "' where email_id='" . $semail . "' and client_id='" . $c_lient_Id . "'";
    mysqli_query($conn, $update_content);
}
//end


if (mysqli_num_rows($pagequery) > 0) {
    $pagerow = mysqli_fetch_array($pagequery);
    $pageId = $pagerow['publish_page_id'];
    $pagetitle = $pagerow['landingpage_title'];
    $pagedec = $pagerow['landingpage_desc'];
    $pageseotitle = $pagerow['page_title_seo'];
    $pagemetadec = $pagerow['meta_description'];
    $lp_googlecode = $pagerow['lp_googlecode'];
    $publishlp_name = $pagerow['publish_page_name'];
    $lp_embedcode = $pagerow['lp_embedcode'];


    $doc = new DOMDocument();
    $doc->loadHTML($pagerow['publish_content']);
    $xml = simplexml_import_dom($doc);
    $images = $xml->xpath('//img');

    foreach ($images as $img) {
        $img['src'][0];
    }

    if (empty($pagemetadec)) {
        $pagemetadec = $pagerow['landingpage_desc'];
    }

    if ($pcmember_pc_type == 'C') {



        $page_set = mysqli_query($conn, "select * from sp_landingpage_manage where id='" . $pageId . "' and client_id='" . $p_client_id . "' and valid=1 and deleted=0");
    } else {
        $page_set = mysqli_query($conn, "select * from sp_landingpage_manage where id='" . $pageId . "' and client_id='" . $c_lient_Id . "' and valid=1 and deleted=0");
    }
    $page_get = mysqli_fetch_array($page_set);
    $btn_text = $page_get['btn_text'];

    $pagenum = mysqli_num_rows($pagequery);
//$pagenum=mysqli_num_rows($pagequery);

    if ($pagenum == 0) {
        header("Location:$redirectPath");
        exit;
    }


    include("geoiploc.php");

    $ip = CommonStaticFunctions::get_remote_user_ip();
    $ip_location = getCountryFromIP($ip, " NamE ");

    $agent = $_SERVER['HTTP_USER_AGENT'];

    function detectDevice() {
        $userAgent = $_SERVER["HTTP_USER_AGENT"];
        $devicesTypes = array(
            "computer" => array("msie 10", "msie 9", "msie 8", "windows.*firefox", "windows.*chrome", "x11.*chrome", "x11.*firefox", "macintosh.*chrome", "macintosh.*firefox", "opera"),
            "tablet" => array("tablet", "android", "ipad", "tablet.*firefox"),
            "mobile" => array("mobile ", "android.*mobile", "iphone", "ipod", "opera mobi", "opera mini"),
            "bot" => array("googlebot", "mediapartners-google", "adsbot-google", "duckduckbot", "msnbot", "bingbot", "ask", "facebook", "yahoo", "addthis")
        );
        foreach ($devicesTypes as $deviceType => $devices) {
            foreach ($devices as $device) {
                if (preg_match("/" . $device . "/i", $userAgent)) {
                    $deviceName = $deviceType;
                }
            }
        }
        return ucfirst($deviceName);
    }

    $device_type = detectDevice();



    $insert_lp = mysqli_query($conn, "INSERT INTO sp_landingpage_stats set landingpage_id='" . $pageId . "',lp_visit='1',entry_time='" . $doe . "', client_id='" . $c_lient_Id . "',lp_ip='" . $ip . "',lp_location='" . $ip_city . "',camp_id='" . $camp_id . "',ref_url='" . $ref_url . "',channel_type='" . $campaign_landingpage . "',user_agent='" . $agent . "',device_type='" . $device_type . "'");
    ?>

    <!DOCTYPE html>
    <html>
        <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <title><?php echo htmlentities($pagetitle); ?></title>

            <meta name="author" content="<?php echo htmlentities($pageseotitle); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
            <meta name="description" content="<?php echo htmlentities($pagemetadec); ?>"/>
            <!-- BEGIN GLOBAL MANDATORY STYLES -->
            <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
            <link href="<?php echo htmlentities($cmsUrl); ?>/manager/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />   
            <!-- END GLOBAL MANDATORY STYLES -->
            <link href="<?php echo htmlentities($cmsUrl); ?>/manager/assets/global/css/components-rounded.min.css" rel="stylesheet" id="style_components" type="text/css" />
            <link href="<?php echo htmlentities($cmsUrl); ?>/manager/css/rangeslider.css" rel="stylesheet" type="text/css" />
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" ></script>
            <script src="<?php echo htmlentities($cmsUrl); ?>/manager/js/rangeslider.min.js" type="text/javascript"></script>

            <meta property="og:image" content="<?php echo htmlentities($img['src'][0]); ?>" />
            <meta property="og:image:secure_url" content="<?php echo htmlentities($img['src'][0]); ?>" /> 

            <meta property="og:image:width" content="200" /> 
            <meta property="og:image:height" content="200" />
            <meta property="fb:admins" content="100004474185988"/>
            <meta property="fb:app_id" content="1614773665402559"/>
            <meta property="og:title" content="<?php echo htmlentities($publishlp_name); ?>" />
            <meta property="og:description" content="<?php echo substr($pagemetadec, 0, 150); ?>" />
            <meta property="og:type" content="website"/>
            <meta property="og:url" content="http://<?php echo htmlentities($fullLpath); ?>"/>

            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:site" content="@<?php echo 'http://' . htmlentities($publishlp_name); ?>">
            <meta name="twitter:title" content="<?php echo 'http://' . htmlentities($publishlp_name); ?>">
            <meta name="twitter:description" content="<?php echo substr($pagemetadec, 0, 150); ?>">
            <meta name="twitter:image" content="<?php echo htmlentities($img['src'][0]); ?>">
            <script src="https://www.google.com/recaptcha/api.js?render=<?php echo G_RECAPTCHA_KEY; ?>"></script>

            <?php echo html_entity_decode($lp_googlecode); ?>
            <style>
            body {font-family: 'Roboto', sans-serif;}
            hr, p {margin: 10px;min-width: 40px;}
            /* Chrome, Safari, Edge, Opera */
                .hfcemi_frame input::-webkit-outer-spin-button,
                .hfcemi_frame input::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
                }

                /* Firefox */
                .hfcemi_frame input[type=number] {
                    -moz-appearance: textfield;
                }
                #form_btn{border-radius:4px;}
                .alpha a{
                    cursor: pointer;
                }
                .vframe
                {
                    pointer-events:auto !important;
                }
                .mapframe
                {
                    pointer-events:auto !important;
                }
                .thanks_msg 
                {
                    background-color: rgba(0, 0, 0, 0.4);
                    bottom: 0;
                    height: 100%;
                    left: 0;
                    position: fixed;
                    right: 0;
                    top: 0;
                    width: 100%;
                    z-index: 1112;
                }
                input
                {
                    border: 1px solid #333333;
                }
                #landingpage_lead
                {
                    background-color: rgba(0, 0, 0, 0.4);
                    bottom: 0;
                    height: 100%;
                    left: 0;
                    position: fixed;
                    right: 0;
                    top: 0;
                    width: 100%;
                    z-index: 1111;
                    text-align:center;
                    font-size:14px!important;
                }

                #p_field_wig .rangeslider__fill {
                    background: #103880;
                    border-radius: 4px;
                }
                #p_field_wig .rangeslider__handle{
                    background: #fff;
                    border: solid 1px #fff;
                    box-shadow: 0 0px 8px 2px rgb(0 0 0 / 15%);
                    height: 20px;
                    position: absolute;
                    width: 20px !important;
                }
                #p_field_wig  .rangeslider__handle:after{
                    content: none;
                }
                #p_field_wig .rangeslider {
                    background: #ececec;
                    position: relative;
                    box-shadow: none;
                    border-radius: 4px;
                    height: 4px;
                    margin-top: 20px;
                }
                .ulquiz select {min-height: 38px;}
                #p_field_wig #add_formfields{display: none;}
                #p_field_wig .question_hover{display: none;}
                #p_field_wig .addimg_icon{display: none;}
                #p_field_wig .trash_icon{display: none;}
                #p_field_wig .edit_icon{display: none;}
                .resizable{
                    width:290px
                }
                .disableslidebtn {
                    opacity: 0.4;
                    pointer-events: none;
                }
                /* 
                #section3 {
                    width: 370px !important;
                } */
                #btndrag {
                    /*left: 0 !important;*/
                }

                /* #section3 {
                    width: 352px !important;
                } */
                @-moz-keyframes blinker {  
                    0% { opacity: 1.0; }
                    50% { opacity: 0.0; }
                    100% { opacity: 1.0; }
                }

                @-webkit-keyframes blinker {  
                    0% { opacity: 1.0; }
                    50% { opacity: 0.0; }
                    100% { opacity: 1.0; }
                }

                @keyframes blinker {  
                    0% { opacity: 1.0; }
                    50% { opacity: 0.0; }
                    100% { opacity: 1.0; }
                }
                @media(max-width: 767px) {

                    /*#formfield {
                    width: 100% !important;
                    }*/
                /* 
                    div[id^=imgbox]  {
                        width: 100% !important;
                        max-width: 100% !important;
                        left: 0px !important; 
                        margin: auto !important;
                    } */
                    /* div[id^=imgbox] img {
                        width: 100% !important;
                        max-width: 100% !important;
                        height: auto !important;
                        object-fit: contain !important;
                    } */
                    /* .paragraph, #p_field {
                        width: 100% !important;
                        max-width: 100% !important;
                        margin: auto !important;
                        left: 0px !important;
                        padding: 0 15px !important;
                    } */
                    /*.block_inner {
                        width: 100% !important;
                        max-width: 100% !important;
                        margin: auto !important;
                        left: 0px !important;
                    }*/
                    /* #section5{
                        height: 150px !important;
                    }
                    #section4 {
                        height: 700px !important;
                    }
                    #imgbox4,
                    #imgbox5,
                    #imgbox6,
                    #imgbox10 {
                        width: 60px !important;
                        height: auto !important;    
                        left: 10px !important;
                    } 
                    #imgbox5 {top: 1480px !important;}
                    #imgbox6 {top: 1572px !important;}
                    #imgbox10 { top: 1698px !important}
                    #textbox3 {
                        padding-left: 100px !important;
                        height: 700px !important;
                    }
                    #textbox3 > p > span {
                        padding: 20px 0 !important;
                    }
                    #textbox3 > p {
                        margin-top: 12px !important;
                    }

                    #section3 {
                        width: 320px !important;
                        height: 380px !important;
                    }
                    #beta1 {
                        top: 165px !important;
                        left: 42px !important;
                    }
                    #textbox3 {
                        margin-top: -10px !important;
                    } 

                    #imgbox5 {
                        margin-top: 20px;
                    } */

                    /*#textcount {
                        width: 100% !important;
                    }*/

                    /* #p_field {
                        width: 90% !important;
                        margin: 20px auto !important;
                        position: relative !important;
                        display: block !important;
                        left: 0px !important;

                    }
                    #p_field_wig {
                        width: 90% !important;
                        margin: 50px auto 20px auto !important;
                        position: relative !important;
                        display: block !important;
                        left: 0px !important;
                    }

                    #textbox6.paragraph {
                        margin-top: 30px !important;
                     }    
                    #section3.ui-draggable-handle {
                        width: 100% !important;
                     }
                    #p_field { top: 730px !important}
                    #textbox3 { padding: 0px !important }
                    #textbox3 > p {left: 20px !important}
                    div#textbox4 {
                        background: #fff !important;
                        padding-top: 8% !important;
                      }
                    #imgbox3 img , #imgbox7 img{
                        height: 100% !important;
                     } */

                }


                @media only screen and (min-device-width : 320px) and (max-device-width : 480px) 
                {
                    #form_btn{-webkit-appearance: none; -moz-appearance: none;}

                    #lpchild_footer
                    {
                        width:90% !important;
                        margin:0 5%!important;
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
                    /*.paragraph
                    {
                        /*width:90%!important;
                        //left:0px!important;*/
                        /* margin:0% 5%!important; 
                        line-height:18px!important;
                        font-size:14px!important;
                    }*/
                    /* .resizable img
                    {
                        height: inherit !important;
                        width: auto !important;
                        max-width: 100% !important;
                    } */
                    .headbox
                    {
                        /*font-size:28px!important;
                        line-height:32px!important;
                        width:90% !important;
                        margin: 0 5% !important;
                        left: 0 !important;*/
                    }
                    br { display: none; } 
                    /*.paragraph ul li
                    {
                        margin:0px!important;
                        padding:0px 10px 10px 0px!important;
                        line-height:18px!important;
                        font-size:14px;
                    }*/
                    /*#p_field
                    {
                        width:90%!important;
                        left:0px!important;
                        margin:0% 5%!important;
                    }*/
                    .paragraph ul
                    {
                        margin: 0px;
                        padding: 10px 10px 10px 25px;
                    }
                    #lpchild_footer > img {
                        height: 50px;
                    }
                    #lpchild_footer > p
                    {
                        font-size:12px !important;
                    }
                    .alpha
                    {
                        margin:0px!important;
                    }
                    /*.formField
                    {
                        width:100%!important;
                    }*/
                    br
                    {
                        display:inline-block;
                    }
                }

                @media only screen and (min-device-width : 481px) and (max-device-width : 667px) 
                {
                    #form_btn{-webkit-appearance: none; -moz-appearance: none;}

                    /* #p_field
                     {
                         width:100% !important;
                         left:0px!important;
                         margin:0% 5%!important;
                     }*/
                    #lpchild_footer
                    {
                        width:90% !important;
                        margin:0 5%!important;
                    }
                    .resizable ,.alpha,.block_inner,.textcheck,.page
                    {
                        width:100%!important;
                        left:0px!important;
                        margin:0px!important;
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
                        /*width:90%!important;
                        //left:0px!important;*/
                        /* margin:0% 5%!important; */
                        line-height:24px!important;
                        font-size:16px!important;
                    }
                    .resizable img
                    {
                        height: 100%!important;
                    }
                    .headbox , .headbox span
                    {
                        width:auto!important;
                    }
                    br { display: none; }
                    #lpchild_footer > img {
                        height: 50px;
                    }
                    #lpchild_footer > p
                    {
                        font-size:12px !important;
                    }
                    .alpha
                    {
                        margin:0px!important;
                    }
                    .formField
                    {
                        width:100%!important;
                    }
                    br
                    {
                        display:inline-block;
                    }
                }
            </style>
            <script>
                mk = jQuery.noConflict() || $.noConflict();
                mk(function () {
                    // Change this selector to find whatever your 'boxes' are
                    var boxes = mk("div");

                    // Set up click handlers for each box
                    boxes.click(function () {
                        var el = mk(this), // The box that was clicked
                                max = 0;

                        // Find the highest z-index
                        boxes.each(function () {
                            // Find the current z-index value
                            var z = parseInt(mk(this).css("z-index"), 10);
                            // Keep either the current max, or the current z-index, whichever is higher
                            max = Math.max(max, z);
                        });

                        // Set the box that was clicked to the highest z-index plus one
                        el.css("z-index", max + 1);
                    });
                });
            </script>


            <script>
                mk(document).ready(function () {
                    mk("input").removeAttr('disabled');
                    mk("#textcount").prop("ondblclick", null);
                    mk(".popover").remove();
                    mk(".lightbox-area").remove();
                    mk(".ui-resizable-handle").remove();
                    mk(document).ready(function () {
                        landingpage_mobile();
                    })
                });

                function landingpage_mobile()
                {
                    mk = jQuery.noConflict() || $.noConflict();
                    var client_id = '<?php echo $c_lient_Id; ?>'
                    var page_id = mk("#page_id").html();
                    var semail = "<?php echo $semail; ?>";
                    var customerID = "<?php echo $customerID; ?>";
                    var channeltype = "<?php echo $campaign_landingpage; ?>";
                    var screen_width = mk(window).width();
                    var beetle = mk('input[name="beetle"]').val();
                    mk("#sp_mobile_landingpage").html('');
                    mk.ajax({url: "https://<?php echo $cms_subdomain; ?>/sp-mobile-landingpage.php",
                        type: "post",
                        data: {screen_width: screen_width, page_id: page_id, client_id: client_id, semail: semail, customerID: customerID, channeltype: channeltype, beetle: beetle},
                        cache: false,
                        success: function (result) {
                            mk("#sp_mobile_landingpage").html(result);
                            if (mk('body').find('.myRange').length > 0) {
                                mk('body').find('.rangeslider').remove();
                                mk('body').find('.myRange').rangeslider({polyfill: false});
                            }
                            mk("body h1:contains('{sipresult}')").hide();
                            mk("body h1:contains('{hlvresult}')").hide();
                            mk("body h1:contains('{nbhlcresult}')").hide();
                            mk("body h1:contains('{ergoresult}')").hide();
                            mk("body h1:contains('{caafscore}')").hide();
                            mk('body #p_field_wig #add_formfields').hide();
                            mk("body h1:contains('{hfc_emi}')").hide();
                            mk("body h1:contains('{hfc_loan}')").hide();        

                            mk("body").find('#textcount ul').each(function () {
                                var li = mk(this).find('> li');
                                var liColor = mk(this).find('span').css('color');
                                li.css('color', liColor);
                            });
                            if(mk('body').find('#textcount').width() < 420){
                                mk('#textcount').css('overflow', 'hidden');
                                mk('body').find('#p_field').css('width', '90%');
                                mk('body').find('#btndrag').css('padding', '0px');

                              //  mk('body').find('.texteffect').css({'margin': '0px 5%', 'width': '90%'});


                            }
                            // Start NFO link
                            mk("a").each(function () {
                                //alert(this.href);
                                var k = this.href;
                                var link = '<?php echo $NFO_pay_link ?>';
                                var n = k.search("nfo_link_button");
                                if (n >= 0)
                                {
                                    if (link != '')
                                    {
                                        this.href = link;
                                    } else {
                                        jQuery(this).closest('div').hide();
                                    }
                                }
                            })
                            // End NFO condition
                            mk('body').find("#p_field_wig .formdrag .ulquiz").css('display','none');
                            mk('body').find("#p_field_wig .slide1").css('display','block');
                            mk('body').find('#p_field_wig .carousel-control-prev').attr('data-slide', 'slide1');
                            mk('body').find('#p_field_wig .carousel-control-next').attr('data-slide', 'slide2');
                            mk('body').find('#p_field_wig #add_formfields').attr('data-slide', 'slide1');
                            mk('body').find("#p_field_wig .carousel-control-prev").addClass('disableslidebtn');
                            mk('body').find("#p_field_wig .carousel-control-next").removeClass('disableslidebtn');
                        }
                    });
                }
                function validate_numeric(evt) {
            var theEvent = evt || window.event;

                // Handle paste
                if (theEvent.type === 'paste') {
                    key = event.clipboardData.getData('text/plain');
                } else {
                // Handle key press
                    var key = theEvent.keyCode || theEvent.which;
                    key = String.fromCharCode(key);
                }
                var regex = /[0-9]|\./;
                if( !regex.test(key) ) {
                    theEvent.returnValue = false;
                    if(theEvent.preventDefault) theEvent.preventDefault();
                }
                changeRange(evt);
            }

            function ResultQuiz(){
                mk = jQuery.noConflict() || $.noConflict();
                mk('#QuizResultModal').modal('show');
            }

            function QuizSlideManage(elm){
                mk = jQuery.noConflict() || $.noConflict();
                var Selectslide = mk(elm).attr('data-slide');
                var CurrentSlide = mk('body').find('#p_field_wig #add_formfields').attr('data-slide');
                var bttype = mk(elm).attr('data-th');
                var total_qst = mk('body').find('#p_field_wig .ulquiz').length; 
                var slidenext = mk('body').find("#p_field_wig .carousel-control-next").attr('data-slide').replace ( /[^\d.]/g, '' );
                var slideprev = mk('body').find("#p_field_wig .carousel-control-prev").attr('data-slide').replace ( /[^\d.]/g, '' );
                //validate
                var validateslide = 0;
                mk("."+CurrentSlide+" .quizcount").each( function(){
                    let label = mk(this).find('label');
                    let labeltype = label.attr('data-thtype');
                    if(labeltype == 'singleselect'){
                        if(mk(this).find('input[type=radio]:checked').length == 0){ alert("Cannot be blank!"); validateslide++;}
                    }
                    if(labeltype == 'multiselect'){
                        if(mk(this).find('input[type=checkbox]:checked').length == 0){  alert("Cannot be blank!"); validateslide++;}
                    }
                    if(labeltype == 'dropdown'){
                        if(mk(this).find('select > option:selected').val() == ''){  alert("Cannot be blank!"); validateslide++;}
                    }
                    if(labeltype =='text_input'){
                        if(mk(this).find('input').val() == ''){   alert("Cannot be blank!"); validateslide++;}
                    }
                    if(labeltype == 'text_area'){
                        if(mk(this).find('textarea').val() == ''){   alert("Cannot be blank!"); validateslide++;}
                    }
                    if(mk('.slide'+slidenext).hasClass('result_outcome')){
                        validateslide++;
                    }
                });
                if((total_qst > 1) && (validateslide == 0)) {
                    mk('body').find("#p_field_wig .carousel-control-prev").removeClass('disableslidebtn');
                    mk('body').find("#p_field_wig .carousel-control-next").removeClass('disableslidebtn');

                    if(Selectslide != CurrentSlide){
                        
                        mk('body').find("#p_field_wig .formdrag .ulquiz").css('display','none');
                        mk('body').find("#p_field_wig .formdrag ul").siblings('.'+Selectslide).show('slide', {direction: 'right'}, 500);
                        mk('body').find('#p_field_wig #add_formfields').attr('data-slide', Selectslide);

                        if(bttype =='pre'){
                            if( (slideprev !=1) ){
                                var slideprevnew = 'slide'+ ( parseInt(slideprev) - parseInt(1) ); 
                                if(Selectslide ==  'slide'+ ( parseInt(slidenext) - parseInt(1) )){
                                    var slidepositonxt = 'slide'+ slidenext; 
                                }else{
                                    var slidepositonxt = 'slide'+ ( parseInt(slidenext) - parseInt(1) ); 
                                }
                            }else{
                                var slideprevnew = 'slide1';
                                var slidepositonxt = 'slide2'; 
                                mk('body').find("#p_field_wig .carousel-control-prev").addClass('disableslidebtn');
                                mk('body').find("#p_field_wig .carousel-control-next").removeClass('disableslidebtn');
                            }    
                            mk('body').find('#p_field_wig .carousel-control-prev').attr('data-slide', slideprevnew);
                            mk('body').find('#p_field_wig .carousel-control-next').attr('data-slide', slidepositonxt);
                        }else{
                        
                            if( (total_qst > slidenext) && (total_qst > slideprev)) {
                                var slidepositonxt = 'slide'+ ( parseInt(slidenext) + parseInt(1) ); 
                                var slideprevnew = 'slide'+ ( parseInt(slideprev) + parseInt(1) ); 
                            }else{
                                var slideprevnew = 'slide'+(parseInt(total_qst)- parseInt(1)); 
                                var slidepositonxt = 'slide'+(total_qst); 
                                mk('body').find("#p_field_wig .carousel-control-next").addClass('disableslidebtn');
                                mk('body').find("#p_field_wig .carousel-control-prev").removeClass('disableslidebtn');
                            }
                            mk('body').find('#p_field_wig .carousel-control-prev').attr('data-slide', slideprevnew);
                            mk('body').find('#p_field_wig .carousel-control-next').attr('data-slide', slidepositonxt);
                        }
                    }else{
                        if(bttype =='pre'){
                            if(slideprev > 1){
                                var slideprevnew = 'slide'+ ( parseInt(slideprev) - parseInt(1) ); 
                                mk('body').find("#p_field_wig .formdrag .ulquiz").css('display','none');
                                mk('body').find("#p_field_wig .formdrag ul").siblings('.'+slideprevnew).show('slide', {direction: 'right'}, 500);
                                mk('body').find('#p_field_wig #add_formfields').attr('data-slide', slideprevnew);

                            }else{
                                mk('body').find("#p_field_wig .carousel-control-prev").removeClass('disableslidebtn'); 
                            }                            
                        }else{
                            if(slidenext < total_qst){
                                var slidepositonxt = 'slide'+ ( parseInt(slidenext) + parseInt(1) ); 
                                mk('body').find("#p_field_wig .formdrag .ulquiz").fadeOut('300');
                                mk('body').find("#p_field_wig .formdrag ul").siblings('.'+slidepositonxt).show('slide', {direction: 'right'}, 500);
                                mk('body').find('#p_field_wig #add_formfields').attr('data-slide', slidepositonxt);
                            }else{
                                mk('body').find("#p_field_wig .carousel-control-next").addClass('disableslidebtn');
                            }

                        }
                    }
                }
            }
            function changeRange(obj) {
                mk = jQuery.noConflict() || $.noConflict();
                if(mk(obj).attr('data-th') !=''){ 
                    if(mk(obj).attr('data-th') =='amount'){

                        mk('body').find("#p_field_wig #amount").val(mk(obj).val()).change();
                    }

                    if(mk(obj).attr('data-th') =='rate'){

                        mk('body').find("#p_field_wig #rate").val(mk(obj).val()).change();
                    }

                    if(mk(obj).attr('data-th') =='timeperiod'){

                        mk('body').find("#p_field_wig #timeperiod").val(mk(obj).val()).change();
                    }

                    if(mk(obj).attr('data-th') =='amountexpval'){

                        mk('body').find("#p_field_wig #amountexp").val(mk(obj).val()).change();
                    }

                    if(mk(obj).attr('data-th') =='totalliabilityval'){

                        mk('body').find("#p_field_wig #totalliability").val(mk(obj).val()).change();
                    }
                    if(mk(obj).attr('data-th') =='amtneedval'){

                        mk('body').find("#p_field_wig #amtneed").val(mk(obj).val()).change();
                    }

                    if(mk(obj).attr('data-th') =='financialassetval'){

                        mk('body').find("#p_field_wig #financialasset").val(mk(obj).val()).change();
                    }
                    if(mk(obj).attr('data-th') =='currtlifecoverval'){

                        mk('body').find("#p_field_wig #currtlifecover").val(mk(obj).val()).change();
                    }

                    if(mk(obj).attr('data-th') =='desired_amount'){
                        mk('body').find("#p_field_wig #desired_amount").val(mk(obj).val()).change();
                    }
                    if(mk(obj).attr('data-th') =='rate_intrst'){
                        mk('body').find("#p_field_wig #rate_intrst").val(mk(obj).val()).change();
                    }
                    if(mk(obj).attr('data-th') =='year_loantenure'){
                        mk('body').find("#p_field_wig #year_loantenure").val(mk(obj).val()).change();
                    }
                    if(mk(obj).attr('data-th') =='gross_monthy_income'){
                        mk('body').find("#p_field_wig #gross_monthy_income").val(mk(obj).val()).change();
                    }

                    if(mk(obj).attr('data-th') =='gross_emipaid'){
                        mk('body').find("#p_field_wig #gross_emipaid").val(mk(obj).val()).change();
                    }
                }

                if(mk(obj).attr('id') =='fq2'){
                    mk('body').find("#p_field_wig .timevallabel").html('Quarterly');
                }

                if(mk(obj).attr('id') =='fq1'){
                    mk('body').find("#p_field_wig .timevallabel").html('Months');
                }

                var maxinsured = mk('body').find('#p_field_wig #max_insured');
                var nbhlcAge = mk('body').find('#p_field_wig #nbhlc_age');
                var nbhlcFamily = mk('body').find('#p_field_wig #nbhlc_family');
                var nbhlcCity = mk('body').find('#p_field_wig #nbhlc_city');
                var nbhlcIncome = mk('body').find('#p_field_wig #nbhlc_income');
                let caafscore = '';

                var ergoage = mk('body').find('#p_field_wig #ergo_age');
                var ergofamily = mk('body').find('#p_field_wig #ergo_family');
                var ergocity = mk('body').find('#p_field_wig #ergo_city');
                var ergoincome = mk('body').find('#p_field_wig #ergo_income');
                var beetle = mk('input[name="beetle"]').val();


                var p =  mk('body').find("#p_field_wig #amount").val();
                var r =  mk('body').find("#p_field_wig #rate").val();
                var ntype =  mk('body').find("#p_field_wig input[name=timefreq]:checked").val(); 
                var n =   mk('body').find("#p_field_wig #timeperiod").val() * ntype;
                //hlc
                var amt_expect =  mk('body').find("#p_field_wig #amountexp");
                var total_liability=  mk('body').find("#p_field_wig #totalliability");
                var amt_needed=  mk('body').find("#p_field_wig #amtneed");
                var financial_asset=  mk('body').find("#p_field_wig #financialasset");
                var life_cover=  mk('body').find("#p_field_wig #currtlifecover");
                var hlv_age  = mk('body').find("#p_field_wig #hlv_age");

               //HFC Emi calculator
               let hfc_amount = mk('body').find("#p_field_wig #desired_amount");
                let hfc_rateint = mk('body').find("#p_field_wig #rate_intrst");
                let hfc_yearten = mk('body').find("#p_field_wig #year_loantenure");
                let hfc_monthlyinc = mk('body').find("#p_field_wig #gross_monthy_income");
                let hfc_emip = mk('body').find("#p_field_wig #gross_emipaid");

                if( (hfc_amount.length !=0) && (hfc_rateint.length !=0) && (hfc_yearten.length !=0) && (hfc_monthlyinc.length !=0) ){
                    let roi_month = parseFloat( (parseFloat(hfc_rateint.val()/12)/100) );
                    let ten_month = (hfc_yearten.val() * 12);
                    let f1 = parseFloat(1 + roi_month); //1 + roi
                    let f2 = Math.pow(f1, ten_month).toFixed(9); // (1 + Roi ) tenure in months
                   
                    let f3 = parseFloat(f2 * roi_month * hfc_amount.val()).toFixed(9); // PxRx(1+R)^N
                    let f4 = parseFloat(f2 - 1).toFixed(9); 
                    let femi = parseFloat(f3/f4).toFixed(9);
                    let incomefactor ;
                    if(hfc_monthlyinc.val() < 25000){
                         incomefactor = 0.45;
                    }
                    if(hfc_monthlyinc.val() > 50000){
                         incomefactor = 0.7;
                    }
                    if( (hfc_monthlyinc.val() > 25000) && (hfc_monthlyinc.val() < 50001) ){
                         incomefactor = 0.6;
                    }
                    let applicableincome = parseFloat( parseFloat(parseFloat(hfc_monthlyinc.val() * incomefactor) - hfc_emip.val()) ).toFixed(9);
                    let loanamt =  Math.floor( parseFloat(parseFloat(applicableincome/femi) * hfc_amount.val()) );
                    let loanemi =Math.ceil(loanamt*femi/hfc_amount.val());
                    if(loanamt < 0){
                        mk("body").find("#p_field_wig .noteligierr").remove();
                        mk("body").find("#p_field_wig").append("<h2 class='noteligierr' style='color:red'>You are not eligible for loan!</h2>");
                        mk("body").find("#eligi_loan").val(' ');
                        mk("body").find("#elgi_emi").val(' ');
                    }else{
                        mk("body").find("#p_field_wig .noteligierr").remove();
                        // console.log('loanamt='+loanamt+"loanemi="+loanemi);
                         mk("body").find("#eligi_loan").val(loanamt);
                         mk("body").find("#elgi_emi").val(loanemi);
                    }

                }

                if(mk(obj).attr('id') =='desired_amount') {
                    mk("body").find("#label_desired_amount").val(mk(obj).val());
                }
                if(mk(obj).attr('id') =='rate_intrst') {
                    mk("body").find("#label_rate_intrst").val(mk(obj).val());
                }
                if(mk(obj).attr('id') =='year_loantenure') {
                    mk("body").find("#label_year_loantenure").val(mk(obj).val());
                }
                if(mk(obj).attr('id') =='gross_monthy_income') {
                    mk("body").find("#label_gross_monthy_income").val(mk(obj).val());
                }
                if(mk(obj).attr('id') =='gross_emipaid') {
                    mk("body").find("#label_gross_emipaid").val(mk(obj).val());
                }

                if(mk(obj).attr('id') =='amount') {
                    mk("body").find("#amountval").html("");
                    mk("body").find("#amountval").val(mk(obj).val());
                }
                if(mk(obj).attr('id') =='rate'){
                    mk("body").find("#rateval").html("");
                    mk("body").find("#rateval").val(mk(obj).val());
                }

                if(mk(obj).attr('id') =='timeperiod'){
                    mk("body").find("#timeval").html("");
                    mk("body").find("#timeval").val(mk(obj).val());
                }   
                if(mk(obj).attr('id') =='amountexp'){
                    mk("body").find("#amountexpval").html("");
                    mk("body").find("#amountexpval").val(mk(obj).val());
                }  

                if(mk(obj).attr('id') =='totalliability'){
                    mk("body").find("#totalliabilityval").html("");
                    mk("body").find("#totalliabilityval").val(mk(obj).val());
                }  

                if(mk(obj).attr('id') =='amtneed'){
                    mk("body").find("#amtneedval").html("");
                    mk("body").find("#amtneedval").val(mk(obj).val());
                }  

                if(mk(obj).attr('id') =='financialasset'){
                    mk("body").find("#financialassetval").html("");
                    mk("body").find("#financialassetval").val(mk(obj).val());
                }  

                if(mk(obj).attr('id') =='currtlifecover'){
                    mk("body").find("#currtlifecoverval").html("");
                    mk("body").find("#currtlifecoverval").val(mk(obj).val());
                }  
                    //sip calculator
                    if( (mk("#amount").length !=0) && (mk("#rate").length !=0) && (mk("#timeperiod").length !=0) ) {

                        let r1 = parseFloat(r/100);
                        var i = parseFloat(r1/12);
                        let i1 = i + 1;
                        let pw1 = (Math.pow(i1, n)) - parseInt(1);
                        var est = parseInt(p) * parseFloat(pw1);
                        let est2 = parseFloat(i1) / parseFloat(i);
                        const FV = parseInt(parseFloat(est) * parseFloat(est2));
                        mk('body').find('#sip_finalresult').val(FV);
                    }
                    //hlc calculator
                    
                if( (amt_expect.length !=0) && (total_liability.length !=0) && (amt_needed.length != 0 ) 
                    && (financial_asset.length != 0 ) && (life_cover.length != 0 ) ) {
                        var h3 ='';
                        if(hlv_age.val() ==''){
                            alert('Enter age!');
                            return false
                        }
                        if(hlv_age.val() > 50){
                            h3 = 12;
                        }
                        if(hlv_age.val() < 50){
                            h3 = 20;
                        }
                        let compamut = parseInt(amt_expect.val()) * parseInt(12) * parseInt(h3);
                        let righthlv =parseInt(compamut) + parseInt(total_liability.val()) + parseInt(amt_needed.val());
                        let HLV = parseInt(righthlv ) - parseInt(parseInt(financial_asset.val()) + parseInt(life_cover.val())); 
                        mk('body').find('#p_field_wig #hlv_finalresult').val(HLV);
                }

                  //Nbhlc calculator 
                  if( maxinsured.length != 0 && nbhlcAge.length != 0 && nbhlcFamily.length != 0 ){  
                        let outcome_res = parseFloat(maxinsured.val()) * parseFloat(nbhlcAge.val()) * parseFloat(nbhlcFamily.val()) * parseFloat(nbhlcCity.val())* parseFloat(nbhlcIncome.val());
                        caafscore = parseFloat(outcome_res) / parseFloat(maxinsured.val());
                        mk('body').find('#p_field_wig #Nbhlc_finalresult').val(Math.round(outcome_res)+'_'+Math.round(caafscore));
                    }

                //ERGO Calculator
                if( (ergoage.length != 0 ) && (ergofamily.length !=0) && (ergocity.length !=0) && (ergoincome.length !=0) ){

                    if(ergoage.val() !='' && ergofamily.val() && ergocity.val() && ergoincome.val()){

                    mk.ajax({
                            url: "https://<?php echo $cms_subdomain; ?>/landingpage-ajaxresult.php",
                            type: "post",
                            dataType : "script",
                            data : {'wgtype': 'ergo','ergoage': ergoage.val() ,'ergofamily': ergofamily.val() ,'ergocity': ergocity.val() ,'ergoincome': ergoincome.val() , beetle: beetle},
                            cache: false,
                            success: function (result) {
                                    //console.log(result);
                                    mk('#ergo_finalresult').val(result);
                                }
                        });
                    }
                }
            
            }

                function chartGraphjs(amt, est) {
                    mk = jQuery.noConflict() || $.noConflict();
                    const data = {
                        datasets: [{
                                data: [amt, est],
                                backgroundColor: ["#5367ff", "#00d09c"],
                            }],
                        labels: [
                            'Invested Amount',
                            'EST'
                        ]
                    };
                    var myPieChart = new Chart(mk('body').find('#myChart'), {
                        type: 'doughnut',
                        data: data
                    });
                }

                function submit_wigetlead() {

                    var campaign_landingpage = "<?php echo htmlentities($campaign_landingpage); ?>";
                    var camp_id = "<?php echo htmlentities($camp_id); ?>";
                    var contentType = "<?php echo htmlentities($contentType); ?>";
                    var ref_url = "<?php echo htmlentities($ref_url); ?>";
                    var beetle = mk('input[name="beetle"]').val();
                    var c_lient_Id = "<?php echo htmlentities($c_lient_Id); ?>";

                    var fname = mk("#f_name").val();
                    var lname = mk("#l_name").val();
                    var email = (mk("#email").length > 0) ? mk("#email").val().trim() : '';
                    var whtsapp_no = mk("#whtsapp_no").val();
                    var sip_result = mk("#sip_finalresult").val();
                    var city = mk("#city").val();
                    var age = mk("#age").val();
                    var profession = mk("#profession").val();
                    var education_level = mk("#education_level").val();
                    var page_id = mk("#page_id").html();
                    var company_name = mk("#company").val();

                    var btn_text = mk("#btn_frmtext").val();
                    var text_color = mk("#text_frmcolor").val();
                    var btn_color = mk("#btn_frmcolor").val();
                    var text_font_weight = mk("#text_frmfont_weight").val();
                    var text_font_size = mk("#text_frmfont_size").val();
                    var text_font = mk("#text_frmfont").val();


                    //hlc
                    var amt_expect =  mk('body').find("#p_field_wig #amountexp").val();
                    var total_liability=  mk('body').find("#p_field_wig #totalliability").val();
                    var amt_needed=  mk('body').find("#p_field_wig #amtneed").val();
                    var financial_asset=  mk('body').find("#p_field_wig #financialasset").val();
                    var life_cover=  mk('body').find("#p_field_wig #currtlifecover").val();
                    var hlv_age  = mk('body').find("#p_field_wig #hlv_age").val();
                    var hlv_result  = mk('body').find("#p_field_wig #hlv_finalresult").val();

                    //sip
                    var amount = mk("#amount").val();
                    var rateinterest = mk("#rate").val();
                    var timeperiod = mk("#timeperiod").val();
                    var contact = (mk("#phone").length > 0) ? mk("#phone").val().trim() : '';

                    //Nbhlc calculator
                    var Nbhlc_result  =mk('body').find('#p_field_wig #Nbhlc_finalresult').val();
                    var btn_name = '<?php echo $btn_text; ?>';
                    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
                    var intRegex = /^[6789]\d{9}$/;
                    var wp_type = mk(".wig_type").val();

                    //ergo result
                    var Ergo_result  =mk('body').find('#p_field_wig #ergo_finalresult').val();

                    //hfc result
                    var hfc_eligi_loan  =mk('body').find('#p_field_wig #eligi_loan').val();
                    var hfc_elgi_emi  =mk('body').find('#p_field_wig #elgi_emi').val();
                 

                    var beetle = mk('input[name="beetle"]').val();
                    var captcha = 'sp1234';
                    if(wp_type =="nbhlc"){
                        if(Nbhlc_result ==''){
                            alert("Please select the fields!");
                            return false;
                        }
                    }
                    if(mk('body').find('#p_field_wig #Nbhlc_finalresult').length !=0){
                        Nbhlc_resultArr = Nbhlc_result.split("_");
                    }
                    mk("#landingpage_lead").html('');
                    var isValid = true;
                    var inputvars = [];

                    //check email and phone type
                    if ((typeof email != 'undefined') || (typeof contact != 'undefined')) {

                        //email check or phone
                        if (email.length != '0' || contact.length != '0') {

                            if (email.length > 0) {

                                if (!emailReg.test(email))
                                {
                                    alert('Please enter a valid email address.');
                                    return false;
                                }
                            }

                            if ((contact.length > 0)) {
                                if ((contact.length < 9) || (!intRegex.test(contact)))
                                {
                                    alert('Please enter a valid phone number.');
                                    return false;
                                }
                            }
                            mk("#email").css({"border": ""});
                            mk("#phone").css({"border": ""});

                        } else {

                            isValid = false;
                            alert("Please fill require fields!");
                            mk("#email").css({"border": "1px solid red"});
                            mk("#phone").css({"border": "1px solid red"});
                        }
                    } else {
                        isValid = false;
                        alert("Please fill require fields!");
                        mk("#email").css({"border": "1px solid red"});
                        mk("#phone").css({"border": "1px solid red"});
                    }

                    var uansarry = [];
                    var totalquestion = 0 ;
                    var corectanswer = 0; 
                    var htmlquizresult ='';
                    if( mk('body').find('#p_field_wig .ulquiz').length > 0 ) {
                        mk(".quizcount").each( function(){
                            let label = mk(this).find('label');
                            let labeltype = label.attr('data-thtype');
                            let quest = label.text(); 
                            let answers = '';
                            let iscorrect = 0;
                            let iscorrectans = '';
                                if(labeltype != undefined){
                                    if(labeltype == 'singleselect'){
                                            answers =mk(this).find('input[type=radio]:checked').val();
                                            iscorrectans = mk(this).find('input[type=hidden]').val();
                                        if(answers == iscorrectans){ iscorrect=1; corectanswer ++; }else{iscorrect= 0; }
                                            totalquestion ++;
                                    }
                                if(labeltype == 'multiselect'){
                                         answer = mk(this).find('input[type=checkbox]:checked').map( function(){
                                                return mk(this).val();
                                            }).get().join();
                                            iscorrectans = mk(this).find('input[type=hidden]').val();
                                    if(answers == iscorrectans){ iscorrect=1; corectanswer ++; }else{iscorrect= 0; }
                                        totalquestion ++;
                                }
                                if(labeltype == 'dropdown'){
                                    answers = mk(this).find('select > option:selected').val();
                                    iscorrectans = mk(this).find('input[type=hidden]').val();
                                    if(answers == iscorrectans){ iscorrect=1; corectanswer ++; }else{iscorrect= 0; }
                                        totalquestion ++;
                                }
                                if(labeltype =='text_input'){ 
                                    answers = mk(this).find('input').val();
                                }
                                if(labeltype == 'text_area'){
                                    answers = mk(this).find('textarea').val();
                                }
                                if(labeltype !='text_input' && labeltype !='text_area'){
                                    if(iscorrect == 0){
                                        htmlquizresult +='<div style="margin: 10px 0; border-bottom: 1px solid #ececec; padding: 10px 0"><p style="font-weight: 500; display: flex; line-height: 26px;"><i style="font-size: 16px; margin: 5px 8px 0 0; color:red" class="fa fa-times-circle" aria-hidden="true"></i>'+quest+'</p><p style="font-weight: 500; display: flex; line-height: 26px;"><i style="font-size: 16px; margin: 5px 8px 0 0; color:green" class="fa fa-check-circle" aria-hidden="true"></i> Correct Answer : '+iscorrectans+'</p></div>';
                                    }else{
                                        htmlquizresult +='<div style="margin: 10px 0; border-bottom: 1px solid #ececec; padding: 10px 0"><p style="font-weight: 500; display: flex; line-height: 26px;"><i style="font-size: 16px; margin: 5px 8px 0 0; color:green" class="fa fa-check-circle" aria-hidden="true"></i>'+quest+'</p><p style="font-weight: 500; display: flex; line-height: 26px;"><i style="font-size: 16px; margin: 5px 8px 0 0; color:green" class="fa fa-check-circle" aria-hidden="true"></i> Correct Answer : '+iscorrectans+'</p></div>';
                                    }
                                }
                                uansarry.push({'questions': quest, 'answers': answers, 'iscorrect':iscorrect });  
                            }  
                        });
                    }
                    mk('body').find('#QuizResultModal .modal-body').html('');
                    mk('body').find('#QuizResultModal .modal-body').html(htmlquizresult);
                    if (isValid == false)
                    {
                        return false;
                    } else
                    {
                        grecaptcha.ready(function () {
                            grecaptcha.execute('<?php echo G_RECAPTCHA_KEY; ?>', {action: 'create_first_form'}).then(function (token) {
                                mk('#sp_mobile_landingpage_form').prepend('<input type="hidden" name="g-recaptcha-response" id="g-recaptcha-1" value="' + token + '">');
                                let gcaptcha = mk('#g-recaptcha-1').val();
                                mk.ajax({
                                    url: "https://<?php echo $cms_subdomain; ?>/landingpage-submit.php",
                                    type: "post",
                                    dataType : "script",
                                    data: {
                                        'fname': fname,
                                        'lname': lname,
                                        'email': email,
                                        'phone': contact,
                                        'contact1': contact,
                                        'whts_appno': whtsapp_no,
                                        'c_lient_Id': c_lient_Id,
                                        'city': city,
                                        'age': age,
                                        'profession': profession,
                                        'education_level': education_level,
                                        'page_id': page_id,
                                        'page_id1': page_id,
                                        'company_name1': company_name,
                                        'captcha': captcha,
                                        'user_survey':uansarry,
                                        'amount': amount,
                                        'amt_expect': amt_expect,
                                        'total_liability': total_liability,
                                        'amt_needed': amt_needed,
                                        'financial_asset': financial_asset,
                                        'life_cover': life_cover,
                                        'hlv_age': hlv_age,
                                        'hlv_result': hlv_result,
                                        'sip_result': sip_result,
                                        'rateinterest': rateinterest,
                                        'timeperiod': timeperiod,
                                        'btn_text': btn_text,
                                        'text_color': text_color,
                                        'btn_color': btn_color,
                                        'text_font_weight': text_font_weight,
                                        'text_font_size': text_font_size,
                                        'text_font': text_font,
                                        'wp_type': wp_type,
                                        'beetle': beetle,
                                        'formdata': inputvars,
                                        'g-recaptcha-response': gcaptcha
                                    },
                                    cache: false,
                                    beforeSend: function () {
                                        mk('#form_btn_sub').attr("disabled", true).val('Please wait ...');
                                    },
                                    success: function (result)
                                    {
                                       let reserror = 1;
                                            try {
                                                result = mk.parseJSON(result);
                                                reserror=0;
                                            } catch (e) {
                                                // is not a valid JSON string
                                                //console.log('not valid json response');
                                                reserror=1;
                                            }
                                            //console.log( result.status);
                                            //response is html 
                                        if (reserror == 1) {
                                            //mk("#sp_mobile_landingpage_form").prepend(result);
                                            if(mk("#sip_finalresult").length != 0){ result = result.replace("{sipresult}", '<p class="resltsip">' + sip_result + '</p>'); }
                                            if(mk('body').find('#p_field_wig #ergo_finalresult').length != 0){ result = result.replace("{ergoresult}", '<p class="resultergo">' + Ergo_result + '</p>'); }
                                            if(mk('body').find("#p_field_wig #hlv_finalresult").length != 0){ result = result.replace("{hlvresult}", '<p class="reslthlv">' + hlv_result + '</p>'); }
                                            if(mk('body').find('#p_field_wig #Nbhlc_finalresult').length != 0){ 
                                                result = result.replace("{nbhlcresult}", '<p class="resltnbhlc">' + Nbhlc_resultArr[0] + '</p>');
                                                result = result.replace("{caafscore}", '<p class="resltcaaf">' + Nbhlc_resultArr[1] + '</p>');  
                                            }
                                            if(mk('body').find('#p_field_wig #elgi_emi').length != 0){ 
                                                result = result.replace("{hfc_emi}", '<p class="reslthfc_emi">' + hfc_elgi_emi + '</p>');
                                                result = result.replace("{hfc_loan}", '<p class="reslthfc_loan">' + hfc_eligi_loan+ '</p>');  
                                            }
                                            
                                            mk("#landingpage_lead").show().html(result);
                                             if( mk('#p_field_wig .ulquiz').length != 0 ) {
                                                mk('#p_field_wig .ulquiz').css('display','none');
                                                mk('#p_field_wig .nav-customslider').css('display','none');
                                                mk('#p_field_wig .result_outcome').css('display','block');
                                                mk('#p_field_wig .result_outcome .restotal_ques').html(corectanswer+'/');
                                                mk('#p_field_wig .result_outcome .restotal_correct').html(totalquestion);
                                             }
                                            if (mk("body").find('.reslthlv').length > 0) {mk("body").find('.reslthlv').text(hlv_result); }
                                            if (mk("body").find('.resultergo').length > 0) { mk("body").find('.resultergo').text(Ergo_result); }
                                            if (mk("body").find('.resltnbhlc').length > 0) {  mk("body").find('.resltnbhlc').text(Nbhlc_resultArr[0]); }
                                            if (mk("body").find('.resltcaaf').length > 0) { mk("body").find('.resltcaaf').text(Nbhlc_resultArr[1]);  }
                                            mk('body').find('#formfield #form_btn_sub').attr("disabled", false).val(btn_text);
                                            window.setTimeout(function () {
                                                mk("#formfield :input:not([type=hidden])").val('');
                                                mk('#formfield #form_btn_sub').val(btn_text);
                                            }, 2000);
                                        }
                                        
                                        if (result.status) {
                                            if( mk('#p_field_wig .ulquiz').length != 0 ) {
                                                mk('#p_field_wig .ulquiz').css('display','none');
                                                mk('#p_field_wig .nav-customslider').css('display','none');
                                                mk('#p_field_wig .result_outcome').css('display','block');
                                                mk('#p_field_wig .result_outcome .restotal_ques').html(corectanswer+'/');
                                                mk('#p_field_wig .result_outcome .restotal_correct').html(totalquestion);
                                            }
                                            mk("#landingpage_lead").css('color', 'green');
                                            mk("#landingpage_lead").show().text('Thank you for submitting. We will get back to you soon.').fadeIn(300).delay(3000).fadeOut(800);

                                            mk('body').find('#form_btn_sub').attr("disabled", false).val(btn_text);
                                            window.setTimeout(function () {
                                                mk("#formfield :input:not([type=hidden])").val('');
                                                mk('#formfield #form_btn_sub').val(btn_text);
                                            }, 2000);
                                        } else {
                                            
                                            mk("#landingpage_lead").css('color', 'red');
                                            mk("#landingpage_lead").show().text(result.message).fadeIn(300).delay(3000).fadeOut(800);
                                        }
                                        //wig results 
                                        if (mk("body h1:contains('{sipresult}')").length > 0) {
                                                mk("body h1:contains('{sipresult}')").show();
                                                mk("body h1:contains('{sipresult}')").each(function () {
                                                    var text = mk(this).html();
                                                    text = text.replace("{sipresult}", '<p class="resltsip">' + sip_result + '</p>');
                                                    mk(this).html(text);
                                                });
                                            }
                                            if (mk("body").find('.resltsip').length > 0) {
                                                mk("body").find('.resltsip').text(sip_result);
                                            }
                                            if (mk("body h1:contains('{hlvresult}')").length > 0) {
                                                mk("body h1:contains('{hlvresult}')").show();
                                                mk("body h1:contains('{hlvresult}')").each(function () {
                                                    var text = mk(this).html();
                                                    text = text.replace("{hlvresult}", '<p class="reslthlv">' + hlv_result + '</p>');
                                                    mk(this).html(text);
                                                });
                                            }
                                            if (mk("body").find('.reslthlv').length > 0) {
                                                mk("body").find('.reslthlv').text(hlv_result);
                                            }

                                            if (mk("body h1:contains('{ergoresult}')").length > 0) {
                                                mk("body h1:contains('{ergoresult}')").show();
                                                mk("body h1:contains('{ergoresult}')").each(function () {
                                                    var text = mk(this).html();
                                                    text = text.replace("{ergoresult}", '<p class="resultergo">' + Ergo_result + '</p>');
                                                    mk(this).html(text);
                                                });
                                            }
                                            if (mk("body").find('.resultergo').length > 0) {
                                                mk("body").find('.resultergo').text(Ergo_result);
                                            }
                                            

                                            if (mk("body h1:contains('{nbhlcresult}')").length > 0) {
                                                mk("body h1:contains('{nbhlcresult}')").show();
                                                mk("body h1:contains('{nbhlcresult}')").each(function () {
                                                    var text = mk(this).html();
                                                    text = text.replace("{nbhlcresult}", '<p class="resltnbhlc">' + Nbhlc_resultArr[0] + '</p>');
                                                    text = text.replace("{caafscore}", '<p class="resltcaaf">' + Nbhlc_resultArr[1] + '</p>');
                                                    mk(this).html(text);
                                                });
                                            }
                                            if (mk("body").find('.resltnbhlc').length > 0) {
                                                mk("body").find('.resltnbhlc').text(Nbhlc_resultArr[0]);
                                            }
                                            if (mk("body").find('.resltcaaf').length > 0) {
                                                mk("body").find('.resltcaaf').text(Nbhlc_resultArr[1]);
                                            }
                                            //hfc result 
                                            if (mk("body h1:contains('{hfc_emi}')").length > 0) {
                                                mk("body h1:contains('{hfc_emi}')").show();
                                                mk("body h1:contains('{hfc_emi}')").each(function () {
                                                    var text = mk(this).html();
                                                    text = text.replace("{hfc_emi}", '<p class="reslthfc_emi">' + hfc_elgi_emi + '</p>');
                                                    mk(this).html(text);
                                                });
                                            }
                                            if (mk("body h1:contains('{hfc_loan}')").length > 0) {
                                                mk("body h1:contains('{hfc_loan}')").show();
                                                mk("body h1:contains('{hfc_loan}')").each(function () {
                                                    var text = mk(this).html();
                                                    text = text.replace("{hfc_loan}", '<p class="reslthfc_loan">' + hfc_eligi_loan + '</p>');
                                                    mk(this).html(text);
                                                });
                                            }

                                            if (mk("body").find('.reslthfc_emi').length > 0) {
                                                mk("body").find('.reslthfc_emi').text(hfc_elgi_emi);
                                            }
                                            if (mk("body").find('.reslthfc_loan').length > 0) {
                                                mk("body").find('.reslthfc_loan').text(hfc_eligi_loan);
                                            }
                                            //end here 
                                            mk('body').find('.headbox').attr('contenteditable', 'false');
                                            mk('body').find('.headbox').removeAttr('contenteditable');
                                    }
                                });
                            });
                        });
                    }
                }

                function submit_landingpage()
                {
                    var campaign_landingpage = "<?php echo htmlentities($campaign_landingpage); ?>";
                    var camp_id = "<?php echo htmlentities($camp_id); ?>";
                    var contentType = "<?php echo htmlentities($contentType); ?>";
                    var ref_url = "<?php echo htmlentities($ref_url); ?>";
                    var beetle = mk('input[name="beetle"]').val();
                    var c_lient_Id = "<?php echo htmlentities($c_lient_Id); ?>";
                    var plan_ion = mk("#plan_ion").val();
                    var cust_id = mk("#cust_id").val();
                    var fname = mk("#f_name").val();
                    var fname1 = mk("#f_name1").val();
                    var fname2 = mk("#f_name2").val();
                    var fname3 = mk("#f_name3").val();
                    var fname4 = mk("#f_name4").val();

                    //var gcaptcha=grecaptcha.getResponse();

                    var lname = mk("#l_name").val();
                    var email = mk("#email").val();
                    var email1 = mk("#email1").val();
                    var email2 = mk("#email2").val();
                    var email3 = mk("#email3").val();
                    var email4 = mk("#email4").val();

                    var captcha = /*mk("#captcha").val()*/ 'sp1234';
                    var city = mk("#city").val();
                    var age = mk("#age").val();
                    var profession = mk("#profession").val();
                    var education_level = mk("#education_level").val();
                    var page_id = mk("#page_id").html();
                    var company_name = mk("#company").val();

                    var contact = mk("#phone").val();
                    var phone1 = mk("#phone1").val();
                    var phone2 = mk("#phone2").val();
                    var phone3 = mk("#phone3").val();
                    var phone4 = mk("#phone4").val();

                    if (mk("#btn_frmtext").length > 0)
                        var btn_name = mk("#btn_frmtext").val();
                    if (mk("#form_btn").length > 0)
                        var btn_name = mk("#form_btn").val();

                    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
                    var intRegex = /^[6789]\d{9}$/;

                    mk("#landingpage_lead").html('');

                    var isValid = true;
                    mk("#email").each(function () {
                        if (mk.trim(mk(this).val()) == '' || (!emailReg.test(email))) {
                            isValid = false;
                            mk(this).css({
                                "border": "1px solid red",
                            });
                        } else {
                            mk(this).css({
                                "border": "",
                                "background": ""
                            });
                        }
                    });


                    mk("#f_name").each(function () {
                        if (mk.trim(mk(this).val()) == '') {
                            isValid = false;
                            mk(this).css({
                                "border": "1px solid red",
                            });
                        } else {
                            mk(this).css({
                                "border": "",
                                "background": ""
                            });
                        }
                    });


                    mk("#company").each(function () {
                        if (mk.trim(mk(this).val()) == '') {
                            isValid = false;
                            mk(this).css({
                                "border": "1px solid red",
                            });
                        } else {
                            mk(this).css({
                                "border": "",
                                "background": ""
                            });
                        }
                    });

                    if (c_lient_Id == 'SP1542096255')
                    {

                        mk("#city").each(function () {
                            if (mk.trim(mk(this).val()) == '') {
                                isValid = false;
                                mk(this).css({
                                    "border": "1px solid red",
                                });
                            } else {
                                mk(this).css({
                                    "border": "",
                                    "background": ""
                                });
                            }
                        });

                    }

                    if (mk("#cust_id").length > 0)
                    {
                        var cust_idstr = cust_id.charAt(0).toLowerCase();
                        mk("#cust_id").each(function () {
                            if (cust_idstr !== 'h' || cust_idstr == '') {
                                isValid = false;
                                mk("#cust_id").css({
                                    "border": "1px solid red",
                                });
                            } else {
                                mk("#cust_id").css({
                                    "border": "",
                                    "background": ""
                                });
                            }
                        });
                    }

                    mk("#phone").each(function () {
                        if (mk.trim(mk(this).val()) == '' || (!intRegex.test(contact))) {
                            isValid = false;
                            mk(this).css({
                                "border": "1px solid red",
                            });
                        } else {
                            mk(this).css({
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
                        grecaptcha.ready(function () {
                            grecaptcha.execute('<?php echo G_RECAPTCHA_KEY; ?>', {action: 'create_first_form'}).then(function (token) {
                                mk('#sp_mobile_landingpage_form').prepend('<input type="hidden" name="g-recaptcha-response" id="g-recaptcha" value="' + token + '">');
                                let gcaptcha = mk('#g-recaptcha').val();
                                mk.ajax({
                                    url: "https://<?php echo $cms_subdomain; ?>/landingpage-submit.php",
                                    type: "post",
                                    dataType : "script",
                                    data: 'fname=' + fname + '&lname1=' + lname + '&email=' + email + '&email1=' + email1 + '&email2=' + email2 + '&email3=' + email3 + '&company_name1=' + company_name + '&contact1=' + contact + '&page_id1=' + page_id + '&c_lient_Id=' + c_lient_Id + '&campaign_landingpage=' + campaign_landingpage + '&contentType=' + contentType + '&camp_id1=' + camp_id + '&ref_url1=' + ref_url + '&city=' + city + '&age=' + age + '&education_level=' + education_level + '&profession=' + profession + '&captcha=' + captcha + '&cust_id=' + cust_id + '&fname1=' + fname1 + '&fname2=' + fname2 + '&fname3=' + fname3 + '&fname4=' + fname4 + '&email4=' + email4 + '&phone1=' + phone1 + '&phone2=' + phone2 + '&phone3=' + phone3 + '&phone4=' + phone4 + '&plan_ion=' + plan_ion + '&beetle=' + beetle + '&g-recaptcha-response=' + gcaptcha,
                                    cache: false,
                                    beforeSend: function () {
                                        //$("#landingpage_lead").html('Please wait..');
                                        mk('#form_btn').attr("disabled", true).val('Please wait ...');

                                    },
                                    success: function (result)
                                    {
                                        if (typeof result.status == 'undefined' && result !== null) {
                                            //mk("#sp_mobile_landingpage_form").prepend(result);
                                            mk("#landingpage_lead").show().html(result);
                                            mk('body').find('#form_btn').attr("disabled", false).val(btn_name);
                                            window.setTimeout(function () {
                                                mk("#p_field :input:not([type=hidden])").val('');
                                                mk('#form_btn').val(btn_name);
                                            }, 2000);
                                        }

                                        if (typeof (result.status) != "undefined" && result.status !== null) {
                                            mk("#landingpage_lead").css('color', 'red');
                                            mk("#landingpage_lead").show().text(result.message).fadeIn(300).delay(3000).fadeOut(800);
                                            alert(result.message);
                                        } else {
                                            //mk("#landingpage_lead").css('color','green');
                                            //mk("#landingpage_lead").show().text('Thank you for submitting. We will get back to you soon.').fadeIn(300).delay(3000).fadeOut(800);
                                            mk('#form_btn').attr("disabled", false).val("Submit");
                                            //alert('Thank you for submitting. We will get back to you soon.');
                                            window.setTimeout(function () {
                                                mk('#formfield input').val('');
                                                mk('#form_btn').val(btn_name);
                                            }, 2000);
                                        }
                                    }
                                });
                            });
                        });
                    }
                }
                var microsite = 1;
            </script>
        </head>

        <body><!--onload="landingpage_mobile();"-->
            <form name="test" id="sp_mobile_landingpage_form" method="post">

            </form>
            <div id="page_id" style="display:none"><?php echo htmlentities($pageId); ?></div>
            <div id="sp_mobile_landingpage" class="mobile_display"></div>
            <div id="landingpage_lead" style="display:none;"></div>
        <!-- Quiz Result Summary -->
        <div class="modal fade" id="QuizResultModal" tabindex="-1" role="dialog" aria-labelledby="QuizResultModalLabel">
            <div class="modal-dialog" role="document" style="width: 80%; margin: 50px auto;">
                <div class="modal-content" style="padding: 20px;">
                    <div class="modal-header" style="border: 0; padding: 0px 20px;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="QuizResultModalLabel">Summary</h4>
                    </div>
                    <div class="modal-body" style="max-height: 400px; overflow: auto">
                    
                    </div>
                </div>
            </div>
        </div>
        <!----End here---->
        </body>
    <?php } else {
        ?> 
        <body><!--onload="landingpage_mobile();"-->
            <form name="test" id="sp_mobile_landingpage_form" method="post">

            </form>

            <div id="sp_mobile_landingpage" class="mobile_display"> 

                <div id="textcount" class="textcheck" style="width: 960px; margin: 0px auto; background-color: rgb(255, 255, 255); min-height: 650px; position: relative; box-shadow: rgba(0, 0, 0, 0.3) 0px 0px 8px;">
                    <p style="text-align: center;
                       margin-top: 30px;
                       padding-top: 82px;
                       font-size: xx-large;"> Not a valid landing page  </p>
                </div>
            </div>

         <!-- Quiz Result Summary -->
         <div class="modal fade" id="QuizResultModal" tabindex="-1" role="dialog" aria-labelledby="QuizResultModalLabel">
            <div class="modal-dialog" role="document" style="width: 80%; margin: 50px auto;">
                <div class="modal-content" style="padding: 20px;">
                    <div class="modal-header" style="border: 0; padding: 0px 20px;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="QuizResultModalLabel">Summary</h4>
                    </div>
                    <div class="modal-body" style="max-height: 400px; overflow: auto">
                    
                    </div>
                </div>
            </div>
        </div>
        <!----End here---->
        </body>
        <?php
    }
    ?>
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
    <!--to add the shadow on landing page-->
    <script>
        jQuery(document).ready(function () {
            var attr = jQuery('textcount').attr('box-shadow');
            if (attr == undefined) {
                if (jQuery('#textcount').css('box-shadow') != 'rgba(0, 0, 0, 0.3) 0px 0px 8px') {
                    jQuery('#textcount').css('box-shadow', 'rgba(0, 0, 0, 0.3) 0px 0px 8px');
                }
            }
            jQuery('#textcount').css("box-shadow","0 9px 0px 0px white, 0 -9px 0px 0px white, 12px 0 14px -8px rgb(0 0 0 / 10%), -12px 0 14px -8px rgb(0 0 0 / 10%)");
        });
    </script>

    <?php include("includes/footer-event.php"); ?>
</html>
