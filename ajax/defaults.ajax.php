<?php 
// echo "TEsST";
// die();
	if(!isset($_SESSION['sadmin']))
	{
		//header('Location: ../login.php?alog');
	}
?>

<?php

 include_once("../classes/dbconst.class.php");
 include_once("../classes/defaults.class.php");
include_once("../classes/include/dbop.class.php");
	 $dbConn = new dbop();
	 $dbConnRoot = new dbop(true);
	$obj_default = new defaults($dbConn,$dbConnRoot);

	if(isset($_REQUEST['update']))
	{
		
		$defaultYear = $_REQUEST['defaultYear'];
		$defaultPeriod = $_REQUEST['defaultPeriod'];
		$interestOnPrinciple = $_REQUEST['interestOnPrinciple'];
		$penaltyToMember = $_REQUEST['penaltyToMember'];
		$bankCharges = $_REQUEST['bankCharges'];
		$tdsPayable = $_REQUEST['tdsPayable'];
		$imposeFine = $_REQUEST['imposeFine'];         // Impose Fine
		$currentAsset = $_REQUEST['currentAsset'];
		$dueFromMember = $_REQUEST['dueFromMember'];
		$bankAccount = $_REQUEST['bankAccount'];
		$cashAccount = $_REQUEST['cashAccount'];
		$defaultIncomeExpenditureAccount = $_REQUEST['defaultIncomeExpenditureAccount'];
		$defaultAdjustmentCredit = $_REQUEST['defaultAdjustmentCredit'];
		$default_loan = $_REQUEST['default_loan'];
		$default_saving_account = $_REQUEST['default_saving_account'];
		$default_fixed_deposit = $_REQUEST['default_fixed_deposit'];
		$default_daily_deposit = $_REQUEST['default_daily_deposit'];
		$default_monthly_deposit = $_REQUEST['default_monthly_deposit'];
		$societyID = $_REQUEST['societyid'];
		//$defaultEmailID = $_REQUEST['defaultEmailID'];
		
		$updateDefault = $obj_default->setDefault($societyID, $defaultYear, $defaultPeriod, $interestOnPrinciple, $penaltyToMember, $bankCharges,$tdsPayable, $currentAsset, $dueFromMember, $bankAccount, $cashAccount,$defaultIncomeExpenditureAccount, $defaultAdjustmentCredit, $igstServiceTax, $cgstServiceTax, $sgstServiceTax, $cessServiceTax,$imposeFine,
	 	$default_loan, $default_saving_account, $default_fixed_deposit, $default_daily_deposit, $default_monthly_deposit  /*,$defaultEmailID*/);
		
		/*$updateDefault = $updateDefault . $obj_default->setDefault(APP_DEFAULT_PERIOD, $defaultPeriod);
		$updateDefault = $updateDefault . $obj_default->setDefault(APP_DEFAULT_INTEREST_ON_PRINCIPLE_DUE, $interestOnPrinciple);
		$updateDefault = $updateDefault . $obj_default->setDefault(APP_DEFAULT_CURRENT_ASSET, $currentAsset);
		$updateDefault = $updateDefault . $obj_default->setDefault(APP_DEFAULT_DUE_FROM_MEMBERS, $dueFromMember);
		$updateDefault = $updateDefault . $obj_default->setDefault(APP_DEFAULT_BANK_ACCOUNT, $bankAccount);
		$updateDefault = $updateDefault . $obj_default->setDefault(APP_DEFAULT_SOCIETY, $societyID);*/
		
		$obj_default->getDefaults($societyID, true);
				
		echo "Defaults Updated Successfully";// . $updateDefault;
	}
?>