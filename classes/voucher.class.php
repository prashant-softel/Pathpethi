<?php
	include_once "dbconst.class.php";
	include_once("utility.class.php");
		
	class voucher
	{
		public $m_dbConn;
		public $obj_utility;
		
		function __construct($dbConn)
		{
			$this->m_dbConn = $dbConn;
			$this->obj_utility = new utility($this->m_dbConn);
		}
		
		public function SetVoucherDetails($BillDate, $RefNo, $RefTableID, $VoucherNo, $SrNo, $VoucherTypeID, $LedgerID, $TransactionType, $Amount, $note = "")
		{			
			$ColName = 'By';
			if($TransactionType == TRANSACTION_CREDIT)
			{
				$ColName = 'To';
			}
			$sqlInsert = "INSERT INTO `voucher`(`Date`, `RefNo`, `RefTableID`, `VoucherNo`, `SrNo`, `VoucherTypeID`, `" . $ColName . "`, `" . $TransactionType . "`,`Note`) VALUES ('" . getDBFormatDate($BillDate) . "', '" . $RefNo .  "', '" . $RefTableID . "', '" .  $VoucherNo .  "', '" . $SrNo . "', '" . $VoucherTypeID . "' , '" . $LedgerID .  "', '" . $Amount . "', '" . $this->m_dbConn->escapeString($note) ."')";
			//echo $sqlInsert;
			$sqlResult = $this->m_dbConn->insert($sqlInsert);
			
			return $sqlResult;
		}
		
		public function GetVoucherDetails($voucherNo , $voucherType , $GetTransactionsDetails = false)
		{
				$ledgername_array=array();
				$Type = '';	
				$tableName = '';

				
				$query1 = "select * from `voucher` where `VoucherNo` = '".$voucherNo."'  and `VoucherTypeID` = '".$voucherType."' ";
				$res1 = $this->m_dbConn->select($query1);
				
				//get ledger name from
				$query2 = "select * from `ledger`";
				$res2 = $this->m_dbConn->select($query2);
				
				for($i = 0; $i < sizeof($res2); $i++)
				{
					$ledgername_array[$res2[$i]['id']]=$res2[$i]['ledger_name'];
				}
				
				/*if($voucherType == VOUCHER_PAYMENT  && $res1[0]['RefTableID'] == TABLE_PAYMENT_DETAILS)
				{
					$Type = 'PAYMENT VOUCHER';	
					$tableName = 'paymentdetails';
				}
				else if($voucherType == VOUCHER_RECEIPT  && $res1[0]['RefTableID'] == TABLE_CHEQUE_DETAILS)
				{
						$Type = 'RECEIPT VOUCHER';
						$tableName = 'chequeentrydetails';			
				}
				else if($voucherType == VOUCHER_CONTRA)
				{
						$Type = 'CONTRA VOUCHER';
						if($res1[0]['RefTableID'] == TABLE_PAYMENT_DETAILS)
						{
									$tableName = 'paymentdetails';			
						}
						else if($res1[0]['RefTableID'] == TABLE_CHEQUE_DETAILS)
						{
									$tableName = 'chequeentrydetails';			
						}
									
				}
				else if($voucherType == VOUCHER_JOURNAL)
				{
					$Type = 'JOURNAL VOUCHER';
				}
				else
				{
						$Type = 'INVALID VOUCHER';
						$tableName = '';				
				}*/
				
				/*if($GetTransactionsDetails == true && $tableName <> "" && $res1[0]['RefNo']  > 0)
				{
					$EntryDetails = $this->GetTransactonDetails($tableName,$res1[0]['RefNo']);		
				}*/
				$n = sizeof($res1); 
				for($i = 0; $i < $n ;$i++)
				{
						if($voucherType == VOUCHER_PAYMENT  && $res1[$i]['RefTableID'] == TABLE_PAYMENT_DETAILS)
						{
							$Type = 'PAYMENT VOUCHER';	
							$tableName = 'paymentdetails';
						}
						else if($voucherType == VOUCHER_RECEIPT  && $res1[$i]['RefTableID'] == TABLE_CHEQUE_DETAILS)
						{
								$Type = 'RECEIPT VOUCHER';
								$tableName = 'chequeentrydetails';			
						}
						else if($voucherType == VOUCHER_CONTRA)
						{
								$Type = 'CONTRA VOUCHER';
								if($res1[$i]['RefTableID'] == TABLE_PAYMENT_DETAILS)
								{
											$tableName = 'paymentdetails';			
								}
								else if($res1[$i]['RefTableID'] == TABLE_CHEQUE_DETAILS)
								{
											$tableName = 'chequeentrydetails';			
								}
											
						}
						else if($voucherType == VOUCHER_JOURNAL)
						{
							$Type = 'JOURNAL VOUCHER';
						}
						else
						{
								$Type = 'INVALID VOUCHER';
								$tableName = '';				
						}
				
					if($GetTransactionsDetails == true && $tableName <> "" && $res1[$i]['RefNo']  > 0)
					{
						if($tableName == 'paymentdetails' && $i == 1)
						{
							$EntryDetails = $this->GetTransactonDetails($tableName,$res1[$i]['RefNo'],true);
						}
						else
						{							
							$EntryDetails = $this->GetTransactonDetails($tableName,$res1[$i]['RefNo']);		
						}
					}
					$res1[$i]['IsMember'] = 0;	
					$arByParentDetails = $this->obj_utility->getParentOfLedger($res1[$i]['By']);
					if(!(empty($arByParentDetails)))
					{
						$ByGroupID = $arByParentDetails['group'];
						$ByCategoryID = $arByParentDetails['category'];	
						if($ByCategoryID == DUE_FROM_MEMBERS)
						{
							$res1[$i]['IsMember'] = 1;
							$queryMember = "SELECT `owner_name` FROM `member_main` where `unit`= '".$res1[$i]['By']."'  ";
							$resMember = $this->m_dbConn->select($queryMember);
							$res1[$i]['owner_name'] = $resMember[0]['owner_name'];	
						}	
						$res1[$i]['TempBy'] = $res1[$i]['By']; 	
						$res1[$i]['By'] = $ledgername_array[$res1[$i]['By']];
						$res1[$i]['To'] = $ledgername_array[$res1[$i]['To']];
						$res1[$i]['Type'] = $Type;
						$res1[$i]['Date'] = getDisplayFormatDate($res1[$i]['Date']);	
						
											
						if(sizeof($EntryDetails) > 0 && $voucherType  <> VOUCHER_JOURNAL )
						{
							for($k = 0; $k < sizeof($EntryDetails); $k++)
							{
								$res1[$i+$k]['ChequeDate'] =  getDisplayFormatDate($EntryDetails[$k]['ChequeDate']);	
								$res1[$i+$k]['ChequeNumber'] = $EntryDetails[$k]['ChequeNumber'];
								
								if($EntryDetails[$k]['InvoiceAmount'] > 0)
								{							
									$res1[$i+$k]['InvoiceAmount'] = $EntryDetails[$k]['InvoiceAmount'];
								} 
								
								$res1[$i+$k]['Amount'] = $EntryDetails[$k]['Amount'];					
								$res1[$i+$k]['TDSAmount'] = $EntryDetails[$k]['TDSAmount'];
								if($voucherType  == VOUCHER_RECEIPT  && $i ==0)
								{
									$res1[$i]['PayerBank'] = $EntryDetails[0]['PayerBank'];
									$res1[$i]['PayerChequeBranch'] = $EntryDetails[0]['PayerChequeBranch'];
									$res1[$i]['DepositID'] = $EntryDetails[0]['DepositID'];
									
									$sqlReg = "SELECT `PeriodID` FROM `billregister` where `BillDate` <  '". getDBFormatDate($res1[$i]['ChequeDate'])."'  order by `BillDate` desc ";
									$resReg = $this->m_dbConn->select($sqlReg);
									
									if($resReg <> "")
									{
										$sqlBill = "SELECT `PeriodID`,`BillNumber` FROM `billdetails` where `PeriodID`= '". $resReg[0]['PeriodID']."' and  `UnitID` = '".$res1[$i]['TempBy'] ."' ";
										$billresult = $this->m_dbConn->select($sqlBill);
										$chequeNumber = $billresult[0]['BillNumber']; 
										if($billresult <> "")
										{
											$sqlPeriod = "Select periodtbl.type, yeartbl.YearDescription from period as periodtbl JOIN year as yeartbl ON periodtbl.YearID = yeartbl.YearID where periodtbl.ID = '" . $billresult[0]['PeriodID'] . "'";
										
											$sqlResult = $this->m_dbConn->select($sqlPeriod);
											$chequeNumber =  "[ Bill No:".$billresult[0]['BillNumber'].'  ][ Maintenance Bill For '.$sqlResult[0]['type'] . " "  . $sqlResult[0]['YearDescription'].']';
											$res1[$i]['BillDetails'] = $chequeNumber;
										}
									}
									else
									{
										$res1[$i]['BillDetails'] = "-";	
									}
								}
								$res1[$i+$k]['ExpenseBy'] = $ledgername_array[$EntryDetails[$k]['ExpenseBy']];
								
								if($res1[$i+$k]['Note'] == '' && $EntryDetails[$k]['Comments']  <> "")
								{
									$res1[$i+$k]['Note'] = $EntryDetails[$k]['Comments'];
								}
							}
						}
					}
				}
				
				return $res1;
				
		}
		
		public function GetTransactonDetails($tableName ,$refNo , $checkForMultEntry = false)
		{			
				if($tableName == 'paymentdetails')	
				{					
					$sql = 'SELECT  `id`,`ChequeDate`,`ChequeNumber`,`Amount`,`Comments` , `TDSAmount`,`ExpenseBy`,`Reference`,`InvoiceAmount` FROM `paymentdetails` where `id` = "'.$refNo.'" ';
					$result = $this->m_dbConn->select($sql);
					if($result[0]['Reference'] > 0 && $checkForMultEntry == true)
					{
						$sql1 = 'SELECT  `id`,`ChequeDate`,`ChequeNumber`,`Amount`,`Comments` , `TDSAmount`,`ExpenseBy`,`InvoiceAmount` FROM `paymentdetails`  where `Reference` = "'.$refNo.'" ';
						$result = $this->m_dbConn->select($sql1);					
					}
				}
				else if($tableName ==  'chequeentrydetails')
				{
					 $sql = 'SELECT `ID`,`ChequeDate`,`ChequeNumber`,`Amount`,`Comments`,`PayerBank`,`PayerChequeBranch`,`DepositID` FROM `chequeentrydetails`  where `ID` = "'.$refNo.'" ';	
					$result = $this->m_dbConn->select($sql);
				}
								
				return $result;
		}
		/*------------------------------------------All Voucher details----------------------------------------------------------*/
	
		public function AllVoucherDetails($voucherNo , $voucherType , $GetTransactionsDetails = false)
		{
				$ledgername_array=array();
				$Type = '';	
				$tableName = '';

				 $query1="SELECT * FROM `voucher` as v join `vouchertype` as vt on v.VoucherTypeID=vt.id where v.VoucherNo='".$voucherNo."'";
				//$query1 = "select * from `voucher` where `VoucherNo` = '".$voucherNo."'  and `VoucherTypeID` = '".$voucherType."' ";
				$res1 = $this->m_dbConn->select($query1);
				//print_r($res1);
				$voucherType=$res1[0]['VoucherTypeID'];
				if( $voucherType <> VOUCHER_SALES && ($voucherType == VOUCHER_PAYMENT || $voucherType == VOUCHER_RECEIPT || $voucherType == VOUCHER_CONTRA))
				{
					$GetTransactionsDetails = true;
				}
				else if( $voucherType <> VOUCHER_SALES && $voucherType == VOUCHER_JOURNAL)
				{
					$GetTransactionsDetails = false;
				}

				// print_r($voucherType);
				
				//get ledger name from
				$query2 = "select * from `ledger`";
				$res2 = $this->m_dbConn->select($query2);
				
				for($i = 0; $i < sizeof($res2); $i++)
				{
					$ledgername_array[$res2[$i]['id']]=$res2[$i]['ledger_name'];
				}
				$n = sizeof($res1); 
				for($i = 0; $i < $n ;$i++)
				{
						if($voucherType == VOUCHER_PAYMENT  && $res1[$i]['RefTableID'] == TABLE_PAYMENT_DETAILS)
						{
							$Type = 'PAYMENT VOUCHER';	
							$tableName = 'paymentdetails';
						}
						else if($voucherType == VOUCHER_RECEIPT  && $res1[$i]['RefTableID'] == TABLE_CHEQUE_DETAILS)
						{
								$Type = 'RECEIPT VOUCHER';
								$tableName = 'chequeentrydetails';			
						}
						else if($voucherType == VOUCHER_CONTRA)
						{
								$Type = 'CONTRA VOUCHER';
								if($res1[$i]['RefTableID'] == TABLE_PAYMENT_DETAILS)
								{
											$tableName = 'paymentdetails';			
								}
								else if($res1[$i]['RefTableID'] == TABLE_CHEQUE_DETAILS)
								{
											$tableName = 'chequeentrydetails';			
								}
											
						}
						else if($voucherType == VOUCHER_JOURNAL)
						{
							$Type = 'JOURNAL VOUCHER';
						}
						else
						{
								$Type = 'INVALID VOUCHER';
								$tableName = '';				
						}
				
					if($GetTransactionsDetails == true && $tableName <> "" && $res1[$i]['RefNo']  > 0)
					{
						if($tableName == 'paymentdetails' && $i == 1)
						{
							$EntryDetails = $this->GetTransactonDetails($tableName,$res1[$i]['RefNo'],true);
						}
						else
						{							
							$EntryDetails = $this->GetTransactonDetails($tableName,$res1[$i]['RefNo']);		
						}
					}
					$res1[$i]['IsMember'] = 0;	
					$arByParentDetails = $this->obj_utility->getParentOfLedger($res1[$i]['By']);
					if(!(empty($arByParentDetails)))
					{
						$ByGroupID = $arByParentDetails['group'];
						$ByCategoryID = $arByParentDetails['category'];	
						if($ByCategoryID == DUE_FROM_MEMBERS)
						{
							$res1[$i]['IsMember'] = 1;
							$queryMember = "SELECT `owner_name` FROM `member_main` where `unit`= '".$res1[$i]['By']."'  ";
							$resMember = $this->m_dbConn->select($queryMember);
							$res1[$i]['owner_name'] = $resMember[0]['owner_name'];	
						}	
						$res1[$i]['TempBy'] = $res1[$i]['By']; 	
						$res1[$i]['By'] = $ledgername_array[$res1[$i]['By']];
						$res1[$i]['To'] = $ledgername_array[$res1[$i]['To']];
						$res1[$i]['Type'] = $Type;
						$res1[$i]['Date'] = getDisplayFormatDate($res1[$i]['Date']);	
						
											
						if(sizeof($EntryDetails) > 0 && $voucherType  <> VOUCHER_JOURNAL )
						{
							for($k = 0; $k < sizeof($EntryDetails); $k++)
							{
								$res1[$i+$k]['ChequeDate'] =  getDisplayFormatDate($EntryDetails[$k]['ChequeDate']);	
								$res1[$i+$k]['ChequeNumber'] = $EntryDetails[$k]['ChequeNumber'];
								
								if($EntryDetails[$k]['InvoiceAmount'] > 0)
								{							
									$res1[$i+$k]['InvoiceAmount'] = $EntryDetails[$k]['InvoiceAmount'];
								} 
								
								$res1[$i+$k]['Amount'] = $EntryDetails[$k]['Amount'];					
								$res1[$i+$k]['TDSAmount'] = $EntryDetails[$k]['TDSAmount'];
								if($voucherType  == VOUCHER_RECEIPT  && $i ==0)
								{
									$res1[$i]['PayerBank'] = $EntryDetails[0]['PayerBank'];
									$res1[$i]['PayerChequeBranch'] = $EntryDetails[0]['PayerChequeBranch'];
									$res1[$i]['DepositID'] = $EntryDetails[0]['DepositID'];
									
									$sqlReg = "SELECT `PeriodID` FROM `billregister` where `BillDate` <  '". getDBFormatDate($res1[$i]['ChequeDate'])."'  order by `BillDate` desc ";
									$resReg = $this->m_dbConn->select($sqlReg);
									
									if($resReg <> "")
									{
										$sqlBill = "SELECT `PeriodID`,`BillNumber` FROM `billdetails` where `PeriodID`= '". $resReg[0]['PeriodID']."' and  `UnitID` = '".$res1[$i]['TempBy'] ."' ";
										$billresult = $this->m_dbConn->select($sqlBill);
										$chequeNumber = $billresult[0]['BillNumber']; 
										if($billresult <> "")
										{
											$sqlPeriod = "Select periodtbl.type, yeartbl.YearDescription from period as periodtbl JOIN year as yeartbl ON periodtbl.YearID = yeartbl.YearID where periodtbl.ID = '" . $billresult[0]['PeriodID'] . "'";
										
											$sqlResult = $this->m_dbConn->select($sqlPeriod);
											$chequeNumber =  "[ Bill No:".$billresult[0]['BillNumber'].'  ][ Maintenance Bill For '.$sqlResult[0]['type'] . " "  . $sqlResult[0]['YearDescription'].']';
											$res1[$i]['BillDetails'] = $chequeNumber;
										}
									}
									else
									{
										$res1[$i]['BillDetails'] = "-";	
									}
								}
								$res1[$i+$k]['ExpenseBy'] = $ledgername_array[$EntryDetails[$k]['ExpenseBy']];
								
								if($res1[$i+$k]['Note'] == '' && $EntryDetails[$k]['Comments']  <> "")
								{
									$res1[$i+$k]['Note'] = $EntryDetails[$k]['Comments'];
								}
							}
						}
					}
				}
				
				return $res1;
		}
	}
?>