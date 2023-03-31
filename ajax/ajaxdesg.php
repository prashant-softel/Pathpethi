
<?php
include_once("../classes/desg.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_desg=new desg($dbConn);
echo $_REQUEST["method"]."@@@";
if($_REQUEST["method"]=="edit")
	{
	$select_type=$obj_desg->selecting();
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
	$obj_desg->deleting();
	return "Data Deleted Successfully";
	}
?>
