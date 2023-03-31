<?php if(!isset($_SESSION)){ session_start(); }

include_once("dbconst.class.php");
include_once("utility.class.php");

class CSocietyDetails
{
	
	public $sSocietyName;
	public $sSocietyAddress ;
	public $sSocietyRegNo ;
	public $iSocietyID;
	
	public function __construct($dbConn)
	{
		$this->sSocietyName = "";
	    $this->sSocietyAddress = "";
	    $this->sSocietyRegNo = "";
	    $this->iSocietyID = 0;
	}
} 

class BankReco
{
	public $objSocietyDetails;
	private $objUtility;	
	
	public $m_dbConn;
	public function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->objSocietyDetails = new CSocietyDetails($this->m_dbConn);
		$this->objUtility = new utility($this->m_dbConn);		
	}
	
	function GetSocietyDetails($ReqSocietyID)
	{
		$sqlFetch = "select * from society where society_id=".$ReqSocietyID."";
		$res02 = $this->m_dbConn->select($sqlFetch); 		
		if($res02 <> "")
		{
			foreach($res02 as $row => $v )
			{
				$this->objSocietyDetails->sSocietyName = $res02[$row]['society_name'];
				$this->objSocietyDetails->sSocietyRegNo = $res02[$row]['registration_no'];	
				$this->objSocietyDetails->sSocietyAddress = $res02[$row]['society_add'];
			}
		}
		else
		{
			echo "No Data Found from society database test for society_id=<".$ReqSocietyID.">.";
		}
	}
	
	function getBankName($ledgerID)
	{
		$sql = "SELECT `ledger_name` FROM `ledger` where `id` = '".$ledgerID."'";			
		$bankname = $this->m_dbConn->select($sql);		
		return $bankname[0]['ledger_name']; 	
	}
	
	function getLedgerName($id)
	{		
		$sql = "SELECT `ledger_name` FROM `ledger` WHERE `id` = '".$id."'";				
		$ledgerName = $this->m_dbConn->select($sql);
		$ledger = $ledgerName[0]['ledger_name'];
		$arParentDetails = $this->objUtility->getParentOfLedger($id);
		if(!(empty($arParentDetails)))
		{			
			$categoryID = $arParentDetails['category'];
			if($categoryID == DUE_FROM_MEMBERS)
			{
				$sqlQuery = "SELECT `owner_name` FROM `member_main` WHERE `unit` = '".$id."'";
				$memberName = $this->m_dbConn->select($sqlQuery);
				if(sizeof($memberName) > 0)
				{
					$ledger .= " - ".$memberName[0]['owner_name'];	
				}
			}			
		}
		return $ledger;
	}
	
	function getVoucherNoArray()
	{
		$arr = array();
		$voucherQuery = 'SELECT `id`, `VoucherNo` FROM `voucher`';
		$res = $this->m_dbConn->select($voucherQuery);						
		for($i = 0; $i < sizeof($res); $i++)
		{			
			$arr[$res[$i]['id']]= $res[$i]['VoucherNo'];			
		}
		return $arr;
	}
	
	function getChequeIssueDetails($ledgerID, $from, $to)
	{
		$details = array();		
		$finalArray = array();
		$paymentDeatails = array();	
		$paidTo = array();	
		//$sql = "SELECT `Date`, `VoucherID`, `ChkDetailID`, `PaidAmount`, `ReceivedAmount`, `DepositGrp`, `Reconcile Date`, `ReconcileStatus` FROM `bankregister` WHERE `LedgerID` = '".$ledgerID."' AND `Date` BETWEEN '".$from."' AND '".$to."'";							
		$sql = "SELECT `Date`, `VoucherID`, `ChkDetailID`, `PaidAmount`, `ReceivedAmount`, `DepositGrp`, `Reconcile Date`, `ReconcileStatus`, `VoucherTypeID` FROM `bankregister` WHERE `LedgerID` = '".$ledgerID."' AND `Date` BETWEEN '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."' ORDER BY `Date` ";
		$result = $this->m_dbConn->select($sql);		
		$arr = $this->getVoucherNoArray();
		
		$paymentDetailsQuery = 'SELECT `id`, `ChequeNumber`, `PaidTo` FROM `paymentdetails`';
		$res =  $this->m_dbConn->select($paymentDetailsQuery);
		for($i = 0; $i < sizeof($res); $i++)
		{			
			$paymentDeatails[$res[$i]['id']]= $res[$i]['ChequeNumber'];	
			$paidTo[$res[$i]['id']] = $res[$i]['PaidTo'];	
		}				
		//print_r($paidTo);
		for($i = 0; $i < sizeof($result); $i++)
		{		
			if($result[$i]['PaidAmount'] > 0)
			{				
				$lID = $paidTo[$result[$i]['ChkDetailID']];
				$chequeNo = $paymentDeatails[$result[$i]['ChkDetailID']];
				$refTable = "";
				if($result[$i]['VoucherTypeID']	== 6)
				{
					$refTable = $this->getRefTableName($result[$i]['VoucherID']);
					if($refTable == TABLE_CHEQUE_DETAILS)
					{
						$chqEntryQuery = "SELECT * FROM `chequeentrydetails` WHERE `ID` = '".$result[$i]['ChkDetailID']."'";
						$chqEntryDtls = $this->m_dbConn->select($chqEntryQuery);
						$lID = $chqEntryDtls[0]['BankID'];
						$chequeNo = $chqEntryDtls[0]['ChequeNumber'];
					}
				}
				
				if($result[$i]['ReconcileStatus'] == 1 && $result[$i]['Reconcile Date'] > getDBFormatDate($to))
				{					
				 	$details['VoucherDate'] = 	$result[$i]['Date'];			
					$details['VoucherNo'] = $arr[$result[$i]['VoucherID']];
					$details['Amount'] = $result[$i]['PaidAmount'];						
					$details['ChequeNo'] = $chequeNo;											
					$details['Particulars'] = $this->getLedgerName($lID);
					array_push($finalArray, $details);
				}
				else if($result[$i]['ReconcileStatus'] == 0)
				{									
					$details['VoucherDate'] = 	$result[$i]['Date'];			
					$details['VoucherNo'] = $arr[$result[$i]['VoucherID']];
					$details['Amount'] = $result[$i]['PaidAmount'];						
					$details['ChequeNo'] = $chequeNo;					
					$details['Particulars'] = $this->getLedgerName($lID);
					array_push($finalArray, $details);
				}												
			}						
		}
		//print_r($finalArray);				
		return $finalArray;	
			
	}
	
	function getChequeDepositDetails($ledgerID, $from, $to)
	{
		$details = array();		
		$finalArray = array();
		$paidBy = array();		
		$chequeEntryDetails = array();
		//$sql = "SELECT `Date`, `VoucherID`, `ChkDetailID`, `PaidAmount`, `ReceivedAmount`, `DepositGrp`, `Is_Opening_Balance`, `Reconcile Date`, `ReconcileStatus` FROM `bankregister` WHERE `LedgerID` = '".$ledgerID."' AND `Date` BETWEEN '".$from."' AND '".$to."'";				
		//$sql = "SELECT `Date`, `VoucherID`, `ChkDetailID`, `PaidAmount`, `ReceivedAmount`, `DepositGrp`, `Is_Opening_Balance`, `Reconcile Date`, `ReconcileStatus`,`VoucherTypeID` FROM `bankregister` WHERE `LedgerID` = '".$ledgerID."' AND `Date` BETWEEN '".getDBFormatDate($this->m_objUtility->GetDateByOffset($from,-1))."' AND '".getDBFormatDate($to)."' AND `Is_Opening_Balance` = 0";		
		$sql = "SELECT `Date`, `VoucherID`, `ChkDetailID`, `PaidAmount`, `ReceivedAmount`, `DepositGrp`, `Is_Opening_Balance`, `Reconcile Date`, `ReconcileStatus`,`VoucherTypeID` FROM `bankregister` WHERE `LedgerID` = '".$ledgerID."' AND `Date` BETWEEN '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."' ORDER BY `Date` ";														
		$result = $this->m_dbConn->select($sql);
		
			
		
		$arr = $this->getVoucherNoArray();
								
		$chequeEntryDetailsQuery = 'SELECT `ID`, `ChequeNumber`, `PaidBy` FROM `chequeentrydetails`';
		$res = $this->m_dbConn->select($chequeEntryDetailsQuery);
		for($i = 0; $i < sizeof($res); $i++)
		{			
			$chequeEntryDetails[$res[$i]['ID']]= $res[$i]['ChequeNumber'];	
			$paidBy[$res[$i]['ID']] = $res[$i]['PaidBy'];		
		}
		
		for($i = 0; $i < sizeof($result); $i++)
		{					
			if($result[$i]['ReceivedAmount'] > 0)
			{
				$lID = $paidBy[$result[$i]['ChkDetailID']];
				$chequeNo = $chequeEntryDetails[$result[$i]['ChkDetailID']];
				$refTable = "";
				if($result[$i]['VoucherTypeID']	== 6)
				{
					$refTable = $this->getRefTableName($result[$i]['VoucherID']);
					if($refTable == TABLE_PAYMENT_DETAILS)
					{
						$paymentEntryQuery = "SELECT * FROM `paymentdetails` WHERE `ID` = '".$result[$i]['ChkDetailID']."'";
						$paymentEntryDtls = $this->m_dbConn->select($paymentEntryQuery);
						$lID = $paymentEntryDtls[0]['PayerBank'];
						$chequeNo = $paymentEntryDtls[0]['ChequeNumber'];
					}
				}
				
				if($result[$i]['ReconcileStatus'] == 1 && $result[$i]['Reconcile Date'] > getDBFormatDate($to))
				{
					$details['VoucherDate'] = 	$result[$i]['Date'];			
					$details['VoucherNo'] = $arr[$result[$i]['VoucherID']];
					$details['Amount'] = $result[$i]['ReceivedAmount'];	
					$details['ChequeNo'] = $chequeNo;
					if($result[$i]['Is_Opening_Balance'] == 1)
					{
						$details['Particulars'] = "Opening Balance";
					}
					else
					{
						$details['Particulars'] = $this->getLedgerName($lID);
					}
					array_push($finalArray, $details);
				}
				else if($result[$i]['ReconcileStatus'] == 0)
				{
					$details['VoucherDate'] = 	$result[$i]['Date'];			
					$details['VoucherNo'] = $arr[$result[$i]['VoucherID']];
					$details['Amount'] = $result[$i]['ReceivedAmount'];	
					$details['ChequeNo'] = $chequeNo;
					if($result[$i]['Is_Opening_Balance'] == 1)
					{
						$details['Particulars'] = "Opening Balance";
					}
					else
					{
						$details['Particulars'] = $this->getLedgerName($lID);
					}
					array_push($finalArray, $details);
				}
			}						
		}
		
		//fetching opening balance row
		$sql23 = "SELECT `Date` as VoucherDate, `VoucherID`, `ChkDetailID`, `PaidAmount`, `ReceivedAmount` as Amount, `DepositGrp`, `Is_Opening_Balance`, `Reconcile Date`, `ReconcileStatus`,`VoucherTypeID` FROM `bankregister` WHERE `LedgerID` = '".$ledgerID."' AND `Is_Opening_Balance` = 1 AND `Reconcile` = 0";														
		$result23 = $this->m_dbConn->select($sql23);
		
		//converting array of array to single array		
		if($result23 <> "")
		{
			$flatten = array();
			array_walk_recursive($result23, function($value,$key) use(&$flatten) {
        		if($key == 'VoucherDate')
				{
					$value = $_SESSION['default_year_start_date'];
				}
				$flatten[$key] = $value;
   		 	});
			//append opening balance array to start of $result array
			if($flatten['Is_Opening_Balance'] == 1)
			{
				$flatten['Particulars'] = "Opening Balance";
			}
			if(count($finalArray) == 0)
			{
				array_push($finalArray, $flatten);
			}
			else
			{
				array_unshift($finalArray ,$flatten);
			}
		}	
						
		return $finalArray;		
	}
	
	function getReconciledBalance($ledgerID, $to)
	{
		$sql = "SELECT sum(ReceivedAmount) - sum(PaidAmount) as 'Total' FROM `bankregister` WHERE `LedgerID` = '".$ledgerID."' AND `ReconcileStatus` = 1 AND `Date` <= '".getDBFormatDate($to)."' AND `Reconcile Date` <= '".getDBFormatDate($to)."'";				
		$total = $this->m_dbConn->select($sql);
		return $total[0]['Total'];
	}
	
	function getBalanceBeforeDate($ledgerID, $from, $to)
	{
		$sql = "SELECT SUM(`PaidAmount`) 'TotalPaid', SUM(`ReceivedAmount`) 'TotalReceived' FROM `bankregister` WHERE `ReconcileStatus` = 0 AND `LedgerID` = '".$ledgerID."' AND `Date` < '".getDBFormatDate($from)."' AND `Is_Opening_Balance` = 0"; //AND `Date` >= '".getDBFormatDate($_SESSION['default_year_start_date'])."'";	
		$result = $this->m_dbConn->select($sql);
		
		//$sql1 = "SELECT SUM(`PaidAmount`) 'TotalPaid', SUM(`ReceivedAmount`) 'TotalReceived' FROM `bankregister` WHERE `ReconcileStatus` = 1 AND `LedgerID` = '".$ledgerID."' AND `Date` < '".getDBFormatDate($from)."' AND `Is_Opening_Balance` = 0 AND `Reconcile Date` > '".getDBFormatDate($to)."'";
		$sql1 = "SELECT SUM(`PaidAmount`) 'TotalPaid', SUM(`ReceivedAmount`) 'TotalReceived' FROM `bankregister` WHERE `ReconcileStatus` = 1 AND `LedgerID` = '".$ledgerID."' AND `Date` < '".getDBFormatDate($from)."' AND `Reconcile Date` > '".getDBFormatDate($to)."'";
		$result1 = $this->m_dbConn->select($sql1);
		
		if(sizeof($result1) > 0)
		{
			$result[0]['TotalPaid'] += $result1[0]['TotalPaid'];
			$result[0]['TotalReceived'] += $result1[0]['TotalReceived'];
		}
		return $result;
	}
	
	function getRefTableName($voucherID)
	{
		$sql = "SELECT `RefTableID` FROM `voucher` where `id`='".$voucherID."' ";
		$TableName = $this->m_dbConn->select($sql);
		return $TableName[0]['RefTableID']; 	
	}
}
?>