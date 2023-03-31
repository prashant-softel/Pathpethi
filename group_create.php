<?php
include_once("includes/head_s.php");

?>

<?php
include_once("classes/group_create.class.php");
$obj_group_create = new group_create($m_dbConn,$m_dbConnRoot);
?>
 <?php
 
 include_once ("classes/dbconst.class.php");
//print_r($_SESSION);
include_once( "classes/include/fetch_data.php");
$objFetchData = new FetchData($m_dbConn);
//$objFetchData->GetSocietyDetails($_SESSION['society_id']);
 
 
 ?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <script type="text/javascript" src="lib/js/jquery.min.js"></script>
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/group_create.js"></script>



    <script language="javascript" type="application/javascript">
	function go_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
		});
        setTimeout('hide_error()',8000);	
    }
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    }

	</script>

 
select.dropdown {
    position: relative;
    width: 200px;
    margin: 0 auto;
    padding: 10px 10px 10px 30px;
	appearance:button;
	overflow: scroll;


    /* Styles */
    background: #fff;
    border: 1px solid silver;
    cursor: pointer;
    outline: none;
	
}

</style>

</head>
<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['nul'])) { ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>
<br>
<div class="panel panel-info" id="panel" style="display:">
        <div class="panel-heading" id="pageheader">Select Society Name </div>
<?php
$star = "<font color='#FF0000'>*</font>";
if(isset($_REQUEST['msg']))
{
	$msg = "Sorry !!! You can't delete it. ( Dependency )";
}
else if(isset($_REQUEST['msg1']))
{
	$msg = "Deleted Successfully.";
}
else{}
?><br><br>
<center>
<a href="gallery_group.php" style="color:#00F; text-decoration:none;"><b><u>Back</u></b></a><br/>
</center>

<br>
<center>
<form name="group_create" id="group_create" method="post"  enctype="multipart/form-data" action="process/group_create.process.php">
<table align='center'>
<?php
if(isset($msg))
{
	if(isset($_POST['ShowData']))
	{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
	}
	else
	{
	?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $msg; ?></b></font></td></tr>
	<?php
	}
}
else
{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
}


?>
		<tr>
			<td><b>Select Group</b></td>
            <td>&nbsp; : &nbsp;</td>
			<td>
		<select name="group_id" id="group_id" onChange="getsociety_list(this.value);">
	<?php //echo $_REQUEST['group_id']; ?> 	
<?php echo $group_create = $obj_group_create->combobox("select group_id,group_name from `group` where status='Y' order by group_id desc",$_REQUEST['group_id']); ?>
		</select>
	</td>
		</tr>
		<tr>
			<td><b>Select Society</b></td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            <div style="overflow-y:scroll;overflow-x:hidden;width:500px; height:200px; border:solid #CCCCCC 2px;" name="society_id[]" id="society_id" >
           <!--<select name="society_id[]" id="society_id" multiple="multiple" style="overflow-y:scroll;overflow-x:hidden;width:400px; height:200px; border:solid #CCCCCC 2px; " class="dropdown" >
	
--><?php echo $group_create = $obj_group_create->combobox11 ("select society_id,society_name from society where status='Y' order by society_id desc ","society_id[]","society_id", $_REQUEST['group_id']); ?>
<!--</select>--></div>
	</td>
   
		</tr>
         <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td colspan="4" align="center"><input type="hidden" name="id" id="id"><input type="submit" name="insert" id="insert" value="Insert" style=" width:100px; height:30px; background-color:#D6D6D6; "></td>
		</tr>
</table>
</form>


<table align="center">
<tr>
<td>
<?php
 "<br>";
/*$str1 = $obj_group_create->pgnation();
echo "<br>";
/*echo $str = $obj_group_create->display1($str1);
echo "<br>";
$str1 = $obj_group_create->pgnation();
echo "<br>"
*/?></td>
</tr>
</table>
</div>
</center>
<?php //echo $_POST['group_id'];?>
<?php

echo "<br>";
if(isset($_REQUEST['society_id']))
{
	?>
   <script> getgroup_create(document.getElementById('group_id').value, <?php echo $_REQUEST['society_id']; ?> );</script> 
 
<?php }
?>

</body>
</html>
<?php include_once "includes/foot.php"; ?>