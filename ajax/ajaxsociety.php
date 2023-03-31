<?php
include_once("../classes/society.class.php");
include_once("../classes/include/dbop.class.php");
	$dbConn = new dbop();
	$obj_society=new society($dbConn, '');
echo $_REQUEST["method"]."@@@";
if($_REQUEST["method"]=="show" || $_REQUEST["method"]=="edit")
	{
		
	$select_type=$obj_society->getMembers();
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
	$obj_society->deleting();
	return "Data Deleted Successfully";
	}
?>
