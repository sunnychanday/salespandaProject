<?php
	include("includes/global.php");
	include("includes/check_login.php");
	include("includes/function.php");

	$sql_cms_subdomain = mysqli_query($conn, "select userid from sp_subdomain where client_id='".$c_lient_Id."'");
	$data_cms_subdomain = mysqli_fetch_array($sql_cms_subdomain);
	$puserid = $data_cms_subdomain['userid'];

	$pcmemqry1="SELECT P.pid, P.member_pc_type, PC.* FROM sp_sub_members as PC INNER JOIN  sp_members as P ON PC.c_client_id=P.client_id  where PC.c_client_id = '".$c_lient_Id."' and P.valid=1 and P.deleted=0 and P.approve=1"; 
	$pcmemb_ftch = mysqli_query($conn, $pcmemqry1);
	$row_pcmem = mysqli_fetch_array($pcmemb_ftch);
	$pcmember_pc_type = $row_pcmem['member_pc_type'];
	$p_client_id = $row_pcmem['p_client_id'];

	if(isset($_POST['lastmsg']))
	{
		$lastmsg=$_POST['lastmsg'];
		
        if($pcmember_pc_type=='C'){
            $result = mysqli_query($conn, "select CS.*,TS.id as syndid, TS.p_client_id, TS.c_client_id, TS.submem_content_publish_url from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".$c_lient_Id."' and CS.valid=1 and CS.deleted=0 and TS.approve=1 and CS.item_order > '$lastmsg' ORDER BY CS.item_order, CS.id desc limit 8");
        }
        else
		{
			$result = mysqli_query($conn, "select * from sp_case_study where valid=1 and deleted=0 and approve=1 and client_id='".$c_lient_Id."' and item_order > '$lastmsg' order by item_order limit 8");
        }

		while($caseStudy=mysqli_fetch_array($result))
		{
			$caseStudyId = $caseStudy['id'];
			$caseStudyItem = $caseStudy['item_order'];
			$casestudyMember=$caseStudy["member_id"];
			$caseStudyName = $caseStudy['case_study'];
			$documentMode=$caseStudy['doc_mode'];
			$caseLandStatus=$caseStudy['landingpage_status']; 
			$caseLandId=$caseStudy['landingpage_id'];
			$caseStudyDescription1 = $caseStudy['case_study_desc'];
			$caseStudyDescription = substr("$caseStudyDescription1", '0', '260')."...";
			$castStudyUrl =  $caseStudy['case_study_url'];	
			$thumbimage = $caseStudy['image_thumb1'];
			$crop_image=$caseStudy['crop_Image'];
			$video_image=$caseStudy['video_image'];
			$caseStudyTitle11 = $caseStudy['case_study_title'];
			$caseStudyActualTitle = ($caseStudy['case_study_actual_title']!='') ? $caseStudy['case_study_actual_title'] : $caseStudy['case_study_title'];
			$filterFlag='s';
	
			//htaccess title convert
			$csname =str_replace(' ', '-', $caseStudyTitle11);
			//end
	
			$contentType=$caseStudy['content_type'];
			if($contentType!='')
			{
				$contentTypeName=getarticleName($caseStudy['content_type']);
			}
			
			$caseStudyTitleLength=strlen($caseStudyActualTitle);
			$attachforcompany = $caseStudy['attach_company'];
		
			if($caseStudyTitleLength > 35)
			{
				$caseStudyTitle = substr($caseStudyActualTitle, '0', '35')."..";
			}
			else
			{
				$caseStudyTitle=$caseStudyActualTitle;
			}
			
			if($pcmember_pc_type=='C'){
				$cslandquery=mysqli_query($conn, "select LS.*, LP.publish_page_id,LP.landingpage_title,LP.landingpage_desc,LP.page_title_seo,LP.meta_description from sp_landingpage_publish as LP INNER JOIN sp_landingpage_syndication as LS ON LP.publish_page_id = LS.landingpage_id where LP.publish_page_id='".$caseLandId."' and LP.client_id='".$p_client_id."' ");
			}
			else
			{
				$cslandquery=mysqli_query($conn, "select  publish_page_id,landingpage_title,landingpage_desc,page_title_seo,meta_description,approve from sp_landingpage_publish where publish_page_id='".$caseLandId."' and client_id='".$c_lient_Id."' ");
			}

			$cslandget=mysqli_fetch_array($cslandquery);
			$cslandname=$cslandget['landingpage_title'];
			$cslandApprove= $cslandget['approve'];
			?>	 
			
			<div id="<?php echo $caseStudyItem; ?>" class="item">
				<?php
				if($thumbimage!='' && $crop_image==''){
					?>
					<img class="thumb" src="manager/uploads/thumb_img/<?php echo $thumbimage; ?>" id="<?php echo $caseStudyId; ?>" alt="<?php echo $caseStudyTitle11; ?>" />
					
					<input name="articleId" type="hidden" value="<?php echo $caseStudyId ; ?>" id="articleId" />
	
					<?php
				}
				else if($thumbimage=='' && $crop_image!='')
				{
					if($caseLandStatus==1 && $cslandApprove==1) {
						?> 
						<a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>"><img id='base64image' src='data:image/png;base64,<?php echo $crop_image; ?>' alt="<?php echo $caseStudyTitle11; ?>" /></a>
						<?php
					}else{
						?>
						<a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>"><img id='base64image' src='data:image/png;base64,<?php echo $crop_image; ?>' alt="<?php echo $caseStudyTitle11; ?>"  /></a>
						<?php
					}
					?>
					
					<input name="articleId" type="hidden" value="<?php echo $caseStudyId ; ?>" id="articleId" />
					<?php
				}elseif($thumbimage!='' && $crop_image!=''){
					
					if($caseLandStatus==1 && $cslandApprove==1){
						?> 
						<a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>"><img class="thumb" src="manager/uploads/thumb_img/<?php echo $thumbimage; ?>" alt="<?php echo $caseStudyTitle11; ?>" /></a>
						<?php
					}else{
						?>
						<a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>"><img class="thumb" src="manager/uploads/thumb_img/<?php echo $thumbimage; ?>" alt="<?php echo $caseStudyTitle11; ?>" /></a>
						<?php
					}
					?>
					
					<input name="articleId" type="hidden" value="<?php echo $caseStudyId ; ?>" id="articleId" />
    
					<?php
				}elseif($video_image!='' && $crop_image==''){
					
					if($caseLandStatus==1 && $cslandApprove==1){
						?> 
						<a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>"><img class="thumb" src="<?php echo $video_image; ?>" alt="<?php echo $caseStudyTitle11; ?>" /></a>
						<?php
					}else{
						?>
						<a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>"><img class="thumb" src="<?php echo $video_image; ?>" alt="<?php echo $caseStudyTitle11; ?>" /></a>
						<?php
					}
					?>

					<input name="articleId" type="hidden" value="<?php echo $caseStudyId ; ?>" id="articleId" />
					<?php
				}else{
					?> 	
					<img src="<?php echo $sitepath; ?>upload/casestudy/nopic/nopic.jpg" class="thumb" alt="<?php echo $caseStudyTitle11; ?>" >
					<input name="articleId" type="hidden" value="<?php echo $caseStudyId ; ?>" id="articleId" />
					<?php
				}
				?>
			
				<!-- Image must be 400px by 300px -->
				<div class="itemSlideTxt">
					<div class="itemH3">
						<?php
						if($caseLandStatus==1 && $cslandApprove==1){
							?> 
							<a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>"><?php echo ucfirst($caseStudyTitle); ?></a>
							<?php
						}else{
							?>
							<a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>"><?php echo ucfirst($caseStudyTitle); ?></a>
							<?php
						}
						?>
					</div><!--Title-->
					<div class="itemH4"><?php echo $contentTypeName; ?></div>                       
					<p><?php echo $caseStudyDescription; ?></p>
					<?php
					if($caseLandStatus==1 && $cslandApprove==1){
						?> 
						<div class="alignC itemSlideBtn"><a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>" class="btn whiteTxt padL padR normal" target="_blank">Learn more &raquo;</a></div>
						<?php
					}else{
						?>
						<div class="alignC itemSlideBtn"><a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>" class="btn whiteTxt padL padR normal">Read more &raquo;</a></div>
						<?php
					}
					?>
				</div>
				<!--</a>-->
			</div>
			<?php
		}
		
		if($caseStudyItem<='1')
		{
			?>
			<div id="more<?php echo $caseStudyItem; ?>" class="morebox" style="display:none;">
				<div class="clearfix"></div> 
				<a href="#" id="<?php echo $caseStudyItem; ?>" class="more">show more</a>
			</div>
			<?php
		}
		else
		{
			?>
			<div id="more<?php echo $caseStudyItem; ?>" class="morebox">
				<div class="clearfix"></div> 
				<a href="#" id="<?php echo $caseStudyItem; ?>" class="more">show more</a>
			</div>
			<?php
		}
	}
