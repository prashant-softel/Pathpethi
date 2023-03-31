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

include_once("classes/desg.class.php");
$obj_desg=new desg($m_dbConn);
?>
 
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsdesg.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }
    function go1()
    {
        document.getElementById("desg1").focus();
    }	
	</script>
</head>

<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();go1();">
<?php }else{ ?>
<body onLoad="go1();">
<?php } ?>
<br>
<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Designation Master</div>


<center>
<form name="desg" id="desg" method="post" action="process/desg.process.php" onSubmit="return val();">
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
    <table align='center' border="0">
		<?php
		if(isset($msg))
		{
			if(isset($_POST["ShowData"]))
			{
		?>
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
		<?php
			}
			else
			{
			?>
            	<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $msg; ?></b></font></td></tr>	   
            <?php		
			}
		}
		else
		{
		?>	
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
        <?php
		}
		?>

		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Enter Designation</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="desg" id="desg1" /></td>
		</tr>
        
		<tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td colspan="4" align="center">
            <input type="hidden" name="id" id="id">
            <input type="submit" name="insert" id="insert" value="Insert">
            </td>
		</tr>
</table>
</form>


<table align="center">
<tr>
<td align="center">
<?php
echo "<br>";
$str1 = $obj_desg->pgnation();
/*echo "<br>";
echo $str = $obj_desg->display1($str1);
echo "<br>";
$str1 = $obj_desg->pgnation();
echo "<br>";*/
?>
</td>
</tr>
</table>

</center>
</div>
<?php include_once "includes/foot.php"; ?>
