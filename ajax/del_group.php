<?php
include_once("../classes/society_group.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_society_group = new society_group($dbConn);
$del_group = $obj_society_group->del_group();
?>