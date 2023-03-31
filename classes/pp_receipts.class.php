<?php
include_once("genbill.class.php");
include_once("dbconst.class.php");
include_once("VoucherRegister.class.php");
include_once("utility.class.php");
class Receipts{

	public $db_Conn;
	public $obj_genBill;
	public $obj_voucher_register;
	public $debug_trace; 
	public $actionPage;
	public $obj_utility;
	
	
	public function __construct($db_Conn)
	{
		$this->db_Conn = $db_Conn;
		$this->obj_genBill = new genbill($this->db_Conn);
		$this->obj_voucher_register = new VoucherRegister($this->db_Conn);
		$this->obj_utility = new utility($this->db_Conn); 
		$this->debug_trace = 0;
		$this->actionPage = '../pp_receipts.php';
	}
	
	public function startProcess(){
	
	try{
	
		$this->db_Conn->begin_transaction();
		
		$Receipt_Account_Type = $_POST['acc_type'];
		$Receipt_Type = $_POST['receipt_type'];
		$PaidBy = $Receipt_Loan_Leader_id = $_POST['ledgerList'];
		$Receipt_payer_bank = $_POST['payer_bank'];
		$Receipt_payer_branch = $_POST['payer_branch'];
		$Receipt_depposit_slip = $_POST['bank_deposit_slip'];
		$Receipt_Cheque_No = $_POST['cheque_no'];
		$Prefix = "";
		
		if($Receipt_Type == RECEIPT_CASH){
		
			$Prefix = "cash_";
			$Receipt_payer_bank = '--';
			$Receipt_payer_branch = '--';
			$Receipt_depposit_slip = DEPOSIT_CASH;
			$Receipt_Cheque_No = CASH_CHEQUE_NO; 
		}
		else if($Receipt_Type == RECEIPT_CHEQUE){
		
			$Prefix = "cheque_";
		}
		
		$Receipt_Bank_id = $_POST[$Prefix.'bank_name'];
		$Receipt_Date = $_POST[$Prefix.'date'];
		$Receipt_Amount = $_POST[$Prefix.'amt'];
		$Receipt_comments = $this->db_Conn->escapeString($_POST[$Prefix.'comment']);
		
		if(empty($PaidBy)){
			throw new Exception("Member does not exists");
		}

		$insert_query="insert into chequeentrydetails (`VoucherDate`,`ChequeDate`,`ChequeNumber`,`Amount`,`PaidBy`,`BankID`,`PayerBank`,`PayerChequeBranch`,`DepositID`,`EnteredBy`,`Comments`) values ('".getDBFormatDate($Receipt_Date)."','".getDBFormatDate($Receipt_Date)."','".$Receipt_Cheque_No."','".$Receipt_Amount."','".$PaidBy."','".$Receipt_Bank_id."','".$this->db_Conn->escapeString($Receipt_payer_bank)."','".$this->db_Conn->escapeString($Receipt_payer_branch)."','".$Receipt_depposit_slip."','".$_SESSION['login_id']."','".$Receipt_comments."')";
		$RefNo = $this->db_Conn->insert($insert_query);
		
		if($Receipt_Account_Type == LOAN_ACCOUNT){
			
			$UpdateLoanTableQuery = "Update `pp_loan` set `last_receipt_id` = '".$RefNo."' WHERE ledger_id = '".$Receipt_Loan_Leader_id."'";
			$this->db_Conn->update($UpdateLoanTableQuery);
		}
		
		$VoucherData = array();
		
		if($RefNo)
		{
			if($Receipt_Account_Type == LOAN_ACCOUNT){
				$PaymentByArray = array("Date"=>$Receipt_Date,"RefNo"=>$RefNo,"RefTable"=>TABLE_CHEQUE_DETAILS , "VoucherType"=>VOUCHER_RECEIPT, "By"=>$Receipt_Bank_id, "To"=>"","Debit"=>$Receipt_Amount,"Credit"=>"","Note"=>$Receipt_comments);
				$PaymentToArray = array("Date"=>$Receipt_Date,"RefNo"=>$RefNo,"RefTable"=>TABLE_CHEQUE_DETAILS , "VoucherType"=>VOUCHER_RECEIPT, "By"=>"", "To"=>$PaidBy, "Debit"=>"", "Credit"=>$Receipt_Amount,"Note"=>$Receipt_comments);
			}
			else{
				$PaymentByArray = array("Date"=>$Receipt_Date,"RefNo"=>$RefNo,"RefTable"=>TABLE_CHEQUE_DETAILS , "VoucherType"=>VOUCHER_RECEIPT, "By"=>"", "To"=>$Receipt_Bank_id,"Debit"=>"","Credit"=>$Receipt_Amount,"Note"=>$Receipt_comments);
				$PaymentToArray = array("Date"=>$Receipt_Date,"RefNo"=>$RefNo,"RefTable"=>TABLE_CHEQUE_DETAILS , "VoucherType"=>VOUCHER_RECEIPT, "By"=>$PaidBy, "To"=>"", "Debit"=>$Receipt_Amount, "Credit"=>"","Note"=>$Receipt_comments);
			}
			
			array_push($VoucherData,$PaymentByArray);
			array_push($VoucherData,$PaymentToArray);
			
			$VoucherRegisterResult = $this->obj_voucher_register->processdata($VoucherData);
			
			if($VoucherRegisterResult['status'] == true)
			{
				$this->db_Conn->commit();
				return 'Insert';
			}
			else
			{
				$this->db_Conn->rollback();
				return 'Failed to insert record';
			}
		}
	}
	catch(Exception $e){
	
		echo $e->getMessage();
		$this->db_Conn->rollback();
	}
}
	
	public function getLedgerCategoryAndLedgerList($ledger_id, $account_type, $member_id){

		try {
			if(empty($ledger_id) && empty($account_type) && empty($member_id)){
				throw new Exception("Ledger/Member can not be blank. Please try again");			
			}
			
			if(!empty($ledger_id)){
				$query = "SELECT c.category_name, c.category_id, l.ledger_name, l.id as ledger_id FROM `ledger` as l JOIN account_category as c ON l.categoryid =  c.category_id where l.id = '$ledger_id'";
			}
			else if(!empty($member_id) && !empty($account_type)){
				$query = "SELECT c.category_name, c.category_id, l.ledger_name, l.id as ledger_id FROM `ledger` as l JOIN account_category as c ON l.categoryid =  c.category_id where l.parent_id = '$member_id'";
				if($account_type == SAVING_ACCOUNT){
					$query .= " AND l.categoryid = '$account_type'"; 
				}
				else{
					$query .= " AND c.parentcategory_id = '$account_type'"; 
				}				
			}
			return $this->db_Conn->select($query);
		
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function getLoanName($loan_type,$member_id){

		$query = "SELECT l.id, ledger_name FROM `ledger` as l JOIN pp_loan as loan ON l.id = loan.ledger_id where categoryid in (SELECT A.category_id FROM account_category A, account_category B WHERE A.parentcategory_id = B.category_id AND B.category_name = '".LOAN."' AND A.category_id = '".$loan_type."') AND loan.member_id = '".$member_id."' ";
		$LoanTypeList = $this->obj_genBill->combobox($query,0,'');

		if(!empty($LoanTypeList))
		{
			$FinalList .= "<option value = '0'>Please Select Loan</option>".$LoanTypeList;
			return $FinalList;
		}
		else
		{
			return "<option value = ''>No Loan Name</option>";
		}
	}
	
	public function getLoanDues($LedgerID){
		
		$BalanceAmount = 0;
		$Query = "SELECT Sum(Debit) - Sum(Credit) as Dues FROM `assetregister` WHERE LedgerID = '".$LedgerID."'";
		$Result = $this->db_Conn->select($Query);
		if($Result[0]['Dues'] <> 0)
		{
			$BalanceAmount = $Result[0]['Dues'];
		}
		return $BalanceAmount;
	}
	
	
	public function getBankDepositSlip($bank_id){

		$query = "SELECT `id`, `desc` FROM `depositgroup` WHERE bankid = '".$bank_id."' and DepositSlipCreatedYearID = '".$_SESSION['default_year']."'";
		$BankDepositSlip = $this->obj_genBill->combobox($query,0,'');

		if(!empty($BankDepositSlip))
		{
			$FinalList .= "<option value = '0'>Please Select Deposit Slip</option>".$BankDepositSlip;
			return $FinalList;
		}
		else
		{
			return "<option value = ''>No Deposit Slip</option>";
		}
	}
	
	public function GetLedgerFullDetails($ledger_id)
	{
		$Query = "SELECT * FROM `pp_loan`  WHERE id = '".$loan_id."'";
		return $this->db_Conn->select($Query);
	}
	
	public function getLedgerDetails($ledger_id){

		$query = "SELECT * FROM `pp_loan` WHERE ledger_id = '".$ledger_id."'";
		
		$LoanDetails =  $this->db_Conn->select($query);
		
		$table_data = "";
		
		if(!empty($LoanDetails))
		{
			$LoanNo = $LoanDetails[0]['id'];
			$LedgerID = $LoanDetails[0]['ledger_id'];
			$AccountNo = '----';
			$StartDate = getDisplayFormatDate($LoanDetails[0]['loan_date']);
			$MaturityDate = getDisplayFormatDate($LoanDetails[0]['maturity_date']);
			$LoanLastAmount = 0;
			$LastReceiptID = $LoanDetails[0]['last_receipt_id'];
			$InterestRate = $LoanDetails[0]['interest_rate'].'%';
			$InterestAmount = $LoanDetails[0]['interest_amt'];
			$InstallmentAmount = $LoanDetails[0]['installment_amt'];
			$MaturityAmount = $LoanDetails[0]['maturity_amt'];
			$BankCharges = $LoanDetails[0]['loan_charges'];
			$LoanMortgage = $LoanDetails[0]['mortgage'];
			$BankID = $LoanDetails[0]['bank_id'];
			
			$LoanMortgageName = '---';
			
			if($LastReceiptID <> 0 && $LastReceiptID <> "")
			{
				$ReceiptAmountQuery = "SELECT `Amount` FROM `chequeentrydetails` WHERE ID =  '".$LastReceiptID."'";
				$ReceiptAmountResult = $this->db_Conn->select($ReceiptAmountQuery);
				$LoanLastAmount = $ReceiptAmountResult[0]['Amount'];
			}
			
			$LoanBalanceAmount = $this->getLoanDues($LedgerID);
			
			if($LoanMortgage == MORTGAGE_FD)
			{
				$LoanMortgageName = "FD";
			}
			else if($LoanMortgage == MORTGAGE_SAVING)
			{
				$LoanMortgageName = "Saving Account";
			}
			
			$ReferredBy1 = $LoanDetails[0]['ref_by_1'];
			$ReferredBy2 = $LoanDetails[0]['ref_by_2'];
			
			$LoanReferredNamesQuery = "SELECT owner_name FROM member_main WHERE member_id in ('".$ReferredBy1."','".$ReferredBy2."')";
			$LoanReferredNames = $this->db_Conn->select($LoanReferredNamesQuery);
			
			if(!empty($LoanReferredNames))
			{
				$ReferredByName1 = $LoanReferredNames[0]['owner_name'];
				if($ReferredBy1 == $ReferredBy2)
				{
					$ReferredByName2 = $LoanReferredNames[0]['owner_name'];	
				}
				else
				{
					$ReferredByName2 = $LoanReferredNames[1]['owner_name'];		
				}
				
			}
			else
			{
				$ReferredByName1 = 'No Referred Name';
				$ReferredByName2 = 'No Referred Name';	
			}
			
			$table_data = '
			<tr style="background-color:#bce8f1;font-size:14px;" height="25"></tr>
			<tr>
        	<!-- Loan No. -->
        	<td valign="left"><span style="margin-left: 4%;">&nbsp;</span><span  style="margin-left: 15px;">Loan No.</span><span  style="margin-left: 100px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 32px;">'.$LoanNo.'</span></td>
        	<!-- Account No. -->
            <td valign="left"><span style="margin-left: 4%;">&nbsp;</span><span  style="margin-left: 15px;">Account No.</span><span  style="margin-left: 80px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 32px;">'.$AccountNo.'</span></td>
		</tr>
        
        <tr>
        	<!-- Loan Start Date -->
        	<td valign="left"><span style="margin-left: 4%;">&nbsp;</span><span  style="margin-left: 15px;">Loan Start Date</span><span  style="margin-left: 64px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 32px;">'.$StartDate.'</span></td>
        	<!-- Loan End Date -->
            <td valign="left"><span style="margin-left: 4%;">&nbsp;</span><span  style="margin-left: 15px;">Loan End Date</span><span  style="margin-left: 65px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 32px;">'.$MaturityDate.'</span></td>
		</tr>

		
        <tr>
        	<!-- Balance Amount -->
        	<td valign="left"><span style="margin-left: 4%;">&nbsp;</span><span  style="margin-left: 15px;">Balance Amount</span><span  style="margin-left: 60px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 32px;">'.$LoanBalanceAmount.'</span></td>
			<!-- Last Amount -->
        	<td valign="left"><span style="margin-left: 4%;">&nbsp;</span><span  style="margin-left: 15px;">Last Amount</span><span  style="margin-left: 80px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 30px;">'.$LoanLastAmount.'</span></td>
        
        </tr>

		<tr>
        	<!-- Interest Rate -->
        	<td valign="left"><span style="margin-left: 4%;">&nbsp;</span><span  style="margin-left: 15px;">Interest Rate</span><span  style="margin-left: 80px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 30px;">'.$InterestRate.'</span></td>
			<!-- Interest in RS -->
			<td valign="left"><span style="margin-left: 4%;">&nbsp;</span><span  style="margin-left: 15px;">Interest (in Rs)</span><span  style="margin-left: 68px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 30px;">'.$InterestAmount.'</span></td>
		</tr>
		
		<tr>
        	<!-- Installment Amount -->
        	<td valign="left"><span style="margin-left: 4%;">&nbsp;</span><span  style="margin-left: 15px;">Installment Amount</span><span  style="margin-left: 46px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 30px;">'.$InstallmentAmount.'</span></td>
			<!-- Maturity Amount -->
			<td valign="left"><span style="margin-left: 4%;">&nbsp;</span><span  style="margin-left: 15px;">Maturity Amount</span><span  style="margin-left: 60px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 30px;">'.$MaturityAmount.'</span></td>
		</tr>
		
		<tr >
        	<!-- Bank Charges -->
        	<td valign="left"><span style="margin-left: 4%;">&nbsp;</span><span  style="margin-left: 3%;">Bank Charges</span><span  style="margin-left: 72px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 6%;">'.$BankCharges.'</span></td>
           	<!-- Mortgage -->
            <td valign="left"><span style="margin-left: 4%;">&nbsp;</span><span  style="margin-left: 3%;">Mortgage</span><span  style="margin-left: 95px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 6%;">'.$LoanMortgageName.'</span></td>
		</tr>
		
		<tr >
        	<!-- Referred by 1 -->
        	<td valign="left"><span style="margin-left: 4%;">&nbsp;</span><span  style="margin-left: 3%;">Referred by 1</span><span  style="margin-left: 75px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 6%;">'.$ReferredByName1.'</span></td>
           	<!-- Referred by 2 -->
            <td valign="left"><span style="margin-left: 4%;">&nbsp;</span><span  style="margin-left: 3%;">Referred by 2</span><span  style="margin-left: 73px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 6%;">'.$ReferredByName2.'</span></td>
		</tr>';

			return $table_data.'@@@'.$BankID;
		}
	}
}

?>

