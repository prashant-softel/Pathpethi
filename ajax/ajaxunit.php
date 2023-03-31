
<?php
include_once("../classes/unit.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_unit=new unit($dbConn);
if($_REQUEST["method"]=="delete" ||  $_REQUEST["method"]=="edit")
{
	echo $_REQUEST["method"]."@@@";
}
if($_REQUEST["method"]=="edit")
	{
	$select_type=$obj_unit->selecting();
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
	$obj_unit->deleting();
	return "Data Deleted Successfully";
	}
	
if(isset($_REQUEST['getMemberStatus']))	
{
	echo $data = $obj_unit->getMemberStatus($_REQUEST['unitID']);
}	
?>
