<?php
include_once("../classes/add_comment.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
	  $dbConnRoot = new dbop(true);
$obj_add_comment = new add_comment($dbConn, $dbConnRoot);
$del_comment = $obj_add_comment->del_comment();
?>