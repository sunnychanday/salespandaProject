<?php
//~ For social icon
$obj = new \Microsite\Microsite($connPDO);
$obj->LinkedInPAge;
$obj->facebookPage;

//~ twitter and whatsapp icon of header  need to hide for now
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?php echo (isset($faviconimg)) ? 'favicon_icon/' . $faviconimg : 'images/favicon.ico' ?>" />
    <title>Microsite: <?php echo ucwords($micrositeDetails['name']).' | '.APP_TITLE; ?> </title>
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Bootstrap CSS -->

    <link rel="stylesheet" href="css/newassets/css/all.min.css">
    <link rel="stylesheet" href="css/newassets/css/aos.css">
    <link rel="stylesheet" href="css/newassets/css/animate.css">
    <link rel="stylesheet" href="css/newassets/css/main.css">
    <link rel="stylesheet" href="css/newassets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
    
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
    .container .navbar-brand img 
    {
        max-height: 70px;
        height: 70px;
        width: auto;
        padding-bottom: 15px;
    }
    img 
    {
        object-fit: contain;        
        width: 100%;
        max-width: 100%;
    }
    .carousel-indicators .active 
    {
        border-radius: 50%;
        width: 12px;
        height: 12px;
        margin: 0;
        background-color: #fff;
    }
    .carousel-item img{
		max-height :400px;
		object-fit:cover;
	}
    </style>
    <!--<script src='https://www.google.com/recaptcha/api.js'></script>-->

    <?php echo $site_font_familyArr['micro_site_ff']; ?>
</head>
<body>
<div class="topbar" style="background: <?php echo htmlentities($QrySelectget['theme_bg'] ? $QrySelectget['theme_bg'] : $variable); ?>">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <ul class="left-top">
                    <li>
                        <a href="mailto:<?php echo $micrositeDetails['person_email'] ?>"><i class="fa fa-envelope"></i><span><?php echo $micrositeDetails['person_email'] ?></span></a>
                    </li>
                    <li>
                        <a href="tel:<?php echo htmlentities($firstPhone) . htmlentities($secondPhone); ?>"><i class="fa fa-phone"></i><?php echo htmlentities($firstPhone) . htmlentities($secondPhone); ?></a>
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
                                                                                    <li><a href="#"><i class="fab fa-whatsapp"></i></a></li>
                    -->
					<?php if(multilingual) {?>
					<li> <select name="lang" id="languages" onchange="changeLang(this.value)">
							<option value="en">English</option>
							<?php foreach($obj->microsite_languages as $langObj){ ?>
							<?php $languageCode  = (isset($langObj['lang_unique_code']) && ($langObj['lang_unique_code'] !="")) ? $langObj['lang_unique_code'] : generateLangCode($langObj['partner_category'],1,$c_lient_Id) ;?>
							<option value="<?php echo $langObj['lang_unique_code'];  ?>"><?php echo $langObj['partner_category']; ?></option>
							
							<?php }?>
					</select> </li>
					
					<?php } ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <?php if ($company_logo != '') { ?>
            <a class="navbar-brand" href="<?php echo $_REQUEST['siteUrl'].$concat_request_url; ?>"><img src=<?php echo 'company_logo/' . htmlentities($company_logo); ?> class="img img-fluid"></a>
        <?php } ?>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
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
			$testimonialsText = $obj->getMultilingualContent('testimonials');
			$achievementsText = $obj->getMultilingualContent('achievements');
			$phone = $obj->getMultilingualContent('phone');
			$comment = $obj->getMultilingualContent('comment');
			$have_question = $obj->getMultilingualContent('have_question');
			$should_checkout = $obj->getMultilingualContent('should_checkout');
		?>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#"><?php echo (isset($menus->home->{$lang})) ? $menus->home->{$lang} : $menus->home->en  ;?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">
					<?php echo (isset($menus->about_us->{$lang})) ? $menus->about_us->{$lang} : $menus->about_us->en  ;?>
					</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#content">
						<?php echo (isset($menus->content->{$lang})) ? $menus->content->{$lang} : $menus->content->en  ;?>
					</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">
						<?php echo (isset($menus->contact_us->{$lang})) ? $menus->contact_us->{$lang} : $menus->contact_us->en  ;?>
					</a>
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
                    <button class="btn main-btn payment-link" data-url="<?php echo $sip_button['url']; ?>">Buy NFO</button>

                <?php }
                if (isset($sip_button['url']) && $sip_button['url'] !== '' && $otherbuttonShow == 0) {
                    ?>
                    <button class="btn main-btn payment-link" data-url="<?php echo $sip_button['url']; ?>">Start SIP</button>
                <?php }
                if (isset($lumsum_button['url']) && $lumsum_button['url'] !== '' && $otherbuttonShow == 0) {
                    ?>
                    <button class="btn main-btn payment-link" data-url="<?php echo $lumsum_button['url']; ?>">Buy Now</button>
                <?php } ?>

            </div>
        </div>
    </div>
</nav>
<!-- landing -->
<!--
                <div class="landing-section">
                        <img src="css/newassets/images/landing-1.jpg" class="img img-fluid banner-web">
                        <img src="css/newassets/images/landing-tab.jpg" class="img img-fluid banner-tab">
                        <img src="css/newassets/images/landing-mob.jpg" class="img img-fluid banner-mob">
                </div>
-->
<?php if ($QrySelectget['microsite_banner'] == '1') { ?>
    <div id="myCarousel" class="carousel slide sp-banner awesome ng-scope" data-ride="carousel">
        <div class="carousel slide" id="myCarousel" data-ride="carousel">
            <!-- Indicators -->
            <ul class="carousel-indicators">
                <?php
                $sliderLen = count($slider_arr);
                if (isset($slider_arr) && !empty($slider_arr) && $sliderLen > 0) {
                    for($ij = 0; $ij < $sliderLen; ++$ij){
                        $class = ($ij === 0) ? 'class="active"' : '';
                        echo '<li data-target="#myCarousel" data-slide-to="'. $ij .'" '. $class .'></li>';
                    }
                } else {
                    $sliderImage = (!empty($parent_microsite_detail['slide1_img'])) ? $parent_microsite_detail['slide1_img'] : $QrySelectget['slide1_img'];
                    $slideImg2Path = (isset($parent_microsite_detail['slide2_img'])) ? $parent_microsite_detail['slide2_img'] : '';
                    $slideImg3Path = (isset($parent_microsite_detail['slide3_img'])) ? $parent_microsite_detail['slide3_img'] : '';
                    if ($slideImg2Path != '' || $slideImg3Path != '') {
                        echo '
                        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                        <li data-target="#myCarousel" data-slide-to="1"></li>';
                        echo ($slideImg3Path != '') ? '<li data-target="#myCarousel" data-slide-to="2"></li>' : '';
                    }
                }
                ?>
            </ul>
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
                        <div class="carousel-item <?php echo ($img_index === 0) ? 'active' : ''; ?>">
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
                    <div class="carousel-item active">
                    <?php
                    if (!empty($sliderImage))
                    echo '<img src="company_banner/' . htmlentities($sliderImage) . '" alt="First slide" style="width:100%;">';
                    ?>
                    </div>
                    <?php
                    echo ($slideImg2Path != '') ? '<div class="carousel-item"><img src="company_banner/' . htmlentities($slideImg2Path) . '" alt="First slide" style="width:100%;"></div>' : '';
                    echo ($slideImg3Path != '') ? '<div class="carousel-item"><img src="company_banner/' . htmlentities($slideImg3Path) . '" alt="First slide" style="width:100%;"></div>' : '';
                }
                ?> 
            </div>
        </div>
    </div>
<?php } ?>
<!-- about -->
<?php if ($QrySelectget['microsite_aboutme'] == '1' || $QrySelectget['microsite_profilepic'] == '1') { ?>
    <div class="about-section" id="about">
        <div class="container">
            <div class="about-wrap">
                <?php if ($QrySelectget['microsite_aboutme'] == '1') { ?>
                    <div class="about-box aos-init aos-animate" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="300">
                        <div class="head">
                            <h3><?php echo (isset($about_us->{$lang})) ? $about_us->{$lang} : $about_us->en  ;?></h3>
                            <h5><?php echo ucwords($micrositeDetails['name']); ?></h5>
                        </div>
                        <p><?php   echo $microsite_about=sanitize_microsite_field($c_lient_Id, 'microsite_about');
                //$aboutUsName=(trim(substr($microsite_about,0,5))=='is a') ? ucwords($micrositeDetails['name'])." " : "";         
                //echo $aboutUsName.$microsite_about;
                                
                                ?></p>
                    </div>
                <?php } ?>
                <?php if ($QrySelectget['microsite_profilepic'] == '1') { ?>
                    <div class="img-box aos-init aos-animate" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="400">
                        <?php
                        
                        $profile_pic = ($micrositeDetails['profile_img'] == '' || file_exists("images/".$micrositeDetails['profile_img'])===false) ? 'new-default.png' : $micrositeDetails['profile_img'];
                        ?>
                        <img src="images/<?php echo $profile_pic; ?>" class="img img-fluid">
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>

<!-- contact -->
<?php if ($QrySelectget['microsite_getintouch'] == '1') { ?>
    <div class="contact-section" style="background-image: url('<?php echo $_REQUEST['siteURL'] . '/css/newassets/images/contact-bg.jpg"'; ?>');">
        <div class="container">
            <div class="head">
                   <h3><?php echo (isset($contact_me_now->{$lang})) ? $contact_me_now->{$lang} : $contact_me_now->en  ;?></h3>
            </div>
            <form class="aos-init aos-animate" id="contact_frm" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                <div class="row">
                    <div class="col form-group">
                        <input type="text" name="name" id="your-name" placeholder="<?php echo (isset($name->{$lang})) ? $name->{$lang} : $name->en  ;?>*" class="form-control" value="<?php echo ($track_url_params['contact_details']['first_name'])??'';?>">
                    </div>
                    <div class="col form-group">
                        <input type="text" name="email" id="your-email" placeholder="<?php echo (isset($email->{$lang})) ? $email->{$lang} : $email->en  ;?>*" class="form-control" value="<?php echo ($track_url_params['semail'])??'';?>">
                    </div>
                    <div class="col form-group">
                        <input type="text" name="phone" id="number-339" placeholder="<?php echo (isset($phone->{$lang})) ? $phone->{$lang} : $phone->en  ;?>*" class="form-control" value="<?php echo ($track_url_params['contact_details']['mobile'])??'';?>">
                    </div>
                </div>
                <button name="submit" type="button" value="Send" onclick="return submit_formbuilder();" id="send-value" class=" btn main-btn"><?php echo (isset($submit_now->{$lang})) ? $submit_now->{$lang} : $submit_now->en  ;?></button>
            </form>
            <div id="formbuilder_lead" style="display:none;font-size:16px;color:red;"></div>

        </div>
    </div>
    </div>
<?php } ?>
<?php if ($QrySelectget['microsite_contentlibrary'] == '1') { ?>

    <!-- solution -->
    <div class="solution-section">
        <div class="container" id="content">
            <div class="head">
                <h3><?php echo (isset($view_solutions->{$lang})) ? $view_solutions->{$lang} : $view_solutions->en  ;?></h3>
                <p><?php echo (isset($view_solutions_desc->{$lang})) ? $view_solutions_desc->{$lang} : $view_solutions_desc->en  ;?></p>
            </div>
            <div class="row col-md-12">
                <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom: 5px;">
                    <select class="form-control select-box"  onchange="showcasefilter(this.value)" style="height: 36px;">
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
                    <form>
                        <div class="form-group">
                            <input type="text" name=""  class="form-control" id="showcsearch-name" placeholder="Search by content title" autocomplete="off" onkeyup="ShowcaseSearchKeyup();">
                            <i class="fa fa-search"></i>
                        </div>
                        <div id="showsrch" style=" display: none;float: right;margin-right: 354px;margin-top: 13px;"></div>

                    </form>
                </div>
            </div>

            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-link active" id="activeShow_0" onclick="showcaseproduct(0)" data-toggle="tab" href="javascript:void(0)" role="tab" aria-controls="nav-all" aria-selected="true" id="activeShow_0" onclick="showcaseproduct(0)">
                        <img src="<?php echo $_REQUEST['siteURL'] . '/css/newassets/images/sol-0.png"'; ?>" class="img img-fluid">
                        <span><?php echo (isset($all->{$lang})) ? $all->{$lang} : $all->en  ;?></span>
                    </a>
                    <!--
                                                            Call All Category of search content by php
                    -->
                    <?php
                    $category_list = $obj->get_category();
					//print_r($category_list);
                    foreach ($category_list as $category) {
						$translation = $obj->translationContent($category["id"],$lang);
						$description = (isset($translation['description'])) ? $translation['description'] : "";
						$categoryname = (strlen(trim($translation['name'])) > 0)? $translation['name'] : $category["it_type"];
                        $catgIMG = str_replace(' ', '-', $category["it_type"]);
                        $imgcatweb = !empty($category["web_icon"]) ? 'images/'.$category["web_icon"]: 'images/'.$imgstartVal[1] . '-' . $catgIMG.'.png';
                        ?>
                        <a class="nav-link" id="activeShow_<?php echo htmlentities($category["id"]); ?>" onclick="showcaseproduct(<?php echo htmlentities($category['id']);?>, '<?php echo testInput($description); ?>')" data-toggle="tab" href="#nav-fund" role="tab" aria-controls="nav-fund" aria-selected="false">

                            <img src="<?php echo $_RESQUEST['siteURL'] . $imgcatweb; ?>" >
                            <span><?php echo $categoryname ;?></span>
                        </a>
                    <?php } ?>
					<p class="row col-md-12 solution_desc text-center" style="display: block;margin-top: 15px;">
					
					</p>

                </div>
            </nav>

            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-all"  role="tabpanel" aria-labelledby="nav-all-tab">
                    <div class="row" id = "showcasefilter"></div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- call -->
<?php if ((isset($sip_button['url']) && $sip_button['url'] !== '') || (isset($lumsum_button['url']) && $lumsum_button['url'] !== '') || (isset($nfo_button['url']) && $nfo_button['url'] !== '')) { ?>
    <div class="call-section" style="background-image: url('images/pattern.png');">
        <div class="container">
            <div class="inner-call aos-init aos-animate" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
                <h5>Are You Impressed with our creative work?</h5>
                <h3>Start Your Project with Tax Advice?</h3>
                <?php
                $otherbuttonShow = 0;
                if (isset($nfo_button['url']) && $nfo_button['url'] !== '') {
                    ?>
                    <button class="btn main-btn payment-link" data-url="<?php echo $nfo_button['url']; ?>">Buy NFO</button>

                <?php } if (isset($sip_button['url']) && $sip_button['url'] !== '' && $otherbuttonShow == 0) { ?>
                    <button class="btn main-btn payment-link" data-url="<?php echo $sip_button['url']; ?>">Start SIP</button>
                <?php }

                if (isset($lumsum_button['url']) && $lumsum_button['url'] !== '' && $otherbuttonShow == 0) {
                    ?>
                    <button class="btn main-btn payment-link" data-url="<?php echo $lumsum_button['url']; ?>">Buy Now</button>
                <?php } ?>

            </div>
        </div>
    </div>
<?php } ?>
<!-- calculator -->
<!--

    <div class="calculator-section"  style="background-image: url('images/calc-bg.jpg');">
        <div class="container">
            <div class="head">
                <h3>Tax Calculator <img src="images/calculator.png" class="img img-fluid"></h3>
            </div>
            <form class="aos-init aos-animate" data-aos="fade-up" data-aos-duration="1000">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Select Your Business Area</label>
                            <select name=""  class="form-control">
                                <option>Retail</option>
                                <option>All</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Select Your Country</label>
                            <select name="" class="form-control">
                                <option>USA</option>
                                <option>All</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Employees Number</label>
                            <select name="" placeholder="Retail" class="form-control">
                                <option>1-5</option>
                                <option>1-6</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Yearly Income</label>
                            <input type="text" name="" placeholder="Payment" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Total Payment</label>
                            <input type="text" name="" placeholder="Payment" class="form-control">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
-->

<!-- achieve -->
<?php if (!empty($achievements)) { ?>

    <div class="achieve-section">
        <div class="container">
            <div class="head text-center">
                <h3><?php echo (isset($achievementsText->{$lang})) ? $achievementsText->{$lang} : $achievementsText->en  ;?></h3>
            </div>
            <ul>
                <?php foreach ($achievements as $value) { ?>
                    <li class="aos-init" data-aos="fade-up" data-aos-duration="1000">
                        <div class="badge-box" style="background-image: url('css/newassets/images/badge-bg.png');">
                            <h5><?php echo $value['year']; ?></h5>
                        </div>
                        <h3><?php echo $value['title']; ?>
                            <span class="badge-border"></span></h3>
                    </li>
                <?php } ?>


            </ul>
        </div>
    </div>
<?php } ?>
<!-- testimonial -->
<?php 
if (!empty($testimonials)) { ?>
    <div class="test-section">
        <div class="container">
            <div class="head text-center">
                <h3><?php echo (isset($testimonialsText->{$lang})) ? $testimonialsText->{$lang} : $testimonialsText->en  ;?></h3>
            </div>
            <div class="<?php echo (count($testimonials) == 1) ? '' : 'owl-carousel test-carousel owl-theme'; ?>">
                <?php foreach ($testimonials as $testm) { ?>
                    <div class="item">
                        <div class="test-box">
                            <div class="img-wrap">
                                <img src="<?php echo $testm['profile_pic']; ?>" class="img img-fluid">
                            </div>
                            <div class="user-info">
                                <h3><?php echo $testm['name']; ?></h3>
                                <h6><?php echo $testm['designation']; ?></h6>
                                <h6><?php echo $testm['company']; ?></h6>
                            </div>
                            <div class="other-info">
                                <p> <?php echo $testm['message']; ?></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>
<!-- contact us -->

<div class="map-section" id="contact">
    <div class="container">
        <div class="row justify-content-center">
            <?php if ($QrySelectget['microsite_mapflag'] == '1') { ?>
                <div class="col map-order aos-init aos-animate" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="400">
                    <?php if ($micrositeDetails['microsite_address'] != '') {
                        ?>
                        <span id="address" style="display:none;"><?php echo htmlentities($micrositeDetails['microsite_address']); ?></span>
                        <iframe class = "map" id="map" width="90%" height="560" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    <?php } else { ?>
                        <span id="address" style="display:none;">Aditya Birla Capital Limited, Tulsi Pipe Road, Babasaheb Ambedkar Nagar, Lower Parel, Mumbai, Maharashtra</span>
                        <iframe class = "map" id="map" width="90%" height="560" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    <?php } ?>
                </div>
            <?php }
            if ($QrySelectget['microsite_getintouch'] == '1') {
            ?>

            <div class="col form-order aos-init aos-animate" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="400">
                <div class="form-wrap">
                    <div class="form-head">
                        <h5><?php echo (isset($have_question->{$lang})) ? $have_question->{$lang} : $have_question->en  ;?></h5>
                        <h3><?php echo (isset($contact_me_now->{$lang})) ? $contact_me_now->{$lang} : $contact_me_now->en  ;?></h3>
                    </div>


                    <form  id="contact_second_form" method="post">
                        <div class="form-group">
                            <input class="form-control require" pattern="^[a-zA-Z\s]+$" title="Only alphabets are allowed" id="your-second-name" type="text" placeholder="<?php echo (isset($name->{$lang})) ? $name->{$lang} : $name->en  ;?>*" name="name" value="<?php echo ($track_url_params['contact_details']['first_name'])??'';?>">
                        </div>
                        <div class="form-group">
                            <input class="form-control require" id="your-second-email" type="email" placeholder="<?php echo (isset($email->{$lang})) ? $email->{$lang} : $email->en  ;?>*" name="email" value="<?php echo ($track_url_params['semail'])??'';?>">
                        </div>
                        <div class="form-group">
                            <input class="form-control require" id="your-second-number-339" type="text" placeholder="<?php echo (isset($phone->{$lang})) ? $phone->{$lang} : $phone->en  ;?>*" name="phone" maxlength="13" value="<?php echo ($track_url_params['contact_details']['mobile'])??'';?>">
                        </div>
                        <div class="form-group">
                            <textarea id="contact_message" class=" form-control input-message " placeholder="<?php echo (isset($comment->{$lang})) ? $comment->{$lang} : $comment->en  ;?>*" rows="5" name="message"></textarea>
                        </div>

                        <button name="submit" type="button" value="Send" onclick="return submit_formbuilder_new();" id="send-valuesecond" class=" btn main-btn"><?php echo (isset($submit_now->{$lang})) ? $submit_now->{$lang} : $submit_now->en  ;?></button>
                    </form>
                    <div id="formbuilder_lead_second" style="display:none;font-size:16px;color:red;"></div>

                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<!-- address -->
<div class="address-section">
    <div class="container">
        <ul>
            <li>
                <i class="fa fa-phone-alt"></i>
                <h5><?php echo (isset($call_us->{$lang})) ? $call_us->{$lang} : $call_us->en  ;?></h5>
                <a href="tel:+91<?php echo htmlentities($firstPhone) . htmlentities($secondPhone); ?>">
                    <p>+91 <?php echo htmlentities($firstPhone) . htmlentities($secondPhone); ?></p>
                </a>
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
                <h5><?php echo (isset($address->{$lang})) ? $address->{$lang} : $address->en  ;?></h5>
                <?php
                if ($QrySelectget['microsite_address'] != '') {
                    echo '<p>' . htmlentities($micrositeDetails['microsite_address']) . '</p>';
                } else {
                    echo '<p ' . $footer_font_color . '>One Indiabulls Centre Tower 1, 16th Floor, Jupiter Mill Compound, 841, Senapati Bapat Marg, Elphinstone Road, Mumbai - 400013</p>';
                }
                ?>

            </li>
            <li>
                <i class="fa fa-envelope"></i>
                <h5><?php echo (isset($email_us->{$lang})) ? $email_us->{$lang} : $email_us->en  ;?></h5>
                <a href="mailto:<?php echo $micrositeDetails['person_email'] ?>"><p><?php echo $micrositeDetails['person_email'] ?></p></a>
            </li>
        </ul>
    </div>
</div>

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
                        <textarea class="form-control" id = "whatsapp_msg" placeholder="Type a message*"></textarea>
                    </div>
                    <div class="form-group">
                        <!-- <label>Share Your Email Id</label> -->
                        <input type="email" id="whatsapp_email" placeholder="Share Your Email Id*" class="form-control" value="<?php echo ($track_url_params['semail'])??'';?>">
                    </div>
                    <button name="submit" type="button" value="Send" onclick="return submit_formbuilder_whstapp();" id="send-valuewhtsapp" class=" btn main-btn">Share Message</button>
                </form>
                <div id="formbuilder_whstapp" style="display:none;font-size:16px;color:red;"></div>

            </div>
        </div>
    </div>
</div>
<form method="post" style="display:none;">
    <input type="text" name="hide_form">
</form>
<?php if ($QrySelectget['microsite_footernavigationmenu'] == '1' || $QrySelectget['microsite_footeraboutme'] == '1' || $QrySelectget['microsite_footercontactdetails'] == '1') { ?>
    <footer>
        <div class="container">
            <ul class="social">
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
                                                                                <li><a href="#"><i class="fab fa-whatsapp"></i></a></li>
                -->
            </ul>
            <!-- class="fraud-box">
                <p>BEWARE OF SPURIOUS / FRAUD PHONE CALLS!</p>
                <p>IRDAI is not involved in activities like selling insurance policies, announcing bonus or investment of premiums. Public receiving such phone calls are requested to lodge a police complaint.</p>
            </div>
            -->
        </div>
    </footer>
<?php } ?>

<script src="css/newassets/js/jquery.js"></script>
<script src="css/newassets/js/bootstrap.min.js"></script>
<script src="css/newassets/js/popper.min.js"></script>
<script src="css/newassets/js/aos.js"></script>
<script src="js/mailcrypt.js"></script>
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo G_RECAPTCHA_KEY; ?>"></script>

</body>
</html>
<script>
    //$.noConflict();
    $(document).ready(function ($) {
			//Code added for set selected language, Rahul Khan 15 07 2021
				var currentLang = "<?php echo $_REQUEST['lang']?>";
				$("#languages option[value='"+currentLang+"']").attr("selected","selected");
			//End
	
	
        var prefix = "+91"+"<?php echo ($track_url_params['contact_details']['mobile']) ? trim(str_replace('+91','',$track_url_params['contact_details']['mobile'])) : '';?>";
        $('#number-339').val(prefix);
        $('#your-second-number-339').val(prefix);
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
        ctr.ajax({url: "<?php echo $basepath . $_SERVER['HTTP_HOST']; ?>/sp-showcasesolutionnew.php",
            type: "post",
            data: {beetle: beetle, c_lient_Id: c_lient_Id, p_client_id: p_client_id, pcType: pcType, SolutionType: SolutionType, siteURL: siteURL,requested_url:requested_url,lang:currentLang},
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
        //carosel js code
        $(function () {
            AOS.init();
        });
        // owl-carousel
        $('.test-carousel').owlCarousel({
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
        $('.landing-carousel').owlCarousel({
            loop: true,
            margin: 10,
            dots: false,
            responsiveClass: true,
            autoplay: true,
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
        $('.pdf-carousel').owlCarousel({
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
            var docViewTop = $(window).scrollTop();
            var docViewBottom = docViewTop + $(window).height();

            var elemTop = $(elem).offset().top;
            var elemBottom = elemTop + $(elem).height();

            return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
        }

        $(window).scroll(function () {
            $('.flip-card').each(function () {
                if (isScrolledIntoView(this) === true) {
                    $(this).addClass('showcard');
                } else {
                    $(this).removeClass('showcard');
                }
            });

        });

        // fix header
        $(window).scroll(function () {
            var sticky = $('.navbar'),
                scroll = $(window).scrollTop();

            if (scroll >= 100)
                sticky.addClass('fixed-header');
            else
                sticky.removeClass('fixed-header');
        });

        $("nav li").click(function () {
            $("nav li").removeClass("active");
            // $(".tab").addClass("active"); // instead of this do the below
            $(this).addClass("active");
        });
        $(document).ready(function () {
            //  if ($('#routeName').data('route') === 'homepage') {
            // Add smooth scrolling to all links
            $("nav li a").on('click', function (event) {

                // Make sure this.hash has a value before overriding default behavior
                if (this.hash !== "") {
                    // Prevent default anchor click behavior
                    event.preventDefault();
                    // Store hash
                    var hash = this.hash;

                    // Using jQuery's animate() method to add smooth page scroll
                    // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
                    $('html, body').animate({
                        scrollTop: $(hash).offset().top
                    }, 800, function () {

                        // Add hash (#) to URL when done scrolling (default click behavior)
                        window.location.hash = hash;

                    });
                }
                $('#navbarSupportedContent').removeClass('show');
            });
            //  }
        });
		
		$('[data-aos]').parent().addClass('hideOverflowOnMobile');
    })


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



    function submit_formbuilder() {
        var frm = jQuery.noConflict();
        var c_lient_Id = '<?php echo $c_lient_Id; ?>';
        var ref_url = "<?php echo htmlentities($_SERVER['HTTP_REFERER']); ?>";
        var hide339 = frm("#hide-339").val();
        var full_name = frm("#your-name").val();
        var email = frm("#your-email").val();
        var contact = frm("#number-339").val();
        var comment = frm("#contact_message").val();
        var form_refer = "Microsite request page";
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        var intRegex = /[0-9 -()+]+$/;
        var userNameRegex = /^[a-zA-Z\s]+$/;
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
            } else if (!userNameRegex.test(full_name.trim())) {
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
                        data: {"your_message": comment, "beetle": beetle, "full_name1": full_name, "email1": email, "c_lient_Id": c_lient_Id, "form_refer": form_refer, "ref_url": ref_url, "contact1": contact, "g-recaptcha-response": token},
                        cache: false,
                        crossDomain: true,
                        beforeSend: function () {
                            frm('#send-value').attr("disabled", true).val('Please wait ...');
                        },
                        success: function (result) {
                            if (result.status) {
                                frm("#your-name").val('');
                                frm("#your-email").val('');
                                frm("#number-339").val('');
                                frm("#formbuilder_lead").css('color', 'green');
                                frm("#formbuilder_lead").show().text('Thank you for submitting. We will get back to you soon.').fadeIn(300).delay(3000).fadeOut(800);
                                frm('#send-value').attr("disabled", false).val("Submit");
                                window.setTimeout(function () {
                                    //window.location.reload();
                                }, 2000);
                            } else {
                                frm('#send-value').attr("disabled", false).val("Submit");
                                frm("#formbuilder_lead").css('color', 'red');
                                frm("#formbuilder_lead").show().text(result.message).fadeIn(300).delay(3000).fadeOut(800);
                            }
                        }
                    });
                });
            });
        }
    }
    function submit_formbuilder_new() {


        var frm = jQuery.noConflict();
        var c_lient_Id = '<?php echo $c_lient_Id; ?>';
        var ref_url = "<?php echo htmlentities($_SERVER['HTTP_REFERER']); ?>";
        var full_name = frm("#your-second-name").val();
        var email = frm("#your-second-email").val();
        var contact = frm("#your-second-number-339").val();
        var comment = frm("#contact_message").val();
        var form_refer = "Microsite request page";
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        var intRegex = /[0-9 -()+]+$/;
        var userNameRegex = /^[a-zA-Z\s]+$/;
        var beetle = frm('input[name="beetle"]').val();

        var isValid = true;
        frm("#your-second-email").each(function () {
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

        frm("#your-second-name").each(function () {
            if (frm.trim(frm(this).val()) == '') {
                isValid = false;
                frm(this).css({
                    "border": "1px solid red",
                });
            } else if (!userNameRegex.test(full_name.trim())) {
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

        frm("#your-second-number-339").each(function () {
            var numberslen = frm.trim(frm(this).val());
            if (numberslen.substr(0,3)!='+91') { numberslen="+91"+numberslen; }
            if ((frm.trim(frm(this).val()) == '' || (!intRegex.test(contact))) || numberslen.length < 13) {
                //~ if (frm.trim(frm(this).val()) == '' || (!intRegex.test(contact))) {
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
                grecaptcha.execute('<?php echo G_RECAPTCHA_KEY; ?>', {action: 'create_first_form'}).then(function (token) {
                    frm('#contact_second_form').prepend('<input type="hidden" name="g-recaptcha-response" value="' + token + '">');
                    frm.ajax({
                        type: "post",
                        url: ajxPath,
                        data: {"your_message": comment, "beetle": beetle, "full_name1": full_name, "email1": email, "c_lient_Id": c_lient_Id, "form_refer": form_refer, "ref_url": ref_url, "contact1": contact, "g-recaptcha-response": token},
                        cache: false,
                        crossDomain: true,
                        beforeSend: function () {
                            frm('#send-value').attr("disabled", true).val('Please wait ...');
                        },
                        success: function (result) {
                            if (result.status) {
                                frm("#formbuilder_lead_second").css('color', 'green');
                                frm("#formbuilder_lead_second").show().text('Thank you for submitting. We will get back to you soon.').fadeIn(300).delay(3000).fadeOut(800);
                            } else {
                                frm("#formbuilder_lead_second").css('color', 'red');
                                frm("#formbuilder_lead_second").show().text(result.message).fadeIn(300).delay(3000).fadeOut(800);
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
            data: {beetle: beetle, c_lient_Id: c_lient_Id, p_client_id: p_client_id, pcType: pcType, ContentType: ContentType, siteURL: siteURL,'is_old': false},
            cache: false, crossDomain: true,
            beforeSend: function () {
                //ctr('#showcasefilter').html('<div class="loader"></div>');                             },
            },
            success: function (result)
            {
                if(result.includes('No such content found in this content type.')){
                    ctr('#showcasefilter').addClass('showfilter_no_content');
                    ctr("#showcasefilter").html(result);
                }else{
                    ctr('#showcasefilter').removeClass('showfilter_no_content');
                    ctr("#showcasefilter").html(result);
                }
            }
        });
    }

    function showcaseproduct(SolutionType,desc) {
		
        var ctr = jQuery.noConflict();
		//var desc = "<p>"+desc+"</p>"
		if(desc == undefined){
			var desc = "";
		}
		ctr("p.solution_desc").html(desc);
		ctr("p.solution_desc").fadeIn();
		
        ctr("#activeShow_" + SolutionType).addClass('active').siblings().removeClass('active');
        var imgsol = ctr(this).attr("src");
		var currentLang = "<?php echo $_REQUEST['lang']?>";

        var c_lient_Id = '<?php echo $c_lient_Id; ?>';
        var p_client_id = '<?php echo $p_client_id; ?>';
        var pcType = '<?php echo $pcmember_pc_type; ?>';
        var siteURL = '<?php echo $basepath . $_SERVER['HTTP_HOST']; ?>';
        var beetle = ctr('input[name="beetle"]').val();
        ctr.ajax({url: "<?php echo $basepath . $_SERVER['HTTP_HOST']; ?>/sp-showcasesolutionnew.php",
            type: "post",
            data: {beetle: beetle, c_lient_Id: c_lient_Id, p_client_id: p_client_id, pcType: pcType, SolutionType: SolutionType, siteURL: siteURL,lang:currentLang},
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
                if (srch('#search-suggest li').length == 0) {
                    srch("#showsrch").css("display", "none");
                }

            }
        });
    }

    function srchshowcaseClick(val1) {
        var srchclick = jQuery.noConflict();
        srchclick("#showcsearch-name").val(val1)
        window.location.href = '<?php echo $basepath . $_SERVER['HTTP_HOST']; ?>/showcase/' + window.encodeURIComponent(val1) + '';
    }

    function whatsappShare(weburl='') {
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            var whats_app_message = encodeURIComponent(weburl);
            var whatsapp_url = "https://api.whatsapp.com/send?text=" + whats_app_message;
            window.location.href = whatsapp_url;

        } else
        {
            alert('Whatsapp sharing is only available through mobile.');
        }
    }
    var microsite = 1;
	
	function changeLang(val){
		var currentUrl = window.location.href;
		var currentLang = "<?php echo $_REQUEST['lang']?>";
		var currentBase = "<?php echo $basepath . $_SERVER['HTTP_HOST']?>";
		if((currentLang != undefined) && (currentLang != "")){
			var currentUrl  = currentUrl.replace(currentLang, val);
		}else{
			var NewHost = "<?php echo $basepath . $_SERVER['HTTP_HOST']?>/"+val;
			var currentUrl  = currentUrl.replace(currentBase, NewHost);
		}
		window.location.href = currentUrl;
	}
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
<?php if(Is_chatbot == 1){ ?>
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
            </svg>
        </div>
        <div class="css-dlvlg4 e2ujk8f1"><svg viewBox="0 0 28 32">
                <path
                        d="M28,32 C28,32 23.2863266,30.1450667 19.4727818,28.6592 L3.43749107,28.6592 C1.53921989,28.6592 0,27.0272 0,25.0144 L0,3.6448 C0,1.632 1.53921989,0 3.43749107,0 L24.5615088,0 C26.45978,0 27.9989999,1.632 27.9989999,3.6448 L27.9989999,22.0490667 L28,22.0490667 L28,32 Z M23.8614088,20.0181333 C23.5309223,19.6105242 22.9540812,19.5633836 22.5692242,19.9125333 C22.5392199,19.9392 19.5537934,22.5941333 13.9989999,22.5941333 C8.51321617,22.5941333 5.48178311,19.9584 5.4277754,19.9104 C5.04295119,19.5629428 4.46760991,19.6105095 4.13759108,20.0170667 C3.97913051,20.2124916 3.9004494,20.4673395 3.91904357,20.7249415 C3.93763774,20.9825435 4.05196575,21.2215447 4.23660523,21.3888 C4.37862552,21.5168 7.77411059,24.5386667 13.9989999,24.5386667 C20.2248893,24.5386667 23.6203743,21.5168 23.7623946,21.3888 C23.9467342,21.2215726 24.0608642,20.9827905 24.0794539,20.7254507 C24.0980436,20.4681109 24.0195551,20.2135019 23.8614088,20.0181333 Z">
                </path>
            </svg>
        </div>
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

    <script src="chatbot/js/owl.carousel.js"></script>
    <?php
    require("chatbot/js/comman_function1.php");
}else if(Is_chatbot == 2) { ?>
        <!-- Start of HubSpot Embed Code -->
        <script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/8262976.js"></script>
        <!-- End of HubSpot Embed Code -->
<?php } } ?>


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
    if ($deviceType === 'Computer') {
    ?>
    var percentage = 88;
    <?php
    } elseif ($deviceType === 'Mobile') {
    ?>
    var percentage = 55;
    <?php
    } else {
    ?>
    var percentage = 75;
    <?php
    }
    ?>

    var h = Math.floor((window.screen.availHeight / 100) * percentage);
    $('img.slider-fluid').css('width', window.screen.width);
    $('img.slider-fluid').css('height', h);
</script>
