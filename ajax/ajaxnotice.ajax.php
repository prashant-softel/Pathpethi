
<?php 
include_once("../classes/include/dbop.class.php");
include_once("../classes/notice.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$obj_notice = new notice($dbConn,$dbConnRoot);

//echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="edit")
{
	$select_type = $obj_notice->selecting();

	//foreach($select_type as $k => $v)
	//{
		//foreach($v as $kk => $vv)
		//{
		//	echo $vv."#";
		//}
	//}
	echo $_REQUEST["method"]."@@@".json_encode($select_type[0]); 
}

if($_REQUEST["method"]=="delete")
{
	$obj_notice->deleting();
	echo $_REQUEST["method"]."@@@Data Deleted Successfully";
}
if($_REQUEST["method"]=="fetch_templates")
{
	$template_id = $_REQUEST["template_id"];
	$templates = $obj_notice->fetch_template_details($template_id);
	echo json_encode($templates);
	 
	
}
?>