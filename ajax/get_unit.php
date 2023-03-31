<?php
	include_once("../classes/member_main.class.php");
	include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
	$obj_member_main = new member_main($dbConn);
	if(isset($_REQUEST['getunit']))
	{
		$get_unit = $obj_member_main->get_unit_new();
	}
	else if(isset($_REQUEST['getyear']))	
	{
		$get_unit = $obj_member_main->get_year();
	}
	else if(isset($_REQUEST['getperiod']))	
	{
		$get_unit = $obj_member_main->get_period();
	}
	else
	{
		$get_unit = $obj_member_main->get_unit();
	}
?>