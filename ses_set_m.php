<?php 
	include_once("classes/include/dbop.class.php");
	$m_dbConn = new dbop();	
?>
<?php if(!isset($_SESSION)){ session_start(); }
/*(if(!isset($_SESSION['member_id']))
{		
?>
<script>window.location.href = 'login_m.php?mlog';</script>   
<?php
}*/
?>