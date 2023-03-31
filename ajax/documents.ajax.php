<?php
include_once("../classes/include/dbop.class.php");
include_once("../classes/doc.class.php");
include_once("../classes/notice.class.php");

	  $dbConn = new dbop();
	  $dbConnRoot = new dbop(true);
	$obj_document = new document($dbConn, $dbConnRoot);
	

	
	if($_REQUEST["method"]=="delete")
	{
		//echo $_REQUEST["method"]."@@@";
		$sDocumentID = $_REQUEST["ID"];
		$res = $obj_document->deleteDoc($sDocumentID);
		echo json_encode($res);
	}
	
?>
