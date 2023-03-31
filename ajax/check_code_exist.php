<?php
include_once("../classes/member_main_new1.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_member_main = new member_main_new1($dbConn);
$check_code_exist = $obj_member_main->check_code_exist();
?>