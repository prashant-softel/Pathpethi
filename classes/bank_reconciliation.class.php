<?php
include_once("voucher.class.php");
include_once("dbconst.class.php");
include_once("latestcount.class.php");
include_once("utility.class.php");
include_once("bank_statement.class.php");
include_once("register.class.php");
class bank_reconciliation
{	
	public $actionPage = "../bank_reconciliation.php";	
	public $m_dbConn;
	public $obj_view_bank_statement;
	public $prevCheque;
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;	
		$this->obj_view_bank_statement = new bank_statement($this->m_dbConn);	
		$this->prevCheque = "";
	}
	
	function startProcess()
	{				
		try
		{
			$obj_voucher = new voucher($this->m_dbConn);
			$obj_LatestCount = new latestCount($this->m_dbConn);
			$obj_Utility = new utility($this->m_dbConn);
			$obj_register = new regiser($this->m_dbConn);
			//echo "reqclass:".$_REQUEST["ledgerID"];
			$details = $this->getDetails($_POST["ledgerID"], $_POST['dateType'], $_POST['voucher'], $_POST['status'], $_POST['From'],$_POST['To'], $_POST['chequeNo'], $_POST['ledger']);												
			$iSrNo = 1;					
			if(isset($_POST))
			{			
				if(PENALTY_TO_MEMBER == 0)
				{
					return "Please set default PENALTY_TO_MEMBER Ledger in Defaults";
				}
								
				$flag = 0;								
				for($i = 0; $i < sizeof($details); $i++)
				{														
					$this->m_dbConn->begin_transaction();																				
					$status = $this->getReconcileStatus($details[$i]['ID']);//$this->m_dbConn->select($selectQuery);				
					
					if($status[0]['ReconcileStatus'] != 1)
					{							
						if($_POST['return'.$i] > 0)
						{												
							if($_POST['bank_penalty'] == "")
							{
								return "Please enter Bank Penalty Amount";									
							}
							
							if($_POST['cancel_date'.$i] == 0)
							{
								//return "Please select clear/return date";	
								$flag = 1;
								continue;
							}
							
							$yearDesc = $this->GetYearDesc($_SESSION['default_year']);
							$selectQuery = 'SELECT `id` FROM `chequeleafbook` WHERE `IsReturnChequeLeaf` = 1 AND `BankID` = "'.$_POST["ledgerID"].'" AND `LeafCreatedYearID` = "'.$_SESSION['default_year'].'"';
							
							$result = $this->m_dbConn->select($selectQuery);
							
							if($result == "")
							{								
								$insert_query="insert into chequeleafbook (`LeafName`,`BankID`,`Comment`,`CustomLeaf`,`IsReturnChequeLeaf`, `status`, `LeafCreatedYearID`) values ('ReverseChequeLeafBook-".$yearDesc."','".$_POST["ledgerID"]."','Leaf Created for bounce cheque entries : ".$yearDesc.".',1, 1, 'Y', '".$_SESSION['default_year']."')";
								$chqleafID = $this->m_dbConn->insert($insert_query);
							}
							else
							{
								$chqleafID = $result[0]['id'];	
							}
																					
							$comment = 'Reverse entry for Cheque #'.$details[$i]['ChequeNumber'].' from '.$details[$i]['ledger_name'].'.';
							$insert_query="insert into paymentdetails(`ChequeDate`, `ChequeNumber`, `Amount`, `PaidTo`, `EnteredBy`, `PayerBank`, `Comments`, `VoucherDate`, `ChqLeafID`, `ExpenseBy`) values ('".getDBFormatDate($_POST['cancel_date'.$i])."', '".$details[$i]['ChequeNumber']."', '".$_POST['bank_penalty']."', '".BANK_CHARGES."', '".$_SESSION['login_id']."', '".$_POST['ledgerID']."', '".$comment."','".getDBFormatDate($_POST['cancel_date'.$i])."','".$chqleafID."', '0')";												
							$data = $this->m_dbConn->insert($insert_query);
							
							$insert_query="insert into paymentdetails(`ChequeDate`, `ChequeNumber`, `Amount`, `PaidTo`, `EnteredBy`, `PayerBank`, `Comments`, `VoucherDate`, `ChqLeafID`, `ExpenseBy`) values ('".getDBFormatDate($_POST['cancel_date'.$i])."', '".$details[$i]['ChequeNumber']."', '".$details[$i]['ReceivedAmount']."', '".$details[$i]['id']."', '".$_SESSION['login_id']."', '".$_POST['ledgerID']."', '".$comment."','".getDBFormatDate($_POST['cancel_date'.$i])."','".$chqleafID."', '0')";						
							$data1 = $this->m_dbConn->insert($insert_query);
											
							$note = 'Cheque #'.$details[$i]['ChequeNumber'].' from '.$details[$i]['ledger_name'].' is rejected. ['.$_POST['note'.$i].']';
							$amount = $details[$i]['ReceivedAmount'] + $_POST['bank_penalty'];	
							//$total_penalty = $_POST['bank_penalty'] + $_POST['society_penalty'];
							$iVoucherCounter = $obj_LatestCount->getLatestVoucherNo($_SESSION['society_id']);								
							$voucherID_I = $obj_voucher->SetVoucherDetails(getDBFormatDate($_POST['cancel_date'.$i]),$data, TABLE_PAYMENT_DETAILS, $iVoucherCounter,$iSrNo, VOUCHER_PAYMENT,$_POST['ledgerID'],TRANSACTION_DEBIT,$_POST['bank_penalty'], $note);																	
							$voucherID_II = $obj_voucher->SetVoucherDetails(getDBFormatDate($_POST['cancel_date'.$i]),$data, TABLE_PAYMENT_DETAILS, $iVoucherCounter,$iSrNo+1, VOUCHER_PAYMENT,BANK_CHARGES,TRANSACTION_CREDIT,$_POST['bank_penalty'], $note);
							
							//$society_amount = $_POST['amount'.$i] + $_POST['society_penalty'];
							$iVoucherCounter = $obj_LatestCount->getLatestVoucherNo($_SESSION['society_id']);
							$voucherID_III = $obj_voucher->SetVoucherDetails(getDBFormatDate($_POST['cancel_date'.$i]),$data1,TABLE_PAYMENT_DETAILS,$iVoucherCounter,$iSrNo,VOUCHER_PAYMENT,$_POST['ledgerID'],TRANSACTION_DEBIT,$details[$i]['ReceivedAmount'],$note);				
							$voucherID_IV = $obj_voucher->SetVoucherDetails(getDBFormatDate($_POST['cancel_date'.$i]),$data1,TABLE_PAYMENT_DETAILS, $iVoucherCounter,$iSrNo+1, VOUCHER_PAYMENT,$details[$i]['id'],TRANSACTION_CREDIT,$details[$i]['ReceivedAmount'], $note);						
		
							$obj_register->SetBankRegister($_POST['cancel_date'.$i],$_POST['ledgerID'],$voucherID_III,VOUCHER_PAYMENT, TRANSACTION_PAID_AMOUNT,$details[$i]['ReceivedAmount'],$chqleafID,$data1,0,$details[$i]['Cheque Date'], $details[$i]['ID']);						
							
							$obj_register->SetBankRegister($_POST['cancel_date'.$i],$_POST['ledgerID'],$voucherID_I,VOUCHER_PAYMENT,TRANSACTION_PAID_AMOUNT,$_POST['bank_penalty'],$chqleafID,$data,0,$details[$i]['Cheque Date'],$details[$i]['ID']);						
							
							//$obj_register->SetIncomeRegister(PENALTY_TO_MEMBER, $_POST['cancel_date'.$i], 0, "", TRANSACTION_CREDIT, $_POST['society_penalty']);
													
							$obj_register->SetExpenseRegister(BANK_CHARGES, $_POST['cancel_date'.$i], $voucherID_II, VOUCHER_PAYMENT, TRANSACTION_DEBIT, $_POST['bank_penalty'], 0); 																		
							
							$arParentDetails = $obj_Utility->getParentOfLedger($details[$i]['id']);
														
							if(!(empty($arParentDetails)))
							{
								$ExpenseByGroupID = $arParentDetails['group'];
								$PaidToCategoryID = $arParentDetails['category'];	
								
								if($ExpenseByGroupID==LIABILITY)
								{																						
									$obj_register->SetLiabilityRegister(getDBFormatDate($_POST['cancel_date'.$i]),$details[$i]['id'],$voucherID_IV,VOUCHER_PAYMENT, TRANSACTION_DEBIT, $details[$i]['ReceivedAmount'], 0);													
								}								
								else if($ExpenseByGroupID==ASSET)
								{
									if($PaidToCategoryID == BANK_ACCOUNT || $PaidToCategoryID == CASH_ACCOUNT)						
									{
											$obj_register->SetBankRegister(getDBFormatDate($_POST['cancel_date'.$i]), $details[$i]['id'], $voucherID_IV, VOUCHER_PAYMENT, TRANSACTION_RECEIVED_AMOUNT, $details[$i]['ReceivedAmount'], $chqleafID, $data1, 0, getDBFormatDate($details[$i]['Cheque Date']), $details[$i]['ID']);
									}
									else
									{
										$obj_register->SetAssetRegister(getDBFormatDate($_POST['cancel_date'.$i]), $details[$i]['id'], $voucherID_IV, VOUCHER_PAYMENT, TRANSACTION_DEBIT, $details[$i]['ReceivedAmount'], 0);				
									}
								}								
								else if($ExpenseByGroupID==INCOME)
								{													
									$obj_register->SetIncomeRegister($details[$i]['id'], getDBFormatDate($_POST['cancel_date'.$i]), $voucherID_IV, VOUCHER_PAYMENT, TRANSACTION_DEBIT, $details[$i]['ReceivedAmount']);
								}								
								else if($ExpenseByGroupID==EXPENSE)
								{																		 
									$obj_register->SetExpenseRegister($details[$i]['id'],getDBFormatDate($_POST['cancel_date'.$i]), $voucherID_IV,VOUCHER_PAYMENT, TRANSACTION_DEBIT,$details[$i]['ReceivedAmount'],0);												
								}																					
							}																												
							
							$reversal_credits = 'INSERT INTO `reversal_credits`(`Date`, `VoucherID`, `UnitID`, `Amount`, `LedgerID`) VALUES ("'.getDBFormatDate($_POST['cancel_date'.$i]).'",0,"'.$details[$i]['id'].'","'.$_POST['society_penalty'].'","'.PENALTY_TO_MEMBER.'")	';
							$this->m_dbConn->insert($reversal_credits);	
							
							if($details[$i]['ledger_name'] != "Opening Balance")
							{						
							$update_chequeDetail = 'UPDATE `chequeentrydetails` SET `IsReturn`= 1 WHERE `ID`= '.$details[$i]['ChkDetailID']	;
							$this->m_dbConn->update($update_chequeDetail);									
							}
							$updateQuery = 'UPDATE `bankregister` SET `Reconcile Date` = "'.getDBFormatDate($_POST['cancel_date'.$i]).'", `ReconcileStatus`= 1, `Return` = 1 WHERE `id` = '.$details[$i]['ID'];								
							$this->m_dbConn->update($updateQuery);																																	
						}
						elseif($_POST['reconcile'.$i] > 0)
						{
							$isMultEntry = false;
							if($_POST['cancel_date'.$i] == 0)
							{
								//return "Please select clear/return date";	
								$flag = 1;
								continue;
							}
														
							$multEntries = $this->GetMultEntryArray($details[$i]['PaidAmount'],$details[$i]['ChkDetailID']);							
							if(sizeof($multEntries) > 0)
							{									
								for($cnt = 0; $cnt < sizeof($multEntries); $cnt++)
								{
									$updateQuery1 = 'UPDATE `bankregister` SET `Reconcile Date` = "'.getDBFormatDate($_POST['cancel_date'.$i]).'", `ReconcileStatus`= 1, `Reconcile` = 1 WHERE `ChkDetailID` = "'.$multEntries[$cnt]['id'].'" AND `PaidAmount` > 0';																				
						$this->m_dbConn->update($updateQuery1);
								}
							}
							else
							{																													
								$updateQuery = 'UPDATE `bankregister` SET `Reconcile Date` = "'.getDBFormatDate($_POST['cancel_date'.$i]).'", `ReconcileStatus`= 1, `Reconcile` = 1 WHERE `id` = '.$details[$i]['ID'];																	
						$this->m_dbConn->update($updateQuery);	
							}
						}													
					}		
					else
					{
						//echo "undo : ".$_POST['undo'.$i];	
						if($_POST['undo'.$i] > 0)
						{
							$multEntries = $this->GetMultEntryArray($details[$i]['PaidAmount'],$details[$i]['ChkDetailID']);							
							if(sizeof($multEntries) > 0)
							{									
								for($cnt = 0; $cnt < sizeof($multEntries); $cnt++)
								{
									$undoQuery1 = 'UPDATE `bankregister` SET `Reconcile Date` = "0", `ReconcileStatus`= 0, `Reconcile` = 0 WHERE `ChkDetailID` = "'.$multEntries[$cnt]['id'].'" AND `PaidAmount` > 0';										
								$this->m_dbConn->update($undoQuery1);
								}
							}
							else
							{
								$undoQuery = 'UPDATE `bankregister` SET `Reconcile Date` = "0", `ReconcileStatus`= 0, `Reconcile` = 0 WHERE `id` = '.$details[$i]['ID'];										
								$this->m_dbConn->update($undoQuery);
							}
						}
					}
					
					$this->m_dbConn->commit();
				}  
				if($flag == 1)
				{
					return "Please select clear/return date";
				}
			}
		}
		catch(Exception $exp)
		{
			$this->m_dbConn->rollback();
			return $exp;
		}
	}
	
	function getChqBounceCharge($ledgerID)
	{
		$sql = 'SELECT `chq_bounce_charge` FROM `society` WHERE `society_id` = (SELECT `society_id` FROM `ledger` WHERE `id` = '.$ledgerID.')';
		$res = $this->m_dbConn->select($sql);
		return $res[0]["chq_bounce_charge"];	
	}
	
	function getLedgers($DepositGrp, $ChkDetailID, $voucherID, $ledger)
	{								
		$ledgerName = array();		
		if($DepositGrp == -1)
		{				
			$ledgerQuery = 'SELECT ledgertable.id,payment.id as "TableID",ledgertable.ledger_name, payment.ChequeNumber FROM `ledger` AS `ledgertable` JOIN `paymentdetails` AS `payment` ON ledgertable.id = payment.PaidTo WHERE payment.PaidTo = ( SELECT `PaidTo` FROM `paymentdetails` WHERE `ID` = '.$ChkDetailID.')'; 
		}
		else
		{				
			$ledgerQuery = 'SELECT ledgertable.id,chequedetails.ID as "TableID",ledgertable.ledger_name, chequedetails.ChequeNumber FROM `ledger` as `ledgertable` JOIN `chequeentrydetails` as `chequedetails` on ledgertable.id = chequedetails.PaidBy where chequedetails.PaidBy = (SELECT `PaidBy` from `chequeentrydetails` where `ID` = '.$ChkDetailID.')'; 
		}	
		if($ledger <> "")
		{
			$ledgerQuery .= ' AND ledgertable.ledger_name = "'.$ledger.'"' ;	
		}		
		$result = $this->m_dbConn->select($ledgerQuery);
		
		if($ledger == "")
		{	
			if($ChkDetailID == 0)
			{
				$sql = 'SELECT `Note` FROM `voucher` where `id` = '.$voucherID;
				$res = $this->m_dbConn->select($sql); 	
				$result[0]['ledger_name'] = $res[0]['Note'];
			}
		}
		return $result;
	}
	
	public function combobox($query, $id, $defaultString = '', $defaultValue = '')
	{
		if($defaultString <> '')
		{		
			$str.="<option value='" . $defaultValue . "'>" . $defaultString . "</option>";
		}
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($id==$v)
						{
							$sel = 'selected';	
						}
						else
						{
							$sel = '';
						}
						
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
					}
					else
					{
						$str.=$v."</OPTION>";
					}
					$i++;
				}
			}
		}
			return $str;
	}

	function getPaymentDetails($chkDetailID, $tableName, $ChequeNo)
	{						
		if($chkDetailID <> "")
		{		
			$chequeDetails_query = 'SELECT `ChequeNumber`, `Comments` FROM ' . $tableName.' WHERE `id` = ' .$chkDetailID;	
			if($ChequeNo <> "")
			{
				$chequeDetails_query .= ' AND `ChequeNumber` LIKE "'.$ChequeNo.'%"';
			}			
			$res = $this->m_dbConn->select($chequeDetails_query);
			
			if($ChequeNo == "")
			{	
				if($chkDetailID == 0)
				{					
					$res[0]['ChequeNumber'] = '-';
					$res[0]['Comments'] = '-';
				}
			}
		}		
		return $res;		
	}
	
	function getDetails($ledgerID, $dateType, $voucherType, $status, $from, $to, $chequeNo, $ledgerName)
	{					
		$displayDetails = array();
		//echo "details:".$ledgerID;		
		$detailsquery = 'SELECT `id`,`Date`,`PaidAmount`,`ReceivedAmount`,`ChkDetailID`, `VoucherID`,`VoucherTypeID`,`Is_Opening_Balance`,`DepositGrp`,`Reconcile Date`, `Cheque Date` FROM `bankregister` where `LedgerID` = '.$ledgerID ; 
		if($dateType == 0)
		{			
			if($from <> "")
			{			
				$detailsquery .= ' AND `Date` >= "'.getDBFormatDate($from).'"';				
			}
			if($to <> "")
			{
				$detailsquery .= ' AND `Date` <= "'.getDBFormatDate($to).'"';	
			}
			
			if($from == "" && $to == "")
			{
				$detailsquery .= " AND `Date` BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";  
			}
		}
		else if($dateType == 2)
		{			
			$detailsquery .= ' AND `Reconcile Date` != 0';
			if($from <> "")
			{			
				$detailsquery .= ' AND `Reconcile Date` >= "'.getDBFormatDate($from).'"';				
			}
			if($to <> "")
			{
				$detailsquery .= ' AND `Reconcile Date` <= "'.getDBFormatDate($to).'"';	
			}
			if($from == "" && $to == "")
			{
				$detailsquery .= " AND `Reconcile Date` BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."' ";  
			}
		}
		else if($dateType == 1)
		{			
			$detailsquery .= ' AND `Cheque Date` != 0';
			if($from <> "")
			{			
				$detailsquery .= ' AND `Cheque Date` >= "'.getDBFormatDate($from).'"';				
			}
			if($to <> "")
			{
				$detailsquery .= ' AND `Cheque Date` <= "'.getDBFormatDate($to).'"';	
			}
			if($from == "" && $to == "")
			{
				$detailsquery .= " AND `Cheque Date` BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."' ";  
			}
		}
		if($voucherType <> "")
		{
			$detailsquery .= ' AND `VoucherTypeID` =  "'.$voucherType.'"';	
		}
		if($status <> "")
		{
			if($status == 1)
			{
				$detailsquery .= ' AND `Reconcile` = 1';	
			}
			elseif($status == 2)
			{
				$detailsquery .= ' AND `Reconcile` = 0';
			}
			else
			{
				$detailsquery .= ' AND `Return` = 1';
			}
		}		
		
		if($dateType == 0)
		{
			$detailsquery .= ' ORDER BY `Date` ';
		}
		else if($dateType == 1)
		{
			$detailsquery .= ' ORDER BY `Cheque Date` ';
		}
		else if($dateType == 2)
		{
			$detailsquery .= ' ORDER BY `Reconcile Date` ';
		}	
		$detailsquery .= ',`id`';	
		$result = $this->m_dbConn->select($detailsquery);				
					
		$paymentdtl_chqno = array();
		$paymentdtl_comments = array();
		$paymentdtl_ledgers = array();
		$paymentdtl_ledgerID = array();
		$paymentdtl_IsMultEntry = array();
		$paymentdtl_Ref = array();
		$sql = 'SELECT payment.id as paymentID, payment.ChequeNumber, payment.Comments, ledger.id, ledger.ledger_name, payment.IsMultipleEntry, payment.Reference FROM `paymentdetails` as `payment` JOIN `ledger` on ledger.id = payment.PaidTo'; // WHERE payment.PayerBank = '.$ledgerID;		 
		if($chequeNo <> "")
		{ 
			$sql .= ' AND ( payment.ChequeNumber LIKE "'.$chequeNo.'%" OR payment.Amount LIKE "'.$chequeNo.'%" )';
		}
		if($ledgerName <> "")
		{
			$sql .= ' AND ledger.ledger_name = "'.$ledgerName.'"';
		}							
		$res = $this->m_dbConn->select($sql);
		
		for($i = 0; $i < sizeof($res); $i++)
		{
			$paymentdtl_chqno[$res[$i]['paymentID']] = $res[$i]['ChequeNumber'];
			$paymentdtl_comments[$res[$i]['paymentID']] = $res[$i]['Comments'];
			$paymentdtl_ledgers[$res[$i]['paymentID']] = $res[$i]['ledger_name'];
			$paymentdtl_ledgerID[$res[$i]['paymentID']] = $res[$i]['id'];
			$paymentdtl_IsMultEntry[$res[$i]['paymentID']] = $res[$i]['IsMultipleEntry'];
			$paymentdtl_Ref[$res[$i]['paymentID']] = $res[$i]['Reference'];
		}		
		
		$chequeentrydtl_chqno = array();
		$chequeentrydtl_comments = array();
		$chequeentrydtl_ledgers = array();
		$chequeentrydtl_ledgerID = array();
		$sql = 'SELECT chqentrydtls.ID as chqentrydtlID, chqentrydtls.ChequeNumber, chqentrydtls.Comments, ledger.id, ledger.ledger_name FROM `chequeentrydetails` as `chqentrydtls` JOIN `ledger` ON ledger.id = chqentrydtls.PaidBy'; //WHERE chqentrydtls.BankID = '.$ledgerID;					
		if($chequeNo <> "")
		{			
			$sql .= ' AND ( chqentrydtls.ChequeNumber LIKE "'.$chequeNo.'%" OR chqentrydtls.Amount LIKE "'.$chequeNo.'%" )';
		}
		if($ledgerName <> "")
		{
			$sql .= ' AND ledger.ledger_name = "'.$ledgerName.'"';
		}		
		$res1 = $this->m_dbConn->select($sql);
			//echo "test 4";	
		for($i = 0; $i < sizeof($res1); $i++)
		{
			$chequeentrydtl_chqno[$res1[$i]['chqentrydtlID']] = $res1[$i]['ChequeNumber'];
			$chequeentrydtl_comments[$res1[$i]['chqentrydtlID']] = $res1[$i]['Comments'];
			$chequeentrydtl_ledgers[$res1[$i]['chqentrydtlID']] = $res1[$i]['ledger_name'];
			$chequeentrydtl_ledgerID[$res1[$i]['chqentrydtlID']] = $res1[$i]['id'];
		}				
		for($i = 0; $i < sizeof($result); $i++)
		{			
			$chequeDetails = array();
			$ledgers = array(); 	
			$tableQuery = "SELECT * FROM `voucher` WHERE `id` = '".$result[$i]['VoucherID']."'";	
			$res = $this->m_dbConn->select($tableQuery); 			
			if($result[$i]['ChkDetailID'] == 0)
			{					
				$chequeDetails[0]['ChequeNumber'] = '-';
				$chequeDetails[0]['Comments'] = '-';
				//$sql = 'SELECT `Note` FROM `voucher` where `id` = '.$result[$i]['VoucherID'];
				//$res = $this->m_dbConn->select($sql); 	
				$ledgers[0]['ledger_name'] = $res[0]['Note'];
			}							
			//else if($result[$i]['PaidAmount'] > 0)
			else if($res[0]['RefTableID'] == 3)
			{																		
				$chequeDetails[0]['ChequeNumber'] = $paymentdtl_chqno[$result[$i]['ChkDetailID']];			
				$chequeDetails[0]['Comments'] = $paymentdtl_comments[$result[$i]['ChkDetailID']];
				if(	$result[$i]['VoucherTypeID'] == 6)
				{
					$sqlLedger = "SELECT ledger_table.id,ledger_table.ledger_name FROM `paymentdetails` as `datatable` join `ledger` as `ledger_table` on ledger_table.id = datatable.PayerBank where datatable.id = ". $result[$i]['ChkDetailID'];
					$ledger = $this->m_dbConn->select($sqlLedger);
					$ledgers[0]['ledger_name'] = $ledger[0]['ledger_name'];
					$ledgers[0]['id'] = $ledger[0]['id'];
				}
				else
				{
					$ledgers[0]['ledger_name'] = $paymentdtl_ledgers[$result[$i]['ChkDetailID']];
					$ledgers[0]['id'] = $paymentdtl_ledgerID[$result[$i]['ChkDetailID']];
				}
				$chequeDetails[0]['IsMultEntry'] = $paymentdtl_IsMultEntry[$result[$i]['ChkDetailID']];	
				$chequeDetails[0]['Ref'] = $paymentdtl_Ref[	$result[$i]['ChkDetailID']];		
			}
			else if($res[0]['RefTableID'] == 2)
			{									
				$chequeDetails[0]['ChequeNumber'] = $chequeentrydtl_chqno[$result[$i]['ChkDetailID']];			
				$chequeDetails[0]['Comments'] = $chequeentrydtl_comments[$result[$i]['ChkDetailID']];				
				$ledgers[0]['ledger_name'] = $chequeentrydtl_ledgers[$result[$i]['ChkDetailID']];
				$ledgers[0]['id'] = $chequeentrydtl_ledgerID[$result[$i]['ChkDetailID']];
				$chequeDetails[0]['IsMultEntry'] = 0;
				$chequeDetails[0]['Ref'] = 0;
			}							 
			if($result[$i]['Is_Opening_Balance'] == 1 && ($ledgerName == "" || $ledgerName == "Opening Balance") && ($chequeNo == "" || $chequeDetails[0]['ChequeNumber'] == "-"))	
			{								
				if($chequeNo <> "")
				{										
					if(strpos( $result[$i]['ReceivedAmount'], $chequeNo) === 0)
					{						 
						$ledgers[0]['ledger_name'] = "Opening Balance";
					}																					
				}
				else
				{
					$ledgers[0]['ledger_name'] = "Opening Balance";
				}
			}
			
			if($ledgers[0]['ledger_name'] <> "" && $chequeDetails[0]['ChequeNumber'] <> "") //	
			{				
				if($this->prevCheque > 0 && $this->prevCheque == $chequeDetails[0]['Ref'])
				{					
					continue;
				}				
				
				$details = array();																																									
				$details['ID'] = $result[$i]['id'];
				$details['Date'] = $result[$i]['Date'];
				$details['PaidAmount'] = $result[$i]['PaidAmount'];
				$details['ReceivedAmount'] = $result[$i]['ReceivedAmount'];
				$details['ChkDetailID'] =  $result[$i]['ChkDetailID'];
				$details['VoucherID'] =  $result[$i]['VoucherID'];
				$details['VoucherTypeID'] =  $result[$i]['VoucherTypeID'];
				$details['Is_Opening_Balance'] =  $result[$i]['Is_Opening_Balance'];
				$details['ReconcileDate'] = $result[$i]['Reconcile Date'];
				$details['ChequeNumber'] = $chequeDetails[0]['ChequeNumber'];
				$details['Comment'] = $chequeDetails[0]['Comments'];
				$details['Cheque Date'] = $result[$i]['Cheque Date'];								
				$details['ledger_name'] = $ledgers[0]['ledger_name'];
				$details['DepositGrp'] = $result[$i]['DepositGrp'];
				$details['id'] = $ledgers[0]['id'];
				
				if($chequeDetails[0]['IsMultEntry'] == 1)
				{																				
					$this->prevCheque = $chequeDetails[0]['Ref'];
					$sqlQuery = "SELECT `id`,`Amount` FROM `paymentdetails` WHERE `Reference` = '".$chequeDetails[0]['Ref']."'";
					$amount = $this->m_dbConn->select($sqlQuery);
					$total = 0;
					$ledgerN = '';	
					for($k = 0; $k < sizeof($amount); $k++)
					{
						$total += $amount[$k]['Amount'];					
						$ledgerN .= $paymentdtl_ledgers[$amount[$k]['id']] . "<br />";	
						$ledgerIDForMult .= $paymentdtl_ledgerID[$amount[$k]['id']] . "<br />";					
					}
					$details['ledger_name'] = $ledgerN;
					$details['PaidAmount'] = $total;
				}				
				array_push($displayDetails, $details);																										
			}						
		}
		$sql23 = 'SELECT `id` as ID,`Date`,`PaidAmount`,`ReceivedAmount`,`ChkDetailID`, `VoucherID`,`VoucherTypeID`,`Is_Opening_Balance`,`DepositGrp`,`Reconcile Date`, `Cheque Date` FROM `bankregister` where `LedgerID` = "'.$ledgerID.'"  AND `Is_Opening_Balance` = 1 ';														
		$result23 = $this->m_dbConn->select($sql23);
		
		//converting array of array to single array		
		
		if($result23 <> "")
		{
			$flatten = array();
			array_walk_recursive($result23, function($value,$key) use(&$flatten) {
        		if($key == 'Date')
				{
					$value = $_SESSION['default_year_start_date'];			
				}
				$flatten[$key] = $value;
   		 	});
			
			//append opening balance array to start of $result array
			if(count($displayDetails) == 0)
			{
				array_push($displayDetails, $flatten);
			}
			else
			{
				array_unshift($displayDetails ,$flatten);
			}
		}	
		return $displayDetails;
	}
	
	function getReconcileStatus($bankRegID)
	{				
		$selectQuery = "SELECT `Reconcile`,`ReconcileStatus` FROM `bankregister` WHERE `id` = '".$bankRegID."' ";		
		$status = $this->m_dbConn->select($selectQuery);		
		return $status;
	}
	
	public function GetYearDesc($YearID)
	{
		$SqlVal = $this->m_dbConn->select("SELECT `YearDescription` FROM `year` where `YearID`=". $YearID);
		return $SqlVal[0]['YearDescription'];
	}
	
	public function GetMultEntryArray($PaidAmount, $ChkDetailID)
	{
		$multEntries = array();
		if($PaidAmount > 0)
		{
			$sql = 'SELECT * FROM `paymentdetails` WHERE `id` = "'.$ChkDetailID.'"';
			$res = $this->m_dbConn->select($sql);
			
			if($res[0]['IsMultipleEntry'] == 1)
			{				
				$sql1 = 'SELECT * FROM `paymentdetails` WHERE `ChequeNumber` = "'.$res[0]['ChequeNumber'].'"';	
				$multEntries = $this->m_dbConn->select($sql1);
			}								
		}
		return $multEntries;	
	}
	
	function getMemberName($ledgerID)
	{
		$legerSql="SELECT `ledger_name`,`categoryid`,`id` FROM `ledger`where id='".$ledgerID."'";
		$category = $this->m_dbConn->select($legerSql);
		if($category <> '')
		{
			for($i=0;$i<sizeof($category);$i++)
			{
				if($category[$i]['categoryid']==DUE_FROM_MEMBERS)
				{
					$selectQuery="SELECT mm.`owner_name`,mm.`unit`,u.unit_id,u.unit_no,l.id,l.ledger_name FROM `member_main` as mm join unit as u on u.unit_id=mm.unit join ledger as l on l.id=u.unit_id where l.id='".$category[$i]['id']."' and mm.`ownership_status`='1'";
					$memberName = $this->m_dbConn->select($selectQuery);
				}
			}
		}
		return $memberName;
		
	}
}
?> 