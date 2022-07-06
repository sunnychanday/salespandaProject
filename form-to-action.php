<?php
	include("includes/global.php");
    include("manager/common_functions.php");
	include("geoiploc.php");
    
	$cookie_name="technoch";
	$cookie_value =$_GET['HTTP_REFERER'];
	$extern  = array('facebook','twitter','linkedin','google','pinterest','quora','instagram');
	$persist = false;
	foreach($extern as $ext)
	{
		if(strpos($cookie_value,$ext)>-1)
		{
			$persist = true;
			if(!isset($_COOKIE[$cookie_name])) 
			{
				setcookie($cookie_name,$cookie_value);
			}
			// 86400 = 1 day
		}
	}

	$_COOKIE[$cookie_name];
	
	$urlpath=$_REQUEST['url'];
	//'<br>client_id='.$client_id=$_REQUEST['client_id'];
	$qry="select * from form_to_action_url where valid=1 and deleted=0 and url_name='".$urlpath."' ORDER BY fid ASC";
	$res = mysqli_query($conn, $qry);
	
	while($frmData=mysqli_fetch_array($res))
	{
		$fid.=$frmData['fid'].',';

	}
	
	'<br>AllURL='.$allfid=substr($fid,0,-1);

	$ip= CommonStaticFunctions::get_remote_user_ip();
	$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$referral_url=$_COOKIE[$cookie_name];
    
    if(empty($referral_url)) 
	{
		'<br>referral_url= '.$referral_url="direct";
	}  
    
   	if($_REQUEST['m']!='')
	{
		'<br>A= '.$newvarfrm=$_REQUEST['m'];
		'<br>A1= '.$formPositionmsg=trim($newvarfrm);
		'<br>A1= '.$qrynp="select * from form_temp where id='".$formPositionmsg."'";
		
		$resultnp=mysqli_query($conn, $qrynp);
		$rownp=mysqli_fetch_array($resultnp);
		$tmsg= $rownp['thanku_code'];

		$set_tmsg = str_replace("<q>","'",$tmsg);
		//$set_new = str_replace("position=fixed","position:fixed",$set_tmsg);
		$get_thanks  = htmlspecialchars_decode($set_tmsg);
		$get_form="";
	}
	
	$qry="select * from form_to_action_url where valid=1 and deleted=0 and url_name='".$urlpath."' ORDER BY fid ASC";
	$res=mysqli_query($conn, $qry);
	
	while($frmData=mysqli_fetch_array($res))
	{	
		$form_id=$frmData['fid'];
		$qryc="select * from form_to_action where id='".$form_id."' and valid=1 and deleted=0";
		$resc=mysqli_query($conn, $qryc);
		
		if(mysqli_num_rows($res)>0)
		{
			$frmDatac=mysqli_fetch_array($resc);
			$full_form=$frmDatac['form_script'];
            $frm_position=$frmDatac['frm_position'];
            $client_id=$frmDatac['client_id'];
            
            $target_city=$frmDatac['target_city'];
            $dfid=$frmDatac['form_default_id'];
            $doe = date("Y-m-d");
           
			$chk_frmcount="select * from form_click_counter where form_url='".$urlpath."' and form_id='".$form_id."' and client_id='".$client_id."' and doe='".$doe."' and valid=1 and deleted=0";
			$get_frmcount=mysqli_query($conn, $chk_frmcount);
			$setCount_frmcount=mysqli_num_rows($get_frmcount);
			
			if($setCount_frmcount==0)
			{
				$addcmp="insert into form_click_counter set frm_position='".$frm_position."',form_id='".$form_id."', client_id='".$client_id."',doe='".$doe."',form_url='".$urlpath."'";
				$rescmp=mysqli_query($conn, $addcmp);
				$form_counter_id=mysqli_insert_id($conn);
			}
            
            if(!empty($target_city)) 
			{
				'<br>ip_location= '.$ip_location=getCountryFromIP($ip, " NamE ");
             
			} 
			else
			{
				'<br>ip_location= '.$ip_location="";
			}
   
			///////////////////////////Trigger Start//////////////////////////////////////////
			$qryc_loadpopup = "select * from form_to_action where FIND_IN_SET(id, '".$allfid."') and frm_position='pop-up-page-load'";
			$resc_loadpopup = mysqli_query($conn, $qryc_loadpopup);
			$frmDatac_loadpopup = mysqli_fetch_array($resc_loadpopup);
            $scroll_loadpopup = $frmDatac_loadpopup['pick_trigger_scroll'];
            $idle_loadpopup = $frmDatac_loadpopup['pick_trigger_idle'];
            $time_idle_loadpopup = $idle_loadpopup * 1000;

			$qryc_rightcenter = "select * from form_to_action where FIND_IN_SET(id, '".$allfid."') and frm_position='bottom-right'";
			$resc_rightcenter = mysqli_query($conn, $qryc_rightcenter);
			$frmDatac_rightcenter = mysqli_fetch_array($resc_rightcenter);
            $scroll_rightcenter=$frmDatac_rightcenter['pick_trigger_scroll'];
            $idle_rightcenter=$frmDatac_rightcenter['pick_trigger_idle'];
            $time_rightcenter= $idle_rightcenter * 1000;
            
            $load_rightcenter=$frmDatac_rightcenter['pick_trigger_load'];
    
            $qryc_leftcenter="select * from form_to_action where FIND_IN_SET(id, '".$allfid."') and frm_position='left-center'";
			$resc_leftcenter=mysqli_query($conn, $qryc_leftcenter);
			$frmDatac_leftcenter=mysqli_fetch_array($resc_leftcenter);
            
            $scroll_leftcenter=$frmDatac_leftcenter['pick_trigger_scroll'];

            $idle_leftcenter=$frmDatac_leftcenter['pick_trigger_idle'];
            $time_leftcenter= $idle_leftcenter * 1000;
            
            $load_leftcenter=$frmDatac_leftcenter['pick_trigger_load'];


			$qryc_topbar="select * from form_to_action where FIND_IN_SET(id, '".$allfid."') and frm_position='top-bar'";
			$resc_topbar=mysqli_query($conn, $qryc_topbar);
			$frmDatac_topbar=mysqli_fetch_array($resc_topbar);
            
            $scroll_topbar=$frmDatac_topbar['pick_trigger_scroll'];
            
            $idle_topbar=$frmDatac_topbar['pick_trigger_idle'];
            $time_topbar= $idle_topbar * 1000;

            $load_topbar=$frmDatac_topbar['pick_trigger_load'];

            $chk_frmpos="select * from form_click_counter where form_url='".$urlpath."' and form_id='".$form_id."' and client_id='".$client_id."' and doe='".$doe."' and valid=1 and deleted=0";
		    $get_frmpos=mysqli_query($conn, $chk_frmpos);
			
			$frm_pos = array();   
			while($frmPosData=mysqli_fetch_array($get_frmpos)){
				$frm_pos[] = $frmPosData['frm_position'];
		    }
			
			/////////////////////////// Trigger Start ////////////////////////////////////////
            $target_url=$frmDatac['target_url'];
            $target_traffic=$frmDatac['target_traffic'];
            $referral_link1=explode(",",$target_traffic);
            $referral_link = array("$referral_link1[0]", "$referral_link1[1]", "$referral_link1[2]" , "$referral_link1[3]" , "$referral_link1[4]","$referral_link1[5]","$referral_link1[6]","$referral_link1[7]","$referral_link1[8]");

            $referral_country1=explode(",",$target_city);
            $referral_country= array("$referral_country1[0]", "$referral_country1[1]", "$referral_country1[2]" , "$referral_country1[3]" , "$referral_country1[4]" , "$referral_country1[5]" , "$referral_country1[6]" , "$referral_country1[7]" ,"$referral_country1[8]" , "$referral_country1[9]");

 	
            $set_form1 = str_replace("<q>","'",$full_form);
			$set_form = str_replace("position=fixed","position:fixed",$set_form1);
			?>
			<html>
			<head>
			<style>
			.gynbg
			{
				position: fixed;left: 0;
				right: 0;
				top: 0;
				bottom: 0;
				height: 100%;
				width: 100%;
				background-color: rgba(0,0,0,0.4);
				z-index: 2999;
			}

			.arrow_up11{background:url(<?php echo $sitepath;?>webcontent/images/arrowup-14x14.png) no-repeat!important;}
			.arrow_down11{background:url(<?php echo $sitepath; ?>webcontent/images/arrowdown-14x14.png) no-repeat!important;}

			@media all and (min-width: 250px) and (max-width: 1030px) {
			  div.m_display{
				display: none;
			  }
			   div.popup_display{
				width: 50%;
			  }

				
			}
			</style>

			<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	
			<?php
			if(in_array("bottom-right", $frm_pos)){
				?>
				<script type="text/javascript">
					$(document).ready(function(){
						var countUsed5 = 0;
						var count_new5; 
						var frm_position5 = 'bottom-right';
						var urlpath5 = '<?php echo $urlpath; ?>';
						var allfid5 = '<?php echo $allfid; ?>'; 
						var load_rightpopup='<?php echo $load_rightcenter; ?>';
						var scroll_rightpopup ='<?php echo $scroll_rightcenter; ?>';
						var delay_rightpopup='<?php echo $time_rightcenter; ?>';

						if(load_rightpopup!='' || scroll_rightpopup!='' || delay_rightpopup!=0)
						{
							var isOpen = false;
						}
						else
						{
							var isOpen = true;
						}

						$('.right_closebtn,.arrow-test').click(function(){
							if(isOpen) 
							{
								if(countUsed5 == 1)
								{
									count_new5 = 0;
								}
								else
								{
									count_new5 = 1;
									countUsed5 = 1
								} 
							 
								$('.right_slide').animate({bottom:'0px'});
								$('.arrow-check').addClass('arrow_down11').removeClass('arrow_up11');
								$('#counter_1').html();
								
								$.ajax({url:"<?php echo $sitepath; ?>webcontent/window-popup-count.php",
									type: "post",
									cache: false,
									data: 'count1='+ count_new5 + '&url='+ urlpath5 + '&frm_position1='+ frm_position5 + '&allFid='+ allfid5,
									success:function(result){ 
										$('#counter_1').show().html(result);
									}
								});
								isOpen = !isOpen;	
							}
							else
							{
								var hg= $('.right_slide').height();
								var hg1=51;
								var hg2=hg1-hg;
								//alert(hg2);
								$('.right_slide').animate({bottom:hg2});
								
								$('.arrow-check').addClass('arrow_up11').removeClass('arrow_down11');
								isOpen = !isOpen;
							}
						});
					});
				</script>
				<?php
			}
			
			if(empty($scroll_loadpopup) && in_array("pop-up-page-load", $frm_pos)){
				?>
				<script type="text/javascript">
					var countUsed2 = 0;
					var count_new2;   
					var frm_position_load = 'pop-up-page-load';
					var urlpath2 = '<?php echo $urlpath; ?>';
					var allfid2 = '<?php echo $allfid; ?>';         
					var time= parseInt('<?php echo $time_idle_loadpopup; ?>');
					//alert(time11);
					
					setTimeout(function(){
						$('#popupshow').show();
						$(".gynbg").show();
						
						if(countUsed2 == 1)
						{
							count_new2 = 0;
						}
						else
						{
							count_new2 = 1;
							countUsed2 = 1
						}
 
						$.ajax({url:"<?php echo $sitepath; ?>webcontent/window-popup-count.php",
							type: "post",
							cache: false,
							data: 'count1='+ count_new2 + '&url='+ urlpath2 + '&frm_position1='+ frm_position_load + '&allFid='+ allfid2,
                        });
					},time)
				</script>
				<?php
			}
			?>
			
			<script type="text/javascript">
				function popup_close(){
					$("#popupshow").remove();
					$(".gynbg").remove();
				}
			</script>

			<script type="text/javascript">
				function popup_leftclose(){
					$("#feedback").hide();
				}
			</script>

			<script type="text/javascript">
				function popup_rightclose(){
					$(".right_slide").hide();
				}
			</script>

			<?php
			if((!empty($time_rightcenter) || !empty($load_rightcenter)) && in_array("bottom-right", $frm_pos)) {
				?>
				<script type="text/javascript">
					var countUsed1 = 0;
					var count_new1;   
					var frm_position_right = 'bottom-right';
					var urlpath1 = '<?php echo $urlpath; ?>';
					var allfid1 = '<?php echo $allfid; ?>';         
					var time11=parseInt('<?php echo $time_rightcenter; ?>');
					//alert(time11);
					setTimeout(function(){
						$('.right_slide').show().animate({bottom:'0px'});
						$('.arrow-check').addClass('arrow_down11').removeClass('arrow_up11');
						
						if(countUsed1 == 1)
						{
							count_new1 = 0;
						}
						else
						{
							count_new1 = 1;
							countUsed1 = 1
						} 
				 
						$.ajax({url:"<?php echo $sitepath; ?>webcontent/window-popup-count.php",
							type: "post",
							cache: false,
							data: 'count1='+ count_new1 + '&url='+ urlpath1 + '&frm_position1='+ frm_position_right + '&allFid='+ allfid1,	
						});
					}, time11)
				</script>

				<?php
			}else{
				?>
				<script type="text/javascript">
					setTimeout(function(){
						var hgnew= $('.right_slide').height();
						var hgnew1=51;
						var hgnew2=hgnew1-hgnew;
						$(".right_slide").show().css({bottom:hgnew2});
						$('.arrow-check').addClass('arrow_up11').removeClass('arrow_down11');
					},1000)
				</script>
				<?php
			}
			
			if((!empty($time_leftcenter) || !empty($load_leftcenter)) && in_array("left-center", $frm_pos)){
				?>
				<script type="text/javascript">
					var countUsed = 0;
					var count_new;   
					var frm_position_left = 'left-center';
					var urlpath = '<?php echo $urlpath; ?>';
					var allfid = '<?php echo $allfid; ?>';         
					var time12=parseInt('<?php echo $time_leftcenter; ?>');
					//alert(time11);
					setTimeout(function(){
						$('#feedback').show().animate({left:'0px'});
						if(countUsed == 1)
						{
							count_new = 0;
						}
						else
						{
							count_new = 1;
							countUsed = 1
						} 
				  
						$.ajax({url:"<?php echo $sitepath; ?>webcontent/window-popup-count.php",
							type: "post",
							cache: false,
							data: 'count1='+ count_new + '&url='+ urlpath + '&frm_position1='+ frm_position_left + '&allFid='+ allfid,
							
						});
					}, time12)
				</script>
				<?php
			} else {
				?>
				<script type="text/javascript">
					setTimeout(function(){
						$("#feedback").show();
					},1000)
				</script>
				<?php
			}
			
			if((!empty($time_topbar) || !empty($load_topbar)) && in_array("top-bar", $frm_pos)) {
				?>
				<script type="text/javascript">
					var countUsed8 = 0;
					var count_new8;   
					var frm_position8 = 'top-bar';
					var urlpath8 = '<?php echo $urlpath; ?>';
					var allfid8 = '<?php echo $allfid; ?>';         
					var time18=parseInt('<?php echo $time_topbar; ?>');
					setTimeout(function(){
						$('.fixedheader').show();
						if(countUsed8 == 1)
						{
							count_new8 = 0;
						}
						else
						{
							count_new8 = 1;
							countUsed8 = 1
						} 
				  
						$.ajax({url:"<?php echo $sitepath; ?>webcontent/window-popup-count.php",
							type: "post",
							cache: false,
							data: 'count1='+ count_new8 + '&url='+ urlpath8 + '&frm_position1='+ frm_position8 + '&allFid='+ allfid8,
						});
					}, time18)
				</script>
				<?php
			}

			if(in_array("left-center", $frm_pos)){
				?>
				<script type="text/javascript">
					$(document).ready(function(){
						var countUsed7 = 0;
						var count_new7;           
						var frm_position7 = 'left-center';
						var urlpath7 = '<?php echo $urlpath; ?>';
						var allfid7 = '<?php echo $allfid; ?>'; 
						var isOpen_left = false;

						$('.pull_feedback').click(function(){
							if (isOpen_left) {              
								if(countUsed7 == 1)
								{
									count_new7 = 0;
								}
								else
								{
									count_new7 = 1;
									countUsed7 = 1
								} 

								$('#feedback').animate({left:'-250px'});
								$.ajax({url:"<?php echo $sitepath; ?>webcontent/window-popup-count.php",
									type: "post",
									cache: false,
									data: 'count1='+ count_new7 + '&url='+ urlpath7 + '&frm_position1='+ frm_position7 + '&allFid='+ allfid7,
								});
								isOpen_left = !isOpen_left;
							}
							else
							{
								$('#feedback').animate({left:'0px'});	
								isOpen_left = !isOpen_left;
							}
						});
					});
					
				</script>
				<?php
			}
			?>

			<script type="text/javascript">
				$(document).ready(function(){
					var isopen_bar = true;
					$('.plus').click(function(){
						if (isopen_bar){  
							$('.plus').html("<img src='<?php echo $sitepath; ?>webcontent/manager/images/arrow_down.png'>");
							$('.slidingDiv').hide();
							isopen_bar = !isopen_bar;
						}
						else
						{
							$('.plus').html("<img src='<?php echo $sitepath; ?>webcontent/manager/images/arrow_up.png'>");
							$('.slidingDiv').show();
							isopen_bar = !isopen_bar;
						}
					});
				});
			</script>

			<script type="text/javascript">
				function submit_form(val)
				{
					var fname = $("#first_name_"+val).val();
					var lname = $("#last_name_"+val).val();
					var email = $("#email_id_"+val).val();
					var cmpny_name = $("#company_name_"+val).val();
					var contact = $("#mobile_"+val).val();
					var referral_url = $("#referral_url_"+val).val();
					$("#leadpopup_content_"+val).html('');
					
					var isValid = true;
					$("#email_id_"+val).each(function() {
						if ($.trim($(this).val()) == '') {
							isValid = false;
							$(this).css({
								"border": "2px solid red",
							   
							});
						}
						else
						{
							$(this).css({
								"border": "",
								"background": ""
							});
						}
					});
					
					$("#first_name_"+val).each(function() {
						if ($.trim($(this).val()) == '') {
							isValid = false;
							$(this).css({
								"border": "2px solid red",
							   
							});
						}
						else
						{
							$(this).css({
								"border": "",
								"background": ""
							});
						}
					});
  
					$("#last_name_"+val).each(function() {
						if ($.trim($(this).val()) == '') {
							isValid = false;
							$(this).css({
								"border": "2px solid red",
							   
							});
						}
						else {
							$(this).css({
								"border": "",
								"background": ""
							});
						}
					});
					
					$("#company_name_"+val).each(function() {
						if ($.trim($(this).val()) == '') {
							isValid = false;
							$(this).css({
								"border": "2px solid red",
                   
							});
						}
						else
						{
							$(this).css({
								"border": "",
								"background": ""
							});
						}
					});

					$("#mobile_"+val).each(function() {
						if ($.trim($(this).val()) == '') {
							isValid = false;
							$(this).css({
								"border": "2px solid red",
							   
							});
						}
						else {
							$(this).css({
								"border": "",
								"background": ""
							});
						}
					});

					if (isValid == false) 
					{
						e.preventDefault();
					}
					else 
					{
						$.ajax({url:"<?php echo $sitepath; ?>webcontent/form-submit-action.php",
							type: "post",
							data: 'fname1='+ fname + '&lname1='+ lname+ '&email1='+ email + '&cmpny_name1='+ cmpny_name + '&contact1=' + contact + '&form_id=' + val + '&referral_url1=' + referral_url,
							cache: false,
							beforeSend: function() {
								$("#leadpopup_content_"+val).html('Please wait..');
							},
							success:function(result){
								$("#first_name_"+val).hide();
								$("#last_name_"+val).hide();
								$("#email_id_"+val).hide();
								$("#company_name_"+val).hide();
								$("#mobile_"+val).hide();
								$("#submit_"+val).hide();
								$("#text_"+val).hide();
								$("#sub_text_"+val).hide();
								$("#feedback_hide_"+val).hide();
								$("#leadpopup_content_"+val).show().html(result);
							}
						});
					}
				}
			</script>

			<?php
			if(!empty($scroll_loadpopup) && in_array("pop-up-page-load", $frm_pos)){
				?>
				<script type="text/javascript">
					$(document).ready(function(){
						var countUsed4 = 0;
						$(window).scroll(function(){
							var count_new4;
							var frm_position4 = 'pop-up-page-load';
							var urlpath = '<?php echo $urlpath; ?>';
							var allfid = '<?php echo $allfid; ?>';  

							var scroll_loadpopup = parseInt('<?php echo $scroll_loadpopup; ?>');
							//alert(scroll_loadpopup);   
							
							if ($(this).scrollTop()  >= $(document).height() / scroll_loadpopup) {
								if(countUsed4 == 1)
								{
									count_new4 = 0;
								}
								else
								{
									count_new4 = 1;
									countUsed4 = 1
								}
                     
								$('#popupshow').show();
								$(".gynbg").show();

								$('#counter_1').html();
									$.ajax({url:"<?php echo $sitepath; ?>webcontent/window-popup-count.php",
									type: "post",
									cache: false,
									data: 'count1='+ count_new4 + '&url='+ urlpath + '&frm_position1='+ frm_position4 + '&allFid='+ allfid,
								});
               
							} else {
								$('#popupshow').hide();
								$(".gynbg").hide();
							}
						});
					});
				</script>

				<?php
			}

			if(!empty($scroll_leftcenter) && in_array("left-center", $frm_pos)){
				?>
				<script type="text/javascript">
					$(document).ready(function(){
						var countUsed6 = 0;
						$(window).scroll(function(){
							var scroll_leftcenter = parseInt('<?php echo $scroll_leftcenter; ?>');
							//alert(scroll_leftcenter);
							var count_new6;
							var frm_position6 = 'left-center';
							var urlpath = '<?php echo $urlpath; ?>';
							var allfid = '<?php echo $allfid; ?>';  
							
							if ($(this).scrollTop()  >= $(document).height() / scroll_leftcenter) {
								if(countUsed6 == 1)
								{
									count_new6 = 0;
								}
								else
								{
									count_new6 = 1;
									countUsed6 = 1
								}
								
								$('#feedback').css({left:'0px'});
                         
								$.ajax({url:"<?php echo $sitepath; ?>webcontent/window-popup-count.php",
									type: "post",
									cache: false,
									data: 'count1='+ count_new6 + '&url='+ urlpath + '&frm_position1='+ frm_position6 + '&allFid='+ allfid,
									 
									
								});
                        
							} else {
								$('#feedback').css({left:'-250px'});
                      
							}
						});
					});
				</script>

				<?php
			}

			if(!empty($scroll_rightcenter) && in_array("bottom-right", $frm_pos)){
				?>
				<script type="text/javascript">
					$(document).ready(function(){
						var countUsed3 = 0;
						$(window).scroll(function(){
							var scroll_rightcenter = parseInt('<?php echo $scroll_rightcenter; ?>');
							var count_new3;
							var frm_position3 = 'bottom-right';
							var urlpath = '<?php echo $urlpath; ?>';
							var allfid = '<?php echo $allfid; ?>';
							
							if ($(this).scrollTop()  >= $(document).height() / scroll_rightcenter) 
							{
								if(countUsed3 == 1)
								{
									count_new3 = 0;
								}
								else
								{
									count_new3 = 1;
									countUsed3 = 1
								}
                      
								$('.right_slide').css({bottom:'0px'});
								$('.arrow-check').addClass('arrow_down11').removeClass('arrow_up11');
								
								$.ajax({url:"<?php echo $sitepath; ?>webcontent/window-popup-count.php",
									type: "post",
									cache: false,
									data: 'count1='+ count_new3 + '&url='+ urlpath + '&frm_position1='+ frm_position3 + '&allFid='+ allfid,
                        
								});
							} else {
								var sg= $('.right_slide').height();
								var sg1=51;
								var sg2=sg1-sg;
								//alert(sg2);
								$('.right_slide').css({bottom:sg2});
								$('.arrow-check').addClass('arrow_up11').removeClass('arrow_down11');
                      
							}
						});
					});
				</script>
				<?php
			}

			if(!empty($scroll_topbar) && in_array("top-bar", $frm_pos)){
				?>
				<script type="text/javascript">
					$(document).ready(function(){
						var countUsed9 = 0;
						$(window).scroll(function(){
							var scroll_topbar = parseInt('<?php echo $scroll_topbar; ?>');
							var count_new9;
							var frm_position9 = 'top-bar';
							var urlpath = '<?php echo $urlpath; ?>';
							var allfid = '<?php echo $allfid; ?>';
            
							if ($(this).scrollTop()  >= $(document).height() / scroll_topbar) 
							{
								if(countUsed9 == 1)
								{
									count_new9 = 0;
								}
								else
								{
									count_new9 = 1;
									countUsed9 = 1
								}
                      
								$('.fixedheader').show();
								$.ajax({url:"<?php echo $sitepath; ?>webcontent/window-popup-count.php",
									type: "post",
									cache: false,
									data: 'count1='+ count_new9 + '&url='+ urlpath + '&frm_position1='+ frm_position9 + '&allFid='+ allfid,                       
								});
							} else {
								$('.fixedheader').hide();
                        
							}
						});
					});
				</script>
				<?php
			}
			?>
			</head>
			<body>
				<?php
				if(((in_array($referral_url, $referral_link , true) and in_array($ip_location, $referral_country , true))) or ($target_url==$referral_url)){
					?>
					<form name="frmone" id="frmone_<?php echo $form_id; ?>" method="post">
						<input type="hidden" id="referral_url_<?php echo $form_id; ?>" value="<?php echo $urlpath; ?>">
						<?php echo $get_form  = htmlspecialchars_decode($set_form); ?>
					</form>
					<?php
				}
				?>
			</body>
			</html>
			<?php
		}
	}
