<?php include_once("includes/head_s.php");
include_once("classes/ledger_details.class.php");

$obj_ledger_details = new ledger_details($m_dbConn);

?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	
	<script type="text/javascript" src="js/populateData.js"></script>
	<script type="text/javascript" src="js/ledger_details.js"></script>

</head>

<body>
<br>
<div id="middle">

<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Ledger Details</div>

<BR/>
<BR/>

<form name="unit" id="unit" method="post" action="ledger_details.php" >
<center>
<table align='center'>

<tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Group  </td>
            <td>&nbsp; : &nbsp;</td>
            <td><select name="gid" id="group_id"  onChange="get_category(this.value);">
            
                	<?php echo $combo_group= $obj_ledger_details->combobox("select `id`,`groupname` from `group`",$_REQUEST['gid']); ?>
                </select>
            </td>
</tr>


<tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Category </td>
            <td>&nbsp; : &nbsp;</td>
			<td>
                <select name="cid" id="category_id" style="width:142px;">
				</select>
            </td>
		</tr>
        
         <tr><td colspan="4">&nbsp;</td></tr>
         
        <tr align="left">
        <td colspan="4" align="center"><input type="submit" name="fetch" id="fetch" value="Fetch" style="width:100px;" ></td>
		</tr>	

</table>
</center>
</form>
<!--
<table align="center" style="display:none;">
<tr>
<td>
-->
<?php //echo $_POST['group_id'];?>
<?php
echo "<br>";
if(isset($_REQUEST['cid']))
{
	?>
    
<script> get_category(document.getElementById('group_id').value, <?php echo $_REQUEST['cid']; ?> );      </script>
<?php
$str1 = $obj_ledger_details->pgnation($_REQUEST['gid'], $_REQUEST['cid']);
/*echo "<br>";
echo $str = $obj_ledger_details->display1($str1);
echo "<br>";
$str1 = $obj_ledger_details->pgnation($_REQUEST['gid'], $_REQUEST['cid']);
echo "<br>";*/
}
?>
<!--
</td>
</tr>
</table>

-->
</div>
<?php include_once('includes/foot.php'); ?>