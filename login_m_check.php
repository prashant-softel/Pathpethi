<?php	error_reporting(0);if(!isset($_SESSION)){ session_start(); }
		
		include_once "classes/login_m.class.php";
		$main_obj = new login_m();

		include_once "fb/fbmain.php";
		$fbid = set_sess($userInfo);	
		
		
		echo "<b>Please Wait...</b>";
		
		
		if($fbid<>"")
		{
			$check_fbid = $main_obj->check_fbid($fbid);
		}
		else
		{
			?>
			<script>window.location.href = 'logout_m.php';</script>
            <?php
		}
?>