<?php if(!isset($_SESSION)){ session_start(); }
//echo 'Session : ' . $_SESSION['sadmin'];
//die();
/*if(!isset($_SESSION['role']) && $_SESSION['role'] == 'Super Admin')
{		
?>
<script>window.location.href = 'login.php?alog';</script>   
<?php
}*/
?>
<?php 
	include_once("classes/include/dbop.class.php");
	$m_dbConn = new dbop();	
?>
<?php //echo phpinfo();?>
