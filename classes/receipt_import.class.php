

<?php
include_once("utility.class.php");
//include_once("PaymentDetails.class.php");
include_once("ChequeDetails.class.php");
//echo "import";
class receiptImport
{
	public $errorLog;
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_utility;
	//public $obj_PaymentDetails;
	public $obj_ChequeDetails;
	public $actionPage = "../import_payments_receipts.php";
	


	function __construct($dbConnRoot, $dbConn)
	{
		
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
		$this->obj_utility= new utility($this->m_dbConn);
		//echo "constructor2";
		//$this->obj_PaymentDetails=new PaymentDetails($this->m_dbConn);
		//echo "constructor3";
		$this->obj_ChequeDetails=new ChequeDetails($this->m_dbConn);
		
	}
	public function ImportData($SocietyID)
	{

		date_default_timezone_set('Asia/Kolkata');
		$errofile_name='import_receipt_'.$SocietyID.'_'.date('Ymd').'.txt';	
		//echo "Import Data";
		$this->errorLog=$errofile_name;
		$errorfile=fopen($errofile_name, "a");
		//echo $errofile_name;
		$tmp_array=array();	
		if(isset($_POST["Import"]))
		{
			$valid_files=array('Receipt');
			//print_r($valid_files);
			//$valid_files=array('BuildingID.csv','BuildingID.CSV','WingID.csv','WingID.CSV');
			$limit=count($_FILES['upload_files']['name']);
			//echo "<br>limit:".$limit;
			$success=0;
			 
			 for($m=0;$m<$limit;$m++)
			 {
				 $filename=$_FILES['upload_files']['name'][$m];
				$tmp_filename=$_FILES['upload_files']['tmp_name'][$m];
				//echo "<br>filename:".$tmp_filename;
				//echo "<br>filesize:".sizeof($valid_files);
				for($i=0;$i<sizeof($valid_files);$i++)
				{
					//echo "<br>filename valid_files:".$valid_files[$i];
				//echo "<br>filename:".$filename;
					$pos=strpos($filename,$valid_files[$i]);
					//echo "<br>pos:".$pos;
					if($pos === FALSE)
					{
						//echo "in if";
						//echo " pos is false";	
					$message = $filename." is not a valid file";
					return $message;
					}
					else
					{
						//echo 'check extension...';
						$ext = pathinfo($filename, PATHINFO_EXTENSION);
						if($ext <> '' && $ext <> 'txt' && $ext <> 'csv')
						{	
								
								return $filename.'  Invalid file format selected. Expected *.txt or *.csv file format';
						}
						else
						{
							//echo "<br>pos:true";
								$success++;
								//echo "i:".$i;
								$tmp_array[$i]=$_FILES['upload_files']['tmp_name'][$m];
								//echo 'valid file'.$filename;
								//break;
						}
					}
					//echo "before die";
					//die();
					//echo "after die";
				}
				
			 }
			// if($success > 1)
			 //{
				 //echo "success got";
				 //$this->obj_utility->logGenerator($errorfile,2,"test");
				 $logfile="";
				 //echo "tmp_array:";
				 //print_r($tmp_array);
				 $result=$this->startprocess($tmp_array[0],0,$errorfile);
				 //echo "result:".$result;
				 if($result <> '')
				 {
					 $this->actionPage="../import_payments_receipts.php";
					 return $result;
					 
				 }
				// $result=$this->startprocess($tmp_array[0],0,$errorfile);
			//}
			 /*else
			 {
				 if(sizeof($valid_files) > sizeof($tmp_array))
				 {
					 $result=array_diff_key($valid_files,$tmp_array);
						foreach($result as $getkey=>$getval)
						{
						echo '<p><font color="#FF0000">'.$result[$getkey].'  File is missing....</font></p>';
						}
							
				}
									
			}*/
		}

	}
	
	
	function startprocess($filename,$pos,$errorfile)
	{
		if($pos==0)
			 {
				 //echo 'billdetails';
				 	// $obj_bankdetails_import=new bankdetails_import($this->m_dbConnRoot, $this->m_dbConn);
					 //$BankArray=$this->getBankArray($filename2);
					 //echo "startprocess";
					 //echo "<br>start processs file name:".$filename;
					 //echo "<br>start processs errfile name:".$errorfile;
					 $import_result=$this->UploadData($filename,$errorfile);
			 		//echo '8';
					//echo "<br> import result:".$import_result;
					return $import_result;
			 }
			 
			 else
			 {
				 
				return 'All Data Imported Successfully...'; 
				 
			}
	}
	
	public function UploadData($fileName,$errorfile)
	{
		//echo 'Inside Upload Data';
		$ChequeLeafBook=array();
		$file = fopen($fileName,"r");
		$data=0;
		$DepositeID=0;
		//fwrite($errorfile,"[Importing WingID]\n");
		$errormsg="[Importing Receipts Details]";
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		//$this->obj_utility->logGenerator($errorfile,0,$fileName);
		while (($row = fgetcsv($file)) !== FALSE)
		{
			//echo '<br/>';
			//$this->obj_utility->logGenerator($errorfile,2,$rowCount);
			//$tmp_array=array();
			//$final_array=array();
			if($row[0] <> '')
				{
					$rowCount++;
					
					if($rowCount == 1)
					{
						$VDate=array_search(RctDate,$row,true);
						$To=array_search(To_,$row,true);
						$Sr=array_search(Sr,$row,true);
						$BankName=array_search(BankName,$row,true);
						$BranchName=array_search(BranchName,$row,true);	
						$UnitNo=array_search(UnitNo,$row,true);	
						$AccountName=array_search(AccountName,$row,true);
						$ChequeNo=array_search(ChequeNo,$row,true);	
						$ChequeDate=array_search(ChequeDate,$row,true);		
						$Remark=array_search(Remark,$row,true);	
						$Amount=array_search(Amount,$row,true);
						$Rs=array_search(Rs,$row,true);
						//print_r($row);
						if(!isset($VDate) || !isset($To) || !isset($AccountName) || !isset($ChequeNo) || !isset($ChequeDate) || !isset($Remark) || !isset($Amount)|| !isset($Rs) || !isset($Sr))
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
					   //echo "account:".$row[$AccountName];
					   
					   try {
							$voucherDate=$row[$VDate];
							$to = trim($row[$To]);
							$PayerBank=$row[$BankName];
							$PayerChequeBranch=$row[$BranchName];
							$unitNo = trim($row[$UnitNo]);
							$accountName=$row[$AccountName];
							$chequeNo=trim($row[$ChequeNo]);	
							$chequeDate=$row[$ChequeDate];	
							$comments=$row[$Remark];	
							$amount=$row[$Amount];
							$rs=$row[$Rs];
							$BankID=$this->getLedgerID($to);
							$sr=$row[$Sr];
							//$this->obj_utility->logGenerator($errorfile,2,$rowCount);
							//echo "by:".$by;
							
							
							if(array_key_exists($to,$ChequeLeafBook) && strtolower($to) <> 'cash')
							{
								$data=$ChequeLeafBook[$to];
							}
							else if(strtolower($to) <> 'cash')
							{
								//date_default_timezone_set('Asia/Kolkata');
								$desc = 'DATA IMPORTED'.date('Y-m-d H:i:sa');
								$queryII = "select `society_creation_yearid` FROM `society` where `society_id` = '".$_SESSION['society_id']."'";
							   $resII = $this->m_dbConn->select($queryII);
							  $insert_query1="insert into depositgroup (`bankid`,`createby`,`depositedby`,`status`,`desc`,`DepositSlipCreatedYearID`) values ('".$BankID."','".$_SESSION['login_id']."','Import Data','0','".$desc."','".$resII[0]['society_creation_yearid']."')";
							   $data = $this->m_dbConn->insert($insert_query1);
							   //$this->obj_utility->logGenerator($errorfile,0,'leaf created');
							   $ChequeLeafBook[$to]=$data;
							}
						   //$arPaidByParentDetails = $this->m_objUtility->getParentOfLedger($BankID);
							//print_r($arPaidByParentDetails);
							
						   if(is_numeric($chequeNo)== FALSE && strtolower($chequeNo)=='cash'  &&  strtolower($to) == 'cash')
						   {
								$DepositeID= -3;  
						   }
						   else if(is_numeric($chequeNo)== FALSE && strtolower($chequeNo)=='neft')
						   {
								$DepositeID= -2;     
						   }
						    else if(is_numeric($chequeNo)== TRUE && strlen($chequeNo) > 6)
						   {
							   //neft or online transaction
								$DepositeID= -2;     
						   }
						    else if(is_numeric($chequeNo)== FALSE && strtolower($chequeNo) <> 'neft' && strtolower($chequeNo) <> 'cash')
						   {
							   //neft or online transaction
								$DepositeID= -2;     
						   }
						   else
						   {
							  $DepositeID=$data; 
						   }
						   
						   if($unitNo == '')
						   {
								$unitNo=$accountName;   
						   }
						   //$unitNo = str_replace(' ', '', $unitNo);
						   $unitNo = trim($unitNo);
						   $PaidBy=$this->getLedgerID($unitNo);
						   
						   //echo "AddNewValues2";
						   //echo "LeafID:".$LeafID;
						  $errormsg="To_ : ".$to . " : PaidBy : ".$PaidBy.":  UNIt No:".$unitNo;
						  $this->obj_utility->logGenerator($errorfile,$sr,$errormsg);
 
						  if($to <> '' && $PaidBy <> '')
						   {
							   //echo "Inside 2";

							   $this->obj_ChequeDetails->AddNewValues($this->getDBFormatDate($voucherDate), $this->getDBFormatDate($chequeDate), $chequeNo, $amount, $PaidBy, $BankID, $PayerBank, $PayerChequeBranch, $DepositeID, $comments,0,0,0,0,0,0,true);
						//echo "Inside 2 End";
									//echo "AddNewValues4";
								
						   }
						   else
						   {
							  $errormsg=implode(' | ',$row)."not inserted please check To_ /UnitNo /AccountName column in csv file";
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
		
		/*if($data<> '')
		{
		$update_import_history="update `import_history` set wing_flag=1 where society_id='".$_SESSION['society_id']."'";							
		//$_SESSION['wing_flag']=1;
		$res123=$this->m_dbConn->update($update_import_history);
		}*/
	}
	$errormsg="[End of  ReceiptDetails]";
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
	
	public function getLedgerID($LedgerName)
	{
		
		$sql="select `id` from `ledger` where `ledger_name`='".$LedgerName."' ";
		$result = $this->m_dbConn->select($sql);
		return $result[0]['id'];	
		
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