<?php if(!isset($_SESSION)){ session_start(); }

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
	
	public $m_dbConn;
	public function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->objSocietyDetails = new CSocietyDetails($this->m_dbConn);		
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
		$sql = 'SELECT `ledger_name` FROM `ledger` where `id` = '.$ledgerID;		
		$bankname = $this->m_dbConn->select($sql);		
		return $bankname[0]['ledger_name']; 	
	}
	
	function getLedgerName($id)
	{
		$sql = 'SELECT `ledger_name` FROM `ledger` WHERE `id` = '.$id;		
		$ledgerName = $this->m_dbConn->select($sql);
		return $ledgerName[0]['ledger_name'];
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
	
	function getChequeIssueDetails($ledgerID)
	{
		$details = array();		
		$finalArray = array();
		$paymentDeatails = array();	
		$paidTo = array();	
		$sql = 'SELECT `Date`, `VoucherID`, `ChkDetailID`, `PaidAmount`, `ReceivedAmount`, `DepositGrp` FROM `bankregister` WHERE `ReconcileStatus` = 0 AND `LedgerID` = '.$ledgerID;
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
				$details['VoucherDate'] = 	$result[$i]['Date'];			
				$details['VoucherNo'] = $arr[$result[$i]['VoucherID']];
				$details['Amount'] = $result[$i]['PaidAmount'];	
				$details['ChequeNo'] = $paymentDeatails[$result[$i]['ChkDetailID']];
				$details['Particulars'] = $this->getLedgerName($paidTo[$result[$i]['ChkDetailID']]);
				//echo $this->getLedgerName($paidTo[$result[$i]['ChkDetailID']]);
				array_push($finalArray, $details);
			}			
			
		}
		//print_r($finalArray);				
		return $finalArray;	
			
	}
	
	function getChequeDepositDetails($ledgerID)
	{
		$details = array();		
		$finalArray = array();
		$paidBy = array();		
		$chequeEntryDetails = array();
		$sql = 'SELECT `Date`, `VoucherID`, `ChkDetailID`, `PaidAmount`, `ReceivedAmount`, `DepositGrp`, `Is_Opening_Balance` FROM `bankregister` WHERE `ReconcileStatus` = 0 AND `LedgerID` = '.$ledgerID;
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
				$details['VoucherDate'] = 	$result[$i]['Date'];			
				$details['VoucherNo'] = $arr[$result[$i]['VoucherID']];
				$details['Amount'] = $result[$i]['ReceivedAmount'];	
				$details['ChequeNo'] = $chequeEntryDetails[$result[$i]['ChkDetailID']];
				if($result[$i]['Is_Opening_Balance'] == 1)
				{
					$details['Particulars'] = "Opening Balance";
				}
				else
				{
					$details['Particulars'] = $this->getLedgerName($paidBy[$result[$i]['ChkDetailID']]);
				}
				array_push($finalArray, $details);
			}			
			
		}				
		return $finalArray;		
	}
	
	function getReconciledBalance($ledgerID)
	{
		$sql = 'SELECT sum(ReceivedAmount) - sum(PaidAmount) as "Total" FROM `bankregister` WHERE `LedgerID` = "'.$ledgerID.'" AND `ReconcileStatus` = 1';
		$total = $this->m_dbConn->select($sql);
		return $total[0]['Total'];
	}
}
?>