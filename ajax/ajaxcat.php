
<?php
include_once("../classes/cat.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
	  $dbConnRoot = new dbop(true);
$obj_cat=new cat($dbConn, $dbConnRoot);
echo $_REQUEST["method"]."@@@";
if($_REQUEST["method"]=="edit")
	{
	$select_type=$obj_cat->selecting();
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
	$obj_cat->deleting();
	return "Data Deleted Successfully";
	}
?>
