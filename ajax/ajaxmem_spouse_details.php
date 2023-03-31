
<?php
include_once("../classes/mem_spouse_details.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_mem_spouse_details=new mem_spouse_details($dbConn);
echo $_REQUEST["method"]."@@@";
if($_REQUEST["method"]=="edit")
	{
	$select_type=$obj_mem_spouse_details->selecting();
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
	$obj_mem_spouse_details->deleting();
	return "Data Deleted Successfully";
	}
?>
