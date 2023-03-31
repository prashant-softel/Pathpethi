<?php
class Payment_Report extends dbop
{
	public $m_dbConn;
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;

	}
	
	function GetLeafDetails($LeafID)
	{
		$bank_details_query = 'SELECT * FROM `chequeleafbook` where `id`='.$LeafID;
		//echo $bank_details_query;
		$bank_details = $this->m_dbConn->select($bank_details_query);
		//print_r($bank_details);
		return $bank_details;
	}
	
	function GetChequeEntryDetailsIDForLeaf($LeafID, $IsCustom)
	{
		if($IsCustom == 1)
		{
			$cheque_entry_id = 'SELECT `ID` from `paymentdetails` WHERE ChqLeafID = "' . $LeafID . '" ORDER BY ChequeNumber, ChequeDate ASC';
		}
		else
		{
			$cheque_entry_id = 'SELECT `ID` from `paymentdetails` WHERE ChqLeafID = "' . $LeafID . '" ORDER BY ChequeNumber ASC';	
		}

		$aryResult = $this->m_dbConn->select($cheque_entry_id);

		$aryID = array();

		for($i = 0; $i < sizeof($aryResult); $i++)
		{
			array_push($aryID, $aryResult[$i]['ID']);
		}

		return $aryID;
	}

	function GetChqLeafDetails($ChequeNumber)
	{
		$bank_details_query = 'SELECT `ChequeNumber`,`ChequeDate`,`PaidTo`,`PayerBank`,`ChqLeafID`,`Amount`,`Comments` FROM `paymentdetails` where `ChequeNumber`='.$ChequeNumber;
		$bank_details = $this->m_dbConn->select($bank_details_query);
		
		return $bank_details;
	}
	function GetChqLeafDetailsByID($ID)
	{
		$bank_details_query = 'SELECT `ChequeNumber`,`ChequeDate`,`PaidTo`,`PayerBank`,`ChqLeafID`,`Amount`,`Comments` FROM `paymentdetails` where `id`='.$ID;
		//echo $bank_details_query;
		$bank_details = $this->m_dbConn->select($bank_details_query);
		//print_r($bank_details);
		return $bank_details;
	}
	function GetChqLeafDetails2($ChequeNumber, $LeafID)
	{
		$bank_details_query = 'SELECT `ChequeNumber`,`ChequeDate`,`PaidTo`,`PayerBank`,`ChqLeafID`,`Amount`,`Comments` FROM `paymentdetails` where `ChequeNumber`='.$ChequeNumber ." and `ChqLeafID`=".$LeafID;
		//echo $bank_details_query;
		$bank_details = $this->m_dbConn->select($bank_details_query);
		//print_r($bank_details);
		return $bank_details;
	}
	function GetSlipDetails($SlipID)
	{
		$bank_details_query = 'SELECT * FROM chequeentrydetails where DepositID='.$SlipID;
		//echo $bank_details_query;
		$bank_details = $this->m_dbConn->select($bank_details_query);
		//$ChequeDetail = $this->m_dbConn->select($bank_details_query);
		//print_r($bank_details);
		return $bank_details;
	}
	function GetAccountNo($LedgerID)
	{
		$bank_details_query = 'SELECT AcNumber FROM bank_master where BankID='.$LedgerID;
		//echo $bank_details_query;
		$bank_details = $this->m_dbConn->select($bank_details_query);
		//$ChequeDetail = $this->m_dbConn->select($bank_details_query);
		//print_r($bank_details);
		return $bank_details[0]['AcNumber'];
	}
	function GetBankBranch($LedgerID)
	{
		$bank_details_query = 'SELECT BranchName FROM bank_master where BankID='.$LedgerID;
		//echo $bank_details_query;
		$bank_details = $this->m_dbConn->select($bank_details_query);
		//$ChequeDetail = $this->m_dbConn->select($bank_details_query);
		//print_r($bank_details);
		return $bank_details[0]['BranchName'];
	}
	
	function getDetails($ledgerID)
	{
		
		//$detailsquery = 'SELECT banktable.PaidAmount,banktable.ReceivedAmount,chequw_detailstable.ChequeDate,chequw_detailstable.ChequeNumber FROM `bankregister` as `banktable` join `chequeentrydetails` as `chequw_detailstable` on banktable.ChkDetailID = chequw_detailstable.ID where banktable.LedgerID = '.$ledgerID;
		$detailsquery = 'SELECT `PaidAmount`,`ReceivedAmount`,`ChkDetailID`, `VoucherID`,`VoucherTypeID` FROM `bankregister` where `LedgerID` = '.$ledgerID;
		//echo $detailsquery;
		
		$result = $this->m_dbConn->select($detailsquery);		 
		return $result;
	}
	
	function getPaymentDetails($chkDetailID, $tableName)
	{
		$chequeDetails_query = 'SELECT `ChequeDate`,`ChequeNumber`, `Comments` FROM ' . $tableName.' WHERE `id` = ' .$chkDetailID;
		//echo $chequeDetails_query;
		$res = $this->m_dbConn->select($chequeDetails_query);
		return $res;
	}
	
	function getVoucherType($voucherTypeID)
	{
		$sql = 'SELECT `type` FROM `vouchertype` where `id` = ' .$voucherTypeID;
		//echo $sql;
		$result = $this->m_dbConn->select($sql);
		return $result; 
	}
	
	function getVoucherDetails($voucherID, $perticular)
	{
		$sql = 'SELECT ledger_table.id,ledger_table.ledger_name FROM `voucher` as `vouchertable` join `ledger` as `ledger_table` on ledger_table.id = vouchertable.'.$perticular . ' and vouchertable.id = ' . $voucherID;	
		//echo $sql;	
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	function getBankName($ledgerID)
	{
		
		$sql = 'SELECT led.ledger_name FROM depositgroup as dep JOIN ledger as led on dep.bankid where led.id='.$ledgerID;
		//echo $sql;
		$bankname = $this->m_dbConn->select($sql);
		//echo "Bank:".$bankname[0]['ledger_name'];
		return $bankname; 	
	}
	
	function getLedgerName($ledgerID)
	{
		
		$sql = 'SELECT led.ledger_name FROM  ledger as led where led.id='.$ledgerID;
		//cho $sql;
		$bankname = $this->m_dbConn->select($sql);
		//echo "Bank:".$bankname[0]['ledger_name'];
		return $bankname; 	
	}
	
	function getLedgerID($DepositID)
	{
		
		$sql = 'SELECT bankid FROM depositgroup where id='.$DepositID;
		//echo $sql;
		$bankname = $this->m_dbConn->select($sql);
		//echo "Bank:".$bankname[0]['ledger_name'];
		return $bankname[0]['bankid']; 	
	}
	function CloseDepositSlip($DepositID)
	{
		$BankID = $this->getLedgerID($DepositID);
		$sql = 'update depositgroup set status=1 where bankid='.$BankID. ' and id='.$DepositID;
		//echo $sql;
		//echo $sql;
		//echo "<script>alert('testclose');<//script>";
		$bankname = $this->m_dbConn->update($sql);
		//echo $bankname;
		
	}
}
?> 