<?php
include("../includes/global.php");
include("../includes/connect.php");
include("../includes/function.php");

//$skey_client_id = base64_decode($_REQUEST['secret_key']);
$skey_client_id = getPclientIdBySecretKey($_REQUEST['secret_key']); 
$arn_no = $_REQUEST['arn_no'];
$sdate = date('Y-m-d', strtotime($_REQUEST['sd']));
$edate = date('Y-m-d', strtotime($_REQUEST['ed'])); 

if($skey_client_id!=''){
  $deltempleadsql = "DELETE FROM sp_temp_lead_generate WHERE p_client_id='".$skey_client_id."' ";
  $deltemplead_rs = mysql_query($deltempleadsql);
}

$source_ip = get_remote_user_ip();
$ref_url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$leadlogsql = "insert into sp_lead_api_logs set p_client_id='".$skey_client_id."',                        
			soruce_ip='".$source_ip."',
			arn_no='".$arn_no."',
			sdate='".$sdate."',
			edate='".$edate."',
			ref_url='".$ref_url."'";

$leadlog_rs = mysql_query($leadlogsql);

$c_client_arr = getSClientIdByArnNo($skey_client_id,$arn_no); 
$c_client_ids = implode("','", $c_client_arr);
$leadsql = "SELECT * from sp_lead_generate WHERE client_id IN('".$c_client_ids."') AND lead_date BETWEEN '".$sdate."' AND '".$edate."' "; 
$lead_rs = mysql_query($leadsql);

while($lead_row=mysql_fetch_array($lead_rs))
{ 
  if($lead_row['content_id']!=0){
     $solcateg = $lead_row['category'];
     $solcateg_arr = explode(",", $solcateg);     
 
   }

   
   if($lead_row['campaign_source']=='Email'){       
      
      $spmailsql = "SELECT mail_id,categories FROM sp_maildetails WHERE mail_id='".$lead_row['camp_id']."' ";
      $spmail_rs = mysql_query($spmailsql) or die(mysql_error());
      $spmail_row = mysql_fetch_array($spmail_rs);

      $solcateg2 = $spmail_row['categories'];

      if($lead_row['category']!=''){
         $solcateg2 = $solcateg2.",".$lead_row['category'];
      }
      
      $solcateg_arr = explode(",", $solcateg2);     

   }   
  
   array_unique($solcateg_arr);
 
   for($j=0; $j<sizeof($solcateg_arr); $j++){

            $addtemplead2="insert into sp_temp_lead_generate set p_client_id='".$skey_client_id."',
                        client_id='".$lead_row['client_id']."',
			lead_date='".$lead_row['lead_date']."',
			category='".$solcateg_arr[$j]."',
			campaign_source='".$lead_row['campaign_source']."',
			source_plateform='".$lead_row['source_platform']."',
			doe='".date('Y-m-d h:i:s')."'";
           $restemplead2=mysql_query($addtemplead2) or die(mysql_error());

      }

}

$leadtempsql = "SELECT count(*) as total_lead,lead_date,client_id,category,campaign_source,source_plateform from sp_temp_lead_generate WHERE p_client_id = '".$skey_client_id."' group by lead_date,client_id,category,campaign_source,source_plateform "; 
$leadtemp_rs = mysql_query($leadtempsql);
while($leadtemp_row = mysql_fetch_array($leadtemp_rs)){
 $lead_arr[] = $leadtemp_row;
}


for($i=0; $i < sizeof($lead_arr); $i++){

$arn_no = getArnByClientId($lead_arr[$i]['client_id']);
$categories_name = categoryName($lead_arr[$i]['category']);

$get_leads_arr[$i] = array('entryDate'=>date('d-M-Y', strtotime($lead_arr[$i]['lead_date'])), 'arnCode'=>$arn_no, 'leadCount'=>$lead_arr[$i]['total_lead'], 'source'=>$lead_arr[$i]['source_plateform'], 'investmentType'=>$categories_name, 'channelType'=>$lead_arr[$i]['campaign_source']);

}

if(isset($skey_client_id) && isset($sdate) && isset($edate)){
	$get_multi_array = array("status"=>"200","message"=>"success","response"=>$get_leads_arr);
}else{
	$get_multi_array = array("status"=>"501", "message"=>"You are not authorized for this service");
}

header('Content-type: application/json');
echo json_encode($get_multi_array);die;

?>