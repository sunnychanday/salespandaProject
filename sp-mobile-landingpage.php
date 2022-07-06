<?php
/*Author name: <Bizight Solutions Pvt Ltd> 
 * Purpose of document/ page : <This is page is used to collect the landing page data.> 
 * Date: 17-02-2021 
 * Copyright info : <Copyright @2021, Bizight Solutions Pvt Ltd>
*/

header('Access-Control-Allow-Origin: *');
include("includes/global.php");
include("includes/function.php");
include("manager/common_functions.php");
//print_r($_SESSION); exit;
include("csrf/csrf-magic.php");

  
   $screen_width=$_POST['screen_width'];
   $page_id=testInput($_POST['page_id']);
   $client_id=testInput($_POST['client_id']);   
   $semail=decode(testInput($_POST['semail']));
   
   $customerID=testInput($_POST['customerID']);
   $channeltype=explode('&',$_REQUEST['channeltype']);
  
    
   $sqry="select * from sp_subdomain where client_id='".$client_id."'";
   $resq=mysqli_query($conn,$sqry) or die(mysqli_error());
   $domianData=mysqli_fetch_array($resq);	
   $subdomain_url=$domianData['subdomain_url'];
   $cms=$domianData['cms_subdomain_url'];

   $redirectPath='https://'.$subdomain_url;
   
   $child_edit_status = getChildSyndLpageEditStatus($client_id,$page_id);

   $pc_member_info = getPCMemberInfo($client_id);

   $pcmember_pc_type = $pc_member_info['member_pc_type'];
   $p_client_id = $pc_member_info['p_client_id']; 

   $screen_width=$_POST['screen_width'];
   

               
                    if($pcmember_pc_type=='C'){ 
                      
                    
                            $cta_set=mysqli_query($conn,"select cta_download_url from cta_button where landingpage_id='".$page_id."' and client_id='".$p_client_id."' and button_id!='0' and valid=1 and deleted=0");
                           
		                while($cta_get=mysqli_fetch_array($cta_set)){
                            $cta_download_url =$cta_get['cta_download_url'];
            
                            
                        }
                        
                       
                        $pathPdf='https://'.$cms."/upload/casestudy/$client_id/";
                    }
                 
                    if($pcmember_pc_type=='C'){ 
                        
                        mysqli_set_charset($conn,"utf8");
                            $pageset=mysqli_query($conn,"select publish_content,mobile_content from sp_landingpage_publish where publish_page_id='".$page_id."' and client_id='".$p_client_id."'");
                             
                    }
                    else 
                    {   mysqli_set_charset($conn,"utf8");
                        $pageset=mysqli_query($conn,"select publish_content,mobile_content from sp_landingpage_publish where publish_page_id='".$page_id."' and client_id='".$client_id."'");
                    }   
                    
    		        $pageget=mysqli_fetch_array($pageset);
                    $landingpage_content=$pageget['publish_content'];
                    $mobile_content=$pageget['mobile_content'];
                    if($screen_width<720)
                    {
                        echo $mobile_content;
                    }
                    else
                    {
                        echo $landingpage_content;
                    } 
                    
                    
                      
              $getAttempts = getAttempts('LandingPage');
              
             if( isset($getAttempts['attempts']) && $getAttempts['attempts']>=2)
             { 
                                    
        ?>

<script src='https://www.google.com/recaptcha/api.js'></script>   
<script>
mk('<div id="recaptcha_alert" style="display:none;font-size:16px;color:red;">Invalid captcha value.</div><div class="g-recaptcha" data-sitekey="6LfOw84ZAAAAAGJnefvSkZm7zxjFksg3TkdWpo9H"></div>').insertBefore( "#btndrag" ); 
</script>
<?php } ?>
<script>
mk(document).ready(function(){
mk(".popover").remove();
mk(".lightbox-area").remove();
mk(".ui-resizable-handle").remove();
var c="<?php echo $cobrandstr; ?>";
var semail="<?php echo $semail; ?>";
var customerID="<?php echo $customerID; ?>";
var channeltype="<?php echo $channeltype[0]; ?>";
var client_id="<?php echo $client_id; ?>";

mk("#email").val(semail);
 

if(customerID!='' && channeltype=="app")
 {
 mk("#cust_id").val(customerID); 
 mk("#cust_id").attr('readonly', 'readonly');
 }

       

var d="<?php echo $pcmember_pc_type; ?>";
 var al="<?php echo $pathPdf; ?>"; 
 var beetle=mk('input[name="beetle"]').val();
 mk("input").removeAttr('disabled');
 mk("#textcount").find('div[id^="textbox"]').attr('contenteditable','false');
 mk("#textcount").find('div[id^="headbox"]').attr('contenteditable','false');
 mk(".errspan").remove(); 
 mk("#lpchild_footer").html('');
 
mk(document).on('click','div[id^="beta"]',function() {
var client_id='<?php echo $client_id; ?>';  
var childedit_status=parseInt('<?php echo $child_edit_status; ?>');
var a=mk(this).attr('id');
var getcta=mk("#" + a).attr("data-url");
var b=mk("#" + a).find('a').attr("href");
var k = b.split("/");
if(mk.trim(getcta)=='btnurl')
{
window.open(b,"_blank");    
}
else if(mk.trim(getcta)=='')
{
mk('div[id^="beta"]').find('a').css('pointer-events','auto');  
}
else
{
 //mk("#" + a).find('a').css('pointer-events','none');
  var beetle=mk('input[name="beetle"]').val();
 mk.ajax({
        url:"https://<?php echo $cms; ?>/sp-ctaclick.php",
        type: "post",
        data: {client_id: client_id,ctaurl: k[6],childedit_status:childedit_status,pctype:d,ctaid:a,beetle:beetle},
       
        cache: false,
         success:function(result)
	   	{
	   	 window.open(result,"_blank");
        }
        });

}
});



     var client_id='<?php echo $client_id; ?>';
     var pc='<?php echo $pcmember_pc_type; ?>';
     var beetle=mk('input[name="beetle"]').val();
    
     mk.ajax({
        url:"https://<?php echo $cms; ?>/sp-childfooter-landingpage.php",
        type: "post",
        data: {client_id: client_id,pc: pc,beetle:beetle},
       
        cache: false,
         success:function(result)
	   	{
           
            if(pc=='C')
                {
            mk("#lpchild_footer").html(result);
              }
            }
        });

 });

function removerefer(del){
mk('#del-refer' + del).remove();
}
</script>