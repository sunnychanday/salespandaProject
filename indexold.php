
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="<?php echo (isset($faviconimg)) ? 'favicon_icon/' . $faviconimg : 'images/favicon.ico' ?>" />
        <title>Microsite: <?php echo ucwords($micrositeDetails['name']).' | '.APP_TITLE; ?> </title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="js/mailcrypt.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/home-style.css?v=1.0.5">
        <link rel="stylesheet" href="css/newassets/css/main.css">
        <link rel="stylesheet" href="css/newassets/css/responsive.css">
        <?php $variable = '#f6971f'; ?>
        <style>
            :root {
                --payment-link-color: <?php echo htmlentities($QrySelectget['form_textcolor']); ?>;
                --primary-color: <?php echo htmlentities($QrySelectget['headerMenucolor'] ? $QrySelectget['headerMenucolor'] : $variable); ?>;
                --whatsapp: #25d266;
            }
        </style>

<!--
        <script src='https://www.google.com/recaptcha/api.js'></script>
-->
		<script src="https://www.google.com/recaptcha/api.js?render=<?php echo G_RECAPTCHA_KEY;?>"></script>


        <?php echo $site_font_familyArr['micro_site_ff']; ?>
    </head>
    <body>
        <header id="main-header" class="white-transparent ng-scope menu-sticky" style="background: <?php echo ((isset($headerFooterColour['header_color'])) ? $headerFooterColour['header_color'] : $QrySelectget['theme_bg']) . ';' . $header_bg_imageclass; ?>">
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
                <a class="navbar-brand <?php echo $navbrand_center; ?>" href=""> 
                    <?php
                    if ($p_client_id != 'ST18934859435') {
                        if ($company_logo != '') {
                            echo '<img src="company_logo/' . htmlentities($company_logo) . '" id="logo_img" class="img-fluid" alt="">';
                        } else if (strstr($_SERVER['SERVER_NAME'], 'absliadvisors.com') == true) {
                            echo ' <img src="company_logo/j0TPSP108098.png" id="logo_img" class="img-fluid" alt="">';
                        } else if (strstr($_SERVER['SERVER_NAME'], 'mutualfundpartner.com') == true) {
                            echo '';
                        } else if (strstr($_SERVER['SERVER_NAME'], 'maxlifeinsurance.agency') == true) {
                            echo ' <img src="company_logo/max_default.png" id="logo_img" class="img-fluid" alt="">';
                        } else if (strstr($_SERVER['SERVER_NAME'], 'upickservices.in') == true || $imgstartVal[1] == 'upickservices') {
                            echo ' <img src="company_logo/UP-Black.png" id="logo_img" class="img-fluid" alt="">';
                        }
                    }
                    ?>
                </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav <?php echo $navvar_center; ?> mr-auto w-100 justify-content-end">
                        <li class="nav-item">
                            <a class="nav-link active" href="#sp-banner" style="color:<?php
                            if (isset($headerFooterColour['header_text'])) {
                                echo $headerFooterColour['header_text'];
                            } else {
                                echo $QrySelectget['headerMenucolor'];
                            }
                            ?>;">Home</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?php echo $newMicro ?>" href="#sp-about" style="color:<?php
                            if (isset($headerFooterColour['header_text'])) {
                                echo $headerFooterColour['header_text'];
                            } else {
                                echo $QrySelectget['headerMenucolor'];
                            }
                            ?>;" ><?php echo ($about_us) ? 'About Us' : 'About Me'; ?></a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#sp-blog" style="color:<?php
                            if (isset($headerFooterColour['header_text'])) {
                                echo $headerFooterColour['header_text'];
                            } else {
                                echo $QrySelectget['headerMenucolor'];
                            }
                            ?>;">Content</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#sp-contact" style="color:<?php
                            if (isset($headerFooterColour['header_text'])) {
                                echo $headerFooterColour['header_text'];
                            } else {
                                echo $QrySelectget['headerMenucolor'];
                            }
                            ?>;">Contact Me</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <?php
        if ($QrySelectget['microsite_banner'] == '1') {
            if ($QrySelectget['headerflag'] == '1') {
                ?>
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
                <?php
            }
            ?>

            <div class="sp-banner awesome ng-scope" id="sp-banner" style="margin-top: <?php echo ($QrySelectget['headerflag'] == '1') ? '174px;' : '99px;'; ?>">

                <div class="carousel slide" id="myCarousel" data-ride="carousel">
                    <?php
                    $sliderLen = count($slider_arr);
                    if (isset($slider_arr) && !empty($slider_arr) && $sliderLen > 0) {
                        echo '<ol class="carousel-indicators">';
                        
                        for($ij = 0; $ij < $sliderLen; ++$ij){
                            $class = ($ij === 0) ? 'class="active"' : '';
                            echo '<li data-target="#myCarousel" data-slide-to="'. $ij .'" '. $class .'></li>';
                        }
                        
                        echo '</ol>';
                    } else {
                        $sliderImage = (!empty($parent_microsite_detail['slide1_img'])) ? $parent_microsite_detail['slide1_img'] : $QrySelectget['slide1_img'];
                        $slideImg2Path = (isset($parent_microsite_detail['slide2_img'])) ? $parent_microsite_detail['slide2_img'] : '';
                        $slideImg3Path = (isset($parent_microsite_detail['slide3_img'])) ? $parent_microsite_detail['slide3_img'] : '';
                        if ($slideImg2Path != '' || $slideImg3Path != '') {
                            echo '<ol class="carousel-indicators">
                            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                            <li data-target="#myCarousel" data-slide-to="1"></li>';
                            echo ($slideImg3Path != '') ? '<li data-target="#myCarousel" data-slide-to="2"></li>' : '';
                            echo '</ol>';
                        }
                    }
                    ?>

                    <div class="carousel-inner">
                        <?php
                        if (isset($slider_arr) && !empty($slider_arr)) {
                            $img_index = 0;
                            foreach ($slider_arr as $slider) {
                                if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/assets/images/microsite-sliders/{$slider['slider_img']}")) {
                                    if ($slider['url_type'] === 'landing') {
                                        $slider_link = "https://{$_SERVER['HTTP_HOST']}/landingpage/{$slider['slider_link']}".$concat_request_url;
                                    } else if ($slider['url_type'] === 'showcase') {
                                        $slider_link = "https://{$_SERVER['HTTP_HOST']}/showcase/{$slider['slider_link']}".$concat_request_url;
                                    } else if ($slider['url_type'] === 'custom') {
                                        $slider_link = "{$slider['slider_link']}".$concat_request_url;
                                    } else if ($slider['url_type'] === 'microsite') {
                                        $slider_link = "https://{$_SERVER['HTTP_HOST']}".$concat_request_url;
                                    } else {
                                        $slider_link = null;
                                    }
                                    ?>

                                    <div class="item <?php echo ($img_index === 0) ? 'active' : ''; ?>">
                                        <a <?php echo ($slider_link === null) ? 'href="javascript:void(0)" style="cursor: auto;"' : 'href="'. $slider_link .'"'; ?>>
                                            <img  src="<?php echo "https://{$_SERVER['HTTP_HOST']}/assets/images/microsite-sliders/{$slider['slider_img']}"; ?>" alt="<?php echo $slider['template_name']; ?>" >
                                        </a>
                                    </div>
                                    <?php
                                    $img_index++;
                                }
                            }
                        } else {
                            ?>
                            <div class="item active">
                                <?php
                                if (!empty($sliderImage))
                                    echo '<img src="company_banner/' . htmlentities($sliderImage) . '" alt="First slide" style="width:100%;">';
                                ?>
                            </div>
                            <?php
                            echo ($slideImg2Path != '') ? '<div class="item"><img src="company_banner/' . htmlentities($slideImg2Path) . '" alt="First slide" style="width:100%;"></div>' : '';
                            echo ($slideImg3Path != '') ? '<div class="item"><img src="company_banner/' . htmlentities($slideImg3Path) . '" alt="First slide" style="width:100%;"></div>' : '';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>

        <div class="main-content ng-scope">
            <?php
            if ($QrySelectget['microsite_aboutme'] == '1' || $QrySelectget['microsite_profilepic'] == '1') {
                ?>
                <section id="sp-about" class="sp-about overview-block-ptb <?php echo $newMicro ?>">
                    <div class="container">
                        <div class="row">
                            <?php
                            if ($QrySelectget['microsite_aboutme'] == '1') {
                                ?>
                                <div class="<?php if ($QrySelectget['microsite_profilepic'] == '0') { ?>col-lg-12<?php } else { ?>col-lg-6 <?php } ?> col-md-12 align-self-start">
                                    <div class="heading-title left">
                                        <small class="sp-font-green"><?php echo ($about_us) ? 'About Us' : 'About Me'; ?></small>
                                        <h5 class="sp-tw-8" style="<?php echo $about_me_font; ?>"><?php echo $micrositeDetails['name'] ?></h5>
                                        <?php echo (!empty($micrositeDetails['designation_title'])) ? '</br><span style="position: absolute;margin-top: -32px;">' . $micrositeDetails['designation_title'] . '</span>' : ''; ?>
                                        <h6 class="sp-font-green"><a href="javascript:void(0);" class="mailcrypt"><?php echo $micrositeDetails['person_email'] ?></a></h6>
                                    </div>

                                    <p><?php echo sanitize_microsite_field($c_lient_Id,'microsite_about') ?></p>

                                    <a href="#sp-contact"><button class="payment-link-button" style="color:black;">Contact Me</button></a>
                                </div>
                                <?php
                            }

                            if ($QrySelectget['microsite_profilepic'] == '1') {
                                ?>
                            <div class="<?php if ($QrySelectget['microsite_aboutme'] == '0') { ?>col-lg-12<?php } else { ?>col-lg-6 <?php } ?> col-md-12 align-self-center sp-re-9-mt-50" style="text-align: center;">
                                    <?php
                                    $profile_pic = ($micrositeDetails['profile_img'] == '' || file_exists("images/".$micrositeDetails['profile_img'])===false) ? 'default.png' : $micrositeDetails['profile_img'];
                                    ?>

                                    <img <?php if ($QrySelectget['microsite_aboutme'] == '0') { ?> style="margin-left:410px;" <?php } ?>class="img-fluid wow fadeIn img-circle imgcircle" src="images/<?php echo $profile_pic; ?>" alt="#" style="<?php echo $header_bg_imageclass; ?>; height: 310px; width: 310px;">
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </section>
                <?php
            }

            if ($QrySelectget['microsite_contentlibrary'] == '1') {
                ?>
                <section id="sp-blog" class="overview-block-ptb grey-bg sp-blog">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="heading-title">
                                    <!-- <small class="">recent talks</small> -->
                                    <h2 class="title sp-tw-6 sp-font-green" style="color:<?php echo htmlentities($QrySelectget['form_textcolor']) . ';' . $solution_type_font; ?>;">View our solutions below</h2>

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
                                                <a data-toggle="tab" aria-expanded="false" href="javascript:void(0)">Show All</a>
                                            </li>

                                            <?php
                                            $category_list = $obj->get_category();
                                            foreach ($category_list as $category) {
                                                $catgIMG = str_replace(' ', '-', $category["it_type"]);
                                                $imgcatweb = !empty($category["web_icon"]) ? 'images/'.$category["web_icon"]: 'images/'.$imgstartVal[1] . '-' . $catgIMG.'.png';
                                                ?>
                                            
                                                <li id="activeShow_<?php echo htmlentities($category["id"]); ?>" onclick="showcaseproduct(<?php echo htmlentities($category["id"]); ?>)">
                                                    <a data-toggle="tab" aria-expanded="false" href="javascript:void(0)">
                                                        <img style="padding: 0 25px;" id="solutionimg<?php echo htmlentities($category["id"]); ?>" src="<?php echo $imgcatweb; ?>" alt="">
                                                        <img class="activeimg" src="<?php echo $imgcatweb; ?>" style="padding: 0 25px;display:none;"> <?php echo htmlentities($category["it_type"]); ?>
                                                    </a>
                                                </li>
                                                <?php
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom: 5px;">
                                    <select class="form-control"  onchange="showcasefilter(this.value)">
                                        <option value="">Content Type</option>
                                        <?php
                                        $content_type_list = $obj->get_content_type();

                                        foreach ($content_type_list as $content_type) {
                                            ?>
                                            <option value="<?php echo htmlentities($content_type["id"]); ?>"><?php echo htmlentities($content_type["article_type"]); ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom: 5px;">
                                    <div class="input-group">
                                        <input type="text" class="form-control showcaseSRH" id="showcsearch-name" placeholder="Search by content title" autocomplete="off" onkeyup="ShowcaseSearchKeyup();" width="48" height="48" />
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
                                    </div>

                                    <div id="showsrch" style="display:none;float: right;margin-right: 354px;margin-top: 13px;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="showcasefilter"></div>
                    </div>
                </section>

                <?php
            }
            ?>

            <div id="sp-contact" class="contact-us <?php echo $newMicro ?>">
                <div class="row no-gutters">

                    <?php
                    if ($QrySelectget['microsite_getintouch'] == '1') {
                        ?>
                        <div class="<?php echo ($QrySelectget['microsite_mapflag'] == '0') ? "col-lg-12" : "col-lg-6"; ?> col-md-12 col-md-push-6 align-self-center">
                            <div class="sp-mlr-60 sp-ptb-80">
                                <div class="heading-title left">
                                    <h5 class="sp-tw-6">Get in Touch</h5>
                                </div>

                                <p>Leave your contact details and my team will get in touch with you.</p>

                                <form id="contact" method="post" class="ng-pristine ng-valid">
                                    <div class="contact-form">
                                        <div class="section-field">
                                            <input class="require" id="your-name" type="text" placeholder="Name*" name="name" value="<?php echo ($track_url_params['contact_details']['first_name'])??'';?>">
                                        </div>

                                        <div class="section-field">
                                            <input class="require" id="your-email" type="email" placeholder="Email*" name="email" value="<?php echo ($track_url_params['semail'])??'';?>">
                                        </div>

                                        <div class="section-field">
                                            <input class="require" id="number-339" type="text" placeholder="Phone*" name="phone" maxlength="13" value="<?php echo ($track_url_params['contact_details']['mobile'])??'';?>">
                                        </div>

                                        <div class="section-field textarea">
                                            <textarea id="contact_message" class="input-message require" placeholder="Comment*" rows="5" name="message"></textarea>
                                        </div>

                                        <br />

                                        <br />

                                        <div class="sp-mt-20 term-set">
                                            <span class="understand"><input type="checkbox" id="termCheck" name="termCheck" value="1">  I understand that I would be contacted with regards to this request placed and consent for the same in spite of being registered with the National Customer Preference Registry (NCPR) with TRAI. I understand that there is a de-registration facility ( for not receiving such calls) which I may avail if required in future.</span>
                                        </div>

                                        <div class="section-field sp-mt-20"></div>

                                        <div id="formbuilder_lead" style="display:none;font-size:16px;color:red;"></div>
                                        <button name="submit" type="button" value="Send" onclick="return submit_formbuilder();" id="send-value" class="payment-link-button">Send Message</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    if ($QrySelectget['microsite_mapflag'] == '1') {
                        ?>
                        <div class="<?php echo ($QrySelectget['microsite_getintouch'] == '0') ? "col-lg-12" : "col-lg-6"; ?> col-md-12 col-md-pull-6" style="height:711px;">
                            <?php
                            if ($micrositeDetails['microsite_address'] != '') {
                                ?>
                                <span id="address" style="display:none;"><?php echo htmlentities($micrositeDetails['microsite_address']); ?></span>
                                <iframe class="map" id="map" style="border:0" allowfullscreen=""></iframe>
                                <?php
                            } else {
                                ?>
                                <span id="address" style="display:none;">Aditya Birla Capital Limited, Tulsi Pipe Road, Babasaheb Ambedkar Nagar, Lower Parel, Mumbai, Maharashtra</span>
                                <iframe class="map" id="map" style="border:0" allowfullscreen=""></iframe>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>

                    <form method="post" style="display:none;">
                        <input type="text" name="hide_form">
                    </form>


                </div>
            </div>
            <!-- === contact-us END=== -->
        </div>
        <!-- === Main Content End === -->

        <?php
        if ($QrySelectget['microsite_disclaimer'] == 1 && !empty($QrySelectget['disclaimer_content'])) {
            ?>
            <div ui-view="footer" class="ng-scope">
                <footer class="dark-bg ng-scope" style="background:<?php echo $QrySelectget['disclaimer_color']; ?>">
                    <div class="sp-footer sp-pb-20">
                        <div class="container">
                            <div class="row overview-block-ptb2">
                                <div class="col-lg-12 col-md-12 sp-mtb-20">
                                    <div class="logo">
                                        <div class="sp-mt-5 sp-mr-10" style="color: #7f7e7f; font-size: 16px; margin-top: 1px; padding: 5px; text-align: justify;">
                                            <?php echo preg_replace('-arn_no-', $obj->arn_no, sanitize_editor_content($QrySelectget['disclaimer_content'])); ?>
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

        if ($QrySelectget['microsite_footernavigationmenu'] == '1' || $QrySelectget['microsite_footeraboutme'] == '1' || $QrySelectget['microsite_footercontactdetails'] == '1') {
            $footer_font_color = (isset($headerFooterColour['footer_text'])) ? 'style="color:' . $headerFooterColour['footer_text'] . ';"' : '';
            include("includes/footer.php");
        }
        ?>

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
        <a class="whatsapp" data-toggle="modal" data-target="#whatsappModal"><i class="fab fa-whatsapp" style="color: white;"></i></a>
        <!-- Modal -->
        <div class="modal fade" id="whatsappModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered whats-modal" role="document">
                <div class="modal-content">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="modal-body text-center">
                        <i class="fab fa-whatsapp" style="color: white;"></i>
                        <h5>Hi!! Type your Whatsapp Message</h5>
                        <form id="whtsapp_contact" method="post">
                            <div class="form-group">
                                <textarea class="form-control" id = "whatsapp_msg" placeholder="Type a message*"></textarea>
                            </div>
                            <div class="form-group">
                                <!-- <label>Share Your Email Id</label> -->
                                <input type="email" id="whatsapp_email" placeholder="Share Your Email Id*" class="form-control" value="<?php echo ($track_url_params['semail']) ?? '';?>">
                            </div>
                            <button name="submit" type="button" value="Send" onclick="return submit_formbuilder_whstapp();" id="send-valuewhtsapp" class=" btn main-btn">Share Message</button>
                        </form>
                        <div id="formbuilder_whstapp" style="display:none;font-size:16px;color:red;"></div>

                    </div>
                </div>
            </div>
        </div>
        <script>
            //$.noConflict();
            $(document).ready(function ($) {
                var prefix = "+91"+"<?php echo ($track_url_params['contact_details']['mobile']) ? trim(str_replace('+91','',$track_url_params['contact_details']['mobile'])) : '';?>";
                $('#number-339').val(prefix);
                $('[data-toggle="tooltip"]').tooltip();
                $('.mailcrypt').mailcrypt();

                $("a[href='#sp-contact'], a[href='#sp-about'], a[href='#sp-blog'], a[href='#sp-banner'], a[href='#categories']").on('click', function (event) {
                    if (this.hash !== "") {
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
                $('#map').attr('src', 'https://www.google.com/maps/embed/v1/place?key=AIzaSyAsIbU83moc8MIocD2dkhaCU90GAoGSspU&q=' + q);

                var ctr = jQuery.noConflict();
                ctr("#activeShow_407").addClass('active').siblings().removeClass('active');
                var imgsol = ctr(this).attr("src");
                var SolutionType = '0';
                var c_lient_Id = '<?php echo $c_lient_Id; ?>';
                var p_client_id = '<?php echo $p_client_id; ?>';
                var pcType = '<?php echo $pcmember_pc_type; ?>';
                var siteURL = '<?php echo $basepath . $_SERVER['HTTP_HOST']; ?>';
                var requested_url = '<?php echo $concat_request_url; ?>';
                var beetle = $('input[name="beetle"]').val();
                ctr.ajax({url: "<?php echo $basepath . $_SERVER['HTTP_HOST']; ?>/sp-showcasesolution.php",
                    type: "post",
                    data: {beetle: beetle, c_lient_Id: c_lient_Id, p_client_id: p_client_id, pcType: pcType, SolutionType: SolutionType, siteURL: siteURL,requested_url:requested_url},
                    cache: false,
                    crossDomain: true,
                    beforeSend: function ()
                    {
                        ctr('#showcasefilter').html('<div class="loader"></div>');
                    },
                    success: function (result) {
                        ctr("#showcasefilter").html(result);
                    }
                });
            })

            function submit_formbuilder() {
                var frm = jQuery.noConflict();
                var c_lient_Id = '<?php echo $c_lient_Id; ?>';
                var ref_url = "<?php echo htmlentities($_SERVER['HTTP_REFERER']); ?>";
                var full_name = frm("#your-name").val();
                var email = frm("#your-email").val();
                var contact = frm("#number-339").val();
                var comment = frm("#contact_message").val();
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
					grecaptcha.ready(function() {
						grecaptcha.execute('<?php echo G_RECAPTCHA_KEY;?>', {action: 'microsite_contact_form'}).then(function(token) {
							frm('#contact').prepend('<input type="hidden" name="g-recaptcha-response" value="' + token + '">');
							frm.ajax({
								type: "post",
								url: ajxPath,
								data: {"your_message": comment, "beetle": beetle, "full_name1": full_name, "email1": email, "c_lient_Id": c_lient_Id, "form_refer": form_refer,"ref_url": ref_url, "contact1": contact ,"g-recaptcha-response" : token},
								cache: false,
								crossDomain: true,
								beforeSend: function () {
									frm('#send-value').attr("disabled", true).val('Please wait ...');
								},
								success: function(result) {
                                    if(result.status){
                                        frm("#your-name").val('');
                                        frm("#your-email").val('');
                                        frm("#number-339").val('');
                                        frm("#contact_message").val('');
                                        frm("#formbuilder_lead").css('color','green');
                                        frm("#formbuilder_lead").show().text('Thank you for submitting. We will get back to you soon.').fadeIn(300).delay(3000).fadeOut(800);
                                        frm('#send-value').attr("disabled", false).val("Submit");
                                        window.setTimeout(function () {
                                            //window.location.reload();
                                        }, 2000);
                                    }else{
                                        frm('#send-value').attr("disabled", false).val("Submit");
                                        frm("#formbuilder_lead").css('color','red');
                                        frm("#formbuilder_lead").show().text(result.message).fadeIn(300).delay(3000).fadeOut(800);
                                    }

								}
							});
						});
					});	

                }
            }

            function showcasefilter(ContentType) {
                var ctr = jQuery.noConflict();
                ctr("#activeShow_" + ContentType).addClass('active').siblings().removeClass('active');
                var c_lient_Id = '<?php echo $c_lient_Id; ?>';
                var p_client_id = '<?php echo $p_client_id; ?>';
                var pcType = '<?php echo $pcmember_pc_type; ?>';
                var siteURL = '<?php echo $basepath . $_SERVER['HTTP_HOST']; ?>';
                var beetle = ctr('input[name="beetle"]').val();
                ctr.ajax({url: "<?php echo $basepath . $_SERVER['HTTP_HOST']; ?>/sp-showcasefilter.php",
                        type: "post",
                        data: {beetle: beetle, c_lient_Id: c_lient_Id, p_client_id: p_client_id, pcType: pcType, ContentType: ContentType, siteURL: siteURL,'is_old': true},
                        cache: false, crossDomain: true,
                        beforeSend: function () {
                            //ctr('#showcasefilter').html('<div class="loader"></div>');                             },
                        },
                        success: function (result)
                        {

                        ctr("#showcasefilter").html(result);
                        }
                        });
            }

            function showcaseproduct(SolutionType) {
                var ctr = jQuery.noConflict();
                ctr("#activeShow_" + SolutionType).addClass('active').siblings().removeClass('active');
                var imgsol = ctr(this).attr("src");

                var c_lient_Id = '<?php echo $c_lient_Id; ?>';
                var p_client_id = '<?php echo $p_client_id; ?>';
                var pcType = '<?php echo $pcmember_pc_type; ?>';
                var siteURL = '<?php echo $basepath . $_SERVER['HTTP_HOST']; ?>';
                var beetle = ctr('input[name="beetle"]').val();
                ctr.ajax({url: "<?php echo $basepath . $_SERVER['HTTP_HOST']; ?>/sp-showcasesolution.php",
                    type: "post",
                    data: {beetle: beetle, c_lient_Id: c_lient_Id, p_client_id: p_client_id, pcType: pcType, SolutionType: SolutionType, siteURL: siteURL},
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

            function ShowcaseSearchKeyup() {
                var srch = jQuery.noConflict();
                var showsearchName = srch("#showcsearch-name").val();
                var c_lient_Id = '<?php echo $c_lient_Id; ?>';
                var p_client_id = '<?php echo $p_client_id; ?>';
                var pcType = '<?php echo $pcmember_pc_type; ?>';
                var beetle = srch('input[name="beetle"]').val();
                srch.ajax({
                    type: "POST",
                    url: "<?php echo $basepath . $_SERVER['HTTP_HOST']; ?>/get_document_title_list.php",
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
                window.location.href = '<?php echo $basepath . $_SERVER['HTTP_HOST']; ?>/showcase/' + window.encodeURIComponent(val1) + '';
            }

            function whatsappShare(weburl='') {
                var $ = jQuery.noConflict();
                if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                    var whats_app_message = encodeURIComponent(weburl);
                    var whatsapp_url = "https://api.whatsapp.com/send?text=" + whats_app_message;
                    window.location.href = whatsapp_url;

                } else
                {
                    alert('Whatsapp sharing is only available through mobile.');
                }
            }
        </script>

        <script>
            var microsite = 1;
        </script>
        <?php include("includes/footer-event.php"); ?>

        <script type="text/javascript">
                    (function () {
                        document.getElementById("declarationbtn").onclick = function () {
                            setCookieV2("declarationCookies", 1, 1000);
                            var r = getCookie('declarationCookies');
                            document.getElementById("popup-cookie-2").style.display = "none";
                            var payment_link = document.getElementsByClassName("payment-link");

                            if (payment_link > 0) {
                                payment_link[0].style.display = "block";
                            }

                            if (payment_link > 1) {
                                payment_link[1].style.display = "block";
                            }
                        }

                        var k = getCookie('declarationCookies');
                        if (k == 1) {
                            var payment_link = document.getElementsByClassName("payment-link");
                            document.getElementById("popup-cookie-2").style.display = "none";

                            if (payment_link > 0) {
                                payment_link[0].style.display = "block";
                            }

                            if (payment_link > 1) {
                                payment_link[1].style.display = "block";
                            }
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

        if (strstr($baseUrl, 'mutualfundpartner.com') == true || 1 == 1) {
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
                    </svg>
                </div>

                <div class="css-dlvlg4 e2ujk8f1"><svg viewBox="0 0 28 32">
                    <path
                        d="M28,32 C28,32 23.2863266,30.1450667 19.4727818,28.6592 L3.43749107,28.6592 C1.53921989,28.6592 0,27.0272 0,25.0144 L0,3.6448 C0,1.632 1.53921989,0 3.43749107,0 L24.5615088,0 C26.45978,0 27.9989999,1.632 27.9989999,3.6448 L27.9989999,22.0490667 L28,22.0490667 L28,32 Z M23.8614088,20.0181333 C23.5309223,19.6105242 22.9540812,19.5633836 22.5692242,19.9125333 C22.5392199,19.9392 19.5537934,22.5941333 13.9989999,22.5941333 C8.51321617,22.5941333 5.48178311,19.9584 5.4277754,19.9104 C5.04295119,19.5629428 4.46760991,19.6105095 4.13759108,20.0170667 C3.97913051,20.2124916 3.9004494,20.4673395 3.91904357,20.7249415 C3.93763774,20.9825435 4.05196575,21.2215447 4.23660523,21.3888 C4.37862552,21.5168 7.77411059,24.5386667 13.9989999,24.5386667 C20.2248893,24.5386667 23.6203743,21.5168 23.7623946,21.3888 C23.9467342,21.2215726 24.0608642,20.9827905 24.0794539,20.7254507 C24.0980436,20.4681109 24.0195551,20.2135019 23.8614088,20.0181333 Z">
                    </path>
                    </svg>
                </div>
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

            <script src="chatbot/js/owl.carousel.js"></script>
            <?php
            require("chatbot/js/comman_function1.php");
        }
        ?>

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
        </script>
        
        <script type="text/javascript">
            <?php
            $deviceType = detectDevice($_SERVER['HTTP_USER_AGENT']);
            if($deviceType === 'Computer'){
              ?>
                var percentage = 88;
            <?php
            }elseif($deviceType === 'Mobile'){
                ?>
                var percentage = 55;
                <?php
            }else{
                ?>
                var percentage = 75;
                <?php
            }
            ?>
                        
            var h = Math.floor((window.screen.availHeight / 100) * percentage);
            $('img.slider-fluid').css('width', window.screen.width);
            $('img.slider-fluid').css('height', h);

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
                    let whatsAppMsgVal = frm.trim(frm(this).val());

                    if ((whatsAppMsgVal == '') || (whatsAppMsgVal.length < 3)) {
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
                                data: {"beetle": beetle, "your_message": whatsapp_msg, "email1": whatsapp_email, "c_lient_Id": c_lient_Id, "form_refer": form_refer, "ref_url": ref_url, "g-recaptcha-response": token},
                                cache: false,
                                crossDomain: true,
                                beforeSend: function () {
                                    frm('#send-valuewhtsapp').attr("disabled", true).val('Please wait ...');
                                },
                                success: function (result) {
                                    frm('#send-valuewhtsapp').attr("disabled", false).val("Submit");
                                    let whatappURL = getLinkWhastapp("<?php echo $firstPhone . '' . $secondPhone; ?>", whatsapp_msg);
                                    window.open(whatappURL, '_blank');

                                    location.reload();
                                }
                            });
                        });
                    });

                }
            }

            function getLinkWhastapp(number, message) {
                if (number.substr(0, 3) == '+91') {
                    var url = 'https://api.whatsapp.com/send?phone=' + number + '&text=' + encodeURIComponent(message)
                    return url
                } else {
                    var url = 'https://api.whatsapp.com/send?phone=+91' + number + '&text=' + encodeURIComponent(message)
                    return url
                }
            }
        </script>
    </body>
</html>
