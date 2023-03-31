<?php	error_reporting(0);if(!isset($_SESSION)){ session_start(); }
		
		include_once "classes/include/dbop.class.php";
		$m_dbConn = new dbop(true);
		
		include_once "classes/login.class.php";
		$main_obj = new login($m_dbConn);

		include_once "fb/fbmain.php";
		$fb_details = set_sess($userInfo);	
		
		
		echo "<b>Please Wait...</b>";
		
		
		if($fb_details['fbid']<>"")
		{
			$check_fbid = $main_obj->chk_log_fb($fb_details['email'], $fb_details['fbid']);
		}
		else
		{
			?>
			<script>window.location.href = 'logout.php';</script>
            <?php
		}
?>