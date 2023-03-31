
<?php
include_once("../classes/add_member_id.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_add_member_id=new add_member_id($dbConn);
echo $_REQUEST["method"]."@@@";
if($_REQUEST["method"]=="edit")
	{
	$select_type=$obj_add_member_id->selecting();
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
	$obj_add_member_id->deleting();
	return "Data Deleted Successfully";
	}
?>
