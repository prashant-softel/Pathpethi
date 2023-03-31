<?php
include_once("../classes/events.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConnRoot = new dbop(true);
	  $dbConn = new dbop();
$obj_events=new events($dbConn,$dbConnRoot);
echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="edit")
	{
	$select_type=$obj_events->selecting($_REQUEST['eventId']);
	foreach($select_type as $k => $v)
		{
		foreach($v as $kk => $vv)
			{
			echo $vv."^";
			}
		}
	}
if($_REQUEST["method"]=="delete")
	{
	$obj_events->deleting($_REQUEST['eventId']);
	return "Data Deleted Successfully";
	}
	
?>
