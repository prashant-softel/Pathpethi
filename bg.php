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

include_once("classes/bg.class.php");
$obj_bg=new bg($m_dbConn);
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsbg.js"></script>
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
        document.getElementById("bg1").focus();
    }
	</script>
</head>

<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();go1();">
<?php }else{ ?>
<body onLoad="go1();">
<?php } ?>

<center><font color="#43729F" size="+1"><b>Blood Group Master</b></font></center>

<center>
<form name="bg" id="bg" method="post" action="process/bg.process.php" onSubmit="return val();">
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
			<td>Enter Blood Group</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="bg" id="bg1" /></td>
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
$str1 = $obj_bg->pgnation();
echo "<br>";
echo $str = $obj_bg->display1($str1);
echo "<br>";
$str1 = $obj_bg->pgnation();
echo "<br>";
?>
</td>
</tr>
</table>

</center>
<?php include_once "includes/foot.php"; ?>
