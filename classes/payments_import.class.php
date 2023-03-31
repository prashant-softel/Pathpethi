<?php
include_once("utility.class.php");
include_once("PaymentDetails.class.php");
include_once("ChequeDetails.class.php");
//echo "import";
class paymentImport
{
	
	public $m_dbConn;
	public $errorLog;
	public $m_dbConnRoot;
	public $obj_utility;
	public $obj_PaymentDetails;
	public $obj_ChequeDetails;
	public $actionPage = "../import_payments_receipts.php";
	public $errofile_name;
	
	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
		$this->obj_utility= new utility($this->m_dbConn);
		$this->obj_PaymentDetails=new PaymentDetails($this->m_dbConn);
		$this->obj_PaymentDetails->actionType = "IMPORT";
		$this->obj_ChequeDetails=new ChequeDetails($this->m_dbConn);
		
	}
	public function ImportData($SocietyID)
	{
		/*$this->errorfile_name = 'tariff_import_errorlog_'.date("d.m.Y").'_'.rand().'.html';
		$this->errorLog = $this->errorfile_name;
		$errorfile = fopen($this->errorfile_name, "a");*/
		
		date_default_timezone_set('Asia/Kolkata');		
		//$errofile_name='import_payment_'.$SocietyID.'_'.date('Y-m-d H:i:sa').'.html';	
		$this->errofile_name = 'import_payment_'.$SocietyID.'_'.date('Y-m-d').'.txt';
		$this->errorLog=$this->errofile_name;
		$errorfile=fopen($this->errofile_name, "a");
		
		$tmp_array=array();	
		if(isset($_POST["Import"]))
		{			
			$valid_files=array('Payment');
			$limit=count($_FILES['upload_files']['name']);
			$success=0;
			 
			for($m=0;$m<$limit;$m++)
			{
				$filename=$_FILES['upload_files']['name'][$m];
				$tmp_filename=$_FILES['upload_files']['tmp_name'][$m];
				for($i=0;$i<sizeof($valid_files);$i++)
				{
					$pos=strpos($filename,$valid_files[$i]);
						
					if($pos === FALSE)
					{
						$message = $filename." is not a valid file";
						return $message;
					}
					else
					{
						$ext = pathinfo($filename, PATHINFO_EXTENSION);
						if($ext <> '' && $ext <> 'txt' && $ext <> 'csv')
						{
							return $filename.'  Invalid file format selected. Expected *.txt or *.csv file format';
						}
						else
						{
							$success++;
							$tmp_array[$i]=$_FILES['upload_files']['tmp_name'][$m];
						}
					}					
				}
			}			
			$logfile="";
			$result=$this->startprocess($tmp_array[0],0,$errorfile);
			if($result <> '')
			{
				$this->actionPage="../import_payments_receipts.php";
				return $result;
			}
		}
	}
		
	function startprocess($filename,$pos,$errorfile)
	{
		if($pos==0)
		{
			$import_result=$this->UploadData($filename,$errorfile);
			return $import_result;
		}
		else
		{
			return 'All Data Imported Successfully...';
		}
	}
	
	public function UploadData($fileName,$errorfile)
	{
		//$ShowDebugTrace = 1;
/*		echo "<pre>";
		print_r($_SESSION);
		echo "</pre>";
		die();
*/		
		$ChequeLeakBook=array();
		$file = fopen($fileName,"r");
		$data=0;
		$ChequeExistance='';
		$errormsg="[Importing Payment Details.....]";
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);

		$opening_date = "";

		if($_SESSION['society_creation_yearid'] <> "")
		{			
			$OpeningBalanceDate = $this->obj_utility->GetDateByOffset($this->obj_utility->getCurrentYearBeginingDate($_SESSION['society_creation_yearid']) , -1);
			if($OpeningBalanceDate <> "")
			{
				$opening_date = $OpeningBalanceDate;		
			}		
		}

		while (($row = fgetcsv($file)) !== FALSE)
		{
			if($row[0] <> '')
			{
				$rowCount++;
				if($rowCount == 1)
				{
					$VDate = array_search(VDate,$row,true);
					$By = array_search(By_,$row,true);					
					$Sr = array_search(Sr,$row,true);
					$AccountName = array_search(AccountName,$row,true);
					$ChequeNo = array_search(ChequeNo,$row,true);	
					$ChequeDate = array_search(ChequeDate,$row,true);		
					$Remark = array_search(Remark,$row,true);	
					$Amount = array_search(Amount,$row,true);
					$Rs = array_search(Rs,$row,true);
					$ExpenseHead = array_search(ExpenseHead, $row, true);
					$SubCategoryName = array_search(SubCategoryName, $row, true);
						
					if(!isset($VDate) || !isset($By) || !isset($AccountName) || !isset($ChequeNo) || !isset($ChequeDate) || !isset($Remark) || !isset($Amount)|| !isset($Rs) || !isset($Sr))
					{
						$result = 'Column Names Not Found Cant Proceed Further......'.'Go Back';
						$errormsg=" Column names in file BankBook not match";
						$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg);
						return $result;
						exit(0);
					}
				}
				else
				{
					try
					{
						$voucherDate = $row[$VDate];
						$by = $row[$By];
						$accountName = $row[$AccountName];
						$chequeNo = $row[$ChequeNo];
						$chequeDate = $row[$ChequeDate];	
						$comments = $row[$Remark];	
						$amount = $row[$Amount];
							
						$rs = $row[$Rs];
						//echo "by for every loop:".$by."<br>";
						$PayerBank = $this->getLedgerID($by, "", "");
						//echo "PayerBank:".$PayerBank."<br>";
						$sr = $row[$Sr];
						$expenseLeder = $row[$ExpenseHead];
						$SubCategoryLedger = $row[$SubCategoryName];

						if($ExpenseHead == 0)
						{
							$expenseLeder = "";
						}

						if($SubCategoryName == 0)
						{
							$SubCategoryLedger = "";	
						}
						
						if($PayerBank=="")
						{
							$errormsg='Bank or Cash Account:'.$by.'Does Not Exists In Current Society.';
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg);
							continue;
						}
						if(array_key_exists($by,$ChequeLeakBook) && strtolower($by) <> 'cash')
						{
							$data=$ChequeLeakBook[$by];
						}
						else if(strtolower($by) <> 'cash')
						{
						    $LeafName = 'DATA IMPORTED'.date('Y-m-d H:i:sa');
						    $insert_query1="insert into chequeleafbook (`LeafName`,`StartCheque`,`EndCheque`,`BankID`,`Comment`,`CustomLeaf`,`LeafCreatedYearID`) values ('".$LeafName."','0','0','".$PayerBank."','DATA IMPORTED','1','".$_SESSION['society_creation_yearid']."')";
							$data = $this->m_dbConn->insert($insert_query1);
  						    $ChequeLeakBook[$by]=$data;
						}
					    $PaidTo = $this->getLedgerID(trim($accountName), $SubCategoryLedger, $opening_date);
						$ExpenseTo = $this->getLedgerID($expenseLeder, "Expenses", $opening_date);
						$isDoubleEntry = 1;

						if($ExpenseTo == "")
						{
						   	$ExpenseTo = 0;
						   	$isDoubleEntry = 0;
						}
						   
						if(is_numeric($chequeNo)== FALSE && strtolower($chequeNo)=='ecs')
						{
							$ModeOfPayment=1;
							$LeafID= $data;   
						}
						else if(is_numeric($chequeNo)== FALSE && strtolower($chequeNo)=='cash')
						{
							$ModeOfPayment= '-1'; 
							$LeafID= -1;  
						}
						else if(is_numeric($chequeNo)== FALSE && strtolower($chequeNo)=='other')
						{
							$ModeOfPayment=2;
							$LeafID= $data;   
						}
						else 
						{
							$ModeOfPayment= 0;
							$LeafID= $data;     
						}
						echo "ModeOfPayment: ".$ModeOfPayment."<br>";
						echo "ChequeDate: ".$chequeDate."<br>";
						echo "VoucherDate: ".$voucherDate."<br>";
						echo "PaidTo/AccName: ".$PaidTo."<br>";
						echo "Cheque No.:".$chequeNo."<br>";
						echo "PayerBank: ".$PayerBank."<br>";
						//die();
						
						if($_SESSION['default_year_start_date'] <> "" && $_SESSION['default_year_end_date'] <> "")
						{
							$correct_voucher_date = $this->obj_utility->getIsDateInRange($voucherDate,$_SESSION['default_year_start_date'],$_SESSION['default_year_end_date']);
						}
						else
						{
							$sql07 = "select * from year where YearID = '".$_SESSION['default_year']."'";
							$sql77 = $this->m_dbConn->select($sql07);
							if($sql77 <> "")
							{
								$correct_voucher_date = $this->obj_utility->getIsDateInRange($voucherDate,$sql77[0]['BeginingDate'],$sql77[0]['EndingDate']);
							}
						}
						
						if($PaidTo <> '')
						{
							$ChequeExistance = '';
							$ECSExistance = '';
							$CashExistance = '';
							$otherExistance = '';
							
							if($ModeOfPayment == 0) //cheque
							{
								$sql03="select id from paymentdetails where ChequeNumber='".$chequeNo."' and PayerBank='".$PayerBank."' ";								
								$resExistance = $this->m_dbConn->select($sql03);								
								$ChequeExistance = $resExistance[0]['id'];
							}
							else if($ModeOfPayment == 1) //ECS
							{
								$sql04 = 'select id from paymentdetails where ModeOfPayment="'.$ModeOfPayment.'" and ChequeDate="'.getDBFormatDate($chequeDate).'" and VoucherDate="'.getDBFormatDate($voucherDate).'" and PaidTo="'.$PaidTo.'" and ChequeNumber="'.$chequeNo.'" and Amount="'.$amount.'"';
								$resExistance = $this->m_dbConn->select($sql04);
								$ECSExistance = $resExistance[0]['id'];
							}
							else if($ModeOfPayment == '-1') //cash entry
							{
								$sql05 = 'select id from paymentdetails where ModeOfPayment="'.$ModeOfPayment.'" and VoucherDate="'.getDBFormatDate($voucherDate).'" and PaidTo="'.$PaidTo.'" and ChequeNumber="'.$chequeNo.'" and Amount="'.$amount.'" and PayerBank="'.$PayerBank.'"';								
								$resExistance = $this->m_dbConn->select($sql05);
								$CashExistance = $resExistance[0]['id'];
								//echo "Cash: ".$CashExistance."<br>";
							}
							else if($ModeOfPayment == 2) //other entry
							{
								$sql06 = 'select id from paymentdetails where ModeOfPayment="'.$ModeOfPayment.'" and VoucherDate="'.getDBFormatDate($voucherDate).'" and PaidTo="'.$PaidTo.'" and ChequeNumber="'.$chequeNo.'" and Amount="'.$amount.'" and PayerBank="'.$PayerBank.'"';
								$resExistance = $this->m_dbConn->select($sql06);
								$otherExistance = $resExistance[0]['id'];
							}
							
							if($PayerBank <> '' && $amount <> '')
							{
								/*echo "Cheque existance: ".$ChequeExistance."<br>";
								echo "ECS existance: ".$ECSExistance."<br>";
								echo "Cash existance: ".$CashExistance."<br>";
								echo "Other existance: ".$otherExistance."<br>";*/
								
								if($ModeOfPayment == 0 && $ChequeExistance <> '')
								{									
									$errormsg="Cheque ".$chequeNo." already issued.";
									$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);   
								}
								else if($ModeOfPayment == 1 && $ECSExistance <> '')
								{
									$errormsg = "ECS entry for Voucher No. <".$sr."> already done.";
									$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);
								}
								else if($ModeOfPayment == '-1' && $CashExistance <> '')
								{
									//echo "Found cash<br>";
									$errormsg = "Cash entry for Voucher No. <".$sr."> already done.";
									$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);
								}
								else if($ModeOfPayment == 2 && $otherExistance <> '')
								{
									$errormsg = "Other entry for Voucher No. <".$sr."> already done.";
									$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);
								}
								else if($correct_voucher_date != 1)
								{
									$errormsg = "Date not in range for Voucher No.: <".$sr."> .";
									$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);
								}
								else if($ChequeExistance == '' && $ECSExistance == '' && $CashExistance == '' && $otherExistance == '' && $correct_voucher_date == 1)
								{
									//echo "coming here??<br>";
									if($isDoubleEntry == 1)
									{
										$success = '';
										//echo "called 1";
										$success = $this->obj_PaymentDetails->AddNewValues($LeafID, $_SESSION['society_id'], $PaidTo, $chequeNo, $this->getDBFormatDate($chequeDate), $amount, $PayerBank, $comments, $this->getDBFormatDate($voucherDate), $ExpenseTo, $isDoubleEntry, $this->getDBFormatDate($chequeDate), 0, $ModeOfPayment, 0, 0, 0, 0, 0, 0, 0, $amount);
										if($success == 'Import Successful')
										{
											$errormsg = "Voucher No. <".$sr."> successfully imported.";
											$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);
										}
										else if($success != 'Import Successful')
										{
											$errormsg = "Voucher No. <".$sr."> not imported successfully.";
											$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);
										}
									}
									else
									{
										$success = '';
										//echo "called 2";
										$success = $this->obj_PaymentDetails->AddNewValues($LeafID, $_SESSION['society_id'], $PaidTo, $chequeNo, $this->getDBFormatDate($chequeDate), $amount, $PayerBank, $comments, $this->getDBFormatDate($voucherDate), $ExpenseTo, $isDoubleEntry, $this->getDBFormatDate('00-00-0000'), 0, $ModeOfPayment);
										if($success == 'Import Successful')
										{
											$errormsg = "Voucher No. <".$sr."> successfully imported.";
											$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);
										}
										else if($success != 'Import Successful')
										{
											$errormsg = "Voucher No. <".$sr."> not imported successfully.";
											$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);
										}
									}
								}
							}
							else if($amount == '' && $rs <> '')
							{
							    $errormsg=implode(' | ',$row)."not inserted";
								$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);
							}
						}
						else
						{
							$errormsg=implode(' | ',$row)."not inserted because account name ledger not found in ledger";
							$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);   
					    }
				 	}
					catch ( Exception $e )
					{
						
					    $errormsg=implode(' | ',$row);
						$errormsg .="not inserted";
						$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);
						$this->obj_utility->logGenerator($errorfile,$sr,$e);
					}
				}
			}
		}
		$errormsg="[End of  Payment Details]";
		$this->obj_utility->logGenerator($errorfile,'End',$errormsg);
		return  "file imported successfully..";
	}
	
	/*public function getBankArray($filename)
	{
		
		//echo 'Inside Upload Data';
		
		$file = fopen($fileName,"r");
		
		//fwrite($errorfile,"[Importing WingID]\n");
		$errormsg="[Importing WingID]";
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		$BankArray = array();
		while (($row = fgetcsv($file)) !== FALSE)
			{
				//echo '<br/>';
				if($row[0] <> '')
					{
						$rowCount++;
						if($rowCount == 1)
						{
							$AccountCode=array_search(AccountCode,$row,true);
							$Description=array_search(Description,$row,true);
							
						}
				//print_r($row);
					   else
					   {
						   $accountCode=$row[$AccountCode];
						   $desc=$row[$Description];
						   $BankArray[$accountCode] =  $desc;
						}			
					}
					
			return $BankArray;		
			}
		
	}*/
	function getDateTime()
	{
		$dateTime = new DateTime();
		$dateTimeNow = $dateTime->format('Y-m-d H:i:s');
		return $dateTimeNow;
	}
	
	public function getLedgerID($LedgerName, $SubCategoryName, $opening_date)
	{
		echo 'GetLedgerID : ' . $LedgerName."<br>";

		$LedgerID = "";

		if($LedgerName <> "")
		{
		 	echo $sql="select `id` from `ledger` where `ledger_name`='".$LedgerName."' ";
			$result = $this->m_dbConn->select($sql);
			
			if($result[0]['id'] == "" && $SubCategoryName <> "")
			{
				echo $sqlCategory = "select `category_id`, `group_id` from account_category where `category_name` = '" . $SubCategoryName . "'";
				$resultCategory = $this->m_dbConn->select($sqlCategory);
				//print_r($resultCategory);

				if($resultCategory[0]['category_id'] <> '')
				{
					//echo 'Inside If';
					$insert_ledger="insert into `ledger`(society_id, categoryid, ledger_name, income, expense, payment, receipt, opening_type, opening_balance, `opening_date`) values('" . $_SESSION['society_id'] . "', '" . $resultCategory[0]['category_id'] . "','". $LedgerName . "', 1, 1, 1, 1, 1, 0, '" . $opening_date."')";
					$LedgerID = $this->m_dbConn->insert($insert_ledger);
				}
			}
			else
			{
				$LedgerID = $result[0]['id'];
			}
		}	 

		return $LedgerID;
	}
	
	function getDBFormatDate($ddmmyyyy)
	{
		//echo $ddmmyyyy;
		if($ddmmyyyy <> '' && $ddmmyyyy <> '00-00-0000')
		{
			$ddmmyyyy = str_replace('/', '-', $ddmmyyyy);
			return date('d-m-Y', strtotime($ddmmyyyy));
		}
		else
		{
			return '00-00-0000';
		}
	}
}

?>