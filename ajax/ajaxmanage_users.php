<?php
	include_once("../classes/initialize.class.php");
		include_once("../classes/include/dbop.class.php");
	
	$dbConnRoot = new dbop(true);
	$obj_initialize = new initialize($dbConnRoot);	
	
	if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'fetchEmail')
	{
		 $result = $obj_initialize->generateActivationEmailTemplate(false,$_REQUEST['email'] , $_REQUEST['name'],$_REQUEST['code']);
		echo $result;
	}
	else if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'sendEmail')
	{
			//print_r($_REQUEST);
			$society_id = (isset($_SESSION['society_id'])) ? $_SESSION['society_id'] : 0;
			
			$obj_initialize->sendNewUserActivationEmail($_REQUEST['email'], $_REQUEST['name'], $_REQUEST['code'], $society_id);
	}
	
?>