<?php
include_once("../classes/list_member.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
	$obj_list_member = new list_member($dbConn);
	if(isset($_REQUEST['getwing']))
	{
		$get_wing = $obj_list_member->get_wing_new();
	}
	else
	{
		$get_wing = $obj_list_member->get_wing();
	}
?>