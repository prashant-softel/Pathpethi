<?php 

include_once "ses_set_as.php"; 
 ?>
<?php
 // Turn off all error reporting
        error_reporting(0);
if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
else
{
	include_once("includes/head_s.php");
}?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Income Register</title>
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

<body>

<center>

<form name="expense_report" id="expense_report" method="post" action="income_register_report.php">
<table>

<tr><h2><font size="+6" style="width:50px">Income Detailed Report</font></h2></tr>
<tr><div id="show" style="font-weight:bold;color:#FF0000"></div></tr>
<tr><font size="+6" style="width:50px">Select period for income register report</font></tr>
<?php if(isset($_GET['temp']))
     {
		 ?>
         
	<script>
    document.getElementById('show').innerHTML = "Please Select Period..";
    
    </script>	 
		 
<?php	}
	?>
<tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>From</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="from_date" id="from_date"  class="basics" size="10" readonly  style="width:80px;"/></td>
		</tr>

<tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>To</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="to_date" id="to_date"  class="basics" size="10" readonly  style="width:80px;"/></td>
		</tr>
<tr><td>&nbsp;&nbsp;</td></tr>

<tr>
			<td colspan="4" align="center">
            
            <input type="submit" name="insert" id="insert" value="Generate Report">
            </td>
		</tr>
</table>
</form>


</body>
</html>