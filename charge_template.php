<?php include_once "ses_set_as.php"; ?>
<?php
include_once("classes/charge_template.class.php");
$obj_charge_template=new charge_template($m_dbConn);
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jscharge_template.js"></script>
    <script language="javascript" type="application/javascript">
	
	</script>
</head>
<body>

<h2 align="center">Charge Template</h2>

<form name="charge_template" id="charge_template" method="post" action="process/charge_template.process.php">
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
			<td>Select Society</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            <select name="socity_id" id="socity_id" style="width:140px;">
				<?php echo $combo_socity_id=$obj_charge_template->combobox("select society_id,society_name from society where status='Y'"); ?>
			</select>
            </td>
		</tr>
        
		<tr>
        	<td valign="top"><?php echo $star;?></td>
			<td>Charge Template Name</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="charge_template_name" id="charge_template_name" /></td>
		</tr>
        
		<tr>
        	<td valign="top"><?php //echo $star;?></td>
			<td valign="top">Description</td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td><textarea name="charge_template_desc" id="charge_template_desc" rows="4" cols="14"></textarea></td>
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
$str1 = $obj_charge_template->pgnation();
echo "<br>";
echo $str = $obj_charge_template->display1($str1);
echo "<br>";
$str1 = $obj_charge_template->pgnation();
echo "<br>";
?>
</td>
</tr>
</table>

</body>
</html>
