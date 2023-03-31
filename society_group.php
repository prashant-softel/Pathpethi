<?php include_once "ses_set.php"; ?>
<?php 
if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
?>

<?php
include_once("classes/society_group.class.php");
$obj_society_group = new society_group($m_dbConn);
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jssociety_group.js"></script>
    <script language="javascript" type="application/javascript">
    function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }
    function go1()
    {
        document.getElementById("grp_name").focus();
    }
    </script>
</head>

<?php if(isset($_REQUEST['ShowData']) || isset($_REQUEST['up']) || isset($_REQUEST['del'])){ ?>
<body onLoad="go_error();go1();">
<?php }else{ ?>
<body onLoad="go1();">
<?php } ?>


<div id="middle">
<center><font color="#43729F" size="+1"><b>Create group of society</b></font></center>

<?php if(!isset($_SESSION['sadmin'])){?>
<br>
<center>
<a href="list_society_group.php?grp" style="color:#00F; text-decoration:none;"><b>Back to list</b></a>
</center>
<br>
<?php } ?>

<center>
<form name="society_group" id="society_group" method="post" action="process/society_group.process.php" onSubmit="return val();">
<table align='center'>
	<?php
		$star = "<font color='#FF0000'>*&nbsp;</font>";
	?>
    <table align='center' border="0">
		
        <tr height="20">
        	<td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST['ShowData'];?></b></font></td>
        </tr>
        
		<tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Group Name</td>
            <td>&nbsp;:&nbsp;</td>
			<td><input type="text" name="grp_name" id="grp_name" size="55"/></td>
		</tr>
        
		<tr align="left">
        	<td valign="top"><?php echo $star;?></td>
			<td valign="top">Select Society</td>
            <td valign="top">&nbsp;:&nbsp;</td>
			<td>
                <div style="overflow-y:scroll;overflow-x:hidden;width:350px; height:150px; border:solid #CCCCCC 2px;">
					<?php echo $combo_society_id = $obj_society_group->combobox11("select society_id,society_name from society where status='Y'","society_id[]","society_id"); ?>
                </div>
            </td>
		</tr>
		
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td colspan="4" align="center">
            <input type="hidden" name="id" id="id">
            <input type="submit" name="insert" id="insert" value="Create">
            </td>
		</tr>
</table>
</form>


<table align="center" style="display:none;">
<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_society_group->pgnation();
echo "<br>";
echo $str = $obj_society_group->display1($str1);
echo "<br>";
$str1 = $obj_society_group->pgnation();
echo "<br>";
?>
</td>
</tr>
</table>

</center>
<?php include_once "includes/foot.php"; ?>
