<?php include_once("../classes/bill_year.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_bill_year = new bill_year($dbConn);

echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="edit")
{
	$select_type = $obj_bill_year->selecting();
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
	$obj_bill_year->deleting();
	return "Data Deleted Successfully";
}

?>