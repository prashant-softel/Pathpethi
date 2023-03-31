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

include_once("classes/wing_import.class.php");
$obj_wing_import = new wing_import($m_dbConn);
$obj_wing_import=$obj_wing_import->CSVWingImport();
$_SESSION['society_id']=$_REQUEST['sid'];
//print_r($obj_unit_import);
//echo $_SESSION['society_id'];
?>

<html>
<head>

</head>



<center><font color="#43729F" size="+1"><b>Import Wing</b></font></center>
<br>

<center>
<a href="society_view.php?imp" style="color:#00F; text-decoration:none;"><b><u>Back to list</u></b></a>
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

</center>
</form>

</body>
</html>