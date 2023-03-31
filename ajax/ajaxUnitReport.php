<?php 
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
	  $dbConnRoot = new dbop(true);
include_once "../classes/unit_report.class.php";
$objunit_report = new unit_report($dbConn, $dbConnRoot);

echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="email")
{
	echo "start...";
	$select_type = $objunit_report->sendEmail();
echo "select_type:".$select_type;
	
}

?>