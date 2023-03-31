<?php
	if(!isset($_SESSION)){ session_start(); }
		
	//TransactionType
	define ('TRANSACTION_CREDIT', 'Credit');
	define ('TRANSACTION_DEBIT', 'Debit');
	
	//For Table BankRegister
	define ('TRANSACTION_PAID_AMOUNT', 'PaidAmount');
	define ('TRANSACTION_RECEIVED_AMOUNT', 'ReceivedAmount');
		
	//ID's in group
	define ('LIABILITY', '1');
	define ('ASSET', '2');
	define ('INCOME', '3');
	define ('EXPENSE', '4');
	
	//ID's in vouchertype Table
	define ('VOUCHER_SALES', '1');
	define ('VOUCHER_PAYMENT', '2');
	define ('VOUCHER_RECEIPT', '3');
	define ('VOUCHER_PURCHASE', '4');
	define ('VOUCHER_JOURNAL', '5');
	define ('VOUCHER_CONTRA', '6');
	
	//Default Year and Period
	define('DEFAULT_YEAR', $_SESSION['default_year']);
	define('DEFAULT_PERIOD', $_SESSION['default_period']);
	
	//ID's in account_category Table
	define('PRIMARY', 1); //1
	define('CURRENT_ASSET', $_SESSION['default_current_asset']);	//2
	define('DUE_FROM_MEMBERS', $_SESSION['default_due_from_member']);	//3
	define('BANK_ACCOUNT', $_SESSION['default_bank_account']);	//6
	
	//ID's in ledger Table
	define('INTEREST_ON_PRINCIPLE_DUE', $_SESSION['default_interest_on_principle']);	//6
	
	//Default society
	define('DEFAULT_SOCIETY', $_SESSION['society_id']);
		
	//Table name from where Voucher Tables 'RefNo' is linked to
	define('TABLE_BILLREGISTER', '1');
	define('TABLE_CHEQUE_DETAILS', '2');
	define('TABLE_PAYMENT_DETAILS', '3');
	
	//Rebate Method
	
	define('REBATE_METHOD_NONE', '1');
	define('REBATE_METHOD_FLAT', '2');
	define('REBATE_DUE_WAIVER', '3');
	
	//Interest Method
	
	define('INTEREST_METHOD_DELAY_DUE', '1');
	define('INTEREST_METHOD_FULL_MONTH', '2');
	
	
	
	//Month names array as per cycle
	$MONTHS_MONTHLY = array();
	$MONTHS_BIMONTHLY = array(); 
	$MONTHS_QUATERLY = array();
	
	function getDBFormatDate($ddmmyyyy)
	{
		if($ddmmyyyy <> '')
		{
			return date('Y-m-d', strtotime($ddmmyyyy));
		}
		else
		{
			return '';
		}
	}
	
	function getDisplayFormatDate($yyyymmdd, $seperator = '-')
	{
		$ddmmyyyy = date("d" . $seperator . "m" . $seperator . "Y", strtotime($yyyymmdd));
		return $ddmmyyyy;
	}
	
	function getAccountCategoryDefaultID($AccountCategoryType)
	{
		//select query
	}
		
?>