
<?php include_once("../classes/depositgroup.class.php");
include_once("../classes/include/dbop.class.php");
$m_dbConn = new dbop();	
$obj_depositgroup = new depositgroup($m_dbConn);


echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="edit")
{
	$select_type = $obj_depositgroup->selecting();

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
	$obj_depositgroup->deleting();
	return "Data Deleted Successfully";
}

?>