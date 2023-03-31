<?php
	include_once("../classes/initialize1.class.php");
		include_once("../classes/include/dbop.class.php");
	
	$dbConnRoot = new dbop(true);
	$obj_initialize = new initialize($dbConnRoot);	
	
	if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'fetchEmail')
	{
		 $result = $obj_initialize->generateActivationEmailTemplate(false,$_REQUEST['email'] , $_REQUEST['name'],$_REQUEST['code']);
		//echo $result;
	}
	else if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'sendEmail')
	{
			//print_r($_REQUEST);
			$obj_initialize->sendNewUserActivationEmail($_REQUEST['email'] , $_REQUEST['name'],$_REQUEST['code']);
	}
	
?>