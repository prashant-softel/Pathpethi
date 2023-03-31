<?php
include_once("../classes/member_main.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_member_main = new member_main($dbConn);
$check_record = $obj_member_main->check_record();
?>