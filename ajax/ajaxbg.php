
<?php
include_once("../classes/bg.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_bg=new bg($dbConn);

echo $_REQUEST["method"]."@@@";
if($_REQUEST["method"]=="edit")
	{
	$select_type=$obj_bg->selecting();
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
	$obj_bg->deleting();
	return "Data Deleted Successfully";
	}
?>
