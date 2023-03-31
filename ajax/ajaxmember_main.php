
<?php
include_once("../classes/member_main.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_member_main=new member_main($dbConn);
echo $_REQUEST["method"]."@@@";
if($_REQUEST["method"]=="edit")
	{
	$select_type=$obj_member_main->selecting();
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
	$obj_member_main->deleting();
	return "Data Deleted Successfully";
	}
?>
