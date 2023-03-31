<?php
include_once("../classes/include/dbop.class.php");
 include_once("../classes/tips.class.php");
 $dbConnRoot = new dbop(true); 
$dbConn=new dbop();
$obj_tips = new tips($dbConnRoot,$dbConn);

echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="edit")
{
	$select_type = $obj_tips->selecting($_REQUEST['Id']);

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
	$obj_tips->deleting($_REQUEST['Id']);
	return "Data Deleted Successfully";
}

?>