<?php
include_once("../classes/pp_receipts.class.php");
include_once("../classes/utility.class.php");
include_once("../classes/include/dbop.class.php");
	$dbConn = new dbop();
	$obj_receipt = new Receipts($dbConn);
	$obj_utility = new utility($dbConn);


	if(isset($_REQUEST['method']) && $_REQUEST['method'] == "getLedgerCategoryAndLedgerList")
	{
		$ledger_id = $_REQUEST['ledger_id'];
		$account_type = $_REQUEST['account_type'];
		$member_id = $_REQUEST['member_id'];
		$result = $obj_receipt->getLedgerCategoryAndLedgerList($ledger_id, $account_type, $member_id);
		echo "@@@".json_encode($result);
	}

	if(isset($_REQUEST['method']) && $_REQUEST['method'] == "getLoanName")
	{
		$loan_type = $_REQUEST['loan_type'];
		$member_id = $_REQUEST['member_id'];
		$result = $obj_receipt->getLoanName($loan_type,$member_id);
		echo "@@@".$result;
	}
	
	if(isset($_REQUEST['method']) && $_REQUEST['method'] == "getLedgerDetails")
	{
		$ledger_id = $_REQUEST['ledger_id'];
		$result = $obj_receipt->getLedgerDetails($ledger_id);
		echo "@@@".$result;
	}
	
	if(isset($_REQUEST['method']) && $_REQUEST['method'] == "getBankDepositSlip")
	{
		$bank_id = $_REQUEST['bank_id'];
		$result = $obj_receipt->getBankDepositSlip($bank_id);
		echo "@@@".$result;
	}

	if(isset($_REQUEST['method']) && $_REQUEST['method'] == "checkAccountExists")
	{
		$member_id 	  = $_REQUEST['member_id'];
		$account_type = $_REQUEST['account_type'];
		$result = $obj_utility->getLedgerIDWithParentAndCategoryId($member_id, $account_type);
		echo "@@@".$result;
	}
?>
