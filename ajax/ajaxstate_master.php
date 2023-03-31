
<?php
include_once("../classes/state_master.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_state_master=new state_master($dbConn);
echo $_REQUEST["method"]."@@@";
if($_REQUEST["method"]=="edit")
	{
	$select_type=$obj_state_master->selecting();
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
	$obj_state_master->deleting();
	return "Data Deleted Successfully";
	}
?>
