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

include_once("classes/state_master.class.php");
$obj_state_master = new state_master($m_dbConn);
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsstate_master.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
	{
		setTimeout('hide_error()',6000);	
	}
	function hide_error()
	{
		document.getElementById('error').style.display = 'none';	
	}
	function go1()
	{
		document.getElementById("state1").focus();
	}	
	</script>
</head>

<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();go1();">
<?php }else{ ?>
<body onLoad="go1();">
<?php } ?>

<center><font color="#43729F" size="+1"><b>State Master</b></font></center>

<center>
<form name="state_master" id="state_master" method="post" action="process/state_master.process.php">
<table align='center'>
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
		else
		{
			//$msg = '';	
		}
	?>
    <table align='center'>
		<?php
		if(isset($msg))
		{
			if(isset($_POST["ShowData"]))
			{
		?>
				<tr height="40" id="error"><td colspan="4" align="center"><font color="red" size="2"><b><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
		<?php
			}
			else
			{
			?>
            	<tr height="40" id="error"><td colspan="4" align="center"><font color="red" size="2"><b><?php echo $msg; ?></b></font></td></tr>	   
            <?php		
			}
		}
		else
		{
		?>	
				<tr height="40" id="error"><td colspan="4" align="center"><font color="red" size="2"><b><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
        <?php
		}
		?>
        
	<tr>
        <td valign="top"><?php echo $star;?></td>
        <td>Enter State</td>
        <td>&nbsp;:&nbsp;</td>
        <td><input type="text" name="state" id="state1" /></td>
    </tr>
    
    <tr>
        <td colspan="4">&nbsp;</td>
    </tr>
    
    <tr>
        <td colspan="4" align="center">
        <input type="hidden" name="id" id="id">
        <input type="submit" name="insert" id="insert" value="Insert" style="background-color:#E8E8E8;">
        </td>
    </tr>
</table>
</form>
<br>


<table align="center">
<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_state_master->pgnation();
echo "<br>";
echo $str = $obj_state_master->display1($str1);
echo "<br>";
$str1 = $obj_state_master->pgnation();
echo "<br>";
?>
</td>
</tr>
</table>

</body>
</html>


</center>
<?php include_once "includes/foot.php"; ?>
