<?php
include("includes/global.php");
	include("includes/function.php");
	include("includes/global-url.php");
	include("manager/common_functions.php");
	include("csrf/csrf-magic.php");

         $client_id=testInput($_POST['client_id']);
              
        $pc=testInput($_POST['pc']);  

       $mem_query="SELECT * FROM `sp_members` WHERE client_id='".$client_id."' and valid=1 and deleted=0 and approve=1 and company_member_type=1";
       $mem_rs=mysqli_query($conn,$mem_query);
       $mem_data=mysqli_fetch_array($mem_rs); 
       $distri_name=$mem_data['first_name'].' '.$mem_data['last_name'];
       if(!empty($mem_data['person_contact1']))
        {
        $distri_contact=$mem_data['person_contact1'];
        }
       else
        {
        $distri_contact=$mem_data['person_contact2'];
        }
       
        
      
if($pc=='C'){
?>
<p id="lpchild_logo" style="width:auto;font-weight:bold;font-size:16px;color:#000;"><?php echo $distri_name; ?></p>
<p id="lpchild_desc" style="font-size:16px;text-align: justify;"><?php echo $distri_contact; ?></p>
<?php } else { ?>

<img id="lpchild_logo" src="http://<?php echo $CSubdomain; ?>/manager/images/partner-logo.jpg" />
<p id="lpchild_desc" style="font-size:16px;text-align: justify;">Partner Description here</p>
<?php } ?>


