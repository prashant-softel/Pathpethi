<?php include_once "ses_set_as.php"; ?>
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
}

include_once("classes/society_import.class.php");
$obj_society_import = new society_import($m_dbConn);
$obj_society_import=$obj_society_import->CSVSocietyImport();
//print_r($obj_unit_import);
?>

<html>
<head>

</head>



<center><font color="#43729F" size="+1"><b>Import Society</b></font></center>
<br>

<center>
<a href="society_view.php?imp" style="color:#00F; text-decoration:none;"><b><u>Back to list</u></b></a>
</center>
<body>

<form name="" action="" method="post" enctype="multipart/form-data">
<center>
<table>


<tr height="50" align="center"><td>&nbsp;</td><th colspan="3" align="center"><table align="center"><tr height="25"><th bgcolor="#CCCCCC" width="180">For Society Admin Login</th></tr></table></th></tr>

<tr align="left">
        	<td valign="middle"></td>
			<td>Security Code</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="key" id="key" /></td>
</tr> 

<tr align="left">
        	<td valign="middle"></td>
			<td>User Name</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="admin_user" id="admin_user" /></td>
</tr> 


<tr align="left">
        	<td valign="middle"></td>
			<td>Password</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="admin_pass" id="admin_pass"  /></td>
</tr>  

<tr align="left">
        	<td valign="middle"></td>
			<td>Filename</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="file" name="file" id="file" /></td>
</tr>   
<!--
<tr align="center">
<td valign="top"><label for="file">Filename:</label></td>
<td valign="top"><input type="file" name="file" id="file" /></td>

</tr>
-->
<tr><td colspan="4">&nbsp;</td></tr>
<tr height="50" align="center">
 <td colspan="4" align="center"><input type="submit" name="Import" value="Import" /></td>
</tr>
</table>
</center>
</form>


<?php include_once "includes/foot.php"; ?>