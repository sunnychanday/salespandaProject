<?php 

      include("../includes/global.php");
      include("../includes/connect.php");
      include("../includes/function.php");
      include("manager/common_functions.php");
      
	   ini_set("display_errors", "1");
	   $company_logo=$msg=$display=$newMicroSite="";
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
			
			
			
			$imgName='';
			/*if(isset($_FILES['image']) && $_FILES['image']['tmp_name'])
			{
			$target_dir = "images/";
			$fileName=time().basename($_FILES["image"]["name"]);
			$target_file = $target_dir .$fileName ;
			$uploadOk = 1;
			//$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
			if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file))
			{
				$image=$fileName;
				
			}				
			} */
			
			if(isset($_POST['profile_image']) && $_POST['profile_image']!='')
			{
				$data=$_POST['profile_image'];
				list($type, $imgdata) = explode(';', $data);
				list(, $type) = explode('/', $type);
				
				
				
			if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
				$data = substr($data, strpos($data, ',') + 1);
				$type = strtolower($type[1]); // jpg, png, gif

				if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
					throw new \Exception('invalid image type');
				}

				$data = base64_decode($data);

				if ($data === false) {
					throw new \Exception('base64_decode failed');
				}
				} else {
					throw new \Exception('did not match data URI with image data');
				}
				 $imgName=time()."{$c_lient_Id}.{$type}";
				 file_put_contents("images/{$imgName}", $data);	
				
				 
			}
			$addr['address']=(isset($_POST['address'])) ? $_POST['address']." " : '';
			$addr['city']=(isset($_POST['city'])) ? $_POST['city'].", " : '';
			$addr['pin']=(isset($_POST['pin'])) ? $_POST['pin'].", " : '';
			$addr['country']=(isset($_POST['country'])) ? $_POST['country'] : '';
			$microsite_about=(isset($_POST['microsite_about'])) ? htmlentities($_POST['microsite_about']) : '';
			
			$microsite_address=$addr['address'].$addr['city'].$addr['pin'].$addr['country'];
			$microsite_address2=json_encode(array('address'=>@$_POST['address'],'city'=>@$_POST['city'],'pin'=>@$_POST['pin'],'country'=>@$_POST['country']));
			
			if($imgName=='') {
			$subdomainqry=mysql_query($q="update sp_microsite set microsite_address='".$microsite_address."',
																microsite_address2='".$microsite_address2."',
																microsite_about='".$microsite_about."'
			where client_id='".$c_lient_Id."'");
			}else {
			$subdomainqry=mysql_query($q="update sp_microsite set microsite_address='".$microsite_address."',
																microsite_address2='".$microsite_address2."',
																profile_img='".$imgName."',
																microsite_about='".$microsite_about."'
			where client_id='".$c_lient_Id."'");	
			}
			
			 if($subdomainqry==true)
			 {
				 $microsql=mysql_query($q="select cms_subdomain_url from sp_subdomain where client_id='".$c_lient_Id."'");
				$microData=mysql_fetch_assoc($microsql);
				
				$newMicroSite="https://".$microData['cms_subdomain_url'];
				 //header("location:https://".$microData['cms_subdomain_url']."/index_new.php"); 
				 
			 }else {
				 $msg="Details not updated";
			 }
			
			
                     
		}
		
		
		
			
		}
       
	   
       $countmicrosite=0;
		if(isset($_GET['semail']) && $_GET['semail']!='') {
		$email=$_GET['semail'];
		//$subdomainqry=mysql_query($q="select sp_subdomain.client_id,sp_members.person_email,concat(sp_members.first_name,' ',sp_members.last_name) as name from sp_subdomain left join sp_members on sp_members.client_id=sp_subdomain.client_id where sp_subdomain.cms_subdomain_url='".$_SERVER['HTTP_HOST']."' and sp_subdomain.valid=1 and sp_subdomain.deleted=0 and sp_subdomain.status=1");$subdomainqry=mysql_query($q="select sp_subdomain.client_id,sp_members.person_email,concat(sp_members.first_name,' ',sp_members.last_name) as name from sp_subdomain left join sp_members on sp_members.client_id=sp_subdomain.client_id where sp_subdomain.cms_subdomain_url='".$_SERVER['HTTP_HOST']."' and sp_subdomain.valid=1 and sp_subdomain.deleted=0 and sp_subdomain.status=1");
		$subdomainqry=mysql_query($q="select sp_subdomain.client_id,sp_members.person_email,concat(sp_members.first_name,' ',sp_members.last_name) as name,cms_subdomain_url from sp_subdomain left join sp_members on sp_members.client_id=sp_subdomain.client_id where sp_members.person_email='".$email."' and sp_subdomain.valid=1 and sp_subdomain.deleted=0 and sp_subdomain.status=1");
	   $countmicrosite=mysql_num_rows($subdomainqry);
       $subdomainget=mysql_fetch_array($subdomainqry);
	  
       $c_lient_Id=$subdomainget['client_id'];
	   $email=$subdomainget['person_email'];
	   $name=$subdomainget['name'];
	   $cms_subdomain_url=$subdomainget['cms_subdomain_url'];
       
		}
       
       
	   if($countmicrosite>0)
       {
		$pc_member_info = getPCMemberInfo($c_lient_Id);
		$pcmember_pc_type = $pc_member_info['member_pc_type'];
        $p_client_id = $pc_member_info['p_client_id']; 
        
        
		 if(!empty($c_lient_Id))
		 {
            $QrySelect="select * from sp_microsite where client_id='".$c_lient_Id."'";
            $QrySelectset=mysql_query($QrySelect);
            $QryselectCount=mysql_num_rows($QrySelectset);
            $QrySelectget=mysql_fetch_array($QrySelectset);
            
            if($QryselectCount==0)
            {
              $ins_sql3  = "INSERT into sp_microsite
                        (`client_id`, `slide1_img`,`slide2_img`, `slide3_img`, `slide1_headline`, `slide2_headline`, `slide3_headline`, `slide1_paragraph`, `slide2_paragraph`, `slide3_paragraph`,`microsite_about`,`theme_bg`,`microsite_address`,`doe`)
                        select '".$c_lient_Id."', `slide1_img`,`slide2_img`, `slide3_img`, `slide1_headline`, `slide2_headline`, `slide3_headline`, `slide1_paragraph`, `slide2_paragraph`, `slide3_paragraph` , `microsite_about`,`theme_bg` , `microsite_address`, '".date('Y-m-d H:i:s')."' from sp_microsite where p_client_id = '".$p_client_id."'";
                       
					   
					   mysql_query($ins_sql3); 
                        
                        //$refer='https://'.$_SERVER['HTTP_HOST'];
                        
                        //echo "<meta http-equiv='refresh' content='0;URL=$refer'>";
            }
            
             }
        

      
        $microsite = "select * from sp_microsite where client_id ='".$c_lient_Id."' ";   
        $microsite = mysql_query($microsite);  
	    $micrositeData = mysql_fetch_array($microsite);          
        $microsite_address2=json_decode($micrositeData['microsite_address2']);
		 
         
        


		}else
		{
		$msg='<div class="col-sm-12" style="text-align: center; margin-top:30px!important;">Invalid Email</div>';
	   $display="hidden";   
		}
	
	   $ButsubSection ='';
	  if(@$QrySelectget['new_site_flag']==1) {  $subSection="hidden"; } else { $ButsubSection="hidden"; } 
	  
?>




<!DOCTYPE html>
<html lang="en">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<!--
    <link rel="shortcut icon" href="http://www.salespanda.com/images/favicon.ico" />
-->
   
    <title></title>
     <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<!--
   <script src="assets/global/plugins/jquery.min.js" type="text/javascript"></script>
-->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
   
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
 <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">-->
  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Montserrat" />
  <link href="manager/crop-script/components/imgareaselect/css/imgareaselect-default.css" rel="stylesheet" media="screen">
<link rel="stylesheet" href="manager/crop-script/css/jquery.awesome-cropper.css">
<link rel="stylesheet" href="manager/assets/global/css/hdfc/components-rounded.min.css">


  
  
 

 
 <link rel="stylesheet" href="css/style.css">
 
<style>


  header.white-transparent 
  {
    height: 97px;
     background: #ffffff;
    // padding: 26px;
  }

.contact-form .section-field input,textarea 
{
	color:#5b5151!important;
}

@media only screen and (min-device-width : 460px) and (max-device-width : 700px) 
{
    .fullimg { width:100%!important;
left:0px!important;
margin:0% 5%!important;}

 .img-thumbnail
 {
  width:100%!important;   
 }
 .clogo
 {
   width:50%!important;     
 }
  
 .plogo
 {
   width:50%!important;     
 } 
    
}



</style>
</head>
<body>
  <header id="main-header" class="white-transparent ng-scope menu-sticky">
    <div class="container">
        <div class="row" style="width:100%;">
            <div class="col-md-6">
                <nav class="navbar navbar-expand-lg navbar-light">
                   
                        <img class="clogo" src="img/hdfcmf-logo.png"  class="img-fluid" alt="" style="width:150px;">
                       
                </nav>
            </div>
			<div class="col-md-6">
                 <img class="plogo" src="img/mf-online-logo.png"  class="img-fluid float-right" alt="" style="width:150px;">
                       
            </div>
            
        </div>
    </div>
  </header>
    
 
  

<div class="main-content ng-scope">
    
     
     <section id="sp-blog" class="overview-block-ptb grey-bg sp-blog">
        <div class="container" >
			
            <div class="row <?php echo $display; ?>" style="margin-top:30px!important;" >
                <div class="col-lg-6 col-md-12">
				<div class="col-sm-12">
				<p style="color:#103880; text-align:center;"><?php echo $msg; 
			 if($newMicroSite!='')
			 {
				 echo "Thank you for submitting your contact details! You will be redirected to your business website.";
				 header("Refresh: 3; url={$newMicroSite}");
				//sleep (20);
				 //header("location:{$newMicroSite}");
			 }
			?> </p>
				<div id="topSection" class="<?php echo $subSection;?>">
                    <div class="heading"><h5 class="title sp-tw-6 sp-font-green">This is how your Business Website will look:</h5> </div>
					
					<div class="col-sm-12" style="font-family: Segoe, 'Segoe UI', 'DejaVu Sans', 'Trebuchet MS', Verdana, 'sans-serif'; font-size: 16px; font-weight: bold; color: #58595b; background-color:#e3e3e3; line-height:2;">
					<div class="col-sm-1"><img src="https://resources.mutualfundpartner.com/img/a.png" width="22" height="22" alt="1"></div>
					<div class="col-sm-11">Banner</div>
					</div>
					<p style="font-family: Segoe, 'Segoe UI', 'DejaVu Sans', 'Trebuchet MS', Verdana, 'sans-serif'; font-size: 15px; line-height: 23px; padding-top: 40px;">This is section that your investor would see once they visit your website. A great section to post all the latest updates and catch all the attention. </p>
					<div class="col-sm-12" style="font-family: Segoe, 'Segoe UI', 'DejaVu Sans', 'Trebuchet MS', Verdana, 'sans-serif'; font-size: 16px; font-weight: bold; color: #58595b; background-color:#e3e3e3; line-height:2;">
					<div class="col-sm-1"><img src="https://resources.mutualfundpartner.com/img/2.png" width="22" height="22" alt="2"></div>
					<div class="col-sm-11">Bio Section</div>
					</div>
					<p style="font-family: Segoe, 'Segoe UI', 'DejaVu Sans', 'Trebuchet MS', Verdana, 'sans-serif'; font-size: 15px; line-height: 23px; padding-top: 40px;">An investor would like to know it’s distributor before buying anything from them. Write something about yourself in this section and let your investors know about you.</p>
					<div class="col-sm-12" style="font-family: Segoe, 'Segoe UI', 'DejaVu Sans', 'Trebuchet MS', Verdana, 'sans-serif'; font-size: 16px; font-weight: bold; color: #58595b; background-color:#e3e3e3; line-height:2;">
					<div class="col-sm-1"><img src="https://resources.mutualfundpartner.com/img/3.png" width="22" height="22" alt="3"></div>
					<div class="col-sm-11">Content Showcase</div>
					</div>
					<p style="font-family: Segoe, 'Segoe UI', 'DejaVu Sans', 'Trebuchet MS', Verdana, 'sans-serif'; font-size: 15px; line-height: 23px; padding-top: 40px;">Show your investors compliance approved content for all your latest products in this section. </p>
<div class="col-sm-12" style="font-family: Segoe, 'Segoe UI', 'DejaVu Sans', 'Trebuchet MS', Verdana, 'sans-serif'; font-size: 16px; font-weight: bold; color: #58595b; background-color:#e3e3e3; line-height:2;">
					<div class="col-sm-1"><img src="https://resources.mutualfundpartner.com/img/4.png" width="22" height="22" alt="4"></div>
					<div class="col-sm-11">Contact us Form with Google Map</div>
					</div>
					<p style="font-family: Segoe, 'Segoe UI', 'DejaVu Sans', 'Trebuchet MS', Verdana, 'sans-serif'; font-size: 15px; line-height: 23px; padding-top: 40px;">Capture all your leads and enquries through this Contact Form.  
</p>
					
					<p style="font-family: Segoe, 'Segoe UI', 'DejaVu Sans', 'Trebuchet MS', Verdana, 'sans-serif'; font-size: 15px; padding-left: 15px; "><a href="JavaScript:void(0);" style="color:#1683c2" data-toggle="modal" data-target="#temCodition"><strong>Terms and Conditions</strong></a></p>
					<div class="form-check" style=" border-radius: 20px;font-family: Segoe, 'Segoe UI', 'DejaVu Sans', 'Trebuchet MS', Verdana, 'sans-serif'; font-size: 16px; font-weight: bold; color: #fff; background-color:#004b8c; line-height:3; ">
				  <input class="form-check-input require" type="checkbox" value="1" id="defaultCheck1" style="margin-left: 40px; margin-top: 12px; padding-left: 71px;">
				  
				  <p style="font-family: Segoe, 'Segoe UI', 'DejaVu Sans', 'Trebuchet MS', Verdana, 'sans-serif'; font-size: 12px; padding-left: 70px; "> 
					Yes, I wish to have a Business Website and agree to the terms and conditions
				  </p></div>
				  <div id="formbuilder_lead" style="display:none;font-size:16px;color:red;"></div>
				  <button name="agree" type="submit" value="Agree" style="background:#1683c2;" id="agree" class="button sp-mt-15">Submit</button>
					
				</div>
					<div class="<?php echo $ButsubSection;?>" id="bottomSection">
                    
                    <div><span style="font-size:18px; margin-left:-12px;"><strong>Congrats <?php echo $name; ?>! It’s almost done.</strong></span>
					</br> <strong>Please fill the below details to personalise your Business Website.</strong></div>
                    <form id="contact" method="post"  class="ng-pristine ng-valid" enctype="multipart/form-data">
					<input type="hidden" name="c_lient_Id" id="c_lient_Id" value="<?php echo $c_lient_Id;?>" />
					<input type="hidden" name="action" id="action" value="updateInfo" />
					<input type="hidden" name="email" name="email" value="<?php echo $email;?>" />
                        <div class="contact-form">
						
							<div class="section-field textarea">
							<label for="microsite_about">About You</label>
                                <textarea id="microsite_about" class="input-message require" placeholder="About yourself" rows="7" name="microsite_about"> <?php echo ($micrositeData['microsite_about']=='') ? $name." is a Financial Distributor of HDFC Mutual Fund, one of the largest mutual funds and well-established fund house in the country with focus on delivering consistent fund performance across categories since the launch of the first scheme(s) in July 2000. " : $micrositeData['microsite_about']; ?></textarea>
                            </div>
							<div class="section-field">
								<!--<label for="uploadImage">Upload Photo</label>
                                <input id="uploadImage" type="file"  title="Upload Photo" accept="image/*" name="image" />-->
								
								<div style="float:left; max-height:285px; overflow:hidden; width:100%;">
									 
									  <?php 
									  
									  $crop_image=$micrositeData['profile_img'];
									  if($crop_image!='') { ?>
									  <input id="sample_input" type="hidden" name="profile_image">
									  <img id='base64image' class="img-thumbnail" src="images/<?php echo $crop_image; ?>" />
									  <?php } else { ?>
                                    <input id="sample_input" type="hidden" title="Upload Photo" name="profile_image">
                                    <img class="img-thumbnail" src="manager/images/no-image-available.gif" border="0" />
                                     <?php 
                                     }	?>								  
									</div>
                                    <div> Image minimum size (230 * 230) </div>
                                                                         
                             
									  <span id="img_show" style="color:red;display:none;">Please select thumbnail image.</span>    
									
                            </div>
                            
							<div class="section-field">
							<label for="address">Address</label>
                                <input class="require" id="address" type="text" value="<?php echo @$microsite_address2->address; ?>" placeholder="Street" name="address">
                            </div>
							<div class="section-field">
							
                                <input class="require" id="city" type="text" value="<?php echo @$microsite_address2->city; ?>" placeholder="City,State" name="city">
                            </div>
							<div class="section-field">
							
                                <input class="require" id="pin" type="text" value="<?php echo @$microsite_address2->pin; ?>" placeholder="Pin Code" name="pin">
                            </div>
							<div class="section-field">
							
                                <input class="require" id="country" type="text" value="<?php echo @$microsite_address2->country; ?>" placeholder="Country" name="country">
                            </div>
                            
                            <br>
                            
                            <button name="save"  style="background:#1683c2;"  id="send-value" class="button sp-mt-15">Submit</button>
                            
                        </div>
                    </form>
                </div>
                </div>
				</div>
				
				
				
				
				<div class="fullimg" class="col-md-12">
                    <img src="https://resources.mutualfundpartner.com/img/microsite-view.jpg" alt="">
                </div>
            </div>
            
             
            
             </div>
    </section>
   
    
</div>
<!-- === Main Content End === --></div></div>




<div ui-view="footer" class="ng-scope" id="sp-contact">
<footer class="dark-bg ng-scope">
    <div class="sp-footer">
        <div class="container">
            
                <div class="col-lg-12 col-md-12 sp-mlr-60">
                    <img src="img/salespanda.png"  class="img-fluid" alt="" style="width:150px; margin-top:25px; margin-bottom:25px;">
                </div>
             
        </div>
    </div>
    
</footer>

</div>



</div>
</div>
</div>

<div id="temCodition" class="modal fade" role="dialog" style="display:none;">
					  <div class="modal-dialog" style="width:60%!important;">
						<div class="modal-header" style="background-color: #ffff !important;">
					<button type="button" class="close" data-dismiss="modal">×</button>
					<h4 class="modal-title" style="font-size:20px;font-weight:bold;"><strong>TERMS AND CONDITIONS</strong></h4>
				</div>
						<div class="modal-content">
						  <div class="modal-body">
							
<p>These terms and conditions (“<strong>Terms</strong>”) are applicable to "<strong><?php echo $name;?></strong>" having its address at "<?php echo (@$micrositeData['microsite_address']=='') ? "___________" :$micrositeData['microsite_address']; ?>" (hereinafter referred to as the “<strong>Distributor</strong>”, which expression shall unless repugnant to the context and meaning thereof be deemed to mean and include its administrators and permitted assigns) that access, use or browse "<strong><?php echo ($cms_subdomain_url=='') ? "___________" :$cms_subdomain_url; ?>"</strong>  for Distributors (also referred to as “Microsite”) and associated websites, webpages, online properties, tools, and weblinks provided for digital marketing solution via web and app (collectively, the “Portal”). 
</p><p>In favor of:
</p><p>Bizight Solutions Private Limited, incorporated under the Companies Act, 1956 having its corporate office at 105, Chiranjiv Towers, 43, Nehru Place, Delhi -110019 and registered office at A3, FF, S-515, Greater Kailash II, New Delhi, Delhi – 110048 hereinafter referred to as “<strong>Bizight</strong>” (which term shall unless the content otherwise provides include its successors and permitted assigns) 
</p><p>The Distributor hereby irrevocably and unconditionally agrees and acknowledges that it has read, understood and agreed to be bound by the following terms and conditions, without any limitation or qualification, and shall comply with all applicable laws and regulations in relation thereto. 

</p><p><strong>1.	Acceptance of the Terms </strong>
</p><p>1.1	The Distributor hereby confirms that it has voluntarily accessed the Portal and hereby agrees to the use of the same in accordance with these Terms. The Distributor understands and confirms that by (i) ticking/clicking the “I agree” or similar icon/tab/checkbox on the online system/Portal or on a URL/ weblink; as well as by (ii) entering the one time password sent by Bizight to the Distributor, the Distributor has given its acceptance of these Terms and it is bound by the Terms hereof. 

</p><p><strong>2.	Services </strong>
</p><p>2.1	Bizight owns, maintains and operates the Portal for the purpose of use and access of the Distributor(s) that are empaneled and registered with HDFC Mutual Fund (“HDFC MF” or “MF”)/HDFC Asset Management Company Limited (“HDFC AMC” or “AMC”) and its Investors (defined hereinafter).
</p><p>2.2	The services offered under this Portal are to facilitate the Distributors to reach out to their investors more efficiently and to effectively distribute the offerings of HDFC AMC/HDFC MF.
</p><p>2.3	This micro site provided under this solution may in future also enable the Distributor to initiate transactions on behalf of the Investors.

</p><p><strong>3.	Disclaimer </strong>
</p><p>3.1	The Distributor understands that the Portal is being made available/ provided to the Distributor by Bizight and that HDFC AMC/ HDFC MF has no role in the same and that the entire arrangement contained in these Terms including the access and use of the Portal by the Distributor and the interface by the Distributor with the Investor (defined hereinafter) is solely between Bizight and the Distributor. 
</p><p>3.2	The Distributor hereby understands that the Portal is being independently hosted by Bizight on its own server and that the same is not hosted on the server or systems of the AMC/ MF and the AMC/ MF has no role in the use or functioning of the same. 
</p><p>3.3	The Distributor hereby further understands and acknowledges that no information being provided by the Investor to the Distributor on the Portal is accessed or made available to HDFC AMC and the AMC/MF is not responsible for any information or data shared by the Investor or accessed or used by the Distributor or any other party and the Distributor shall be solely liable for the same. 
</p><p>
</p><p><strong>4.	Portal Access and Security</strong>
</p><p>4.1	The Portal can be accessed only by those Distributors who have are registered and empaneled with HDFC AMC/ HDFC MF and have been issued valid login credentials to access the Portal by Bizight.
</p><p>4.2	The Distributor are responsible for managing its account access, password, and any other information for logging into the Portal and as a part of the Portal security features. The Distributor must have a valid username and password for purposes of accessing the Portal. It is the responsibility of the Distributor to treat such information as confidential, and to protect against disclosure to third parties. The Distributor would be solely responsible for any and all activities that occur under its account. The Distributor agrees to notify Bizight immediately of any unauthorized use of the Portal or any other breach of security whatsoever that the Distributor may be aware of.
</p><p>4.3	HDFC AMC/ HDFC MF shall not be liable for any loss that the Distributor may incur as a result of an unauthorized third party using its passwords or accounts, either with or without its knowledge.
</p><p>4.4	The Distributor acknowledges and understands that this is an electronic service being offered by Bizight to the Distributor and the access of the Portal cannot always be guaranteed to the Distributor. In any event, the Distributor shall not hold HDFC AMC/ HDFC MF responsible for any interruptions, access to or use of this Portal, its contents, services or any part thereof if interrupted or for any error, virus or harmful components in the Portal or the server. 
</p><p>4.5	The Portal and its features may be expanded, limited or modified at any time to meet the needs of its Distributors, or for technical or other reasons, without advance notice or reason. Access to the Portal or part thereof may be changed, modified, suspended, added, removed, or access without prior notice to the Distributor. The Portal may also be discontinued, temporarily or permanently, without notice. 
</p><p>4.6	The Distributor shall be liable for the compatibility of the materials with its own computer software. To access some of the content or features of the Portal or its Services, Distributors may need to enhance or update the hardware or software in their computer systems. The Distributors are responsible for making all requisite arrangements necessary for their access to the Portal, and shall not hold any other party liable for its failure to access the Portal or any services, partially or fully, whether due to the Distributor's system, the internet network or any other cause whatsoever.
</p><p>4.7	The Distributor shall be responsible for implementing sufficient procedures and checkpoints to satisfy any requirements for integrity, security and accuracy of data input and output, and for maintaining a means external to the Portal for the reconstruction of any lost data. HDFC MF, HDFC AMC, HDFC Trustee Company Limited, or affiliates’ or group companies or any other connected persons, including, but not limited to, the directors, sponsors, officers, or employees assumes no responsibility for any damages to, viruses that may infect, or services, repairs or corrections that must be performed, on its computer or other property on account of accessing or using this Portal.
</p><p>4.8	The permission to access the Portal will not convey any proprietary or ownership rights in the above software/ hardware. The Distributor agrees that he shall not attempt to modify, translate, disassemble, decompile or reverse engineer the software/ hardware underlying the Portal or create any derivative product based on the software/ hardware.
</p><p>4.9	Any failure or breach by the Distributor or any of its employees or representatives, including any misrepresentation or fraud or gross negligence, shall be deemed to be a breach by the Distributor vis-à-vis HDFC AMC/ HDFC MF and/or the Investor and the Distributor shall be solely responsible for the same. 
</p><p>4.10	The Distributor shall comply with all requirements under applicable law and any other regulatory requirements including in relation to personal data and the processing thereof. 
</p><p>4.11	The Distributor understands and acknowledges that the internet per se is susceptible to a number of frauds, misuse, hacking and other actions which could affect my systems. There can be no guarantee or assure or certify any security against such internet frauds, hacking and other actions which could affect the Distributor’s systems. The Distributor understands and acknowledges that it will be availing/ using the Portal and services at its own risk, value and assessment. The AMC/MF shall not be responsible for breach of security, fraud, data loss, data pilferage, data security compromises, or any mechanical or security failures, in any manner whatsoever. 

</p><p><strong>5.	Use of Portal</strong>
</p><p>5.1	The use of the Portal by the Distributor should not bring the name and reputation of HDFC AMC/ HDFC MF or any of its products into disrepute. The Distributor hereby agrees that it shall not misrepresent any information of HDFC AMC/ HDFC MF and shall in no event provide any faulty, inaccurate or unauthorised information to the Investor. 
</p><p>5.2	All copyrights and other intellectual property rights in the materials on the Portal belong to the AMC/ MF and while accessing the Portal, the Distributors are deemed to have acknowledged the said copyright and other proprietary rights where they exist in any material provided. Distributor acknowledges that, by using the Portal, it will not gain any ownership interest in the Portal. Distributor further acknowledges and agrees that all title and rights in and to any third party content that is incorporated into the Portal is the property of the respective content owners and may be protected by applicable patent, copyright, trademark or other intellectual property laws.
</p><p>5.3	The Distributor acknowledges that HDFC AMC/ HDFC MF do not own and hold the rights or the ownership to the Portal and all the information and data available on the Portal and the Distributor is granted a limited license to access, view, download, print and share certain materials posted on the Portal solely for the purpose of enhancing the services that the Distributor provide to the Investors and for facilitating its performance of its obligations as a Distributor more efficiently. The Distributor agrees not to reproduce, duplicate, copy, alter, modify, transmit, create derivative works of, publish, sublicense, distribute, circulate, disassemble, decompile, reverse engineer or re-sell the Portal or any part of the Portal. The Distributor shall not misuse the information relating to Investors of HDFC MF available on the Portal and shall use such data or information only for any service-related activities for the Investors such as processing instructions, responding to service requests, resolve any Investor grievances, etc. The Distributor agrees that it shall not take any actions, whether intentional or unintentional, that may circumvent, disable, damage or impair the Portal’s ability to function, or allow or assist any third party to do so. The Distributor agrees not to access, without authority, or interfere with, damage or disrupt: a) any part of the Portal; b) any equipment or network on which the Portal is stored; c) any software used in the provision of the Portal; or d) any equipment or network or software owned or used by any third party.
</p><p>5.4	This Portal or the contents thereof or any facility available under the Portal, including the facility available to the Distributors to send by email to the Investors, shall not be construed as an offer or a solicitation of an offer by HDFC AMC/ HDFC MF to buy and/or sell any products/units of HDFC MF to any person whomsoever. The Distributor acknowledges that HDFC AMC accepts no liability for any email or communication sent by the Distributors from the Portal or for the consequences of any actions taken by any person on the basis of such email or communication or information provided. The Distributor shall solely be responsible for any liability arising out of such email or communication.
</p><p>5.5	The Distributor acknowledges that if the Distributor chooses to access the Portal from any location outside India the Distributor shall do so on its own initiative and would be responsible for compliance with applicable laws and regulations.

</p><p><strong>6.	Third Party Sites</strong>
</p><p>6.1	For the convenience of the Distributors, this Portal may provide links to other websites and resources (each a "Third Party Site"). The Distributor is solely responsible for any responsibility for the availability, contents, products, services or use of any Third Party Site, any website accessed from a Third Party Site or any changes or updates to such sites.
</p><p>6.2	The Distributor understands that, subject to the Terms, the Distributor shall be able to share the information available on the Portal through other Third Party Sites such as facebook, gmail etc. The Distributor understands and acknowledges that it shall be solely liable for the compliances with regard to each of these Third Party Sites. The information available on the Third Party Sites may have certain restrictions on its use or distribution, which differ from the Terms applicable to this Portal. Prior to accessing the contents of such Third Party Sites or using such Third Party Sites, the Distributors are advised to review the terms of use and privacy policies applicable to such websites. The Distributor acknowledges that they bear all risks associated with access to and use of content provided on a Third Party Site and agree that HDFC AMC/ HDFC MF are not responsible for any loss or damage of any sort that any Distributor may incur from dealing with a third party.

</p><p><strong>7.	Accuracy of Information</strong>
</p><p>The Distributor understands and acknowledges that no commitment is being made as regards to the accuracy or completeness of the information on the Portal. No representation, warranty or undertaking is given to the Distributor that the information available on this Portal or the results emanating from any tools/calculators provided on the Portal is accurate, complete, updated, comprehensive, or up to date. In no event will HDFC MF/HDFC AMC be liable, financially or otherwise, to any person for any direct, indirect, special or consequential damages arising out of any use of the information contained on this Portal, including, without limitation, any lost profits, business interruption, or otherwise.

</p><p><strong>8.	No Warranty</strong>
</p><p>The Distributor hereby confirms that no warranty is being made that: (a) the Portal or the contents thereof will meet the requirements of the Distributor; (b) the Portal or the contents thereof will be available on an uninterrupted, timely, secure or error-free basis; (c) the results/statements/data/information that may be obtained from the use of the Portal or any materials offered through the Portal or applications, will be accurate or reliable; and (d) the quality of any services or information obtained by the Distributor through the Portal will meet its expectations.

</p><p><strong>9.	Liability</strong>
</p><p>HDFC AMC/ HDFC MF shall not be liable or bear any responsibility in any manner whatsoever, whether for costs, expenses, loss or damage and whether direct, indirect or consequential and whether economic or otherwise arising from the access of the Distributors of the Portal or from the services and/or facilities provided in the Portal or from the access of the Investors of the Portal or any data provided by the Investors on the Portal or any processing, use or access of such data by the Distributor or any other party. Use of the Portal and the services/facilities on the Portal is entirely at the risk of the Distributor. The Distributor further acknowledges that HDFC AMC/ HDFC MF shall not be responsible or liable in any manner whatsoever for any illegal, anti-social, objectionable, speculative, immoral activities or purposes, on account or violation of/or intrusion of privacy, misuse of any personal information including mobile number, name, age, sex, address, whereabouts, IP address, location, interests, etc. of the Investor and the Distributor shall solely be liable for compliances in relation to the same.

</p><p><strong>10.	Indemnification</strong> 
</p><p>The Distributor shall, save harmless the HDFC AMC/ HDFC MF, against and make good to them, any and all losses, damages, liabilities, suits, claims, counterclaims, actions, penalties, expenses (including any stamp duty, attorney's fees and court costs and any expenses incurred by the any of them for the enforcement of this clause), which any of them shall suffer as a result of any breach of my warranties, representations, covenants, undertakings or agreement contained herein or any claim made by any person or Investor in relation to any use of the Portal by the Distributor and anything in relation thereto.

</p><p><strong>11.	Privacy Policy </strong>
</p><p>The Distributor hereby agrees and confirms that the privacy policy of the Distributor (“Privacy Policy”) shall be hosted on the Portal. The Distributor hereby further confirms that any access to the Portal by any person shall be governed by the said Privacy Policy and the Distributor shall be fully responsible for any processing of personal data or sensitive personal data or information shared on the Portal or received by the Distributor on the Portal either directly or through Bizight. 

</p><p><strong>12.	Bizight’s Covenants </strong>
</p><p>12.1	Bizight hereby agrees and confirms that it shall not store, use, re-produce, copy, manipulate, distort, share, any information that is uploaded, shared, accessed, derived or received by Bizight or the Distributor on the Portal (“Information”), other than to the extent of sharing the same with the Distributor. 

</p><p>13.	Additional terms and conditions specific to certain facilities made available on the Portal may be stipulated and any Distributor availing of such facilities shall be bound by such terms and conditions in addition to these Terms. If the Distributor does not intend to be legally bound by these Terms, the Distributor shall refrain from accessing or using this Portal. These Terms may be modified at any time, and any Distributor continuing to use the Portal thereafter shall be bound by the revised Terms. Further, if any of the points in these Terms or any disclaimers or notices hosted on the Portal are found to be unenforceable under applicable law, that will have no bearing on the enforceability of the rest of the terms thereof.

</p><p><strong>14.	Termination</strong>
</p><p>14.1	The Distributor are permitted to access the Portal solely on account of the Distributor being empaneled with HDFC AMC/ HDFC MF as a Distributor. The rights of access to the Portal and the facilities/services provided on the Portal shall at any time be terminated without any notice thereof upon occurrence of the following events: (i) the termination of the distribution agreement/ empanelment agreement or any such agreement of a similar nature by whatever name called, between the Distributor with HDFC AMC/ HDFC MF; and/or (ii) restriction or termination of access of the Portal by Bizight; or (iii) upon discovery that the Distributor have been in breach of these Terms; or (iv) for any other reason considered appropriate by Bizight.
</p><p>14.2	On termination the Distributor will delete all the materials and all copies of the same from its computers and confirm to Bizight that this has been done and the Distributor will have no further right to use them for any purpose whatsoever.

</p><p><strong>15.	Compliance with laws</strong>
</p><p>The Distributor undertakes to comply and be bound by all applicable laws and statutory requirements in India. Any dispute arising out of the use of this Portal and/or any of the services/facilities offered through the Portal shall be subject to the non-exclusive jurisdiction of the Courts in Mumbai, India. 
</p>
						  </div>
						</div>
					  </div>
					</div>
					
<script>

$(document).ready(function(){ 
var k=jQuery.noConflict();
k("#agree").on("click",function(){
	
	if(k("#defaultCheck1").prop("checked")==false)
	{
		
		//k("#formbuilder_lead").html("Please accept the aggrement");
		k("#formbuilder_lead").show().text('Please agree to the terms and conditions to proceed.').fadeIn( 300 ).delay( 3000 ).fadeOut( 800 );
		return false;
	}
	var email="<?php echo $email;?>";
	var c_lient_Id="<?php echo $c_lient_Id;?>";
	k.ajax({url:"<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/declaration.php",
        type: "post",
        data: {action:'flag',c_lient_Id:c_lient_Id,email:email},
        cache: false,
        crossDomain : true,
		success:function(result)
        {
		k("#topSection").css("display",'none');
		k("#bottomSection").removeClass('hidden');
        }
});
	

 
});

});



/*$('#contact').submit(function() {
	var k=jQuery.noConflict();
	
	var data=k("#contact").serialize();
k.ajax({url:"<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/declaration.php",
        type: "post",
        data: data,
        cache: false,
        crossDomain : true,
		success:function(result)
        {
			alert(result);
			return false;
        k("#topSection").css("display",'none');
		k("#bottomSection").removeClass('hidden');
        }
});
return false;
}); */

 
    
    
    

    


  




</script>
<script src="manager/crop-script/components/imgareaselect/scripts/jquery.imgareaselect.js"></script> 
<script src="manager/crop-script/build/jquery.awesome-cropper.js"></script>
<script>

    $(document).ready(function () {
	var im=jQuery.noConflict();
	var a = document.getElementById('img_validate');
    
        im('#sample_input').awesomeCropper(

        { width: 200, height: 200, debug: true }

        );
		im(".fileinput-new").html("Select your Profile Picture");
		//$("span").removeClass("green");
		im( "span" ).removeClass( "btn green btn-file mbottom10" ).addClass( "btn btn-file mbottom10" );
		

    });
	

</script> 

<script>

	var microsite=1;</script>
 <?php include("includes/footer-event.php"); ?>
</body></html>


