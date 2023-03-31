<?php include_once "ses_set_s.php"; ?>
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
include_once("classes/members_commitee.class.php");
$obj_commitee = new commitee($m_dbConn);
$member_details = $obj_commitee->getMemberDetailsEx();
$commiteeDetails = $obj_commitee->getCommiteeDetails();
$commiteeCate = $obj_commitee->getCommiteeCategory();
//print_r($commiteeCate );
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >      
    <script type="text/javascript" src="js/jscommitee.js"></script>  
    <script type="text/javascript" src="js/ajax.js"></script>		
    <script language="javascript" type="application/javascript">	
	function go_error()
    {		
		document.getElementById('error').style.display = 'block';
        setTimeout('hide_error()',8000);	
    }
    function hide_error()
    {		
		document.getElementById('error').innerHTML = '';
        document.getElementById('error').style.display = 'none';
    }
	
	function show()
	{
		//document.getElementById('show_commitee_member').style.display = 'none';
		//document.getElementById('show').style.display = 'block';
		document.getElementById('show_commitee_member').innerHTML = '<td colspan="2" align="center"> No Of Commitee Members :  <input type="text" name="no_of_commitee_members" id="no_of_commitee_members" /> </td>';
	}
	</script>
</head>
<body>
<br><br>
 <?php if( $_SESSION['View'] == 'ADMIN')
            {?>
<div class="panel panel-info" id="panel" style="display:none">
 <!--<div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:100%">-->
<?php }
		else
        {?>
        <div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">
        <?php }?>
<div class="panel-heading" id="pageheader">Managing Committee</div>        
<center>

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

if($commiteeDetails <> "" && !isset($_REQUEST['key']))
{	
?>
<br />
<form name="update_commitee" id="update_commitee" method="post" action="commitee.php?key=update" onSubmit=" ">
<table align="center" style="width:100%;" class="table table-bordered table-hover table-striped">
    <tr style="background-color:#d9edf7;" ><th width="25%"  valign="middle" style="font-size: 12px;"><center>Designation</center></th>
    <th width="40%"  style="font-size: 12px;"><center>Name of Committee Member</center></th>
    <th width="30%"  style="font-size: 12px;"><center>Responsibilities</center></th>
    </tr>        
<?php 
	$count = 0;
	for($i = 0; $i < sizeof($commiteeDetails); $i++)
	{		
?>	
<tr>
	<th  style="width:25%;"> <?php echo $commiteeDetails[$i]['position']; ?>: </th>
    <td  style="width:40%;"> <?php echo $member_details[$commiteeDetails[$i]['member_id']]; if($commiteeDetails[$i]['position'] == 'Commitee Member') { $count++; ?>  <a href="#" onClick="deleteCommiteeMember('delete-' + <?php echo $commiteeDetails[$i]['id'] ?>);">
    <?php if($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN)
            {?>
<img src="images/del.gif" align="right" /></a>  <?php }?>
 
<?php }?>
</td>
<td><?php echo $commiteeCate[$commiteeDetails[$i]['member_id']]?> </td>

<?php }
?>


</tr> 


<!--<tr>
	<th style="width:25%;"> Secretory : </th>
    <td style="width:25%;"><?php //echo $commiteeDetails[0]['secretory'];?></td>
</tr>
<tr>
	<th> Join Secretory : </th>
    <td><?php //echo $commiteeDetails[0]['join_secretory'];?></td>
</tr>
<tr>
	<th> Treasurer : </th>
    <td><?php //echo $commiteeDetails[0]['treasurer'];?></td>
</tr>
<tr>
	<th> General Manager : </th>
    <td><?php //echo $commiteeDetails[0]['general_manager'];?></td>
</tr>
<tr>
	<th> Commitee Member : </th>
    <td><?php //echo $commiteeDetails[0]['commitee_member'];?>  </td>
</tr> -->

<?php if($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN)
            {?>
<tr id="show_commitee_member">
	<td colspan="3" align="center"> <input type="button" name="showCommiteeMember" value="Add New Commitee Members" onClick="show();" class="btn btn-primary"/> </td>
    
</tr>
<!--<tr id="show" style="display:none;">
	<td colspan="2" align="center"> No Of Commitee Members :  <input type="text" name="no_of_commitee_members" id="no_of_commitee_members" /> </td>    
</tr>-->
<tr style="display:none;"> <td colspan="3"> <input type="hidden" name="no_of_commitee_member_indb" id="no_of_commitee_member_indb" value="<?php echo $count;?>" /> </td> </tr>
<?php }?>
</table>
<?php if($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN)
            {?>
<div align="center" style="width:80px; height:40px;">
	<input type="submit" name="edit" id="edit" value="Edit" class="btn btn-primary"/> 
</div>
<?php }?>
</form><br><br>
<?php }

else
{  ?>

<form name="members_commitee" id="members_commitee" method="post" action="process/commitee.process.php" onSubmit="">
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

		<?php $list_query = "select mo.mem_other_family_id, CONCAT(CONCAT(u.unit_no,' - '), CONCAT(mo.other_name, IF(mo.coowner = 1, ' (Owner)', ' (Co-Owner)'))) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit JOIN mem_other_family as mo ON mm.member_id = mo.member_id where u.society_id = '" . DEFAULT_SOCIETY . "' and mm.ownership_status = 1 and mo.status = 'Y' and mo.coowner > 0 ORDER BY u.sort_order, mo.coowner";
		?>
		<tr>
			<td>Secretary : </td>
			<td><select name="secretory" id="secretory" >
                 <?php //echo $combo_member = $obj_commitee->combobox("SELECT `unit`, `owner_name` FROM `member_main` WHERE `society_id` = '" . DEFAULT_SOCIETY . "'", 0);
			echo $combo_member = $obj_commitee->combobox($list_query, 0); ?>
                </select>
            </td>
		</tr>
        <tr>
        	<td>Joint Secretary : </td>
            <td>
            	<select name="join_secretory" id="join_secretory">
                 <?php //echo $combo_member = $obj_commitee->combobox("SELECT `unit`, `owner_name` FROM `member_main` WHERE `society_id` = '" . DEFAULT_SOCIETY . "'", 0);
			echo $combo_member = $obj_commitee->combobox($list_query, 0); ?>
                </select>
            </td>
        </tr>
		<tr>
			<td>Treasurer : </td>
			<td>
            	<select name="treasurer" id="treasurer">
                 <?php //echo $combo_member = $obj_commitee->combobox("SELECT `unit`, `owner_name` FROM `member_main` WHERE `society_id` = '" . DEFAULT_SOCIETY . "'", 0);
		       echo $combo_member = $obj_commitee->combobox($list_query, 0); ?>
                </select>
            </td>
		</tr>
        <tr>
			<td>Chairman : </td>
			<td>
            	<select name="chairman" id="chairman">
                 <?php //echo $combo_member = $obj_commitee->combobox("SELECT `unit`, `owner_name` FROM `member_main` WHERE `society_id` = '" . DEFAULT_SOCIETY . "'", 0);
			echo $combo_member = $obj_commitee->combobox($list_query, 0); ?>
                </select>
            </td>
		</tr>
        <?php 	
			$totalCount = $_POST['no_of_commitee_members'] + $_POST['no_of_commitee_member_indb'];	
		 for($i = 1; $i <= $totalCount; $i++)		
			  {
		?>
        <tr>
			<td>Committee Member<?php echo $i; ?> : </td>
			<td>
            	<select name="commitee_member<?php echo $i; ?>" id="commitee_member<?php echo $i; ?>">
                 <?php //echo $combo_member = $obj_commitee->combobox("SELECT `unit`, `owner_name` FROM `member_main` WHERE `society_id` = '" . DEFAULT_SOCIETY . "'", 0);
		       echo $combo_member = $obj_commitee->combobox($list_query, 0); ?>
                </select>
            </td>
		</tr>
        <?php }
		?>
		<tr>
			<td colspan="2" align="center"><input type="hidden" name="id" id="id"><input type="submit" name="insert" id="insert" value="Create" style="background-color:#E8E8E8;"></td>
		</tr>
        <tr><td colspan="2"><input type="hidden" name="no_of_commitee_members" id="no_of_commitee_members" value="<?php echo $totalCount; ?>" /> </td></tr> 
        <script language="javascript"> getCommiteeDetails(); </script>       
</table>
</form>
<?php } ?>
</center>
</div>
<?php include_once "includes/foot.php"; ?>