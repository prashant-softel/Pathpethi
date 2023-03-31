<?php
include_once("../classes/society_group.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_society_group = new society_group($dbConn);

$check_grp_name = $obj_society_group->check_grp_name();
?>