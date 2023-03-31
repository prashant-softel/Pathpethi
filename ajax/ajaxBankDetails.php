
<?php include_once("../classes/BankDetails.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_BankDetails = new BankDetails($dbConn);

if(isset($_REQUEST['getbalance']))
{
	$balance = $obj_BankDetails->getOpeningBalance($_REQUEST['ledger']);
}
else
{
	echo $_REQUEST["method"]."@@@";
	
	if($_REQUEST["method"]=="edit")
	{
		$select_type = $obj_BankDetails->selecting();
	
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
		$obj_BankDetails->deleting();
		return "Data Deleted Successfully";
	}
}

?>