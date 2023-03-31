
<?php
include_once("../classes/charge_template.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_charge_template=new charge_template($dbConn);
echo $_REQUEST["method"]."@@@";
if($_REQUEST["method"]=="edit")
	{
	$select_type=$obj_charge_template->selecting();
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
	$obj_charge_template->deleting();
	return "Data Deleted Successfully";
	}
?>
