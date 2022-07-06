<?php
	

	if($caseId!='')
	{
              if($pcmember_pc_type=='C')
              {
		   $qry = "select CS.content_type, CS.category, CS.vertical, TS.id as syndid, TS.p_client_id, TS.c_client_id, TS.submem_content_publish_url from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where CS.id='".$caseId."' and TS.c_client_id='".$c_lient_Id."' and CS.approve!='3' and CS.valid=1 and CS.deleted=0 and TS.approve=1";
               }
               else
               {
		    $qry="select content_type,category,vertical from sp_case_study where id='".$caseId."' and client_id='".$c_lient_Id."' and valid=1 and approve=1 and deleted=0";
               }  

		$resq=mysqli_query($conn, $qry);
		$docData=mysqli_fetch_array($resq);
		$csType = $docData['content_type'];
		$solutionct=$docData["category"];
		$industry=$docData["vertical"];
	}

	
	if($solutionct!='')	
	{
		
                    if($pcmember_pc_type=='C')
                    {
                       $sideqry = "select CS.*, TS.id as syndid, TS.p_client_id, TS.c_client_id, TS.submem_content_publish_url from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where CS.id!='".$caseId."' and TS.c_client_id='".$c_lient_Id."' and CS.approve!='3' and CS.valid=1 and CS.deleted=0 and TS.approve=1 and (CS.category IN($solutionct) or FIND_IN_SET('".$solutionct."',CS.category)) order by RAND() limit 3";
                    }
                    else
                    {
			           $sideqry = "select id,case_study_title,video_image,case_study_desc,image_thumb1,crop_Image,content_type from sp_case_study where id!='$caseId' and client_id='".$c_lient_Id."' and approve=1 and valid=1 and deleted=0 and (category IN($solutionct) or FIND_IN_SET('".$solutionct."',category)) order by RAND() limit 3";
                    }
		
		
	}
	

	$rightres = mysqli_query($conn, $sideqry);
	$count = mysqli_num_rows($rightres);

  while($rightData = mysqli_fetch_array($rightres))
  {
		$csid_sb = $rightData['id'];
		$csTitle = $rightData['case_study_title'];
		$caseStudyTitleLength=strlen($csTitle);
		if($caseStudyTitleLength > 35)
		{
			$caseStudyTitle = substr("$csTitle", '0', '35')."..";
		}
		else
		{
			$caseStudyTitle=$csTitle;
		}
		
		$csname =str_replace(' ', '-', $csTitle);
		
	
		$csDesc = $rightData['case_study_desc'];
		$cDesc_sb = substr("$csDesc", '0', '160')."...";
	
	      if($rightData['video_image']!='')
			{
			  $showcaseThumb=$rightData['video_image'];  
			}
			else
			{
			  $showcaseThumb=$weburl.'/manager/uploads/thumb_img/'.$rightData['image_thumb1'];  
			}
		
		$contentType=$rightData['content_type'];
		if($contentType!='')
		{
			$contentTypeName=getarticleName($contentType);
		}
?>
<div class="col-md-12">
    <div class="row">
        
<?php if($csid_sb!=''){ ?>
	
    <img class="card-img-top" width="230" height="230" src="<?php echo $showcaseThumb; ?>" id="<?php echo $csid_sb; ?>" alt="<?php echo $csTitle; ?>"  />
    
       <div class="card-body">
        <h5 class="card-title"><?php echo ucfirst($caseStudyTitle);?></h5>
        <p class="card-subtitle mb-2 text-muted"><?php echo $contentTypeName; ?></p>
        <p class="card-text" style="line-height: 1.5;font-size: 14px;text-align: justify;"><?php echo $cDesc_sb; ?></p>
		<a class="btn btn-primary" href="<?php echo $defaultwebPath; ?>/showcase/<?php echo $csname; ?>" <?php if($c_lient_Id!=SP108098){?> target="_blank" <?php } ?>>Read more</a>
        </div>
           
         
    </div>
</div>
<?php } }?>	