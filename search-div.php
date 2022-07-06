<?php
include_once("includes/global.php");
include_once("includes/connect.php");
//include_once("header_function.php");
?>
<link rel="stylesheet" type="text/css" href="<?php echo $sitepath; ?>jquery/jquery.autocomplete.css" />
<script type='text/javascript' src='<?php echo $sitepath; ?>jquery/jquery.autocomplete.js'></script>


<script type="text/javascript">
$().ready(function() {
	$("#search_txt1").autocomplete("<?php echo $sitepath; ?>includes/get_document_title_list.php", {
		width: '41%',
		matchContains: true,
		selectFirst: false
	});
})

function goforsearch()
{
	var sss=$("#search_txt1").val();
	var sitepath=$("#sitepath").val();
	//window.location.href = ''+sitepath+'showcase.php?searchby='+sss+'';
	window.location.href = ''+sitepath+'Search-content/'+sss+'';

}


function showIndustry()
{
	$("#industrydiv").css('display','');
	$("#solutiondiv").css('display','none');
	$("#productdiv").css('display','none');
	$("#contentdiv").css('display','none');
}
function showProduct()
{
	$("#industrydiv").css('display','none');
	$("#solutiondiv").css('display','none');
	$("#productdiv").css('display','');
	$("#contentdiv").css('display','none');
}

function showContent()
{
	$("#industrydiv").css('display','none');
	$("#solutiondiv").css('display','none');
	$("#productdiv").css('display','none');
	$("#contentdiv").css('display','');
}

function showSolution()
{
	$("#industrydiv").css('display','none');
	$("#solutiondiv").css('display','');
	$("#productdiv").css('display','none');
	$("#contentdiv").css('display','none');
}
</script>


<input type="hidden" name="search_by" id="search_by" value="" />   
<input type="hidden" name="searchKey" id="searchKey" value=""  /> 
<input type="hidden" name="sitepath" id="sitepath" value="<?php echo $sitepath;?>" /> 
<input type="hidden" name="userid" id="userid" value="<?php echo $userid;?>" /> 
 
<div class="main-search alignC padB">
<ul class="boxshadow search-area radiusA" id="search_area">
<li class="posRel">
 <?php
 $requri=$_SERVER['REQUEST_URI'];

	if (strpos($requri,'casestudy') == false && strpos($requri,'leads') == false )
				 {
					 ?>
<div id="sitenav-menu">
<p class="label">Filter by Solution <span class="bold font80">&#9660;</span></p>
               <ul class="dropdown" id="sitenav">
               
               <li class="whiteTxt"><input type="radio" name="browse" id="solution" value="" checked="checked" onclick="showSolution()" /> Solution
                    <div id="solutiondiv" class="multichkbox boxshadow" style="width:98%; border-top:1px solid #434343; border-bottom:1px solid #434343;">
                  <?php
				  	//$chkcontcat = "select * from sp_case_study where valid=1 and deleted=0 and approve=1 and content_type!='' and subcategory!='' and category!=''";
					$chkcontcat = "select * from sp_case_study where valid=1 and deleted=0 and approve=1 and content_type!=''";
					$rescntco = mysql_query($chkcontcat) or die(mysql_error());	  
					while($advData = mysql_fetch_array($rescntco))
					{
						if($advData["category"]!='')
						{
							$categId.=$advData["category"].',';
						}
						
						if($advData["subcategory"]!='')
						{
							$scatcategId.=$advData["subcategory"].',';
						}
					}
					$catAdv=substr($categId,0,-1);
					$subcatAdv=substr($scatcategId,0,-1);
				  
				  	$categ_array=explode(',',$catAdv);
					//echo "<pre>";
					$ccc=array_unique($categ_array);
					//echo "</pre>";
					foreach($ccc as $key=> $val)
					{
						if( $val=='')
						{
					  		unset($ccc[$key]);
					  	}
					}
					$subcateg_array=explode(',',$subcatAdv);
					//echo "<pre>";
					$sssub=array_unique($subcateg_array);
					//echo "</pre>";
					foreach($sssub as $keys=> $val)
					{
						if( $val=='')
						{
					  		unset($sssub[$keys]);
					  	}
					}
				
			 		$unique_subcategory=implode(',',array_unique($sssub));
				
			 		$unique_category=implode(',',array_unique($ccc));
					
				
			//	$solutionQ=mysql_query($aa="SELECT * FROM sp_category WHERE valid=1 AND deleted=0 and id IN(".$unique_category.") ORDER BY it_type") or die("error in category Table".mysql_error());
			//	$solutionQ=mysql_query($aa="select id,it_type from sp_category where valid=1 and deleted=0 and id IN( select DISTINCT(it_type_id) from sp_subcategory where valid=1 and deleted=0 and it_type_id IN(".$unique_category.") ) order by it_type ") or die("error in category Table".mysql_error());
			
			$solutionQ=mysql_query($aa="select id,it_type from sp_category where valid=1 and deleted=0 order by it_type ") or die("error in category Table".mysql_error());	
			//	$getcateg = "select id,it_type from sp_category where valid=1 and deleted=0 and id IN( select DISTINCT(it_type_id) from sp_subcategory where valid=1 and deleted=0 and it_type_id IN(".$unique_category.") ) order by it_type ";	
				
				
				
				
				//echo	$aa;
				while($catrow=mysql_fetch_array($solutionQ))
				{
					$catid=$catrow["id"];
					$catname=$catrow["it_type"];
					
					echo "<div class='clear padA5 txtShadowB whiteTxt' style='border-top: 1px dashed #565555; background-color:#5b5e5f;'>".categoryName($catid).":</div>";
					
						
				//	$getcat = "select id, it_subcat from sp_subcategory where valid=1 and deleted=0 and it_type_id='".$catid."' and it_type_id!='' and id IN(".$unique_subcategory.") order by it_subcat ";	
					$resct = mysql_query($getcat) or die(mysql_error());		
					while($catcmp = mysql_fetch_array($resct))
					{		
				
						$scatgcId = $catcmp['id'];	
						$scatcName = getsubcatName($scatgcId);
						$ctName =str_replace(' ', '-', $scatcName);
					
					?>
                    <a href="<?php echo $sitepath;?>content/<?php echo $ctName;?>" class="block">&bull; <?php echo $scatcName;?></a>
                    
                   
                    <?php  
				}
			}
				  ?>
                     </div>
                   </li>
                   
               <li class="whiteTxt"><input type="radio" name="browse" id="industry" value="" onclick="showIndustry()" /> Industry
                    <div id="industrydiv" class="multichkbox boxshadow" style="width:98%; border-top:1px solid #434343; border-bottom:1px solid #434343; display:none;">
                  <?php
				    $chkvert = "select * from sp_case_study where valid=1 and deleted=0 and approve=1";
					$res_vert = mysql_query($chkvert) or die(mysql_error());	  
					while($advData = mysql_fetch_array($res_vert))
					{
						$vert_id.=$advData["category"].',';
					}
					$vertAdv=substr($vert_id,0,-1);
				  
				  	$vertical_array=explode(',',$vertAdv);
					//echo "<pre>";
					$vertll=array_unique($vertical_array);
					//echo "</pre>";
					foreach($vertll as $key=> $val)
					{
						if( $val=='')
						{
					  		unset($vertll[$key]);
					  	}
					}
				 	$unique_vertical=implode(',',array_unique($vertll));
					
					$industryQ=mysql_query($aa="SELECT * FROM sp_verticals WHERE valid=1 AND deleted=0 and vid IN(".$unique_vertical.") ORDER BY vertical_name") or die("error in category Table".mysql_error());
					//echo $aa;
				  while($industryrow=mysql_fetch_array($industryQ))
				  {
					$industryid=$industryrow["vid"];
					$industryname=$industryrow["vertical_name"];
					$vtName =str_replace(' ', '-', $industryname);
					if($industryid==checkIndustry($industryid)){
					?>
                    <a href="<?php echo $sitepath;?>contents/<?php echo $vtName;?>" class="block">&bull; <?php echo $industryname;?></a>
                    <?php 
					}
				  }
				  ?>
                     </div>
                   </li>
                   
               <?php /*?><li class="whiteTxt"><input type="radio" name="browse" id="product" value="" onclick="showProduct()"/> Product
                    <div id="productdiv" class="multichkbox boxshadow" style="width:98%; border-top:1px solid #434343; border-bottom:1px solid #434343;	display:none;">
                  <?php
				    $productQ=mysql_query("SELECT * FROM sp_product WHERE valid=1 AND deleted=0 ORDER BY it_detail") or die("error in category Table".mysql_error());
				  while($productrow=mysql_fetch_array($productQ))
				  {
					$productid=$productrow["id"];
					$productname=$productrow["it_detail"];
					if($productid==checkProduct($productid)){
					?>
                    <a href="<?php echo $sitepath;?>search_case_study.php?product=<?php echo $productid;?>" class="block"><?php echo $productname;?></a>
                    <?php
					}
				  }
				  ?>
                     </div>
                   </li><?php */?>
                   
               <?php /*?><li class="whiteTxt"><input type="radio" name="browse" id="content" value="" onclick="showContent()"/> Content Type
                    <div id="contentdiv" class="multichkbox boxshadow" style="width:98%; border-top:1px solid #434343; border-bottom:1px solid #434343; display:none;">
                  <?php
				    $contentQ=mysql_query("SELECT * FROM sp_article_type WHERE valid=1 AND deleted=0 ORDER BY article_type") or die("error in category Table".mysql_error());
				  while($contentrow=mysql_fetch_array($contentQ))
				  {
					$contentid=$contentrow["id"];
					$contentname=$contentrow["article_type"];
					if($contentid==checkContent($contentid)){
					?>
                    <a href="<?php echo $sitepath;?>search_case_study.php?content=<?php echo $contentid;?>" class="block"><?php echo $contentname;?></a>
                    <?php  
					}
				  }
				  ?>
                     </div>
                   </li><?php */?>
             </ul><strong>or</strong>
           		</div>
                <?php } ?>
                <?php
					if (strpos($requri,'how-it-works') != false)
					 {
				 ?>
                <input type="text" id="search_txt1" name="search_txt1" class="srch-txt" placeholder='Search for content' value="" />
                <?php } else { ?>
                <input type="text" id="search_txt1" name="search_txt1" class="srch-txt" placeholder='Search content by title' value="" />
                <?php } ?>
                
<div id="result" class="radiusB boxshadow"></div><input type="button" class="srch-btn" value="" onclick="goforsearch();" />
<!--<div class="upload-btn">
	<ul class="dropdown">
       <li class="whiteTxt"><a href="<?php echo $sitepath; ?>uploadcontent/" class="block padL">Add Content</a></li>
       <li class="whiteTxt"><a href="<?php echo $sitepath; ?>partners/index.php?pg=basic" class="block padL">Add IT Company for Listing</a></li>
       <li class="whiteTxt"><a href="<?php echo $sitepath; ?>leads/index.php?pg=add_requirement" class="block padL">Add Requirement</a></li>
       <li class="whiteTxt"><a href="<?php echo $sitepath; ?>ebook/" class="block padL">Create eBook</a></li>
     </ul>
     
</div>-->
</li>
</ul>
</div>