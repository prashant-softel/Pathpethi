<?php 
	error_reporting(0);
	if(!isset($_SESSION)){ session_start(); }
	//Roles
	define('ROLE_SUPER_ADMIN', 'Super Admin');
	define('ROLE_ADMIN', 'Admin');
	define('ROLE_MEMBER', 'Member');
	define('ROLE_ADMIN_MEMBER', 'Admin Member');
	define('ROLE_MASTER_ADMIN', 'Master Admin');
		
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
	define ('VOUCHER_LOAN','7');


	//Default Year and Period
	define('DEFAULT_YEAR', $_SESSION['default_year']);
	define('DEFAULT_PERIOD', $_SESSION['default_period']);
	
	//ID's in account_category Table
	define('PRIMARY', 1); //1
	define('CURRENT_ASSET', $_SESSION['default_current_asset']);	//2
	define('DUE_FROM_MEMBERS', $_SESSION['default_due_from_member']);	//3
	define('BANK_ACCOUNT', $_SESSION['default_bank_account']);	//6
	define('CASH_ACCOUNT', $_SESSION['default_cash_account']);	//6
	define('LOAN_ACCOUNT', $_SESSION['default_loan']);
	define('SAVING_ACCOUNT', $_SESSION['default_saving_account']);
	define('FIXED_DEPOSIT_ACCOUNT', $_SESSION['default_fixed_deposit']);
	define('DAILY_DEPOSIT_ACCOUNT', $_SESSION['default_daily_deposit']);
	define('MONTHLY_DEPOSIT_ACCOUNT', $_SESSION['default_monthly_deposit']);

	
	//ID's in ledger Table
	define('INTEREST_ON_PRINCIPLE_DUE', $_SESSION['default_interest_on_principle']);	//6
	//define('INTEREST_ON_LOAN', $_SESSION['default_interest_on_loan']);	//6
	define('PENALTY_TO_MEMBER', $_SESSION['default_penalty_to_member']);
	define('BANK_CHARGES', $_SESSION['default_bank_charges']);
	define('TDS_PAYABLE', $_SESSION['default_tds_payable']);
	define('IMPOSE_FINE', $_SESSION['default_impose_fine']);                   /// impose Fine
	define('ADJUSTMENT_CREDIT', $_SESSION['default_adjustment_credit']);
	define('IGST_SERVICE_TAX', $_SESSION['igst_service_tax']);
	define('CGST_SERVICE_TAX', $_SESSION['cgst_service_tax']);
	define('SGST_SERVICE_TAX', $_SESSION['sgst_service_tax']);
	define('CESS_SERVICE_TAX', $_SESSION['cess_service_tax']);
	
	
	//Transaction wise deposit ID
	define('DEPOSIT_NEFT', '-2');
	define('DEPOSIT_CASH', '-3');
	define('DEPOSIT_ONLINE', '-4');
	
	//
	
	define("CASH_CHEQUE_NO", '-1');
	
	
	//Default society
	define('DEFAULT_SOCIETY', $_SESSION['society_id']);

	define('Is_Accounting_Software', 1);
		
	//Table name from where Voucher Tables 'RefNo' is linked to
	define('TABLE_BILLREGISTER', '1');
	define('TABLE_CHEQUE_DETAILS', '2');
	define('TABLE_PAYMENT_DETAILS', '3');
	define('TABLE_REVERSAL_CREDITS', '4');
	define('TABLE_NEFT', '5');
	define('TABLE_FD_MASTER', '6');
	define('TABLE_LOAN','7');
	define('TABLE_DEPOSIT','8');
	
	//Rebate Method
	
	define('REBATE_METHOD_NONE', '1');
	define('REBATE_METHOD_FLAT', '2');
	define('REBATE_DUE_WAIVER', '3');
	
	//Interest Method
	
	define('INTEREST_METHOD_DELAY_DUE', '1');
	define('INTEREST_METHOD_FULL_MONTH', '2');
	
	//Bill Format Method
	define('BILL_FORMAT_WITH_RECEIPT', '1');
	define('BILL_FORMAT_WITHOUT_RECEIPT', '2');
	
	//Notice Type
	define('NOTICE_TYPE_ADMINISTRATION', '1');
	define('NOTICE_TYPE_GENERAL', '2');
	define('NOTICE_TYPE_BUY_SELL_RENT', '3');
	
	//Default Profile ID's in DB in table 'societydb.profile'
	define('PROFILE_SUPER_ADMIN_ID', '1');
	define('PROFILE_ADMIN_ID', '2');
	define('PROFILE_MEMBER_ID', '3');
	define('PROFILE_ADMIN_MEMBER_ID', '4');
	
	//UserProfiles
	define('PROFILE_HOME', 'home_s.php');
	define('PROFILE_GENERATE_BILL', 'genbill.php');
	define('PROFILE_EDIT_BILL', 'Maintenance_bill.php');
	define('PROFILE_CHEQUE_ENTRY', 'ChequeDetails.php');
	define('PROFILE_PAYMENTS', 'PaymentDetails.php');
	define('PROFILE_UPDATE_INTEREST', 'updateInterest.php');
	define('PROFILE_REVERSE_CHARGE', 'reverse_charges.php');
	define('PROFILE_MANAGE_MASTER', 'settings.php');
	define('PROFILE_BANK_RECO', 'bank_reconciliation.php');
	define('PROFILE_SEND_NOTIFICATION', 'notification.php');
	define('PROFILE_NEFT_DETAILS', 'NeftDetails.php');
	define('PROFILE_CREATE_VOUCHER', 'createvoucher.php');
	define('PROFILE_VOUCHER_EDIT', 'VoucherEdit.php');
	define('PROFILE_DEPOSITGROUP', 'depositgroup.php');
	define('PROFILE_CHEQUELEAFBOOK', 'chequeleafbook.php');
	define('PROFILE_EDIT_MEMBER', 'view_member_profile.php');
	
	//Max date used in Bill Master
	define('PHP_MAX_DATE', '2037-03-31');

		define('GST_START_DATE', $_SESSION['gst_start_date']);
	
	//Month names array as per cycle
	$MONTHS_MONTHLY = array('April','May','June','July','August','September','October','November','December','January','February','March');
	$MONTHS_BIMONTHLY = array('April-May','June-July','August-September','October-November','December-January','February-March');
	$MONTHS_QUATERLY = array('April-June','July-September','October-December','January-March');
	$MONTHS_QUADRUPLE = array('April-July','August-November','December-March');
	$MONTHS_HALFYEARLY = array('April-September', 'October-March');
	$MONTHS_YEARLY = array('April-March');
		
	//action constants
	define ('ADD', '1');	
	define ('DELETE', '2');	
	define ('EDIT', '3');	
	
	//send reminder sms
	define("SENDBILLREMINDER", 1);
	define("SENDEVENTREMINDER", 2);
	define("SENDFDREMINDER", 3);

	//FD Actions
	define('FD_CREATED' ,'0');
	define('FD_CLOSED' ,'1');
	define('FD_RENEW' ,'2');
	define( 'CREATE_NEW_LEDGER', '-1'); 
	
	//Interest frequency
	define('MONTHLY' ,'0');
	define('QUARTERLY' ,'1');
	define('SEMIANNUAL' ,'2');
	define('ANNUAL' ,'3');
	define('ON_MATURITY' ,'4');

	//FD Interest Type
	define('FD_CUMULATIVE' ,'0');
	define('FD_NON_CUMULATIVE' ,'1');
	
	define('CLIENT_FEATURE_EXPORT_MODULE','CLIENT_FEATURE_EXPORT_MODULE');
	define('CLIENT_FEATURE_SMS_MODULE','CLIENT_FEATURE_SMS_MODULE');
	
	//bill type
	define("BILL_TYPE_REGULAR", 0);
	define("BILL_TYPE_SUPPLEMENTARY", 1);
	
	//sms sent type
	define("SMS_TYPE_GENERAL", 0);
	define("SMS_TYPE_BILL_NOTIFICATION_MANUALLY", 1);
	define("SMS_TYPE_BILL_NOTIFICATION_CRON", 2);
	//Email activation status
	define("ACCOUNT_EXIST_ACTIVE", 1);
	define("ACCOUNT_EXIST_MAPPING_NOT_FOUND", 2);
	define("NO_ACCOUNT", 3);

	define("MEMBER_TYPE_OTHER", 0);
	define("MEMBER_TYPE_OWNER", 1);
	define("MEMBER_TYPE_COOWNER", 2);
	define("MEMBER_TYPE_TENANT", 10);

	// Mortgage Constant

	define("MORTGAGE_FD",1);
	define("MORTGAGE_SAVING",2);

	//Gender

	define('GENDER_MALE',1);
	define('GENDER_FEMALE',2);

	// Loan

	define("LOAN","LOAN");

	// AccountType

	define("ACCOUNT_SAVING",'1');
	define("ACCOUNT_LOAN",'2');

	// Receipt Type

	define('RECEIPT_CASH','1');
	define('RECEIPT_CHEQUE','2');
	
	// Maturity Calculations
	
	$MATURITY_CALCULATIONS = array('DAILY'=>0, 'MONTHLY'=>1, 'QUARTERLY'=>2, 'YEARLY'=>3);

	function getDBFormatDate($ddmmyyyy)
	{
		if($ddmmyyyy <> '' && $ddmmyyyy <> '00-00-0000')
		{
			return date('Y-m-d', strtotime($ddmmyyyy));
		}
		else
		{
			return '00-00-0000';
		}
	}
	
	function getDisplayFormatDate($yyyymmdd, $seperator = '-')
	{
		$ddmmyyyy = '';
		if(strtotime($yyyymmdd) <> '' &&  $yyyymmdd <> '0000-00-00' && $yyyymmdd <> '00-00-0000')
		{
			$ddmmyyyy = date("d" . $seperator . "m" . $seperator . "Y", strtotime($yyyymmdd));
			
		}
		/*else
		{
			return '00-00-0000';
		}*/
		return $ddmmyyyy;
		
	}
	
	function getMonths($cycleID)
	{
		$Months = array();

		switch($cycleID)
		{
			case 1:
				$Months = $GLOBALS['MONTHS_YEARLY'];
				break;
			case 2:
				$Months = $GLOBALS['MONTHS_HALFYEARLY'];
				break;
			case 3:
				$Months = $GLOBALS['MONTHS_QUADRUPLE'];
				break;
			case 4:
				$Months = $GLOBALS['MONTHS_QUATERLY'];
				break;
			case 5:
				$Months = $GLOBALS['MONTHS_BIMONTHLY'];
				break;
			case 6:
				$Months = $GLOBALS['MONTHS_MONTHLY'];
				break;
		}
	
		return $Months;
	}
	
	function getRandomUniqueCode()
	{
		$timestamp = DateTime::createFromFormat('U.u', microtime(true));
		$uniqueTime = $timestamp->format("m-d-Y H:i:s.u");
		
		$un=  uniqid();
		$dmtun = $uniqueTime.$un;
		$mdun = md5($dmtran.$un);
		
		//$sort=substr($mdun, 16); // if you want short length code.

		return $mdun;	
	}
	
	 function getCurrentTimeStamp()
	{
		$aryDateTime = array();
		date_default_timezone_set('Asia/Kolkata');	
		$current_DateTime = date('Y-m-d H:i:s');
		$current_Time = date('H:i:s');
		$current_Date = date('Y-m-d');
		
		$aryDateTime['DateTime'] = $current_DateTime;
		$aryDateTime['Date'] = $current_Date;
		$aryDateTime['Time'] = $current_Time;
		
		return $aryDateTime;
	}
	
	function isValidEmailID($email)
	{
		$bResult = true;
		if(filter_var($email, FILTER_VALIDATE_EMAIL) == false)
		{
			$bResult = false;
		}
		//echo '<br/>******Email : ['. $email .'] is [' . $bResult . ']******<br/>';
		return $bResult;
	}
		
?>