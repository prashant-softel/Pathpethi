<?php include_once("../classes/ledger_details.class.php");
		include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_ledger_details = new ledger_details($dbConn);

if(isset($_REQUEST['getcategory']))	
{
	$get_category = $obj_ledger_details->get_category_name();
}