
<?php
include_once("../classes/wing.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_wing=new wing($dbConn);
echo $_REQUEST["method"]."@@@";
if($_REQUEST["method"]=="edit")
	{
	$select_type=$obj_wing->selecting();
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
	$obj_wing->deleting();
	return "Data Deleted Successfully";
	}
?>
