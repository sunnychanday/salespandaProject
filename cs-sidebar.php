<?php
/*Author name: <Bizight Solutions Pvt Ltd> 
 * Purpose of document/ page : This is use to get similar content on showcase page 
 * Date:  11-02-2021 
 * Copyright info : <Copyright @2021, Bizight Solutions Pvt Ltd>
*/

	if($caseId!=''){
        if($pcmember_pc_type=='C'){
			$qry = "select CS.content_type, CS.category, CS.vertical, TS.id as syndid, TS.p_client_id, TS.c_client_id, TS.submem_content_publish_url from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where CS.id='".$caseId."' and TS.c_client_id='".$c_lient_Id."' and CS.approve!='3' and CS.valid=1 and CS.deleted=0 and TS.approve=1";
		}else{
			$qry="select content_type,category,vertical from sp_case_study where id='".$caseId."' and client_id='".$c_lient_Id."' and valid=1 and approve=1 and deleted=0";
		}  

		$resq=mysqli_query($conn,$qry);
		$docData=mysqli_fetch_array($resq);
		$csType = $docData['content_type'];
		$solutionct=$docData["category"];
		$industry=$docData["vertical"];
	}

	if($solutionct!=''){
		if($pcmember_pc_type=='C'){
			$sideqry = "select CS.*, TS.id as syndid, TS.p_client_id, TS.c_client_id, TS.submem_content_publish_url from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where CS.id!='".$caseId."' and TS.c_client_id='".$c_lient_Id."' and CS.approve!='3' and CS.valid=1 and CS.deleted=0 and TS.approve=1 and (CS.category IN($solutionct) or FIND_IN_SET('".$solutionct."',CS.category)) order by RAND() limit 3";
		}else{
			'<br>Q2= '.$sideqry = "select id,case_study_title,case_study_desc,image_thumb1,crop_Image,content_type from sp_case_study where id!='$caseId' and client_id='".$c_lient_Id."' and approve=1 and valid=1 and deleted=0 and (category IN($solutionct) or FIND_IN_SET('".$solutionct."',category)) order by RAND() limit 3";
		}
	}
	//Vertical 
	else if($solutionct=='' && $csType!='')	//Vertical 
	{
		if($pcmember_pc_type=='C'){
			$sideqry = "select CS.*, TS.id as syndid, TS.p_client_id, TS.c_client_id, TS.submem_content_publish_url from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where CS.id!='".$caseId."' and TS.c_client_id='".$c_lient_Id."' and CS.approve!='3' and CS.valid=1 and CS.deleted=0 and TS.approve=1 and CS.content_type=".$csType." order by RAND() limit 3";    
		}else{ 
			'<br>Q3= '.$sideqry = "select id,case_study_title,case_study_desc,image_thumb1,crop_Image,content_type from sp_case_study where id!='$caseId' and client_id='".$c_lient_Id."' and approve=1 and valid=1 and deleted=0 and content_type=".$csType." order by RAND() limit 3";
		}
	}
	else if($solutionct=='' && $csType==''){
		if($pcmember_pc_type=='C'){
			$sideqry = "select CS.*, TS.id as syndid, TS.p_client_id, TS.c_client_id, TS.submem_content_publish_url from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where CS.id!='".$caseId."' and TS.c_client_id='".$c_lient_Id."' and CS.approve!='3' and CS.valid=1 and CS.deleted=0 and TS.approve=1 order by RAND() limit 3";
		}else{
			'<br>Q4= '.$sideqry = "select id,case_study_title,case_study_desc,image_thumb1,crop_Image,content_type from sp_case_study where id!='$caseId' and client_id='".$c_lient_Id."' and approve=1 and valid=1 and deleted=0 order by RAND() limit 3";
		}
	}

	$rightres = mysqli_query($conn,$sideqry);
	$count = mysqli_num_rows($rightres);

	while($rightData = mysqli_fetch_array($rightres)){
		$csid_sb = $rightData['id'];
		$csTitle = $rightData['case_study_title'];
		$caseStudyTitleLength=strlen($csTitle);
		if($caseStudyTitleLength > 35){
			$caseStudyTitle = substr("$csTitle", '0', '35')."..";
		}else{
			$caseStudyTitle=$csTitle;
		}
		//htaccess title convert
		$csname =str_replace(' ', '-', $csTitle);
		
		$csnameAavtar=$csname =str_replace('-', ' ', $csTitle);
		
		//end
		$csDesc = $rightData['case_study_desc'];
		$cDesc_sb = substr("$csDesc", '0', '260')."...";
		$scImage_sb = $rightData['image_thumb1'];
		$thumbImage = $rightData['crop_Image'];
		
		$contentType=$rightData['content_type'];
		if($contentType!=''){
			$contentTypeName=getarticleName($contentType);
		}
		
		if($csid_sb!=''){
			?>
			<div class="col-md-12">
				<div class="row">
					<?php
					if($scImage_sb!='' && $thumbImage==''){
						?>
						<img class="card-img-top" width="230" height="230" src="<?php echo $weburl; ?>/manager/uploads/thumb_img/<?php echo $scImage_sb; ?>" id="<?php echo $csid_sb; ?>" alt="<?php echo $csTitle; ?>"  />
						<?php
					}else if($scImage_sb=='' && $thumbImage!=''){
						?>
						<img class="card-img-top" width="230" height="230" id='base64image' src='data:image/png;base64,<?php echo $thumbImage; ?>' alt="<?php echo $csTitle; ?>"  />
						<?php
					}else if($scImage_sb!='' && $thumbImage!=''){
						?>
						<img class="card-img-top" width="230" height="230" src="<?php echo $weburl; ?>/manager/uploads/thumb_img/<?php echo $scImage_sb; ?>" id="<?php echo $csid_sb; ?>" alt="<?php echo $csTitle; ?>" />
						<?php
					}else{
						?>
						<img class="card-img-top" width="230" height="230" src="<?php echo $weburl; ?>/upload/casestudy/nopic/nopic.jpg" class="thumb" alt="<?php echo $csTitle; ?>" >
						<?php
					}
					?>    
					<div class="card-body">
						<h5 class="card-title"><?php echo ucfirst($csnameAavtar);?></h5>
						<p class="card-subtitle mb-2 text-muted"><?php echo $contentTypeName; ?></p>
						<p class="card-text" style="line-height: 1.5;font-size: 14px;text-align: justify;"><?php echo $cDesc_sb; ?></p>
						<a class="btn btn-primary" style="background:<?php echo htmlentities($QrySelectget['form_textcolor']); ?>;"  href="<?php echo $defaultwebPath; ?>/showcase/<?php echo $csTitle.$concat_request_url; ?>" <?php if($c_lient_Id != 'SP108098'){?> target="_blank" <?php } ?>>Read more</a>
					</div>
				</div>
			</div>
			<?php
		}
	}
