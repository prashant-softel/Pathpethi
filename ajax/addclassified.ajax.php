
<?php include_once("../class/classified.class.php");
$obj_classified = new classified;

$_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="edit")
{
	$select_type = $obj_classified->selecting();

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
	$obj_classified->deleting();
	return "Data Deleted Successfully";
}

?>