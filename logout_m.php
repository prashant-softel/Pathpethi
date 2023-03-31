<?php	if(!isset($_SESSION)){ session_start(); }
		//include_once "fb/fbmain.php";
		session_destroy();

		////////////////////////////
		session_unset($_SESSION['member_name']);
		session_unset($_SESSION['member_id']);
		session_unset($_SESSION['com_id']);
		
		session_unset($_SESSION['fbid']);
		session_unset($_SESSION['name']);
		session_unset($_SESSION['gender']);
		session_unset($_SESSION['email']);
		session_unset($_SESSION['dob']);
		////////////////////////////

?>
<script>
	window.location.href = 'login_m.php?mlog';
	//window.location.href = '<?php //echo $logoutUrl;?>&kk';
</script>