<?php include_once("includes/head_s.php");
include_once("check_default.php");
include_once("classes/unit.class.php");
include_once("classes/utility.class.php");
$obj_unit 	 = new unit($m_dbConn, $m_dbConnRoot);
$obj_utility = new utility($m_dbConn);
$last_iid 	 = $obj_unit->getLastIID();
$star 		 = "<font color='#FF0000'>*</font>";
?>
<html>
<head>
	
	<link rel="stylesheet" type="text/css" href="css/pagination.css">
	<link href="css/messagebox.css" rel="stylesheet" type="text/css" />

	<style>
		input[readonly]{
			background-color:whitesmoke;
		}
	</style>

	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/ajax_new.js"></script>
	<script type="text/javascript" src="js/jsunit_08112018.js"></script>
	<script type="text/javascript" src="js/validate.js"></script>
	
	<script language="javascript" type="application/javascript">
		function go_error() {
			setTimeout('hide_error()', 10000);
		}

		function hide_error() {
			document.getElementById('error').style.display = 'none';
		}

		$(function() {
			$.datepicker.setDefaults($.datepicker.regional['']);
			$(".basics").datepicker({
				dateFormat: "dd-mm-yy",
				defaultDate: new Date(),
				showOn: "both",
				buttonImage: "images/calendar.gif",
				buttonImageOnly: true,
				yearRange: "-50:+1",
				maxDate: '0',
				onSelect:function(date){
					var userDate = date.split('-');
					var dob = new Date(userDate[2], userDate[1]-1, userDate[0]);
					console.log('dob', dob);
					var age_dt = new Date(Date.now() - dob.getTime());
					console.log(age_dt.getUTCMonth());
					var age = (age_dt.getUTCFullYear() - 1970);
					$("#member_age").val(age);
				}
			})
		});

	</script>
</head>

<?php if (isset($_REQUEST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])) { ?>

	<body onLoad="go_error();">
	<?php } else { ?>
		<body>
		<?php } ?>
		<br>
		<div id="middle">
			<div class="panel panel-default">
				<div class="panel-heading" id="pageheader" style="text-align:center; ">Add New Member</div>
				<center><br>
					<div style="padding-left: 15px;padding-bottom: 10px;"><button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;" id="btnBack"><i class="fa  fa-arrow-left"></i></button>
						<?php if (isset($_REQUEST['uid']) && $_REQUEST['uid'] <> "") { ?>
							<button type="button" class="btn btn-primary" onClick="viewMemberStatus('<?php echo $_REQUEST['uid']; ?>');" style="height:35px;"><i class="fa  fa-history">&nbsp;Show Transfer History</i></button>
							<button type="button" id="transferOwnership" class="btn btn-primary" onClick="showOrHideOwnershipFields()" style="height:35px;"><i class="fa  fa-exchange">&nbsp;Transfer Ownership</i></button>
						<?php } ?>
					</div>
					<form name="unitForm" id="unitForm" method="post" action="process/unit.process.php" <?php echo $val; ?> onSubmit=" return validateOwnershipTransfer();">
						
						<input type="hidden" name="form_error" id="form_error" value="<?php echo $_REQUEST["form_error"]; ?>" />
						
						<table align='center' style=" width:100%;padding-left:2%">
							<?php
							if (isset($msg)) {
								if (isset($_REQUEST["ShowData"])) {
							?>
									<tr height="30">
										<td colspan="4" align="center">
											<font color="red" style="size:11px;"><b id="error"><?php echo $_REQUEST["ShowData"]; ?></b></font>
										</td>
									</tr>
								<?php
								} else {
								?>
									<tr height="30">
										<td colspan="4" align="center">
											<font color="red" style="size:11px;"><b id="error"><?php echo $msg; ?></b></font>
										</td>
									</tr>
								<?php
								}
							} else {
								?>
								<tr height="30">
									<td colspan="4" align="center">
										<font color="red" style="size:11px;"><b id="error"><?php echo $_REQUEST["ShowData"]; ?></b></font>
									</td>
								</tr>
							<?php
							}
							?>
							<tr>
								<td colspan="2">
									<table width="50%" style="font-size:12px; float:left;" id="PrintableTable">
										<tr align="left">
											<td valign="middle"><?php echo $star; ?></td>
											<td>Member ID</td>
											<td>&nbsp; : &nbsp;</td>
											<td><input type="text" name="member_id" id="member_id" value="<?=$last_iid?>" class="bg-secondary" readonly/></td>
										</tr>
										<tr align="left">
											<td valign="middle"><?php echo $star; ?></td>
											<td>Name</td>
											<td>&nbsp; : &nbsp;</td>
											<td><input type="text" name="member_name" id="member_name" value="<?php echo $_REQUEST['member_name']; ?>"/></td>
										</tr>
										<tr align="left">
											<td valign="middle"><?php echo $star; ?></td>
											<td>Date of Birth</td>
											<td>&nbsp; : &nbsp;</td>
											<td>
												<input type="text" name="birth_date" id="birth_date" class="basics" value="<?php echo $_REQUEST['birth_date']; ?>" />
											</td>
										</tr>
										<tr align="left">
											<td valign="middle"><?php echo $star; ?></td>
											<td>Age</td>
											<td>&nbsp; : &nbsp;</td>
											<td>
												<input type="text" name="member_age" id="member_age" readonly/>
											</td>
										</tr>
										<tr align="left">
											<td>
										<tr>
											<td valign="middle"><?php echo $star; ?></td>
											<td>Gender</td>
											<td>&nbsp;&nbsp; : &nbsp;&nbsp;</td>
											<td>
												<select name="member_gender" id="member_gender">
													<option value="<?= GENDER_MALE ?>">Male</option>
													<option value="<?= GENDER_FEMALE ?>">Female</option>
												</select>
											</td>
										</tr>
								</td>
								<td>
							<tr></tr>
							</td>
							</tr>
							<tr align="left">
								<td valign="middle"></td>
								<td>Address</td>
								<td>&nbsp; : &nbsp;</td>
								<td><textarea cols="50" rows="5" name="member_add" id="member_add"></textarea></td>
							</tr>
						</table>
						<table width="50%" style="font-size:12px; float:left;" id="PrintableTable">
							<tr align="left">
								<td valign="middle"><?php echo $star; ?></td>
								<td>Member Category</td>
								<td>&nbsp; : &nbsp;</td>
								<td>
									<select name="member_category" id="member_category">
										<?php echo $obj_utility->comboboxMemberCat("SELECT * FROM pp_member_category"); ?>
									</select>
								</td>
							</tr>
							<tr align="left">
								<td valign="middle"><?php echo $star; ?></td>
								<td>Mobile No.</td>
								<td>&nbsp; : &nbsp;</td>
								<td>
									<input type="text" name="member_mobile" id="member_mobile" size="10" />
								</td>
							</tr>
							<tr align="left">
								<td valign="middle"><?php echo $star; ?></td>
								<td>Email Id</td>
								<td>&nbsp; : &nbsp;</td>
								<td>
									<input type="text" name="member_email_id" id="member_email_id" />
								</td>
							</tr>

							<tr align="left">
								<td valign="middle"><?php echo $star;
													?></td>
								<td>Aadhar No.</td>
								<td>&nbsp; : &nbsp;</td>
								<td><input type="text" name="member_aadhar_no" id="member_aadhar_no"/></td>
							</tr>

							<tr align="left">
								<td valign="middle"><?php echo $star;
													?></td>
								<td>PAN No.</td>
								<td>&nbsp; : &nbsp;</td>
								<td><input type="text" name="member_pan_no" id="member_pan_no" /></td>
							</tr>
							<tr align="left">
								<td valign="middle"><?php echo $star; ?></td>
								<td>Occupation</td>
								<td>&nbsp; : &nbsp;</td>
								<td>
									<input type="text" name="member_occupation" id="member_occupation" />
								</td>
							</tr>
							<tr align="left">
								<td valign="middle"><?php echo $star; ?></td>
								<td>Area</td>
								<td>&nbsp; : &nbsp;</td>
								<td>
									<input type="text" name="member_area" id="member_area" />
								</td>
							</tr>
							<tr align="left">
								<td valign="middle"><?php echo $star; ?></td>
								<td>City</td>
								<td>&nbsp; : &nbsp;</td>
								<td>
									<input type="text" name="member_city" id="member_city" />
								</td>
							</tr>
							<tr align="left">
								<td valign="middle"><?php echo $star; ?></td>
								<td>State</td>
								<td>&nbsp; : &nbsp;</td>
								<td>
									<input type="text" name="member_state" id="member_state" />
								</td>
							</tr>
						</table>
						<tr>
							<td colspan="6" align="center">
								</br>
								<input type="submit" name="insert" id="insert" value="Insert" class="btn btn-primary" style="color:#FFF;  box-shadow:none;border-radius: 5px; width:100px; height:30px;background-color: #337ab7;border-color: #2e6da4; ">
							</td>
						</tr>
						</table>
						</br>
						</table>
					</form>
					
				</center>
			</div>
		</div>
		<?php
		if (isset($_REQUEST['uid']) && $_REQUEST['uid'] <> '') {
		?>
			<script>
				getunit('edit-' + <?php echo $_REQUEST['uid']; ?>);
			</script>
		<?php
		}?>
		