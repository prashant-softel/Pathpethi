
<?php 
include_once("../classes/include/dbop.class.php");
include_once("../classes/group_create.class.php");

$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$obj_group_create = new group_create($dbConn, $dbConnRoot);

if(isset($_REQUEST["method"]) && $_REQUEST["method"] == 'fetchsocietylist')	
{
		//echo 'test';
	$query = "select society_id,society_name from society where status='Y' order by society_id desc";
	$select_type = $obj_group_create->combobox11($query, '', 0, $_REQUEST['group_id']); 
}
else
{
	echo $_REQUEST["method"]."@@@";

	if($_REQUEST["method"]=="edit")
	{
		$select_type = $obj_group_create->selecting();
	
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
		$obj_group_create->deleting();
		return "Data Deleted Successfully";
	}
	
	if(isset($_REQUEST['getcategory']))	
	{
		$getgroup_create = $obj_group_create->get_group_name();
	}
}
?>