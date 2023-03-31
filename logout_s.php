<?php if(!isset($_SESSION)){ session_start(); }

session_destroy();

////////////////////////////
	session_unset($_SESSION['admin']);
	session_unset($_SESSION['sadmin']);
	
if(isset($_SESSION['ssid']))
{
	session_unset($_SESSION['ssid']);
}
if(isset($_SESSION['owner_id']))
{
	session_unset($_SESSION['owner_id']);
}
if(isset($_SESSION['owner_name']))
{
	session_unset($_SESSION['owner_name']);
}

////////////////////////////

?>
<script>
	//window.location.href = '../login_s.php?slog';
	window.location.href = 'index.php';
</script>