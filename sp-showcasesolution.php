<?php 
/*Author name: <Bizight Solutions Pvt Ltd> 
 * Purpose of document/ page : <This is page is used to get the content library data for microsite page.> 
 * Date: 17-02-2021 
 * Copyright info : <Copyright @2021, Bizight Solutions Pvt Ltd>
*/
header('Access-Control-Allow-Origin: *'); 
include("includes/global.php");
include("includes/function.php");
include("manager/common_functions.php");
include("csrf/csrf-magic.php");

/*
error_reporting(E_ALL);
ini_set('display_errors', 1);
//*/

$articleId = $articleName = '';
$requested_url = $_REQUEST['requested_url'] ?? '';
if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
	//echo strtolower(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST));die;
	if((strtolower(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST)) != strtolower($_SERVER['HTTP_HOST'])) || (strtolower(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST))!=strtolower(parse_url($_REQUEST['siteURL'], PHP_URL_HOST)))){
		errorCallbackFunction();
		// referer not from the same domain
	}
}elseif(isset($_REQUEST['siteURL']) && $_REQUEST['siteURL'] != $_SERVER['HTTP_HOST']){
	errorCallbackFunction();
}

if($_REQUEST['SolutionType']==0)
{
	$subquery="";
} else {
	if($_REQUEST['pcType']=='C')
		$subquery=" AND FIND_IN_SET('".trim(testInput($_REQUEST['SolutionType']))."', CS.category)";
	else
		$subquery=" AND FIND_IN_SET('".trim(testInput($_REQUEST['SolutionType']))."', category)";
}

if($_REQUEST['pcType']=='C')
	$csqry = mysqli_query($conn, "select CS.*, TS.id as syndid from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".testInput($_REQUEST['c_lient_Id'])."' and CS.valid=1 and CS.deleted=0 and TS.approve=1 $subquery ORDER BY  CS.item_order, CS.id desc limit 9");
else
	$csqry = mysqli_query($conn, "select id, video_image, member_id, item_order, image_thumb1, doe, dou, crop_Image, case_study_title, case_study_actual_title, case_study_desc, doc_mode, content_type, case_study, landingpage_status, landingpage_id from sp_case_study where client_id='".testInput($_REQUEST['c_lient_Id'])."' and valid=1 and deleted=0 and approve=1 $subquery ORDER BY  item_order, id desc limit 9");
$countShowcase=mysqli_num_rows($csqry);
if($countShowcase>0)
{      
	while($caseStudy = mysqli_fetch_assoc($csqry)) {
		$caseStudyId = $caseStudy['id'];
		$caseStudyItem = $caseStudy['item_order']; 
		$casestudyMember = $caseStudy["member_id"];
		$caseStudyName = $caseStudy['case_study'];
		$documentMode = $caseStudy['doc_mode'];
		$caseLandStatus = $caseStudy['landingpage_status']; 
		$caseLandId = $caseStudy['landingpage_id']; 
		$caseStudyDescription1 = $caseStudy['case_study_desc'];
		$caseStudyDescription = substr("$caseStudyDescription1", '0', '140')."..";
		$castStudyUrl =  (isset($caseStudy['case_study_url'])) ? $caseStudy['case_study_url'] : '';
		$crop_image = $caseStudy['crop_Image'];
		
		if(isset($caseStudy['video_image']) && $caseStudy['video_image'] != '')
			$showcaseThumb=$caseStudy['video_image'];
		else
			$showcaseThumb=$_REQUEST['siteURL'].'/manager/uploads/thumb_img/'.$caseStudy['image_thumb1'];
		$caseStudyTitle11 = $caseStudy['case_study_title'];
		$caseStudyActualTitle = ($caseStudy['case_study_actual_title']!='') ? $caseStudy['case_study_actual_title'] : $caseStudy['case_study_title'];
		$csname =str_replace(' ', '-', $caseStudyTitle11 );
		$contentType=$caseStudy['content_type'];
		if($contentType!='')
		   $contentTypeName=getarticleName(testInput($caseStudy['content_type']));
		if($contentType!='')
		{
			$article = (isset($article)) ? testInput($article) : '';
			$casequery=mysqli_query($conn,"select article_type from sp_article_type where id='{$article}'");
			$caserow=mysqli_fetch_array($casequery);
			$articleName=$caserow['article_type'];
			$articleId=$caserow['id'];
		}	

		if($articleId==$contentType)
			$urlName=$articleName;
		$caseStudyTitleLength=strlen($caseStudyActualTitle);
		$attachforcompany = (isset($caseStudy['attach_company'])) ? $caseStudy['attach_company'] : '';
		$filterFlag='s';
		
		//htaccess title convert
		$csname =str_replace(' ', '-', $caseStudyTitle11);
		if($caseStudyTitleLength > 35)
			$caseStudyTitle = substr($caseStudyActualTitle, '0', '35')."..";
		else
			$caseStudyTitle=$caseStudyActualTitle;

		if($_REQUEST['pcType']=='C')
			$cslandquery=mysqli_query($conn,"select LS.*, LP.publish_page_id, LP.landingpage_title, LP.publish_page_name, LP.landingpage_desc, LP.page_title_seo, LP.meta_description from sp_landingpage_publish as LP INNER JOIN sp_landingpage_syndication as LS ON LP.publish_page_id = LS.landingpage_id where LP.publish_page_id='".testInput($caseLandId)."' and LP.client_id='".testInput($p_client_id)."' ");
		else
			$cslandquery=mysqli_query($conn,"select  publish_page_id, landingpage_title, publish_page_name, landingpage_desc, page_title_seo, meta_description, approve from sp_landingpage_publish where publish_page_id='".testInput($caseLandId)."' and client_id='".testInput($_REQUEST['c_lient_Id'])."' ");
		$cslandget=mysqli_fetch_array($cslandquery);
		$cslandname=$cslandget['publish_page_name'];
		$cslandApprove= $cslandget['approve'];
		?>
            <div class="col-sm-4" style="margin-top: 20px;">
                <div class="item">
                    <div class="sp-blog-box">
                        <div class="sp-blog-image clearfix">
                            <?php if($caseLandStatus==1 && $cslandApprove==1) { ?>
                                <a target="_blank" href="<?php echo htmlentities($_REQUEST['siteURL']); ?>/landingpage/<?php echo htmlentities($cslandname).$requested_url; ?>"><img class="img-fluid center-block" src="<?php echo htmlentities($showcaseThumb); ?>" alt="<?php echo htmlentities($cslandname); ?>"></a>
                            <?php } else { ?>
                                <a target="_blank" href="<?php echo htmlentities($_REQUEST['siteURL']); ?>/showcase/<?php echo htmlentities($csname).$requested_url; ?>"><img class="img-fluid center-block" src="<?php echo htmlentities($showcaseThumb); ?>" alt="<?php echo htmlentities($csname); ?>"></a>
                            <?php } ?>
                        </div>
                        <div class="sp-blog-detail" style="height:320px;">
                            <div class="blog-title">
                                <h5 class="sp-tw-4 sp-mb-10"><?php echo ucfirst(htmlentities($caseStudyTitle)); ?></h5>
                            </div>
                            <div class="blog-content">
                                <p><?php echo htmlentities($caseStudyDescription); ?></p>
                            </div>
                            <div class="social-icons">
                                <ul class="info-share">
                                    <li>
                                        <?php if($caseLandStatus==1 && $cslandApprove==1) {?>
                                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo htmlentities($_REQUEST['siteURL']); ?>/landingpage/<?php echo htmlentities($cslandname).$requested_url; ?>?channel_type=Facebook"><i class="fa fa-facebook"></i></a>
                                        <?php } else { ?>
                                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo htmlentities($_REQUEST['siteURL']); ?>/showcase/<?php echo htmlentities($csname).$requested_url; ?>?channel_type=Facebook"><i class="fa fa-facebook"></i></a>
                                        <?php } ?>
                                    </li>
                                    <li>
                                        <?php if($caseLandStatus==1 && $cslandApprove==1) {?>
                                            <a href="https://twitter.com/intent/tweet?text=<?php echo htmlentities($caseStudyDescription); ?>&url=<?php echo htmlentities($_REQUEST['siteURL']); ?>/landingpage/<?php echo htmlentities($cslandname).$requested_url; ?>?channel_type=twitter"><i class="fa fa-twitter"></i></a>
                                        <?php } else { ?>
                                            <a href="https://twitter.com/intent/tweet?text=<?php echo htmlentities($caseStudyDescription); ?>&url=<?php echo htmlentities($_REQUEST['siteURL']); ?>/showcase/<?php echo htmlentities($csname).$requested_url; ?>?channel_type=twitter"><i class="fa fa-twitter"></i></a>
                                        <?php } ?>
                                    </li>
                                    <li>
                                        <?php if($caseLandStatus==1 && $cslandApprove==1) {?>
                                            <a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo htmlentities($_REQUEST['siteURL']); ?>/landingpage/<?php echo htmlentities($cslandname).$requested_url; ?>?channel_type=linkedin"><i class="fa fa-linkedin"></i></a>
                                        <?php } else {?>
                                            <a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo htmlentities($_REQUEST['siteURL']); ?>/showcase/<?php echo htmlentities($csname).$requested_url; ?>?channel_type=linkedin"><i class="fa fa-linkedin"></i></a>
                                        <?php } ?>
                                    </li>
                                    <li>
                                        <?php if($caseLandStatus==1 && $cslandApprove==1) { ?>
                                            <a onclick="whatsappShare(jQuery(this).data('link'));" style="cursor: pointer;" data-link="<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>/landingpage/<?php echo $cslandname.$requested_url; ?>?channel_type=whatsapp"><i class="fa fa-whatsapp"></i></a>
                                        <?php } else { ?>
                                            <a onclick="whatsappShare(jQuery(this).data('link'));" style="cursor: pointer;" data-link="<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>/showcase/<?php echo $csname.$requested_url; ?>?channel_type=whatsapp"><i class="fa fa-whatsapp"></i></a>
                                        <?php } ?>
                                    </li>
                                </ul>
                            </div>
                            <div class="sp-blog-meta">
                                <ul class="list-inline">
                                    <li class="list-inline-item"><i class="fa fa-edit"></i>&nbsp;<?php echo $contentTypeName; ?></li>
                                    <li class="list-inline-item"><i class="fa fa-calendar"></i>&nbsp;<?php if(strtotime($caseStudy['dou'])=='62169955200 ' || strtotime($caseStudy['dou'])=='FALSE') { echo date('d - m - Y',strtotime($caseStudy['doe'])); } else { echo date('d - m - Y',strtotime($caseStudy['dou'])); }?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		<?php } } else { ?>
            <div class="col-md-12">
                <h4 class="sp-tw-6 sp-font-white text-center contentText"><span class="sp-font-green">No such content found in this solution type.</span></h4>
            </div>
       <?php }?>
