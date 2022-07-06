<?php
	include("../includes/connect.php");
    include("manager/common_functions.php");
	include("includes/check_login2.php");

	function addinfo($page)
	{
		$datetime =date("Y/m/d") . ' ' . date('H:i:s') ;
		
		if(mysql_num_rows(mysql_query($aa="SELECT page FROM sp_pagehits WHERE page = '$page'")))
		{
			'<br><br><br>A1='.$aa;
			//A counter for this page  already exsists. Now we have to update it.

			$updatecounter = mysql_query($a1="UPDATE sp_pagehits SET datetime='".$datetime."',count = count+1 WHERE page = '$page'");
			'<br><br><br>A2='.$a1;
			if (!$updatecounter) 
			{
				die ("Can't update the counter : " . mysql_error()); // remove ?
			}
		}
		else
		{
			// This page did not exsist in the counter database. A new counter must be created for this page.

			$insert = mysql_query($a3="INSERT INTO sp_pagehits set page='".$page."', count='1',datetime='".$datetime."'");
			'<br><br><br>A3='.$a3;
			if (!$insert) 
			{
				die ("Can\'t insert into sp_pagehits : " . mysql_error()); // remove ?
			}
		}

		$ip= CommonStaticFunctions::get_remote_user_ip();
		$agent=$_SERVER['HTTP_USER_AGENT'];
		$datetime =date("Y/m/d") . ' ' . date('H:i:s');


		if(!mysql_num_rows(mysql_query("SELECT ip_address FROM sp_pageinfo WHERE ip_address = '$ip'"))) // check if the IP is in database
		{
			// if not , add it.	
			$adddata = mysql_query("INSERT INTO sp_pageinfo (ip_address, user_agent, datetime) VALUES('$ip' , '$agent','$datetime' ) ") ;
			if (!$adddata) 
			{
				die('Could not add IP : ' . mysql_error()); // remove ?
			}
		}
		
		$result = mysql_query("SELECT * FROM sp_pageinfo");
		$num_rows = mysql_num_rows($result);
		if($num_rows > 0)
		{
			for ($i = 1; $i <= $to_delete; $i++) 
			{

				$delete = mysql_query("DELETE FROM sp_pageinfo ORDER BY id LIMIT 1") ;
				if (!$delete) 
				{
					die('Could not delete : ' . mysql_error()); // remove ?
				}
			}
		}
	} 
