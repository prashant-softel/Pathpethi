<?php
include_once("../classes/list_member.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_list_member = new list_member($dbConn);
$del_member = $obj_list_member->del_member();
?>