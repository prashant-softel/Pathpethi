
<?php include_once("../classes/account_category.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_account_category = new account_category($dbConn);

echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="edit")
{
	$select_type = $obj_account_category->selecting();

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
	$obj_account_category->deleting();
	return "Data Deleted Successfully";
}

?>