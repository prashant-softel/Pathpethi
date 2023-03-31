<?php include_once("../classes/PaymentGateway.class.php");
include_once("../classes/Utility.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_Payment_Gateway = new PaymentGateway($dbConn);
$obj_Utility = new Utility($dbConn);

//echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="initiate")
{
	$LoginID = $_REQUEST["LoginID"];
	$UnitID = $_REQUEST["UnitID"];
	$Date = $_REQUEST["Date"];
	$Amount = $_REQUEST["Amount"];
	$BillType = $_REQUEST["BillType"];	
	$PaidTo = $_REQUEST["PT"];
	$TranxID = "0";
	$Status = "0";
	$payuMoneyId = "0";
	$Mode = "0";
	$Comments = $_REQUEST["Comment"];

	$select_type = $obj_Payment_Gateway->InitiatePayment(1,$LoginID,$UnitID,$PaidTO,$Date,$Amount,$BillType,$TranxID,$Status,$payuMoneyId,$Mode,'0','0','0',$Comments);
	echo $select_type;
}

if($_REQUEST["method"]=="getDueAmount")
{
	$UnitID = $_REQUEST["UnitID"];
	$BillType = $_REQUEST["BillType"];
	$data= $obj_Utility->getDueAmountByBillType($UnitID, $BillType);
	echo $data;
}

?>