<?php include_once "ses_set.php"; ?>
<?php
include_once("includes/header.php");

include_once("classes/society.class.php");
$obj_society = new society($m_dbConn);
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jssociety.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',4000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }
	</script>
    
    <!--<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />
	<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
    <script type="text/javascript" src="javascript/jquery.clockpick.1.2.4.js"></script>
    <script type="text/javascript" src="javascript/ui.core.js"></script>
    <script type="text/javascript" src="javascript/ui.datepicker.js"></script>-->
    <script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
    <script type="text/javascript">
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "yy-mm-dd", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true 
        })});
            
    </script>

</head>

<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>

<div id="middle">
<center><font color="#43729F" size="+1"><b>Society Master</b></font></center>

<form name="society" id="society" method="post" action="process/society.process.php" onSubmit="return val();">
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
			<td>Select Wing</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            <select name="wing_id" id="wing_id" style="width:142px;">
				<?php echo $combo_wing_id = $obj_society->combobox("select wing_id,wing from wing where status='Y'"); ?>
			</select>
            </td>
		</tr>
        
		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Enter Society Name</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="society_name" id="society_name" /></td>
		</tr>
        
		<tr>
        	<td valign="top"><?php echo $star;?></td>
			<td valign="top">Society Address</td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td><textarea name="society_add" id="society_add" rows="4" cols="20"></textarea></td>
		</tr>
        
		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>City</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="city" id="city" value="Mumbai"/></td>
		</tr>
        
		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Region</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="region" id="region" value="Maharashtra" /></td>
		</tr>
        
		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Postal Code</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="postal_code" id="postal_code" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)"/></td>
		</tr>
        
		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Country</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="country" id="country" value="India" /></td>
		</tr>
        
		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Phone No. 1</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="phone" id="phone" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)"/></td>
		</tr>
        
		<tr>
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Phone No. 2</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="phone2" id="phone2" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)"/></td>
		</tr>
        	
		<tr>
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Fax</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="fax" id="fax" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)"/></td>
		</tr>
        
		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Email id</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="email" id="email" /></td>
		</tr>
        
		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Member Since</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="member_since" id="member_since"  class="basics" size="10" readonly/></td>
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
$str1 = $obj_society->pgnation();
echo "<br>";
echo $str = $obj_society->display1($str1);
echo "<br>";
$str1 = $obj_society->pgnation();
echo "<br>";
?>
</td>
</tr>
</table>

</body>
</html>
