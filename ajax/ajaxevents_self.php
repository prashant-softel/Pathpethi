<?php
include_once("../classes/events_self.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_events = new events_self($dbConn);

echo $_REQUEST["method"]."@@@";
if($_REQUEST["method"]=="edit")
	{
	$select_type=$obj_events->selecting();
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
	$obj_events->deleting();
	return "Data Deleted Successfully";
	}
?>
