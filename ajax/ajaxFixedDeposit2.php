
<?php include_once("../classes/FixedDeposit.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_FixedDeposit = new FixedDeposit($dbConn);

//remove all empty spaces after php closing brackets
ob_clean();

if($_REQUEST["method"] == "fetch_vouchers")
{
	$fd_id = $_REQUEST["fd_id"];
	$vouchers = $obj_FixedDeposit->show_Vouchers($fd_id);
	//echo $vouchers;
	echo json_encode($vouchers);
}

if($_REQUEST["method"] == "fetch_voucher_details")
{
	$voucher_no = $_REQUEST['VoucherNo'];
	$ledger_name = $_REQUEST['ledgername'];

	$details = $obj_FixedDeposit->fetch_voucher_details($voucher_no,$ledger_name);
	
	echo json_encode($details);
}

if($_REQUEST["method"] == "fetch_all_dets")
{
	$ledger_id = $_REQUEST['ledgerid'];
	$fd_details = $obj_FixedDeposit->get_details_for_renew($ledger_id);
	
	echo json_encode($fd_details);
}

if($_REQUEST["method"] == "fetch_acc_int")
{
	$ledger_id = $_REQUEST['ledger_id'];
	$fd_id = $_REQUEST['fd_id'];
	$acc_int_ledger = $_REQUEST['acc_int_led'];
	
	$acc_int = $obj_FixedDeposit->getAccuedInterestFromFD_JVTable($fd_id,$acc_int_ledger);
	
	$fd_close = $obj_FixedDeposit->get_fd_close_details($fd_id);
	$dets = array();
	array_push($dets,$acc_int,$fd_close);
	
	echo json_encode($dets);
}

if($_REQUEST["method"] == "update_ledgers")
{
	$fd_id = $_REQUEST['fd_id'];
	$ledger_dets = $obj_FixedDeposit->get_ledgers($fd_id);
	
	$fd_close = $obj_FixedDeposit->get_fd_close_details($fd_id);
	array_push($ledger_dets,$fd_close);
	
	echo json_encode($ledger_dets);
}

?>