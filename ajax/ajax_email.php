<?php 	
include_once ("../classes/dbconst.class.php");
include_once("../classes/include/dbop.class.php");
//echo "dm";
include_once("../classes/activate_user_email.class.php");
//echo "dm2";
		//echo json_encode($_POST["email"]);
		$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		if($_POST["mode"] == 'email')
		{
			$obj_activation = new activation_email($dbConn, $dbConnRoot);
		
			echo $obj_activation->AddMappingAndSendActivationEmail($_POST['role'], $_POST['unit_id'], $_POST['society_id'], $_POST['code'], $_POST['email'], $_POST['name']);
		}
			
?>