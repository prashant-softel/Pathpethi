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

include_once("classes/ledger_import.class.php");
$obj_ledger_import = new ledger_import($m_dbConn);
$obj_limport=$obj_ledger_import->CSVLedgerImport($_REQUEST['society_id']);
///echo $_SESSION['society_id'];
//print_r($obj_unit_import);
$_SESSION['society_id']=$_REQUEST['sid'];
//echo $_SESSION['society_id'];
?>

<html>
<head>

</head>



<center><font color="#43729F" size="+1"><b>Import Ledgers</b></font></center>
<br>

<center>
<a href="society_view.php?imp" style="color:#00F; text-decoration:none;"><b><u>Back to list</u></b></a>
</center>
<body>

<form name="" action="" method="post" enctype="multipart/form-data">
<center>
<table>	
<tr>
			<!--<td>Current Society : &nbsp;</td>-->
			<td><select name="default_society" id="default_society" style="display:none">
            	<?php
					echo $combo_society = $obj_ledger_import->combobox("select society_id,concat_ws(' - ',society_name,landmark) from `society` where status='Y' and society_id='".$_SESSION['society_id']."' ", $_SESSION['society_id'], 'Please Select'); 
				?>		
            </select>

            </td>
</tr>
<tr>
<td><label for="file">Filename:</label></td>
<td><input type="file" name="file" id="file" /></td>
<BR/>
<BR/>

<tr>
<td></td>
</tr>
<tr>
</tr>
<tr>
<td></td><td><input type="submit" name="Import" value="Import" id="insert"/></td>
</tr>
</tr></table>
</center>
</form>


<?php include_once "includes/foot.php"; ?>