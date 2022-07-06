<?php 
header('Access-Control-Allow-Origin: *'); 
 include("includes/global.php");
include("manager/common_functions.php");
include("csrf/csrf-magic.php");
	
$searchTerm = strtolower(testInput($_REQUEST["showsearchName"]));

if($_REQUEST["pcType"]=='C')
{     if(!empty($searchTerm)) 
      {
      $sql = "select CS.id, CS.case_study_title,CS.case_study_actual_title, TS.id as syndid from sp_template_syndication as TS INNER JOIN sp_case_study as CS ON TS.case_id=CS.id where TS.c_client_id='".testInput($_REQUEST["c_lient_Id"])."' and CS.valid=1 and CS.deleted=0 and TS.approve=1 and (CS.case_study_title LIKE '%".$searchTerm."%' OR CS.case_study_actual_title LIKE '%".$searchTerm."%') order by CS.case_study_title desc";
      }
      
}
else
{    if(!empty($searchTerm)) 
      {
     $sql = "select * from sp_case_study where valid=1 and deleted=0 and approve=1 and client_id='".testInput($_REQUEST["c_lient_Id"])."' and (case_study_title LIKE '%".$searchTerm."%' OR case_study_actual_title LIKE '%".$searchTerm."%') order by case_study_title desc";
      }
}

$rsd = mysqli_query($conn,$sql);
?>

<ul id="search-suggest">
<?php while($rs = mysqli_fetch_array($rsd)) 
{
$cname = ($rs['case_study_title']!='') ? ucwords($rs['case_study_title']) : ucwords($rs['case_study_title']);
?>
<li onClick="srchshowcaseClick('<?php echo htmlentities($cname); ?>');"><?php echo htmlentities($cname); ?></li>
<?php 
}
?>
</ul>


