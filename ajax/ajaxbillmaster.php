<?php
	include_once("../classes/billmaster.class.php");
	include_once("../classes/include/dbop.class.php");
	$dbConn = new dbop();
	$obj_billmaster = new billmaster($dbConn);
	
	if(isset($_REQUEST['getunit']))
	{
		$get_unit = $obj_billmaster->fetch_unit_data();
	}
	else if(isset($_REQUEST['acchead']))
	{
		echo $get_acchead = $obj_billmaster->fetch_acc_head($_REQUEST['billtype']);
	}
	else if(isset($_REQUEST['update']))
	{
		$aryHead = json_decode($_REQUEST['head']);
		$aryAmt = json_decode($_REQUEST['amt']);
		
		$unit = $_REQUEST['unit'];
		
		$period = $_REQUEST['period'];
		$start_period = $_REQUEST['start_period'];
		$end_period = $_REQUEST['end_period'];
		$bill_type = $_REQUEST['bill_type'];
		
		for($iCnt = 0; $iCnt < sizeof($aryHead); $iCnt++)
		{
			$update_master = $obj_billmaster->update_billmaster($unit, $aryHead[$iCnt], $aryAmt[$iCnt], $period, $start_period, $end_period, $bill_type);
		}
		
		echo 'Records Updated';
	}
	else if(isset($_REQUEST['getdata']))
	{
		$aryUnit = json_decode($_REQUEST['unit']);
		$period = $_REQUEST['period'];
		$fetch_data = $obj_billmaster->fetch_data($aryUnit, $period, $_REQUEST['bill_type']);
	}
	else if(isset($_REQUEST['details']))
	{
		$get_details = $obj_billmaster->fetch_details($_REQUEST['unit'], $_REQUEST['head'], $_REQUEST['bill_type']);
	}
?>