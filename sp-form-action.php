<?php
	$c_lient_Id = $_REQUEST['client_id'];
	include("includes/global.php");
	include("includes/function.php");
	include("geoiploc.php");
	include("includes/check_package_balance.php");

	header('Access-Control-Allow-Origin: *');

	$pc_member_info = getPCMemberInfo($c_lient_Id);
	$pcmember_pc_type = $pc_member_info['member_pc_type'];
	$p_client_id = $pc_member_info['p_client_id'];

	if ($viewExceeded == 0){
		$cookie_name = "technoch";
		$cookie_value = $_GET['HTTP_REFERER'];
		$extern = array('facebook', 'twitter', 'linkedin', 'google', 'pinterest', 'quora', 'instagram');
		$persist = false;

		foreach ($extern as $ext) {
			if (strpos($cookie_value, $ext) > -1) {
				$persist = true;
				if (!isset($_COOKIE[$cookie_name])) {
					setcookie($cookie_name, $cookie_value);
				}
				// 86400 = 1 day
			}
		}

		$_COOKIE[$cookie_name];
		$urlpath = $_REQUEST['url'];

		/** Added By Softprodigy for Url Modification on Jan17,2017*** */
		$parsed = parse_url($urlpath);
		$string = '';
		if (isset($parsed['query']) && $parsed['query'] != '') {
			parse_str($parsed['query'], $_params);
			unset($_params['emailTkn']);
			unset($_params['camp']);
			$parsed['query'] = $_params;
			$string = http_build_query($parsed['query']);
		}

		$urlpath = $parsed['scheme'] . '://' . $parsed['host'] . $parsed['path'];
		if (isset($string) && $string != '') {
			$urlpath .= '?' . $string;
		}
		/** *End Added by Softprodigy for Url Modification on Jan17,2017* */

		if ($pcmember_pc_type == 'C') {
			$client_id = $p_client_id;
			$c_client_id = $_REQUEST['client_id'];
		} else {
			$client_id = $_REQUEST['client_id'];
		}

		if ($pcmember_pc_type == 'C') {
			$qry = "select * from form_to_action_url where valid=1 and deleted=0 and (url_name='" . mysqli_real_escape_string($conn, $urlpath) . "' or url_web='" . mysqli_real_escape_string($conn, $urlpath) . "') and client_id='" . $c_client_id . "' ORDER BY fid ASC";
		} else {
			$qry = "select * from form_to_action_url where valid=1 and deleted=0 and (url_name='" . mysqli_real_escape_string($conn, $urlpath) . "' or url_web='" . mysqli_real_escape_string($conn, $urlpath) . "') and client_id='" . $client_id . "' ORDER BY fid ASC";
		}
		
		$res = mysqli_query($conn, $qry);
		while ($frmData = mysqli_fetch_array($res)) {
			$fid.=$frmData['fid'] . ',';
		}

		'<br>AllURL=' . $allfid = substr($fid, 0, -1);

		$ip = get_remote_user_ip();
		$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$referral_url = $_COOKIE[$cookie_name];

		if (empty($referral_url)) {
			'<br>referral_url= ' . $referral_url = "direct";
		}

		if ($_REQUEST['m'] != '') {
			'<br>A= ' . $newvarfrm = $_REQUEST['m'];
			'<br>A1= ' . $formPositionmsg = trim($newvarfrm);

			'<br>A1= ' . $qrynp = "select * from form_temp where id='" . $formPositionmsg . "'";
			$resultnp = mysqli_query($conn, $qrynp);
			$rownp = mysqli_fetch_array($resultnp);
			$tmsg = $rownp['thanku_code'];

			$set_tmsg = str_replace("<q>", "'", $tmsg);
			//$set_new = str_replace("position=fixed","position:fixed",$set_tmsg);
			$get_thanks = htmlspecialchars_decode($set_tmsg);
			$get_form = "";
		}

		if ($pcmember_pc_type == 'C') {
			$qry = "select * from form_to_action_url where valid=1 and deleted=0 and (url_name='" . mysqli_real_escape_string($conn, $urlpath) . "' or url_web='" . mysqli_real_escape_string($conn, $urlpath) . "') and client_id='" . $c_client_id . "' ORDER BY fid ASC";
		} else {
			$qry = "select * from form_to_action_url where valid=1 and deleted=0 and (url_name='" . mysqli_real_escape_string($conn, $urlpath) . "' or url_web='" . mysqli_real_escape_string($conn, $urlpath) . "') and client_id='" . $client_id . "' ORDER BY fid ASC";
		}
		
		$res = mysqli_query($conn, $qry);
		while ($frmData = mysqli_fetch_array($res)) {
			$form_id = $frmData['fid'];

			if ($pcmember_pc_type == 'C') {
				$qryc = "SELECT E.*,ES.* FROM sp_engagement_syndication as ES INNER JOIN form_to_action as E ON ES.engmnt_form_id = E.id where E.id='" . $form_id . "' and ES.c_client_id = '" . $c_client_id . "' and ES.approve=1 and E.syndication_status=1 and E.valid=1 and E.deleted=0 and ES.valid=1 and ES.deleted=0";
			} else {
				$qryc = "SELECT * FROM form_to_action where id='" . $form_id . "' and client_id='" . $client_id . "' and status=1 and valid=1 and deleted=0";
			}

			$resc = mysqli_query($conn, $qryc);

			if (mysqli_num_rows($res) > 0) {
				$frmDatac = mysqli_fetch_array($resc);
				$full_form = $frmDatac['form_script'];
				$frm_position = $frmDatac['frm_position'];
				$client_id = $frmDatac['client_id'];
				$target_city = $frmDatac['target_city'];
				$dfid = $frmDatac['form_default_id'];
				$doe = date("Y-m-d");

				$chk_frmcount = "select * from form_click_counter where form_url='" . $urlpath . "' and form_id='" . $form_id . "' and client_id='" . $_REQUEST['client_id'] . "' and doe='" . $doe . "' and valid=1 and deleted=0";
				$get_frmcount = mysqli_query($conn, $chk_frmcount);
				$setCount_frmcount = mysqli_num_rows($get_frmcount);
				
				if ($setCount_frmcount == 0) {
					$addcmp = "insert into form_click_counter set frm_position='" . $frm_position . "',form_id='" . $form_id . "',frm_click_count='1', client_id='" . $_REQUEST['client_id'] . "',doe='" . $doe . "',form_url='" . $urlpath . "'";
					$rescmp = mysqli_query($conn, $addcmp);
					$form_counter_id = mysqli_insert_id($conn);
				}

				if (!empty($target_city)) {
					'<br>ip_location= ' . $ip_location = getCountryFromIP($ip, " NamE ");
				} else {
					'<br>ip_location= ' . $ip_location = "";
				}

				///////////////////////Trigger Start///////////////////////////

				if ($pcmember_pc_type == 'C') {
					$qryc_loadpopup = "SELECT E.*,ES.* FROM sp_engagement_syndication as ES INNER JOIN form_to_action as E ON ES.engmnt_form_id = E.id where FIND_IN_SET(E.id, '" . $allfid . "') and frm_position='pop-up-page-load' and ES.c_client_id = '" . $c_client_id . "' and ES.approve=1 and E.syndication_status=1 and E.valid=1 and E.deleted=0 and ES.valid=1 and ES.deleted=0";
				} else {
					$qryc_loadpopup = "select * from form_to_action where FIND_IN_SET(id, '" . $allfid . "') and status=1 and frm_position='pop-up-page-load'";
				}
				
				$resc_loadpopup = mysqli_query($conn, $qryc_loadpopup);
				$frmDatac_loadpopup = mysqli_fetch_array($resc_loadpopup);

				$scroll_loadpopup = $frmDatac_loadpopup['pick_trigger_scroll'];

				$idle_loadpopup = $frmDatac_loadpopup['pick_trigger_idle'];
				$time_idle_loadpopup = $idle_loadpopup * 1000;

				if($pcmember_pc_type == 'C') {
					$qryc_rightcenter = "SELECT E.*,ES.* FROM sp_engagement_syndication as ES INNER JOIN form_to_action as E ON ES.engmnt_form_id = E.id where FIND_IN_SET(E.id, '" . $allfid . "') and frm_position='bottom-right' and ES.c_client_id = '" . $c_client_id . "' and ES.approve=1 and E.syndication_status=1 and E.valid=1 and E.deleted=0 and ES.valid=1 and ES.deleted=0";
				} else {
					$qryc_rightcenter = "select * from form_to_action where FIND_IN_SET(id, '" . $allfid . "') and status=1 and frm_position='bottom-right'";
				}

				$resc_rightcenter = mysqli_query($conn, $qryc_rightcenter);
				$frmDatac_rightcenter = mysqli_fetch_array($resc_rightcenter);
				$scroll_rightcenter = $frmDatac_rightcenter['pick_trigger_scroll'];
				$idle_rightcenter = $frmDatac_rightcenter['pick_trigger_idle'];
				$time_rightcenter = $idle_rightcenter * 1000;
				$load_rightcenter = $frmDatac_rightcenter['pick_trigger_load'];

				if ($pcmember_pc_type == 'C') {
					$qryc_leftcenter = "SELECT E.*,ES.* FROM sp_engagement_syndication as ES INNER JOIN form_to_action as E ON ES.engmnt_form_id = E.id where FIND_IN_SET(E.id, '" . $allfid . "') and frm_position='left-center' and ES.c_client_id = '" . $c_client_id . "' and ES.approve=1 and E.syndication_status=1 and E.valid=1 and E.deleted=0 and ES.valid=1 and ES.deleted=0";
				} else {
					$qryc_leftcenter = "select * from form_to_action where FIND_IN_SET(id, '" . $allfid . "') and status=1 and frm_position='left-center'";
				}

				$resc_leftcenter = mysqli_query($conn, $qryc_leftcenter);
				$frmDatac_leftcenter = mysqli_fetch_array($resc_leftcenter);

				$scroll_leftcenter = $frmDatac_leftcenter['pick_trigger_scroll'];


				$idle_leftcenter = $frmDatac_leftcenter['pick_trigger_idle'];
				$time_leftcenter = $idle_leftcenter * 1000;

				$load_leftcenter = $frmDatac_leftcenter['pick_trigger_load'];

				if ($pcmember_pc_type == 'C') {
					$qryc_topbar = "SELECT E.*,ES.* FROM sp_engagement_syndication as ES INNER JOIN form_to_action as E ON ES.engmnt_form_id = E.id where FIND_IN_SET(E.id, '" . $allfid . "') and frm_position='top-bar' and ES.c_client_id = '" . $c_client_id . "' and ES.approve=1 and E.syndication_status=1 and E.valid=1 and E.deleted=0 and ES.valid=1 and ES.deleted=0";
				} else {
					$qryc_topbar = "select * from form_to_action where FIND_IN_SET(id, '" . $allfid . "') and status=1 and frm_position='top-bar'";
				}

				$resc_topbar = mysqli_query($conn, $qryc_topbar);
				$frmDatac_topbar = mysqli_fetch_array($resc_topbar);

				$scroll_topbar = $frmDatac_topbar['pick_trigger_scroll'];
				$idle_topbar = $frmDatac_topbar['pick_trigger_idle'];
				$time_topbar = $idle_topbar * 1000;

				$load_topbar = $frmDatac_topbar['pick_trigger_load'];


				//////////////////////Trigger Start////////////////////////

				$target_url = $frmDatac['target_url'];
				$target_traffic = $frmDatac['target_traffic'];
				$referral_link1 = explode(",", $target_traffic);
				$referral_link = array("$referral_link1[0]", "$referral_link1[1]", "$referral_link1[2]", "$referral_link1[3]", "$referral_link1[4]", "$referral_link1[5]", "$referral_link1[6]", "$referral_link1[7]", "$referral_link1[8]");

				$referral_country1 = explode(",", $target_city);
				$referral_country = array("$referral_country1[0]", "$referral_country1[1]", "$referral_country1[2]", "$referral_country1[3]", "$referral_country1[4]", "$referral_country1[5]", "$referral_country1[6]", "$referral_country1[7]", "$referral_country1[8]", "$referral_country1[9]");

				$set_form1 = str_replace("<q>", "'", $full_form);
				$set_form = str_replace("position=fixed", "position:fixed", $set_form1);
				?>
				<html>
					<head>
						<style>
							.gynbg
							{
								position: fixed;left: 0;right: 0; top: 0;bottom: 0; height: 100%; width: 100%;background-color: rgba(0,0,0,0.4);z-index: 2999;
							}
							.arrow_up11{background:url(<?php echo $sitepath; ?>webcontent/images/arrowup-14x14.png) no-repeat!important;}
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
						<script type="text/javascript">
							//mk(document).ready(function(){
							var countUsed5 = 0;
							var count_new5;
							var frm_position5 = 'bottom-right';
							var urlpath5 = '<?php echo $urlpath; ?>';
							var allfid5 = '<?php echo $allfid; ?>';
							var load_rightpopup = '<?php echo $load_rightcenter; ?>';
							var scroll_rightpopup = '<?php echo $scroll_rightcenter; ?>';
							var delay_rightpopup = '<?php echo $time_rightcenter; ?>';

							if (load_rightpopup != '' || scroll_rightpopup != '' || delay_rightpopup != 0)
							{
								var isOpen = false;
							}
							else
							{
								var isOpen = true;
							}
						
							mk('.right_closebtn,.arrow-test').unbind().click(function () {
								if (isOpen)
								{
									if (countUsed5 == 1)
									{
										count_new5 = 0;
									}
									else
									{
										count_new5 = 1;
										countUsed5 = 1
									}

									mk('.right_slide').animate({bottom: '0px'});
									mk('.arrow-check').addClass('arrow_down11').removeClass('arrow_up11');

									mk('#counter_1').html();
									mk.ajax({url: "<?php echo $sitepath; ?>webcontent/window-popup-count.php",
										type: "post",
										cache: false,
										crossDomain: true,
										data: 'count1=' + count_new5 + '&url=' + urlpath5 + '&frm_position1=' + frm_position5 + '&allFid=' + allfid5,
										success: function (result) {
											mk('#counter_1').show().html(result);
										}

									});
									isOpen = !isOpen;
								}
								else
								{
									var hg = mk('.right_slide').height();
									var hg1 = 51;
									var hg2 = hg1 - hg;
									//alert(hg);
									mk('.right_slide').animate({bottom: hg2});
									mk('.arrow-check').addClass('arrow_up11').removeClass('arrow_down11');

									isOpen = !isOpen;
								}
							});


							//});
						</script>

						<?php
						if (empty($scroll_loadpopup)){
							?>
							<script type="text/javascript">
								var countUsed2 = 0;
								var count_new2;
								var frm_position_load = 'pop-up-page-load';
								var urlpath2 = '<?php echo $urlpath; ?>';
								var allfid2 = '<?php echo $allfid; ?>';
								var time = parseInt('<?php echo $time_idle_loadpopup; ?>');
								//alert(time11);
								
								setTimeout(function () {
									mk('#popupshow').show();
									mk(".gynbg").show();
									if (countUsed2 == 1)
									{
										count_new2 = 0;
									}
									else
									{
										count_new2 = 1;
										countUsed2 = 1
									}

									mk.ajax({url: "<?php echo $sitepath; ?>webcontent/window-popup-count.php",
										type: "post",
										cache: false,
										crossDomain: true,
										data: 'count1=' + count_new2 + '&url=' + urlpath2 + '&frm_position1=' + frm_position_load + '&allFid=' + allfid2,
									});
								}, time)
							</script>
							<?php
						}
						?>

						<script type="text/javascript">
							function popup_close() {
								mk("#popupshow").remove();
								mk(".gynbg").remove();
							}
						</script>

						<script type="text/javascript">
							function popup_leftclose() {
								mk("#feedback").hide();
							}
						</script>

						<script type="text/javascript">
							function popup_rightclose() {
								mk(".right_slide").hide();
							}
						</script>

						<?php
						if (!empty($time_rightcenter) || !empty($load_rightcenter)){
							?>
							<script type="text/javascript">
								var countUsed1 = 0;
								var count_new1;
								var frm_position_right = 'bottom-right';
								var urlpath1 = '<?php echo $urlpath; ?>';
								var allfid1 = '<?php echo $allfid; ?>';
								var time11 = parseInt('<?php echo $time_rightcenter; ?>');
								//alert(time11);
								setTimeout(function () {
									mk('.right_slide').show().animate({bottom: '0px'});
									mk('.arrow-check').addClass('arrow_down11').removeClass('arrow_up11');
									if (countUsed1 == 1)
									{
										count_new1 = 0;
									} else
									{
										count_new1 = 1;
										countUsed1 = 1
									}

									mk.ajax({url: "<?php echo $sitepath; ?>webcontent/window-popup-count.php",
										type: "post",
										cache: false,
										crossDomain: true,
										data: 'count1=' + count_new1 + '&url=' + urlpath1 + '&frm_position1=' + frm_position_right + '&allFid=' + allfid1,
									});
								}, time11)
							</script>

							<?php
						}else{
							?>
							<script type="text/javascript">
								setTimeout(function () {
									var hgnew = mk('.right_slide').height();
									var hgnew1 = 51;
									var hgnew2 = hgnew1 - hgnew;
									mk(".right_slide").show().css({bottom: hgnew2});
									mk('.arrow-check').addClass('arrow_up11').removeClass('arrow_down11');
								}, 1000)
							</script>
							<?php
						}
						
						if (!empty($time_leftcenter) || !empty($load_leftcenter)){
							?>
							<script type="text/javascript">
								var countUsed = 0;
								var count_new;
								var frm_position = 'left-center';
								var urlpath = '<?php echo $urlpath; ?>';
								var allfid = '<?php echo $allfid; ?>';
								var time12 = parseInt('<?php echo $time_leftcenter; ?>');

								setTimeout(function () {
									mk('#feedback').show().animate({left: '0px'});
									if (countUsed == 1)
									{
										count_new = 0;
									} else
									{
										count_new = 1;
										countUsed = 1
									}

									mk.ajax({url: "<?php echo $sitepath; ?>webcontent/window-popup-count.php",
										type: "post",
										cache: false,
										crossDomain: true,
										data: 'count1=' + count_new + '&url=' + urlpath + '&frm_position1=' + frm_position + '&allFid=' + allfid,
									});
								}, time12)
							</script>
							<?php
						}else{
							?>
							<script type="text/javascript">
								setTimeout(function () {
									mk("#feedback").show();
								}, 1000)
							</script>
							<?php
						}
						
						if (!empty($time_topbar) || !empty($load_topbar)) {
							?>
							<script type="text/javascript">
								var countUsed8 = 0;
								var count_new8;
								var frm_position8 = 'top-bar';
								var urlpath8 = '<?php echo $urlpath; ?>';
								var allfid8 = '<?php echo $allfid; ?>';
								var time18 = parseInt('<?php echo $time_topbar; ?>');
								setTimeout(function () {
									mk('.fixedheader').show();
									if (countUsed8 == 1)
									{
										count_new8 = 0;
									} else
									{
										count_new8 = 1;
										countUsed8 = 1
									}

									mk.ajax({url: "<?php echo $sitepath; ?>webcontent/window-popup-count.php",
										type: "post",
										cache: false,
										crossDomain: true,
										data: 'count1=' + count_new8 + '&url=' + urlpath8 + '&frm_position1=' + frm_position8 + '&allFid=' + allfid8,
									});
								}, time18)
							</script>
							<?php
						}
						?>

						<script type="text/javascript">
							//mk(document).ready(function(){
							var countUsed7 = 0;
							var count_new7;
							var frm_position7 = 'left-center';
							var urlpath7 = '<?php echo $urlpath; ?>';
							var allfid7 = '<?php echo $allfid; ?>';
							var isOpen_left = true;

							mk('.pull_feedback').unbind().click(function () {
								if (isOpen_left) {
									if (countUsed7 == 1)
									{
										count_new7 = 0;
									} else
									{
										count_new7 = 1;
										countUsed7 = 1
									}

									mk('#feedback').animate({left: '0px'});
									mk.ajax({url: "<?php echo $sitepath; ?>webcontent/window-popup-count.php",
										type: "post",
										cache: false,
										crossDomain: true,
										data: 'count1=' + count_new7 + '&url=' + urlpath7 + '&frm_position1=' + frm_position7 + '&allFid=' + allfid7,
									});
									isOpen_left = !isOpen_left;
								} else {

									mk('#feedback').animate({left: '-250px'});
									isOpen_left = !isOpen_left;
								}

							});

							//});

						</script>

						<script type="text/javascript">
							//mk(document).ready(function(){
							var isopen_bar = true;
							mk('.plus').unbind().click(function () {
								if (isopen_bar) {
									mk('.plus').html("<img src='<?php echo $sitepath; ?>webcontent/manager/images/arrow_down.png'>");
									mk('.slidingDiv').hide();
									isopen_bar = !isopen_bar;
								} else
								{
									mk('.plus').html("<img src='<?php echo $sitepath; ?>webcontent/manager/images/arrow_up.png'>");
									mk('.slidingDiv').show();
									isopen_bar = !isopen_bar;
								}
							});
							//});

						</script>
						<script type="text/javascript" src="http://<?php echo $sdomainPath ?>/js/ldcheckCookies.js"></script>
						<script type="text/javascript">
							function submit_form(val)
							{
								var fname = mk("#first_name_" + val).val();
								var lname = mk("#last_name_" + val).val();
								var email = mk("#email_id_" + val).val();
								var cmpny_name = mk("#company_name_" + val).val();
								var contact = mk("#mobile_" + val).val();
								var pcmember_type = mk("#pcmember_type_" + val).val();
								var referral_url = mk("#referral_url_" + val).val();

								if (pcmember_type == 'C')
								{
									var c_client_id = mk("#c_client_id_" + val).val();
								}


								mk("#leadpopup_content_" + val).html('');


								if (email != '') {
									checkSLUemailCookie(email);
								}

								var isValid = true;
								mk("#email_id_" + val).each(function () {
									if (mk.trim(mk(this).val()) == '') {
										isValid = false;
										mk(this).css({
											"border": "2px solid red",
										});
									} else {
										mk(this).css({
											"border": "",
											"background": ""
										});
									}
								});



								mk("#first_name_" + val).each(function () {
									if (mk.trim(mk(this).val()) == '') {
										isValid = false;
										mk(this).css({
											"border": "2px solid red",
										});
									} else {
										mk(this).css({
											"border": "",
											"background": ""
										});
									}
								});


								mk("#last_name_" + val).each(function () {
									if (mk.trim(mk(this).val()) == '') {
										isValid = false;
										mk(this).css({
											"border": "2px solid red",
										});
									} else {
										mk(this).css({
											"border": "",
											"background": ""
										});
									}
								});


								mk("#company_name_" + val).each(function () {
									if (mk.trim(mk(this).val()) == '') {
										isValid = false;
										mk(this).css({
											"border": "2px solid red",
										});
									} else {
										mk(this).css({
											"border": "",
											"background": ""
										});
									}
								});

								mk("#mobile_" + val).each(function () {
									if (mk.trim(mk(this).val()) == '') {
										isValid = false;
										mk(this).css({
											"border": "2px solid red",
										});
									} else {
										mk(this).css({
											"border": "",
											"background": ""
										});
									}
								});

								if (isValid == false)
								{
									e.preventDefault();
								} else
								{



									mk.ajax({url: "<?php echo $sitepath; ?>webcontent/form-submit-action.php",
										type: "post",
										data: 'fname1=' + fname + '&lname1=' + lname + '&email1=' + email + '&cmpny_name1=' + cmpny_name + '&contact1=' + contact + '&form_id=' + val + '&referral_url1=' + referral_url + '&c_client_id1=' + c_client_id + '&pcmember_type1=' + pcmember_type,
										cache: false,
										crossDomain: true,
										beforeSend: function () {
											mk("#leadpopup_content_" + val).html('Please wait..');
										},
										success: function (result) { //alert(result); 

											mk("#first_name_" + val).hide();
											mk("#last_name_" + val).hide();
											mk("#email_id_" + val).hide();
											mk("#company_name_" + val).hide();
											mk("#mobile_" + val).hide();
											mk("#submit_" + val).hide();
											mk("#text_" + val).hide();
											mk("#sub_text_" + val).hide();
											mk("#sub_text2_" + val).hide();
											mk("#sub_text3_" + val).hide();
											mk("#sub_text4_" + val).hide();
											mk("#feedback_hide_" + val).hide();
											mk("#leadpopup_content_" + val).show().html(result);
											var arrctatype = result.split(".");
											if (arrctatype[0] == 2)
											{
												mk("#popupshow").remove();
												mk(".gynbg").remove();
											}
										}
									});
								}
							}
						</script>

						<?php
						if (!empty($scroll_loadpopup)){
							?>
							<script type="text/javascript">
								//mk(document).ready(function(){
								var countUsed3 = 0;
								mk(window).scroll(function () {
									var count_new3;

									var frm_position3 = 'pop-up-page-load';
									var urlpath = '<?php echo $urlpath; ?>';
									var allfid = '<?php echo $allfid; ?>';

									var scroll_loadpopup = parseInt('<?php echo $scroll_loadpopup; ?>');
									//alert(scroll_loadpopup);   
									if (mk(this).scrollTop() >= mk(document).height() / scroll_loadpopup) {
										if (countUsed3 == 1)
										{
											count_new3 = 0;
										} else
										{
											count_new3 = 1;
											countUsed3 = 1
										}

										mk('#popupshow').show();
										mk(".gynbg").show();

										mk('#counter_1').html();
										mk.ajax({url: "<?php echo $sitepath; ?>webcontent/window-popup-count.php",
											type: "post",
											cache: false,
											crossDomain: true,
											data: 'count1=' + count_new3 + '&url=' + urlpath + '&frm_position1=' + frm_position3 + '&allFid=' + allfid,
										});

									} else {
										mk('#popupshow').hide();
										mk(".gynbg").hide();
									}
								});


								//});
							</script>

							<?php
						}

						if (!empty($scroll_leftcenter)) {
							?>
							<script type="text/javascript">

								//mk(document).ready(function(){
								var countUsed4 = 0;
								mk(window).scroll(function () {

									var scroll_leftcenter = parseInt('<?php echo $scroll_leftcenter; ?>');
									//alert(scroll_leftcenter); 
									var count_new4;

									var frm_position4 = 'left-center';
									var urlpath = '<?php echo $urlpath; ?>';
									var allfid = '<?php echo $allfid; ?>';
									if (mk(this).scrollTop() >= mk(document).height() / scroll_leftcenter) {
										if (countUsed4 == 1)
										{
											count_new4 = 0;
										} else
										{
											count_new4 = 1;
											countUsed4 = 1
										}
										mk('#feedback').css({left: '0px'});

										mk.ajax({url: "<?php echo $sitepath; ?>webcontent/window-popup-count.php",
											type: "post",
											cache: false,
											crossDomain: true,
											data: 'count1=' + count_new4 + '&url=' + urlpath + '&frm_position1=' + frm_position4 + '&allFid=' + allfid,
										});

									} else {
										mk('#feedback').css({left: '-250px'});

									}
								});
								//});
							</script>

							<?php
						}

						if (!empty($scroll_rightcenter)){
							?>
							<script type="text/javascript">

								//mk(document).ready(function(){
								var countUsed6 = 0;

								mk(window).scroll(function () {

									var scroll_rightcenter = parseInt('<?php echo $scroll_rightcenter; ?>');
									var count_new6;

									var frm_position6 = 'bottom-right';
									var urlpath = '<?php echo $urlpath; ?>';
									var allfid = '<?php echo $allfid; ?>';
									if (mk(this).scrollTop() >= mk(document).height() / scroll_rightcenter)
									{
										if (countUsed6 == 1)
										{
											count_new6 = 0;
										} else
										{
											count_new6 = 1;
											countUsed6 = 1
										}

										mk('.right_slide').css({bottom: '0px'});
										mk('.arrow-check').addClass('arrow_down11').removeClass('arrow_up11');
										mk.ajax({url: "<?php echo $sitepath; ?>webcontent/window-popup-count.php",
											type: "post",
											cache: false,
											crossDomain: true,
											data: 'count1=' + count_new6 + '&url=' + urlpath + '&frm_position1=' + frm_position6 + '&allFid=' + allfid,
										});

									} else {
										var sg = mk('.right_slide').height();
										var sg1 = 51;
										var sg2 = sg1 - sg;


										mk('.right_slide').css({bottom: sg2});
										mk('.arrow-check').addClass('arrow_up11').removeClass('arrow_down11');

									}

								});


								//});
							</script>
							<?php
						}

						if (!empty($scroll_topbar)){
							?>
							<script type="text/javascript">

								//mk(document).ready(function(){
								var countUsed9 = 0;

								mk(window).scroll(function () {

									var scroll_topbar = parseInt('<?php echo $scroll_topbar; ?>');
									var count_new9;

									var frm_position9 = 'top-bar';
									var urlpath = '<?php echo $urlpath; ?>';
									var allfid = '<?php echo $allfid; ?>';
									if (mk(this).scrollTop() >= mk(document).height() / scroll_topbar)
									{
										if (countUsed9 == 1)
										{
											count_new9 = 0;
										} else
										{
											count_new9 = 1;
											countUsed9 = 1
										}

										mk('.fixedheader').show();
										mk.ajax({url: "<?php echo $sitepath; ?>webcontent/window-popup-count.php",
											type: "post",
											cache: false,
											crossDomain: true,
											data: 'count1=' + count_new9 + '&url=' + urlpath + '&frm_position1=' + frm_position9 + '&allFid=' + allfid,
										});

									} else {

										mk('.fixedheader').hide();


									}

								});


								//});
							</script>
							<?php
						}
						?>
					</head>
					<body>
						<?php
						if (((in_array($referral_url, $referral_link, true) and in_array($ip_location, $referral_country, true))) or ( $target_url == $referral_url)) {
							?>
							<form name="frmone" id="frmone_<?php echo $form_id; ?>" method="post">
								<input type="hidden" id="referral_url_<?php echo $form_id; ?>" value="<?php echo $urlpath; ?>">
								<input type="hidden" id="pcmember_type_<?php echo $form_id; ?>" value="<?php echo $pcmember_pc_type; ?>">
								<input type="hidden" id="c_client_id_<?php echo $form_id; ?>" value="<?php echo $c_client_id; ?>">

								<?php echo $get_form = htmlspecialchars_decode($set_form); ?>
							</form>
							<?php
						}
						?>

					</body>
				</html>
				<?php
			}
		}
	}
