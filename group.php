<?php include_once "ses_set_s.php"; ?>
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
include_once("classes/group.class.php");
$obj_group = new group($m_dbConn);
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <!--<script type="text/javascript" src="lib/js/"></script>-->
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/group.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
		/*$(document).ready(function()
		{
			$("#error").fadeIn("slow");
		});*/
		document.getElementById('error').style.display = 'block';
        setTimeout('hide_error()',8000);	
    }
    function hide_error()
    {
		/*$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});*/
		document.getElementById('error').innerHTML = '';
        document.getElementById('error').style.display = 'none';
    }
	</script>
</head>
<body>
<center>
<br>
<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Group</div>
        

<!--<h2 align="center" id='top'>Group</h2>-->

<?php
$star = "<font color='#FF0000'>*</font>";
if(isset($_REQUEST['msg']))
{
	$msg = "Sorry !!! You can't delete it. ( Dependency )";
}
else if(isset($_REQUEST['msg1']))
{
	$msg = "Deleted Successfully.";
}
else{}
?>

<form name="group" id="group" method="post" action="process/group.process.php">
<table align='center'>
<?php
if(isset($msg))
{
	if(isset($_POST['ShowData']))
	{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
	}
	else
	{
	?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $msg; ?></b></font></td></tr>
	<?php
	}
}
else
{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
}
?>


		<tr>
			<td>SrNo</td>
			<td><input type="text" name="srno" id="srno" /></td>
		</tr>
		<tr>
			<td>Group Name</td>
			<td><input type="text" name="groupname" id="groupname" /></td>
		</tr>
		<tr>
			<td colspan="2" align="center"><input type="hidden" name="id" id="id"><input type="submit" name="insert" id="insert" value="Insert" style="background-color:#E8E8E8;"></td>
		</tr>
</table>
</form>


<table align="center">
<tr>
<td>
<?php
echo "<br>";
echo $str1 = $obj_group->pgnation();
/*echo "<br>";
echo $str = $obj_group->display1($str1);
echo "<br>";
$str1 = $obj_group->pgnation();
echo "<br>";*/
?>
</td>
</tr>
</table>
</center>
</div>
<?php include_once "includes/foot.php"; ?>