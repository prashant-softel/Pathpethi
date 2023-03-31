<?php include_once "ses_set.php"; ?>
<?php
include_once("includes/header.php");

include_once("classes/home_status.class.php");
$obj_home_status = new home_status($m_dbConn);
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jshome_status.js"></script>
    <script language="javascript" type="application/javascript">
    function go_error()
    {
        setTimeout('hide_error()',4000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }
    function go1()
    {
        document.getElementById("status1").focus();
    }
    </script>

</head>

<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();go1();">
<?php }else{ ?>
<body onLoad="go1();">
<?php } ?>

<div id="middle">
<center><font color="#43729F" size="+1"><b>Home Status Master</b></font></center>

<form name="home_status" id="home_status" method="post" action="process/home_status.process.php">

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
			<td>Enter Home Status</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="status" id="status1" /></td>
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
<td>
<?php
echo "<br>";
$str1 = $obj_home_status->pgnation();
echo "<br>";
echo $str = $obj_home_status->display1($str1);
echo "<br>";
$str1 = $obj_home_status->pgnation();
echo "<br>";
?>
</td>
</tr>
</table>

</body>
</html>
