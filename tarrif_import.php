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
include_once("classes/changelog.class.php");
include_once("classes/tarrif_import.class.php");
$obj_tarrif_import = new tarrif_import($m_dbConn);
$tarrif_import=$obj_tarrif_import->CSVTarrifImport();
//print_r($obj_unit_import);
$_SESSION['society_id']=$_REQUEST['sid'];

?>

<html>
<head>

</head>



<center><font color="#43729F" size="+1"><b>Import Tarrif Details</b></font></center>
<br>

<center>
<a href="billmaster.php?imp" style="color:#00F; text-decoration:none;"><b><u>Back to list</u></b></a>
</center>
<BR />
<body>

<form name="" action="" method="post" enctype="multipart/form-data">
<center>
<table>






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