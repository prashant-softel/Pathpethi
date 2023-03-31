<?php include_once("includes/head_s.php");
include_once("classes/dbconst.class.php");
include_once("classes/adduser.class.php");
include_once("classes/add_member_id.class.php");
include_once("classes/initialize.class.php");


//print_r($map_details);

$msg = '';

if(isset($_REQUEST['submit']) && $_REQUEST['submit'] == 'Update')
{
	$objUpdate = new adduser($m_dbConnRoot);
	$status = 1;
	if(isset($_REQUEST['restore']))
	{
		$status = $_REQUEST['restore'];
	}
	
	$result = $objUpdate->updateUserRole($_REQUEST['id'], $_REQUEST['role'], $status);
	
	$aryProfile = array();
	
	$aryProfile['PROFILE_GENERATE_BILL'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_GENERATE_BILL)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_GENERATE_BILL)] : 0;
	
	$aryProfile['PROFILE_EDIT_BILL'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_EDIT_BILL)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_EDIT_BILL)] : 0;
	
	$aryProfile['PROFILE_CHEQUE_ENTRY'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_CHEQUE_ENTRY)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_CHEQUE_ENTRY)] : 0;
	
	$aryProfile['PROFILE_PAYMENTS'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_PAYMENTS)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_PAYMENTS)] : 0;
	
	$aryProfile['PROFILE_BANK_RECO'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_BANK_RECO)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_BANK_RECO)] : 0;
	
	$aryProfile['PROFILE_UPDATE_INTEREST'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_UPDATE_INTEREST)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_UPDATE_INTEREST)] : 0;
	
	$aryProfile['PROFILE_REVERSE_CHARGE'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_REVERSE_CHARGE)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_REVERSE_CHARGE)] : 0;
	
	$aryProfile['PROFILE_SEND_NOTIFICATION'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_SEND_NOTIFICATION)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_SEND_NOTIFICATION)] : 0;
	
	$aryProfile['PROFILE_MANAGE_MASTER'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_MANAGE_MASTER)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_MANAGE_MASTER)] : 0;

	$aryProfile['PROFILE_EDIT_MEMBER'] = (isset($_REQUEST[getFileNameWithoutExt(PROFILE_EDIT_MEMBER)])) ? $_REQUEST[getFileNameWithoutExt(PROFILE_EDIT_MEMBER)] : 0;
	
	$resultProfile = $objUpdate->updateUserProfile($_REQUEST['id'], $aryProfile);
	
	if($result > 0)
	{
		//$map_details[0]['role'] = $_REQUEST['role'];
		$msg = 'Role Updated Successfully';
	}
}

$obj_initialize = new initialize($m_dbConnRoot);
$map_details = $obj_initialize->getMapDetails($_REQUEST['id']);
$profile_details = $obj_initialize->getProfile($map_details[0]['profile']);

$obj_member = new add_member_id($m_dbConn, $m_dbConnRoot);
$member_info = $obj_member->getMemberProfile($map_details[0]['unit_id']);
$memberDetails = $obj_member->getMemberInfo($map_details[0]['login_id']);

function getFileNameWithoutExt($fileName)
{
	return substr($fileName, 0, strrpos($fileName, '.'));
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>
<body>
	<br />
	<center>
	<div class="panel panel-info" id="panel" style="display:none">
	<div class="panel-heading" id="pageheader">Update User Role</div><br />
    	<?php if(isset($_REQUEST['cltID']) && $_REQUEST['cltID'] <> '')
				{ ?>
        <button type="button" class="btn btn-primary" onclick="window.location.href='client_details.php?client=<?php echo $_REQUEST['cltID']; ?>'">Back To Society List</button> 
        <?php 	}
		      else
			  	{  ?> 
        <button type="button" class="btn btn-primary" onclick="window.location.href='add_member_id.php'">Back To List</button>
        <?php 	} ?> <br /><br />
    	<div style="color:#FF0000;font-weight:bold;" id="msg"><?php echo $msg; ?></div>
		<form name="add_user"  method="post" action="">
			<input type="hidden" value="<?php echo $_REQUEST['id']; ?>" name="id" />
			<table id="example" class="display" cellspacing="0" style="width:35%;border:1px solid #CCC;">
				<tr>
					<td>Name<?php echo $star; ?></td><td>:&nbsp;</td>
					<td><?php if($map_details[0]['role'] == ROLE_ADMIN) { echo $memberDetails[0]['name']; }else{ echo $member_info[0]['owner_name']; } ?></td>
				</tr>
				<tr>
					<td>Description<?php echo $star; ?></td><td>:&nbsp;</td>
					<td><?php echo $map_details[0]['desc']; ?></td>
				</tr>
				<tr>
					<td>Code<?php echo $star; ?></td><td>:&nbsp;</td>
					<td><?php echo $map_details[0]['code']; ?></td>
				</tr>
				<tr>
					<td>Status<?php echo $star; ?></td><td>:&nbsp;</td>
					<td><?php echo ($map_details[0]['status'] == '1') ? 'Inactive' : (($map_details[0]['status'] == '2') ? 'Active' : 'Deleted'); ?></td>
				</tr>
				<?php if($map_details[0]['status'] == '3')
				{
					?>
						<tr>
							<td width="65px">Restore User<?php echo $star; ?></td><td>:&nbsp;</td>
							<td><input type="checkbox" name="restore" id="restore" value="2"/></td>
						</tr>
					<?php
				}
				else
				{
					?>
						<tr>
							<td width="60px">Deactivate User<?php echo $star; ?></td><td>:&nbsp;</td>
							<td><input type="checkbox" name="restore" id="restore" value="3"/></td>
						</tr>
					<?php
				}
				?>
				<tr>
					<td>Role<?php echo $star;?></td><td>:&nbsp;</td>
					<td>
						<select name="role" id="role">
							<?php if($map_details[0]['role'] == ROLE_ADMIN)
							{
								?>
								<option value="<?php echo ROLE_ADMIN; ?>"><?php echo ROLE_ADMIN; ?></option>
								<?php
							}
							else
							{
								?>
								<option value="<?php echo ROLE_MEMBER; ?>"><?php echo ROLE_MEMBER; ?></option>
								<option value="<?php echo ROLE_ADMIN_MEMBER; ?>"><?php echo ROLE_ADMIN_MEMBER; ?></option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Profile<?php echo $star;?></td><td>:&nbsp;</td>
					<td>
						<label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_GENERATE_BILL); ?>" <?php if($profile_details[PROFILE_GENERATE_BILL] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Generate Bill</label><br />
                        
                        <label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_EDIT_BILL); ?>" <?php if($profile_details[PROFILE_EDIT_BILL] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Edit Bill</label><br />
						
						<label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_CHEQUE_ENTRY); ?>" <?php if($profile_details[PROFILE_CHEQUE_ENTRY] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Deposit Cheques</label><br />
						
						<label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_PAYMENTS); ?>" <?php if($profile_details[PROFILE_PAYMENTS] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Issue Cheques</label><br />
						
						<label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_BANK_RECO); ?>" <?php if($profile_details[PROFILE_BANK_RECO] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Bank Reconciliation</label><br />
						
						<label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_UPDATE_INTEREST); ?>" <?php if($profile_details[PROFILE_UPDATE_INTEREST] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Update Interest</label><br />
						
						<label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_REVERSE_CHARGE); ?>" <?php if($profile_details[PROFILE_REVERSE_CHARGE] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Reverse Entry</label><br />
                        
						<label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_SEND_NOTIFICATION); ?>" <?php if($profile_details[PROFILE_SEND_NOTIFICATION] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Send Notification</label><br />
                       
                        <label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_MANAGE_MASTER); ?>" <?php if($profile_details[PROFILE_MANAGE_MASTER] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Manage Masters</label><br />

                        <label><input type="checkbox" value="1" name="<?php echo getFileNameWithoutExt(PROFILE_EDIT_MEMBER); ?>" <?php if($profile_details[PROFILE_EDIT_MEMBER] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Edit Member</label>                        
					</td>
				</tr>
				<script>
					document.getElementById('role').value = "<?php echo $map_details[0]['role'] ?>"
				</script>
				<tr>
					<td colspan="3" align="center" style="padding:10px;"><input type="submit" name="submit" value="Update" class="btn btn-primary" /></td>
				</tr>
			</table>
    	</form>
	</div>
    </center>
<?php include_once "includes/foot.php"; ?>
