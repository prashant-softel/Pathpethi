<?php

include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once("voucher.class.php");
include_once("register.class.php");
include_once("dbconst.class.php");
include_once("latestcount.class.php");
include_once("utility.class.php");
include_once("changelog.class.php");
include_once("include/fetch_data.php");


class ChequeDetails extends dbop
{
	public $actionPage = "../ChequeDetails.php";
	public $m_dbConn;
	public $m_voucher;
	public $m_register;
	public $m_latestcount;
	public $m_objUtility;
	public $ADDEntryTracker;
	public $DELETEEntryTracker;
	public $EDITEntryTracker;
	public $actionType;
	public $m_objLog;
	public $m_objFetchDetails;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		$this->actionPage = "../ChequeDetails.php?depositid=".$_POST['DepositID']."&bankid=".$_POST['BankID'];

		/*//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
		//$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);*/
		$dbConnRoot = new dbop(true);
		$this->m_voucher = new voucher($dbConn);
		$this->m_latestcount = new latestCount($dbConn);
		$this->m_register = new regiser($dbConn);
		$this->m_objUtility = new utility($dbConn,$dbConnRoot);
		$_POST['EnteredBy'] = $_SESSION['login_id'];
		$this->m_objLog = new changeLog($dbConn);
		$this->m_objFetchDetails = new FetchData($dbConn);
	}

	public function startProcess()
	{
		$errorExists = 0;

		/*//$curdate 		=  $this->curdate;
		//$curdate_show	=  $this->curdate_show;
		//$curdate_time	=  $this->curdate_time;
		//$ip_location	=  $this->ip_location;*/
		$_POST['EnteredBy'] = $_SESSION['login_id'];
		$values = $_POST['VoucherDate'] ."|". $_POST['ChequeDate'] ."|". $_POST['ChequeNumber']."|". $_POST['Amount']."|". $_POST['PaidBy']."|". $_POST['BankID']."|". $_POST['PayerBank']."|". $_POST['PayerChequeBranch'];
		//echo "<script>alert('".$values."')<//script>";	
		if($_REQUEST['insert']=='Insert' && $errorExists==0)
		{
			//echo $_POST['VoucherDate'] ."|". $_POST['ChequeDate'] ."|". $_POST['ChequeNumber']."|". $_POST['Amount']."|". $_POST['PaidBy']."|". $_POST['BankID']."|". $_POST['TransactionTypeID']."|". $_POST['PayerBank']."|". $_POST['PayerChequeBranch'];
			if($_POST['VoucherDate']<>"" && $_POST['ChequeDate']<>"" && $_POST['ChequeNumber']<>"" && $_POST['Amount']<>"" && $_POST['PaidBy']<>"" && $_POST['BankID']<>"" && $_POST['PayerBank']<>"" && $_POST['PayerChequeBranch']<>"" && $_POST['DepositID']<>""  && $_POST['EnteredBy']<>"")
			{		
			
			$insert_query="insert into chequeentrydetails (`VoucherDate`,`ChequeDate`,`ChequeNumber`,`Amount`,`TDS_Amount`,`PaidBy`,`BankID`,`PayerBank`,`PayerChequeBranch`,`DepositID`,`EnteredBy`,`BillType`) values ('".getDBFormatDate($_POST['VoucherDate'])."','".getDBFormatDate($_POST['ChequeDate'])."','".$_POST['ChequeNumber']."','".$_POST['Amount']."','".$_POST['TDS_Amount']."','".$_POST['PaidBy']."','".$_POST['BankID']."','".$_POST['PayerBank']."','".$_POST['PayerChequeBranch']."','".$_POST['DepositID']."','".$_POST['EnteredBy']."','".$_POST['BillType']."')";
			$data = $this->m_dbConn->insert($insert_query);
			//echo "<script>alert('".$data."')<//script>";
			$dataVoucher  = $this->m_voucher->SetVoucherDetails(getDBFormatDate($_POST['VoucherDate']),$data, TABLE_CHEQUE_DETAILS, $this->m_latestcount->getLatestVoucherNo($_SESSION['society_id']),1,VOUCHER_RECEIPT,$_POST['PaidBy'],TRANSACTION_CREDIT,$_POST['Amount']);
			
			$bankregisterquery = "INSERT INTO bankregister(`LedgerID`, `VoucherID`, `VoucherTypeID`, `PaidAmount`, `ReceivedAmount`, `DepositGrp`, `ChkDetailID`) VALUES ('".$_POST['BankID']."','".$dataVoucher."','".VOUCHER_RECEIPT ."','','".$_POST['Amount']."',1,'".$data."')";
			//echo $bankregisterquery;
			//echo "<script>alert('".$bankregisterquery."')<//script>";
			$dataBankRegister = $this->m_dbConn->insert($bankregisterquery);
			
			$resVal = $this->m_register->SetAssetRegister(getDBFormatDate($_POST['VoucherDate']),$_POST['PaidBy'],$dataVoucher, VOUCHER_RECEIPT, TRANSACTION_CREDIT, $_POST['Amount'], 0);
			//echo "<script>alert('".$resVal."')<//script>";
			return "Insert";
			
			}
			
			else
			{
				return "All * Fields Required..";
			}

			
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			$up_query="update chequeentrydetails set `VoucherDate`='".getDBFormatDate($_POST['VoucherDate'])."',`ChequeDate`='".getDBFormatDate($_POST['ChequeDate'])."',`ChequeNumber`='".$_POST['ChequeNumber']."',`Amount`='".$_POST['Amount']."',`PaidBy`='".$_POST['PaidBy']."',`BankID`='".$_POST['BankID']."',`PayerBank`='".$_POST['PayerBank']."',`PayerChequeBranch`='".$_POST['PayerChequeBranch']."',`DepositID`='".$_POST["DepositID"]."',`EnteredBy`='".$_POST["EnteredBy"]."' where id='".$_POST['id']."'";
			$data = $this->m_dbConn->update($up_query);
			return "Update";
		}
		else
		{
			return $errString;
		}
	}
	
	public function AddNewValues2($DepositID,$PaidBy,$ChequeNumber,$VoucherDate, $ChequeDate,$ByArray,$ToArray, $ListOfAccounts, $VoucherCounter,$SystemVoucherNo,$IsCallUpdtCnt, $PayerAmount, $PayerBankID,$PayerBankName,$PayerChequeBranch,$Comments,$Vouchertype,$BillType = 0,$IsEditMode = false,$GUID)
	{
		
			///echo '<br>AddNewValues2 Function';
			$BankLists = array();
			$BankID = '';
			$ListOfCashLedger = $this->m_objUtility->GetBankLedger($_SESSION['default_cash_account']);
			$ListOfBankLedger = $this->m_objUtility->GetBankLedger($_SESSION['default_bank_account']);
			$BankLists = array_column($ListOfBankLedger, 'id');

			$ExCounterType = VOUCHER_RECEIPT;
			$CashLedgers = array();
			for($i = 0; $i< sizeof($ListOfCashLedger); $i++)
			{
				array_push($CashLedgers,$ListOfCashLedger[$i]['id']);
			}
			
			$IsSameCountChecked = $this->m_objUtility->IsSameCounterApply();
			if(isset($_SESSION['login_id']))
			{
				$sLoginID = $_SESSION['login_id'];
			}
			else
			{
				$sLoginID =  "-2";	//payTM api
			}

			if(isset($_SESSION['society_id']))
			{
				$sSocietyID = $_SESSION['society_id'];
			}
			if($sSocietyID == "")
			{
				$sqlSoc = "select society_id from society";
				$resSoc = $this->m_dbConn->select($sqlSoc);
				$sSocietyID = $resSoc[0]["society_id"];
				//echo "Society:".$sSocietyID;
				//die();
			}
			if(isset($_SESSION['default_due_from_member']))
			{
				$DuesFromMembers = $_SESSION['default_due_from_member'];
			}
			
			if($DuesFromMembers == "")
			{
				$sqlDuesFrmMem = "select APP_DEFAULT_DUE_FROM_MEMBERS from appdefault";
				$resDuesFrmMem = $this->m_dbConn->select($sqlDuesFrmMem);
				$DuesFromMembers = $resDuesFrmMem[0]["APP_DEFAULT_DUE_FROM_MEMBERS"];
				
				//$DuesFromMembers = "4";
				//echo "DuesFromMembers :".$DuesFromMembers;
				//die();
			}
			//$this->m_dbConn->begin_transaction();
			
			/*echo '<br>Deposit ID : '.$DepositID;
			var_dump($DepositID);*/
			
			if($DepositID == "-2") //NEFT
			{
				//$VoucherDate = $ChequeDate;  // VoucherDate should be as per set by admin
			}
				
			$arPaidByParentDetails = $this->m_objUtility->getParentOfLedger($PaidBy);
			/*echo '<br>Paid By : ';
			var_dump($PaidBy);
			echo '<br>Parent Ledger : ';
			var_dump($arPaidByParentDetails);*/
			
			//print_r($arPaidByParentDetails);
			if(!(empty($arPaidByParentDetails)))
			{
				$PaidByGroupID = $arPaidByParentDetails['group'];
				$PaidByCategoryID = $arPaidByParentDetails['category'];
				$PaidByName = $arPaidByParentDetails['ledger_name'];	
			}
			
			$insert_query="insert into chequeentrydetails (`VoucherDate`,`ChequeDate`,`ChequeNumber`,`Amount`,`PaidBy`,`BankID`,`PayerBank`,`PayerChequeBranch`,`DepositID`,`EnteredBy`,`Comments`,`isEnteredByMember`,`BillType`) values ('".getDBFormatDate($VoucherDate)."','".getDBFormatDate($ChequeDate)."','".$ChequeNumber."','".$PayerAmount."','".$PaidBy."','".$PayerBankID."','".$PayerBankName."','".$PayerChequeBranch."','".$DepositID."','".$sLoginID."','".$this->m_dbConn->escapeString($Comments)."',0,'".$BillType."')";
			
			$data = $this->m_dbConn->insert($insert_query);
			
			//$this->ADDEntryTracker ="New Record Added at($data)"; 
			//$this->EDITEntryTracker = "New Record for Edit Added at($data)"; 
			
			$LatestVoucherNo = $this->m_latestcount->getLatestVoucherNo($sSocietyID);
			/*
			echo '<br>Insert Cheque Query : '.$insert_query;
			echo '<br>Data : '.$data;
			echo '<br>Voucher Date : '.$VoucherDate;
			echo '<br>Latest Voucher Number : '.$LatestVoucherNo;
			echo '<br>To Array : ';
			var_dump($ToArray);
			echo '<br>Comments : '.$Comments;
			echo '<br>Voucher Counter : '.$VoucherCounter;
			*/
			$SrNo=0;	
			$AccountsArray = array_column($ListOfAccounts, 'id');
			//var_dump($AccountsArray);
			
			
			for($i=0;$i<sizeof($ByArray);$i++)
			{
				//echo '<br>By Head : '.$ByArray[$i]['Head'];
				$SrNo++;
				if(!empty($GUID))
				{
					$dataVoucher  = $this->m_voucher->SetVoucherDetails_WithGUID($VoucherDate,$data,TABLE_CHEQUE_DETAILS,$LatestVoucherNo,$SrNo,VOUCHER_RECEIPT, $ByArray[$i]['Head'],TRANSACTION_DEBIT,$ByArray[$i]['Amt'], $Comments,$VoucherCounter,$GUID);		
				}
				else
				{
					$dataVoucher  = $this->m_voucher->SetVoucherDetails($VoucherDate,$data,TABLE_CHEQUE_DETAILS,$LatestVoucherNo,$SrNo,VOUCHER_RECEIPT, $ByArray[$i]['Head'],TRANSACTION_DEBIT,$ByArray[$i]['Amt'], $Comments,$VoucherCounter);		
				}
				
				if($dataVoucher=='')
				{
					$rowerrormsg.='<br>Voucher Details(ToLedgers) Not Imported';
					//$this->m_objUtility->logGenerator($errorfile,'',$rowerrormsg,'E');
					return false;
				}
						
				
					//$date, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance
				$setregister=$this->m_register->SetRegister($ChequeDate,$ByArray[$i]['Head'],$dataVoucher,VOUCHER_RECEIPT,TRANSACTION_CREDIT,$ByArray[$i]['Amt'],0);
				
					
			}
			
			for($i=0;$i<sizeof($ToArray);$i++)
			{
				if(in_array($ByArray[$i]['Head'],$BankLists))
				{
					$BankID = $ByArray[$i]['Head'];
				}
				//echo '<br>To Array '.$ToArray;
				$SrNo++;
				if(!empty($GUID))
				{
					$dataVoucher1  = $this->m_voucher->SetVoucherDetails_WithGUID(getDBFormatDate($VoucherDate),$data,TABLE_CHEQUE_DETAILS,$LatestVoucherNo,$SrNo,VOUCHER_RECEIPT,$ToArray[$i]['Head'],TRANSACTION_CREDIT,$ToArray[$i]['Amt'], $Comments ,$VoucherCounter,$GUID);	
				}
				else
				{
					$dataVoucher1  = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),$data,TABLE_CHEQUE_DETAILS,$LatestVoucherNo,$SrNo,VOUCHER_RECEIPT,$ToArray[$i]['Head'],TRANSACTION_CREDIT,$ToArray[$i]['Amt'], $Comments ,$VoucherCounter);	
				}
				
				
				if($dataVoucher1=='')
				{
					
					$rowerrormsg.='<br>Voucher Details(By Ledgers) Not Imported';
					return false;
				}
				if(in_array($ToArray[$i]['Head'],$AccountsArray))
				{
					//echo $ToArray[$i]['Head'];
					//echo '<br>In Bank Register';
					$bankregQuery = $this->m_register->SetBankRegister(getDBFormatDate($VoucherDate), $ToArray[$i]['Head'], $dataVoucher1, VOUCHER_RECEIPT, TRANSACTION_RECEIVED_AMOUNT, $ToArray[$i]['Amt'], $DepositID, $data, 0, getDBFormatDate($ChequeDate), 0, getDBFormatDate($reconcileDate), $reconcileStatus, $reconcile, $return);				
				}
				else
				{
				$setregister=$this->m_register->SetRegister($ChequeDate,$ToArray[$i]['Head'],$dataVoucher1,VOUCHER_RECEIPT,TRANSACTION_DEBIT,$ToArray[$i]['Amt'],0);
				}
				
				
			}
			
					
					
			/*
			for($i=0;$i<sizeof($ToArray);$i++)
			{
				if(in_array($ByArray[$i]['Head'],$BankLists))
				{
					$BankID = $ByArray[$i]['Head'];
				}
				$SrNo++;
				$dataVoucher  = $this->m_voucher->SetVoucherDetails($VoucherDate,$data,TABLE_CHEQUE_DETAILS,$LatestVoucherNo,$SrNo,VOUCHER_RECEIPT, $ToArray[$i]['Head'],TRANSACTION_CREDIT,$ToArray[$i]['Amt'], $Comments,$VoucherCounter);	
				
				
				if($dataVoucher=='')
				{
					$rowerrormsg.='<br>Voucher Details(ToLedgers) Not Imported';
					//$this->m_objUtility->logGenerator($errorfile,'',$rowerrormsg,'E');
					return false;
				}
				
				if(in_array($ToArray[$i]['Head'],$AccountsArray))
				{
					//echo '<br>In Bank Register';
					$bankregQuery = $this->m_register->SetBankRegister(getDBFormatDate($VoucherDate), $ToArray[$i]['Head'], $dataVoucher, VOUCHER_RECEIPT, TRANSACTION_RECEIVED_AMOUNT, $ToArray[$i]['Amt'], $DepositID, $data, 0, getDBFormatDate($ChequeDate), 0, getDBFormatDate($reconcileDate), $reconcileStatus, $reconcile, $return);				
				}
				else
				{
					//$date, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance
					$setregister=$this->m_register->SetRegister($ChequeDate,$ToArray[$i]['Head'],$dataVoucher,VOUCHER_RECEIPT,TRANSACTION_DEBIT,$ToArray[$i]['Amt'],0);
				}
			}
					
					
			for($i=0;$i<sizeof($ByArray);$i++)
			{
				$SrNo++;
				$dataVoucher1  = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),$data,TABLE_CHEQUE_DETAILS,$LatestVoucherNo,$SrNo,VOUCHER_RECEIPT,$ByArray[$i]['Head'],TRANSACTION_DEBIT,$ByArray[$i]['Amt'], $Comments ,$VoucherCounter);
				
				if($dataVoucher1=='')
				{
					
					$rowerrormsg.='<br>Voucher Details(By Ledgers) Not Imported';
					return false;
				}
				
				$setregister=$this->m_register->SetRegister($ChequeDate,$ByArray[$i]['Head'],$dataVoucher1,VOUCHER_RECEIPT,TRANSACTION_CREDIT,$ByArray[$i]['Amt'],0);
				
			}
			 */
			
			if($IsCallUpdtCnt == 1)
			{
				if($IsSameCountChecked == 1)
				{
					if(in_array($PayerBankID,$CashLedgers))
					{
						$this->m_objUtility->UpdateExVCounter($ExCounterType, $VoucherCounter, $PayerBankID);
					}
					else
					{
						$this->m_objUtility->UpdateExVCounter($ExCounterType, $VoucherCounter, 0);
					}
				}
				else
				{
					$this->m_objUtility->UpdateExVCounter($ExCounterType, $VoucherCounter, $PayerBankID);	
				}
					
			}
		
		if($IsEditMode == true)
		{
			return $LatestVoucherNo;
		}
		else
		{
			return 'Import Successful';		
		}		
		
	}


	public function AddNewValuesWithTDS($VoucherDate, $ChequeDate, $ChequeNo, $ExVoucherCounter,$systemVoucherNo,$IsCallUpdtCnt, $Amount, $TDS_Amount, $PaidBy, $BankID, $PayerBank, $PayerBranch, $DepositID, $Comments,$BillType, $reconcileDate = 0, $reconcileStatus = 0, $reconcile = 0, $return = 0)
	{
		echo "INside AddNewValuesWithTDS" . $TDS_Amount;
			$this->AddNewValues($VoucherDate, $ChequeDate, $ChequeNo, $ExVoucherCounter,$systemVoucherNo,$IsCallUpdtCnt, $Amount, $PaidBy, $BankID, $PayerBank, $PayerBranch, $DepositID, $Comments,$BillType, $reconcileDate, $reconcileStatus, $reconcile, $return, 0, false,"", $TDS_Amount);
	}
	
	public function AddNewValues($VoucherDate, $ChequeDate, $ChequeNo, $ExVoucherCounter,$systemVoucherNo,$IsCallUpdtCnt, $Amount, $PaidBy, $BankID, $PayerBank, $PayerBranch, $DepositID, $Comments,$BillType, $reconcileDate = 0, $reconcileStatus = 0, $reconcile = 0, $return = 0, $EnteredByMember = 0,$isFunCalledFrmImport = false, $GatewayID = "", $TDS_Amount=0)
	{
		try
		{
			//***Below code is just to verify it is a cash entry or bank entry according to that send Ledger to update counter it only applicable when same bank checked
			$ListOfCashLedger = $this->m_objUtility->GetBankLedger($_SESSION['default_cash_account']);
			$ExCounterType = VOUCHER_RECEIPT;
			$CashLedgers = array();
			for($i = 0; $i< sizeof($ListOfCashLedger); $i++)
			{
				array_push($CashLedgers,$ListOfCashLedger[$i]['id']);
			}
			
			$IsSameCountChecked = $this->m_objUtility->IsSameCounterApply();
			if(isset($_SESSION['login_id']))
			{
				$sLoginID = $_SESSION['login_id'];
			}
			else
			{
				$sLoginID =  "-2";	//payTM api
			}

			if(isset($_SESSION['society_id']))
			{
				$sSocietyID = $_SESSION['society_id'];
			}
			if($sSocietyID == "")
			{
				$sqlSoc = "select society_id from society";
				$resSoc = $this->m_dbConn->select($sqlSoc);
				$sSocietyID = $resSoc[0]["society_id"];
				$_SESSION['society_id'] = $sSocietyID;
			}
			if(isset($_SESSION['default_due_from_member']))
			{
				$DuesFromMembers = $_SESSION['default_due_from_member'];
			}
			if($DuesFromMembers == "")
			{
				$sqlDuesFrmMem = "select APP_DEFAULT_DUE_FROM_MEMBERS from appdefault";
				$resDuesFrmMem = $this->m_dbConn->select($sqlDuesFrmMem);
				$DuesFromMembers = $resDuesFrmMem[0]["APP_DEFAULT_DUE_FROM_MEMBERS"];
				$_SESSION['default_due_from_member'] = $DuesFromMembers;
				//$DuesFromMembers = "4";
				//echo "DuesFromMembers :".$DuesFromMembers;
				//die();
			}
			$this->m_dbConn->begin_transaction();
			
			if($DepositID == "-2") //NEFT
			{
				//$VoucherDate = $ChequeDate;  // VoucherDate should be as per set by admin
			}
			if($TDS_Amount <> 0)
			{
				$TDS_Receivable = $_SESSION['default_tds_receivable'];
			}
			$arPaidByParentDetails = $this->m_objUtility->getParentOfLedger($PaidBy);
			//print_r($arPaidByParentDetails);
			if(!(empty($arPaidByParentDetails)))
			{
				$PaidByGroupID = $arPaidByParentDetails['group'];
				$PaidByCategoryID = $arPaidByParentDetails['category'];
				$PaidByName = $arPaidByParentDetails['ledger_name'];	
			}
			//echo '<br>Before chequeentry Insert';
			$insert_query="insert into chequeentrydetails (`VoucherDate`,`ChequeDate`,`ChequeNumber`,`Amount`,`TDS_Amount`,`PaidBy`,`BankID`,`PayerBank`,`PayerChequeBranch`,`DepositID`,`EnteredBy`,`Comments`,`isEnteredByMember`,`BillType`) values ('".getDBFormatDate($VoucherDate)."','".getDBFormatDate($ChequeDate)."','".$ChequeNo."','".$Amount."','".$TDS_Amount."','".$PaidBy."','".$BankID."','".$this->m_dbConn->escapeString($PayerBank)."','".$this->m_dbConn->escapeString($PayerBranch)."','".$DepositID."','".$sLoginID."','".$this->m_dbConn->escapeString($Comments)."','".$EnteredByMember."','".$BillType."')";
			$data = $this->m_dbConn->insert($insert_query);
			$this->ADDEntryTracker ="New Record Added at($data)"; 
			$this->EDITEntryTracker = "New Record for Edit Added at($data)"; 
			
			//echo '<br> After Insert';
			$LatestVoucherNo = $this->m_latestcount->getLatestVoucherNo($sSocietyID);
			if($PaidByCategoryID == CASH_ACCOUNT || $PaidByCategoryID == BANK_ACCOUNT)
			{
				$dataVoucher  = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),$data, TABLE_CHEQUE_DETAILS, 
				$LatestVoucherNo,1,VOUCHER_CONTRA,$PaidBy,TRANSACTION_DEBIT,$Amount+$TDS_Amount,'', $ExVoucherCounter);
			}
			else
			{
				//This should be other way
				$dataVoucher  = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),$data, TABLE_CHEQUE_DETAILS, 
				$LatestVoucherNo,1,VOUCHER_RECEIPT,$PaidBy,TRANSACTION_DEBIT,$Amount+$TDS_Amount, '', $ExVoucherCounter);

			}
			//echo '<br> Test 1';
			//To Credit section
			if($PaidByCategoryID == CASH_ACCOUNT || $PaidByCategoryID == BANK_ACCOUNT)
			{
				$bankregQuery = $this->m_register->SetBankRegister(getDBFormatDate($VoucherDate), $PaidBy, $dataVoucher, VOUCHER_CONTRA, TRANSACTION_PAID_AMOUNT, $Amount+$TDS_Amount, $DepositID, $data, 0, getDBFormatDate($ChequeDate), 0, getDBFormatDate($reconcileDate), $reconcileStatus, $reconcile, $return);
			}			
			else if($PaidByGroupID==LIABILITY)
			{
			$SetLiabilityRegister = $this->m_register->SetLiabilityRegister(getDBFormatDate($VoucherDate),$PaidBy,$dataVoucher, VOUCHER_RECEIPT, TRANSACTION_CREDIT, $Amount+$TDS_Amount, 0);
			}
			else if($PaidByGroupID==ASSET)
			{
			$SetAssetRegister = $this->m_register->SetAssetRegister(getDBFormatDate($VoucherDate),$PaidBy,$dataVoucher, VOUCHER_RECEIPT, TRANSACTION_CREDIT, $Amount+$TDS_Amount, 0);
			}
			else if($PaidByGroupID==INCOME)
			{
			$SetIncomeRegister = $this->m_register->SetIncomeRegister($PaidBy,getDBFormatDate($VoucherDate),$dataVoucher, VOUCHER_RECEIPT, TRANSACTION_CREDIT, $Amount+$TDS_Amount);
			}
			else if($PaidByGroupID==EXPENSE)
			{
				$SetExpenseRegister = $this->m_register->SetExpenseRegister($PaidBy,getDBFormatDate($VoucherDate),$dataVoucher, VOUCHER_RECEIPT, TRANSACTION_CREDIT, $Amount+$TDS_Amount,0);
			}
			//echo '<br> Test 2';
			//By Debit side Update Voucher
			if($PaidByCategoryID == CASH_ACCOUNT || $PaidByCategoryID == BANK_ACCOUNT)
			{
			$dataVoucher  = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate), $data, TABLE_CHEQUE_DETAILS, $LatestVoucherNo, 2, VOUCHER_CONTRA, $BankID, TRANSACTION_CREDIT, $Amount+ $TDS_Amount,'',$ExVoucherCounter);
			}
			else
			{
				//pending : This needs to be otherway?? This should be Debit Transaction
			$dataVoucher  = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate), $data, TABLE_CHEQUE_DETAILS, $LatestVoucherNo, 2, VOUCHER_RECEIPT, $BankID, TRANSACTION_CREDIT, $Amount, '', $ExVoucherCounter);
			
				if($TDS_Amount > 0)
				{
					$TDSDataVoucher = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate), $data, TABLE_CHEQUE_DETAILS, $LatestVoucherNo, 3, VOUCHER_RECEIPT, $TDS_Receivable, TRANSACTION_CREDIT, $TDS_Amount, '', $ExVoucherCounter);
				}
			}
			//echo '<br> Test 3';
			if($PaidByCategoryID == CASH_ACCOUNT || $PaidByCategoryID == BANK_ACCOUNT)
			{
				$bankregisterquery = $this->m_register->SetBankRegister(getDBFormatDate($VoucherDate),$BankID, $dataVoucher,VOUCHER_CONTRA, TRANSACTION_RECEIVED_AMOUNT, $Amount, $DepositID, $data, 0, getDBFormatDate($ChequeDate), 0, getDBFormatDate($reconcileDate), $reconcileStatus, $reconcile, $return);
			}
			else
			{
				$bankregisterquery = $this->m_register->SetBankRegister(getDBFormatDate($VoucherDate),$BankID, $dataVoucher,VOUCHER_RECEIPT, TRANSACTION_RECEIVED_AMOUNT, $Amount, $DepositID, $data, 0, getDBFormatDate($ChequeDate), 0, getDBFormatDate($reconcileDate), $reconcileStatus, $reconcile, $return);
				if($TDS_Amount > 0)
				{
					$SetTDSAssetRegister = $this->m_register->SetAssetRegister(getDBFormatDate($VoucherDate),$TDS_Receivable,$dataVoucher, VOUCHER_RECEIPT, TRANSACTION_DEBIT, $TDS_Amount, 0);
					
				}
			}
			//echo '<br> Test 4';
			if($IsCallUpdtCnt == 1)
			{
				if($IsSameCountChecked == 1)
				{
					if(in_array($BankID,$CashLedgers))
					{
						$this->m_objUtility->UpdateExVCounter($ExCounterType, $ExVoucherCounter, $BankID);
					}
					else
					{
						$this->m_objUtility->UpdateExVCounter($ExCounterType, $ExVoucherCounter, 0);
					}
				}
				else
				{
					$this->m_objUtility->UpdateExVCounter($ExCounterType, $ExVoucherCounter, $BankID);	
				}
					
			}
			//echo '<br> Test 5';
			//echo '<br> Paid By '.$PaidBy;
			$ledgertype="select * from `ledger` where receipt='1' and categoryid='".$DuesFromMembers."' and society_id=".$sSocietyID." and `id`=".$PaidBy." ";
			$IsLedgerUnit=$this->m_dbConn->select($ledgertype);
			//echo "IsLedgerUnit";
			//echo '<br> Test 6';
			//print_r($IsLedgerUnit);
			//echo '<br> Before Is Ledger Unit';
			if($IsLedgerUnit <> " ")
			{
				$unitUpdateQuery = 'UPDATE `unit` SET `Payer_Bank`="'.$PayerBank.'",`Payer_Cheque_Branch`="'.$PayerBranch.'" WHERE `unit_id` = '.$PaidBy;	
				$this->m_dbConn->update($unitUpdateQuery);		
			}
			//echo '<br>Exit';
			$this->m_dbConn->commit();
			if($this->actionType == ADD)
			{
				//echo "add type";
				if($systemVoucherNo <> $ExVoucherCounter)
				{
					$this->ADDEntryTracker .= " :: Voucher Number ".$ExVoucherCounter." Changed  to ".$systemVoucherNo. " on insert";
				}
				$this->m_objLog->setLog($this->ADDEntryTracker, $sLoginID, 'ChequeEntryDetails', $data);
			}
			if($this->actionType == EDIT)
			{
				$this->EDITEntryTracker .= "<br> VoucherDate | ChequeDate | ChequeNumber | Amount | PaidBy | BankID | PayerBank | PayerChequeBranch | DepositID | EnteredBy | Comments | BillType<br>";
				$this->EDITEntryTracker .= $VoucherDate ."|". $ChequeDate ."|". $ChequeNo ."|". $Amount ."|". $PaidBy."|". $BankID . "|" . $PayerBank."|".$PayerBranch."|".$DepositID . "|".$sLoginID. "|" .$Comments. "|" .$BillType;
				
				if($systemVoucherNo <> $ExVoucherCounter)
				{
					$this->EDITEntryTracker .= " :: Voucher Number ".$ExVoucherCounter." Changed  to ".$systemVoucherNo. " on Update";
				}
				
				$this->m_objLog->setLog($this->EDITEntryTracker, $sLoginID, 'ChequeEntryDetails', $data);
			}
			
			
			//if($isFunCalledFrmImport ==false && $DepositID == -2 && $PaidByGroupID == ASSET && $PaidByCategoryID == DUE_FROM_MEMBERS)
			if($isFunCalledFrmImport ==false && $PaidByGroupID == ASSET && $PaidByCategoryID == DUE_FROM_MEMBERS)
			{
				//neft payment entry for member hence send email
				$PaymentFor = "Regular Maintenance Bill";
				$SocietyAccountName = $this->m_objUtility->getLedgerName($BankID);
				$sTrnxStatus = "";
				
				if($BillType == 1)
				{
					$PaymentFor = "Supplementary Maintenance Bill";
				}
				
				if($DepositID == DEPOSIT_CASH)
				{
					$TransactionData['ModeOfReceipt'] = "Cash";
				}
				else if($DepositID == DEPOSIT_NEFT)
				{
						$TransactionData['ModeOfReceipt'] = "NEFT";
						//echo "neft";
				}
				else if($DepositID == DEPOSIT_ONLINE)
				{
					$TransactionData['ModeOfReceipt'] = "Online";
					$sTrnxStatus = $this->m_objUtility->GetPaymentGatewayTransactionStatus($ChequeNo);
					//echo "online";
				}
				else if($DepositID == "3")
				{
					$TransactionData['ModeOfReceipt'] = "Cheque";
				}
				//echo "test".$TransactionData['ModeOfReceipt'];
				$TransactionData['Date'] = $ChequeDate;
				$TransactionData['SocietyAccountName'] = $SocietyAccountName;
				$TransactionData['PaidBy'] = $PaidBy;
				$TransactionData['PaidByName'] = $PaidByName;
				$TransactionData['BankName'] = $PayerBank;
				$TransactionData['BranchName'] = $PayerBranch;
				$TransactionData['TransationNo'] = $ChequeNo;
				$TransactionData['Amount'] = $Amount;
				$TransactionData['Comments'] = $Comments;
				$TransactionData['BillType'] = $PaymentFor;
				$TransactionData['Status'] = $sTrnxStatus;
				
				$validator  = $this->sendNeftNotificationByEmail($TransactionData, $GatewayID,$sSocietyID);
			}
			return "Insert";
		}
		catch(Exception $exp)
		{
			$this->m_dbConn->rollback();
			return $exp;
		}
	}
	
	
	public function DeletePreviousRecord($PaidBy, $PayerBank, $PayerChequeBranch,$ChequeDetailsId)
	{
		try
		{
			$this->m_dbConn->begin_transaction();
			$VoucherIDArray = array();
			
			$delete_query = "delete from `chequeentrydetails` where `ID` = ".$ChequeDetailsId." ";
			$data1 = $this->m_dbConn->delete($delete_query);
			$this->DELETEEntryTracker = "Record Deleted :";
			$this->EDITEntryTracker = "\r\nPrev Record :";												
			
			$voucherquery1="select `id` from  `voucher` where `RefNo`=".$ChequeDetailsId." and `RefTableID`=".TABLE_CHEQUE_DETAILS."";
			$data2 = $this->m_dbConn->select($voucherquery1);
			
			if( sizeof($data2) > 0)
			{
				for($i = 0; $i < sizeof($data2); $i++)
				{
					if($data2[$i]['id'] == "" || $data2[$i]['id'] == 0)
					{
						array_push($VoucherIDArray,-1);
					}
					else
					{
						array_push($VoucherIDArray,$data2[$i]['id']);
					}
				}
			}
			
			$regDelete1 = '';
			$regDelete2 = '';
			$regDelete3 = '';
			$regDelete4 = '';
			$regDelete5 = '';
			$voucherquery2 = '';
			
			for($i = 0; $i < sizeof($VoucherIDArray); $i++)
			{
				if($VoucherIDArray[$i] <> "" &&  $VoucherIDArray[$i] > 0)
				{
					//$regDelete1="delete from `liabilityregister` where `VoucherID` IN(".$VoucherIDArray[0].",".$VoucherIDArray[1].")";
					
					if($regDelete1 == "")
					{
						$regDelete1 = "delete from `liabilityregister` where `Is_Opening_Balance` = 0 AND `VoucherID` = '".$VoucherIDArray[$i]."'  ";
					}
					else if($regDelete1 <> "")
					{
						$regDelete1 .= " OR `VoucherID` = '".$VoucherIDArray[$i]."'   ";
					}
					
					
					//$regDelete2=delete from `assetregister` where `VoucherID` IN(".$VoucherIDArray[0].",".$VoucherIDArray[1].")";
					
					if($regDelete2 == "")
					{
						$regDelete2 = "delete from `assetregister` where `Is_Opening_Balance` = 0 AND  `VoucherID` = '".$VoucherIDArray[$i]."'  ";
					}
					else if($regDelete2 <> "")
					{
						$regDelete2 .= " OR `VoucherID` = '".$VoucherIDArray[$i]."'   ";
					}
					
					
					//$regDelete3="delete from `incomeregister` where `VoucherID` IN(".$VoucherIDArray[0].",".$VoucherIDArray[1].")";
					
					if($regDelete3 == "")
					{
						$regDelete3 = "delete from `incomeregister` where `VoucherID` = '".$VoucherIDArray[$i]."'  ";
					}
					else if($regDelete3 <> "")
					{
						$regDelete3 .= " OR `VoucherID` = '".$VoucherIDArray[$i]."'   ";
					}
					
					
					//$regDelete4="delete from `expenseregister` where `VoucherID` IN(".$VoucherIDArray[0].",".$VoucherIDArray[1].")";
					
					if($regDelete4 == "")
					{
						$regDelete4 = "delete from `expenseregister` where `VoucherID` = '".$VoucherIDArray[$i]."'  ";
					}
					else if($regDelete4 <> "")
					{
						$regDelete4 .= " OR `VoucherID` = '".$VoucherIDArray[$i]."'   ";
					}
					
					
					//$regDelete5="delete from `bankregister` where `VoucherID` IN(".$VoucherIDArray[0].",".$VoucherIDArray[1].")";
					
					if($regDelete5 == "")
					{
						$regDelete5 = "delete from `bankregister` where `Is_Opening_Balance` = 0 AND `VoucherID` = '".$VoucherIDArray[$i]."'  ";
					}
					else if($regDelete5 <> "")
					{
						$regDelete5 .= " OR `VoucherID` = '".$VoucherIDArray[$i]."'   ";
					}
					
					//$voucherquery2="DELETE FROM  `voucher` where `id` IN(".$VoucherIDArray[0].",".$VoucherIDArray[1].")";
					
					if($voucherquery2 == "")
					{
						$voucherquery2 = "DELETE FROM  `voucher` where `id` = '".$VoucherIDArray[$i]."'  ";
					}
					else if($voucherquery2 <> "")
					{
						$voucherquery2 .= " OR `id` = '".$VoucherIDArray[$i]."'   ";
					}
				}
			}
			
			if($regDelete1 <> "")
			{
				$regResult1 = $this->m_dbConn->delete($regDelete1);
			}
			if($regDelete2 <> "")
			{
				$regResult2 = $this->m_dbConn->delete($regDelete2);
			}
			if($regDelete3 <> "")
			{
				$regResult3 = $this->m_dbConn->delete($regDelete3);
			}
			if($regDelete4 <> "")
			{
				$regResult4 = $this->m_dbConn->delete($regDelete4);
			}
			if($regDelete5 <> "")
			{
				$regResult5 = $this->m_dbConn->delete($regDelete5);
			}
			if($voucherquery2 <> "")
			{
				$data3 = $this->m_dbConn->delete($voucherquery2);
			}
			/*$regDelete1=$this->m_dbConn->delete("delete from `liabilityregister` where `VoucherID` IN(".$VoucherIDArray[0].",".$VoucherIDArray[1].")");
			$regDelete2=$this->m_dbConn->delete("delete from `assetregister` where `VoucherID` IN(".$VoucherIDArray[0].",".$VoucherIDArray[1].")");
			$regDelete3=$this->m_dbConn->delete("delete from `incomeregister` where `VoucherID` IN(".$VoucherIDArray[0].",".$VoucherIDArray[1].")");
			$regDelete4=$this->m_dbConn->delete("delete from `expenseregister` where `VoucherID` IN(".$VoucherIDArray[0].",".$VoucherIDArray[1].")");
			$bankregisterquery = $this->m_dbConn->delete("delete from `bankregister` where `VoucherID` IN(".$VoucherIDArray[0].",".$VoucherIDArray[1].")");*/
			
			$this->m_dbConn->commit();	
			if($this->actionType == DELETE)
			{
				$this->DELETEEntryTracker .= "<br> PaidBy | PayerBank | PayerChequeBranch | ChequeDetailsID <br>";
				$this->DELETEEntryTracker .= $PaidBy."|".$PayerBank."|".$PayerChequeBranch."|".$ChequeDetailsId;
				$this->m_objLog->setLog($this->DELETEEntryTracker, $_SESSION['login_id'], 'ChequeEntryDetails',$ChequeDetailsId);
			}
			if($this->actionType == EDIT)
			{				
				$this->EDITEntryTracker .= "<br> PaidBy | PayerBank | PayerChequeBranch | ChequeDetailsID <br>";
				$this->EDITEntryTracker .= $PaidBy."|".$PayerBank."|".$PayerChequeBranch."|".$ChequeDetailsId;
				$this->EDITEntryTracker .="\r\nChequeEntry record deleted successfully.";
				$this->m_objLog->setLog($this->EDITEntryTracker, $_SESSION['login_id'], "chequeEntryDetails", $ChequeDetailsId);
			}
			return "Update";
		}
		catch(Exception $exp)
		{
			$this->m_dbConn->rollback();
			return $exp;
		}
	}
	
	public function combobox($query, $AddExernalValue = "")
	{
		$str.='<option value="">Please Select</option>';
		
		if($AddExernalValue <> "")
		{
			$str = $AddExernalValue;
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
							$str.='<OPTION VALUE='.$v.'>';
							$str.= $v.'</OPTION>';
						}
						$i++;
						
					}
				}
			}
			//print_r( $str);
			//echo "<script>alert('test')<//script>";
			return $str;
	}
	
	public function comboboxEx($query)
	{
		//echo 'Query '.$query;
		$id=0;
		//echo "<script>alert('test')<//script>";
		$str.="<option value=''>Please Select</option>";
	$data = $this->m_dbConn->select($query);
	//echo "<script>alert('test2')<//script>";
		if(!is_null($data))
		{
			$vowels = array('/', '*', '%', '&', ',', '(', ')', '"');
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
						//$str.=$v."</OPTION>";
						$str.= str_replace($vowels, ' ', $v)."</OPTION>";
					}
					//echo "<script>alert('".$str."')<//script>";
					$i++;
				}
			}
		}
		//return $str;
		//print_r( $str);
		//echo "<script>alert('test')<//script>";
		return $str;
	}
	public function display1($rsas)
	{
		$thheader = array('VoucherDate','ChequeDate','ChequeNumber','Amount','TDS','PaidBy','Deposited in Bank','PayerBank','PayerChequeBranch','Deposit Slip','EnteredBy','Comments');
		$this->display_pg->edit		= "getChequeDetails";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "ChequeDetails.php";

		$res = $this->display_pg->display_new($rsas, false);
		return $res;
	}
	public function pgnation($DepositID,$IsReadonlyPage = false)
	{
		//$sql1 = "select id,`VoucherDate`,`ChequeDate`,`ChequeNumber`,`Amount`,`PaidBy`,`BankID`,`PayerBank`,`PayerChequeBranch`,`DepositID` from chequeentrydetails where status='Y'";
		//echo "id";
		//echo $DepositID;
		if($DepositID == -3)
		{
			 $sql1 = "select chq.id, if( billtype=0 , 'Regular', if(billtype=1,'Supp',if(billtype=2,'Invoice','Unknown'))) as BillType,DATE_FORMAT(chq.VoucherDate, '%d-%m-%Y'), DATE_FORMAT(chq.ChequeDate, '%d-%m-%Y'),vcr.ExternalCounter as VoucherNumber,chq.Amount, chq.TDS_Amount, led.ledger_name,chq.PayerBank, chq.Comments from chequeentrydetails as chq JOIN ledger as led on chq.PaidBy = led.id JOIN voucher as vcr ON vcr.RefNo = chq.id  where chq.DepositID='-3' and vcr.To = '".$_REQUEST['bankid']."' and chq.BankID= '".$_REQUEST["bankid"]."'and chq.status='Y' ";	
			
			$thheader = array('Bill Type','VoucherDate','ChequeDate','VoucherNumber','Amount', 'TDS Cut', 'PaidBy','PayerBank','Comments');
		}
		else
		{
		$sql1 = "select chq.id,if( billtype=0 , 'Regular', if(billtype=1,'Supp',if(billtype=2,'Invoice','Unknown')))   as BillType, DATE_FORMAT(chq.VoucherDate, '%d-%m-%Y'), DATE_FORMAT(chq.ChequeDate, '%d-%m-%Y'), chq.ChequeNumber, vcr.ExternalCounter as VoucherNumber, chq.Amount, chq.TDS_Amount, led.ledger_name,chq.PayerBank,chq.PayerChequeBranch,dep.desc, chq.Comments from chequeentrydetails as chq JOIN  ledger as led on chq.PaidBy = led.id JOIN voucher as vcr ON vcr.RefNo = chq.id JOIN depositgroup as dep on chq.DepositID = dep.id where vcr.To = '".$_REQUEST['bankid']."' and chq.DepositID='".$DepositID."' and chq.status='Y' ";
		
			$thheader = array(' Bill Type','VoucherDate','ChequeDate','ChequeNumber','VoucherNumber','Amount', 'TDS Cut','PaidBy','PayerBank','PayerChequeBranch','Deposit Slip','Comments');
		}
		
		if(isset($_REQUEST["edt"]) && $_REQUEST["edt"] <> "")
		{
			$sql1 .= "  and chq.id = '".$_REQUEST["edt"]."'";	
		}
		if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
		{
			$sql1 .= "  and chq.VoucherDate BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
		}
		
		
		$sql1 .= "   ORDER BY chq.VoucherDate DESC";
		/*$cntr = "select count(status) as cnt from chequeentrydetails where status='Y'";
		
		$this->display_pg->sql1		= $sql1;
		$this->display_pg->cntr1	= $cntr;
		$this->display_pg->mainpg	= "ChequeDetails.php";

		$limit	= "50";
		$page	= $_REQUEST['page'];
		$extra	= "";

		$res	= $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;*/
		
		$result = $this->m_dbConn->select($sql1);
		$this->display_pg->edit		= "getChequeDetails";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "ChequeDetails.php";
		$this->display_pg->print		= "getChequeDetails";
		
		if($IsReadonlyPage == false)
		{
			$res = $this->display_pg->display_datatable($result, true, true,false,true,true);
		}
		else
		{
				$res = $this->display_pg->display_datatable($result, true, true,false,true,false);
		}
	}
	
	public function display1_neft($rsas,$IsReadonlyPage = false)
	{
		$thheader = array('EnteredBy [Icon - Member]','Bill Type','VoucherDate','VoucherNumber','Transaction Date','Transaction No','Amount','PaidBy','Payer Bank','Payer Branch','Comments');
		//$this->display_pg->edit		= "getNeftDetails";
		$this->display_pg->edit		= "getChequeDetails";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "NeftDetails.php";
		$this->display_pg->print 	= "getChequeDetails";
		//$res = $this->display_pg->display_new($rsas, false);
		if($IsReadonlyPage == false)
		{
			$res = $this->display_pg->display_datatable($rsas, true, true,false,false,true);
		}
		else
		{
			$res = $this->display_pg->display_datatable($rsas, true, true,false,false,false);
		}
		
		return $res;
	}
	public function pgnation_neft($BankID,$IsReadonlyPage = false)
	{
		$sql1 = "select chq.id, IF(chq.isEnteredByMember = 1, '<i class=\'fa  fa-user\'  style=\'font-size:10px;font-size:1.75vw;color:#6698FF;\'></i>', ''),if( billtype=0 , 'Regular', if(billtype=1,'Supp',if(billtype=2,'Invoice','Unknown'))) as BillType, DATE_FORMAT(chq.VoucherDate, '%d-%m-%Y'), vcr.ExternalCounter as VoucherNumber, DATE_FORMAT(chq.ChequeDate, '%d-%m-%Y'), chq.ChequeNumber,chq.Amount,led.ledger_name,
		chq.PayerBank,chq.PayerChequeBranch, chq.Comments from chequeentrydetails as chq JOIN  ledger as led on chq.PaidBy = led.id JOIN voucher as vcr ON vcr.RefNo = chq.id where chq.DepositID IN(".DEPOSIT_NEFT.",".DEPOSIT_ONLINE.") and chq.BankID = '" . $BankID . "' and vcr.To = '".$BankID."' and chq.status='Y' ";

		if(isset($_REQUEST["edt"]) && $_REQUEST["edt"] <> "")
		{
			$sql1 .= "  and chq.id = '".$_REQUEST["edt"]."'";	
		}
		
		if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
		{
			$sql1 .= "  and chq.VoucherDate BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
		}
		$sql1 .= "   ORDER BY chq.VoucherDate DESC";	
		
		/*$cntr = "select count(status) as cnt from chequeentrydetails where DepositID = -2 and BankID = '" . $BankID . "' and status='Y'";
		
		//echo $sql1;
		
		$this->display_pg->sql1		= $sql1;
		$this->display_pg->cntr1	= $cntr;
		$this->display_pg->mainpg	= "NeftDetails.php";

		$limit	= "50";
		$page	= $_REQUEST['page'];
		$extra	= "";

		$res	= $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;*/
		$result = $this->m_dbConn->select($sql1);
		$this->display1_neft($result,$IsReadonlyPage);
	}
	
	public function selecting()
	{
		//$sql = "select `ID`,DATE_FORMAT(VoucherDate, '%d-%m-%Y') as  VoucherDate,DATE_FORMAT(ChequeDate, '%d-%m-%Y') as ChequeDate,`ChequeNumber`,`Amount`,`PaidBy`,`BankID`,`PayerBank`,`PayerChequeBranch`,`Comments`,`DepositID`,`BillType` from chequeentrydetails where ID='".$_REQUEST['ChequeDetailsId']."'";
		$sql = "select chq.ID,DATE_FORMAT(VoucherDate, '%d-%m-%Y') as  VoucherDate,DATE_FORMAT(ChequeDate, '%d-%m-%Y') as ChequeDate,`ChequeNumber`,`Amount`,`TDS_Amount`,`PaidBy`,`BankID`,`PayerBank`,`PayerChequeBranch`,`Comments`,`DepositID`,`BillType`,IF(led.categoryid ='".DUE_FROM_MEMBERS."' ,1,0) as IsMemberLedger from chequeentrydetails as chq JOIN `ledger` as led on chq.PaidBy = led.id   where chq.ID='".$_REQUEST['ChequeDetailsId']."'";
		$res = $this->m_dbConn->select($sql);
		
		$reconcileSql = "SELECT bank.ReconcileStatus, bank.Reconcile, bank.Return FROM `bankregister` AS bank JOIN `voucher` AS voucher ON bank.VoucherID = voucher.id WHERE bank.ChkDetailID = '".$_REQUEST['ChequeDetailsId']."' AND voucher.RefTableID = ".TABLE_CHEQUE_DETAILS;
		$reconcileStatus = $this->m_dbConn->select($reconcileSql);
		if(sizeof($reconcileStatus) > 0)
		{	
			$res[0]['ReconcileStatus'] = $reconcileStatus[0]['ReconcileStatus'];
			$res[0]['Reconcile'] = $reconcileStatus[0]['Reconcile'];
			$res[0]['Return'] = $reconcileStatus[0]['Return'];
		}
		
		$sqlVoucher = "select  `Note`,`VoucherNo`, `ExternalCounter`, `To` from `voucher` where  `RefNo` ='".$_REQUEST['ChequeDetailsId']."'  and  `RefTableID` = '".TABLE_CHEQUE_DETAILS."' ";
		$data = $this->m_dbConn->select($sqlVoucher);
		
		if(sizeof($data) > 2) // This is to check whether it is direct entry or bank to multiple ledgers if it is multiple then show it in JV format 
		{
			$ByLedgers = array_column($data, 'To');
			if(in_array($_SESSION['default_tds_receivable'],$ByLedgers) && count($data) == 3) //Count 3 means one by site and 2 entry at To side one is party ledger and second is TDS 
			{
				$res[0]['Show_JvFormat'] = 0;	
			}
			else
			{
				$res[0]['Show_JvFormat'] = 1;	
			}
		}
		else
		{
			$res[0]['Show_JvFormat'] = 0;
		}
		
		if( strlen($data[0]['Note']) > strlen($res[0]['Comments']) )
			{
				$res[0]['Comments'] = 	$data[0]['Note'];
			}
	
		$res[0]['VoucherNumber'] = $data[0]['ExternalCounter'];
		$res[0]['VoucherNo'] = $data[0]['VoucherNo'];
		
		return $res;
	}
	public function deleting()
	{
		$sql = "update chequeentrydetails set status='N' where ID='".$_REQUEST['ChequeDetailsId']."'";
		$res = $this->m_dbConn->update($sql);
	}
	
	function getPayerBankDetails()
	{		
		$selectQuery = 'SELECT unit.`Payer_Bank`, unit.`unit_id`, unit.`Payer_Cheque_Branch`, sum(asset.Debit)-sum(asset.Credit) as `LedgerBalance` FROM `unit` JOIN assetregister as asset ON asset.LedgerID = unit.unit_id group by unit.unit_id';
		$result = $this->m_dbConn->select($selectQuery);
		return $result;	
	}
	
	function UpdateChequeWithTDS($VoucherDate, $ChequeDate, $ChequeNumber, $VoucherNumber, $systemVoucherNo, $IsCallUpdtCnt, $Amount, $TDS_Amount, $PaidBy, $BankID, $PayerBank, $PayerChequeBranch, $DepositID, $Comments,$ChequeDetailsId,$BillType)
	{
		$this->UpdateCheque($VoucherDate, $ChequeDate, $ChequeNumber, $VoucherNumber, $systemVoucherNo, $IsCallUpdtCnt, $Amount, $PaidBy, $BankID, $PayerBank, $PayerChequeBranch, $DepositID, $Comments,$ChequeDetailsId,$BillType,$TDS_Amount);
	}
	
	function UpdateCheque($VoucherDate, $ChequeDate, $ChequeNumber, $VoucherNumber, $systemVoucherNo, $IsCallUpdtCnt, $Amount, $PaidBy, $BankID, $PayerBank, $PayerChequeBranch, $DepositID, $Comments,$ChequeDetailsId,$BillType,$TDS_Amount = 0)
	{
/*		echo "<br>VoucherDate : ".$VoucherDate."<br>ChequeDate : ".$ChequeDate."<br>ChequeNumber : ".$ChequeNumber."<br>VoucherNumber : ".$VoucherNumber."<br>IsCallUpdtCnt : 
		".$IsCallUpdtCnt."<br>Amount : ".$Amount."<br>PaidBy : ".$PaidBy."<br>BankID : ".$BankID."<br>PayerBank : ".$PayerBank."<br>DepositID : ".$DepositID."<br>Comments : 
		".$Comments."<br>ChequeDetailsId : ".$ChequeDetailsId."<br>BillType : ".$BillType."<br>TDS_Amount : ".$TDS_Amount;*/	
		//echo "DepositId:".$DepositID;
		$sql = "select * from chequeentrydetails where ID='".$ChequeDetailsId."'";
		$res = $this->m_dbConn->select($sql);
		//echo "<br> UpdateCheque";
		//print_r($res);
		$PaidByPrev = $res[0]["PaidBy"];
		$PayerBankPrev = $res[0]["PayerBank"];
		$PayerChequeBranchPrev = $res[0]["PayerChequeBranch"];
		$VoucherDatePrev= $res[0]["VoucherDate"];
		
		$reconcileSql = "SELECT bank.`Reconcile Date`, bank.ReconcileStatus, bank.Reconcile, bank.`Return` FROM `bankregister` AS bank JOIN `voucher` AS voucher ON bank.VoucherID = voucher.id WHERE bank.ChkDetailID = '".$ChequeDetailsId."' AND voucher.RefTableID = ".TABLE_CHEQUE_DETAILS;			
		$reconcileStatus = $this->m_dbConn->select($reconcileSql);
		
		if(sizeof($reconcileStatus) > 0)
		{			
			$reconcileDate = $reconcileStatus[0]['Reconcile Date'];
			$status = $reconcileStatus[0]['ReconcileStatus'];
			$reconcile = $reconcileStatus[0]['Reconcile'];
			$return = $reconcileStatus[0]['Return'];
		}		
		
		$this->DeletePreviousRecord($PaidByPrev, $PayerBankPrev, $PayerChequeBranchPrev,$ChequeDetailsId);
		//echo "2";
		if($TDS_Amount <> 0)
		{
			$this->AddNewValuesWithTDS($VoucherDate, $ChequeDate, $ChequeNumber, $VoucherNumber,$VoucherNumber,$IsCallUpdtCnt, $Amount, $TDS_Amount, $PaidBy, $BankID, $PayerBank, $PayerChequeBranch, $DepositID, $Comments,$BillType,$reconcileDate,$status,$reconcile,$return);			
		}
		else
		{
			$this->AddNewValues($VoucherDate, $ChequeDate, $ChequeNumber, $VoucherNumber, $systemVoucherNo, $IsCallUpdtCnt, $Amount, $PaidBy, $BankID, $PayerBank, $PayerChequeBranch, $DepositID, $Comments, $BillType ,$reconcileDate, $status, $reconcile,$return);	
		}
		
		
		//echo "3";
		
		
	}
	
	function deleteReturnChequeEntry($PaidBy)
	{
		
		$reconcileSql = "SELECT bank.id, bank.Return, bank.`Reconcile Date` FROM `bankregister` AS bank JOIN `voucher` AS voucher ON bank.VoucherID = voucher.id WHERE bank.ChkDetailID = '".$_REQUEST['ChequeDetailsId']."' AND voucher.RefTableID = ".TABLE_CHEQUE_DETAILS;
		$reconcileStatus = $this->m_dbConn->select($reconcileSql);		
		
		if($reconcileStatus[0]['Return'] == 1)
		{
			$deleteQuery = "DELETE FROM `reversal_credits` WHERE `Date` = '".$reconcileStatus[0]['Reconcile Date']."' AND `UnitID` = '".$PaidBy."' AND `LedgerID` ='".PENALTY_TO_MEMBER."'";	
			$this->m_dbConn->delete($deleteQuery);
			
			$sqlQuery = "SELECT `ChkDetailID` FROM `bankregister` WHERE `Ref` = '".$reconcileStatus[0]['id']."'";
			$paymentID = $this->m_dbConn->select($sqlQuery);			
		}
		return $paymentID;
	}
	
	public function sendNeftNotificationByEmail($TransactionData, $GatewayID = "", $socID)
	{
		$result = 0;
		try
		{
			$sqlnotify_by_email = "SELECT `neft_notify_by_email` from `society` where `status` = 'Y' ";
			$resnotify_by_email = $this->m_dbConn->select($sqlnotify_by_email);
			//echo "trace1";
			$strUnitType = $this->m_objFetchDetails->getUnitPresentation($TransactionData['PaidBy']);		
			//echo "trace2";	
			if($resnotify_by_email[0]['neft_notify_by_email'] == 1)
			{
				//echo "trace3";
				//notify members by email flag is set
				require_once("include/fetch_data.php");
				$baseDir = dirname( dirname(__FILE__) );
				require_once($baseDir.'/swift/swift_required.php');
				$obj_fetch = new FetchData($this->m_dbConn);
				if($TransactionData['ModeOfReceipt']!= "NEFT" && $TransactionData['ModeOfReceipt']  != "Cash" && $TransactionData['ModeOfReceipt']  != "Online")
				{
					$TrabsactionMsgAmount='Cheque Amount';
					$TrabsactionMsgDate='Cheque Date';
					$TrabsactionMsgID='Cheque Number';
					$TrabsactionMsgHeader ='Cheque';
					//echo "cheque";
				}
				else if($TransactionData['ModeOfReceipt']  == "Online")
				{
					$TrabsactionMsgAmount='Amount';
					$TrabsactionMsgDate='Date';
					$TrabsactionMsgID='Transaction ID';
					//$TrabsactionMsgHeader ='Online';
					//echo "Online";
				}
				else if($TransactionData['ModeOfReceipt']  == "Cash")
				{
					$TrabsactionMsgAmount='Amount';
					$TrabsactionMsgDate='Date';
					$TrabsactionMsgID='NA';
				}
				else
				{
					$TrabsactionMsgAmount='Transaction Amount';
					$TrabsactionMsgDate='Transaction Date';
					$TrabsactionMsgID='Transaction ID';
				}
				//creating email subject
				$AmountRecd = number_format($TransactionData['Amount'],2);
				
				$mailSubject = $TransactionData['ModeOfReceipt'].$TrabsactionMsgHeader." Payment of Rs. ". $AmountRecd ." for ".$strUnitType." ".$TransactionData['PaidByName']." is received.";
				
				$memberDetails = $obj_fetch->GetMemberDetails($TransactionData['PaidBy']);
				$societyDetails = $obj_fetch->GetSocietyDetails($obj_fetch->GetSocietyID($TransactionData['PaidBy']));
				$mailToEmail = $obj_fetch->objMemeberDetails->sEmail;
				$mailToName = $obj_fetch->objMemeberDetails->sMemberName;
				
				//creating email body
				$mailBody = $this->m_objUtility->GetEmailHeader();
				//if($TransactionData['PaidByName'] <> "")
				//{
					//$mailSubject .= " #".$TransactionData['PaidByName'];
				//}
				
				$mailBody .= '<tr><td>Dear  '.$mailToName.',<br /></td></tr>
							  <tr><td><br /></td></tr>';
				//echo "mode:".$TransactionData['ModeOfReceipt'];				
				//die();
				$mailBody .= '<tr><td>Thank You. We have received your '.$TransactionData['ModeOfReceipt'].$TrabsactionMsgHeader.' payment for '.$strUnitType.' :'.$TransactionData['PaidByName'].'. Transaction details are :<br /></td></tr>
							 <tr><td><br /></td></tr>';
							
	$mailBody .='<table style="border-collapse: collapse; border: 1px solid black;">
	  <tr>
		<th style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">'.$strUnitType.'</th>
		<td style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">'.$TransactionData['PaidByName'].'</td>
	  </tr>';
	  if($TransactionData['ModeOfReceipt']  != "Cash" && $TransactionData['ModeOfReceipt']  != "Online")
		{
			$mailBody .=  
		  '<tr>
			<th style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">Deposited In Account:</th>
			<td style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">'.$TransactionData['SocietyAccountName'].'</td>
		  </tr>';
		  
		
	 $mailBody .= 
	  '<tr>
		<th style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">Payer Bank Name:</th>
		<td style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">'.$TransactionData['BankName'].'</td>
	  </tr>
	  <tr>
		<th style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">Payer Branch Name:</th>
		<td style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">'.$TransactionData['BranchName'].'</td>
	  </tr>';
		}
	   if($TransactionData['ModeOfReceipt']  == "Online")
	   {
	   		$mailBody .= 
	  '<tr>
		<th style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">Payment Gateway Name:</th>
		<td style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">'.$TransactionData['BankName'].'</td>
	  </tr>';
	   }
	  $mailBody .= '<tr>
		<th style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">'.$TrabsactionMsgAmount.':</th>
		<td style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">'.$AmountRecd.'			   </td>
	  </tr>
	  
	   <tr>
		<th style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">'.$TrabsactionMsgDate.':</th>
		<td style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">'.getDisplayFormatDate($TransactionData['Date']).'</td>
	  </tr>';
	  
	   if($TransactionData['ModeOfReceipt']  != "Cash")
		{
	   $mailBody .= '<tr>
		<th style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">'.$TrabsactionMsgID.':</th>
		<td style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">'.$TransactionData['TransationNo'].'</td>
	  </tr>';
		}
	  if($TransactionData['ModeOfReceipt']  == "Online")
	   {
	   		$mailBody .= 
	  '<tr>
		<th style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">Transaction Status:</th>
		<td style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">'.$TransactionData['Status'].'</td>
	  </tr>';
	   }
	   $mailBody .= '<tr>
		<th style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">Payment For:</th>
		<td style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">'.$TransactionData['BillType'].'</td>
	  </tr>
	  
	  <tr>
		<th style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">Comments:</th>
		<td style="border-collapse: collapse; border: 1px solid black;text-align:left;padding:8px;">'.$TransactionData['Comments'].'</td>
	  </tr>
	
	</table>';
	
				$mailBody .= "<br/><b>Note: Above deposit is subject to clearance/reconciliation.</b>";
				$mailBody .= $this->m_objUtility->GetEmailFooter();
				
				
				if($mailToEmail == '')
				{
					return "Email ID Missing";
				}
				else if(filter_var($mailToEmail, FILTER_VALIDATE_EMAIL) == false)
				{
					return "Incorrect Email ID  ".$mailToEmail." ";
				}
				
				$societyEmail = $obj_fetch->objSocietyDetails->sSocietyEmail;
				$societyName = $obj_fetch->objSocietyDetails->sSocietyName;
				$society_CCEmail = $obj_fetch->objSocietyDetails->sSocietyCC_Email;
				
				$EMailIDToUse = $this->m_objUtility->GetEmailIDToUse(false, 0, 0, 0, 0, 0, $socID);
				//print_r($EMailIDToUse);
			
				if($EMailIDToUse['status'] == 0)
				{
					$EMailID = $EMailIDToUse['email'];
					$Password = $EMailIDToUse['password'];
					
					// Create the mail transport configuration
					//$transport = Swift_SmtpTransport::newInstance('cs10.webhostbox.net', 465, "ssl")
					$transport = Swift_SmtpTransport::newInstance('cs10.webhostbox.net',587)	
						  ->setUsername($EMailID)
						  ->setSourceIp('0.0.0.0')
						  ->setPassword($Password) ;
										
					// Create the message
					$message = Swift_Message::newInstance();
					$message->setTo(array(
					  $mailToEmail => $mailToName
					 ));
					 
					$message->setSubject($mailSubject);
					$message->setBody($mailBody);
					$message->setFrom($EMailID, $obj_fetch->objSocietyDetails->sSocietyName);
					
					if($society_CCEmail <> "")
					{
						$message->setCc(array($society_CCEmail => $societyName));
					}
					
					$message->setContentType("text/html");	
					// Send the email
					$mailer = Swift_Mailer::newInstance($transport);
					//echo "sending email";
					$result = $mailer->send($message);
					//echo "res:".$result;
				}
				
				
			}
		}
		catch(Exception $exp)
		{
			return $exp->getMessage();
		}
		
		return $result;
	}
}
?>