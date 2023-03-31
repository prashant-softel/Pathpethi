<?php
include_once("classes/testing.class.php");
$obj_testing = new testing();
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <script type="text/javascript" src="jquery/jquery_min.js"></script>
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jstesting.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
		});
        setTimeout('hide_error()',8000);	
    }
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    }
	</script>
</head>
<body>

<h2 align="center" id='top'>Testing</h2>

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

<form name="testing" id="testing" method="get" action="process/testing.class.php">
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
			<td>name</td>
			<td>
		<input type="checkbox" name="name" id="name" value="" checked="checked">	</td>
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
$str1 = $obj_testing->pgnation();
echo "<br>";
echo $str = $obj_testing->display1($str1);
echo "<br>";
$str1 = $obj_testing->pgnation();
echo "<br>";
?>
</td>
</tr>
</table>

</body>
</html>
