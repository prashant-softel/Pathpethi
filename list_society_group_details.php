<?php include_once "ses_set_as.php"; ?>
<?php 
if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
else
{
	include_once("includes/head_s.php");
}
?>

<?php
include_once("classes/society_group.class.php");
$obj_society_group = new society_group($m_dbConn);
?>
 

<html>
<head>
</head>

<body>

<center><font color="#43729F" size="+1"><b>View Society under this group</b></font></center>


<br>
<center>
<?php if(isset($_REQUEST['ev'])){?>
<a href="events_view_as.php?grp&mmkpk=123" style="color:#00F; text-decoration:none;"><b>Back</b></a>
<?php }else{ ?>
<a href="list_society_group.php?grp&vvv" style="color:#00F; text-decoration:none;"><b>Back to list</b></a>
<?php } ?>
</center>
<br>

<center>
<?php
$group_in_details = $obj_society_group->group_in_details($_REQUEST['id']);
?>

<br><br><br><br>

</center>
<?php include_once "includes/foot.php"; ?>
