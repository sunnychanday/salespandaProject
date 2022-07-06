<?php
	include("includes/global.php");
	include("includes/check_login.php");
	include("includes/function.php");
	
	$usercmp="select * from sp_members where valid=1 and deleted=0 and pid='".$userid."'";
	$resus = mysqli_query($conn, $usercmp);
	$userData = mysqli_fetch_array($resus);
 	$companyId=$userData['comp_id'];
	$clientId=$userData['client_id'];
	$doe=date('Y-m-d h:i:s');
	
	//echo '<br>client_id'.$clientId=$c_lient_Id;
 	$companyVertical=getComIndustry($companyId);
	
	if(isset($_POST["submitted"]) && $_POST["submitted"] == 1)
	{
		/*	echo 'SSSs= '.$to_select_list=$_REQUEST['to_select_list'];
		echo "<pre>";
		print_r($_POST);
		echo "</pre>";
		exit;*/

		//$clientId=$c_lient_Id;
		$catarray=$_POST['to_select_list'];
		$arraycount=count($catarray);
		
		if($arraycount!=0)
		foreach($catarray as $cat)		
		{
			$hashId = $cat;
			$hashtgId.= $hashId.',';
		}
		
		$allhash = substr($hashtgId,0,-1);
		$chkcomp = "select * from sp_hashtag where client_id='".$clientId."' and comp_id='".$companyId."'";
		$rescmp = mysqli_query($conn, $chkcomp);
		$hashchek = mysqli_num_rows($rescmp);
		if($hashchek==0)
		{
			$addhash="insert into sp_hashtag set client_id='".$clientId."', comp_id='".$companyId."', hashtag='".$allhash."', doe='".$doe."'";
			$addres=mysqli_query($conn, $addhash); 
		}
		else
		{
			$updhash="update sp_hashtag set hashtag='".$allhash."', doe='".$doe."' where client_id='".$clientId."' and comp_id='".$companyId."'";
			$updres=mysqli_query($conn, $updhash);
		}
	}
	
	if(isset($_POST["submitted"]) && $_POST["submitted"]==2)
	{
		$newsolutionName=$_POST['solutionsearch'];
		$client_id=$_POST['clientID'];
		$vertical=$_POST['vertical'];
		
		$chk="select * from sp_category where it_type='".$newsolutionName."' and category_industry='".$vertical."'";
		$res = mysqli_query($chk);
		$catsCount = mysqli_num_rows($res);
		
		if($catsCount==0)
		{
		 	$addc="insert into sp_category set 	it_type='".$newsolutionName."', category_industry='".$vertical."', category_type='S', client_id='".$client_id."', doe='".$doe."'";
			$resc = mysqli_query($conn, $addc);
			$newcatId = mysqli_insert_id($conn);
			
			$chkcomp="select * from sp_hashtag where client_id='".$clientId."' and comp_id='".$companyId."'";
			$rescmp=mysqli_query($conn, $chkcomp);
			$hashchek=mysqli_num_rows($rescmp);
			
			if($hashchek==0)
			{
				$addhash="insert into sp_hashtag set client_id='".$clientId."', comp_id='".$companyId."', hashtag='".$newcatId."', doe='".$doe."'";
				$addres=mysqli_query($conn, $addhash); 
			}
			else
			{
				$extdata=mysqli_fetch_array($rescmp);
				$extHash=$extdata['hashtag'];
				$newHash=$extHash.','.$newcatId;
				$updhash="update sp_hashtag set hashtag='".$newHash."', doe='".$doe."' where client_id='".$clientId."' and comp_id='".$companyId."'";
				$updres=mysqli_query($updhash);
			}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>SalesPanda/Manage Member Account</title>
	<meta name="description" content="SalesPanda – a social selling platform that allows technology sellers & marketers to promote their offerings using content and offers to technology buyers and also collaborate with other sellers to exchange leads and partnership information"/>
	<meta name="keywords" content="Add Requirements, Share Requirements, Share Content, Inbound Marketing, Showcase Products, Showcase Services"/>

	<meta name="robots" content="INDEX,FOLLOW" />
	<link rel="canonical" href="http://www.salespanda.com/" />
	<meta name="YahooSeeker" content="INDEX, FOLLOW" />
	<meta name="msnbot" content="INDEX, FOLLOW" />

	<link rel="shortcut icon" href="<?php echo $sitepath; ?>images/favicon.ico" />
	<link href="<?php echo $sitepath; ?>css/style.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="<?php echo $sitepath; ?>jquery/jquery-1.4.2.min.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo $sitepath; ?>jquery/jquery.autocomplete.css" />
	<script type="text/javascript" src="<?php echo $sitepath; ?>js/jquery-1.8.0.js"></script>
	<script type='text/javascript' src='<?php echo $sitepath; ?>jquery/jquery.autocomplete.js'></script>

	<script language="javascript" type="text/javascript">
		function move_list_items(sourceid, destinationid)
		{
			$("#"+sourceid+"  option:selected").appendTo("#"+destinationid);
		}
		//this will move all selected items from source list to destination list
		function move_list_items_all(sourceid, destinationid)
		{
			$("#"+sourceid+" option").appendTo("#"+destinationid);
		}
		
		
		function selectAll() 
		{ 
			selectBox = document.getElementById("to_select_list");

			for (var i = 0; i < selectBox.options.length; i++) 
			{ 
				 selectBox.options[i].selected = true; 
			} 
		}	
	</script>
</head>
<body>
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
						
						<div id="">
							<?php include("sub-main-tab.php");?>
						</div>
						
						<div align="left"></div>

						<form name="frmct" id="frmct" action="" method="post">
							<table width="80%" height="254" cellpadding="5" cellspacing="5">
								<tbody>
									<tr>
										<input type="hidden" id="submitted" name="submitted" value="1">
										<td rowspan="5">
											<select id="from_select_list" multiple="multiple" name="from_select_list" style="width:300px;height:200px;"> 
												<?php 	
												$hqry="select * from sp_hashtag where valid=1 and deleted=0 and client_id='".$clientId."'";
												$resh=mysqli_query($conn, $hqry);
												$hashData=mysqli_fetch_array($resh);
												$hashext=$hashData['hashtag'];
	
												//$categoryQuery="select * from sp_category where valid=1 and deleted=0 and category_industry='".$companyVertical."' ORDER BY it_type";
												if($hashext!='')
												{
													$categoryQuery="select * from sp_category where valid=1 and deleted=0 and category_industry='".$companyVertical."' and id NOT IN($hashext)";
												}
												else
												{
													$categoryQuery="select * from sp_category where valid=1 and deleted=0 and category_industry='".$companyVertical."'";
												}
												
												$categoryquery=mysqli_query($conn, $categoryQuery);
												$countCat=mysqli_num_rows($categoryquery);
							
												while($catrow=mysqli_fetch_array($categoryquery))
												{
													$categoryid=$catrow["id"];
													$catName=$catrow["it_type"];
			  	
													?>      
													<OPTION value="<?php echo $categoryid; ?>"><?php echo $catName; ?></OPTION>
													<?php
												}
												?>
											</select>
										</td>
    
										<td><input id="moveright" type="button" value=">>" onclick="move_list_items('from_select_list','to_select_list'); selectAll();" /></td>
										
										<td rowspan="5">
											<select id="to_select_list" multiple="multiple" name="to_select_list[]" style="width:300px;height:200px;" class="adi">  
												<?php
												$hQuery="select * from sp_hashtag where valid=1 and deleted=0 and client_id	='".$clientId."'";
												$hres = mysqli_query($conn, $hQuery);
												$countCat=mysqli_num_rows($hres);
												$exthash=mysqli_fetch_array($hres);
												$catg=$exthash['hashtag'];
												$catArray=explode(',',$catg);
			
												foreach($catArray as $cattg)
												{
													if($cattg!=''){
														$ctId=$cattg;
														$catName=categoryName($ctId);
														?>      
														<OPTION value="<?php echo $ctId; ?>" selected="selected"><?php echo $catName; ?></OPTION>
														<?php
													}
												}
												?>
											</select>
										</td>
									</tr>

									<tr>
										<td>
											<input id="moveleft" type="button" value="&lt;&lt;" onclick="move_list_items('to_select_list','from_select_list'); selectAll(); " />
										</td>
									</tr>
									
									<tr>
										<td>&nbsp;</td>
									</tr>
									
									<tr>
										<td><!--<input id="moverightall" type="button" value="Move Right All" onclick="move_list_items_all('from_select_list','to_select_list');" />--></td>
									</tr>
					
									<tr>
										<td height="69"><!--<input id="moveleftall" type="button" value="Move Left All" onclick="move_list_items_all('to_select_list','from_select_list');" />--></td>
									</tr>

									<tr>
										<td height="51">&nbsp;</td>
										<td><input type="submit" name="button" id="button" value="Submit" class="btn-2" /></td>
										<td>&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</form>

						<form action="" method="post" name="frmhash" id="frmhash">
							<table width="40%" align="left" style="border-collapse:collapse;">
								<tr>
									<input type="hidden" id="submitted" name="submitted" value="2">
									<td>
										<span class="font16">Add New Hashtag
										<input type="hidden" name="vertical" id="vertical" value="<?php echo $companyVertical; ?>" />
										<input type="hidden" name="clientID" id="clientID" value="<?php echo $clientId; ?>" />
										</span>
									</td>
								</tr>
								
								<tr>
									<td>
										<input name="solutionsearch" type="text" id="solutionsearch" size="40" />
										<input type="submit" name="button" id="button" value="Add Hashtag" class="btn" />
									</td>
								</tr>
  
								<tr>
									<td>&nbsp;</td>
								</tr>
					
								<tr>
									<td>&nbsp;</td>
								</tr>
							</table>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php include("../includes/footer-new.php"); ?>
	
	
	<SCRIPT type="text/javascript">
	  
		function listbox_move(listID,direction){
		var listbox=document.getElementById(listID);
		var selIndex=listbox.selectedIndex;
		if(-1==selIndex){alert("Please select an option to move.");return;}
		var increment=-1;
		if(direction=='up')
			increment=-1;
		else
			increment=1;
		if((selIndex+increment)<0||(selIndex+increment)>(listbox.options.length-1)){return;}
		 
		var selValue=listbox.options[selIndex].value;
		var selText=listbox.options[selIndex].text;
		listbox.options[selIndex].value=listbox.options[selIndex+increment].value;
		listbox.options[selIndex].text=listbox.options[selIndex+increment].text;
		listbox.options[selIndex+increment].value=selValue;
		listbox.options[selIndex+increment].text=selText;
		listbox.selectedIndex=selIndex+increment;
		}
	 
	 
		function listbox_moveacross(sourceID,destID){
		var src=document.getElementById(sourceID);
		var dest=document.getElementById(destID);
		 
		var picked1 = false;
		for(var count=0;count<src.options.length;count++){
			if(src.options[count].selected==true){picked1=true;}
		}
	 
		if(picked1==false){alert("Please select an option to move.");return;}
	 
		for(var count=0;count<src.options.length;count++){
			if(src.options[count].selected==true){var option=src.options[count];
				var newOption=document.createElement("option");
				newOption.value=option.value;
				newOption.text=option.text;
				newOption.selected=true;
				try{dest.add(newOption,null);
				src.remove(count,null);
			}
				catch(error){dest.add(newOption);src.remove(count);
			}
			count--;
			}
		}}
	 
		function listbox_selectall(listID,isSelect){
			var listbox=document.getElementById(listID);
			for(var count=0;count<listbox.options.length;count++){
				listbox.options[count].selected=isSelect;
				}
		}
	 
	</SCRIPT>

	<script type="text/javascript">
	$().ready(function() {
		$("#solutionsearch").autocomplete("../../includes/get-solution.php", {
			width: 268,  
			matchContains: true,
			selectFirst: false
		});
	});
	</script>
</body>
</html>