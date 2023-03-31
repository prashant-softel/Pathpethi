<?php	error_reporting(0);if(!isset($_SESSION)){ session_start(); }
		
		include_once "fb/fbmain_new.php";
		$fb_details = set_sess($userInfo);	
		
		include_once "classes/include/dbop.class.php";
		$m_dbConn = new dbop(true);
		
		include_once "classes/initialize.class.php";
		$obj_initialize = new initialize($m_dbConn);
		
		echo "<b>Please Wait...</b>";
		
		echo '<br>Details : ';
		//print_r($fb_details);
		$fb_details = array('name'=>'Ankur Patil', 'email'=>'ankur.2088@yahoo.co.in', 'fbid'=>'1234567890');

		if($fb_details['fbid']<>"")
		{
			//Add the user to DB
			$result = $obj_initialize->addUser($fb_details['name'], $fb_details['email'], '', $fb_details['fbid'], true);
			if($result > 0)
			{
				?>
                	<script>window.location.href = "initialize.php?imp"</script>
                <?php
			}
		}
?>