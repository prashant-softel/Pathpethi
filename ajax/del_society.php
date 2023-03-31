<?php
include_once("../classes/society.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_society = new society($dbConn);
$del_society = $obj_society->del_society();
?>