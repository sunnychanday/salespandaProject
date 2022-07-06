<?php
	header("Access-Control-Allow-Origin: *");  
	header ("Expires: ".gmdate("D, d M Y H:i:s", time())." GMT");   
    header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  
    header ("Cache-Control: no-cache, must-revalidate");  
    header ("Pragma: no-cache"); 
    
    /*echo "<pre>11";
	print_r($_SERVER['HTTP_ORIGIN']);die;*/
    
	$c_lient_Id = $_REQUEST['client_id'];
	
	include("includes/global.php");
	include("includes/function.php");
	include("geoiploc.php");
	include("includes/check_package_balance.php");
	
	$time = ''; $visitTime=''; $emailId = ''; $campId = ''; $endTime='';
	//~ echo "<pre>";
	//~ print_r($_REQUEST);die;
	//print_r(json_decode($_REQUEST));die;
	
	if(isset($_REQUEST['tkn']) && $_REQUEST['tkn']!=1)
	{ 
		if(isset($_REQUEST['start']) && $_REQUEST['start']!='')
		{
		    $datumUhrzeit = substr($_REQUEST['start'], 0, strpos($_REQUEST['start'], '('));
			$time = strtotime($datumUhrzeit);
			
			if($time !== false)
			{ 
				$visitTime  = date('Y-m-d H:i:s', $time );
			}
		}
		
		if(isset($_REQUEST['end']) && $_REQUEST['end']!='')
		{
		    $datumUend = substr($_REQUEST['end'], 0, strpos($_REQUEST['end'], '('));
			$end = strtotime($datumUend);
			$endTime  = date('Y-m-d H:i:s', $end );
		}
		
		if(isset($_REQUEST['timeSpent']) && $_REQUEST['timeSpent']!='')
		{
			$timeSpent = $_REQUEST['timeSpent'];
		}
		
		$userAgent = $_SERVER['HTTP_USER_AGENT']; 
		$contact = 0;
		if(isset($_REQUEST['url']) && $_REQUEST['url']!=''){   
			$tokens = parse_url($_REQUEST['url']);
		
			parse_str($tokens['query'], $query); 
		
			if(isset($query['camp_id']) && $query['camp_id']!='')
			{
				$campId = 	$query['camp_id'];
			}
			
			if(isset($query['channel_type']) && $query['channel_type']!=''){
		    $source = $query['channel_type'];
		    }
		}
		
		if(isset($_REQUEST['c']) && $_REQUEST['c']!=''){
			$contact = 	decode($_REQUEST['c']);
			$contact = checkContact($contact);
			$contact = ($contact!='') ? $contact : 0;
		}
		
		if(isset($_REQUEST['vtoken']) && $_REQUEST['vtoken']!=''){
		   $vtoken = $_REQUEST['vtoken']; 
		}
		
		if(isset($_REQUEST['uemail']) && $_REQUEST['uemail']!='')
		{
		    $emailId = $_REQUEST['uemail'];
		}
			
		if($_REQUEST['tkn']==4)
		{
		   $addSQL = "UPDATE sp_eventtrack set emailid='".$emailId."' WHERE visitdate='".$visitTime."' and visitor_token='".$vtoken."' and client_id='".$c_lient_Id."'";
		}
		elseif($endTime!=''){
			$addSQL = "UPDATE sp_eventtrack set emailid='".$emailId."',end_time='".$endTime."',timeSpent='".$timeSpent."' WHERE visitdate='".$visitTime."' and visitor_token='".$vtoken."' and client_id='".$c_lient_Id."'";
		}
		else{
			$mic = (isset($_REQUEST['mic']) && $_REQUEST['mic']==1) ? 1 : 0;
			
			$addSQL="insert into sp_eventtrack set client_id='".$c_lient_Id."', emailid='".$emailId."', camp_id='".$campId."',useragent='".$userAgent."', visitdate='".$visitTime."', visit_page='".$_REQUEST['visit_page']."',source='".$source."', url='".urldecode($_REQUEST['url'])."',ip_address='".(get_remote_user_ip())."', visitor_token='".$vtoken."', referring='".$_REQUEST['HTTP_REFERER']."', contact_id=$contact, microsite=$mic";
		//echo $addSQL;
			
		echo "contact-----------".$contact;//die;

			if(isset($contact) && $contact!=0){
				echo $update_content="update sp_contact set known=1,known_date='".date('Y-m-d H:i:s')."',vtoken='".$vtoken."' where id='".$contact."' and client_id='".$c_lient_Id."' and vtoken!='".$vtoken."'";
				mysqli_query($conn, $update_content);
			
				echo "<br>".$update_content.'------------'.mysqli_affected_rows();
				if(mysqli_affected_rows()==1){
					$knwSQL="insert into known_visitors set client_id='".$c_lient_Id."', known_date='".date('Y-m-d H:i:s')."',  vtoken='".$vtoken."',contact_id=$contact";
					mysqli_query($conn, $knwSQL);	
				}
				
			}  
		}
		
		$res=mysqli_query($conn, $addSQL);
	}
	
	function checkContact($id=''){
		global $conn;
		$query =  mysqli_query($conn, "select id from sp_contact where id=".$id);
		$solution =  mysqli_fetch_array($query);
		if(isset($solution['id']) && $solution['id']!=''){
			$result = $solution['id'];	
		}else{
			$result = '';
		}
		return $result;
	}


?>
