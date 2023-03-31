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

if(isset($_GET['ssid'])){if($_GET['ssid']<>$_SESSION['society_id']){?><script>window.location.href = "logout.php";</script><?php }}

include_once("classes/bill_period.class.php");
$obj_bill_period = new bill_period($m_dbConn);
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >  
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsbill_period.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
		$(document).ready(function()
		{
			$("#error").show();
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
	
	/*function makeEnable()
{
	alert("Please select cycle to add");
	var x=document.getElementById("Cycle");
	x.disabled=false;
		
}
*/
	</script>
</head>
<?php if(isset($_REQUEST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<?php } ?>

<body>
<br>
<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Bill Period</div>


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
<center>
<form name="bill_period" id="bill_period" method="post" action="process/bill_period.process.php">
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

<!--
		<tr>
			<td>Type</td>
			<td><input type="text" name="Type" id="Type" /></td>
		</tr>
    -->    
        <tr>
        
				<td>Billing Cycle<?php echo $star; ?></td>        
				<td>
                <select name="Cycle" id="Cycle"> <!--disabled="disabled"-->
              <?php echo $combo_state=$obj_bill_period->combobox("select ID,Description from billing_cycle_master",'0');?>  
                  </select></td>	        
        
        </tr>
        
        
        <!---
        <tr>
			<td>Year</td>
			<td><input type="text" name="Year" id="Year" /></td>
		</tr>
        ---->
        
		<tr>
			<td>Bill Year</td>
			<td>
		<select name="YearID" id="YearID">
		<?php echo $combo_state = $obj_bill_period->combobox("select YearID,YearDescription from year where status='Y' ORDER BY YearID DESC", '0'); ?>
		</select>
	</td>
		</tr>
		<tr>
			<td colspan="2" align="center"><input type="hidden" name="id" id="id"><input type="submit" name="insert" id="insert" value="Insert" style="color: #fff;background-color: #337ab7;border-color: #2e6da4;width:30%;height:20%;margin-top:5%"></td>
		</tr>
</table>
</form>


<table align="center">
<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_bill_period->pgnation();
/*echo "<br>";
echo $str = $obj_bill_period->display1($str1);
echo "<br>";
$str1 = $obj_bill_period->pgnation();
echo "<br>";*/
?>
</td>
</tr>
</table>
</center>
</div>
<?php include_once "includes/foot.php"; ?>
