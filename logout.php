<?php if(!isset($_SESSION)){ session_start(); }

session_destroy();
 error_reporting(0);
////////////////////////////
	session_unset($_SESSION['admin']);
	
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
$redirectURL = "";
if(isset($_REQUEST["url"]) && $_REQUEST["url"] <> '')
{
	$redirectURL = $_REQUEST["url"];
	
	?>
	<script>
		localStorage.clear();
		window.location.href = 'login.php?url=<?php echo $redirectURL; ?>';
	</script>
    <?php
}
else
{
	?>
	<script>
		localStorage.clear();
		window.location.href = 'login.php';
	</script>
    <?php
}
