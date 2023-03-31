<?php include_once "ses_set_as.php"; ?>
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
include_once("classes/society_group.class.php");
$obj_society_group = new society_group($m_dbConn);
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jssociety_group.js"></script>
    <script language="javascript" type="text/javascript">
    function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }
    </script>
    <script type="text/javascript" language="javascript">
    function del_group(grp_id)
    {
        var conf = confirm("Are you sure ??? you want to delete it ??");
        if(conf==true)
        {
            remoteCall("ajax/del_group.php","grp_id="+grp_id,"res_del_group");
        }
    }
    function res_del_group()
    {
        var res = sResponse;	//alert(res);
        window.location.href = 'list_society_group.php?grp&tm=<?php echo time();?>&del&bb';
    }
	</script>
</head>

<?php if(isset($_REQUEST['add']) || isset($_REQUEST['up']) || isset($_REQUEST['del'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>

<center><font color="#43729F" size="+1"><b>View group of society</b></font></center>

<?php if(!isset($_SESSION['sadmin'])){?>
<br>
<center>
<a href="society_group.php?grp" style="color:#00F; text-decoration:none;"><b><u>Create group of society</u></b></a>
</center>
<br>
<?php } ?>

<center>
<?php if(isset($_SESSION['sadmin'])){?>
<br>
<form method="get">
<table align="center" border="0">
<tr>
	<td>Society</td>
    <td>&nbsp; : &nbsp;</td>
    <td>
    <select name="society_id" id="society_id" style="width:250px;" onChange="get_wing(this.value);">
	<?php echo $combo_society = $obj_society_group->combobox07("select society_id,concat_ws(' - ',society_name,landmark) from society where status='Y' order by society_id desc",$_REQUEST['society_id']); ?>
    </select>
    </td>
</tr>

<tr><td colspan="3">&nbsp;</td></tr>

<tr><td colspan="3" align="center"><input type="submit" name="search" id="insert" value="Search"></td></tr>

</table>
<?php }?>
<input type="hidden" name="grp" value="grp">
</form>


<table align="center" border="0">
<tr>
	<td valign="top" align="center">
    <font color="red">
	<?php 
		if(isset($_GET['add']))
		{
			echo "<b id=error>Record added Successfully</b>";
		}
		else if(isset($_GET['up']))
		{
			echo "<b id=error>Record updated Successfully</b>";
		}
		else if(isset($_GET['del']))
		{
			echo "<b id=error>Record deleted Successfully</b>";
		}
		else
		{
			echo '<b id=error></b>';
		} 
	?>
    </font>
    </td>
</tr>

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

<br><br><br><br>

</center>
<?php include_once "includes/foot.php"; ?>
