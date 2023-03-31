<?php
	include_once("classes/include/dbop.class.php");
	$m_dbConn = new dbop();
?>
<?php if(!isset($_SESSION)){ session_start(); }
if((!isset($_SESSION['member_id'])) && (!isset($_SESSION['admin'])))
{		
?>
<script>window.location.href = 'login.php?alog';</script>   
<?php
}
?>