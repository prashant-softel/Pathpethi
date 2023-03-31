<?php if(!isset($_SESSION)){ session_start(); } ?>
<?php include_once ("classes/dbconst.class.php"); 
include_once("classes/include/dbop.class.php");
include_once("classes/genbill.class.php");
include_once("classes/utility.class.php");
include_once("includes/head_s.php");
include_once("classes/pp_receipts.class.php");

$db_Conn = new dbop();
$db_ConnRoot = new dbop(true);

$obj_genBill = new genbill($db_Conn);
$obj_receipt = new Receipts($db_Conn);
$obj_utility = new utility($db_Conn,$db_ConnRoot);
$ledger_id   = $_REQUEST['ledger_id'];
$account_type = $_REQUEST['account_type'];
$member_id = $_REQUEST['member_id'];
if(isset($ledger_id) && !empty($ledger_id))
{
	$ledgerDetails = $obj_receipt->GetLedgerFullDetails($ledger_id);
	$Loan_Ledger_id = $LoanDetails[0]['ledger_id'];
	$Loan_Category_id = $LoanDetails[0]['subcategory_id'];
}
// var_dump($ledgerDetails);
?>
 

<html>
<head>
<!--	<link rel="stylesheet" type="text/css" href="css/pagination.css" >-->
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsreceipts.js" defer></script>
	
	<script type="text/javascript" src="js/ajax.js"></script>
	<script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
	<script type="text/javascript">

		$(function()
		{
			$.datepicker.setDefaults($.datepicker.regional['']);
			$(".basics").datepicker({ 
			dateFormat: "dd-mm-yy", 
			showOn: "both", 
			buttonImage: "images/calendar.gif", 
			buttonImageOnly: true,
		})});
		
		function go_error()
		{
			setTimeout('hide_error()',10000);	
		}
		
		function hide_error()
		{
			document.getElementById('error').style.display = 'none';	
		}
	</script>

	
</head>
<body>
<div id="middle">
<center>
<br>
<div class="panel panel-info" id="panel" style="display:none">
<div class="panel-heading" id="pageheader">Receipts</div>
<br>
<center>

<form name="receipt" id="receipt" method="post" action="process/pp_receipt.process.php" onSubmit="return val();">
	<?php
		$star = "<font color='#FF0000'>*&nbsp;</font>";
		if(isset($_REQUEST['msg']))
		{
			$msg = "Sorry !!! You can't delete it. ( Dependency )";
		}
		else if(isset($_REQUEST['msg1']))
		{
			$msg = "Deleted Successfully.";
		}
	?>
	<table align='center' style=" width:100%;">
		<?php
		if(isset($msg))
		{
			if(isset($_POST["ShowData"]))
			{
		?>
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
		<?php
			}
			else
			{
			?>
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $msg; ?></b></font></td></tr>	   
			<?php		
			}
		}
		else
		{
		?>	
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
		<?php
		}
		?>
		<!--</table>-->
			 
		<tr>
			<td> 
				<table width="100%" style="font-size:12px; float:left;" id="PrintableTable">
				   <tr>
						<td valign="left" style="width:33%"><span>Member Name</span><span>&nbsp; : &nbsp;</span>
							<span><select name="mem_name" id="mem_name" style="width:60%;" class="field_select" OnBlur="getLoanList();">
							<?php echo $obj_genBill->combobox("SELECT member_id, owner_name FROM `member_main` Where `ownership_status` = 1 ", $member_id);?>
							</select></span>
						</td>
						<td valign="left" style="width:33%"><span>Account Type</span><span>&nbsp; : &nbsp;</span>
							<span><select name="acc_type" id="acc_type" style="width:60%" class="field_select" onChange="getTemplate();" data-type="<?=$account_type?>">
								<option value="0">Please select Account Type</option>
								<option value="<?php echo SAVING_ACCOUNT;?>">Saving Account</option>
								<option value="<?php echo DAILY_DEPOSIT_ACCOUNT;?>">Daily Account</option>
								<option value="<?php echo MONTHLY_DEPOSIT_ACCOUNT;?>">Monthly Account</option>
								<option value="<?php echo LOAN_ACCOUNT;?>">Loan Account</option>
							</select></span>
						</td>
						<td valign="left" style="width:33%"><span>Mode</span><span>&nbsp; : &nbsp;</span>
							<span><select name="receipt_type" id="receipt_type" style="width:60%;" class="field_select" onChange="getTemplate();">
								<option value="0">Please select Receipt Type</option>
								<option value="<?php echo RECEIPT_CASH;?>" selected>Cash</option>
								<option value="<?php echo RECEIPT_CHEQUE;?>">Cheque</option>
							</select></span>
						</td>
					</tr>
					<tr><td><br></td></tr>
				</table>	
			</td>
		</tr>
		<tr>
	   <td> 
	   <table width="100%" style="font-size:12px; float:left;display:none;" id="ledger_detail_table">
	   <hr/>	
       <tr style="background-color:#bce8f1;font-size:14px;" height="25"></tr>
		<tr>
			<!-- account category -->
			<td valign="left" id="account_category_td"><span style="margin-left: 4%;"><?php echo $star;?></span><span  style="margin-left: 2%;">Account Category</span><span  style="margin-left: 52px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 6%;">
				<select name="ledger_category" id="ledger_category" onBlur="loadLedgerList();"></select>
			</span></td>

			<!-- Ledger list -->

			<td valign="left" id="ledger_list_td"><span style="margin-left: 4%;"><?php echo $star;?></span><span  style="margin-left: 2%;">Ledger Name</span><span  style="margin-left: 84px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 6%;">
				<select name="ledgerList" id="ledgerList" onChange="getLoanDetails();"></select>
			</span></td>

		</tr>
       
	  </table>

	  
		<table width="100%" id="loan_details_table" style="display:none;font-size:12px">
        </table>
		
		<table width="100%" style="font-size:12px; float:left;display:none" id="cash_table">
		<tr style="background-color:#bce8f1;font-size:14px;" height="25"></tr>
		
		
		<!-- Bank And Cheque Date  -->
		<tr >
			<td valign="left"><span style="margin-left: 4%;"><?php echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Select Cash</span><span  style="margin-left: 75px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 6%;">
				<select name="cash_bank_name" id="cash_bank_name">
					<?php echo $obj_utility->BankComboBox(CASH_ACCOUNT);?>
				</select>
			</span></td>
			<td valign="left"><span style="margin-left: 4%;"><?php echo $star;?>&nbsp;</span><span  style="margin-left: 16px;"> Date</span><span  style="margin-left: 117px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 32px;"><input type="text" name="cash_date" id="cash_date"  class="basics field_date" size="10" readonly  style="width:80px;" value="<?=Date('d-m-Y')?>" /></span></td>
		</tr>
		
		<!-- Amount And Narration  -->
		<tr>
			<td valign="left"><span style="margin-left: 4%;"><?php echo $star;?>&nbsp;</span><span  style="margin-left: 15px;">Amount </span><span  style="margin-left: 93px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 32px;"><input type="text" name="cash_amt" id="cash_amt" class="field_input"></span></td>
			<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 15px;">Narration</span><span  style="margin-left: 106px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 32px;"><input type="text" name="cash_comment" id="cash_comment" class="field_input"></span></td>
		</tr>

		
		</table>
		
		
		
		<table width="100%" style="font-size:12px; float:left;display:none" id="cheque_table">
		<tr style="background-color:#bce8f1;font-size:14px;" height="25"></tr>
		
		
		<!-- Cheque No. And Cheque Date  -->
		<tr >
			<td valign="left"><span style="margin-left: 4%;"><?php echo $star;?>&nbsp;</span><span  style="margin-left: 11px;">Cheque No.</span><span  style="margin-left: 80px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 6%;"><input type="text" name="cheque_no" id="cheque_no"  class="field_input" /></span></td>
			<td valign="left"><span style="margin-left: 4%;"><?php echo $star;?>&nbsp;</span><span  style="margin-left: 8px;">Cheque Date</span><span  style="margin-left: 80px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 32px;"><input type="text" name="cheque_date" id="cheque_date" value="<?=date('d-m-Y')?>"  class="basics field_date" size="10" readonly  style="width:80px;" /></span></td>
		</tr>
		
		<!-- Bank And Deposit  -->
		<tr>
			<td valign="left"><span style="margin-left: 4%;"><?php echo $star;?>&nbsp;</span><span  style="margin-left: 7px;"> Select Bank </span><span  style="margin-left: 77px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 32px;">
				
				<select name="cheque_bank_name" id="cheque_bank_name" onBlur="getBankDepositSlip();">
					<?php echo $obj_utility->BankComboBox(BANK_ACCOUNT);?>
				</select>

			</span></td>
			<td valign="left"><span style="margin-left: 4%;"><?php echo $star;?>&nbsp;</span><span  style="margin-left: 8px;"> Select Deposit</span><span  style="margin-left: 72px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 32px;">
				<select name="bank_deposit_slip;" id="bank_deposit_slip">

				</select>
			</span></td>
		</tr>

		<!--  Payer Bank And Payer Branch -->
		<tr>
			<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 15px;"> Payer Bank</span><span  style="margin-left: 82px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 32px;"><input type="text" name="payer_bank" id="payer_bank" class="field_input"></span></td>
			<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 15px;"> Payer Branch</span><span  style="margin-left: 76px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 32px;"><input type="text" name="payer_branch" id="payer_branch" class="field_input"></span></td>
		</tr>
		
		<!-- Amount And Narration -->

		<tr>
			<td valign="left"><span style="margin-left: 4%;"><?php echo $star;?>&nbsp;</span><span  style="margin-left: 8px;"> Amount</span><span  style="margin-left: 103px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 32px;"><input type="text" name="cheque_amt" id="cheque_amt" class="field_input"></span></td>
			<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 15px;"> Narration</span><span  style="margin-left: 101px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 32px;"><input type="text" name="cheque_comment" id="cheque_comment" class="field_input"></span></td>
		</tr>

		</table>
		
		
		
		<table width="100%" style="padding-top: 2%;"> 
		
        <tr align="center">
			<td colspan="4" align="right">
			<input type="hidden" name="login_id" id="login_id">
			<input type="hidden" name="id" id="id">
			<input type="hidden" name="ledger_id" id="ledger_id" value="<?=$ledger_id?>">
			<input type="hidden" name="unit_presentation_previous_value" id="unit_presentation_previous_value">
			<input type="submit" name="insert" id="insert" value="Submit"  class="btn btn-primary"  style="padding: 6px 12px; color:#fff;background-color: #2e6da4;margin-right: 20px;">
			</td>
			 <td colspan="4" align="left">
				<a href="#" class="btn btn-danger" style="width: 75px;box-shadow: 1px 1px 4px #666;">Close</a>
			</td>
		</tr>
		<tr><td><br></td></tr>
		<br>
<br>
	</table>
 </td>
</tr>
</table>

</form>
</center>

</div>
<br><br>

</center>
</div>
</div>

<!-- Account Category ID Hidden Field-->
<input type="hidden" name="saving_account" id="saving_account" value="<?=SAVING_ACCOUNT?>">
<input type="hidden" name="daily_account" id="daily_account" value="<?=DAILY_DEPOSIT_ACCOUNT?>">
<input type="hidden" name="monthly_account" id="monthly_account" value="<?=MONTHLY_DEPOSIT_ACCOUNT?>">
<input type="hidden" name="monthly_account" id="fixed_account" value="<?=FIXED_DEPOSIT_ACCOUNT?>">
<input type="hidden" name="loan_account" id="loan_account" value="<?=LOAN_ACCOUNT?>">

<?php if(isset($_REQUEST['loan_id']))
{ ?>
	<script>
    	prePopulateLoanDetails(<?php echo $Member_id;?>,<?php echo $Loan_Category_id;?>,<?php echo $Loan_Ledger_id;?>);
    </script>	
<?php }
include_once "includes/foot.php"; ?>
