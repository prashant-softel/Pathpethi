<?php include_once "ses_set_s.php"; ?>
<?php

	include_once("includes/head_s.php");

?>
<?php
include_once("classes/account_category.class.php");
$obj_account_category = new account_category($m_dbConn);
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >  
        
	<script type="text/javascript" src="js/ajax.js"></script>
    <script type="text/javascript" src="js/populateData.js"></script>
	<script type="text/javascript" src="js/account_category.js"></script>
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
</head>

<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg'])){ ?>
<body onLoad="go_error();">
<?php } ?>

<body>
<center>
<br>
<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Account Category</div>
<div style="text-align:right;padding-right: 50px;padding-top: 10px;">
        <input type="button" id="insertView" value="Ledger"  class="btn btn-primary"  style="width:100px;padding: 2px 7px;" onClick="window.open('ledger.php', '_blank')" />
        </div>

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
?>

<form name="account_category" id="account_category" method="post" action="process/account_category.process.php">
<table  align="center" style="margin-top: -35px;">
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
			<td>SrNo</td>
			<td><input type="text" name="srno" id="srno" /></td>
		</tr>
		<tr>
			<td>Group ID<?php echo $star; ?></td>
			<td>
		<select name="group_id" id="group_id" onChange="get_category(this.value);">
		<?php echo $combo_society = $obj_account_category->combobox("select `id`,`groupname` from `group`",0); ?>

		</select>
	</td>
		</tr>
        <tr>
			<td>Parent Category ID<?php echo $star; ?></td>
			<td>
            	<select name="parentcategory_id" id="parentcategory_id">
                <option value='1'>Primary</option>
                <?php echo $combo_parentID = $obj_account_category->combobox("select `category_id`, `category_name` from `account_category` where `group_id` = 1", 0); ?>
                </select>
            </td>
		</tr>
		<tr>
			<td>Category Name<?php echo $star; ?></td>
			<td><input type="text" name="category_name" id="category_name" /></td>
		</tr>
		<tr>
			<td>Note</td>
			<td><input type="text" name="description" id="description" /></td>
		</tr>		
        <tr>
			<td>FD Category</td>
			<td><input type="checkbox" name="is_fd_category" id="is_fd_category" value="1" /></td>
		</tr>		
		<!--<tr>
			<td>Is Bill Item</td>
			<td>
		<input type="checkbox" name="is_bill_item" id="is_bill_item" value="1" checked="checked"/>	</td>
		</tr>-->
	<!--	<tr>
			<td>Opening Balance</td>
			<td><input type="text" name="opening_balance" id="opening_balance" /></td>
		</tr> -->
		<tr>
			<td colspan="2" align="center"><input type="hidden" name="id" id="id"><input type="submit" name="insert" id="insert" value="Insert" style="background-color:#E8E8E8;"></td>
		</tr>
</table>
</form>


<table align="center">
<tr>
<td>
<?php
echo "<br>";
echo $str1 = $obj_account_category->pgnation();
/*echo "<br>";
echo $str = $obj_account_category->display1($str1);
echo "<br>";
$str1 = $obj_account_category->pgnation();
echo "<br>";*/
?>
</td>
</tr>
</table>
</center>
</div>
<?php include_once "includes/foot.php"; ?>