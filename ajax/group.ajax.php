
<?php include_once("../classes/group.class.php");	
	include_once("../classes/include/dbop.class.php");
	$dbConn = new dbop();
	$obj_group = new group($dbConn);

echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="edit")
{
	$select_type = $obj_group->selecting();

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
	$obj_group->deleting();
	return "Data Deleted Successfully";
}

?>