<?php
include_once("../classes/dbconst.class.php");
include_once("../classes/daily_collection.class.php");
include_once("../classes/include/dbop.class.php");
$m_dbConn = new dbop();
$obj_daily_collection = new dailycollection($m_dbConn);

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'FetchDailyCollectionReport')
{
	$agentname = $_REQUEST['agent_id'];
	$date = $_REQUEST['date'];
	$result = $obj_daily_collection->FetchDailyCollectionReport($agentname,$date);
	echo '@@@'.json_encode($result);
}

if($_REQUEST["method"]=="update")
{
$select_type=$obj_daily_collection->updatedailycollection($_REQUEST['id']);
echo '@@@'.json_encode($select_type);
}

if($_REQUEST["method"]=="agent_daily_collection")
{
	$result = $obj_daily_collection->agentDailyCollection($_REQUEST);
	echo '@@@'.json_encode($result);
}



?>
