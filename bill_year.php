<?php include_once "ses_set_s.php"; 
include_once("classes/dbconst.class.php");?>
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
include_once("classes/bill_year.class.php");
$obj_bill_year = new bill_year($m_dbConn);
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <!--<script type="text/javascript" src="js/jquery_min.js"></script>-->
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsbill_year.js"></script>
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
<br>
<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Bill Year</div>

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
<form name="bill_year" id="bill_year" method="post" action="process/bill_year.process.php" onSubmit="enable();">
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
			<td>Year Description </td>
			<td><input type="text" name="YearDescription" id="YearDescription" /></td>
		</tr>
        <tr>
        	<td>Previous Year Description </td>
            <td> 
            	<select name="PrevYearID" id="PrevYearID">
                <option value="0">Please Select</option>
                <?php echo $combo_prevID = $obj_bill_year->combobox("select `YearID`, `YearDescription` from `year` where `status` = 'Y'", 0); ?>
                </select>
            </td>
        </tr>		
         <tr>
        	<td>Freeze Year</td>
            <td> 
            		<input  type="checkbox" name="freeze_year"  id="freeze_year"  value="1"   <?php if($_SESSION['role'] <> ROLE_SUPER_ADMIN){ echo 'disabled';} ?> />	
            </td>
        </tr>	
        <tr><td><br></td></tr>	
		<tr>
			<td colspan="2" align="center">
            	<input type="hidden" name="id" id="id">
            	<input type="hidden" name="prev_year_status" id="prev_year_status" value="0">
            	<input type="submit" name="insert" id="insert" value="Insert" style="padding: 6px 12px; color:#fff;background-color: #2e6da4;" class="btn btn-primary">
            </td>
		</tr>
</table>
</form>


<table align="center">
<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_bill_year->pgnation();
echo "<br>";
//echo $str = $obj_bill_year->display1($str1);
echo "<br>";
//$str1 = $obj_bill_year->pgnation();
echo "<br>";
?>
</td>
</tr>
</table>
</center>
</div>
<?php include_once "includes/foot.php"; ?>
