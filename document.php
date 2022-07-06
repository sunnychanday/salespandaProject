<?php
	include("../includes/global.php");
	include("../includes/check_login.php");
	//include("../../includes/connect.php");
	include("../includes/function.php");
	include ("../includes/paging_class.php");
	
$company_industry=getComIndustry($companyId);	

	$articletype = $_REQUEST['article_type'];
	$searchBy = $_REQUEST['titlesrc'];
	
	if($searchBy!='' && $articletype=='')
	{
		$docqry = "select * from sp_case_study WHERE case_study_title LIKE '%".$searchBy."%' and member_id='".$userid."' and client_id='".$c_lient_Id."' and valid=1 and deleted=0 ";
	}
	if($articletype!='' && $searchBy=='')
	{
		$docqry = "select * from sp_case_study WHERE content_type=".$articletype." and member_id=".$userid." and client_id='".$c_lient_Id."' and valid=1 and deleted=0";
	}
	if($articletype!='' && $searchBy!='')
	{
		$docqry = "select * from sp_case_study WHERE content_type=".$articletype." and case_study_title LIKE '%".$searchBy."%' and client_id='".$c_lient_Id."' and member_id=".$userid." and valid=1 and deleted=0";
	}

	else if($articletype=='' && $searchBy=='')
	{
		$docqry = "select * from sp_case_study where member_id='".$userid."' and client_id='".$c_lient_Id."' and valid=1 and deleted=0 order by doe desc";
	}
	$result=mysqli_query($conn,$docqry);
	$offset=20;
	$display_range=25;
	
	$recordcount = mysqli_num_rows($result);
	$no_page = max(1,ceil($recordcount/$offset));
	$page=1;
	if(isset($_REQUEST['page']))
	{   
		$page=$_REQUEST['page']; 
	}
	if($page >$no_page)
	{
	$page = $no_page;
	}
	else if($page == 0)
	{
	$page = 1;
	}
	$pager = Pager::getPagerData($no_page, $display_range, $page, $offset); 
	$qry1= $docqry." LIMIT $pager->offset, $offset";
	$doc_res=mysqli_query($conn,$qry1);
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SalesPanda/Manage Member Account</title>
<meta name="description" content="SalesPanda – a social selling platform that allows technology sellers & marketers to promote their offerings using content and offers to technology buyers and also collaborate with other sellers to exchange leads and partnership information"/>
<meta name="keywords" content="Add Requirements, Share Requirements, Share Content, Inbound Marketing, Showcase Products, Showcase Services"/>

<meta name="robots" content="INDEX,FOLLOW" />
<link rel="canonical" href="http://www.salespanda.com/" />
<meta name="YahooSeeker" content="INDEX, FOLLOW" />
<meta name="msnbot" content="INDEX, FOLLOW" />

<link rel="shortcut icon" href="<?php echo $sitepath; ?>images/favicon.ico" />
<link href="<?php echo $sitepath;?>css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo $sitepath; ?>js/jquery-1.8.0.js"></script>

</head>


<div id="main">
<div id="main1">
 <?php include("../includes/header-new.php"); ?>
  <div id="content">
<div class="con01">
<div class="conimage"><img src="<?php echo $sitepath; ?>images/001.png" width="990" height="20" /></div>
<div class="con02">

     
	  
	   <div id="maintab"> 
     
   <?php include("main-tab.php");?>
  </div>
   <div class="floatL padB alignC widthFull">
<form name="frmsrc" id="frmsrc" action="" method="post">
<table width="100%" border="0" align="center" cellpadding="5" cellspacing="5" class="highlight_text">
  <tr>
    <td width="9%" nowrap="nowrap">Search  By Title </td>
    <td width="32%"><input name="titlesrc" class="textbox" type="text" id="titlesrc" size="50" value="<?php echo $searchBy; ?>" placeholder="Enter Document Name" /></td>
    <td width="7%" nowrap="nowrap">Filter By</td>
    <td width="29%"><select name="article_type" id="article_type" class="selectarea" style="width:330px;">
      <option value="">Select Document Type</option>
      <?php 
					 $query=mysqli_query($conn,"SELECT * FROM sp_article_type WHERE deleted=0 AND valid=1");
					 while($row=mysqli_fetch_array($query))
					 {
						 $article_type=$row["article_type"];
						 $article_id=$row["id"];
					 ?>
      <option value="<?php echo  $article_id;?>"<?php if($articletype==$article_id){?> selected="selected"<?php } ?>><?php echo $article_type?></option>
      <?php } ?>
    </select></td>
    <td width="23%"><input type="submit" name="Submit" value="Search" /></td>
    </tr>
</table>
</form>
</div>
   </div>   

</div>

</div>


</div>
</div>
<a href="#" class="btn">Add New Content</a>
<?php if($recordcount>0){ errmsg();?><?php successmsg();?>

<div align="right" style="font-size:12px"><strong>[ Total Content: <?php echo $recordcount; ?> ]</strong></div>
<div class="maincontact">
<div id="vf111" class="verify" style="display:none;">
<div class="clipboardtop-01"></div>
<div class="clipboardmiddle-02 font14">
<!--<span class="font12 bold">Tag alerts are by default set by your job profile. You can change them as per your needs. Tag alerts help users to :</span>-->
<ul class="padA font12 lineH"><li>Receive Leads and Account alerts as per your role and requirement</li>
<li>Personalise your Salespanda browing every time you login to see contacts, leads and company information</li>
<li>Get a dashboard view of relevant content as per your needs.</li></ul>
</div>
<div class="clipboardtop-03"></div>
</div>
 
  
  <form name="myform" id="myform" action="" method="post">
  <input type="hidden" name="pg" id="pg" value="<?php echo $pg; ?>" />
  
  <table  width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#cccccc" >
 
  <tr class="alt-row-col-head greenTxt alignC">
   <td width="5%"><input type="checkbox" name="check_all_cs" id="check_all_cs" /></td>
    <td width="5%">Logo</td>
    <td width="10%">Document Type</td>
    <td width="10%">Requirement Type</td>
    <td width="15%">Document Title </td>
    <td width="15%">Description</td>
    <td width="15%">Category</td>
    <td width="15%">Subcategory</td>
    <td width="5%" align="center" nowrap="nowrap">Creation Date </td>
    <td width="5%" align="center">Action</td>
  </tr>
  <?php 
  		$j=1;
		$tocReqType="";
  		while($docData=mysqli_fetch_array($doc_res))
		{
		
		$did=$docData['id'];
		$docTitle=$docData['case_study_title'];
		$docDesc11=$docData['case_study_desc'];
		
		if(strlen($docDesc11) > 100)
		{
  			$docDesc= substr($docDesc11, 0, 100)."... ".$hyperlink;
		}
		else 
		{
    		$docDesc= $docDesc11;
		}
		$docType = $docData['content_type'];
		
		$tocReqType = $docData['content_reqrt_type'];
		$allName="";
		if($tocReqType!='')
		{
			$contReqArray = explode(',', $tocReqType);
			foreach($contReqArray as $reqType)
			{
				$extReqType=getreqirementType($reqType);
				$allName.=$extReqType.', ';
				
			}
			$extrqName = substr($allName,0,-2);
			$all=$extrqName;
		}
		
		$docCategory = $docData['category'];
		$allCategory="";
		if($docCategory!='')
		{
			$categoryArray = explode(',', $docCategory);
			foreach($categoryArray as  $catcont)
			{
				$extcatg=categoryName($catcont);
				$allCategory.=$extcatg.', ';
			}
			$extgategory = substr($allCategory,0,-2);
		}
		
		
		
		$docSubcategory = $docData['subcategory'];
		$allExtSub="";
		if($docSubcategory!='')
		{
			$subcategoryArray = explode(',', $docSubcategory);
			foreach($subcategoryArray as $extsubcat)
			{
				$existingSubCat = subCategoryName($extsubcat);
				$allExtSub.=$existingSubCat.', ';
			}
			$extsubctgory = substr($allExtSub,0,-2);
		}
		
		$cdate = $docData['doe'];
		$mdate= $docData['dou'];
		$url= $docData['case_study_url'];
		$csLogo= $docData['image_thumb1'];
		$cmpId=$docData['attach_company'];
		if($cmpId=='' || $cmpId==0){
		 $atch = 'I';
		}
		else
		{
		 $atch = 'C';
		}
		
		if($mdate=='0000-00-00 00:00:00'){
			$modDate = 'Never';
		}
		else
		{
			$modDate = dateFormat1($mdate);
		}
		
  ?>
  <tr class="alt-row-col" style="word-wrap:normal;word-break: break-word;">
   <td valign="top" nowrap="nowrap"><input name="cs_reminder[]" type="checkbox" id="cs_reminder" value="<?php echo $did; ?>" />   
     [<?php echo $atch; ?>]</td>
    <td valign="top"><?php if($csLogo!=''){?>
			   <img src="../upload/casestudy/thumb/<?php echo $csLogo; ?>" height="20" width="40" border="0">
			   <?php } else { ?>
			   <img src="../upload/casestudy/nopic/nopic.jpg" height="20" width="40" border="0">
			   <?php } ?>			   </td>
    <td valign="top"><?php if($docType!=''){ echo $docmnType=getarticleName($docType); } ?></td>
    <td valign="top"><?php if($tocReqType!=''){ echo $extrqName;  }  ?></td>
    <td valign="top"><a href="index.php?pg=docDetail&d=<?php echo $did; ?>&url=<?php echo $url; ?>"><?php echo ucfirst($docTitle);?></a></td>
    <td valign="top"><?php echo ucfirst($docDesc);?></td>
    <td valign="top"><?php  if($docCategory!=''){ echo $extgategory; }?></td>
    <td valign="top"><?php if($docSubcategory!='')	{ echo $extsubctgory; } ?></td>
    <td align="center" valign="top"><?php echo dateFormat1($cdate); ?>
      <p>&nbsp;</p></td>
    <td align="center" valign="top" nowrap="nowrap"><a href="document-detail.php?d=<?php echo $did; ?>&url=<?php echo $url; ?>&search1=<?php echo $articletype; ?>&search2=<?php echo $searchBy; ?>&page=<?php echo $page; ?>">Edit</a> | <a href="delete-document.php?d=<?php echo $did; ?>&page=<?php echo $page; ?>" onclick="return confirm('Are You Sure to Delete Document');">Delete</a> </td>
  </tr>
  <?php } ?>
</table>
</form>
<div class="clear"></div>
<div class="submit"><div class="sub-01"><div class="sub-02"></div>
<div class="sub-03"></div>
</div>
</div>
</div>

 <?php
         if ($recordcount>$offset)
         {?>
        <table width="100%" border="0" cellpadding="4" cellspacing="1" class="greytable">
          <tr class="bodytext">
            <td height="28" align="left"> <ul class="pagination"><?php if ($page!=1){ ?>
               
				<li class="first link cur"><a href="index.php?pg=document&article_type=<?php echo $articletype; ?>&titlesrc=<?php echo $searchBy; ?>&page=<?php echo ($page - 1);?>">&laquo;</a></li>
                <?php } for ($i = $pager->lrange; $i <= $pager->rrange; $i++){
                    if ($page !=1)
                        echo ""; 
                    if ($i == $pager->page){?>
                <li class="link"><a class="cur"><?php echo $i; ?></a></li>
                <?php }else {?>
                <li class="link"><a href="index.php?pg=document&article_type=<?php echo $articletype; ?>&titlesrc=<?php echo $searchBy; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                <?php }}if ($page == $pager->np){
                    echo "";
                }else {?>
                <li class="last link"><a href="index.php?pg=document&article_type=<?php echo $articletype; ?>&titlesrc=<?php echo $searchBy; ?>&page=<?php echo ($page + 1); ?>">&raquo;</a></li>
                <?php } ?></ul></td>
          </tr>
        </table>
        <?php } } else { ?>

  <table width="100%" border="0" cellpadding="4" cellspacing="1">
  <tr class="add-comment-02">
    <td align="center"><strong class="font14"> Data Not Found!!</strong></td>
  </tr>
</table><br />

        <?php }?>

<?php include("../includes/footer-new.php"); ?>
</body>
</html>

<script type="text/javascript">

$('#check_all_cs').click(function() {
if( $('#check_all_cs').is(":checked") )
{
$('input[name="cs_reminder[]"]').each( function() {
$(this).attr("checked",true);
})
}
else
{
$('input[name="cs_reminder[]"]').each( function() {
$(this).attr("checked",false);
})
}

});

$('input[name="cs_reminder[]"]').bind('change', function() {

if ( $('input[name="cs_reminder[]"]').filter(':not(:checked)').length == 0 )
{
$('#check_all_cs').attr("checked",true);
}
else
{
$('#check_all_cs').attr("checked",false);
}

});



