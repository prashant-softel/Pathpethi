
<?php 
include_once("../classes/include/dbop.class.php");
include_once("../classes/dbconst.class.php");
include_once("../classes/unit.class.php");

$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$obj_unit = new unit($dbConn,$dbConnRoot);


if($_REQUEST["method"] == 'fetch')
{
	$yearid = $_REQUEST['yearid'];
	$societyID = $_REQUEST['societyID'];
	$sqlBillcycle = "select `society_name` from `society` where `society_id` = '".$societyID."' " ;
	$resBillcycle = $dbConn->select($sqlBillcycle);
	
	$finalArray = $obj_unit->fetchOwnershipDetails($yearid);
	
	$obj_unit->displayResults($finalArray,$resBillcycle);
	
}

?>