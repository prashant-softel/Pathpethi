<?php
include "dbconst.class.php";

class bank_statement
{
	public $m_dbConn;
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;

	}
	
	function getBankDetails($ledgerID)
	{
		$bank_details_query = 'SELECT `BranchName`,`Address` FROM `bank_master` where `BankID` = '.$ledgerID;
		$bank_details = $this->m_dbConn->select($bank_details_query);
		$bank_details;
	}
	function GetSlipDetails($SlipID, $from_date = "", $to_date = "")
	{
		$bank_details_query = 'SELECT * FROM chequeentrydetails where DepositID='.$SlipID;
		if($from_date <> "" && $to_date <> "")
		{
			//$bank_details_query .= " and (`ChequeDate` between '" . getDBFormatDate($from_date) . "' and '" . getDBFormatDate($to_date) . "'";
			//$bank_details_query .= " OR `ChequeDate` = '1970-01-01' OR `ChequeDate` = '0000-00-00')";
			$bank_details_query .= " and (`VoucherDate` between '" . getDBFormatDate($from_date) . "' and '" . getDBFormatDate($to_date) . "'";
			$bank_details_query .= " OR `VoucherDate` = '1970-01-01' OR `VoucherDate` = '0000-00-00')";
		}
		$bank_details_query .= " ORDER BY `VoucherDate` DESC";
		$bank_details = $this->m_dbConn->select($bank_details_query);
		return $bank_details;
	}
	function GetAccountNo($LedgerID)
	{
		$bank_details_query = 'SELECT AcNumber FROM bank_master where BankID='.$LedgerID;
		$bank_details = $this->m_dbConn->select($bank_details_query);
		//$ChequeDetail = $this->m_dbConn->select($bank_details_query);
		//print_r($bank_details);
		return $bank_details[0]['AcNumber'];
	}
	function GetBankBranch($LedgerID)
	{
		$bank_details_query = 'SELECT BranchName FROM bank_master where BankID='.$LedgerID;
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
		
		$bankname = $this->m_dbConn->select($sql);
		//echo "Bank:".$bankname[0]['ledger_name'];
		return $bankname; 	
	}
	function getLedgerID($DepositID)
	{
		
		$sql = 'SELECT bankid FROM depositgroup where id='.$DepositID;
		
		$bankname = $this->m_dbConn->select($sql);
		//echo "Bank:".$bankname[0]['ledger_name'];
		return $bankname[0]['bankid']; 	
	}
	function CloseDepositSlip($DepositID)
	{
		$BankID = $this->getLedgerID($DepositID);
		$sql = 'update depositgroup set status=1 where bankid='.$BankID. ' and id='.$DepositID;
		//echo $sql;
		//echo "<script>alert('testclose');<//script>";
		$bankname = $this->m_dbConn->update($sql);
		//echo $bankname;
		
	}
}
?> 