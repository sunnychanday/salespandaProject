<form id="frm" action="" method="post" name="frm" class="a">
<input type="hidden" name="solution" id="solution" value="<?php echo $solution; ?>" />
<input type="hidden" name="industry" id="industry" value="<?php echo $industry; ?>" />
<input type="hidden" name="product" id="product" value="<?php echo $product; ?>" />
<input type="hidden" name="content" id="content" value="<?php echo $content; ?>" />
<input type="hidden" name="member_pc_type" id="member_pc_type" value="<?php echo $pcmember_pc_type; ?>" />
<input type="hidden" name="p_client_id" value="<?php echo $p_client_id; ?>" />
<?php error_reporting(0);?>
<!--===============================--DOCUMENT TYPE SECTION START--==============================-->


<!--<p class="b grayTxt"><?php if($solution!='' && $content=='' && $docTypeLevel!=''){ echo $docTypeLevel; }  ?></p>

<p class="b grayTxt"><?php if($solution=='' && $content!='' && $solutionLabel!=''){ echo $solutionLabel; } ?></p>-->


<div class="">
<?php 
	if ($solution!='')
	{

            if($pcmember_pc_type=='C'){
                  $cjkcat = "select DISTINCT(CS.content_type) from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".$clientId."' and CS.valid=1 and CS.deleted=0 and TS.approve=1 and FIND_IN_SET('".$solution."',CS.category)";
            }
            else{
	          $cjkcat = "select DISTINCT(content_type) from sp_case_study where valid=1 and deleted=0 and approve=1 and FIND_IN_SET('".$solution."',category) and (client_id='".$clientId."' or client_id='SP_INTERAL')";
            }  
		$reschk = mysql_query($cjkcat) or die(mysql_error());
		while($catCountcst=mysql_fetch_array($reschk))
		{
			if($catCountcst['content_type']!='')
			{
				$csTypeId.= $catCountcst['content_type'].',';
			}
		}
		$casestudytype=substr($csTypeId,0,-1);

                if($pcmember_pc_type=='C'){
                   $dtquery="select * from sp_article_type where valid=1 and deleted=0 and id IN(".$casestudytype.") and client_id='".$p_client_id."' ";
                }
                else{  
		   $dtquery="select * from sp_article_type where valid=1 and deleted=0 and id IN(".$casestudytype.") and (client_id='".$clientId."' or client_id='SP_INTERAL')";
                } 
		$dtype_query=mysql_query($dtquery) or die(mysql_error());
		
?>

<?php

		$atypeCount=mysql_num_rows($dtype_query);
	 	if($atypeCount!=0 or $atypeCount!='')
	 	{
	 		$count1=1;
	 		while($dtype_row=mysql_fetch_array($dtype_query))
			{
				$article_name=$dtype_row["article_type"];
				$articleId=$dtype_row["id"];
?>
				<!--<p><input type="checkbox" name="article_name[]" id="article_name<?php echo $dtype_row["id"];?>" value="<?php echo $dtype_row["id"];?> " onclick=" return srch()" />
				<span style="cursor:pointer;" title="<?php echo $article_name; ?>" ><?php echo $article_name; //shorten_string($article_name,2);?></span></p>-->
				
<?php
		  }
			
		}
?> 
	
</div>
<!--========================--DOCUMENT TYPE SECTION END--================================-->
<!--========================--INDUSTRY SECTION START--====================================-->
<?php
//check content type in case syudy table
        if($pcmember_pc_type=='C'){
			$ckvert = "select CS.*,TS.id as synid from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".$clientId."' and CS.vertical!='' and CS.valid=1 and CS.deleted=0 and TS.approve=1 and FIND_IN_SET('".$solution."',CS.category)";
		}
		else{	
			$ckvert = "select * from sp_case_study where client_id='".$clientId."' and vertical!='' and valid=1 and deleted=0 and approve=1 and FIND_IN_SET('".$solution."',category)";
		}
		$resvert = mysql_query($ckvert) or die(mysql_error());
		$catvertCount = mysql_num_rows($resvert);
		if($catvertCount!=0)
		{
			while($vertData=mysql_fetch_array($resvert))
			{
				$vertId.= $vertData['vertical'].',';
			}
			$casestudyvertical=substr($vertId,0,-1);
			$vertical_query=mysql_query($v="select * from sp_verticals where valid=1 and deleted=0 and vid IN(".$casestudyvertical.")");  
?>
			<p class="b grayTxt"><?php if($industryLabel!=''){ echo $industryLabel; } else { echo "Industry";} ?></p>
<div class="scrollV">
<?php 
			
	 		if($count_segment=mysql_num_rows($vertical_query)!=0)
	 		{
				$count2=1;
				while($vertical_row=mysql_fetch_array($vertical_query))
				{
					$verticalName=$vertical_row["vertical_name"];
?>
     				<p><input type="checkbox" name="vertical_name[]" id="cvertical_<?php echo $vertical_row["vid"];?>" value="<?php echo $vertical_row["vid"];?> " onClick=" return srch()" <?php if($vertical_row["vid"]==$v || $vertical_row["vid"]==$industry){?> checked="checked"<?php }?> />
     				<span style="cursor:pointer;" title="<?php echo $verticalName; ?>" ><?php echo $verticalName; ?></span></p>
    
     				
	<?php
   			 	
				}

				if($pcmember_pc_type=='C'){
					$usvert = "select CS.*,TS.id as synid from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".$clientId."' and CS.vertical='' and CS.valid=1 and CS.deleted=0 and TS.approve=1 and FIND_IN_SET('".$solution."',CS.category)";
				}
				else{
					$usvert = "select * from sp_case_study where client_id='".$clientId."' and vertical='' and valid=1 and deleted=0 and approve=1 and FIND_IN_SET('".$solution."',category)";
				}
				$resus = mysql_query($usvert) or die(mysql_error());
				$nonvertCount=mysql_num_rows($resus);
				if($nonvertCount!=0)
				{
?>
					<p><input type="checkbox" name="crossvertical" id="crossvertical" value="crossvertical" onClick=" return srchcross()" />
     				<span style="cursor:pointer;" title="" >Cross-Industry</span></p>
<?php 	
				}
?> 
				
 				</div>
<?php }}}?>

<!--======================--INDUSTRY SECTION END--====================================-->

<!--======================--CONTENT SECTION START--====================================-->

<?php 
		
	if($content!='')
	{
	
	 	$cjkcnt = "select id, category from sp_case_study where client_id='".$clientId."' and valid=1 and deleted=0 and approve=1 and FIND_IN_SET('".$content."',content_type)";
		$rescat = mysql_query($cjkcnt) or die(mysql_error());
		while($catCountcst=mysql_fetch_array($rescat))
		{
			if($catCountcst['category']!='')
			{
				$csTypeId.= $catCountcst['category'].',';
			}
		}
		$casestudycatg=substr($csTypeId,0,-1);
?>
	
<?php	
		  
		$dtype_query=mysql_query($cst="select * from sp_category where valid=1 and deleted=0 and id IN(".$casestudycatg.")");  
		$atypeCount=mysql_num_rows($dtype_query);
		if($atypeCount!=0 or $atypeCount!='')
	 	{
?>

<?php
	 		$count1=1;
	 		while($dtype_row=mysql_fetch_array($dtype_query))
			{
				$category_name=$dtype_row["it_type"];
				$catId=$dtype_row["id"];
?>
			<!--<p><input type="checkbox" name="category_name[]" id="category_name<?php echo $dtype_row["id"];?>" value="<?php echo $dtype_row["id"];?> " onclick=" return srch()" />
			<span style="cursor:pointer;" title="<?php echo $category_name; ?>" ><?php echo $category_name; //shorten_string($category_name,2);?></span></p>-->
					
<?php
		 	
			}
		
		}
?> 
		
</div>
<!--========================--DOCUMENT TYPE SECTION END--================================-->
<!--========================--INDUSTRY SECTION START--====================================-->
<?php
//check content type in case syudy table

        if($pcmember_pc_type=='C'){
			$ckvert = "select CS.*,TS.id as synid from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".$clientId."' and CS.vertical!='' and CS.valid=1 and CS.deleted=0 and TS.approve=1 and FIND_IN_SET('".$content."',CS.content_type)";
		}
		else{
			$ckvert = "select * from sp_case_study where client_id='".$clientId."' and vertical!='' and valid=1 and deleted=0 and approve=1 and FIND_IN_SET('".$content."',content_type)";
		}
		
		$resvert = mysql_query($ckvert) or die(mysql_error());
		$catvertCount = mysql_num_rows($resvert);
		if($catvertCount!=0)
		{
			while($vertData=mysql_fetch_array($resvert))
			{
				$vertId.= $vertData['vertical'].',';
			}
			$casestudyvertical=substr($vertId,0,-1);
			$vertical_query=mysql_query($v="select * from sp_verticals where valid=1 and deleted=0 and vid IN(".$casestudyvertical.")");  
?>
			<p class="b grayTxt"><?php if($industryLabel!=''){ echo $industryLabel; } else { echo "Industry";} ?></p>
<div class="scrollV">
<?php 
			
	 		if($count_segment=mysql_num_rows($vertical_query)!=0)
	 		{
				$count2=1;
				while($vertical_row=mysql_fetch_array($vertical_query))
				{
					$verticalName=$vertical_row["vertical_name"];
?>
     				<p><input type="checkbox" name="vertical_name[]" id="cvertical_<?php echo $vertical_row["vid"];?>" value="<?php echo $vertical_row["vid"];?> " onClick=" return srch()" <?php if($vertical_row["vid"]==$v || $vertical_row["vid"]==$industry){?> checked="checked"<?php }?> />
     				<span style="cursor:pointer;" title="<?php echo $verticalName; ?>" ><?php echo $verticalName;// shorten_string($verticalName,2);?></span></p>
     
     				
	<?php
   			 	
				}

	//for unselected vertical of same category   CROSS INDUSTRY
				$usvert="select * from sp_case_study where client_id='".$clientId."' and vertical='' and valid=1 and deleted=0 and approve=1 and FIND_IN_SET('".$solution."',category)";
				$resus = mysql_query($usvert) or die(mysql_error());
				$nonvertCount=mysql_num_rows($resus);
				if($nonvertCount!=0)
				{
?>
					<p><input type="checkbox" name="crossvertical" id="crossvertical" value="crossvertical" onClick=" return srchcross()" />
     				<span style="cursor:pointer;" title="" >Cross-Industry</span></p>
<?php 	
				}
?> 
				
 				</div>
<?php }}}?>

<!--========================--CONTENT SECTION END--====================================-->

</form>