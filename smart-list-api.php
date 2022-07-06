<?php
	
    include("includes/global.php");
	include("includes/function.php");
	
	if(isset($_SESSION) && $_SESSION!=''){
		session_destroy();       
	}
	
	//~ $get_array = array("status"=>"506", "message"=>"Something Getting Wrong!");
	//~ echo json_encode($get_array, JSON_UNESCAPED_SLASHES);die;
    //$url = 'https://api.sendgrid.com/'; 
    
    //$token 	= '26a40516-98da-4773-bd0f-90b62f3586d9';
	$token 	= $_GET['stoken'];
	$gatePass 	= array('token' => $token);
 
	$ch = curl_init();
	$data = array('token' => $token);
	curl_setopt($ch, CURLOPT_URL, 'https://cms.hdfcfund.com/en/hdfc/api/v1/subscribe/validate-source?token='.$token);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$response = curl_exec($ch);
	
	//echo $result;
    //$response = '{"product":"Hdfc product2","email":"abc3@gmail.com","isVerifiedSource":true}';
	//echo $response; exit;
    
	$source_ip = get_remote_user_ip();
	$ref_url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$responseArray = json_decode($response);
	$msg=0;
	$spContactId = '';
	if($responseArray->data->isVerifiedSource == true){
		$c_lient_Id = "SP11374";
		$userEmail 	= trim($responseArray->data->email);
		$product   = trim($responseArray->data->product);
		
		if($userEmail=='' || $product==''){
			$get_array = array("status"=>"502", "message"=>"Please check parameters it's value may be empty.");
		}else{
			$listName = "SmartList_".$product;
			$spcontact="select * from sp_contact where client_id='".$c_lient_Id."' and email_id='".$userEmail."' and valid=1 and deleted=0";
			$spcontactget = mysqli_query($conn, $spcontact);
			$spcontactcount = mysqli_num_rows($spcontactget);
			$spcontactset = mysqli_fetch_array($spcontactget);
			
			if($spcontactcount==0){   
				$spcontactInsert="insert into sp_contact set client_id='".$c_lient_Id."',email_id='".$userEmail."',source='HDFC Subscription',doe='".date('Y-m-d h:i:s')."'";
			
				if(mysqli_query($conn, $spcontactInsert)){
					$spContactId = mysqli_insert_id($conn);
					$msg = 1;
				}
			}else{
				$spcontactInsert="update sp_contact set source='HDFC Subscription', unsubscribe=0 where id='".$spcontactset['id']."'";
				if(mysqli_query($conn, $spcontactInsert)){
					$spContactId=$spcontactset['id']; 
					$msg =1;
				} 
			}
			
			$splist="select * from sp_contact_list where client_id='".$c_lient_Id."' and contact_list_name='".$listName."'";
			$splistget = mysqli_query($conn, $splist);
			$splistset = mysqli_fetch_array($splistget);
			$splistcount = mysqli_num_rows($splistget);
			
			if($splistcount==0){ 
				$splist01 = "insert into sp_contact_list set client_id='".$c_lient_Id."',contact_list_name='".$listName."',list_create_time='".date('Y-m-d h:i:s')."'"; 
				$splistget01 = mysqli_query($conn, $splist01);  
				$listId = mysqli_insert_id($conn); 
			}else{
				$listId=$splistset['list_id'];
			}
			
            $list_fetch="select * from sp_list_details where contact_id='".$spContactId."' and list_id='".$listId."' and client_id='".$c_lient_Id."' and valid=1 and deleted=0";
			$list_set=mysqli_query($conn, $list_fetch);
			$listcontactcount=mysqli_num_rows($list_set);
			
			if($listcontactcount==0){
				$inslistcontact = "insert into sp_list_details set list_id='".$listId."',client_id='".$c_lient_Id."',contact_id ='".$spContactId."'"; 
				mysqli_query($inslistcontact);
			}

			$get_array = array("status"=>"200", "message"=>"Success");				   
        }
		
		//~ else{ 
			//~ $get_array = array("status"=>"505", "message"=>"Invalid User");
		//~ }
	}
	else{
	    $get_array = array("status"=>"506", "message"=>"Something Getting Wrong!");
	}
	
	header('Content-type: application/json');
    echo json_encode($get_array, JSON_UNESCAPED_SLASHES);
	die;
