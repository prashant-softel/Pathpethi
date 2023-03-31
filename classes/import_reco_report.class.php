<?php
include_once("utility.class.php");
include_once("PaymentDetails.class.php");
include_once("ChequeDetails.class.php");
include_once "dbconst.class.php";
//echo "import";
class ImportRecoReport
{
	
	public $m_dbConn;
	public $errorLog;
	public $m_dbConnRoot;
	public $obj_utility;
	public $obj_PaymentDetails;
	public $obj_ChequeDetails;
	public $actionPage = "../import_reco_report.php";
	
	function __construct($dbConnRoot, $dbConn)
	{
		//echo "constructor";
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
		$this->obj_utility= new utility($this->m_dbConn);
		//echo "constructor2";
		$this->obj_PaymentDetails=new PaymentDetails($this->m_dbConn);
		//echo "constructor3";
		$this->obj_ChequeDetails=new ChequeDetails($this->m_dbConn);
		
	}
	public function ImportData($SocietyID)
	{
		//$errofile_name='import_errorlog_'.date("d.m.Y").'_'.rand().'.txt';
		date_default_timezone_set('Asia/Kolkata');		
		$errofile_name='import_reco__report_'.$SocietyID.'_'.date('Y-m-d H:i:sa').'.txt';	
		//echo "Import Data";
		$this->errorLog=$errofile_name;
		$errorfile=fopen($errofile_name, "a");
		//echo $errofile_name;
		$tmp_array=array();	
		if(isset($_POST["Import"]))
		{
			$_REQUEST['TranxType'] = strtolower($_REQUEST['TranxType']);
			$valid_files=array($_REQUEST['TranxType']);
			//print_r($valid_files);
			//$valid_files=array('BuildingID.csv','BuildingID.CSV','WingID.csv','WingID.CSV');
			$limit=count($_FILES['upload_files']['name']);
			//echo "<br>limit:".$limit;
			$success=0;
			 
			 for($m=0;$m<$limit;$m++)
			 {
				$filename=$_FILES['upload_files']['name'][$m];
				$tmp_filename=$_FILES['upload_files']['tmp_name'][$m];
				echo "<br>filename:".$tmp_filename;
				echo "<br>filesize:".sizeof($valid_files);
				for($i=0;$i<sizeof($valid_files);$i++)
				{
					echo "<br>filename valid_files:".$valid_files[$i];
				//echo "<br>filename:".$filename;
					$pos=strpos($filename,$valid_files[$i]);
						
					echo "<br>pos:".$pos;
					if($pos === FALSE)
					{
						//echo "in if";
						echo " pos is false";	
					$message = $filename." is not a valid file";
					//die();
					return $message;
					}
					else
					{
						echo 'check extension...';
						$ext = pathinfo($filename, PATHINFO_EXTENSION);
						if($ext <> '' && $ext <> 'txt' && $ext <> 'csv')
						{	
								
								return $filename.'  Invalid file format selected. Expected *.txt or *.csv file format';
						}
						else
						{
							echo "<br>pos:true";
								$success++;
								echo "i:".$i;
								$tmp_array[$i]=$_FILES['upload_files']['tmp_name'][$m];
								echo 'valid file'.$filename;
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
				// $this->obj_utility->logGenerator($errorfile,2,"test");
				 $logfile="";
				 //echo "tmp_array:";
				 //print_r($tmp_array);
				 $result=$this->startprocess($tmp_array[0],0,$errorfile);
				 //echo "result:".$result;
				 if($result <> '')
				 {
					 $this->actionPage="../import_reco_report.php";
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
		$ChequeLeakBook=array();
		$file = fopen($fileName,"r");
		$data=0;
		$bError = false;
		$ChequeExistance='';
		//fwrite($errorfile,"[Importing WingID]\n");
		$errormsg="[Importing ".$_REQUEST["TranxType"]." Details.....]";
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		echo "checkpoint reached";
		//die();
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
						
						$ChequeNo=array_search(ChequeNo,$row,true);
						$ClearedOn=array_search(ClearedOn,$row,true);
						$SrNo=array_search(Sr,$row,true);
						$VoucherNo=array_search(VSr,$row,true);
						$Amount=array_search(Amount,$row,true);
						 echo 'ChequeNo: <'.$ChequeNo . '> Cleared On <'.$ClearedOn .">";
						 //die();
						 //break;
						/*$Sr=array_search(Sr,$row,true);
						$AccountName=array_search(AccountName,$row,true);
						$ChequeNo=array_search(ChequeNo,$row,true);	
						$ChequeDate=array_search(ChequeDate,$row,true);		
						$Remark=array_search(Remark,$row,true);	
						$Amount=array_search(Amount,$row,true);
						$Rs=array_search(Rs,$row,true);*/
						//print_r($row);
						
						if(!isset($ChequeNo) || !isset($ClearedOn) )
						{
							$result = 'Column Names Not Found Cant Proceed Further......'.'Go Back';
							$errormsg=" Column names in file BankBook not match";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg);
							$bError = true;
							return $result;
							exit(0);
						}
					}
				   else
				   {
					    try {
								$ChequeNumber = $row[$ChequeNo];
								echo 'ChequeNo: <'.$ChequeNumber . '> Cleared On <'.$ClearedDate .">";
								//$ChequeNumber = str_replace(".", "", $ChequeNumber);
								$ClearedDate = $row[$ClearedOn];
								$SerialNo = $row[$SrNo];
								$VoucherNumber = $row[$VoucherNo];
								$AmountValue = $row[$Amount];
								echo 'ChequeNo: <'.$ChequeNumber . '> Cleared On <'.$ClearedDate .">";
								echo "<br/>";
								if($ClearedDate != "" && $ChequeNumber > 0 &&  isset($ClearedDate) && $ClearedDate != "0000-00-00")
								{
									if($_REQUEST['TranxType'] == 'receipts')
									{
								   $select_query1="select bkr.ID,chk.BankID,chk.ChequeNumber,bkr.ReconcileStatus,bkr.Reconcile,bkr.`Reconcile Date` from `chequeentrydetails` as chk JOIN `bankregister` as bkr ON  bkr.ChkDetailID = chk.ID AND chk.BankID ='".$_REQUEST['cbBank']."' where chk.ChequeNumber=".$ChequeNumber ." AND bkr.ReceivedAmount > 0 AND bkr.ReceivedAmount = '" . $AmountValue . "'" ;	

								   //$errormsg .=  $select_query1;	
								   }
								   else if($_REQUEST['TranxType'] == 'payments')
								   {
									   $select_query1="select bkr.ID,pmt.PayerBank,pmt.ChequeNumber,bkr.ReconcileStatus,bkr.Reconcile,bkr.`Reconcile Date` from `paymentdetails` as pmt JOIN `bankregister` as bkr ON  bkr.ChkDetailID = pmt.ID AND pmt.PayerBank ='".$_REQUEST['cbBank']."' where pmt.ChequeNumber=".$ChequeNumber ." AND bkr.PaidAmount > 0";
								   }
								   echo $select_query1;
								   $data = $this->m_dbConn->select($select_query1);
								   echo "<br/>";
								   print_r($data);
								   
									echo "size:".sizeof($data);
									if(sizeof($data) > 0 )
									{
										$DateForDB =  getDBFormatDate($ClearedDate);
										
										for($iCtr = 0; $iCtr < sizeof($data); $iCtr++)
										{	
											$update_query1 ="update `bankregister` set `Reconcile`=1,`ReconcileStatus`=1,`Reconcile Date`='".$DateForDB."' where ID='".$data[$iCtr]['ID'] ."'";
											echo $update_query1;
											echo "<br/>";
										 //$errormsg .=  $select_query1;
										//die();
											$this->m_dbConn->update($update_query1);
										}
									}	
									else
									{
									    $bError = true;
										echo "<br/>Error: Unable to update. No records found for ChequeNo: <".$ChequeNumber. "> ChequeClear <".$ClearedDate."> in database.";
										$errormsg=implode(' | ',$row). "ChequeNo: <".$ChequeNumber. "> ChequeClear <".$ClearedDate.">.";
										$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);
									}
							   }
							   else
							   {
									$bError = true;
									$sErrStr = "";
									if(!isset($ClearedDate) || $ClearedDate == "0000-00-00")
									{
										echo "<br/>Error: not updated because ClearedOn date not found in excel file. ChequeNo: <".$ChequeNumber. "> ChequeClear <".$ClearedDate."> SrNo <".$SerialNo."> VoucherNo <".$VoucherNumber.">.";
									}
									else if(!isset($ChequeNumber) || $ChequeNumber == "")
									{
										echo "<br/>Error: ChequeNo is empty. Unable to update from excel file. ChequeNo: <".$ChequeNumber. "> ChequeClear <".$ClearedDate."> SrNo <".$SerialNo."> VoucherNo <".$VoucherNumber.">.";
									}
									else if(!isset($ChequeNumber) || $ChequeNumber == "ECS")
									{
										echo "<br/>Error: ECS cheques not allowed. Unable to update from excel file. ChequeNo: <".$ChequeNumber. "> ChequeClear <".$ClearedDate."> SrNo <".$SerialNo."> VoucherNo <".$VoucherNumber.">.";
									}
									else
									{
										echo "<br/>Error: Unable to update from excel file. ChequeNo: <".$ChequeNumber. "> ChequeClear <".$ClearedDate."> SrNo <".$SerialNo."> VoucherNo <".$VoucherNumber.">.";
									}
									$errormsg=implode(' | ',$row). "ChequeNo: <".$ChequeNumber. "> ChequeClear <".$ClearedDate."> SrNo <".$SerialNo."> VoucherNo <".$VoucherNumber.">.";
									$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);   
							   }
						
						}
						catch ( Exception $e )
						{
							$bError = true;
						   $errormsg=implode(' | ',$row);
						   $errormsg .="not inserted";
							$this->obj_utility->logGenerator($errorfile,$sr,$errormsg);
							$this->obj_utility->logGenerator($errorfile,$sr,$e);
						}
				}
		}
		
	}
	if($bError)
		{
		//die();
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