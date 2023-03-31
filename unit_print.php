<?php include_once "ses_set.php"; ?>
<?php
include_once("classes/unit_print.class.php");
$obj_unit_print = new unit_print($m_dbConn);

$_SESSION['ssid'] = $_REQUEST['ssid'];
$_SESSION['wwid'] = $_REQUEST['wwid'];
?>
 

<html>
<head>
<script language="javascript" type="application/javascript">
function print_form()
{
	document.getElementById('prt1').style.display = 'none';	
	document.getElementById('prt2').style.display = 'none';	
	
	window.print();
}
</script>

</head>

<body>

<div id="middle">

<table align="center" border="0" width="970">
<tr>
	<td align="center"><font size="+2"><b><?php echo $_SESSION['society_name'];?></b></font></td>
</tr>
<tr>
	<td align="center"><font size="+2"><b>Unique code for member</b></font></td>
</tr>
</table>

<?php if(isset($_SESSION['admin'])){?>
<center>
<a href="javascript:void(0);" onClick="print_form();" id="prt1"><img src="images/print.png" width="40" width="40" /></a>
</center>
<?php } ?>

<table align="center">
<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_unit_print->pgnation();
echo "<br>";
echo $str = $obj_unit_print->display1($str1);
echo "<br>";
$str1 = $obj_unit_print->pgnation();
echo "<br>";
?>
</td>
</tr>
</table>

<?php if(isset($_SESSION['admin'])){?>
<center>
<a href="javascript:void(0);" onClick="print_form();" id="prt2"><img src="images/print.png" width="40" width="40" /></a>
</center>
<?php } ?>

</body>
</html>
