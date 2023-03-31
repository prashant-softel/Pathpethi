<?php
include_once("../classes/del_control_sadmin.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_del_control_sadmin = new del_control_sadmin($dbConn);
$delete_perm_sadmin = $obj_del_control_sadmin->set_delete_perm_sadmin();
?>
