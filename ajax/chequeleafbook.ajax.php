
<?php include_once("../classes/chequeleafbook.class.php");
$obj_chequeleafbook = new chequeleafbook;

echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="edit")
{
	$select_type = $obj_chequeleafbook->selecting();

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
	$obj_chequeleafbook->deleting();
	return "Data Deleted Successfully";
}

?>