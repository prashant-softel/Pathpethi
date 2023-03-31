<?php

include_once("dbconst.class.php");
include_once("utility.class.php");

class Cleaner
{
	public $m_dbConn;
	public $ErrorLog;
	public $m_objUtility;
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->m_objUtility = new utility($dbConn);
	}
	
	public function FetchRecord()
	{
		//fetch all records from bank register
		$sqlFetchBank = "SELECT * FROM `bankregister`  where `Is_Opening_Balance` <> 1 order by `Date`";
		$resBank = $this->m_dbConn->select($sqlFetchBank);
		//validate each record fetch from bankregister
		for($i = 0; $i < sizeof($resBank); $i++)
		{
				echo "<br><br><br><font color='#0000FF'>Validating Bank Entry..</font>";
				$BankTable = "<table border='1px solid black' style='border-collapse:collapse;'><tr><th>ID</th><th>DATE</th><th>LEDGER ID</th><th>VOUCHER ID</th><th>VOUCHER TYPE ID</th><th>PAID AMOUNT</th><th>RECEIVED AMOUNT</th><th>CHECK DETAILS ID</th></tr>";
				$BankTable .= "<tr><td>".$resBank[$i]['id']."</td><td>".$resBank[$i]['Date']."</td><td>".$resBank[$i]['LedgerID']."</td><td>".$resBank[$i]['VoucherID']."</td><td>".$resBank[$i]['VoucherTypeID']."</td><td>".$resBank[$i]['PaidAmount']."</td><td>".$resBank[$i]['ReceivedAmount']."</td><td>".$resBank[$i]['ChkDetailID']."</td></tr></table>";
				echo $BankTable;
				
				//get table id from voucher to check entry is from  ie.paymentdetails or chequeentrydetails
				$RefTableName = $this->getRefTableName($resBank[$i]['VoucherID']);	
				echo "<br>RefTableName:".$RefTableName;
				$chkDetailID = $resBank[$i]['ChkDetailID'];
				//echo "<br>ChkDetailID:".$chkDetailID;
				$voucherID = $resBank[$i]['VoucherID'];
				$voucherTypeID = $resBank[$i]['VoucherTypeID'];			
				
				//get vouchertype from voucher table
				$Type = $this->getVoucherType($voucherTypeID);
				$voucherType = $Type[0]['type'];
				$paidAmount = $resBank[$i]['PaidAmount'];
				$receivedAmount = $resBank[$i]['ReceivedAmount'];
				if($RefTableName <> "")
				{
					if($RefTableName == TABLE_PAYMENT_DETAILS)
					{	
						//reading record from paymentdetails table
						echo "<br>Type:Payment";			
						$sqlFetchPayment = "SELECT * FROM `paymentdetails` where `id`='".$chkDetailID."'";
						$resPayment = $this->m_dbConn->select($sqlFetchPayment);
						if($resPayment <> "")
						{
							//entry exist in paymentdetails table now check for related voucher and register table for existence
							$this->paymentDetailsChecker($resPayment,$resBank[$i]['id']);
						}
						else
						{
							//entry does not exist in paymentdetails
							echo "<br>Record not found in paymentdetails";
							$delBank = "<font color='#FF0000'>DELETE FROM `bankregister` WHERE `id`='".$resBank[$i]['id']."'</font> ";
							echo "<br>**Error**".$delBank;
						}
					}
					else if($RefTableName == TABLE_CHEQUE_DETAILS)
					{
						//reading record from chequeentrydetails table
						echo "<br>Type:Receipt";
						$sqlFetchCheque = "SELECT * FROM `chequeentrydetails` where `ID`='".$chkDetailID."'";
						$resCheque = $this->m_dbConn->select($sqlFetchCheque);									
						if($resCheque <> "")
						{
							//entry exist in chequeentrydetails table now check for related voucher and register table for existence
							$this->receiptDetailsChecker($resCheque,$resBank[$i]['id']);
						}
						else
						{
							//entry does not exist in chequeentrydetails
							echo "<br>Record not found in chequeentrydetails";
							$delBank = "<font color='#FF0000'>DELETE FROM `bankregister` WHERE `id`='".$resBank[$i]['id']."'</font> ";
							echo "<br>**Error**".$delBank;
						}							
					}
						
				}
				else
				{
						//bankregister entry not connected to any voucher hence delete bankregister entry
						echo "<br>Voucher not found[Voucher ID:".$resBank[$i]['VoucherID']." missing]";
						$delBank = "<font color='#FF0000'>DELETE FROM `bankregister` WHERE `id`='".$resBank[$i]['id']."' </font>";
						echo "<br>**Error**".$delBank;
				}
				
			echo "<br>---------------------------------------------------------------------------------------------------------------------------------------------------------";	
		}
		
		
	}
	
	
	public function paymentDetailsChecker($PaymentDetails,$BankID)
	{
		$isContraEntry=true;
		$Id_Array = array();
		$Id_Array2 = array();
		
		//get PaidTo group id and category id 
		$arPaidToParentDetails = $this->m_objUtility->getParentOfLedger($PaymentDetails[0]['PaidTo']);
		if(!(empty($arPaidToParentDetails)))
		{
			$PaidToGroupID = $arPaidToParentDetails['group'];
			$PaidToCategoryID = $arPaidToParentDetails['category'];
		}
		echo "<br>PaidTo:[".$arPaidToParentDetails['ledger_name']."][".$arPaidToParentDetails['group_name']."]";
		
		//get PayerBank group id and category id 
		$arParentDetails = $this->m_objUtility->getParentOfLedger($PaymentDetails[0]['PayerBank']);
		if(!(empty($arParentDetails)))
		{
			$PayerBankGroupID = $arParentDetails['group'];
			$PayerBankCategoryID = $arParentDetails['category'];
		}
		echo "<br>PayerBank:[".$arParentDetails['ledger_name']."][".$arParentDetails['group_name']."]";
		
		//check if payment voucher exists
		$voucher_select = "select `id`,`VoucherNo` from `voucher` where `RefNo`=".$PaymentDetails[0]['id']." and `RefTableID`=".TABLE_PAYMENT_DETAILS." ";
		$resVoucher = $this->m_dbConn->select($voucher_select);
		if($resVoucher == "")
		{
			//payment voucher does not exists
			echo "<br>Record not found in voucher[RefNo:".$PaymentDetails[0]['id']."  RefTableID:".TABLE_PAYMENT_DETAILS." missing]";
			$delBank = "<font color='#FF0000'>DELETE FROM `bankregister` WHERE `id`='".$BankID."'</font> ";
			echo "<br>**Error**".$delBank;
			$delPayment = "<font color='#FF0000'>DELETE FROM `paymentdetails` WHERE `id`='".$PaymentDetails[0]['id']."'</font> ";
			echo "<br>**Error**".$delPayment;
		}
		else
		{
			//payment voucher exists
			foreach($resVoucher as $key => $val)
			{
				array_push($Id_Array,$resVoucher[$key]['id']);
			}
			$VoucherIDArray = implode(',', $Id_Array);
			echo "<br>PaidToCategoryID::".$PaidToCategoryID;
			echo "<br>PayerBankCategoryID::".$PayerBankCategoryID;
			
			/*check condition for contra entry
				if paidto is bank account and payerbank is cash account i.e contra entry
				if paidto is bank account and payerbank is bank account  i.e contra entry
				if paidto is cash account and payerbank is bank account  i.e contra entry
			*/
			if(($PaidToCategoryID == BANK_ACCOUNT || $PaidToCategoryID == CASH_ACCOUNT) && ($PayerBankCategoryID == BANK_ACCOUNT || $PayerBankCategoryID == CASH_ACCOUNT))
			{
				//set flag for contra entry
				$isContraEntry = true;	
			}
			else
			{
				$isContraEntry = false;	
			}
			
			//entry is not contra entry check all register and voucher
			if($isContraEntry == false)
			{
				//echo "<br>Single entry";
				//payment voucher is exist now check if entry exists in any register
				if($resVoucher <> "")
				{
					//check entry in each register
					$sqlFetchLiability="SELECT * FROM `liabilityregister` where `VoucherID` IN ($VoucherIDArray)";
					$resLiability = $this->m_dbConn->select($sqlFetchLiability);
					$sqlFetchAsset="SELECT * FROM `assetregister` where `VoucherID` IN ($VoucherIDArray)";
					$resAsset = $this->m_dbConn->select($sqlFetchAsset);	
					$sqlFetchIncome="SELECT * FROM `incomeregister` where `VoucherID` IN ($VoucherIDArray)";
					$resIncome = $this->m_dbConn->select($sqlFetchIncome);
					$sqlFetchExpense="SELECT * FROM `expenseregister` where `VoucherID` IN ($VoucherIDArray)";
					$resExpense = $this->m_dbConn->select($sqlFetchExpense);
					//if any not exists in any register
					if($resLiability == "" && $resAsset == "" && $resExpense == "" && $resIncome == "")
					{
						echo "<br>Record not found any register";
						$delBank = "<font color='#FF0000'>DELETE FROM `bankregister` WHERE `id`='".$BankID."'</font> ";
						echo "<br>**Error**".$delBank;
						$delPayment = "<font color='#FF0000'>DELETE FROM `paymentdetails` WHERE `id`='".$PaymentDetails[0]['id']."'</font> ";
						echo "<br>**Error**".$delPayment;
						$delVoucher = "<font color='#FF0000'>DELETE FROM from `voucher` where `RefNo`='".$PaymentDetails[0]['id']."' and `RefTableID`='".TABLE_PAYMENT_DETAILS."'</font> ";
						echo "<br>**Error**".$delVoucher;
					}
				}
				
				//if expenseby is not blanck or 0 then entry is double entry
				if($PaymentDetails[0]['ExpenseBy'] <> "" && $PaymentDetails[0]['ExpenseBy'] <> 0)
				{
					//echo "<br>journal voucher entry";
					$arParentExpenseByDetails = $this->m_objUtility->getParentOfLedger($PaymentDetails[0]['ExpenseBy']);
					if(!(empty($arParentExpenseByDetails)))
					{
						$ExpenseByGroupID = $arParentExpenseByDetails['group'];
						$ExpenseByCategoryID = $arParentExpenseByDetails['category'];
					}
					echo "<br>ExpenseBy:[".$arParentExpenseByDetails['ledger_name']."][".$arParentExpenseByDetails['group_name']."]";
					
					//check for journal voucher
					$JVoucherNo = $resVoucher[0]['VoucherNo']-1;
					$jvoucher_select = "select `id` from `voucher` where `VoucherNo`=".$JVoucherNo." ";
					$VoucherData02 = $this->m_dbConn->select($jvoucher_select);
					
					
					if($VoucherData02 == "")
					{
						//journal voucher does not exist
						echo "<br>Journal Voucher Record not found in voucher[Voucher No:".$JVoucherNo." missing]";
						$delBank2 = "<font color='#FF0000'>DELETE FROM `bankregister` WHERE `id`='".$BankID."'</font> ";
						echo "<br>**Error**".$delBank2;
						$delPayment2 = "<font color='#FF0000'>DELETE FROM `paymentdetails` WHERE `id`='".$PaymentDetails[0]['id']."'</font> ";
						echo "<br>**Error**".$delPayment2;
						$delVoucher2 = "<font color='#FF0000'>DELETE FROM from `voucher` where `RefNo`='".$PaymentDetails[0]['id']."' and `RefTableID`='".TABLE_PAYMENT_DETAILS."'</font> ";
						echo "<br>**Error**".$delVoucher2;
					}
					else
					{
						//journal voucher exists
						foreach($VoucherData02 as $key => $val)
						{
							array_push($Id_Array2,$VoucherData02[$key]['id']);
						}
						$VoucherIDArray2 = implode(',', $Id_Array2);
						
						//check entry in each register
						$sqlFetchLiability = "SELECT * FROM `liabilityregister` where `VoucherID` IN ($VoucherIDArray2)";
						$resLiability2 = $this->m_dbConn->select($sqlFetchLiability);
						$sqlFetchAsset = "SELECT * FROM `assetregister` where `VoucherID` IN ($VoucherIDArray2)";
						$resAsset2 = $this->m_dbConn->select($sqlFetchAsset);	
						$sqlFetchIncome = "SELECT * FROM `incomeregister` where `VoucherID` IN ($VoucherIDArray2)";
						$resIncome2 = $this->m_dbConn->select($sqlFetchIncome);
						$sqlFetchExpense = "SELECT * FROM `expenseregister` where `VoucherID` IN ($VoucherIDArray2)";
						$resExpense2 = $this->m_dbConn->select($sqlFetchExpense);
						
						//if entry not found in any register
						if($resLiability2 == "" && $resAsset2 == "" && $resExpense2 == "" && $resIncome2 == "")
						{
							echo "<br>Record not found any register";
							$delBank2 = "<font color='#FF0000'>DELETE FROM `bankregister` WHERE `id`='".$BankID."'</font> ";
							echo "<br>**Error**".$delBank2;
							$delPayment2 = "<font color='#FF0000'>DELETE FROM `paymentdetails` WHERE `id`='".$PaymentDetails[0]['id']."'</font> ";
							echo "<br>**Error**".$delPayment2;
							$delVoucher2 = "<font color='#FF0000'>DELETE FROM from `voucher` where `VoucherNo`='".$JVoucherNo."' </font> ";
							echo "<br>**Error**".$delVoucher2;
						}
					}
				}
			}
			else
			{
				//if contra entry flag true
				echo "<br>contra entry";	
			}
		}
	}
	
	public function receiptDetailsChecker($ReceiptDetails,$BankID)
	{
		$isContraEntry = false;	
		$Id_Array = array();
		//check for voucher exists
		$voucher_select = "select `id`,`VoucherNo` from `voucher` where `RefNo`=".$ReceiptDetails[0]['ID']." and `RefTableID`=".TABLE_CHEQUE_DETAILS." ";
		$resVoucher = $this->m_dbConn->select($voucher_select);
		if($resVoucher == "")
		{
			//voucher not found in voucher table
			echo "<br>Record not found in voucher[RefNo:".$ReceiptDetails[0]['ID']."  RefTableID:".TABLE_CHEQUE_DETAILS." missing]";
			$delBank = "<font color='#FF0000'>DELETE FROM `bankregister` WHERE `id`='".$BankID."'</font> ";
			echo "<br>**Error**".$delBank;
			$delReceipt = "<font color='#FF0000'>DELETE FROM `chequeentrydetails` WHERE `ID`='".$ReceiptDetails[0]['ID']."'</font> ";
			echo "<br>**Error**".$delReceipt;
		}
		else
		{
			//get category id and group id for PaidBy
			$arPaidByParentDetails = $this->m_objUtility->getParentOfLedger($ReceiptDetails[0]['PaidBy']);
			if(!(empty($arPaidByParentDetails)))
			{
				$PaidByGroupID = $arPaidByParentDetails['group'];
				$PaidByCategoryID = $arPaidByParentDetails['category'];
			}
			echo "<br>PaidBy:[".$arPaidByParentDetails['ledger_name']."][".$arPaidByParentDetails['group_name']."]";
			
			//get category id and group id for PaidBy
			$arBankIDDetails = $this->m_objUtility->getParentOfLedger($ReceiptDetails[0]['BankID']);
			if(!(empty($arBankIDDetails)))
			{
				$BankGroupID = $arBankIDDetails['group'];
				$BankCategoryID = $arBankIDDetails['category'];
			}
			echo "<br>Deposite to ledger:[".$arBankIDDetails['ledger_name']."][".$arBankIDDetails['group_name']."]";
			
			foreach($resVoucher as $key => $val)
			{
				array_push($Id_Array,$resVoucher[$key]['id']);
			}
			$VoucherIDArray = implode(',', $Id_Array);
			/*check condition for contra entry
			if PaidBy is bank account and selected Bank is cash account i.e contra entry
			if PaidBy is bank account and selected Bank is bank account  i.e contra entry
			if PaidBy is cash account and selected Bank is bank account  i.e contra entry
			*/
			if(($PaidByCategoryID == BANK_ACCOUNT || $PaidByCategoryID == CASH_ACCOUNT) && ($BankCategoryID == BANK_ACCOUNT || $BankCategoryID == CASH_ACCOUNT))
			{
				//set flag for contra entry
				$isContraEntry = true;	
			}
			else
			{
				$isContraEntry = false;	
			}
			
			//if entry is not contra then check all register for existence
			if($isContraEntry == false)
			{
				$sqlFetchLiability = "SELECT * FROM `liabilityregister` where `VoucherID` IN ($VoucherIDArray)";
				$resLiability = $this->m_dbConn->select($sqlFetchLiability);
				$sqlFetchAsset = "SELECT * FROM `assetregister` where `VoucherID` IN ($VoucherIDArray)";
				$resAsset = $this->m_dbConn->select($sqlFetchAsset);	
				$sqlFetchIncome = "SELECT * FROM `incomeregister` where `VoucherID` IN ($VoucherIDArray)";
				$resIncome = $this->m_dbConn->select($sqlFetchIncome);
				$sqlFetchExpense = "SELECT * FROM `expenseregister` where `VoucherID` IN ($VoucherIDArray)";
				$resExpense = $this->m_dbConn->select($sqlFetchExpense);
				
				//entry not found in any register
				if($resLiability == "" && $resAsset == "" && $resExpense == "" && $resIncome == "")
				{
					echo "<br>Record not found any register";
					$delBank = "<font color='#FF0000'>DELETE FROM `bankregister` WHERE `id`='".$BankID."'</font> ";
					echo "<br>**Error**".$delBank;
					$delReceipt = "<font color='#FF0000'>DELETE FROM `chequeentrydetails` WHERE `ID`='".$ReceiptDetails[0]['ID']."'</font> ";
					echo "<br>**Error**".$delReceipt;
					$delVoucher = "<font color='#FF0000'>DELETE FROM from `voucher` where `RefNo`='".$ReceiptDetails[0]['ID']."' and `RefTableID`='".TABLE_CHEQUE_DETAILS."'</font> ";
					echo "<br>**Error**".$delVoucher;
				}
			}
			else
			{
				echo "contra entry";	
			}
		}
	}
	
	public function getVoucherType($voucherTypeID)
	{
		$sql = 'SELECT `type` FROM `vouchertype` where `id` = ' .$voucherTypeID;
		$result = $this->m_dbConn->select($sql);
		return $result; 
	}
	
	public function getRefTableName($voucherID)
	{
		$sql = "SELECT `RefTableID` FROM `voucher` where `id`='".$voucherID."' ";
		$TableName = $this->m_dbConn->select($sql);
		return $TableName[0]['RefTableID']; 	
	}

	
	
}
?>