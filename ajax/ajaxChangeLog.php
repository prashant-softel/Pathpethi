<?php 
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
include_once "../classes/changelog.class.php";
$obj_changeLog = new changeLog($dbConn);

//echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="applyFilter")
{
	//echo "start...";
	//echo "PostString: ".$_REQUEST['ChangedBy']."||".$_REQUEST['ChangeTSFrom']."||".$_REQUEST['ChangeTSTo']."||".$_REQUEST['method'];
	$res = $obj_changeLog->pgnation();
//echo "select_type:".$res;
echo  $res;
	
}

?>