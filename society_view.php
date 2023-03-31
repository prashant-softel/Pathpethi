<?php include_once "ses_set_as.php"; ?>
<?php
 // Turn off all error reporting
        error_reporting(0);
include_once("header.php");

if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
else
{
	include_once("includes/head_s.php");
}

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
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }
	
	function del_society(sss_id)
	{
		var conf = confirm("Are you sure ??? you want to delete it ??");
		if(conf==true)
		{
			remoteCall("ajax/del_society.php","sss_id="+sss_id,"res_del_society");
		}
	}
	function res_del_society()
	{
		var res = sResponse;	//alert(res);
		window.location.href = 'society_view.php?imp&tm=<?php echo time();?>&del';
	}
	</script>

</head>

<?php if(isset($_REQUEST['ad']) || isset($_REQUEST['del']) || isset($_REQUEST['up'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>

<?php 
if(isset($_SESSION['sadmin'])){
?>
<br><br>
<center><font color="#43729F" size="+1"><b>List of Society</b></font></center>
<br>

<center>
<a href="society.php?imp" style="color:#00F; text-decoration:none;"><b><u>Add New Society</u></b></a><br/>
<a href="defaults.php?sid=new" style="color:#00F; text-decoration:none;"><b><u>Import New Society</u></b></a>
</center>
<br>
<?php }else{?>
<center><font color="#43729F" size="+1"><b>View your society</b></font></center>
<br>
<?php }?>

<center>
<table align="center">
<tr>
	<td valign="top" align="center"><font color="red"><?php if(isset($_GET['up'])){echo "<b id=error>Record updated Successfully</b>";}else if(isset($_GET['del'])){echo "<b id=error>Record deleted Successfully</b>";}else if(isset($_GET['ad'])){echo "<b id=error>Record added Successfully</b>";}else{} ?></font></td>
</tr>
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

<br><br><br><br><br><br><br>

</center>


<?php include_once "includes/foot.php"; ?>
