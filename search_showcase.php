<?php 
	include("includes/global.php");
	include("includes/check_login2.php");
	include("includes/connect.php");
	include("includes/function.php");
	include("includes/global-url.php");
	
	error_reporting(0);
	$flag_val=1;
	ob_start();
	
	if($c_lient_Id!=SP108098)
	 {
	 	$tblank="target='_blank'";
	 }

         if($_REQUEST['member_pc_type']!='')											
	 {
            $pcmember_pc_type = $_REQUEST['member_pc_type'];
         }

         if($_REQUEST['p_client_id']!='')											
	 {
            $p_client_id = $_REQUEST['p_client_id'];
         } 

	if($_REQUEST['solution']!='')											// get Category value from Browse
	{	
		$solution = $_REQUEST['solution'];	
		$solutionQry= "AND FIND_IN_SET('".trim($solution)."',category) ";
		$verticalQry='';
	}	
	
	if($_REQUEST['content']!='')											// get Category value from Browse
	{	
		$content = $_REQUEST['content'];	
		$solutionQry= "AND FIND_IN_SET('".trim($content)."',content_type) ";
		$contentQry='';
	}
	
	if($_REQUEST['industry']!='')											// get Vertical value from Browse
	{ 	
		$industry = $_REQUEST['industry'];	
		$verticalQry= "AND FIND_IN_SET('".trim($industry)."',vertical) ";
		$solutionQry='';
	}		

	$crossvertical = $_REQUEST['crossvertical'];							//get crossvertical flag
	if($crossvertical=='crossvertical')
	{
		$crossQuery="AND vertical=''";
	}
	else
	{
		$crossQuery='';
	}
	///////////////////////////////////////////////////////////////////// DOCUMENT START
	if(isset($_REQUEST['article_name'])) 										
	{	
		$docType=$_REQUEST['article_name'];	
	} 																			
	foreach($docType as $docTypeVal) 											
	{
		$dTypeId.=$docTypeVal.",";
	}
	$docTypeId = substr($dTypeId, 0, -1);
	
	if($_REQUEST['article_name']!='')
	{
		 $docSearchQry.="AND content_type IN ($docTypeId)";						//get document type checkbox value
	}
	else
	{
		 $docSearchQry='';
	}
	/////////////////////////////////////////////////////////////////////	VERTICAL START
	if(isset($_REQUEST['vertical_name']))  										
	{
		$verticalname=$_REQUEST['vertical_name'];
	}
	foreach($verticalname as $verticalnameVal)									
	{
		$vertId.=$verticalnameVal.",";
	}
	$verticleId = trim(substr($vertId, 0, -1));
	
	$verticalarray=explode(",",$verticleId);
	foreach($verticalarray as $val)
	{
		$str .="OR FIND_IN_SET('".trim($val)."',vertical) ";
        $trimmed = ltrim($str, "OR"); 
	}
	
	if($_REQUEST['vertical_name']!='')
	{
	 	$vertDocQry.=" AND (".$trimmed.")";					//get vertical check box value
	}
	else
	{
	 	$vertDocQry='';
	}
	/////////////////////////////////////////////////////////////////////	CATEGORY START
	if(isset($_REQUEST['category_name'])) 									
	{
		$categoryId=$_REQUEST['category_name'];
	}
	foreach($categoryId as $catVal) 
	{
		$catId.=$catVal.",";
	}
	$category_Id = trim(substr($catId, 0, -1));
	$catarray=explode(",",$category_Id);
	foreach($catarray as $val)
	{
		$catstr.="OR FIND_IN_SET('".trim($val)."',category) ";
        $trimmed = ltrim($catstr, "OR"); 
	}
	if($_REQUEST['category_name']!='')
	{
		$catDocQry.=" AND (".$trimmed.")";	
	}
	else
	{
		$catDocQry='';
	}
	////////////////////////////////////////////////////////////////	QUERY SECTION
		
	if($docType!='' or $verticalname!='' or $categoryId!='' or $content!='')
	{
             if($pcmember_pc_type=='C'){
                 $showcase_query = "select CS.*,TS.id as synid from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".$c_lient_Id."' and CS.valid=1 and CS.deleted=0 and TS.approve=1 $docSearchQry $vertDocQry $catDocQry $solutionQry $verticalQry $crossQuery $solutionQry order by CS.id desc";
                 
                 $showcase_query2="select id,member_id,content_type,case_study,image_thumb1,crop_Image,case_study_title,case_study_actual_title,case_study_desc,landingpage_status,landingpage_id from sp_child_case_study where valid=1 and deleted=0 and approve=1 and client_id='".$c_lient_Id."' $docSearchQry $vertDocQry $catDocQry $solutionQry $verticalQry $crossQuery $solutionQry order by id desc";
             }   
             else{ 
		$showcase_query="select id,member_id,content_type,case_study,image_thumb1,crop_Image,case_study_title,case_study_actual_title,doc_mode,case_study_desc,landingpage_status,landingpage_id from sp_case_study where valid=1 and deleted=0 and approve=1 and client_id='".$c_lient_Id."' $docSearchQry $vertDocQry $catDocQry $solutionQry $verticalQry $crossQuery $solutionQry order by id desc";
            } 
	}
	else if($solution!='' and $industry=='' and $docType=='' and $verticalname=='' and $categoryId=='')
	{
                if($pcmember_pc_type=='C'){
                    $showcase_query = "select CS.*,TS.id as synid from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".$c_lient_Id."' and CS.valid=1 and CS.deleted=0 and TS.approve=1 $crossQuery and FIND_IN_SET('".$solution."',CS.category) order by CS.id desc";
                    
                    $showcase_query2="select id,member_id,content_type,case_study,image_thumb1,crop_Image,case_study_title,case_study_actual_title,case_study_desc,landingpage_status,landingpage_id from sp_child_case_study where valid=1 and deleted=0 and approve=1 and client_id='".$c_lient_Id."' $crossQuery and FIND_IN_SET('".$solution."',category) order by id desc";
                } 
                else{
		$showcase_query="select id,member_id,content_type,case_study,image_thumb1,crop_Image,case_study_title,case_study_actual_title,doc_mode,case_study_desc,landingpage_status,landingpage_id from sp_case_study where valid=1 and deleted=0 and approve=1 and client_id='".$c_lient_Id."' $crossQuery and FIND_IN_SET('".$solution."',category) order by id desc";
               }
	}
	else if($solution=='' and $industry!='' and $docType=='' and $verticalname=='' and $categoryId=='')
	{
                if($pcmember_pc_type=='C'){
                    $showcase_query = "select CS.*,TS.id as synid from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".$c_lient_Id."' and CS.valid=1 and CS.deleted=0 and TS.approve=1 $crossQuery and FIND_IN_SET('".$industry."',CS.vertical) order by CS.id desc";
                    
                    $showcase_query2="select id,member_id,content_type,case_study,image_thumb1,crop_Image,case_study_title,case_study_actual_title,case_study_desc,landingpage_status,landingpage_id from sp_child_case_study where valid=1 and deleted=0 and approve=1 and client_id='".$c_lient_Id."' $crossQuery and FIND_IN_SET('".$industry."',vertical) order by id desc";
                }
                else{
		$showcase_query="select id,member_id,content_type,case_study,image_thumb1,crop_Image,case_study_title,case_study_actual_title,doc_mode,case_study_desc,landingpage_status,landingpage_id from sp_case_study where valid=1 and deleted=0 and approve=1 and client_id='".$c_lient_Id."' $crossQuery and FIND_IN_SET('".$industry."',vertical) order by id desc";
                } 
	}
	
	$showcase_search=mysql_query($showcase_query)or die("Error in show case search query.".mysql_error());
	$countcs=mysql_num_rows($showcase_search);
	
	$showcase_search2=mysql_query($showcase_query2);
	$countcs2=mysql_num_rows($showcase_search2);
	
	if($countcs!=0 || $countcs2!=0)
 	{
 	    
 	    
 	    if($pcmember_pc_type=='C'){
 	        
 	        
 	            while($row=mysql_fetch_array($showcase_search2))
        		{
        			$case_study_id=$row["id"];
        			$casestudyMember=$row["member_id"];
        			$content_type=$row["content_type"];
        			$case_study=$row["case_study"];
        			$thumbimage = $row['image_thumb1'];
        			$crop_image=$row['crop_Image'];
        			$caseStudyTitle11=$row["case_study_title"];
        			$caseStudyActualTitle = ($row['case_study_actual_title']!='') ? $row['case_study_actual_title'] : $row['case_study_title'];
        			
        			$documentMode=$row['doc_mode'];
                    $caseLandStatus=$row['landingpage_status']; 
        			$caseLandId=$row['landingpage_id'];
        
        			$caseStudyTitleLength=strlen($caseStudyActualTitle);
        			if($caseStudyTitleLength > 35)
        			{
        				$case_study_title = substr($caseStudyActualTitle, '0', '35')."...";
        			}
        			else
        			{
        				$case_study_title=$caseStudyActualTitle;
        			}
        			$csdescription12 = $row['case_study_desc'];
        			$csdescription = substr("$csdescription12", '0', '260')."...";
        			
        			$castStudyType =getarticleName($content_type);
        				
        			
        			
        			//htaccess title convert
        			$csname =str_replace(' ', '-', $caseStudyTitle11);
        			//end
        			
        			if($caseLandId!=0){
        			    $child_edit_status = getChildSyndLpageEditStatus($c_lient_Id,$caseLandId);
        			}
        
                    if($pcmember_pc_type=='C'){
                        
                        if($child_edit_status==1){
                            $cslandquery=mysql_query("select publish_page_id,landingpage_title,publish_page_name,landingpage_desc,page_title_seo,meta_description,approve from sp_child_landingpage_publish where publish_page_id='".$caseLandId."' and client_id='".$c_lient_Id."' ");
                        }
                        else{
        				    $cslandquery=mysql_query("select LS.*, LP.publish_page_id,LP.landingpage_title,LP.publish_page_name,LP.landingpage_desc,LP.page_title_seo,LP.meta_description from sp_landingpage_publish as LP INNER JOIN sp_landingpage_syndication as LS ON LP.publish_page_id = LS.landingpage_id where LP.publish_page_id='".$caseLandId."' and LP.client_id='".$p_client_id."' ");
                        }    
        			}
        			else{
        				$cslandquery=mysql_query("select publish_page_id,landingpage_title,publish_page_name,landingpage_desc,page_title_seo,meta_description,approve from sp_landingpage_publish where publish_page_id='".$caseLandId."' and client_id='".$c_lient_Id."' ");
        			}
        
                    $cslandget=mysql_fetch_array($cslandquery);
                    $cslandname=$cslandget['publish_page_name'];
                    $cslandApprove= $cslandget['approve'];
        			
        		?>
                <div class="item">
                	<?php if($thumbimage!='' && $crop_image==''){?>
        			<img class="thumb" src="<?php echo $sitepath; ?>webcontent/manager/uploads/thumb_img/<?php echo $thumbimage; ?>" alt="<?php echo $caseStudyTitle11; ?>" />
        			<?php }  else if($thumbimage=='' && $crop_image!='') {?>
                         
                      <?php if($caseLandStatus==1 && $cslandApprove==1) {?> 
                          <a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>" target="_blank"><img id='base64image' src='data:image/png;base64,<?php echo $crop_image; ?>' alt="<?php echo $caseStudyTitle11; ?>" /></a>
                      <?php }else{ ?>
                          <a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>"><img id='base64image' src='data:image/png;base64,<?php echo $crop_image; ?>' alt="<?php echo $caseStudyTitle11; ?>" /></a>
                      <?php } ?>  
                     
                    <?php } else if($thumbimage!='' && $crop_image!='') {?>
                         
                      <?php if($caseLandStatus==1 && $cslandApprove==1) {?> 
                          <a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>" target="_blank"><img class="thumb" src="<?php echo $sitepath; ?>webcontent/manager/uploads/thumb_img/<?php echo $thumbimage; ?>" alt="<?php echo $caseStudyTitle11; ?>" /></a>
                      <?php }else{ ?>
                          <a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>"><img class="thumb" src="<?php echo $sitepath; ?>webcontent/manager/uploads/thumb_img/<?php echo $thumbimage; ?>" alt="<?php echo $caseStudyTitle11; ?>" /></a>
                      <?php } ?>  
                     
                    <?php } else { ?>
        			<img class="thumb" src="<?php echo $sitepath; ?>upload/casestudy/nopic/nopic.jpg" alt="<?php echo $caseStudyTitle11; ?>" />
        			<?php } ?>
                    <div class="itemSlideTxt">
                        <div class="itemH3">
                       <?php if($caseLandStatus==1 && $cslandApprove==1) {?> 
                          <a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>" target="_blank"><?php echo ucfirst($case_study_title); ?></a>
                       <?php } else{?> 
                          <a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>"><?php echo ucfirst($case_study_title); ?></a>
                       <?php } ?>
                       &nbsp; 
                      </div>
                        <div class="itemH4"><?php echo $castStudyType; ?>&nbsp;</div>
                        <p><?php echo $csdescription; ?></p>
                        <div class="alignC itemSlideBtn">
                         <?php if($caseLandStatus==1 && $cslandApprove==1) {?>  
                       
                             <a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>" target="_blank" class="btn whiteTxt padL padR normal" <?php echo $tblank; ?>>Read more &raquo;</a>
                         
                          <?php }  else { ?>
                        
                           <a class="btn whiteTxt padL padR normal" href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>" <?php echo $tblank; ?>>Read more &raquo;</a>
                          <?php } ?>
                          		
                        </div>
                    </div>
                </div>
             <?php   
        	}
 	        
 	    }
 	    
 	    
 	    
		while($row=mysql_fetch_array($showcase_search))
		{
			$case_study_id=$row["id"];
			$casestudyMember=$row["member_id"];
			$content_type=$row["content_type"];
			$case_study=$row["case_study"];
			$thumbimage = $row['image_thumb1'];
			$crop_image=$row['crop_Image'];
			$caseStudyTitle11=$row["case_study_title"];
			$caseStudyActualTitle = ($row['case_study_actual_title']!='') ? $row['case_study_actual_title'] : $row['case_study_title'];
			
			$documentMode=$row['doc_mode'];
            $caseLandStatus=$row['landingpage_status']; 
			$caseLandId=$row['landingpage_id'];

			$caseStudyTitleLength=strlen($caseStudyActualTitle);
			if($caseStudyTitleLength > 35)
			{
				$case_study_title = substr($caseStudyActualTitle, '0', '35')."...";
			}
			else
			{
				$case_study_title=$caseStudyActualTitle;
			}
			$csdescription12 = $row['case_study_desc'];
			$csdescription = substr("$csdescription12", '0', '260')."...";
			
			$castStudyType =getarticleName($content_type);
				
			
			
			//htaccess title convert
			$csname =str_replace(' ', '-', $caseStudyTitle11);
			//end

                        if($pcmember_pc_type=='C'){
				$cslandquery=mysql_query("select LS.*, LP.publish_page_id,LP.landingpage_title,LP.publish_page_name,LP.landingpage_desc,LP.page_title_seo,LP.meta_description from sp_landingpage_publish as LP INNER JOIN sp_landingpage_syndication as LS ON LP.publish_page_id = LS.landingpage_id where LP.publish_page_id='".$caseLandId."' and LP.client_id='".$p_client_id."' ");
			}
			else{
				$cslandquery=mysql_query("select publish_page_id,landingpage_title,publish_page_name,landingpage_desc,page_title_seo,meta_description,approve from sp_landingpage_publish
			where publish_page_id='".$caseLandId."' and client_id='".$c_lient_Id."' ");
			}

            $cslandget=mysql_fetch_array($cslandquery);
            $cslandname=$cslandget['publish_page_name'];
            $cslandApprove= $cslandget['approve'];
			
		?>
        <div class="item">
        	<?php if($thumbimage!='' && $crop_image==''){?>
			<img class="thumb" src="<?php echo $sitepath; ?>webcontent/manager/uploads/thumb_img/<?php echo $thumbimage; ?>" alt="<?php echo $caseStudyTitle11; ?>" />
			<?php }  else if($thumbimage=='' && $crop_image!='') {?>
                 
              <?php if($caseLandStatus==1 && $cslandApprove==1) {?> 
                  <a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>" target="_blank"><img id='base64image' src='data:image/png;base64,<?php echo $crop_image; ?>' alt="<?php echo $caseStudyTitle11; ?>" /></a>
              <?php }else{ ?>
                  <a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>"><img id='base64image' src='data:image/png;base64,<?php echo $crop_image; ?>' alt="<?php echo $caseStudyTitle11; ?>" /></a>
              <?php } ?>  
             
            <?php } else if($thumbimage!='' && $crop_image!='') {?>
                 
              <?php if($caseLandStatus==1 && $cslandApprove==1) {?> 
                  <a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>" target="_blank"><img class="thumb" src="<?php echo $sitepath; ?>webcontent/manager/uploads/thumb_img/<?php echo $thumbimage; ?>" alt="<?php echo $caseStudyTitle11; ?>" /></a>
              <?php }else{ ?>
                  <a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>"><img class="thumb" src="<?php echo $sitepath; ?>webcontent/manager/uploads/thumb_img/<?php echo $thumbimage; ?>" alt="<?php echo $caseStudyTitle11; ?>" /></a>
              <?php } ?>  
             
            <?php } else { ?>
			<img class="thumb" src="<?php echo $sitepath; ?>upload/casestudy/nopic/nopic.jpg" alt="<?php echo $caseStudyTitle11; ?>" />
			<?php } ?>
            <div class="itemSlideTxt">
                <div class="itemH3">
               <?php if($caseLandStatus==1 && $cslandApprove==1) {?> 
                  <a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>" target="_blank"><?php echo ucfirst($case_study_title); ?></a>
               <?php } else{?> 
                  <a href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>"><?php echo ucfirst($case_study_title); ?></a>
               <?php } ?>
               &nbsp; 
              </div>
                <div class="itemH4"><?php echo $castStudyType; ?>&nbsp;</div>
                <p><?php echo $csdescription; ?></p>
                <div class="alignC itemSlideBtn">
                 <?php if($caseLandStatus==1 && $cslandApprove==1) {?>  
               
                     <a href="<?php echo $sdomainCmsPath; ?>/landingpage/<?php echo $cslandname; ?>" target="_blank" class="btn whiteTxt padL padR normal" <?php echo $tblank; ?>>Read more &raquo;</a>
                 
                  <?php }  else { ?>
                
                   <a class="btn whiteTxt padL padR normal" href="<?php echo $sdomainCmsPath; ?>/showcase/<?php echo $csname; ?>" <?php echo $tblank; ?>>Read more &raquo;</a>
                  <?php } ?>
                  		
                </div>
            </div>
        </div>
     <?php   
	}
 }
else
	{
	?>
    <p class="bold orangeTxt">No data Found.</p>
    <?php } ?>