<?php
	include_once "dbconst.class.php";
	
	class regiser
	{
		private $m_dbConn;
		private $ShowDebugTrace;
		function __construct($dbConn)
		{
			$this->m_dbConn = $dbConn;
			$ShowDebugTrace = 0;
		}


		public function UpdateRegister($ledgerID, $voucherID, $transactionType, $amount)
		{
			
			if($this->ShowDebugTrace == 1)
			{
				echo "Inside UpdateRegister";
			}
			$aryParent = $this->getLedgerParent($ledgerID);
			$groupID = $aryParent['group'];
			$categoryID = $aryParent['category'];

			if($this->ShowDebugTrace == 1)
			{
				print_r($aryParent );
				echo "<BR>GroupID :". $groupID . " Category :" . $categoryID . " LedgerID : " . $ledgerID . " VoucherID :" . $voucherID . "   Amount :" . $amount . "<BR>";
			}

			if($groupID == ASSET) 	
			{
				$sqlUpdate = "UPDATE `assetregister` SET `" . $transactionType . "` = '" . $amount . "'  where `VoucherID` = '" . $voucherID ."' and `LedgerID` ='" . $ledgerID . "' ";
				
			}
			else if($groupID == LIABILITY)
			{
				$sqlUpdate = "UPDATE `liabilityregister` SET `" . $transactionType . "` = '" . $amount . "'  where `VoucherID` = '" . $voucherID ."' and `LedgerID` ='" . $ledgerID . "' ";
				
			}
			else if($groupID == EXPENSE)
			{
				$sqlUpdate = "UPDATE `expenseregister` SET `" . $transactionType . "` = '" . $amount ."' where `VoucherID`= '" . $voucherID ."' and `LedgerID` ='" . $ledgerID . "' ";

			}
			else if($groupID == INCOME)
			{
				$sqlUpdate = "UPDATE `incomeregister` SET `" . $transactionType . "` = '" . $amount ."' where `VoucherID`= '" . $voucherID ."' and `LedgerID` ='" . $ledgerID . "' ";		
			}
			else
			{
				echo "<BR>Invalid $groupID<BR>";	
			}

			if($sqlUpdate <> "")
			{
				$sqlResult = $this->m_dbConn->update($sqlUpdate);
			}
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>sqlUpdate:" . $sqlUpdate . "<BR>";
				echo "Result:" . $sqlResult . "<BR>";
			}
			return $sqlResult;
		}

		public function SetRegister($date, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance)
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "Inside SetRegister";
			}
			$aryParent = $this->getLedgerParent($ledgerID);
			$groupID = $aryParent['group'];
			$categoryID = $aryParent['category'];
			if($this->ShowDebugTrace == 1)
			{
				print_r($aryParent );
				echo "<BR>GroupID :". $groupID . "  LedgerID : " . $ledgerID . " VoucherID :" . $voucherID . "   Amount :" . $amount . "<BR>";
			}
			if($groupID == ASSET) 	
			{
				if($categoryID == $_SESSION['default_bank_account'] || $categoryID == $_SESSION['default_cash_account'])
				{
					$transactionType = TRANSACTION_PAID_AMOUNT;
					if($transactionType == TRANSACTION_DEBIT)
					{
						$transactionType = TRANSACTION_RECEIVED_AMOUNT;
					}
					return $this->SetBankRegister($date, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, 0, 0, $isOpeningBalance = 0, $chequeDate = 0, $ref = 0, $reconcileDate = 0, $reconcileStatus = 0, $reconcile = 0, $return = 0);	
				}
				else
				{
					return $this->SetAssetRegister2($date, $groupID , $categoryID, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance);	
				}	
			}
			else if($groupID == LIABILITY)
			{
		 		return $this->SetLiabilityRegister2($date, $groupID , $categoryID, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance);
			}
			else if($groupID == EXPENSE)
			{
		 		return $this->SetExpenseRegister($ledgerID, $date, $voucherID, $voucherTypeID, $transactionType, $amount);
			}
			else if($groupID == INCOME)
			{
		 		return $this->SetIncomeRegister($ledgerID, $date, $voucherID, $voucherTypeID, $transactionType, $amount);
			}
			else
			{
				echo "<BR>Invalid $groupID<BR>";	
			}

		}
		public function SetIncomeRegister($ledgerID, $date, $voucherID, $voucherTypeID, $transactionType, $amount)
		{
			if($this->ShowDebugTrace == 1)
			{	
				echo "<BR>In SetIncome. Ledger:". $ledgerID . " VoucherID :" . $voucherID . "   Amount :" . $amount . "<BR>";
			}
			$sqlInsert = "INSERT INTO `incomeregister`(`LedgerID`, `Date`, `VoucherID`, `VoucherTypeID`, `" . $transactionType . "`) VALUES ('" . $ledgerID . "', '" . getDBFormatDate($date) . "', '" . $voucherID .  "', '" . $voucherTypeID . "', '" . $amount . "')";
			
			$sqlResult = $this->m_dbConn->insert($sqlInsert);
			if($this->ShowDebugTrace == 1)
			{	
				echo "<BR>Result Inserted ID: " . $sqlResult . "<BR>";
			}
			return $sqlResult;
		}
		public function SetExpenseRegister($ledgerID, $date, $voucherID, $voucherTypeID, $transactionType, $amount, $ExpenseHead = 0)
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>In SetExpense  LedgerID:". $ledgerID . " VoucherID :" . $voucherID . "   Amount :" . $amount . "<BR>";
			}
			$sqlInsert = "INSERT INTO `expenseregister`(`LedgerID`, `Date`, `VoucherID`, `VoucherTypeID`, `" . $transactionType . "`,`ExpenseHead`) VALUES ('" . $ledgerID . "', '" . getDBFormatDate($date) . "', '" . $voucherID .  "', '" . $voucherTypeID . "', '" . $amount . "','".$ExpenseHead."')";
			$sqlResult = $this->m_dbConn->insert($sqlInsert);
			
			if($this->ShowDebugTrace == 1)
			{	
				echo "<BR>Result Inserted ID: " . $sqlResult . "<BR>";
			}
			return $sqlResult;
		}
			
		public function SetAssetRegister($date, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance)
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>In SetAssetRegister Ledger:". $ledgerID . " VoucherID :" . $voucherID . "   Amount :" . $amount . "<BR>";
			}
			$aryParent = $this->getLedgerParent($ledgerID);
		
			$groupID = $aryParent['group'];
			$categoryID = $aryParent['category'];
		 	$this->SetAssetRegister2($date, $groupID, $categoryID, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance);
		}

		public function SetAssetRegister2($date, $groupID, $categoryID, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance)
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Inside SetAssetRegister2<BR>";
			}
			$sqlInsert = "INSERT INTO `assetregister`(`Date`, `CategoryID`, `SubCategoryID`, `LedgerID`, `VoucherID`, `VoucherTypeID`, `" . $transactionType . "`, `Is_Opening_Balance`) VALUES ('" . getDBFormatDate($date) . "', '" . $groupID . "', '" . $categoryID . "', '" . $ledgerID . "', '" . $voucherID . "',  '" . $voucherTypeID . "', '" . $amount . "', '" . $isOpeningBalance . "')";
			$sqlResult = $this->m_dbConn->insert($sqlInsert);
			if($this->ShowDebugTrace == 1)
			{	
				echo "<BR>Result Inserted ID: " . $sqlResult . "<BR>";
			}
			
			return $sqlResult;
		}
		
		public function SetLiabilityRegister($date, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance)
		{
			if($this->ShowDebugTrace == 1)
			{
					echo "<BR>Liability:". $ledgerID . " VoucherID :" . $voucherID . "   Amount :" . $amount . "<BR>";
			}
			$aryParent = $this->getLedgerParent($ledgerID);
		
			$groupID = $aryParent['group'];
			$categoryID = $aryParent['category'];
			
		 	$this->SetLiabilityRegister2($date, $groupID, $categoryID, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance);
		}
		
		public function SetLiabilityRegister2($date, $groupID, $categoryID, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance)
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Inside SetLiabilityRegister2";
			}
			$sqlInsert = "INSERT INTO `liabilityregister`(`Date`, `CategoryID`, `SubCategoryID`, `LedgerID`, `VoucherID`, `VoucherTypeID`, `" . $transactionType . "`, `Is_Opening_Balance`) VALUES ('" . getDBFormatDate($date) . "', '" . $groupID . "', '" . $categoryID . "', '" . $ledgerID . "', '" . $voucherID . "',  '" . $voucherTypeID . "', '" . $amount . "', '" . $isOpeningBalance . "')";
			$sqlResult = $this->m_dbConn->insert($sqlInsert);
			
			if($this->ShowDebugTrace == 1)
			{	
				echo "<BR>Result Inserted ID: " . $sqlResult . "<BR>";
			}			
			return $sqlResult;
		}
		
		
		public function SetBankRegister($date, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $depositGroup, $chequeDetailID, $isOpeningBalance = 0, $chequeDate = 0, $ref = 0, $reconcileDate = 0, $reconcileStatus = 0, $reconcile = 0, $return = 0)
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Bank Register:". $ledgerID . " VoucherID :" . $voucherID . "   Amount :" . $amount . "<BR>";
			}
			
			$sqlInsert = "INSERT INTO `bankregister`(`Date`, `LedgerID`, `VoucherID`, `VoucherTypeID`, `" . $transactionType . "`, `DepositGrp`, `ChkDetailID`, `Is_Opening_Balance`, `Cheque Date`, `Ref`, `Reconcile Date`, `ReconcileStatus`, `Reconcile`, `Return`) VALUES ('" . getDBFormatDate($date) . "', '" . $ledgerID . "', '" . $voucherID .  "', '" . $voucherTypeID . "', '" . $amount . "', '" . $depositGroup . "', '" . $chequeDetailID . "', '" . $isOpeningBalance . "', '".getDBFormatDate($chequeDate)."', '" . $ref . "', '".getDBFormatDate($reconcileDate)."', '".$reconcileStatus."', '".$reconcile."', '".$return."')";
			$sqlResult = $this->m_dbConn->insert($sqlInsert);
			
			return $sqlResult;
		}
	
		private function getLedgerParent($ledgerID)
		{
			$sqlSelect = "select categorytbl.group_id, ledgertbl.categoryid from ledger As ledgertbl JOIN account_category As categorytbl ON ledgertbl.categoryid = categorytbl.category_id where ledgertbl.id = '" . $ledgerID . "'";
			$result = $this->m_dbConn->select($sqlSelect);
			
			$aryParent = array();
			$aryParent['group'] = $result[0]['group_id'];
			$aryParent['category'] = $result[0]['categoryid'];
			
			return $aryParent;
		}
	}
?>