<?php 

      include("../includes/global.php");
      include("../includes/connect.php");
      include("../includes/function.php");
      include("manager/common_functions.php");
      
	   ini_set("display_errors", "1");
		error_reporting(E_ALL);
        
		
		if(isset($_POST['action']))
		{
		$email=$_POST['email'];
		$c_lient_Id=$_POST['c_lient_Id'];
		$action=$_POST['action'];
		if($action=='flag' && $c_lient_Id!='' && $email!='')
		{
			
			$subdomainqry=mysql_query($q="select client_id from sp_members where client_id='".$c_lient_Id."' and person_email='".$email."' and valid=1 and deleted=0 and approve=1");
			$countmicrosite=mysql_num_rows($subdomainqry);
			if($countmicrosite>0)
			{
			$ins_sql3  = "update  sp_microsite set new_site_flag=1 where client_id='".$c_lient_Id."'";
			mysql_query($ins_sql3);
			echo "sucess";
			die();
			}else {
			echo "Invalid details";
			die();			
			}
                     
		}else if($action=='updateInfo' && $c_lient_Id!='' && $email!='')
		{
			
			$subdomainqry=mysql_query($q="select client_id from sp_members where client_id='".$c_lient_Id."' and person_email='".$email."' and valid=1 and deleted=0 and approve=1");
			$countmicrosite=mysql_num_rows($subdomainqry);
			if($countmicrosite>0)
			{
			//$ins_sql3  = "update  sp_microsite set new_site_flag=1 where client_id='".$c_lient_Id."'";
			//mysql_query($ins_sql3);
			echo "sucess";
			die();
			}else {
			echo "Invalid details";
			die();			
			}
                     
		}
		
		
		
			
		}
       
	   
	  
?>





