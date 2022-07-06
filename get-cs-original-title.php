<?php
	require_once "includes/global.php";
	include("includes/check_login.php");
	include("includes/function.php");

	$pc_member_info = getPCMemberInfo($c_lient_Id);
	$pcmember_pc_type = $pc_member_info['member_pc_type'];
	$p_client_id = $pc_member_info['p_client_id']; 

	if($_POST['cs_original_title']!='')
	{
		$cs_orig_title = $_POST['cs_original_title'];  
	}    

	if($pcmember_pc_type=='C'){                             
		$sql = "select CS.id, CS.case_study_title, TS.id as syndid from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".$c_lient_Id."' and CS.valid=1 and CS.deleted=0 and TS.approve=1 and (CS.case_study_title LIKE '%".$cs_orig_title."%' OR CS.case_study_actual_title LIKE '%".$cs_orig_title."%') order by CS.case_study_title desc";
      
		$sql2 = "select id,case_study_title from sp_child_case_study where valid=1 and deleted=0 and approve=1 and client_id='".$c_lient_Id."' and (case_study_title LIKE '%".$cs_orig_title."%' OR case_study_actual_title LIKE '%".$cs_orig_title."%') order by case_study_title desc";
	}
	else
	{
		$sql = "select id,case_study_title from sp_case_study where valid=1 and deleted=0 and approve=1 and client_id='".$c_lient_Id."' and (case_study_title LIKE '%$cs_orig_title%' OR case_study_actual_title LIKE '%$cs_orig_title%') order by case_study_title desc";
	}

	$rsd = mysqli_query($conn, $sql);
	$rs = mysqli_fetch_array($rsd);

	$rsd2 = mysqli_query($conn, $sql2);
	$rs2 = mysqli_fetch_array($rsd2);

	echo ($rs['case_study_title']!='') ? $rs['case_study_title'] : $rs2['case_study_title'];
