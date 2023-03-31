<?php
include_once("dbconst.class.php");
include_once("utility.class.php");
class bank_statement
{
	public $m_dbConn;
	public $obj_Utility;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->obj_Utility = new utility($dbConn);
	}
	
	function getBankDetails($ledgerID)
	{
		$bank_details_query = 'SELECT `BranchName`,`Address`,`AcNumber` FROM `bank_master` where `BankID` = '.$ledgerID;
		//echo $bank_details_query;		
		$bank_details = $this->m_dbConn->select($bank_details_query);
		return $bank_details;
	}
	
	function getDetails($ledgerID, $from, $to, $transType)
	{			
		//$detailsquery = 'SELECT banktable.PaidAmount,banktable.ReceivedAmount,chequw_detailstable.ChequeDate,chequw_detailstable.ChequeNumber FROM `bankregister` as `banktable` join `chequeentrydetails` as `chequw_detailstable` on banktable.ChkDetailID = chequw_detailstable.ID where banktable.LedgerID = '.$ledgerID;
		$detailsquery = "SELECT `PaidAmount`,`ReceivedAmount`,`ChkDetailID`, `VoucherID`,`VoucherTypeID`,`Is_Opening_Balance`,`Date`,`DepositGrp` ,`ReconcileStatus`,`Reconcile`,`Return` FROM `bankregister` where `LedgerID` = '".$ledgerID."'";	
		if($from <> "")
		{
			$detailsquery .= " AND `Date` >= '".getDBFormatDate($from)."'";  
		}
		if($to <> "")
		{
			$detailsquery .= " AND `Date` <= '".getDBFormatDate($to)."'";
		}
		
		if($from == "" && $to == "")
		{
			$detailsquery .= " AND `Date` BETWEEN  '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."' ";  
		}
		
		if($transType == 1)
		{
			$detailsquery .= " AND `PaidAmount` > 0";	
		}
		else if($transType == 2)
		{
			$detailsquery .= " AND `ReceivedAmount` > 0";
		}
		
		//echo "<br>Query : ".$detailsquery;
		$detailsquery .= ' order by `Date`';						
		
		$result = $this->m_dbConn->select($detailsquery);		 
		return $result;
	}
	
	function getActualBankDetails($ledgerID, $from, $to, $transType)
	{
		$detailsquery = "SELECT * FROM `actualbankstmt` where `BankID` = '".$ledgerID."'";	
		if($from <> "")
		{
			$detailsquery .= " AND `Date` >= '".getDBFormatDate($from)."'";  
		}
		if($to <> "")
		{
			$detailsquery .= " AND `Date` <= '".getDBFormatDate($to)."'";
		}
		
		if($from == "" && $to == "")
		{
			$detailsquery .= " AND `Date` BETWEEN  '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."' ";  
		}
		
		if($transType == 1)
		{
			$detailsquery .= " AND `Debit` > 0";	
		}
		else if($transType == 2)
		{
			$detailsquery .= " AND `Credit` > 0";
		}
		
		//echo "<br>Query : ".$detailsquery;
		$detailsquery .= ' order by `Date`';						
		
		$result = $this->m_dbConn->select($detailsquery);		 
		return $result;
	}
	
	
	function getPaymentDetails($chkDetailID, $tableName)
	{		
		if($chkDetailID <> "")
		{	
			if($tableName == 'paymentdetails')
			{
				$chequeDetails_query = 'SELECT `ChequeDate`,`ChequeNumber`, `Comments`, `ChqLeafID`,`IsMultipleEntry`,`Reference` FROM ' . $tableName.' WHERE `id` = ' .$chkDetailID;	
			}
			else
			{
				$chequeDetails_query = 'SELECT `ChequeDate`,`ChequeNumber`, `Comments` FROM ' . $tableName.' WHERE `id` = ' .$chkDetailID;
			}
			$res = $this->m_dbConn->select($chequeDetails_query);
			
			if($tableName == 'paymentdetails')
			{
				$customLeafQuery = "SELECT `CustomLeaf` FROM `chequeleafbook` WHERE `id` = ".$res[0]['ChqLeafID'];				
				$result = $this->m_dbConn->select($customLeafQuery);
				
				$res[0]['CustomLeaf'] = $result[0]['CustomLeaf'];
			}
			
		}		
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
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	function getBankName($ledgerID)
	{
		$sql = 'SELECT `ledger_name` FROM `ledger` where `id` = '.$ledgerID;
		$bankname = $this->m_dbConn->select($sql);
		return $bankname; 	
	}
	
	function getRefTableName($voucherID)
	{
		$sql = "SELECT `RefTableID`, `VoucherNo` FROM `voucher` where `id`='".$voucherID."' ";
		$TableName = $this->m_dbConn->select($sql);
		return $TableName;
		//return $TableName[0]['RefTableID']; 	
	}
	
	function getLedgerDetails($chkDetailID, $tableName, $columnName, $voucherID)
	{
		if($chkDetailID == 0)
		{
			$sql = 'SELECT `Note` FROM `voucher` where `id` = '.$voucherID;
			$res = $this->m_dbConn->select($sql); 	
			$result[0]['ledger_name'] = $res[0]['Note'];
		}
		else
		{								
			$sql = "SELECT ledger_table.id,ledger_table.ledger_name FROM `" . $tableName ."` as `datatable` join `ledger` as `ledger_table` on ledger_table.id = datatable.".$columnName . " where datatable.id = '" . $chkDetailID . "'";
			
			if($tableName == 'chequeentrydetails')
			{
				$sql = "SELECT ledger_table.id,ledger_table.ledger_name FROM `" . $tableName ."` as `datatable` join `ledger` as `ledger_table` on ledger_table.id = datatable.".$columnName . " where datatable.ID = '" . $chkDetailID . "'";
			}		
			$result = $this->m_dbConn->select($sql);
			$arParentDetails = $this->obj_Utility->getParentOfLedger($result[0]['id']);
			if(!(empty($arParentDetails)))
			{			
				$categoryID = $arParentDetails['category'];
				if($categoryID == DUE_FROM_MEMBERS)
				{
					$sqlQuery = "SELECT `owner_name` FROM `member_main` WHERE `unit` = '".$result[0]['id']."'";
					$memberName = $this->m_dbConn->select($sqlQuery);
					if(sizeof($memberName) > 0)
					{
						$result[0]['ledger_name'] .= " - ".$memberName[0]['owner_name'];	
					}
				}			
			}		
		}
		return $result;
	}
	
	//get bankID when depositID or chqleafID is passed 
	function getBankIDFromDID($ID, $table)
	{		
		if($table == 'paymentdetails')
		{
			$sql = "SELECT `BankID` FROM `chequeleafbook` WHERE `id` = ".$ID;			
			$res = $this->m_dbConn->select($sql);
			return $res[0]['BankID'];	
		}
		else
		{
			$sql = "SELECT `bankid` FROM `depositgroup` WHERE `id` = ".$ID;			
			$res = $this->m_dbConn->select($sql);
			return $res[0]['bankid'];	
		}
				
	}
	
	function getBalanceBeforeDate($ledgerID, $from)
	{
		$total = 0;
		if($from <> "")
		{
			$sql = "SELECT SUM(`PaidAmount`) 'TotalPaid', SUM(`ReceivedAmount`) 'TotalReceived' FROM `bankregister` WHERE `LedgerID` = '".$ledgerID."'";		
			$sql .= " AND `Date` < '".getDBFormatDate($from)."'";	
			$balanceBeforeDate = $this->m_dbConn->select($sql);
			$total = $balanceBeforeDate[0]['TotalReceived'] - $balanceBeforeDate[0]['TotalPaid'];
		}
		else
		{
			$openingBalance = $this->obj_Utility->getOpeningBalance(	$ledgerID, $_SESSION['default_year_start_date']);
			$total = $openingBalance['Credit']-$openingBalance['Debit'];
		}
		return $total;
	}
	
	function getTotalAmountForMultEntry($ref)
	{
		$sqlQuery = "SELECT `id`,`Amount` FROM `paymentdetails` WHERE `Reference` = '".$ref."'";
		$amount = $this->m_dbConn->select($sqlQuery);		
		return $amount;
	}
}
?> 