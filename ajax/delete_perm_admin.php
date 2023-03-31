<?php
include_once("../classes/del_control_admin.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_del_control_admin = new del_control_admin($dbConn);
$delete_perm_admin = $obj_del_control_admin->set_delete_perm_admin();
?>
