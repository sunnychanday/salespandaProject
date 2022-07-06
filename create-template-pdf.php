<?php
	error_reporting(0);
	include("includes/global.php");
	include("includes/check_login.php");

	$doe = date("Y-m-d h:i:s");
	$client_id=$_GET['wrl'];
	$csd=$_GET['csd'];
	$qry1="select case_study_library from sp_case_study where id='".$csd."' and client_id='".$client_id."'";
	$resq1=mysqli_query($conn, $qry1);
	$docData1=mysqli_fetch_array($resq1);
	$template_id=$docData1['case_study_library'];

	$query_content = mysqli_query($conn, "select * from user_templates where template_id='".$template_id."'");
	$row_content=mysqli_fetch_array($query_content);
	$template_content=$row_content['template_content'];
	$template_name=$row_content['template_name'];
	
	require_once('tcpdf_include.php');
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT,true, 'UTF-8', false);

	//$pdf->SetCreator(PDF_CREATOR);
	//$pdf->SetAuthor('Nicola Asuni');
	//$pdf->SetTitle('TCPDF Example 006');
	//$pdf->SetSubject('TCPDF Tutorial');
	//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
	//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);
	//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(8, 10, 8, 10);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
	$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		require_once(dirname(__FILE__).'/lang/eng.php');
		$pdf->setLanguageArray($l);
	}

	// ---------------------------------------------------------

	// set font
	$pdf->SetFont('dejavusans', '', 10);

	// add a page
	$pdf->AddPage();
	$pdf->setJPEGQuality(99);

	$html =$template_content;

	$pdf->writeHTML($html, true, false, true, false, '');

	$pdf->lastPage();


	$pdf->Output($template_name.'.'.pdf, 'I');
