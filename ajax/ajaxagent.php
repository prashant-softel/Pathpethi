<?php
include_once("../classes/dbconst.class.php");
include_once("../classes/agent_form.class.php");
//include_once("include/display_table.class.php");
include_once("../classes/include/dbop.class.php");

$m_dbConn = new dbop();

$obj_agent_form = new agent_form($m_dbConn);

echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="edit")
	{
	 $select_type=$obj_agent_form->selecting($_REQUEST['agent_id']);
	 echo json_encode($select_type);
	}
if($_REQUEST["method"]=="delete")
	{
	$obj_agent_form->deleting($_REQUEST['agent_id']);
	return "Data Deleted Successfully";
	}
	
?>
