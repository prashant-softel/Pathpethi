<?php include_once("../classes/bill_period.class.php");
include_once("../classes/include/dbop.class.php");
include_once("../classes/dbconst.class.php");
	  $dbConn = new dbop();
$obj_bill_period = new bill_period($dbConn);


if(isset($_REQUEST['getdate']))
{
	$aryDate = $obj_bill_period->getPeriodStartAndEndDate($_REQUEST['period']);
	if($aryDate <> '')
	{
		echo getDisplayFormatDate($aryDate[0]['BeginingDate']) . "@@@" . getDisplayFormatDate($aryDate[0]['EndingDate']);
	}
}
else if(isset($_REQUEST['getperiod']))
{
	if(isset($_REQUEST['cycleID']))
	{
		
	$get_unit = $obj_bill_period->get_period($_REQUEST['cycleID']);
	}
	else
	{
		
	$get_unit = $obj_bill_period->get_period(0);
	}
}
else
{
	echo $_REQUEST["method"]."@@@";
	
	if($_REQUEST["method"]=="edit")
	{
		$select_type = $obj_bill_period->selecting();
	
		foreach($select_type as $k => $v)
		{
			foreach($v as $kk => $vv)
			{
				echo $vv."#";
			}
		}
	}
	
	if($_REQUEST["method"]=="delete")
	{
		$obj_bill_period->deleting();
		return "Data Deleted Successfully";
	}
}
?>