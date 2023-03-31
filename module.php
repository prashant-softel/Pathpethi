<?php include_once("includes/head_s.php"); 
include_once("classes/initialize.class.php");

$obj_init = new initialize($m_dbConnRoot);

if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'set')
{
	$sr = (isset($_REQUEST['sr'])) ? 1 : 0;
	$notice = (isset($_REQUEST['notice'])) ? 1 : 0;
	$event = (isset($_REQUEST['event'])) ? 1 : 0;
	$document = (isset($_REQUEST['document'])) ? 1 : 0;
	$service = (isset($_REQUEST['service'])) ? 1 : 0;
	$classified = (isset($_REQUEST['classified'])) ? 1 : 0;
	$forum = (isset($_REQUEST['forum'])) ? 1 : 0;
	$directory = (isset($_REQUEST['directory'])) ? 1 : 0;
	$obj_init->updateModuleAccess($sr, $notice, $event, $document, $service, $classified, $forum, $directory);
	$obj_init->getModuleAccess();
}
?>

<center>
	<div class="panel panel-info" id="panel" style="display:none">
		<div class="panel-heading" id="pageheader">Set Module Access</div><br />
    	<div style="color:#FF0000;font-weight:bold;" id="msg"><?php echo $msg; ?></div>
		<form name="add_user"  method="post" action="">
			<input type="hidden" value="set" name="mode" />
			<table id="example" class="display" style="width:35%;text-align:center;border:1px solid #CCC;">
				<tr>
					<th style="text-align:center;">Module</th>
					<th style="text-align:center;">Access</th>
				</tr>
				<tr>
					<td>Service Request</td>
					<td><input type="checkbox" name="sr" id="sr" value="1" <?php if($_SESSION['module']['service_request'] == 1) { echo "checked"; } ?>>
				</tr>
				<tr>
					<td>Notices</td>
					<td><input type="checkbox" name="notice" id="notice" value="1" <?php if($_SESSION['module']['notice'] == 1) { echo "checked"; } ?>>
				</tr>
				<tr>
					<td>Events</td>
					<td><input type="checkbox" name="event" id="event" value="1" <?php if($_SESSION['module']['event'] == 1) { echo "checked"; } ?>>
				</tr>
				<tr>
					<td>Documents</td>
					<td><input type="checkbox" name="document" id="document" value="1" <?php if($_SESSION['module']['document'] == 1) { echo "checked"; } ?>>
				</tr>
				<tr>
					<td>Service Providers</td>
					<td><input type="checkbox" name="service" id="service" value="1" <?php if($_SESSION['module']['service'] == 1) { echo "checked"; } ?>>
				</tr>
				<tr>
					<td>Classified</td>
					<td><input type="checkbox" name="classified" id="classified" value="1" <?php if($_SESSION['module']['classified'] == 1) { echo "checked"; } ?>>
				</tr>
				<tr>
					<td>Forum</td>
					<td><input type="checkbox" name="forum" id="forum" value="1" <?php if($_SESSION['module']['forum'] == 1) { echo "checked"; } ?>>
				</tr>
				<tr>
					<td>Directory</td>
					<td><input type="checkbox" name="directory" id="directory" value="1" <?php if($_SESSION['module']['directory'] == 1) { echo "checked"; } ?>>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" value="Update Access"></td>
				</tr>
			</table>
    	</form>
		<br />
	</div>
</center>

<?php include_once "includes/foot.php"; ?>
        