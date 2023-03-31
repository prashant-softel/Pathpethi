<?php
include_once("../classes/society.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_society = new society($dbConn);
$check_socty_exist = $obj_society->check_socty_exist();
?>