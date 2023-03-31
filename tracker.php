<?php
//ob_start();
$file = "/var/www/html/images/logo.png";
/*header("Content-Type: image/png"); // it will return image 
header('Content-Length: ' . filesize($file));
readfile($file);*/
//ob_end_flush();


if(file_exists($file)) 
{
	echo "exists";
	echo '<img src="https://way2society.com/images/logo.png" alt="" width="100px" height="100px"/>';
   /*header("Content-Type: image/png"); // it will return image 
	header('Content-Length: ' . filesize($file));

    readfile($file);*/
    exit;
}

if(isset($_REQUEST) && sizeof($_REQUEST) > 0)
{
	include_once("classes/genbill.class.php");
	include_once ("include/dbop.class.php");
	
	$DecodedSocID = (isset($_REQUEST['sid']) && $_REQUEST['sid'] <> "" ) ? base64_decode($_REQUEST['sid']) : 0;
	$DecodedUnitID = (isset($_REQUEST['uid']) && $_REQUEST['uid'] <> "" ) ? base64_decode($_REQUEST['uid']) : 0;
	$DecodedPeriodID = (isset($_REQUEST['pid']) && $_REQUEST['pid'] <> "" ) ? base64_decode($_REQUEST['pid']) : 0;
	$DecodedDbName = (isset($_REQUEST['dbname']) && $_REQUEST['dbname'] <> "" ) ? base64_decode($_REQUEST['dbname']) : 0;
	$billType = 0;
	
	if(isset($_REQUEST['dbname']) && $_REQUEST['dbname'] <> "")
	{
		$billType = $_REQUEST['BT'];
	}
	
	
	$dbConn = new dbop(false,$DecodedDbName);
	$dbConnRoot = new dbop(true);
	$obj_genbill = new genbill($dbConn,$dbConnRoot);
	
	//setting Maintenance Bill read-unread flag
	$obj_genbill->setMaintenanceBillReadUnreadFlag($DecodedUnitID,$DecodedPeriodID,$billType);
}


?>