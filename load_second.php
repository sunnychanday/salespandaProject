<?php
	$last_msg_id=$_GET['last_msg_id'];
	$csqry = "select * from sp_case_study where id < ".$last_msg_id." and user_id='".$userid."' and valid=1 and deleted=0 ORDER BY id desc LIMIT 15";
	$csres = mysqli_query($conn, $csqry);
	$totalRecord = mysqli_num_rows($csres);
	
	if($totalRecord > 0)
	{
		while($caseStudy = mysqli_fetch_array($csres))
		{
			$caseStudyId = $caseStudy['id'];
			$casestudyMember=$caseStudy["member_id"];
			$caseStudyName = $caseStudy['case_study'];
			$total_liked = $caseStudy["total_liked"];
			$total_tag = $caseStudy["total_tag"];
			$caseStudyDescription1 = $caseStudy['case_study_desc'];
			$caseStudyDescription = substr("$caseStudyDescription1", '0', '80')."...";
			$castStudyUrl =  $caseStudy['case_study_url'];
			$thumbimage = $caseStudy['image_thumb1'];
			$caseStudyTitle11 = $caseStudy['case_study_title'];
			
			$csname_urlname=trim(strtolower($caseStudyTitle11));
			$csname_urlname=str_replace(" ",'_',$csname_urlname);
			$csname_urlname=str_replace("/",'_',$csname_urlname);
			$csname_urlname=str_replace("%",'_',$csname_urlname);
			//$csname_urlname=str_replace("'",'_',$csname_urlname);
			//$csname_urlname=str_replace("`",'_',$csname_urlname);
			$csname_urlname=ucwords($csname_urlname);
			//	$cstitleUrl = $csname_urlname."_".$caseStudyId;
			$cstitleUrl = trim($csname_urlname);
			
			$contentType=$caseStudy['content_type'];
			if($contentType!='')
			{
				$contentTypeName=getarticleName($caseStudy['content_type']);
			}
			
			if($contentType==1){
				$urlName = 'White-Paper';
			}
			
			if($contentType==2){
				$urlName = 'Blogs-and-Articles';
			}
			
			if($contentType==3){
				$urlName = 'Case-Studies';
			}
			
			if($contentType==4){
				$urlName = 'Brochure-and-Datasheet';
			}
			
			if($contentType==5){
				$urlName = 'Statistics';
			}
			
			if($contentType==6){
				$urlName = 'Infographics';
			}
			
			if($contentType==7){
				$urlName = 'Presentation';
			}
			
			if($contentType==''){
				$urlName = 'ShowCase';
			}
			
			$caseStudyTitleLength=strlen($caseStudyTitle11);
			$attachforcompany = $caseStudy['attach_company'];
		
			if($total_tag>=1){
				$tagbyname = "select * from sp_article_tag where case_study_id = '".$caseStudyId."' and deleted=0 order by rand()";
				$resm = mysqli_query($conn, $tagbyname);
				$resData = mysqli_fetch_array($resm);
				$taggedMember = getMemberName($resData['member_id']);
			}
		
			if($total_liked>=1){
				$likebyname = "select * from sp_article_liked where case_study_id = '".$caseStudyId."' and deleted=0 order by rand()";
				$like_m = mysqli_query($conn, $likebyname);
				$likeData = mysqli_fetch_array($like_m);
				$likedMember = getMemberName($likeData['member_id']);
			}
		
			if($caseStudyTitleLength > 70)
			{
				$caseStudyTitle = substr("$caseStudyTitle11", '0', '70')."...";
			}
			else
			{
				$caseStudyTitle=$caseStudyTitle11;
			}
		
			$memberType=getMemberType($casestudyMember);
			if($memberType=='Individual Profile')
			{
				$profilePath=$sitepath.'member_case_study.php?id='.$casestudyMember;	
			}
			/*else
			{
				$profilePath=$sitepath.'company_case_study.php?id='.getCompanyIdFromMemberid($casestudyMember);		
			}*/
			
			$extn = end(explode('.', $castStudyUrl));
			if($extn=='PDF' or $extn=='pdf' or $extn=='doc' or $extn=='docx' or $extn=='ppt' or $extn=='PPT')
			{
				$castStudyUrl_link = $castStudyUrl;
				$castStudyUrl_link_url='';
			}
			else
			{
				$castStudyUrl_link_url = $castStudyUrl;
				$castStudyUrl_link_url11 = str_replace(".$sitepath."," ","$castStudyUrl_link_url");
				$castStudyUrl_link='';
			}
			
			$like_unlike_query = mysqli_query($conn, $t="select * from sp_article_liked where member_id='".$userid."' and case_study_id='".$caseStudyId."' and valid=1 and deleted=0");
			$like_row = mysqli_fetch_array($like_unlike_query);
	
			$article_like_id=$like_row["case_study_id"];
			$article_user_id=$like_row["member_id"];
		
			$cs_categ=mysqli_query($conn, $csc="SELECT * from sp_case_study_category where user_id='".$userid."' AND case_study_id='".$caseStudyId."' AND valid=1 AND deleted=0 AND tag_retag_id='R'");
			
			$cs_cat_row=mysqli_fetch_array($cs_categ);
			$cs_cid=$cs_cat_row["case_study_id"];
			$cs_catUid=$cs_cat_row["user_id"];
	
			$cs_subcat=mysqli_query($conn, $css="SELECT * FROM sp_case_study_subcategory WHERE user_id='".$userid."' AND case_study_id='".$caseStudyId."' AND valid=1 AND deleted=0 AND tag_retag_id='R'"));
			$cs_subcat_row=mysqli_fetch_array($cs_subcat);

			$cs_sid=$cs_subcat_row["case_study_id"];
			$cs_subcatUid=$cs_subcat_row["user_id"];
	
			$cs_product=mysqli_query($conn, $csp="SELECT * FROM sp_case_study_product WHERE user_id='".$userid."' AND case_study_id='".$caseStudyId."' AND valid=1 AND deleted=0 AND tag_retag_id='R'");
			$cs_product_row=mysqli_fetch_array($cs_product);
			$cs_pid=$cs_product_row["case_study_id"];
			$cs_prdUid=$cs_product_row["user_id"];
	
			$cs_vertical=mysqli_query($conn, $csv="SELECT * FROM sp_case_study_vertival WHERE user_id='".$userid."' AND case_study_id='".$caseStudyId."' AND valid=1 AND deleted=0 AND tag_retag_id='R'");
			$cs_vertical_row=mysqli_fetch_array($cs_vertical);
			$cs_vid=$cs_vertical_row["case_study_id"];
			$cs_verUid=$cs_vertical_row["user_id"];
	
			if($cs_cid!='' || $cs_sid!='' || $cs_pid!='' || $cs_vid!='')
			{
				$article_tag_id=$caseStudyId;
			}
	
			if($cs_catUid!='' || $cs_subcatUid!='' || $cs_prdUid!='' || $cs_verUid!='')
			{
				$article_taguser_id=$userid;
			}
			?>
			
			<div id="<?php echo $caseStudyId; ?>" class="item">
				<?php /*?><div class="tagLike">
					<?php if($userid > 0)
						{
						?>
						<?php 
						//echo $caseStudyId."==".$article_tag_id."==".$userid."==".$article_taguser_id;
						if($caseStudyId!=$article_tag_id || $userid!=$article_taguser_id){?> 
						<a id="tag_<?php echo $caseStudyId; ?>" class="like floatL" onClick="tagme(<?php echo $caseStudyId; ?>)">TAG</a><?php } else{?><a id="untag_<?php echo $caseStudyId; ?>" class="like floatL" onClick="untagme(<?php echo $caseStudyId;?>)">UNTAG</a><?php } ?>
						<a id="untagme_<?php echo $caseStudyId; ?>" class="like floatL unlikeme" onClick="hide_tagme(<?php echo $caseStudyId; ?>)" style="display:none">UNTAG</a>
						<a id="tagme_<?php echo $caseStudyId; ?>" class="like floatL likeme" onClick="unhide_tagme(<?php echo $caseStudyId; ?>)" style="display:none">TAG</a>
						<?php
						//echo $caseStudyId."==".$article_like_id."==".$userid."==".$article_user_id; 
						 if($caseStudyId!=$article_like_id && $userid!=$article_user_id){?>
						<a id="like_<?php echo $caseStudyId; ?>" class="like floatR" onClick="likeme(<?php echo $caseStudyId; ?>)">LIKE</a><?php } else{ ?><a id="unlike_<?php echo $caseStudyId; ?>" class="like floatR" onClick="unlikeme(<?php echo $caseStudyId;?>)">UNLIKE</a><?php } ?>
						<a id="unlikeme_<?php echo $caseStudyId; ?>" class="like floatR unlikeme" onClick="hideme(<?php echo $caseStudyId; ?>)" style="display:none">UNLIKE</a>
						<a id="likeme_<?php echo $caseStudyId; ?>" class="like floatR likeme" onClick="unhideme(<?php echo $caseStudyId; ?>)" style="display:none">LIKE</a>
						<?php } else { ?>
						<a id="clicker" class="tag floatL" onClick="getcsId(<?php echo $caseStudyId?>)">TAG</a>
						<a id="clicker" class="like floatR" onClick="getlikeId(<?php echo $caseStudyId?>)">LIKE</a><?php } ?>
						
				  </div><?php */?>
			
				<?php
				if($thumbimage!=''){
					if($castStudyUrl_link!='' && $castStudyUrl_link_url==''){
						?>
						<a href="showcase.php?csId=<?php echo $caseStudyId; ?>">
						<?php
					}
					else if($castStudyUrl_link_url!='') {
						?>
						<a href="showcase.php?csId=<?php echo $caseStudyId; ?>">
						<?php
					}
					else
					{
						?>
						<a href="showcase.php?csId=<?php echo $caseStudyId; ?>">
						<?php
					}
					?>
			
					<img class="thumb" src="<?php echo $sitepath; ?>upload/casestudy/thumb/<?php echo $thumbimage; ?>" id="<?php echo $caseStudyId; ?>"/>
					<?php
				}
				else
				{
					if($castStudyUrl_link!='' && $castStudyUrl_link_url==''){
						?>
						<a href="showcase.php?csId=<?php echo $caseStudyId; ?>">
						<?php
					}
					else if($castStudyUrl_link_url!='' && $castStudyUrl_link=='') {
						?>
						<a href="showcase.php?csId=<?php echo $caseStudyId; ?>">
						<?php
					}
					else
					{
						?>
						<a href="showcase.php?csId=<?php echo $caseStudyId; ?>">
						<?php
					}
					?>
			
					<img src="<?php echo $sitepath; ?>upload/casestudy/nopic/nopic.jpg" class="thumb">
					<?php
				}
				?>
			
				<!-- Image must be 400px by 300px -->
				<h3><?php echo ucfirst($caseStudyTitle); ?></h3><!--Title-->
			</a>
 			<?php /*?><p class="tagCount bdrT">
            	<span class="floatL posRel t">
                	<span id="totaltag_<?php echo $caseStudyId?>"><?php echo $total_tag;?> Tags</span>
                    <span class="retagCount countrLB"><span class="bdr">
			
			<a class="" href="<?php echo $profilePath;?>"><span class="whiteTxt">Added by :</span> <?php echo ucwords(getMemberName($casestudyMember));?></a> 
			<?php if($attachforcompany!=0){?>
			 <a class="" href="<?php echo $sitepath; ?>company_case_study.php?id=<?php echo $attachforcompany; ?>"> <span class="whiteTxt">for</span> <?php echo ucwords(companyName($attachforcompany));?></a> 
			<?php } ?>
			
			<?php if($total_tag>=1){ ?>
 			<a class="" id="clickernew" onClick="getCaseStudyId(<?php echo $caseStudyId?>)">
			<?php } ?>
			<span class="whiteTxt">Tagged by :</span> <?php if($total_tag>=1){ echo $taggedMember; } ?> <?php if($total_tag>1){ echo 'and'; ?> <?php echo $total_tag-1?> other <?php } ?></a></span></span>
				</span>
            	<span class="floatR posRel l">
                	<span id="totalLike_<?php echo $caseStudyId?>"><?php echo $total_liked;?> Likes</span>
                    <span class="retagCount countrRB"><span class="bdr">
			
			<?php if($total_liked>=1){ ?>
 			<a class="" id="clickerlike" onClick="getlikemember(<?php echo $caseStudyId?>)">
			<?php } ?>
			<span class="whiteTxt">Liked by :</span> <?php if($total_liked>=1){ echo $likedMember; } ?> <?php if($total_liked>1){ echo 'and'; ?> <?php echo $total_liked-1?> other <?php } ?></a></span></span>
                </span>
            </p><?php */?>
			
			

        </div>
		<?php
	}
}
else 
{
	?>
	<div></div>
	<?php
}
exit;
