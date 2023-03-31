
<?php
include_once("../classes/society_group.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_society_group=new society_group($dbConn);
echo $_REQUEST["method"]."@@@";
if($_REQUEST["method"]=="edit")
	{
	$select_type=$obj_society_group->selecting();
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
	$obj_society_group->deleting();
	return "Data Deleted Successfully";
	}
?>
