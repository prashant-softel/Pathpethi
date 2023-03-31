
<?php include_once("../classes/members_commitee.class.php");	
	include_once("../classes/include/dbop.class.php");	
	$dbConn = new dbop();
	$obj_commitee = new commitee($dbConn);	

echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="edit")
{
	$select_type = $obj_commitee->selecting();

	foreach($select_type as $k => $v)
	{
		echo $v;
		foreach($v as $kk => $vv)
		{
			echo $vv."#";
		}
	}
}

if($_REQUEST["method"]=="delete")
{	
	$obj_commitee->deleting();
	return "Data Deleted Successfully";
}

?>