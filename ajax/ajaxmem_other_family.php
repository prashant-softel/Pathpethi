
<?php
include_once("../classes/mem_other_family.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_mem_other_family=new mem_other_family($dbConn);
echo $_REQUEST["method"]."@@@";
if($_REQUEST["method"]=="edit")
	{
	$select_type=$obj_mem_other_family->selecting();
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
	$obj_mem_other_family->deleting();
	return "Data Deleted Successfully";
	}
?>
