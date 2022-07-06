<?php
	include("includes/global.php");
	include("includes/check_login.php");
	include("includes/function.php");
	
	$delquery=mysqli_query($conn, $a="select c_client_id from sp_sub_members where p_client_id='SP11374'");
	
	while($getquery=mysqli_fetch_array($delquery))
	{
	    $delid=$getquery['c_client_id'];
		$files = glob('/home/salespanda/public_html/hdfcmfpartners/webcontent/upload/casestudy/'.$delid.'/*');
		
		foreach($files as $file)
		{
			if(is_file($file)){
				unlink($file);
			}
		}
	}
