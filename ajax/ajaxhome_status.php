
<?php
include_once("../classes/home_status.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_home_status=new home_status($dbConn);
echo $_REQUEST["method"]."@@@";
if($_REQUEST["method"]=="edit")
	{
	$select_type=$obj_home_status->selecting();
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
	$obj_home_status->deleting();
	return "Data Deleted Successfully";
	}
?>
