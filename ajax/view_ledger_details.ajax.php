<?php include_once("../classes/view_ledger_details.class.php");
	include_once("../classes/include/dbop.class.php");
	$dbConn = new dbop();
	$obj_ledger_details = new view_ledger_details($dbConn);

if(isset($_REQUEST['getvoucherdetails']))	
{
	echo $get_voucher_details = $obj_ledger_details->details2($_REQUEST['lid'], $_REQUEST['vid'], $_REQUEST['vtype']);
}
	
if($_REQUEST['method'] == "for_delete")
{
	//echo "Coming here?<br>";
	$VoucherNos = json_decode($_REQUEST['VoucherNos']);
	//$VNo_array = implode($VoucherNos);
	//print_r($VNo_array);
	//print_r($VoucherNos);
	$deleted_chequeIDs = $obj_ledger_details->delete_receipts($VoucherNos);
	
	echo $deleted_chequeIDs;
}

if($_REQUEST['method'] == "link_voucher")
{
	$fd_id = $_REQUEST['fd_id'];
	$VoucherNos = json_decode($_REQUEST['VoucherNos']);
	$linked_vouchers = $obj_ledger_details->link_vouchers($VoucherNos,$fd_id);
	//print_r($linked_vouchers);
	echo $linked_vouchers;
}