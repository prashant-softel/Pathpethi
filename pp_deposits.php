<?php if (!isset($_SESSION)) {
	session_start();
}  
include_once("classes/dbconst.class.php");
include_once("classes/dbmanager.class.php");
include_once("includes/head_s.php");
include_once("classes/pp_deposits.class.php");
include_once("classes/utility.class.php");

$obj_utility = new utility($m_dbConn, $m_dbConnRoot);
$obj_deposits = new pp_deposits($m_dbConn, $m_dbConnRoot);

$deposit_category = $_REQUEST['account_category'];
$deposit_name = "";
$default_deposit = 0;
$deposit_category_query = "SELECT category_name, category_id FROM account_category  WHERE parentcategory_id = '$deposit_category'";

if($deposit_category == SAVING_ACCOUNT){
	$deposit_name = " - Saving A/C";
	$default_deposit = $deposit_category;
	$deposit_category_query = "SELECT category_name, category_id FROM account_category  WHERE category_id  = '$deposit_category'";	
}
else if($deposit_category == FIXED_DEPOSIT_ACCOUNT){
	$deposit_name = " - Fixed Deposit A/C";
	$depositAmountLabel = 'Deposit Amount';
}
else if($deposit_category == DAILY_DEPOSIT_ACCOUNT){
	$deposit_name = " - Daily Deposit A/C";
	$depositAmountLabel = 'Installment Amount';
}
else if($deposit_category == MONTHLY_DEPOSIT_ACCOUNT){
	$deposit_name = " - Monthly Deposit A/C";
	$depositAmountLabel = 'Installment Amount';
}







?>


<html>

<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css">
	<link href="css/popup.css" rel="stylesheet" type="text/css" />
	<style>
		.textbox{
			padding: 0px;
			margin-right: 3%;
		}

		.item{
			padding-top: 3px;
		}
	</style>

	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsdeposits.js" defer></script>
	<script type="text/javascript" src="js/jsreceipts.js" defer></script>
	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<script src="javascript/moment.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="js/validate.js"></script>
	<script type="text/javascript" src="js/ajax.js"></script>
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
			showOn: "both",
			buttonImage: "images/calendar.gif",
			buttonImageOnly: true,
		});
		});



		function togglePopup(id) {
		var popup = document.getElementById(id);
		popup.classList.toggle('show');
		}

		function LinkGDrive() {
		document.getElementById("frmGDriveLink").submit();
		}
		$(document).ready(function() {
		var iIsGDriveSetup = <?php if (isset($_REQUEST["GDriveFlag"])) {
									echo $_REQUEST["GDriveFlag"];
								} else {
									echo "0";
								} ?>;
		if (iIsGDriveSetup == "1") {
			alert("Google Drive Setup completed Successfully");
		}
		});

		$(document).on('focusout', '#period', function() {
				var months = parseInt($("#period").val());
				if (!isNaN(months)) {
					var startDate = $("#deposit_date").val();
					if(startDate){
						var usDateFormat = startDate.split('-');
						var usDate = new Date(usDateFormat[1] + '/' + usDateFormat[0] + '/' + usDateFormat[2]);
						var d = new Date();
						console.log(d);
						d.setMonth(usDate.getMonth() + months);
						var us = moment(d).format('L');
						var ist = us.split('/');
						$("#deposit_maturity_date").val(ist[1] + '-' + ist[0] + '-' + ist[2]);
					}
				} else {
					$("#period").focus();
				}
			});
	</script>
</head>

<?php if ((isset($_POST['ShowData']) && $_POST['ShowData'] <> '')  || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])) { ?>

	<body onLoad="go_error();">
	<?php } else if (isset($_REQUEST['edt']) || $_REQUEST['insert'] == 'Edit') { ?>

		<body onLoad="getsociety('edit-<?php echo $_SESSION['society_id']; ?>');">
		<?php } else if (isset($_REQUEST['show'])) { ?>

			<body onLoad="getsociety('show-<?php echo $_REQUEST['id']; ?>');">

				<body>
				<?php } ?>

				<div id="middle">
					<center>
						<br>
						<div class="panel panel-info" id="panel" style="display:block">
							<div class="panel-heading" id="pageheader">Deposits <?=$deposit_name?></div>
							<br>


							<?php $val = 'onSubmit="return val();"';
							?>

							<center>
							
								<form name="society" id="society" method="post" action="process/pp_deposits.process.php" onSubmit="return val();">
									<?php
									$star = "<font color='#FF0000'>*&nbsp;</font>";
									if (isset($_REQUEST['msg'])) {
										$msg = "Sorry !!! You can't delete it. ( Dependency )";
									} else if (isset($_REQUEST['msg1'])) {
										$msg = "Deleted Successfully.";
									} else {
										//$msg = '';	
									}
									?>
									<div class="container-fluid">
										<div class="row">
											<div class="col-sm-12">
												<?php
												if (isset($msg)) {
													if (isset($_POST["ShowData"])) {
												?>
														<tr height="30">
															<td colspan="4" align="center">
																<font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font>
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
															<font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font>
														</td>
													</tr>
												<?php
												}
												?>
											</div>
											<div class="col-sm-6">
												<div class=""></div>
												<div class="item row">
													<label class="col-form-label col-sm-5 text-left">Select Deposits<span>&nbsp; : &nbsp;</span>
													</label>
													<div class="col-sm-7 text-left">
														<select name="deposit_type" id="deposit_type" class="form-control-sm" tabindex="1" autofocus required>
															<option value="0">Select Deposit Type</option>
															<?php echo  $obj_deposits->comboboxDepositType($deposit_category_query, $default_deposit);?>
														</select>
													</div>
												</div>
												<div class="item row">
													<label class="col-form-label col-sm-5 text-left">Start Date <span>&nbsp; : &nbsp;</span></label>
													<div class="col-sm-7 text-left">
														<input type="text" name="deposit_date" id="deposit_date" class="basics field_date form-control-sm" value="<?=date('d-m-Y')?>" size="10" tabindex="3" readonly required />
													</div>
												</div>
												<div class="item row">

													<label class="col-form-label col-sm-5 text-left">Maturity Type<span>&nbsp; : &nbsp;
														</span></label>
													<div class="col-sm-7 text-left">

														<select name="maturity_type" id="maturity_type" class="form-control-sm" tabindex="5" required>
															<?php echo $obj_deposits->comboboxMaturityType("SELECT * FROM pp_maturity_type"); ?>
														</select>

													</div>
												</div>
												<div class="item row">
													<label class="col-form-label col-sm-5 text-left">Maturity Calculation Method<span> : &nbsp;</span></label>
													<div class="col-sm-7 text-left">
														<select name="maturity_cal_method" id="maturity_cal_method" class="form-control-sm" tabindex="7" required>
															<option value="0">Simple</option>
															<option value="1">Compound</option>

														</select>
													</div>
												</div>
												<?php if($deposit_category != SAVING_ACCOUNT){?>
												<div class="item row">
													<label class="col-form-label col-sm-5 text-left"><?=$depositAmountLabel?><span> : &nbsp;</span></label>
													<div class="col-sm-7 text-left">
														<input type="text" name="deposit_amt" id="deposit_amt" class="field_input form-control-sm" tabindex="8" required/>
													</div>
												</div>
												<div class="item row">
													<label class="col-form-label col-sm-5 text-left">Interest Amount<span> : &nbsp;</span></label>
													<div class="col-sm-7 text-left">
														<input type="text" name="int_amt" id="int_amt" class="field_input form-control-sm" tabindex="11" required>
													</div>
												</div>
												<?php }?>
												<?php if($deposit_category == FIXED_DEPOSIT_ACCOUNT){?>
												<br>
												<div class="item row">
													<label class="col-form-label col-sm-5 text-left">Select Mode<span> : &nbsp;</span></label>
													<div class="col-sm-7 text-left">
													<select name="receipt_type" id="receipt_type" class="field_select">
														<option value="0">Please select Receipt Type</option>
														<option value="<?php echo RECEIPT_CASH;?>" selected>Cash</option>
														<option value="<?php echo RECEIPT_CHEQUE;?>">Cheque</option>
													</select>
													</div>
												</div>
												<div class="chequeDiv hidden">
												<div class="item row">
													<label class="col-form-label col-sm-5 text-left">Select Deposit<span> : &nbsp;</span></label>
													<div class="col-sm-7 text-left">
													<select name="bank_deposit_slip" id="bank_deposit_slip" tabindex="13"></select>
													</div>
												</div>
												<div class="item row">
													<label class="col-form-label col-sm-5 text-left">Payer Bank<span> : &nbsp;</span></label>
													<div class="col-sm-7 text-left">
														<input type="text" name="payer_bank" id="payer_bank" class="field_input form-control-sm">	
													</div>
												</div>
												</div>
												<?php }?>
											</div>
											<div class="col-sm-6">
												<?php if($deposit_category != SAVING_ACCOUNT){?>
												<div class="item row form-check">
													<label class="col-form-label col-sm-5 text-left">Period (Months)<span> : &nbsp;</span> </label>													
													<div class="col-sm-7 text-left">
														<input type="text" name="period" id="period" class="field_input form-control-sm" tabindex="2" required>
													</div>
												</div>
												<div class="item row">
													<label class="col-form-label col-sm-5 text-left">Deposit Maturity Date<span> : &nbsp;</span></label>
													<div class="col-sm-7 text-left">
														<input type="text" name="deposit_maturity_date" id="deposit_maturity_date" class="basics field_date form-control-sm" size="10" tabindex="4" readonly required />
													</div>
												</div>
												<?php }?>
												<div class="item row">
													<label class="col-form-label col-sm-5 text-left">Maturity Calculation<span> : &nbsp;</span></label>
													<div class="col-sm-7 text-left">
														<select name="maturity_cal" id="maturity_cal" class="form-control-sm" tabindex="6" required>
															<?php
															foreach ($MATURITY_CALCULATIONS as $maturity_name => $maturity_value) {
																$selected = $maturity_name == 'QUARTERLY' ? 'selected ': '';
																?>
																<option value="<?=$maturity_value?>" <?=$selected?>><?=$maturity_name?></option>
															<?php }
															
															
															?>															
														</select>
													</div>
												</div>
												<br><br>
												<?php if($deposit_category != SAVING_ACCOUNT){?>
												<div class="item row">
													<label class="col-form-label col-sm-5 text-left">Interest Rate<span> : &nbsp;</span></label>
													<div class="col-sm-7 text-left">
														<input type="text" name="int_rate" id="int_rate" class="field_input form-control-sm calculate_field" tabindex="9" required>
													</div>
												</div>
												<div class="item row">
													<label class="col-form-label col-sm-5 text-left">Maturity Amount<span> : &nbsp;</span></label>
													<div class="col-sm-7 text-left">
														<input type="text" name="maturity_amt" id="maturity_amt" class="field_input form-control-sm" tabindex="12" required>
													</div>
												</div>
												<?php }?>
												<?php if($deposit_category == FIXED_DEPOSIT_ACCOUNT){?>
												<br>
												<div class="item row">
													<label class="col-form-label col-sm-5 text-left">Bank Ledger<span> : &nbsp;</span></label>
													<div class="col-sm-7 text-left">
														<select name="cash_name" id="cash_name" class="cashLedger" tabindex="12">
															<?php echo $obj_utility->BankComboBox(CASH_ACCOUNT);?>															
														 </select>
														 <select name="cheque_bank_name" id="cheque_bank_name" class="hidden bankLedger" tabindex="12" onchange="getBankDepositSlip();">
															<?php echo $obj_utility->BankComboBox(BANK_ACCOUNT);?>																
														 </select>
													</div>
												</div>
												<div class="chequeDiv hidden">
												<div class="item row">
													<label class="col-form-label col-sm-5 text-left">Cheque Number<span> : &nbsp;</span></label>
													<div class="col-sm-7 text-left">
														<input type="text" name="cheque_number" id="cheque_number" class="field_input form-control-sm">
													</div>
												</div>
												<div class="item row">
													<label class="col-form-label col-sm-5 text-left">Payer Branch<span> : &nbsp;</span></label>
													<div class="col-sm-7 text-left">
														<input type="text" name="payer_branch" id="payer_branch" class="field_input form-control-sm">	
													</div>
												</div>
												</div>
												<?php }?>
											</div>
										</div>
										<br>
										
        
       
										<div class="row">
											<div class="col-sm-6">
												<div class="item row">
												<label class="col-form-label col-sm-5 text-left">Member Category&nbsp; : &nbsp;</label>
												<div class="col-sm-7 text-left">
																<select name="member_cat" id="member_cat" class="form-control-sm" tabindex="13" required>
																	<?php echo $combo_state = $obj_utility->comboboxMemberCat("SELECT * FROM pp_member_category"); ?>
																</select>
												</div>
												</div>
												<div class="item row">
												<label class="col-form-label col-sm-5 text-left">Status&nbsp; : &nbsp;</label>
												<div class="col-sm-7 text-left">
															
																<select name="status" id="status" class="form-control-sm" tabindex="15" required>
																	<option value="0">Open</option>
																	<option value="1">Closed</option>
																</select>
											</div>
												</div>
												<div class="item row">
											<label class="col-form-label col-sm-5 text-left">Introducer 1&nbsp; : &nbsp;</label>
															<div class="col-sm-7 text-left">
																<select name="introducer_1" id="introducer_1" class="field_select form-control-sm" tabindex="16" required>
																	<?php echo $combo_state = $obj_deposits->comboboxName("SELECT DISTINCT  member_id, owner_name FROM member_main WHERE status = 'Y'"); ?>
																</select>
															</div>
												</div>
												<div class="item row">
												<label class="col-form-label col-sm-5 text-left">Joint Name&nbsp; : &nbsp;</label>
															<div class="col-sm-7 text-left">
															<input type="text" name="joint_name" id="joint_name" class="field_input form-control-sm" value="<?php echo $_REQUEST['region']; ?>" tabindex="17" required/>
														</div>
												</div>
												<div class="item row">
												<label class="col-form-label col-sm-5 text-left" for="auto_renew">Auto Renew&nbsp; : &nbsp;</label>
												<div class="col-sm-7 text-left">
													<input type="checkbox" name="auto_renew" id="auto_renew" value="1" class="form-control-sm" tabindex="20"/>
												</div>
												</div>
												
											</div>
											<div class="col-sm-6">
											<div class="item row">
												<label class="col-form-label col-sm-5 text-left">Account Operator&nbsp; : &nbsp;</label>
															<div class="col-sm-7 text-left">
																<select name="ac_op" id="ac_op" class="form-control-sm" tabindex="14" required>
																	<?php echo $obj_deposits->comboboxAcOp("SELECT * FROM pp_ac_operator"); ?>
																</select>
															</div>
												</div>
												<div class="item row"><br><br></div>
												<div class="item row">
											<label class="col-form-label col-sm-5 text-left">Introducer 2&nbsp; : &nbsp;</label>
															<div class="col-sm-7 text-left">
																<select name="introducer_2" id="introducer_2" class="field_select form-control-sm" tabindex="18" required>
																	<?php echo $combo_state = $obj_deposits->comboboxName("SELECT DISTINCT member_id, owner_name FROM member_main WHERE status = 'Y'"); ?>
																</select>
															</div>
												</div>
												<div class="item row">
											<label class="col-form-label col-sm-5 text-left">Agent Name&nbsp; : &nbsp;</label>
															<div class="col-sm-7 text-left">
															<select name="agent_name" id="agent_name" class="field_select form-control-sm" tabindex="19" required>
															<?php echo $obj_deposits->comboboxForAgent("SELECT * FROM pp_agent"); ?>
																</select>
															</div>
												</div>
											</div>
											<div class="col-sm-12">
											<br>
											<label class="col-form-label col-sm-2 text-left textbox">Note&nbsp; : &nbsp;</label>
											<div class="col-sm-9 text-left">
											<textarea name="note_footer" id="note_footer" class="field_input" tabindex="21" required></textarea>
											<script>
												CKEDITOR.config.height = 100;
												CKEDITOR.replace('note_footer', {
													toolbar: [{
															name: 'clipboard',
															items: ['Undo', 'Redo']
														},
														{
															name: 'editing',
															items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike']
														}
													]
												});
											</script>			
											</div>
											
										</div>
										<div class="row">
											<div class="col-sm-12">
												<br>
												<input type="hidden" name="login_id" id="login_id">
												<input type="hidden" name="id" id="id" value="<?php echo $_GET['member_id']; ?>">
												<input type="hidden" name="account_type" id="account_type" value="<?php echo $_GET['account_category']; ?>">
												<input type="hidden" name="cash_account" id="cash_account" value="<?=RECEIPT_CASH?>">
												<input type="hidden" name="cheque_account" id="cheque_account" value="<?=RECEIPT_CHEQUE?>">
												<input type="submit" name="insert" id="insert" value="Submit" class="btn btn-primary" style="padding: 6px 12px; color:#fff;background-color: #2e6da4;margin-right: 20px;">
												<a href="#" class="btn btn-danger" style="width: 75px;box-shadow: 1px 1px 4px #666;">Close</a>
											</div>
										</div>
								
										</div>
									</div>
									<br>
									</form>
							</center>

						</div>
						<br><br>

					</center>
				</div>
				</div>
				<script>
																	
																</script>													
				<?php include_once "includes/foot.php"; ?>