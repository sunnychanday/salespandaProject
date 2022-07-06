<?php 
	include("../includes/global.php");
	include("../includes/check_login2.php");
    include("manager/common_functions.php");
	include("geoiploc.php");

	//echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];         
       
	$set_ip_exclusion="select distinct(exclusion_ip) from sp_ip_exclusion where client_id='".$clientId."' and userid='".$userID."'";
	$get_ip_exclusion=mysqli_query($conn, $set_ip_exclusion);
	
	while($find_ip_excl= mysqli_fetch_array($get_ip_exclusion))
	{
		if($find_ip_excl["exclusion_ip"]!='')
		{
		   $exclusion_ip.=$find_ip_excl["exclusion_ip"].',';
		}

	}
	
	$exclusion_ip_find=substr($exclusion_ip,0,-1);
	$excl_new=explode(',',$exclusion_ip_find);
    
	$entryDate = date("Y-m-d");
	
	$pathqry="select subdomain_url,cms_subdomain_url from sp_subdomain where client_id='".$clientId."'";
	$pthqry=mysqli_query($conn, $pathqry) or die($conn->error);
	$pathData=mysqli_fetch_array($pthqry);
	$defaulpathname='http://'.$pathData['cms_subdomain_url'].$_SERVER['SCRIPT_NAME'];
	
	if($_SERVER['SCRIPT_NAME']!='/'){
		$page_hits ='http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	}
	else{
		$page_hits ='http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']."index.php";
	}
    
	$ip= CommonStaticFunctions::get_remote_user_ip();
	$ip_location=getCountryFromIP($ip, " NamE ");
	$agent=$_SERVER['HTTP_USER_AGENT'];
	$str_time=time();
    
	if (in_array($ip, $excl_new, true)) 
	{
		$fake_ip=1;
	}
	else
	{
		$fake_ip=0;
	}
    
	if($page_hits==$defaulpathname)
	{
		$pghtq="SELECT * FROM sp_pagehits WHERE page ='".$page_hits."' and client_id='".$clientId."' and entryDate='".$entryDate."' and ip_address='".$ip."'";
	    $rst=mysqli_query($conn, $pghtq) or die($conn->error);		
	    $endTime=strtotime("+30 minutes",$str_time);
	    $exCount=mysqli_num_rows($rst);

		if($exCount==0 &&  $fake_ip == 0)
		{ 
			if($ip_location=='India' or $ip_location=='United Kingdom' or $ip_location=='United States' or $ip_location=='Australia')
			{
				$ip_location1=$ip_location;
			}
			else
			{
				$ip_location1="others";
			}
            
			$a3="INSERT INTO sp_pagehits set page='".$page_hits."', count_hit='1',startTime='".$str_time."', endtime='".$endTime."', client_id='".$clientId."',ip_address='".$ip."',ip_location='".$ip_location1."',user_agent='".$agent."', entryDate='".$entryDate."'";
			$insert = mysqli_query($conn, $a3);
			
			if($_SERVER['SCRIPT_NAME']!= '/index.php')
			{
				$a4="select entryDate,startTime,ip_address from sp_pagehits where page='http://".$_SERVER['HTTP_HOST']."/index.php' and entryDate = '".$entryDate."' and client_id='".$clientId."' and ip_address='".$ip."'";
				$check_query = mysqli_query($conn, $a4);
				
				$num_rows = mysqli_num_rows($check_query);
				$set_data=mysqli_fetch_array($check_query);
				$dateofEntry=$set_data['entryDate'];
				$set_time=$set_data['startTime'];
				$start_time=$set_time;
				$end_time=strtotime("+30 minutes",$start_time);
				$ipAddress=$set_data['ip_address'];
				
				if($num_rows == 0 && $fake_ip == 0)
				{
					if($ip_location=='India' or $ip_location=='United Kingdom' or $ip_location=='United States' or $ip_location=='Australia')
					{
                        $ip_location1=$ip_location;
					}
					else
					{
						$ip_location1="others";
					}
					
					$a3s="INSERT INTO sp_pagehits set page='http://".$_SERVER['HTTP_HOST']."/index.php', count_hit='1',startTime='".$str_time."', endtime='".$endTime."', client_id='".$clientId."',ip_address='".$ip."',ip_location='".$ip_location1."',user_agent='".$agent."', entryDate='".$entryDate."'";
					$insert = mysqli_query($conn, $a3s);
				}
				else if($str_time>=$end_time && $fake_ip == 0)
				{
					$a1="UPDATE sp_pagehits SET startTime='".$str_time."', endtime='".$end_time."', count_hit = count_hit+1, ip_address='".$ip."',user_agent='".$agent."' WHERE page = 'http://".$_SERVER['HTTP_HOST']."/index.php' and client_id='".$clientId."' and ip_address='".$ipAddress."' and entryDate='".$dateofEntry."'";
					$updatecounter = mysqli_query($conn, $a1);
				}
			}
		}
		else
		{ 
			$get_timedata=mysqli_fetch_array($rst);
			$dateofEntry=$get_timedata['entryDate'];
			$set_time=$get_timedata['startTime'];
			$start_time=$set_time;
			$end_time=strtotime("+30 minutes",$start_time);
			$ipAddress=$get_timedata['ip_address'];
	        
			if($str_time>=$end_time && $fake_ip == 0)
   	   		{	
				$a11="UPDATE sp_pagehits SET startTime='".$str_time."', endtime='".$end_time."', count_hit = count_hit+1, ip_address='".$ip."',user_agent='".$agent."' WHERE page = '$page_hits' and client_id='".$clientId."' and ip_address='".$ipAddress."' and entryDate='".$dateofEntry."'";
				$updatecounter = mysqli_query($conn, $a11);
				
				if($_SERVER['SCRIPT_NAME'] != '/index.php')
				{
					$check_querys = mysqli_query($conn, $a44="select * from sp_pagehits where page='http://".$_SERVER['HTTP_HOST']."/index.php' and entryDate = '".$entryDate."' and client_id='".$clientId."' and ip_address='".$ip."'");
					
					$num_rows = mysqli_num_rows($check_querys);
                    if($num_rows == 0 && $fake_ip == 0)
                    {
						if($ip_location=='India' or $ip_location=='United Kingdom' or $ip_location=='United States' or $ip_location=='Australia')
						{
							$ip_location1=$ip_location;
                        }
						else
						{
                           $ip_location1="others";
						}

						$insert = mysqli_query($conn, $as3="INSERT INTO sp_pagehits set page='http://".$_SERVER['HTTP_HOST']."/index.php', count_hit='1',startTime='".$str_time."', endtime='".$endTime."', client_id='".$clientId."',ip_address='".$ip."',ip_location='".$ip_location1."',user_agent='".$agent."', entryDate='".$entryDate."'");
						
                    }
                    else 
                    {
						$inddata=mysqli_fetch_array($check_querys);
						$dateofEntry=$inddata['entryDate'];
						$set_time=$inddata['startTime'];
						$lastendtime=$inddata['endtime'];
						$start_times=$set_time;
						$endtiming=strtotime("+30 minutes",$start_time);
						
						if($str_time>=$lastendtime && $fake_ip == 0)
						{
							$updatecounter = mysqli_query($conn, $ad1="UPDATE sp_pagehits SET startTime='".$str_time."', endtime='".$endtiming."', count_hit = count_hit+1, ip_address='".$ip."',user_agent='".$agent."' WHERE page = 'http://".$_SERVER['HTTP_HOST']."/index.php' and client_id='".$clientId."' and ip_address='".$ipAddress."' and entryDate='".$dateofEntry."'");
						
						}
					}
			   	}
			}
		}
	}
