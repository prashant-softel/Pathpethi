<?php
include_once("../classes/add_member_id.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_add_member_id = new add_member_id($dbConn);
$get_mem_info = $obj_add_member_id->get_mem_info();
?>