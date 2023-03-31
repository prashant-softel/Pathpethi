
<?php
include_once("../classes/mem_bike_parking.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_mem_bike_parking=new mem_bike_parking($dbConn);
echo $_REQUEST["method"]."@@@";
if($_REQUEST["method"]=="edit")
	{
	$select_type=$obj_mem_bike_parking->selecting();
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
	$obj_mem_bike_parking->deleting();
	return "Data Deleted Successfully";
	}
?>
