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

include_once("classes/unit_import.class.php");
$obj_unit_import = new unit_import($m_dbConn);
$obj_unit_import=$obj_unit_import->CSVUnitImport($_REQUEST["sid"]);
//print_r($obj_unit_import);
$_SESSION['society_id']=$_REQUEST['sid'];
//echo $_SESSION['society_id'];
?>

<html>
<head>

</head>

<center><font color="#43729F" size="+1"><b>Import Unit</b></font></center>
<br>

<center>
<a href="wing.php?imp" style="color:#00F; text-decoration:none;"><b><u>Back to list</u></b></a>
</center>

<body>

<form name="" action="" method="post" enctype="multipart/form-data">
<center>
<table>
<tr>
<td><label for="file">Filename:</label></td>
<td><input type="file" name="file" id="file" /></td>
<BR/>
<BR/>

<tr>
<td><input type="submit" name="Import" value="Import" /></td>
</tr>
</table>
</center>
</form>

<?php include_once "includes/foot.php"; ?>