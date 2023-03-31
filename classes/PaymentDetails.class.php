<?php
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once("include/dbop.class.php");
include_once("voucher.class.php");
include_once("changelog.class.php");
include_once("register.class.php");
include_once("dbconst.class.php");
include_once("latestcount.class.php");
include_once("utility.class.php");

class PaymentDetails extends dbop
{
	public $actionPage = "../PaymentDetails.php";
	public $m_dbConn;
	public $m_voucher;
	public $m_register;
	public $m_latestcount;
	public $m_objUtility;
	public $m_objLog;
	public $ADDEntryTracker;
	public $DELETEEntryTracker;
	public $EDITEntryTracker;
	public $actionType;
	private $prevRefForMultEntry;
	private $m_PrevDataVoucher;
	private $m_PrevLatestVoucher;
	private $multEntryID;
	private $prevMultEntryDetails;
	public $debug_trace;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);

		/*//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
		//$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);*/
		$this->m_voucher = new voucher($dbConn);
		$this->m_latestcount = new latestCount($dbConn);
		$this->m_register = new regiser($dbConn);
		$this->m_objUtility = new utility($dbConn);
		$_POST['EnteredBy'] = $_SESSION['login_id'];
		$this->m_objLog = new changeLog($dbConn);
		$this->prevRefForMultEntry = 0;
		$this->m_PrevDataVoucher = 0;
		$this->m_PrevLatestVoucher = 0;
		$this->multEntryID = 0;
		$this->prevMultEntryDetails = array();
		$this->debug_trace = 0;
		//dbop::__construct();
	}

/*	public function startProcess()
	{
		$errorExists = 0;

		//$curdate 		=  $this->curdate;
		//$curdate_show	=  $this->curdate_show;
		//$curdate_time	=  $this->curdate_time;
		//$ip_location	=  $this->ip_location;

		if($_REQUEST['insert']=='Insert' && $errorExists==0)
		{
			$insert_query="insert into PaymentDetails (`ChequeDate`,`ChequeNumber`,`Amount`,`PaidTo`,`EnteredBy`,`PayerBank`,`Comments`,`VoucherDate`) values ('".getDBFormatDate($_POST['ChequeDate'])."','".$_POST['ChequeNumber']."','".$_POST['Amount']."','".$_POST['PaidTo']."','".$_POST['EnteredBy']."','".$_POST['PayerBank']."','".$Comments."','".getDBFormatDate($VoucherDate)."')";
			$data = $this->m_dbConn->insert($insert_query);
			
			$dataVoucher  = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),$data,TABLE_PAYMENT_DETAILS,$this->m_latestcount->getLatestVoucherNo($_SESSION['society_id']),1,VOUCHER_PAYMENT,$_POST['PaidTo'],TRANSACTION_DEBIT,$_POST['Amount']);
			
			$bankregisterquery = "INSERT INTO bankregister(`LedgerID`, `VoucherID`, `VoucherTypeID`, `PaidAmount`, `ReceivedAmount`, `DepositGrp`, `ChkDetailID`) VALUES ('".$_POST['PayerBank']."','".$dataVoucher."','".VOUCHER_PAYMENT ."','".$_POST['Amount']."','',1,'".$data."')";
			$dataBankRegister = $this->m_dbConn->insert($bankregisterquery);
			
			$resVal = $this->m_register->SetAssetRegister(getDBFormatDate($_POST['VoucherDate']),$_POST['PaidTo'],$dataVoucher, VOUCHER_PAYMENT, TRANSACTION_DEBIT, $_POST['Amount'], 0);
			
			return "Insert";
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			$up_query="update PaymentDetails set `ChequeDate`='".$_POST['ChequeDate']."',`ChequeNumber`='".$_POST['ChequeNumber']."',`Amount`='".$_POST['Amount']."',`PaidTo`='".$_POST['PaidTo']."',`EnteredBy`='".$_POST['EnteredBy']."',`PayerBank`='".$_POST['PayerBank']."',`Comments`='".$Comments."',`VoucherDate`=".getDBFormatDate($_POST['VoucherDate'])."' where id='".$_POST['id']."'";
			$data = $this->m_dbConn->update($up_query);
			return "Update";
		}
		else
		{
			return $errString;
		}
	}*/
	
	function getNarration($lId)
	{
		$sql = "SELECT v.note  FROM `voucher` as v join ledger as l on l.id = v.To where l.id = ".$lId." order by v.id desc LIMIT 1";
		$data = $this->m_dbConn->select($sql);
		return $data[0]['note'];
		
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
	
	public function DeletePaymentInvoice($VoucherNo,$ClearVoucherNo,$BankID,$LeafID)
	{
		$ClearVoucherDetailQuery = "SELECT id, RefNo FROM voucher where VoucherNo = '".$ClearVoucherNo."'";
		$ClearVoucherDetail = $this->m_dbConn->select($ClearVoucherDetailQuery);
		
		$VoucherDetailQuery = "SELECT * FROM voucher where VoucherNo = '".$VoucherNo."'";
		$VoucherDetail = $this->m_dbConn->select($VoucherDetailQuery);
		
		
		$DeleteFromVoucherquery = "DELETE from voucher where VoucherNo = '".$VoucherNo."'";
		$VoucherDeleted = $this->m_dbConn->delete($DeleteFromVoucherquery);
		
		if(sizeof($VoucherDetail) > 0)
		{
			$regDelete1 = "delete from `assetregister` where `Is_Opening_Balance` = 0 AND `VoucherID`='".$VoucherDetail[0]['id']."'  ";
			$regDelete2 = "delete from `incomeregister` where `VoucherID`='".$VoucherDetail[0]['id']."' ";
			$regDelete3 = "delete from `expenseregister` where `VoucherID`='".$VoucherDetail[0]['id']."' ";
			$regDelete4 = "delete from `liabilityregister` where `VoucherID`='".$VoucherDetail[0]['id']."' ";
			$regDelete5 = "delete from `bankregister` where `Is_Opening_Balance` = 0 and `VoucherID`='".$VoucherID."' ";

			for($m = 1; $m < sizeof($VoucherDetail); $m++)
			{
				if($VoucherDetail[$m] <> "" && $VoucherDetail[$m] > 0)
				{
					$regDelete1 .= " OR `VoucherID`='".$VoucherDetail[$m]['id']."' ";
					$regDelete2 .= " OR `VoucherID`='".$VoucherDetail[$m]['id']."' ";
					$regDelete3 .= " OR `VoucherID`='".$VoucherDetail[$m]['id']."' ";
					$regDelete4 .= " OR `VoucherID`='".$VoucherDetail[$m]['id']."' ";
					$regDelete5 .= " OR `VoucherID`='".$VoucherDetail[$m]['id']."' ";
				}
			}
			
			$regResult1 = $this->m_dbConn->delete($regDelete1);	
			$regResult2 = $this->m_dbConn->delete($regDelete2);
			$regResult3 = $this->m_dbConn->delete($regDelete3);	
			$regResult4 = $this->m_dbConn->delete($regDelete4);	
			$regResult5 = $this->m_dbConn->delete($regDelete5);
		}
		
		$DeleteFromInvoiceQuery = "delete from `invoicestatus` where InvoiceRaisedVoucherNo = '".$VoucherNo."'";
		$DeleteFromInvoice = $this->m_dbConn->delete($DeleteFromInvoiceQuery);
		
		$InvoiceDetailsQuery = "SELECT * FROM `invoicestatus` WHERE InvoiceClearedVoucherNo = '".$ClearVoucherNo."'";
		$InvoiceDetails = $this->m_dbConn->select($InvoiceDetailsQuery);
		
		
		if(sizeof($InvoiceDetails) <> 0)
		{
			$index = sizeof($InvoiceDetails)-1;
			$UpdatePaymentDetailsquery = "UPDATE paymentdetails set InvoiceAmount = '".$InvoiceDetails[$index]['InvoiceChequeAmount']."',TDSAmount = '".$InvoiceDetails[$index]['TDSAmount']."' WHERE id = '".$ClearVoucherDetail[0]['RefNo']."'";
			$DeleteFromInvoice = $this->m_dbConn->delete($UpdatePaymentDetailsquery);
		}
		else
		{
			$UpdatePaymentDetailsquery = "UPDATE paymentdetails set InvoiceAmount = 0,TDSAmount = 0 WHERE id = '".$ClearVoucherDetail[0]['RefNo']."'";
			$DeleteFromInvoice = $this->m_dbConn->delete($UpdatePaymentDetailsquery);
		}
		
		$ChangeMsg = '';
	
		for($i = 0 ; $i < sizeof($VoucherDetail); $i++)
		{
			if(isset($VoucherDetail[$i]['By']) && $VoucherDetail[$i]['By'] <> '0' && $VoucherDetail[$i]['Debit'] <> 0 && $VoucherDetail[$i]['Debit'] <> '')
			{
				//echo '<br> By side entry '.$VoucherDetail[$i]['By'];
				$LedgerName=$this->m_objUtility->getLedgerName($VoucherDetail[$i]['By']);
				$ChangeMsg .=$LedgerName." ".$VoucherDetail[$i]['Debit']." :: ";
				
			}
			else if(isset($VoucherDetail[$i]['To']) && $VoucherDetail[$i]['To'] <> '0' && $VoucherDetail[$i]['Credit'] <> 0 && $VoucherDetail[$i]['Credit'] <> '')
			{
				$LedgerName=$this->m_objUtility->getLedgerName($VoucherDetail[$i]['To']);
				$ChangeMsg .=$LedgerName." ".$VoucherDetail[$i]['Credit']." :: ";
			}	
		}
		
		$ChangeMsg .=" Voucher Number ".$VoucherDetail[$i]['VoucherNo']." delete" ; 
	
		$this->m_objLog->setLog($ChangeMsg, $_SESSION['login_id'], 'Invoice_status', '--');
	}
	
	public function AddNewPaymentEntry($LeafID, $SocietyID, $PaidTo, $ChequeNumber, $ChequeDate,$PayerAmount, $PayerBank, $Comments, $VoucherType, $VoucherDate,$ModeOfPayment, $ByArray, $ToArray, $MultipleEntry = 0,$ListOfAccounts,$VoucherCounter,$SystemNo,$IsCallUpdtCnt,$IseditMode = false, $reconcileDate = 0, $reconcileStatus = 0, $reconcile = 0, $return = 0,$GUID)
	{
			
			$ListOfCashLedger = $this->m_objUtility->GetBankLedger($_SESSION['default_cash_account']);
			
			$CashLedgers = array();
			for($i = 0; $i< sizeof($ListOfCashLedger); $i++)
			{
				array_push($CashLedgers,$ListOfCashLedger[$i]['id']);
			}
			
			$IsSameCountChecked = $this->m_objUtility->IsSameCounterApply();
		
			$SystemVoucherNo=$SystemNo;
			$ExVoucherNo=$VoucherCounter;
			$isMultEntry = false;	
			$ExtVoucherType = "";
			$ExtVoucherType = VOUCHER_PAYMENT;
			//***Below code is just to verify it is a cash entry or bank entry according to that send Ledger to update counter it only applicable when same bank checked
			$ListOfCashLedger = $this->m_objUtility->GetBankLedger($_SESSION['default_cash_account']);
			$CashLedgers = array();
			for($i = 0; $i< sizeof($ListOfCashLedger); $i++)
			{
				array_push($CashLedgers,$ListOfCashLedger[$i]['id']);
			}
			/*if(!$bSkipBeginTrnx)
			{					
				$this->m_dbConn->begin_transaction();
			}*/
			//echo '<br>Leaf ID : '.$LeafID;
			
			
			if($LeafID=='' || $LeafID==0)
			{
				$LeafID=-1;
			}
			
			
					$insert_query="insert into paymentdetails (`ChequeDate`,`ChequeNumber`,`Amount`,`PaidTo`,`EnteredBy`,`PayerBank`,`Comments`,`VoucherDate`,
			`ChqLeafID`,`ExpenseBy`,`InvoiceDate`,`InvoiceAmount`,`TDSAmount`,`ModeOfPayment`,`IsMultipleEntry`) values ('".getDBFormatDate($ChequeDate)."','".$ChequeNumber."','".$PayerAmount."','".$PaidTo
			."','".$_SESSION['login_id']."','".$this->m_dbConn->escapeString($PayerBank)."','".$this->m_dbConn->escapeString($Comments)."','".getDBFormatDate($VoucherDate)."','"
			.$LeafID."','".$ExpenseBy."','".getDBFormatDate($InvoiceDate)."','".$invoiceAmount."','".$TDSAmount."','".$ModeOfPayment."','".$MultipleEntry."')";
					//echo '<br>Insert Query : '.$insert_query;
					
				    //echo $insert_query;
					$data = $this->m_dbConn->insert($insert_query);	
				
				 	
				 //$LatestVoucherNo = $this->m_latestcount->getLatestVoucherNo($_SESSION['society_id']);
				if($ExistPaymemtVoucher != 0)
				{
					//echo '<br>Exist Payment Voucher  : '.$ExistPaymemtVoucher;
					$LatestVoucherNo = $ExistPaymemtVoucher;
				}
				else
				{	
					//echo '<br>It Will Get Through Function';
					$LatestVoucherNo = $this->m_latestcount->getLatestVoucherNo($_SESSION['society_id']);								
				}
					
					$Payer = $PayerBankName;
					$SrNo = 0;
					
					$AccountsArray = array_column($ListOfAccounts, 'id');
					
					for($i=0;$i<sizeof($ByArray);$i++)
					{
						//echo '<br>By Ledger Name : '.$ByArray[$i]['Head'];
						//echo '<br>By LedgerAmount : '.$ByArray[$i]['Amt'];
						$SrNo++;
						
						if($VoucherType == 'Contra')
						{
							if(!empty($GUID))
							{
								$dataVoucherContra1  = $this->m_voucher->SetVoucherDetails_WithGUID(getDBFormatDate($VoucherDate),$data,TABLE_PAYMENT_DETAILS,$LatestVoucherNo,$SrNo,VOUCHER_CONTRA,$ByArray[$i]['Head'],TRANSACTION_DEBIT,$ByArray[$i]['Amt'], $Comments ,$ExVoucherNo,$GUID);
							}
							else
							{
								$dataVoucherContra1  = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),$data,TABLE_PAYMENT_DETAILS,$LatestVoucherNo,$SrNo,VOUCHER_CONTRA,$ByArray[$i]['Head'],TRANSACTION_DEBIT,$ByArray[$i]['Amt'], $Comments ,$ExVoucherNo);	
							}
							
						
						if($dataVoucherContra1=='')
						{
							
							$rowerrormsg.='<br>Voucher Contra Details(By Ledgers) Not Imported';
							//$this->m_objUtility->logGenerator($errorfile,'',$rowerrormsg,'E');
							return false;
						}
						if(in_array($ByArray[$i]['Head'],$AccountsArray))
						{
							//$date, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $depositGroup, $chequeDetailID, $isOpeningBalance = 0, $chequeDate = 0, $ref = 0, $reconcileDate = 0, $reconcileStatus = 0, $reconcile = 0, $return = 0
							//echo '<br>In Bank Register';
								$setbankregister=$this->m_register->SetBankRegister($ChequeDate,$ByArray[$i]['Head'],$dataVoucherContra1,VOUCHER_CONTRA,TRANSACTION_PAID_AMOUNT,$ByArray[$i]['Amt'],$LeafID,$data,0,$ChequeDate,0,$reconcileDate,$reconcileStatus,$reconcile,$return);
						}
						else
						{
						$setregister=$this->m_register->SetRegister($ChequeDate,$ByArray[$i]['Head'],$dataVoucherContra1,VOUCHER_CONTRA,TRANSACTION_CREDIT,$ByArray[$i]['Amt'],0);
						}
							
							
						}
						else
						{
							if(!empty($GUID))
							{	
								$dataVoucher1  = $this->m_voucher->SetVoucherDetails_WithGUID(getDBFormatDate($VoucherDate),$data,TABLE_PAYMENT_DETAILS,$LatestVoucherNo,$SrNo,VOUCHER_PAYMENT,$ByArray[$i]['Head'],TRANSACTION_DEBIT,$ByArray[$i]['Amt'], $Comments ,$ExVoucherNo,$GUID);
							}
							else
							{
								$dataVoucher1  = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),$data,TABLE_PAYMENT_DETAILS,$LatestVoucherNo,$SrNo,VOUCHER_PAYMENT,$ByArray[$i]['Head'],TRANSACTION_DEBIT,$ByArray[$i]['Amt'], $Comments ,$ExVoucherNo);	
							}
						//echo '<br>After Transaction Credit';
						//echo '<br>Data Voucher 1 : '.$dataVoucher1;
						
						if($dataVoucher1=='')
						{
							
							$rowerrormsg.='<br>Voucher Details(By Ledgers) Not Imported';
							//$this->m_objUtility->logGenerator($errorfile,'',$rowerrormsg,'E');
							return false;
						}
						if(in_array($ByArray[$i]['Head'],$AccountsArray))
						{
							//$date, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $depositGroup, $chequeDetailID, $isOpeningBalance = 0, $chequeDate = 0, $ref = 0, $reconcileDate = 0, $reconcileStatus = 0, $reconcile = 0, $return = 0
							//echo '<br>In Bank Register';
							$setbankregister=$this->m_register->SetBankRegister($ChequeDate,$ByArray[$i]['Head'],$dataVoucher1,VOUCHER_PAYMENT,TRANSACTION_PAID_AMOUNT,$ByArray[$i]['Amt'],$LeafID,$data,0,$ChequeDate,0,$reconcileDate,$reconcileStatus,$reconcile,$return);
						}
						else
						{
							$setregister=$this->m_register->SetRegister($ChequeDate,$ByArray[$i]['Head'],$dataVoucher1,VOUCHER_PAYMENT,TRANSACTION_CREDIT,$ByArray[$i]['Amt'],0);
						}
						//echo '<br><b>Set Register : </b>'.$setregister;
						
						}
						
					}
					
					for($i=0;$i<sizeof($ToArray);$i++)
					{
						//echo '<br> Inside the ToArray';
						$SrNo++;
						if($VoucherType == 'Contra')
						{
							//echo '<br>Voucher Type - Contra';
							if(!empty($GUID))
							{
								$dataVoucherContra  = $this->m_voucher->SetVoucherDetails_WithGUID(getDBFormatDate($VoucherDate),$data,TABLE_PAYMENT_DETAILS,$LatestVoucherNo, $SrNo,VOUCHER_CONTRA, $ToArray[$i]['Head'],TRANSACTION_CREDIT,$ToArray[$i]['Amt'], $Comments,$ExVoucherNo,$GUID);
							}
							else
							{
								$dataVoucherContra  = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),$data,TABLE_PAYMENT_DETAILS,$LatestVoucherNo, $SrNo,VOUCHER_CONTRA, $ToArray[$i]['Head'],TRANSACTION_CREDIT,$ToArray[$i]['Amt'], $Comments,$ExVoucherNo);						
							}
							
							
							if($dataVoucherContra =='')
							{
							
								$rowerrormsg.='<br>Voucher Contra Details(ToLedgers) Not Imported';
								//$this->m_objUtility->logGenerator($errorfile,'',$rowerrormsg,'E');
								return false;
							}

							if(in_array($ToArray[$i]['Head'],$AccountsArray))
							{
								//$date, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $depositGroup, $chequeDetailID, $isOpeningBalance = 0, $chequeDate = 0, $ref = 0, $reconcileDate = 0, $reconcileStatus = 0, $reconcile = 0, $return = 0
								//echo '<br>In Bank Register';
								$setbankregister=$this->m_register->SetBankRegister($ChequeDate,$ToArray[$i]['Head'],$dataVoucherContra,VOUCHER_CONTRA,TRANSACTION_RECEIVED_AMOUNT,$ToArray[$i]['Amt'],$LeafID,$data,0,$ChequeDate,0,0,0,0,1);
							}
							else
							{
								//$date, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance
					  			$setregister=$this->m_register->SetRegister($ChequeDate,$ToArray[$i]['Head'],$dataVoucherContra,VOUCHER_CONTRA,TRANSACTION_DEBIT,$ToArray[$i]['Amt'],0);
							}
					
						}
	
						else
						{	
							
							if(!empty($GUID))
							{
								//echo '<br> Payment Set Voucher';
								$dataVoucher  = $this->m_voucher->SetVoucherDetails_WithGUID(getDBFormatDate($VoucherDate),$data,TABLE_PAYMENT_DETAILS,$LatestVoucherNo, $SrNo,VOUCHER_PAYMENT, $ToArray[$i]['Head'],TRANSACTION_CREDIT,$ToArray[$i]['Amt'], $Comments,$ExVoucherNo,$GUID);	
							}
							else
							{
								$dataVoucher  = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),$data,TABLE_PAYMENT_DETAILS,$LatestVoucherNo, $SrNo,VOUCHER_PAYMENT, $ToArray[$i]['Head'],TRANSACTION_CREDIT,$ToArray[$i]['Amt'], $Comments,$ExVoucherNo);									
							}
							
							
							
							if($dataVoucher=='')
							{
							
							$rowerrormsg.='<br>Voucher Details(ToLedgers) Not Imported';
							//$this->m_objUtility->logGenerator($errorfile,'',$rowerrormsg,'E');
							return false;
							}
						

							if(in_array($ToArray[$i]['Head'],$AccountsArray))
							{
								//$date, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $depositGroup, $chequeDetailID, $isOpeningBalance = 0, $chequeDate = 0, $ref = 0, $reconcileDate = 0, $reconcileStatus = 0, $reconcile = 0, $return = 0
								//echo '<br>In Bank Register';
								$setbankregister=$this->m_register->SetBankRegister($ChequeDate,$ToArray[$i]['Head'],$dataVoucher,VOUCHER_PAYMENT,TRANSACTION_RECEIVED_AMOUNT,$ToArray[$i]['Amt'],$LeafID,$data,0,$ChequeDate,0,0,0,0,1);
							}
							else
							{
								//$date, $ledgerID, $voucherID, $voucherTypeID, $transactionType, $amount, $isOpeningBalance
					  			$setregister=$this->m_register->SetRegister($ChequeDate,$ToArray[$i]['Head'],$dataVoucher,VOUCHER_PAYMENT,TRANSACTION_DEBIT,$ToArray[$i]['Amt'],0);
							}
						}
					}
				
					if($IsCallUpdtCnt == 1)
					{	
						if($IsSameCountChecked == 1)
						{
							if(in_array($PayerBank,$CashLedgers))
							{
								//$ExpectedCounter = $this->m_objUtility->GetCounter($ExtVoucherType,$PayerBank,false);
								$this->m_objUtility->UpdateExVCounter($ExtVoucherType, $ExVoucherNo, $PayerBank);
							}
							else
							{
								//$ExpectedCounter = $this->m_objUtility->GetCounter($ExtVoucherType,0,false);
								$this->m_objUtility->UpdateExVCounter($ExtVoucherType, $ExVoucherNo, 0);
							}
						}
						else
						{	
							//$ExpectedCounter = $this->m_objUtility->GetCounter($ExtVoucherType,$PayerBank,false);
							$this->m_objUtility->UpdateExVCounter($ExtVoucherType, $ExVoucherNo, $PayerBank);	
						}	
					}
		
		if($IseditMode == true)
		{
			return $LatestVoucherNo;
		}
		else
		{
			return true;	
		}				
				
	}
		
	
	
	public function AddNewValues($LeafID, $SocietyID, $PaidTo, $ChequeNumber, $ChequeDate, $ExVoucherNo, $SystemDefineVNo, $IsCallUpdtCnt, $Amount, $PayerBank, $Comments, $VoucherDate, $ExpenseBy, $DoubleEntry,$InvoiceDate,$TDSAmount,$ModeOfPayment, $reconcileDate = 0, $reconcileStatus = 0, $reconcile = 0, $return = 0, $MultipleEntry = 0, $Ref = 0, $ID = 0, $invoiceAmount = 0, &$PaymentVoucherNo,
	 $ExistPaymemtVoucher= 0,$actionMode = ADD, $bSkipBeginTrnx = false)
	{							
		try
		{
			$isMultEntry = false;	
			$ExtVoucherType = "";
			$ExtVoucherType = VOUCHER_PAYMENT;
			//***Below code is just to verify it is a cash entry or bank entry according to that send Ledger to update counter it only applicable when same bank checked
			$ListOfCashLedger = $this->m_objUtility->GetBankLedger($_SESSION['default_cash_account']);
			$CashLedgers = array();
			for($i = 0; $i< sizeof($ListOfCashLedger); $i++)
			{
				array_push($CashLedgers,$ListOfCashLedger[$i]['id']);
			}
			if(!$bSkipBeginTrnx)
			{					
				$this->m_dbConn->begin_transaction();
			}
			$IsSameCountChecked = $this->m_objUtility->IsSameCounterApply();
			if($ModeOfPayment == 0  && $ChequeNumber <> "")
			{
				if($LeafID != -1)
				{
					$ChequeExistance = $this->m_dbConn->select("select id,Reference from paymentdetails where ChequeNumber='".$ChequeNumber."' and PayerBank=".$PayerBank);
								
				}			
				else
				{
					//$PayerBank = "-1";
				}
			}
			else
			{				
				$ChequeExistance = "";
			}
			if($ChequeExistance <> "")
			{								
				if($ChequeExistance[0]['Reference'] <> 0 && $ChequeExistance[0]['Reference'] == $ID && $MultipleEntry == 1)				
				{
					$ChequeExistance = "";
				}
			}
			if($MultipleEntry == 1)
			{
				//save first  parent row id for reference
				$this->prevRefForMultEntry = $Ref;				
			}
			if($Ref <> 0 && $this->prevRefForMultEntry == $Ref && $MultipleEntry <> 1)
			{
				//check for multiple related rows
				$isMultEntry = true;				
			}			
			
			
			if($ChequeExistance == "" || $LeafID == "-1" || $isMultEntry == true)
			{
				
				
				if($TDSAmount == '')
				{
					$TDSAmount=0;
				}
				
				
				
				if($LeafID != -1)
				{	
					
					
					//*** for common counter
					//$this->m_objUtility->UpdateExVCounter($ExtVoucherType, 0, 0);
					
					 $insert_query="insert into paymentdetails (`ChequeDate`,`ChequeNumber`,`Amount`,`PaidTo`,`EnteredBy`,`PayerBank`,`Comments`,`VoucherDate`,
			`ChqLeafID`,`ExpenseBy`,`InvoiceDate`,`InvoiceAmount`,`TDSAmount`,`ModeOfPayment`,`IsMultipleEntry`) values ('".getDBFormatDate($ChequeDate)."','".$ChequeNumber."','".$Amount."','".$PaidTo
			."','".$_SESSION['login_id']."','".$PayerBank."','".$this->m_dbConn->escapeString($Comments)."','".getDBFormatDate($VoucherDate)."','"
			.$LeafID."','".$ExpenseBy."','".getDBFormatDate($InvoiceDate)."','".$invoiceAmount."','".$TDSAmount."','".$ModeOfPayment."','".$MultipleEntry."')";
			
				//echo $insert_query;
					$data = $this->m_dbConn->insert($insert_query);	
					if($MultipleEntry == 1)
					{
					//set reference for self multiple entry
						$this->multEntryID = $data;
						$updateRefQuery = 'UPDATE `paymentdetails` SET `Reference`= "'.$this->multEntryID.'" WHERE `id` = "'.$data.'"';
						$this->m_dbConn->update($updateRefQuery);
					}			
					if($isMultEntry == true && $this->multEntryID <> 0)
					{
						//set reference for related multiple entry
						$updateRefQuery = 'UPDATE `paymentdetails` SET `Reference`= "'.$this->multEntryID.'" WHERE `id` = "'.$data.'"';
						$this->m_dbConn->update($updateRefQuery);	
					}								
				}
				else
				{
					$date="0000-00-00";
				  $insert_query='insert into paymentdetails (`ChequeDate`,`ChequeNumber`,`Amount`,`PaidTo`,`EnteredBy`,`PayerBank`,`Comments`,`VoucherDate`,
			`ChqLeafID`,`ExpenseBy`,`InvoiceDate`,`TDSAmount`,`ModeOfPayment`) values ("'.$date.'","'.$ChequeNumber.'","'.$Amount.'","'.$PaidTo
			.'","'.$_SESSION['login_id'].'","'.$PayerBank.'","'.$this->m_dbConn->escapeString($Comments).'","'.getDBFormatDate($VoucherDate).'","'
			.$LeafID.'","'.$ExpenseBy.'","'.getDBFormatDate($InvoiceDate).'","'.$TDSAmount.'","-1")';
					$data = $this->m_dbConn->insert($insert_query);	
				 }
					$this->ADDEntryTracker ="new record added at($data)"; 
					if($ModeOfPayment != 2  && $ModeOfPayment != -1 && $ChequeNumber <> "")
					{
						$Updatechequeleafbook="Update chequeleafbook set LastIssuedCheque='".$ChequeNumber."' where `id`=".$LeafID." ";
						$UpdateStatus = $this->m_dbConn->update($Updatechequeleafbook);
					}
				
				//$LatestVoucherNo = $this->m_latestcount->getLatestVoucherNo($_SESSION['society_id']);
				
					$arCreditors = $this->m_dbConn->select("select ledgertbl.id,ledgertbl.ledger_name,categorytbl.group_id, ledgertbl.categoryid from ledger As ledgertbl JOIN account_category As categorytbl ON ledgertbl.categoryid = categorytbl.category_id where ledgertbl.payment=1 and categorytbl.group_id=1 and ledgertbl.society_id=".$_SESSION['society_id']);
				//echo $arCreditors;
					$bIsCreditor = 0;
				//print_r($arCreditors);
					for($iHeaderCount = 0; $iHeaderCount < sizeof($arCreditors); $iHeaderCount++)
					{
				//echo "<script>alert(".$headerList[$iHeaderCount]['id'].")<//script>";
					//echo "hdr:".$arCreditors[$iHeaderCount]['id'];
						if($PaidTo == $arCreditors[$iHeaderCount]['id'])
						{
							$bIsCreditor = 1;
							//echo 'PaidTo : ' . $PaidTo . ' Creditor : ' . $bIsCreditor;
							break;
						}
						//echo "PaidTo:".$PaidTo;
					}
				
					$IsPaidToisBankAC = 0;
					if( $PaidTo != "")
					{
						$arPaidToParentDetails = $this->m_objUtility->getParentOfLedger($PaidTo);
						if(!(empty($arPaidToParentDetails)))
						{
							$PaidToGroupID = $arPaidToParentDetails['group'];
							$PaidToCategoryID = $arPaidToParentDetails['category'];
							//echo "GroupID:" .$GroupID;
							//if($PaidToGroupID == ASSET)
							//{
								//$IsPaidToisFixedAsset = 1;
								//echo "fixedaseet:" .$IsExpenseByisFixedAsset;
							//}
							if($PaidToCategoryID == $_SESSION['default_bank_account'] || $PaidToCategoryID == $_SESSION['default_cash_account'])//don't use constant 
							{
								$IsPaidToisBankAC = 1;
							}
						}
					}
	
					$IsExpenseByisFixedAsset = 0;
					$IsExpenseByisBankAC = 0;
					if( $ExpenseBy != "")
					{
						$arParentDetails = $this->m_objUtility->getParentOfLedger($ExpenseBy);
						//print_r($arParentDetails);
						if(!(empty($arParentDetails)))
						{
							$ExpenseByGroupID = $arParentDetails['group'];
							$ExpenseByCategoryID = $arParentDetails['category'];						
							if($ExpenseByGroupID == ASSET)
							{							
								$IsExpenseByisFixedAsset = 1;
								//echo "fixedaseet:" .$IsExpenseByisFixedAsset
							}
							if($ExpenseByCategoryID == $_SESSION['default_bank_account'] || $ExpenseByCategoryID == $_SESSION['default_cash_account'])
							{							
								$IsExpenseByisBankAC = 1;
							}
						}
					}
					$LiableForContraEntry = 0;
					//if($IsPaidToisBankAC == 1 && $IsExpenseByisBankAC == 1)
					if($IsPaidToisBankAC == 1 && $DoubleEntry != 1)
					{
						$LiableForContraEntry = 1;
					}
					else if($IsPaidToisBankAC == 1 && $IsExpenseByisBankAC == 1)				
					{
						$LiableForContraEntry = 1;
					}
								
					if($DoubleEntry == 1 && $ExpenseBy != "" && $LiableForContraEntry == 0) //Double entry checkbox is checked
					{
						$creditAmt = $Amount;
						if($Ref <> 0 && $this->prevRefForMultEntry == $Ref  && $MultipleEntry <> 1)
						{
							/*$LatestVoucherNo = $this->prevMultEntryDetails['JVVoucherNo'];												
							$SrNo = $this->prevMultEntryDetails['JVVoucherSrNo'];
							$this->prevMultEntryDetails['JVVoucherSrNo'] = $this->prevMultEntryDetails['JVVoucherSrNo'] + 3;*/
							$creditAmt = $invoiceAmount - $TDSAmount;
						}
						else
						{						
							if($MultipleEntry == 1)
							{
								/*$this->prevMultEntryDetails['JVVoucherNo'] = $LatestVoucherNo;
								$this->prevMultEntryDetails['JVVoucherSrNo'] = $SrNo + 3;	*/	
								$creditAmt = $invoiceAmount - $TDSAmount;												
							}
							/*else if($this->prevMultEntryDetails['JVVoucherSrNo'] == 0)
							{
								$this->prevMultEntryDetails['JVVoucherNo'] = $LatestVoucherNo;
								$this->prevMultEntryDetails['JVVoucherSrNo'] = $SrNo + 3;
							}*/
						}
					
						$SrNo=1;					
						$LatestVoucherNo = $this->m_latestcount->getLatestVoucherNo($_SESSION['society_id']);
							
							
							if($TDSAmount =='')
							{
								$TDSAmount=0;
							}
							
							//$TotalAmount=$Amount + $TDSAmount;
							$TotalAmount=$invoiceAmount;
							
							$dataVoucher1  = $this->m_voucher->SetVoucherDetails(getDBFormatDate($InvoiceDate),0,0,$LatestVoucherNo,$SrNo,VOUCHER_JOURNAL,$ExpenseBy,TRANSACTION_DEBIT,$TotalAmount, $Comments,$ExVoucherNo);
								
							$updatePaymentTable="UPDATE `paymentdetails` set `VoucherID`=".$dataVoucher1." ,`VoucherTypeID`=".VOUCHER_JOURNAL." where `id`=".$data." ";
							$updateData = $this->m_dbConn->update($updatePaymentTable);
							
							if($ExpenseByGroupID==LIABILITY)
							{
								//echo "<br> ExpenseByGroupID LIABILITY";
												
									$regResult1 = $this->m_register->SetLiabilityRegister(getDBFormatDate($InvoiceDate),$ExpenseBy,$dataVoucher1,VOUCHER_JOURNAL, TRANSACTION_DEBIT, $TotalAmount, 0);	
											
							}
							
							if($ExpenseByGroupID==ASSET)
							{
												//echo "<br> ExpenseByGroupID ASSET";
									$regResult2 = $this->m_register->SetAssetRegister(getDBFormatDate($InvoiceDate), $ExpenseBy, $dataVoucher1, VOUCHER_JOURNAL, TRANSACTION_DEBIT, $TotalAmount, 0);	
							}
							
							if($ExpenseByGroupID==INCOME)
							{
												//echo "<br> ExpenseByGroupID INCOME";
						            $regResult3 = $this->m_register->SetIncomeRegister($ExpenseBy, getDBFormatDate($InvoiceDate), $dataVoucher1, VOUCHER_JOURNAL, TRANSACTION_DEBIT, $TotalAmount);
							}
							
							if($ExpenseByGroupID==EXPENSE)
							{
								//echo "<br> ExpenseByGroupID EXPENSE";
								 
									$regResult4 = $this->m_register->SetExpenseRegister($ExpenseBy,getDBFormatDate($InvoiceDate), $dataVoucher1,VOUCHER_JOURNAL, TRANSACTION_DEBIT,$TotalAmount,0);
											
						    }
								
														
							$dataVoucher2  = $this->m_voucher->SetVoucherDetails(getDBFormatDate($InvoiceDate),0,0,$LatestVoucherNo,$SrNo+1,VOUCHER_JOURNAL,$PaidTo,TRANSACTION_CREDIT,$creditAmt, $Comments, $ExVoucherNo);
							$ExpectedCounter = 0;
							if($IsCallUpdtCnt == 1)
							{	
								if($IsSameCountChecked == 1)
								{
									if(in_array($Payer,$CashLedgers))
									{
										$this->m_objUtility->UpdateExVCounter($ExtVoucherType, $ExVoucherNo, $PayerBank);
									}
									else
									{
										$this->m_objUtility->UpdateExVCounter($ExtVoucherType, $ExVoucherNo, 0);
									}
								}
								else
								{
									$this->m_objUtility->UpdateExVCounter($ExtVoucherType, $ExVoucherNo, $PayerBank);	
								}	
							}
                           
						    if($actionMode == ADD)
							{
								if($ExVoucherNo <> $SystemDefineVNo)
								{
									$ChangeMsg = 'Voucher Number '.$SystemDefineVNo.' Changed to '.$ExVoucherNo.' On Payment Insert';
									$this->m_objLog->setLog($ChangeMsg, $_SESSION['login_id'], 'Payment', $data);
								}
								
							}
							else if($actionMode == EDIT)
							{
								if($SystemDefineVNo <> $ExVoucherNo)
								{
									//when user update the payment entry  and if user change the Counter then log insert
									$ChangeMsg = 'Voucher Number '.$SystemDefineVNo.' Changed to '.$ExVoucherNo.' On Payment Updatation';
									$this->m_objLog->setLog($ChangeMsg, $_SESSION['login_id'], 'Payment', $data);		
								}
							}
							
							if($PaidToGroupID==LIABILITY)
							{
												//echo "<br> PaidToGroupID LIABILITY";
									$regResult5 = $this->m_register->SetLiabilityRegister(getDBFormatDate($InvoiceDate),$PaidTo,$dataVoucher2,VOUCHER_JOURNAL, TRANSACTION_CREDIT, $creditAmt, 0);
									
							}
							
							if($PaidToGroupID==ASSET)
							{
								//echo "<br> PaidToGroupID ASSET";
									$regResult6 = $this->m_register->SetAssetRegister(getDBFormatDate($InvoiceDate), $PaidTo, $dataVoucher2,VOUCHER_JOURNAL, TRANSACTION_CREDIT, $creditAmt, 0);	
							}
							
							if($PaidToGroupID==INCOME)
							{
												//echo "<br> PaidToGroupID INCOME";
									$regResult7 = $this->m_register->SetIncomeRegister($PaidTo, getDBFormatDate($InvoiceDate), $dataVoucher2,VOUCHER_JOURNAL, TRANSACTION_CREDIT, $creditAmt);
							}
							
							if($PaidToGroupID==EXPENSE)
							{
									//echo "<br> PaidToGroupID EXPENSE";
									$regResult8 = $this->m_register->SetExpenseRegister($PaidTo,getDBFormatDate($InvoiceDate), $dataVoucher2,VOUCHER_JOURNAL, TRANSACTION_CREDIT, $creditAmt,0);
							}
								
							
							if($TDSAmount > 0)
							{
								//echo "<br> tdsamount > 0";
								
									$dataVoucher3  = $this->m_voucher->SetVoucherDetails(getDBFormatDate($InvoiceDate),0,0,$LatestVoucherNo,$SrNo+2,VOUCHER_JOURNAL,TDS_PAYABLE,TRANSACTION_CREDIT,$TDSAmount, $Comments);		
														
									$regResult9 = $this->m_register->SetLiabilityRegister(getDBFormatDate($InvoiceDate),TDS_PAYABLE,$dataVoucher3,VOUCHER_JOURNAL, TRANSACTION_CREDIT, $TDSAmount, 0);
							}
					}
				
					$VoucherType = VOUCHER_PAYMENT;
					$Payer = $PayerBank;
					if($LiableForContraEntry == 1)
					{
						$VoucherType = VOUCHER_CONTRA;
						if($ExpenseBy <> "")
						{
							$Payer = $ExpenseBy;
						}
					}
				
					if($Ref <> 0 && $this->prevRefForMultEntry == $Ref  && $MultipleEntry <> 1)
					{							
						/*if($this->m_PrevDataVoucher <> "")
						{
							$dataVoucher = $this->m_PrevDataVoucher;
							$sql = "SELECT `Debit` FROM `voucher` WHERE `id` = '".$dataVoucher."'";
							$debitAmount = $this->m_dbConn->select($sql);
							//$dAmount = $debitAmount[0]['Debit'] + $Amount;
							$dAmount = $debitAmount[0]['Debit'] + $invoiceAmount;
							$sqlUpdate = "UPDATE `voucher` SET `Debit`='".$dAmount."' WHERE `id` = '".$dataVoucher."'";
							$this->m_dbConn->update($sqlUpdate);
						}
						if($this->m_PrevLatestVoucher <> "")
						{
							$dataVoucher1 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),$data,TABLE_PAYMENT_DETAILS,
							$this->m_PrevLatestVoucher,$this->prevMultEntryDetails['SrNo'],$VoucherType,$PaidTo,TRANSACTION_CREDIT,$invoiceAmount, $Comments);	
						$this->prevMultEntryDetails['SrNo'] = $this->prevMultEntryDetails['SrNo'] + 1;
						}*/
					}
					else
					{					
						
						if($ExistPaymemtVoucher != 0)
						{
							$LatestVoucherNo = $ExistPaymemtVoucher;
						}
						else
						{	
							$LatestVoucherNo = $this->m_latestcount->getLatestVoucherNo($_SESSION['society_id']);								
						}
						$PaymentVoucherNo = $LatestVoucherNo;
						
						$dataVoucher  = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),$data,TABLE_PAYMENT_DETAILS,
					
					$LatestVoucherNo,1,$VoucherType, $Payer,TRANSACTION_DEBIT,$Amount, $Comments,$ExVoucherNo);	
																	
								
					$dataVoucher1  = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),$data,TABLE_PAYMENT_DETAILS,
					$LatestVoucherNo,2,$VoucherType,$PaidTo,TRANSACTION_CREDIT,$Amount, $Comments ,$ExVoucherNo);
					
					if($IsCallUpdtCnt == 1)
					{	
						if($IsSameCountChecked == 1)
						{
							if(in_array($PayerBank,$CashLedgers))
							{
								//$ExpectedCounter = $this->m_objUtility->GetCounter($ExtVoucherType,$PayerBank,false);
								$this->m_objUtility->UpdateExVCounter($ExtVoucherType, $ExVoucherNo, $PayerBank);
							}
							else
							{
								//$ExpectedCounter = $this->m_objUtility->GetCounter($ExtVoucherType,0,false);
								$this->m_objUtility->UpdateExVCounter($ExtVoucherType, $ExVoucherNo, 0);
							}
						}
						else
						{	
							//$ExpectedCounter = $this->m_objUtility->GetCounter($ExtVoucherType,$PayerBank,false);
							$this->m_objUtility->UpdateExVCounter($ExtVoucherType, $ExVoucherNo, $PayerBank);	
						}	
					}
					
					if($actionMode == ADD)
					{
						if($ExVoucherNo <> $SystemDefineVNo)
						{
							$ChangeMsg = 'Voucher Number '.$SystemDefineVNo.' Changed to '.$ExVoucherNo.' On Payment Insert';
							$this->m_objLog->setLog($ChangeMsg, $_SESSION['login_id'], 'Payment', $data);
						}
						
					}
					else if($actionMode == EDIT)
					{
						if($SystemDefineVNo <> $ExVoucherNo)
						{
							//when user update the payment entry  and if user change the Counter then log insert
							$ChangeMsg = 'Voucher Number '.$SystemDefineVNo.' Changed to '.$ExVoucherNo.' On Payment Updatation';
							$this->m_objLog->setLog($ChangeMsg, $_SESSION['login_id'], 'Payment', $data);		
						}
					}
					
					
					
						if($MultipleEntry == 1)
						{						
							$this->m_PrevDataVoucher = $dataVoucher;
							$this->m_PrevLatestVoucher = $LatestVoucherNo;	
							$this->prevMultEntryDetails['SrNo'] = 3; 
						}
					//}									
						$this->m_register->SetBankRegister(getDBFormatDate($VoucherDate),$Payer, $dataVoucher, $VoucherType,
						TRANSACTION_PAID_AMOUNT, $Amount, $LeafID, $data, 0, getDBFormatDate($VoucherDate), 0, getDBFormatDate($reconcileDate), $reconcileStatus, $reconcile, $return);
						
						if($LiableForContraEntry == 1)
						{
							$this->m_register->SetBankRegister(getDBFormatDate($VoucherDate), $PaidTo, $dataVoucher1, $VoucherType,
						TRANSACTION_RECEIVED_AMOUNT, $Amount, $LeafID, $data, 0, getDBFormatDate($VoucherDate), 0, getDBFormatDate($reconcileDate), $reconcileStatus, $reconcile, $return);
						}
						else
						{
							/*if($bIsCreditor == 1)
							{
								$LiabilityID = $this->m_register->SetLiabilityRegister(getDBFormatDate($VoucherDate),$PaidTo,$dataVoucher1,
								VOUCHER_PAYMENT, TRANSACTION_DEBIT, $Amount, 0);
							}
							else
							{
								$ExpenseID = $this->m_register->SetExpenseRegister($PaidTo,getDBFormatDate($VoucherDate), $dataVoucher1, 
							VOUCHER_PAYMENT, TRANSACTION_DEBIT, $Amount,0);
							}*/
						
						
							if($PaidToGroupID == LIABILITY)
							{
								$LiabilityID = $this->m_register->SetLiabilityRegister(getDBFormatDate($VoucherDate),$PaidTo,$dataVoucher1,VOUCHER_PAYMENT, TRANSACTION_DEBIT, $Amount, 0);
							}
							else if($PaidToGroupID == ASSET)
							{
								$AssetID = $this->m_register->SetAssetRegister(getDBFormatDate($VoucherDate), $PaidTo, $dataVoucher1,VOUCHER_PAYMENT, TRANSACTION_DEBIT, $Amount, 0);	
							}
							else if($PaidToGroupID == INCOME)
							{
								$IncomeID = $this->m_register->SetIncomeRegister($PaidTo, getDBFormatDate($VoucherDate), $dataVoucher1,VOUCHER_PAYMENT, TRANSACTION_DEBIT, $Amount);
							}
							else if($PaidToGroupID == EXPENSE)
							{
								$ExpenseID = $this->m_register->SetExpenseRegister($PaidTo,getDBFormatDate($VoucherDate), $dataVoucher1,VOUCHER_PAYMENT, TRANSACTION_DEBIT, $Amount,0);
							}
						
						
						}	
							
						//return "success";
					}
				
			}
			else
			{
				return '-2';
				//echo "Cheque ".$ChequeNumber." already issued,";
				$this->prevRefForMultEntry = 0;
				$this->m_dbConn->rollback();
				//echo "DoneRollBack";
			}
			if(!$bSkipBeginTrnx)
			{
				$this->m_dbConn->commit();
			}
			//echo "actionType:".$this->actionType;
			if($this->actionType == ADD)
			{
				//echo "add type";
				$this->m_objLog->setLog($this->ADDEntryTracker, $_SESSION['login_id'], 'paymentdetails', $data);
			}
			if($this->actionType == EDIT)
			{
				return $data;
			}
			if($this->actionType == "IMPORT")
			{
				return "Import Successful";
			}
			
		}
		catch(Exception $exp)
		{
			$this->m_dbConn->rollback();
			return $exp;
		}
	}
	public function CommitTransaction()
	{
		$this->m_dbConn->commit();
	}
	public function BeginTransaction()
	{
		$this->m_dbConn->begin_transaction();
	}
	public function RollBackCurrentTransaction()
	{
		$this->m_dbConn->rollback();
			
	}
	
	public function comboboxEx($query)
	{
		$id=0;
		//echo "<script>alert('test')<//script>";
		$str.="<option value=''>Please Select</option>";//<option value='".CREATE_NEW_LEDGER."'>Create New Ledger</option>";*/
	$data = $this->m_dbConn->select($query);
	//print_r($data);
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
						
						$str.="<option value=".$v.' '.$sel.">";
					}
					else
					{
						//$str.=$v."</OPTION>";
						$str.= str_replace($vowels, ' ', $v)."</option>";
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
		if($_REQUEST["bankid"] != "-1")
		{
		$thheader = array('ChequeDate','ChequeNumber','Amount','PaidTo','PayerBank','Comments','VoucherDate');
		}
		else
		{
			$thheader = array('ChequeDate','ChequeNumber','Amount','PaidTo','Comments','VoucherDate');
		}
		$this->display_pg->edit		= "getPaymentDetails";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "PaymentDetails.php";

		$res = $this->display_pg->display_new($rsas, false);
		return $res;
	}
	public function pgnation($IsReadonlyPage = false)
	{
		//$sql1 = "select id,`ChequeDate`,`ChequeNumber`,`Amount`,`PaidTo`,`EnteredBy`,`PayerBank`,`Comments`,`VoucherDate` from PaymentDetails where status='Y'";
		//$sql1 = "select pay.id, pay.ChequeDate,pay.ChequeNumber,pay.Amount, leg.ledger_name as temp1, log.name, leg.ledger_name,pay.Comments, pay.VoucherDate from PaymentDetails as pay JOIN ledger as leg ON pay.PaidTo = leg.id and pay.PayerBank = leg.id JOIN login as log on pay.EnteredBy = log.login_id where pay.status='Y' and pay.PayerBank=".$_REQUEST["bankid"]." and leg.society_id=".$_SESSION['society_id'];
		if($_REQUEST["bankid"] != "-1" && $_REQUEST["LeafID"]!=-1)
		{			
			 $sql1 = "select pay.id, IF(pay.IsMultipleEntry = 1, '<i class=\'fa  fa-check\'  style=\'font-size:10px;font-size:1.75vw;color:#6698FF;\'></i>', ''), DATE_FORMAT(pay.ChequeDate, '%d-%m-%Y'), IF(pay.ChequeNumber = '-1','Cash', pay.ChequeNumber) as ChequeNumber,pay.Amount, leg.ledger_name as temp1,  leg1.ledger_name,pay.Comments, DATE_FORMAT(pay.VoucherDate, '%d-%m-%Y'), DATE_FORMAT(pay.InvoiceDate, '%d-%m-%Y'),pay.TDSAmount  from paymentdetails as pay JOIN ledger as leg ON pay.PaidTo = leg.id JOIN ledger as leg1 ON pay.PayerBank = leg1.id where pay.status='Y' and pay.PayerBank='".$_REQUEST["bankid"]."' and pay.ChqLeafID='".$_REQUEST["LeafID"]."' and leg.society_id=".$_SESSION['society_id'];
		}
		else if($_REQUEST["LeafID"] == -1) 
		{
	    	 $sql1 = "select pay.id, IF(pay.ChequeNumber = '-1', 'Cash', pay.ChequeNumber) as ChequeNumber,vcr.ExternalCounter as VoucherNumber, pay.Amount, leg.ledger_name as temp1,  pay.Comments, DATE_FORMAT(pay.VoucherDate, '%d-%m-%Y') from paymentdetails as pay JOIN ledger as leg ON pay.PaidTo = leg.id JOIN voucher as vcr ON vcr.RefNo = pay.id where pay.status='Y'  and pay.ChqLeafID='-1' and vcr.By = '".$_REQUEST['bankid']."' and leg.society_id=".$_SESSION['society_id'];
		}
		else  
		{
			$sql1 = "select pay.id, DATE_FORMAT(pay.ChequeDate, '%d-%m-%Y'), IF(pay.ChequeNumber = '-1', 'Cash', pay.ChequeNumber) as ChequeNumber,pay.Amount, leg.ledger_name as temp1,  pay.Comments, DATE_FORMAT(pay.VoucherDate, '%d-%m-%Y') from paymentdetails as pay JOIN ledger as leg ON pay.PaidTo = leg.id where pay.status='Y' and pay.PayerBank='-1' and pay.ChqLeafID='-1' and leg.society_id=".$_SESSION['society_id'];
		}
		
		if(isset($_REQUEST["edt"]) && $_REQUEST["edt"] <> "")
		{
			$sql1 .= "  and pay.id = '".$_REQUEST["edt"]."'";	
		}
		
		if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
		{
			$sql1 .= "  and pay.VoucherDate BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
		}
		$sql1 .= " order by pay.VoucherDate DESC";
		
		/*$cntr = "select count(status) as cnt from paymentdetails where status='Y'";

		$this->display_pg->sql1		= $sql1;
		$this->display_pg->cntr1	= $cntr;
		$this->display_pg->mainpg	= "PaymentDetails.php";

		$limit	= "50";
		$page	= $_REQUEST['page'];
		$extra	= "";

		$res	= $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;*/
		$result = $this->m_dbConn->select($sql1);
		
		if($_REQUEST["bankid"] != "-1" && $_REQUEST["LeafID"]!=-1)
		{
		$thheader = array('Multiple?','ChequeDate','ChequeNumber','Amount','PaidTo','PayerBank','Comments','VoucherDate','InvoiceDate','TDSAmount');
		}
		else if($_REQUEST["LeafID"]== -1)
		{
			//** We are setting the header for datatable shown in cash payemnt
			$thheader = array('ChequeNumber','VoucherNumber','Amount','PaidTo','Comments','VoucherDate');
		}
		else
		{
			$thheader = array('ChequeNumber','VoucherNumber','Amount','PaidTo','Comments','VoucherDate');
		}
		
		$returnChequeQuery = "SELECT `IsReturnChequeLeaf` FROM `chequeleafbook` WHERE `id` = '".$_REQUEST["LeafID"]."'";		
		$isReturnCheque = $this->m_dbConn->select($returnChequeQuery);
		
		$this->display_pg->edit		= "getPaymentDetails";
		$this->display_pg->print		= "getPaymentDetails";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "PaymentDetails.php";
		
		if($IsReadonlyPage == false)
		{
			if($_REQUEST["LeafID"]!=-1)
			{
				/*if($isReturnCheque[0]['IsReturnChequeLeaf'] == 1)
				{
					$res = $this->display_pg->display_datatable($result, false, false);
				}
				else*/
				{
					$res = $this->display_pg->display_datatable($result, false, true,false,true);
				}
			}		
			else
			{
				$res = $this->display_pg->display_datatable($result, true, true,false,true);
			}
		}
		else
		{
			if($_REQUEST["LeafID"]!=-1)
			{
				if($isReturnCheque[0]['IsReturnChequeLeaf'] == 1)
				{
					$res = $this->display_pg->display_datatable($result, false, false,false,false,false);
				}
				else
				{
					$res = $this->display_pg->display_datatable($result, false, true,false,true,false);
				}
			}		
			else
			{
				$res = $this->display_pg->display_datatable($result, true, true,false,true,false);
			}
				
		}
		//$result = $this->m_dbConn->select($sql1);
		//$this->paymentDetailsTable($result);
	}
	
	
	public function getReconcileStatus($VoucherNo,$BankID)
	{
		$bankDetails_query = "SELECT bk.`Reconcile Date`, bk.`ReconcileStatus`, bk.`Reconcile`, bk.`Return` FROM `voucher` as v JOIN `bankregister` as bk ON v.id = bk.VoucherID where v.VoucherNo ='".$VoucherNo."' and v.`By` = '".$BankID."'";
		
		$bankDetails_Result = $this->m_dbConn->select($bankDetails_query);
		
		return $bankDetails_Result;
	}
	
	
	public function selecting()
	{
		$show_in_jvformat = 0;	
		$sql = "select p.id, p.ModeOfPayment, p.ChequeDate, p.ChequeNumber, DATE_FORMAT(p.VoucherDate, '%d-%m-%Y') as VoucherDate, p.Amount
		,p.PaidTo, p.ExpenseBy, p.Timestamp, p.EnteredBy, p.PayerBank, p.ChqLeafID, p.Comments, p.InvoiceDate, p.TDSAmount, p.VoucherID, p.VoucherTypeID, p.status, v.ExternalCounter from paymentdetails as p JOIN voucher as v ON p.id = v.RefNo where p.id='".$_REQUEST['PaymentDetailsId']."' and v.By = '".$_REQUEST['BankId']."' ";
		$res = $this->m_dbConn->select($sql);
		
		
		$sqlVoucher = "select v.Note,v.Date, ins.NewInvoiceNo as InvoiceNo,ins.InvoiceStatusID, ins.AmountReceived as InvoceAmount,ins.TDSAmount from `voucher` as v join `invoicestatus` as ins on v.VoucherNo=ins.InvoiceClearedVoucherNo where v.RefNo ='".$_REQUEST['PaymentDetailsId']."' and `RefTableID` = '".TABLE_PAYMENT_DETAILS."' group by ins.InvoiceStatusID";
		$data2 = $this->m_dbConn->select($sqlVoucher);
		
		 $sqlVoucherNo= "select VoucherNo from voucher where RefNo='".$_REQUEST['PaymentDetailsId']."' and RefTableID='".TABLE_PAYMENT_DETAILS."'";
		 
			$Paymentvoucher = $this->m_dbConn->select($sqlVoucherNo);	
			
			$res[0]['PaymentVoucherNo'] = $Paymentvoucher[0]['VoucherNo'];
			
			 if(sizeof($Paymentvoucher) > 2)
			 {
				 $show_in_jvformat = 1;
				 	
			 }
			 $res[0]['show_in_jvformat'] = $show_in_jvformat;
			
		if(sizeof($data2) > 0)
		{
		
			for($iCntRow=0; $iCntRow<sizeof($data2);$iCntRow++)
			{
				$InvoiceNo=$data2[$iCntRow]['InvoiceNo'];
				$InvoiceID=$data2[$iCntRow]['InvoiceStatusID'];
			
				$sqlLegerName="SELECT ds.*, v.date,v.Note,l.ledger_name,l.id FROM `invoicestatus` as ds join `voucher` as v on ds.InvoiceRaisedVoucherNo=v.VoucherNo join ledger as l on v.By=l.id where InvoiceStatusID='".$InvoiceID."'";
				$data3 = $this->m_dbConn->select($sqlLegerName);
					
				$data3[0]['date']=getDisplayFormatDate($data3[0]['date']);
				$data2[$iCntRow]['ExpenseDetails'] = $data3[0];
			}
			$res[0]['InvoiceData'] = json_encode($data2);
		}
		return $res;
	}
	
	public function deleting()
	{
		$sql = "update paymentdetails set status='N' where id='".$_REQUEST['PaymentDetailsId']."'";
		$res = $this->m_dbConn->update($sql);
	}
	
	public function deletePaymentDetails($ChequeDate,$ChequeNumber,$VoucherDate,$Amount,$PaidTo,$ExpenseBy,$PayerBank,$ChqLeafID,$Comments,$InvoiceDate,$TDSAmount,$RowID,$isCleaner=false,$Ref=0,$prevRef=0)
	{	
		$IsSuccess = true;
		try
		{
			$this->m_dbConn->begin_transaction();
		
			$VoucherIDArray = array();
			$Id_Array = array();
			$paymentQuery = "SELECT * from `paymentdetails` where `id`=".$RowID." ";
			$paymentRes = $this->m_dbConn->select($paymentQuery);				
			$sql = "delete from `paymentdetails` where `id`=".$RowID." ";
		
			$this->DELETEEntryTracker = "Record Deleted at($RowID)";
			$voucher_select = "select `id`,`VoucherNo` from `voucher` where `RefNo`=".$RowID." and `RefTableID`=".TABLE_PAYMENT_DETAILS." ";
			$res00 = $this->m_dbConn->select($voucher_select);
			//print_r($res00);
			if($res00 > 0)
			{ 
			$clearVoucher="select `InvoiceClearedVoucherNo` from `invoicestatus` where InvoiceClearedVoucherNo='".$res00[0]['VoucherNo']."' ";
			$clearVoucher1 = $this->m_dbConn->select($clearVoucher);
			if($clearVoucher1 > 0)
			{
			 for($iVoucher=0;$iVoucher < sizeof($clearVoucher1);$iVoucher++)
			 {
				 $deletQuery="update `invoicestatus` set InvoiceClearedVoucherNo='',AmountReceived='' where `InvoiceClearedVoucherNo`='".$clearVoucher1[$iVoucher]['InvoiceClearedVoucherNo']."'";
				$DocumentStatus = $this->m_dbConn->update($deletQuery);
				//echo "delete document";
			 }
			}
		    }
			//die();
			if($Ref <> 0 && $prevRef <> 0 && $Ref == $prevRef)
			{
				$VoucherID = $res00[0]['id'];
				$res = $this->m_dbConn->delete($sql);			
			}
			else
			{
				$VoucherID = $res00[1]['id'];			
			//}
				$voucher_delete = "delete from `voucher` where `RefNo`=".$RowID." and `RefTableID`=".TABLE_PAYMENT_DETAILS." ";			
				if($isCleaner == false)
				{
					//echo '<br>Payment Details Entry Deleted ';
					$res = $this->m_dbConn->delete($sql);
					$res01 = $this->m_dbConn->delete($voucher_delete);
				}
				else
				{
					echo "<br>**Error**<font color='#FF0000'>".$sql."</font>";
					echo "<br>**Error**<font color='#FF0000'>".$voucher_delete."</font>";
				}		
				foreach($res00 as $key => $val)
				{
					if($res00[$key]['id'] == "" || $res00[$key]['id'] == 0)
					{
						array_push($Id_Array,-1);
					}
					else
					{
						array_push($Id_Array,$res00[$key]['id']);
					}
					//array_push($Id_Array,$res00[$key]['id']);
				}			
				$bankIDArray=implode(',', $Id_Array);
				$bankregi_delete = '';
				for($i = 0; $i < sizeof($Id_Array); $i++)
				{
					if($Id_Array[$i] <> "" &&  $Id_Array[$i] > 0)
					{
						//$bankregi_delete = "delete from `bankregister` where `VoucherID` IN ($bankIDArray) ";
						
						if($bankregi_delete == "")
						{
							$bankregi_delete .= "delete from `bankregister` where `Is_Opening_Balance` = 0 AND `VoucherID`='".$Id_Array[$i]."'  ";
						}
						else
						{
							$bankregi_delete .= " OR `VoucherID`='".$Id_Array[$i]."'  ";
						}
					}
				}
	
				if($isCleaner == false && $bankregi_delete <> "")
				{
					$res02 = $this->m_dbConn->delete($bankregi_delete);
				}
				else
				{
					echo "<br>**Error**<font color='#FF0000'>".$bankregi_delete."</font>";
					
				}
			}
			if($ExpenseBy <> 0)
			{
				//need to fetch voucher no as per voucherID in paymentdetails:VoucherID
				if($paymentRes[0]['VoucherID'] <> 0)
				{
					$query = "SELECT `VoucherNo` FROM `voucher` WHERE `id` = '".$paymentRes[0]['VoucherID']."'";
					$jvNo = $this->m_dbConn->select($query);
					$JVoucherNo=$jvNo[0]['VoucherNo'];	
						
				}
				else
				{
					$JVoucherNo=$res00[0]['VoucherNo']-1;
				}
				
				$jvoucher_select = "select `id` from `voucher` where `VoucherNo`=".$JVoucherNo." ";
				$VoucherData02 = $this->m_dbConn->select($jvoucher_select);
				$voucherID01 = $VoucherData02[0]['id'];	
				if(sizeof($VoucherData02) > 0)
				{
					for($i = 0; $i < sizeof($VoucherData02); $i++)
					{
						if($VoucherData02[$i]['id'] == "" || $VoucherData02[$i]['id'] == 0)
						{
							array_push($VoucherIDArray,'-1');
						}
						else
						{
							array_push($VoucherIDArray,$VoucherData02[$i]['id']);
						}
					}
					
				}
				$regDelete15 ='';
				$voucher_tdsdelete = '';
				if($TDSAmount <> 0)
				{
					$voucherID03 = $VoucherData02[2]['id'];
					if($VoucherData02[2]['id'] > 0 && $VoucherData02[2]['id'] <> "")
					{
						$regDelete15 = "delete from `liabilityregister` where `Is_Opening_Balance` = 0 AND `VoucherID`='".$voucherID03."' and `Credit`='".$TDSAmount."' ";
						$voucher_tdsdelete = "delete from `voucher` where  `id`= '".$VoucherData02[2]['id']."' ";
					}
					
					if($isCleaner == false && sizeof($VoucherData02) > 0)
					{
						if($VoucherData02[2]['id'] > 0 && $VoucherData02[2]['id'] <> "")
						{
							$regResult15 = $this->m_dbConn->delete($regDelete15);
							$VoucherDatatds = $this->m_dbConn->delete($voucher_tdsdelete);
						}
					}
					else if(sizeof($VoucherData02) > 0)
					{
						if($VoucherData02[2]['id'] > 0 && $VoucherData02[2]['id'] <> "")
						{
							echo "<br>**Error**<font color='#FF0000'>".$regDelete15."</font>";
							echo "<br>**Error**<font color='#FF0000'>".$voucher_tdsdelete."</font>";
						}
					}
				}
				$voucher_delete001 ='';
				if(sizeof($VoucherData02) > 0)
				{
					
					for($m = 0; $m < 2; $m++)
					{
						//$voucher_delete001="delete from `voucher` where  `id`='".$VoucherData02[0]['id']."' OR `id`='".$VoucherData02[1]['id']."' ";
						if($VoucherData02[$m]['id'] <> "" && $VoucherData02[$m]['id'] > 0)
						{
							if($voucher_delete001 == "")
							{
								$voucher_delete001 = "delete from `voucher` where  `id`='".$VoucherData02[$m]['id']."' ";
							}
							else
							{
								$voucher_delete001 .= " OR `id`='".$VoucherData02[$m]['id']."'  ";
							}
						}
					}
				}
				if($isCleaner == false && sizeof($VoucherData02) > 0)
				{
					$VoucherData001 = $this->m_dbConn->delete($voucher_delete001);
				}
				else if(sizeof($VoucherData02) > 0)
				{
					echo "<br>**Error**<font color='#FF0000'>".$voucher_delete001."</font>";
				}
				$regDelete1 = '';
				$regDelete2 = '';
				$regDelete3 = '';
				$regDelete4 = '';
				if($TDSAmount <> 0)
				{
					$regDelete1 = "delete from `liabilityregister` where `Is_Opening_Balance` = 0 AND `VoucherID`='".$VoucherID."' ";
					if(sizeof($VoucherIDArray) > 0)
					{
						for($m = 0; $m < sizeof($VoucherIDArray); $m++)
						{
							//$regDelete1 .="  OR `VoucherID`='".$VoucherIDArray[0]."' OR `VoucherID`='".$VoucherIDArray[1]."' OR `VoucherID`='".$VoucherIDArray[2]."' ";
							if($VoucherIDArray[$m] <> "" && $VoucherIDArray[$m] > 0)
							{
								$regDelete1 .="  OR `VoucherID`='".$VoucherIDArray[$m]."' ";
							}
						}
					}
					if($isCleaner == false)
					{
						$regResult1 = $this->m_dbConn->delete($regDelete1);
					}
					else
					{
						echo "<br>**Error**<font color='#FF0000'>".$regDelete1."</font>";
					}
				}
				else
				{
					$regDelete1 = "delete from `liabilityregister` where `Is_Opening_Balance` = 0 AND `VoucherID`='".$VoucherID."' ";
					if(sizeof($VoucherIDArray) > 0)
					{
						//for($m = 0; $m < (sizeof($VoucherIDArray)-1); $m++)
						for($m = 0; $m < sizeof($VoucherIDArray); $m++)
						{
							if($VoucherIDArray[$m] <> "" && $VoucherIDArray[$m] > 0)
							{
								//$regDelete1 .=" OR `VoucherID`='".$VoucherIDArray[0]."' OR `VoucherID`='".$VoucherIDArray[1]."' ";
								$regDelete1 .=" OR `VoucherID`='".$VoucherIDArray[$m]."' ";
							}
						}
					}
					if($isCleaner == false)
					{
						$regResult1 = $this->m_dbConn->delete($regDelete1);
					}
					else
					{
						echo "<br>**Error**<font color='#FF0000'>".$regDelete1."</font>";
					}
				}
				$regDelete2 = "delete from `assetregister` where `Is_Opening_Balance` = 0 AND `VoucherID`='".$VoucherID."'  ";
				$regDelete3 = "delete from `incomeregister` where `VoucherID`='".$VoucherID."' ";
				$regDelete4 = "delete from `expenseregister` where `VoucherID`='".$VoucherID."' ";
			
				if(sizeof($VoucherIDArray) > 0)
				{
					//for($m = 0; $m < (sizeof($VoucherIDArray)-1); $m++)
					for($m = 0; $m < sizeof($VoucherIDArray); $m++)
					{
						/*$regDelete2 .= " OR `VoucherID`='".$VoucherIDArray[0]."' OR `VoucherID`='".$VoucherIDArray[1]."' ";
						$regDelete3 .= " OR `VoucherID`='".$VoucherIDArray[0]."' OR `VoucherID`='".$VoucherIDArray[1]."' ";
						$regDelete4 .= " OR `VoucherID`='".$VoucherIDArray[0]."' OR `VoucherID`='".$VoucherIDArray[1]."' ";*/
						if($VoucherIDArray[$m] <> "" && $VoucherIDArray[$m] > 0)
						{
							$regDelete2 .= " OR `VoucherID`='".$VoucherIDArray[$m]."' ";
							$regDelete3 .= " OR `VoucherID`='".$VoucherIDArray[$m]."' ";
							$regDelete4 .= " OR `VoucherID`='".$VoucherIDArray[$m]."' ";
						}
					}
				}
				if($isCleaner == false)
				{
					$regResult2 = $this->m_dbConn->delete($regDelete2);	
					$regResult3 = $this->m_dbConn->delete($regDelete3);
					$regResult4 = $this->m_dbConn->delete($regDelete4);	
				}
				else
				{
					echo "<br>**Error**<font color='#FF0000'>".$regDelete2."</font>";
					echo "<br>**Error**<font color='#FF0000'>".$regDelete3."</font>";
					echo "<br>**Error**<font color='#FF0000'>".$regDelete4."</font>";	
				}
			}
			else
			{
				$regDelete1 = "delete from `liabilityregister` where `Is_Opening_Balance` = 0 AND `VoucherID`='".$VoucherID."' ";
				$regDelete2 = "delete from `assetregister` where `Is_Opening_Balance` = 0 AND `VoucherID`='".$VoucherID."'  ";
				$regDelete3 = "delete from `incomeregister` where `VoucherID`='".$VoucherID."'  ";
				$regDelete4 = "delete from `expenseregister` where `VoucherID`='".$VoucherID."'  ";
				
				for($i = 0; $i < sizeof($Id_Array); $i++)
				{
					if($Id_Array[$i] <> "" &&  $Id_Array[$i] > 0)
					{
						if($VoucherID <> $Id_Array[$i])
						{
							$regDelete1 .= " OR `VoucherID`='".$Id_Array[$i]."' ";
							$regDelete2 .= " OR `VoucherID`='".$Id_Array[$i]."' ";
							$regDelete3 .= " OR `VoucherID`='".$Id_Array[$i]."' ";
							$regDelete4 .= " OR `VoucherID`='".$Id_Array[$i]."' ";		
						}
					}
				}
				if($isCleaner == false)
				{
					$regResult1 = $this->m_dbConn->delete($regDelete1);
					$regResult2 = $this->m_dbConn->delete($regDelete2);	
					$regResult3 = $this->m_dbConn->delete($regDelete3);
					$regResult4 = $this->m_dbConn->delete($regDelete4);	
				}
				else
				{
					echo "<br>**Error**<font color='#FF0000'>".$regDelete1."</font>";
					echo "<br>**Error**<font color='#FF0000'>".$regDelete2."</font>";
					echo "<br>**Error**<font color='#FF0000'>".$regDelete3."</font>";
					echo "<br>**Error**<font color='#FF0000'>".$regDelete4."</font>";
				}
		
				
			}
		
			$this->m_dbConn->commit();
			if($this->actionType == DELETE)
			{
				$this->DELETEEntryTracker .= "<br>PaidTo | ChequeNumber | ChequeDate | Amount | PayerBank | Comments | VoucherDate | InvoiceDate | TDSAmount | LeafID | DoubleEntry | ExpenseBy | RowID | ModeOfPaymentPre<br>";
				$this->DELETEEntryTracker .= $PaidTo."|".$ChequeNumber."|".$ChequeDate."|".$Amount."|".$PayerBank."|".$Comments."|".$VoucherDate."|".$InvoiceDate."|".$TDSAmount."|".$LeafID."|".$DoubleEntry."|".$ExpenseBy."|".$RowID."|".$ModeOfPaymentPre;
				$this->m_objLog->setLog($this->DELETEEntryTracker, $_SESSION['login_id'], 'paymentdetails',$RowID);
			}
		
		}
		catch(Exception $exp)
		{
			$this->m_dbConn->rollback();
			$IsSuccess = false;
			
		}
		return $IsSuccess;
		
	}
	
	public function UpdatePaymentDetails($PaidTo,$ChequeNumber,$ExVoucherNumber,$SystemDefineVNo,$IsCallUpdtCnt,$ChequeDate,$Amount,$PayerBank,$Comments,$VoucherDate,$InvoiceDate,$TDSAmount,$LeafID,$DoubleEntry,$ExpenseBy,$RowID = 0, $ModeOfPayment = "", $reconcileDate = 0, $reconcileStatus = 0, $reconcile = 0, $return = 0, $MultipleEntry = 0, $Ref = 0, $InvoiceAmount = 0, &$PaymentVoucherNo,$ExistPaymemtVoucher,$bSkipBeginTrnx,$AllowCheckDelete = true, $bcash)
	{
		$IsSuccess = true;
		//$AllowCheckDelete = true;
		$isMultEntry = false;	
		try
		{
			
			if(!$bSkipBeginTrnx)
			{
				$this->m_dbConn->begin_transaction();
			}
			if($RowID == 0)
			{
				/*if($bcash == false && $LeafID== -1)
				{
					 $refId="select * from voucher where VoucherNo='".$ExistPaymemtVoucher."'";
					$sqlData = $this->m_dbConn->select($refId);
					
					 $PaymentDetails = "select * from `paymentdetails` where `id`=".$sqlData[0]['RefNo']."";
					//die();
				}
				/*else
				{
				$PaymentDetails = "select * from `paymentdetails` where `ChequeNumber`=".$ChequeNumber." and `ChqLeafID`=".$LeafID." and `PayerBank`=".$PayerBank." ";
				}*/
			}
			else
			{
				
				 $PaymentDetails = "select * from `paymentdetails` where `id`= ".$RowID." ";
				
				//check while editing user has change cheque number and entered cheque number is already exists except current cheque number  
				/*if($ModeOfPayment == 0 && $LeafID!= -1)
				{
					
					$sqlCheckExists = "select * from `paymentdetails` where `ChequeNumber` = '".$ChequeNumber."' and `id` <> ".$RowID." and `PayerBank`='".$PayerBank."' AND `Reference` <> '".$RowID."' ";
					$sqlData = $this->m_dbConn->select($sqlCheckExists);
					
					if($sqlData <> "")
					{
						$AllowCheckDelete = false;
						$IsSuccess = false;	
					}
				}*/
				
			}
			if($Ref <> 0 && $this->prevRefForMultEntry == $Ref)
			{
				$isMultEntry = true;
			}
			if($AllowCheckDelete == true || $isMultEntry == true)
			{
				//echo '<br>Test 9';
				$flag = false;	
				if($RowID <> 0 && $RowID <> "")
				{
					$flag = true;
					$Data = $this->m_dbConn->select($PaymentDetails);
				}															
				/*
				
				if($Data <> "")
				{
					$flag = true;					
					if($RowID == 0 && $isMultEntry == true)
					{
						$flag = false;						
					}
				}*/
				
				if($flag == true)
				{
					
					/*if($RowID == 0)
					{
						$ID = $Data[0]['id'];
					}
					else*/
					{
						$ID = $RowID;	
					}
					
					$ChequeDatePre = $Data[0]['ChequeDate'];
					$VoucherDatePre = $Data[0]['VoucherDate'];
					$AmountPre = $Data[0]['Amount'];
					$PaidToPre = $Data[0]['PaidTo'];
					$ExpenseByPre = $Data[0]['ExpenseBy'];
					$PayerBankPre = $Data[0]['PayerBank'];
					$ChqLeafIDPre = $Data[0]['ChqLeafID'];
					$CommentsPre = $Data[0]['Comments'];
					$InvoiceDatePre = $Data[0]['InvoiceDate'];
					$TDSAmountPre = $Data[0]['TDSAmount'];
					$ModeOfPaymentPre = $Data[0]['ModeOfPayment'];
					$InvoiceAmountPre = $Data[0]['InvoiceAmount'];
					
					$str = "\r\nPaidTo | ChequeNumber | ChequeDate | Amount | PayerBank | Comments | VoucherDate | InvoiceDate | TDSAmount | LeafID | DoubleEntry | ExpenseBy | RowID | ModeOfPaymentPre";
					$str1 = "\r\n".$PaidToPre."|".$ChequeNumber."|".$ChequeDatePre."|".$AmountPre."|".$PayerBankPre."|".$CommentsPre."|".$VoucherDatePre."|".$InvoiceDatePre."|".$TDSAmountPre."|".$LeafID."|".$DoubleEntry."|".$ExpenseByPre."|".$RowID."|".$ModeOfPaymentPre."|".$InvoiceAmountPre;
					$this->EDITEntryTracker = "\r\nPrev Record:".$str."<br>";
					$this->EDITEntryTracker .= $str1."<br>";
										
					if($AllowCheckDelete == true || $isMultEntry == true)
					{
						
						if($bcash == false)
						{				
							$Status = $this->deletePaymentDetails($ChequeDatePre,$ChequeNumber,$VoucherDatePre,$AmountPre,$PaidToPre,$ExpenseByPre,$PayerBankPre,$ChqLeafIDPre,$CommentsPre,$InvoiceDatePre,$TDSAmountPre,$ID, false, $Ref, $this->prevRefForMultEntry);
						}
						else 
						{
							$Status = true;
						}
					}
					else
					{
						$Status = false;
								
					}
					
					if($Status)
					{
						$this->EDITEntryTracker .="\r\npayment record deleted successfully.";
						$result = $this->AddNewValues($LeafID, 0, $PaidTo, $ChequeNumber, $ChequeDate, $ExVoucherNumber, $SystemDefineVNo,$IsCallUpdtCnt, $Amount, $PayerBank, $Comments, $VoucherDate, $ExpenseBy, $DoubleEntry,$InvoiceDate,$TDSAmount,$ModeOfPayment, $reconcileDate, $reconcileStatus, $reconcile, $return, $MultipleEntry, $Ref,$ID,$InvoiceAmount,$PaymentVoucherNo,$ExistPaymemtVoucher,EDIT, $bSkipBeginTrnx);	
						if($result <> "")
						{
							$str2 = "\r\n".$PaidTo."|".$ChequeNumber."|".$ChequeDate."|".$Amount."|".$PayerBank."|".$Comments."|".$VoucherDate."|".$InvoiceDate."|".$TDSAmount."|".$LeafID."|".$DoubleEntry."|".$ExpenseBy."|".$result."|".$ModeOfPayment;
							$this->EDITEntryTracker .="\r\nnew record added at($result):".$str2;
						}
					}
					
					$this->m_objLog->setLog($this->EDITEntryTracker, $_SESSION['login_id'], "paymentdetails", $RowID);
				}
				else
				{
					$result = $this->AddNewValues($LeafID, 0, $PaidTo, $ChequeNumber, $ChequeDate, $ExVoucherNumber,$SystemDefineVNo,$IsCallUpdtCnt, $Amount, $PayerBank, $Comments, $VoucherDate, $ExpenseBy, $DoubleEntry,$InvoiceDate,$TDSAmount,$ModeOfPayment, $reconcileDate, $reconcileStatus, $reconcile, $return, $MultipleEntry, $Ref,0,$InvoiceAmount,$PaymentVoucherNo,$ExistPaymemtVoucher,EDIT,$bSkipBeginTrnx);
					$str2 = $PaidTo."|".$ChequeNumber."|".$ChequeDate."|".$Amount."|".$PayerBank."|".$Comments."|".$VoucherDate."|".$InvoiceDate."|".$TDSAmount."|".$LeafID."|".$DoubleEntry."|".$ExpenseBy."|".$result."|".$ModeOfPayment;
					$this->EDITEntryTracker .="new record added at($result):".$str2."<br>";
				}
				if($MultipleEntry == 1)
				{
					$this->prevRefForMultEntry = $Ref;				
				}
			}
			$this->m_dbConn->commit();
			return $result;
		}
		catch(Exception $exp)
		{
			$this->m_dbConn->rollback();
			$IsSuccess = false;
		}
		
	echo  $IsSuccess;
	return $result;	
		
	}
	
	function ShowPopupData($paidTo,$PaidVoucher)
	{
		//$sql="Select * from `documentstatus` ";
		//$sql="SELECT ds.*, v.date FROM `documentstatus` as ds join `voucher` as v on ds.DocNo=v.VoucherNo group by v.VoucherNo";
		$sql="SELECT ds.*,v.ExternalCounter,v.VoucherNo,v.date,l.ledger_name,l.id,v.Note FROM `invoicestatus` as ds join `voucher` as v on ds.InvoiceRaisedVoucherNo=v.VoucherNo join ledger as l on v.By=l.id where (ds.InvoiceClearedVoucherNo='".$PaidVoucher."' OR ds.InvoiceClearedVoucherNo='') group by v.VoucherNo";

		$sqlData = $this->m_dbConn->select($sql);
		
		$sqlDataFinal = array();
		for($k=0;$k < sizeof($sqlData);$k++)
		{   
		  $sqlPaidTo="SELECT ds.InvoiceRaisedVoucherNo,l.ledger_name,l.id FROM `invoicestatus` as ds join `voucher` as v on ds.InvoiceRaisedVoucherNo=v.VoucherNo join ledger as l on v.To=l.id where ds.InvoiceRaisedVoucherNo='".$sqlData[$k]['InvoiceRaisedVoucherNo']."' and v.To='".$paidTo."' group by v.VoucherNo";
			$sqlData1 = $this->m_dbConn->select($sqlPaidTo);
			//print_r($sqlData1);
			if($sqlData1 <> '')
			{
				
				$sqlTDS="SELECT ins.TDSVoucherNo,v.VoucherNo,l.ledger_name,l.id FROM `invoicestatus` as ins join voucher as v on v.VoucherNo=ins.TDSVoucherNo join ledger as l on v.To=l.id where v.VoucherNo='".$sqlData[$k]['TDSVoucherNo']."' ";
				$sqlData2 = $this->m_dbConn->select($sqlTDS);
			
				$sqlData[$k]['tds_ledger'] = $sqlData2[0]['id']; 
				array_push($sqlDataFinal, $sqlData[$k]);	
			}
			
		}
		
		
		return $sqlDataFinal;
	}
	
	
	function TDSPayble($paidto)
	{ 
		if($paidto==TDS_PAYABLE)
		{ 
		
		 $TDSPayble ="select SUM(`Credit`-`Debit`) as 'sum' from `liabilityregister` where LedgerID='".TDS_PAYABLE."'";
		$sqlData2 = $this->m_dbConn->select($TDSPayble);
		}
		//print_r($sqlData2);
	    return $sqlData2;
	}
	
	function fetchComment($clearVoucher)
	{
		//echo $query="select * from `voucher` where `To`='".$paidTo."'";
		 $query="select v.`Note` from `voucher` as v join `invoicestatus` as ds on ds.InvoiceClearedVoucherNo=v.VoucherNo where InvoiceClearedVoucherNo='".$clearVoucher."' group by ds.InvoiceClearedVoucherNo";
		$sqlData = $this->m_dbConn->select($query);
		
		return $sqlData;
			
	}
	
	function AddTDSDetails($PaidTo,$ChequeNumber,$ChequeDate,$Amount,$PayerBank,$Comments,$VoucherDate,$LeafID,$InvoiceDate,$InvoiceNumber,$ExpenceBy, $InvoiceAmount, $TDSAmount ,&$TDSVoucherNo)
	{
		
		$LatestVoucherNo = $this->m_latestcount->getLatestVoucherNo($_SESSION['society_id']);
		$TDSVoucherNo=$LatestVoucherNo;
			
		$dataVoucher1 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($InvoiceDate),0,0,
		$LatestVoucherNo,1,VOUCHER_JOURNAL,$PaidTo,TRANSACTION_DEBIT,$TDSAmount,'');
		//echo 'setbyvoucher';
		
		$dataVoucher2 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($InvoiceDate),0,0,
		$LatestVoucherNo,2,VOUCHER_JOURNAL,TDS_PAYABLE,TRANSACTION_CREDIT,$TDSAmount,'');
		//echo 'setbyvoucher';
		
		$arByParentDetails = $this->m_objUtility->getParentOfLedger($PaidTo);
		

		if(!(empty($arByParentDetails)))
		{
			$ByGroupID = $arByParentDetails['group'];
			$ByCategoryID = $arByParentDetails['category'];	
		
		if($ByGroupID==LIABILITY)
			{
				//echo 'SetLiabilityRegister';
				$regResult1 = $this->m_register->SetLiabilityRegister(getDBFormatDate($VoucherDate),$PaidTo,$dataVoucher1,VOUCHER_JOURNAL, TRANSACTION_DEBIT,$TDSAmount,0,$iLatestChangeID);	
				
			}
			if($ByGroupID==ASSET)
			{
				//echo 'SetAssetRegister';
				$regResult2 = $this->m_register->SetAssetRegister(getDBFormatDate($VoucherDate), $PaidTo, $dataVoucher1, VOUCHER_JOURNAL, TRANSACTION_DEBIT, $TDSAmount,0,$iLatestChangeID);	
			}
			if($ByGroupID==INCOME)
			{
				//echo 'SetIncomeRegister';
				$regResult3 = $this->m_register->SetIncomeRegister($PaidTo, getDBFormatDate($VoucherDate), $dataVoucher1, VOUCHER_JOURNAL, TRANSACTION_DEBIT, $TDSAmount,$iLatestChangeID);
			}
			if($ByGroupID==EXPENSE)
			{
				//echo 'SetExpenseRegister';
				$regResult4 = $this->m_register->SetExpenseRegister($PaidTo,getDBFormatDate($VoucherDate), $dataVoucher1, VOUCHER_JOURNAL, TRANSACTION_DEBIT,$TDSAmount,0,$iLatestChangeID);
			}
		
		}
		
		$arToParentDetails = $this->m_objUtility->getParentOfLedger(TDS_PAYABLE);

		if(!(empty($arToParentDetails)))
		{
			$ToGroupID = $arToParentDetails['group'];
			$ToCategoryID = $arToParentDetails['category'];	
			
			if($ToGroupID==LIABILITY)
			{
				//echo 'SetLiabilityRegister';
				$regResult1 = $this->m_register->SetLiabilityRegister(getDBFormatDate($VoucherDate),TDS_PAYABLE,$dataVoucher2, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $TDSAmount, 0,$iLatestChangeID);	
				//echo 'SetLiabilityRegister';
			}
			if($ToGroupID==ASSET)
			{
				
				//echo 'SetAssetRegister';
				$regResult2 = $this->m_register->SetAssetRegister(getDBFormatDate($VoucherDate), TDS_PAYABLE, $dataVoucher2, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $TDSAmount, 0,$iLatestChangeID);	
			}
			
			if($ToGroupID==INCOME)
			{
				//echo 'SetIncomeRegister';
				$regResult3 = $this->m_register->SetIncomeRegister(TDS_PAYABLE, getDBFormatDate($VoucherDate), $dataVoucher2, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $TDSAmount,$iLatestChangeID);
			}
			if($ToGroupID==EXPENSE)
			{
			
				//echo 'SetExpenseRegister';
				$regResult4 = $this->m_register->SetExpenseRegister(TDS_PAYABLE,getDBFormatDate($VoucherDate), $dataVoucher2, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $TDSAmount,0,$iLatestChangeID);
			
			}
			
		
		}
	}
	
	function AddTDSDetailsEx($LedgerBy, $LedgerTo, $VoucherDate, $VoucherAmount, $VoucherType, $Comments, &$VoucherNo)
	{
	
		$LatestVoucherNo = $this->m_latestcount->getLatestVoucherNo($_SESSION['society_id']);
		$VoucherNo = $LatestVoucherNo;
		
		$ExpectedCounter = $this->m_objUtility->GetCounter(VOUCHER_JOURNAL,0,false);
		$ExVoucherCounter = $ExpectedCounter[0]['CurrentCounter'];
		$IsCallUpdtCnt = 1;
		if($IsCallUpdtCnt == 1)
		{
			$this->m_objUtility->UpdateExVCounter(VOUCHER_JOURNAL,$ExVoucherCounter,0);
		}
			
		$dataVoucher1 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),0,0,
		$LatestVoucherNo,1,$VoucherType,$LedgerBy,TRANSACTION_DEBIT,$VoucherAmount,$Comments,$ExVoucherCounter);
		//echo 'setbyvoucher';
		
		$dataVoucher2 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),0,0,
		$LatestVoucherNo,2,$VoucherType,$LedgerTo,TRANSACTION_CREDIT,$VoucherAmount,$Comments,$ExVoucherCounter);
		//echo 'setbyvoucher';
		
		$arByParentDetails = $this->m_objUtility->getParentOfLedger($LedgerBy);
		

		if(!(empty($arByParentDetails)))
		{
			$ByGroupID = $arByParentDetails['group'];
			$ByCategoryID = $arByParentDetails['category'];	
		
		if($ByGroupID==LIABILITY)
			{
				//echo 'SetLiabilityRegister';
				$regResult1 = $this->m_register->SetLiabilityRegister(getDBFormatDate($VoucherDate),$LedgerBy,$dataVoucher1,$VoucherType, TRANSACTION_DEBIT,$VoucherAmount,0,$iLatestChangeID);	
				
			}
			if($ByGroupID==ASSET)
			{
				//echo 'SetAssetRegister';
				$regResult2 = $this->m_register->SetAssetRegister(getDBFormatDate($VoucherDate), $LedgerBy, $dataVoucher1, $VoucherType, TRANSACTION_DEBIT, $VoucherAmount,0,$iLatestChangeID);	
			}
			if($ByGroupID==INCOME)
			{
				//echo 'SetIncomeRegister';
				$regResult3 = $this->m_register->SetIncomeRegister($LedgerBy, getDBFormatDate($VoucherDate), $dataVoucher1, $VoucherType, TRANSACTION_DEBIT, $VoucherAmount,$iLatestChangeID);
			}
			if($ByGroupID==EXPENSE)
			{
				//echo 'SetExpenseRegister';
				$regResult4 = $this->m_register->SetExpenseRegister($LedgerBy,getDBFormatDate($VoucherDate), $dataVoucher1, $VoucherType, TRANSACTION_DEBIT,$VoucherAmount,0,$iLatestChangeID);
			}
		
		}
		
		$arToParentDetails = $this->m_objUtility->getParentOfLedger($LedgerTo);

		if(!(empty($arToParentDetails)))
		{
			$ToGroupID = $arToParentDetails['group'];
			$ToCategoryID = $arToParentDetails['category'];	
			
			if($ToGroupID==LIABILITY)
			{
				//echo 'SetLiabilityRegister';
				$regResult1 = $this->m_register->SetLiabilityRegister(getDBFormatDate($VoucherDate),$LedgerTo,$dataVoucher2, $VoucherType, TRANSACTION_CREDIT, $VoucherAmount, 0,$iLatestChangeID);	
				//echo 'SetLiabilityRegister';
			}
			if($ToGroupID==ASSET)
			{
				
				//echo 'SetAssetRegister';
				$regResult2 = $this->m_register->SetAssetRegister(getDBFormatDate($VoucherDate), $LedgerTo, $dataVoucher2, $VoucherType, TRANSACTION_CREDIT, $VoucherAmount, 0,$iLatestChangeID);	
			}
			
			if($ToGroupID==INCOME)
			{
				//echo 'SetIncomeRegister';
				$regResult3 = $this->m_register->SetIncomeRegister($LedgerTo, getDBFormatDate($VoucherDate), $dataVoucher2, $VoucherType, TRANSACTION_CREDIT, $VoucherAmount,$iLatestChangeID);
			}
			if($ToGroupID==EXPENSE)
			{
			
				//echo 'SetExpenseRegister';
				$regResult4 = $this->m_register->SetExpenseRegister($LedgerTo,getDBFormatDate($VoucherDate), $dataVoucher2, $VoucherType, TRANSACTION_CREDIT, $VoucherAmount,0,$iLatestChangeID);
			
			}
			
		
		}
	}
	
	function CreatePaymentToJVDetailsEx($LedgerBy, $LedgerTo, $VoucherDate, $VoucherAmount, $CGSTAmount, $SGSTAmount, $VoucherType, $Comments, &$VoucherNo, $RefID=0, $RefTableID=0)
	{
//		echo "<BR>Inside CreatePaymentToJVDetailsEx";
//		echo "<BR>Inside VoucherNo" . $VoucherNo;
//		echo "<BR>";
		if($VoucherNo != 0)
		{
			$LatestVoucherNo = $VoucherNo;
		}
		else
		{	
			$LatestVoucherNo = $this->m_latestcount->getLatestVoucherNo($_SESSION['society_id']);								
			$VoucherNo = $LatestVoucherNo;						
		}
		
		$ExpectedCounter = $this->m_objUtility->GetCounter(VOUCHER_JOURNAL,0,false);
		$ExVoucherCounter = $ExpectedCounter[0]['CurrentCounter'];
		$IsCallUpdtCnt = 1;
		if($IsCallUpdtCnt == 1)
		{
			$this->m_objUtility->UpdateExVCounter(VOUCHER_JOURNAL,$ExVoucherCounter,0);
		}
				
		$SrNo = 1;
		
		$ByAmount = $VoucherAmount - $CGSTAmount - $SGSTAmount;
		
		$dataVoucher1 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),$RefID, $RefTableID,
		$LatestVoucherNo,$SrNo,$VoucherType,$LedgerBy,TRANSACTION_DEBIT,$ByAmount,$Comments,$ExVoucherCounter);
		
		
		$regResult1 = $this->m_register->SetRegister(getDBFormatDate($VoucherDate),$LedgerBy,$dataVoucher1,$VoucherType, TRANSACTION_DEBIT,$ByAmount,0,$iLatestChangeID);	
	
		
		if($CGSTAmount>0)
		{
			$CGSTLedger =$_SESSION['cgst_input'];
			
			$SrNo++;
			$dataVoucher2 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),$RefID, $RefTableID,
			$LatestVoucherNo,$SrNo,$VoucherType,$CGSTLedger,TRANSACTION_DEBIT,$CGSTAmount,$Comments,$ExVoucherCounter);
			$regResult2 = $this->m_register->SetRegister(getDBFormatDate($VoucherDate),$CGSTLedger,$dataVoucher2,$VoucherType, TRANSACTION_DEBIT,$CGSTAmount,0,$iLatestChangeID);	
			

		}
		
		if($SGSTAmount>0)
		{		
			$SGSTLedger =$_SESSION['sgst_input'];
			
			$SrNo++;
			$dataVoucher3 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),$RefID, $RefTableID,
			$LatestVoucherNo,$SrNo,$VoucherType,$SGSTLedger,TRANSACTION_DEBIT,$SGSTAmount,$Comments,$ExVoucherCounter);
			$regResult3 = $this->m_register->SetRegister(getDBFormatDate($VoucherDate),$SGSTLedger,$dataVoucher3,$VoucherType, TRANSACTION_DEBIT,$SGSTAmount,0,$iLatestChangeID);	
			
		}
		
		
		$SrNo++;
		$dataVoucher4 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),$RefID, $RefTableID,
		$LatestVoucherNo,2,$VoucherType,$LedgerTo,TRANSACTION_CREDIT,$VoucherAmount,$Comments,$ExVoucherCounter);
		$regResult4 = $this->m_register->SetRegister(getDBFormatDate($VoucherDate),$LedgerTo,$dataVoucher4,$VoucherType, TRANSACTION_CREDIT,$VoucherAmount,0,$iLatestChangeID);	

		return $SrNo;
	}

	
	
	function UpdateInvoiceStatus($PaymentVoucherNo,$InvoiceNumber,$TDSVoucherNo,$InvoiceAmount, $TDSAmount,$DocStatusID,$IGSTAmount,$CGSTAmount,$SGSTAmount,$CESSAmount)
	{
		$InvoiceRaisedVoucherNo = 0;
		$PaidTo = 0;
		$InvoiceExpenceBy = 0;
		$VoucherDate = "";
		$Comments = "";
		$InvoiceDate = "";
		$this->UpdateInvoiceStatus_with_invoiceVoucher($PaymentVoucherNo,$InvoiceNumber,$TDSVoucherNo,$InvoiceAmount, $TDSAmount,$DocStatusID,$IGSTAmount,$CGSTAmount,$SGSTAmount,$CESSAmount,$InvoiceRaisedVoucherNo,$PaidTo,$InvoiceExpenceBy,$InvoiceDate,$Comments);
	}
	
	function UpdateInvoiceStatus_with_invoiceVoucher($PaymentVoucherNo,$InvoiceNumber,$TDSVoucherNo,$InvoiceAmount, $TDSAmount,$DocStatusID,$IGSTAmount,$CGSTAmount,$SGSTAmount,$CESSAmount,$InvoiceRaisedVoucherNo,$PaidTo,$InvoiceExpenceBy,$InvoiceDate,$Comments)
	{
		// Amit changes start
		if($this->debug_trace == 1)
		{
			echo "Updating Invoice Status";
		}
		
		$msg = ''; // msg for error_log
		if(!empty($InvoiceRaisedVoucherNo) && $InvoiceRaisedVoucherNo <> 0)
		{
			$this->m_objUtility->Delete_VoucherandRegister_table($InvoiceRaisedVoucherNo,VOUCHER_JOURNAL); //Deleting exiting entries
			$InvoiceRaisedVoucherNo = $LatestVoucherNo = $this->m_latestcount->getLatestVoucherNo($_SESSION['society_id']);
			$EXVoucherNumber = $this->m_objUtility->GetCounter(VOUCHER_JOURNAL,0);
			
			if($InvoiceAmount <> 0) // Expense Entry
			{
				$GrossAmount = $InvoiceAmount - ($CGSTAmount + $SGSTAmount);
				$dataVoucher1 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($InvoiceDate),1,0,
	$LatestVoucherNo,$SrNo,VOUCHER_JOURNAL,$InvoiceExpenceBy,TRANSACTION_DEBIT,$GrossAmount,$this->m_dbConn->escapeString($Comments),$EXVoucherNumber);
				
				$setregister=$this->m_register->SetRegister($InvoiceDate,$InvoiceExpenceBy,$dataVoucher1,VOUCHER_JOURNAL,TRANSACTION_DEBIT,$GrossAmount,0);
				$msg .= 'Expense Head : '.$InvoiceExpenceBy.' : Amt : '.$GrossAmount ; 
			}
			
			if($CGSTAmount <> 0) // CGST Entry
			{
				
			 $dataVoucher2 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($InvoiceDate),1,0,
$LatestVoucherNo,$SrNo,VOUCHER_JOURNAL,$_SESSION['cgst_input'],TRANSACTION_DEBIT,$CGSTAmount,$this->m_dbConn->escapeString($Comments),$EXVoucherNumber);
			
			 $setregister=$this->m_register->SetRegister($InvoiceDate,$_SESSION['cgst_input'],$dataVoucher2,VOUCHER_JOURNAL,TRANSACTION_DEBIT,$CGSTAmount,0);
			 $msg .= ':: CGST Head : '.$_SESSION['cgst_input'].' : Amt : '.$CGSTAmount ; 
					
			}
			
			if($SGSTAmount <> 0) // SGST Entry
			{
				
			 $dataVoucher3 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($InvoiceDate),1,0,
$LatestVoucherNo,$SrNo,VOUCHER_JOURNAL,$_SESSION['sgst_input'],TRANSACTION_DEBIT,$SGSTAmount,$this->m_dbConn->escapeString($Comments),$EXVoucherNumber);
			
			 $setregister=$this->m_register->SetRegister($InvoiceDate,$_SESSION['sgst_input'],$dataVoucher3,VOUCHER_JOURNAL,TRANSACTION_DEBIT,$SGSTAmount,0);
			 
			 $msg .= ':: CGST Head : '.$_SESSION['sgst_input'].' : Amt : '.$SGSTAmount ; 
					
			}
			
			if($PaidTo <> 0) // Party Entry
			{
				
				$dataVoucher4 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($InvoiceDate),1,0,
$LatestVoucherNo,$SrNo,VOUCHER_JOURNAL,$PaidTo,TRANSACTION_CREDIT,$InvoiceAmount,$this->m_dbConn->escapeString($Comments),$EXVoucherNumber);
				
				$setregister=$this->m_register->SetRegister($InvoiceDate,$PaidTo,$dataVoucher4,VOUCHER_JOURNAL,TRANSACTION_CREDIT,$InvoiceAmount,0);
				
				$msg .= ':: Party Head : '.$PaidTo.' : Amt : '.$InvoiceAmount ; 
			}
			$log = 'Invoice Added '.$msg;
			$iLatestChangeID = $this->m_objLog->setLog($log, $_SESSION['login_id'], 'VOUCHER', 'Payment Invoice Edit'); // error log
		}
		
		// Amit changes end
		
		$updateDocStatus = "update `invoicestatus` set `InvoiceClearedVoucherNo`='".$PaymentVoucherNo."', NewInvoiceNo = '".$InvoiceNumber."', `TDSVoucherNo`='".$TDSVoucherNo."',	`InvoiceChequeAmount`='".$InvoiceAmount."', `AmountReceived`='".$InvoiceAmount."',TDSAmount='".$TDSAmount."',IGST_Amount='".$IGSTAmount."',CGST_Amount='".$CGSTAmount."',SGST_Amount='".$SGSTAmount."',CESS_Amount='".$CESSAmount."'" ;
		
		if(!empty($InvoiceRaisedVoucherNo) && $InvoiceRaisedVoucherNo <> 0)
		{
			$updateDocStatus .= " , InvoiceRaisedVoucherNo = '".$InvoiceRaisedVoucherNo."' ";
		}
		
		$updateDocStatus .= " where InvoiceStatusID='".$DocStatusID."'";
		
		//echo "<br>Update : ".$updateDocStatus;
		$res=$this->m_dbConn->update($updateDocStatus);
	}
	
	public function combobox1($query, $id, $bShowAll = false)
	{
		
		if($bShowAll == true)
		{
			$str.="<option value=''>All</option>";
		}
		else
		{
			$str.="<option value='0'>Please Select</option>";
		}
		echo $query;
		$data = $this->m_dbConn->select($query);
		
		if(!is_null($data))
		{
			//$vowels = array('/', '-', '.', '*', '%', '&', ',', '(', ')', '"');
			$vowels = array('/', '-', '.', '*', '%', '&', ',', '"');
			//$vowels = array();
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
						//echo $str;
					}
					else
					{
						$str.= str_replace($vowels, ' ', $v)."</OPTION>";
					}
					$i++;
				}
			}
		}
			return $str;
	}
	
		public function combobox2($query, $id, $bShowAll = false)
	{
		
		if($bShowAll == true)
		{
			$str.="<option value=''>All</option>";
		}
		else
		{
			$str.="<option value='0'>Please Select</option>";
		}
		echo $query;
		$data = $this->m_dbConn->select($query);
		
		if(!is_null($data))
		{
			//$vowels = array('/', '-', '.', '*', '%', '&', ',', '(', ')', '"');
			$vowels = array('/', '-', '.', '*', '%', '&', ',', '"');
			//$vowels = array();
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
						//echo $str;
					}
					else
					{
						$str.= str_replace($vowels, ' ', $v)."</OPTION>";
					}
					$i++;
				}
			}
		}
			return $str;
	}
}
?>