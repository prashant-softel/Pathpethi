
<?php
include_once("../classes/occupation.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_occupation=new occupation($dbConn);
echo $_REQUEST["method"]."@@@";
if($_REQUEST["method"]=="edit")
	{
	$select_type=$obj_occupation->selecting();
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
	$obj_occupation->deleting();
	return "Data Deleted Successfully";
	}
?>
