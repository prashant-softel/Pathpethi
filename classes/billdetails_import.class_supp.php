<?php
//include_once("include/dbop.class.php");
include_once("defaults.class.php");
include_once("changelog.class.php");
include_once("utility.class.php");
include_once("register.class.php");

class billdetails_import 
{
	
	public $m_dbConn;
	public $m_dbConnRoot;
	private $obj_default;
	public $changeLog;
	private $obj_utility;
	public $errofile_name;
	public $m_objLog;
	public $actionPage = "../import_opening_balance.php";
	
	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
		$this->obj_default = new defaults($this->m_dbConn);
		$this->changeLog = new changeLog($this->m_dbConn);
		$this->obj_utility = new utility($this->m_dbConn);
		date_default_timezone_set('Asia/Kolkata');		
		$this->errofile_name = 'fd_import_errorlog_'.date("d.m.Y").'_'.rand().'.html';
		$this->errorLog = $this->errofile_name;

	}
	
	public function CSVBillDetailsImport()
	{
		//echo '11';
		
		if(isset($_POST["Import"]))
		{
			//echo '1';
			if(isset($_FILES['file']) && $_FILES['file']['error'] == 0)
			{
				
				$result = "0";
				  $ext = pathinfo($_FILES['file'] ['name'], PATHINFO_EXTENSION);
				//$fileName = "files/" . $dateTimeNow. ".csv";
				 $tempName = $_FILES['file'] ['tmp_name'];
				/*
				$original_file_name='BuildingID.csv';
				//echo $_FILES['file'] ['name'];
				if(($_FILES['file'] ['name']) != "$original_file_name") {
					  //exit("Does not match");
					  $result = '<p>File Name Does Not Match(only BuildingID.csv file accepted)...</p>';
					  
					}
				else 
				*/
				if($ext <> '' && $ext <> 'csv')
				{	
					$result = '<p>Invalid file format selected. Expected csv file format</p>';
				}
				else
				{
					//if ( move_uploaded_file ($_FILES['file'] ['tmp_name'], $fileName)  )
					if (isset($_FILES['file']['error']) || is_array($_FILES['file']['error']))
					{  
						$result = '<p> Bill Details Data Uploading Process Started <' . $this->getDateTime() . '> </p>';
						
						$result .= $this->UploadData($tempName);
						
						$result .= '<p> Bill Details Data Uploading Process Complete <' . $this->getDateTime() . '> </p>';
					}
					else
					{ 
						echo $_FILES['file'] ['error'];
						switch ($_FILES['file'] ['error'])
						{
							case 1:
								   echo '<p> The file is bigger than this PHP installation allows</p>';
								   $result = '<p> The file is bigger than this PHP installation allows</p>';
								   break;
							case 2:
								   echo '<p> The file is bigger than this form allows</p>';
								   $result = '<p> The file is bigger than this form allows</p>';
								   break;
							case 3:
								   echo '<p> Only part of the file was uploaded</p>';
								   $result = '<p> Only part of the file was uploaded</p>';
								   break;
							case 4:
								   echo '<p> No file was uploaded</p>';
								   $result = '<p> No file was uploaded</p>';
								   break;
						}
					} 
				}
			}
			else if(isset($_FILES['file']) && $_FILES['file']['error'] <> 0)
			{
				
				//echo '2';
				$errorCode = $_FILES['file']['error']; 
				switch ($errorCode)
				{
					case 1:
						   //echo '<p> The file is bigger than this PHP installation allows</p>';
						   $result = '<p> The file is bigger than this PHP installation allows</p>';
						   break;
					case 2:
						   //echo '<p> The file is bigger than this form allows</p>';
						   $result = '<p> The file is bigger than this form allows</p>';
						   break;
					case 3:
						   //echo '<p> Only part of the file was uploaded</p>';
						   $result = '<p> Only part of the file was uploaded</p>';
						   break;
					case 4:
						   //echo '<p> No file was uploaded</p>';
						   $result = '<p> No file was uploaded</p>';
						   break;
				}
			}
			//echo '<body onload="parent.doneloading(\''.$result.'\')"></body>'; 
			//return $result;
			
		}
		
		else
		{
			//echo '3';
			}
	}
	public function ImportData($SocietyID)
	{ 
		$errorfile = fopen($this->errofile_name, "a");
		$result = $this->UploadData($_FILES['upload_files']['tmp_name'][0],$errorfile);
		
		$message = '';
		if($result == true)
		{
			$message = 'File uploaded successfully';
		}
		else
		{
			$message = 'Error in uploading file';
		}
		
		return $message;
	}
	
	public function UploadData($fileName,$errorfile)
	{
		$this->obj_utility->logGenerator($errorfile,'start','Inside UploadData');
		$bImportSuppBills = true;
		//$filename="C:\\wamp\\www\\OneDrive\\sujit\\beta_aws\\BillDetail.csv";
		if (file_exists($fileName))
		{
		$file = fopen($fileName,"r");
		$errormsg="[Importing BillMainPrevYear]	";
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		$isImportSuccess = false;
		$linecount=0;
		$periodname=$_POST["eperiod"];
		$prevPeriod = $this->getPrevPeriod($_POST['Period']);
		
		if(isset($_POST['Year']))
		{
			$_SESSION['default_year'] = $_POST['Year'];
		}
		
		$UpdateFlag = false;
//		$bImportSuppBills = true;
		}
		while (($row = fgetcsv($file)) !== FALSE)
		{	//echo "<pre>";
			  //print_r($row);
			  //echo "</pre>";
			if($row[0] <> '')
				{
					$rowCount++;
					if($rowCount == 1)
					{  
						$BCode=array_search(BCode,$row,true);
						$FCode=array_search(FCode,$row,true);
						$BillDate=array_search(BillDate,$row,true);
						$DueDate=array_search(DueDate,$row,true);
						$BillNo=array_search(BillNo,$row,true);
						
						$TotalAmount=array_search(BalancePrincipal_PrevYear,$row,true);
						$Interest=array_search(BalanceInterest_PrevYear,$row,true);
						$AmountPayable=array_search(BalanceAmount_PrevYear,$row,true);
						$PrincipalArrears=array_search(BalancePrincipal_PrevYear,$row,true);
						$InterestArrears=array_search(BalanceInterest_PrevYear,$row,true);
						$BillAmount=array_search(BalanceAmount_PrevYear,$row,true);
						//if($bImportSuppBills)
						{
							$SuppBillDate=array_search(SuppBillDate,$row,true);
							$SuppDueDate=array_search(SuppDueDate,$row,true);
							$SuppBillNo=array_search(SuppBillNo,$row,true);
							
							$SuppTotalAmount=array_search(SuppBalancePrincipal_PrevYear,$row,true);
							$SuppInterest=array_search(SuppBalanceInterest_PrevYear,$row,true);
							$SuppAmountPayable=array_search(SuppBalanceAmount_PrevYear,$row,true);
							$SuppPrincipalArrears=array_search(SuppBalancePrincipal_PrevYear,$row,true);
							$SuppInterestArrears=array_search(SuppBalanceInterest_PrevYear,$row,true);
							$SuppBillAmount=array_search(SuppBalanceAmount_PrevYear,$row,true);
						}

						$bImportSuppBills = false;																				
						if(!isset($SuppBillNo) )
						{
							$errormsg=" Supp Columns not founnd in file ".$fileName." BillNo " .$SuppBillNo. ". Suppl bills wont be imported\n";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");
							$bImportSuppBills = false;														
						}
						else
						{
							$errormsg=" Supp Columns founnd in file ".$fileName." BillNo " .$SuppBillNo. ". Suppl bills would be imported\n";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");
							$bImportSuppBills = true;														
						}
						
						$csvColUpdateAssetTableFalg = array_search(UpdateAssetTable,$row,true);
						
						$QtrMonth=array_search(QtrMonth,$row,true);
						if($csvColUpdateAssetTableFalg === "")
						{
							if(!isset($BCode) || !isset($FCode) || !isset($BillDate) ||  !isset($DueDate) ||  !isset($BillNo) ||  !isset($TotalAmount) || !isset($Interest) ||  !isset($AmountPayable) || !isset($PrincipalArrears)  || !isset($InterestArrears) ||  !isset($BillAmount))
							{
								$result = '<p>Column Names Not Found Cant Proceed Further......</p>'.'Go Back';
								$errormsg=" Column names in file ".$fileName." line " .$rowCount. "not match\n";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								
								return $result;
								exit(0);
							}	
						}
						else
						{
							//update asset flag set means file called individualy after society setup
							$UpdateFlag = true;
							if(!isset($FCode) || !isset($BillDate) ||  !isset($DueDate)  ||  !isset($TotalAmount) || !isset($Interest) ||  !isset($AmountPayable) || !isset($PrincipalArrears)  || !isset($InterestArrears) ||  !isset($BillAmount))
							{  
								$result = '<p>Column Names Not Found Cant Proceed Further......</p>'.'Go Back';
								$errormsg=" Column names in file ".$fileName." line " .$rowCount. "not match\n";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								return $result;
								exit(0);
							}	
						}
						
					}
				 else
				   {
					   	$society_code=$row[$BCode];
						$UnitID=$row[$FCode];
						$BillSubTotal=$row[$TotalAmount];
						$BillInterest=$row[$Interest];
						
						$BillNumber=$row[$BillNo];
						$CurrentBillAmount=$row[$BillAmount];
						$PrincipalArrears2 = 0;
						$InterestArrears2 = 0;

						$TotalBillPayable=$row[$AmountPayable];
						$QtrMonth2=$row[$QtrMonth];
						$QtrMonthNewValue=str_replace(",","-",$QtrMonth2);
						if($bImportSuppBills)
						{
							$SuppBillSubTotal=$row[$SuppTotalAmount];
							$SuppBillInterest=$row[$SuppInterest];
							$SuppBillNumber=$row[$SuppBillNo];
							$SuppCurrentBillAmount=$row[$SuppBillAmount];
							$SuppPrincipalArrears2 = 0;
							$SuppInterestArrears2 = 0;
							$SuppTotalBillPayable=$row[$SuppAmountPayable];
						}

						if(strcmp($QtrMonthNewValue,$periodname)==0 && !$UpdateFlag)
						{
$this->obj_utility->logGenerator($errorfile,$rowCount,'Inside if block');
	
									$linecount++;
									
									if($linecount == 1)
									{
											
										$BillDate2=$row[$BillDate];
										$DueDate2=$row[$DueDate];
										$date_range=$this->get_date($prevPeriod);
										$diff1=$this->obj_utility->getDateDiff($date_range,$this->getDBFormatDate($BillDate2));
									    $diff2=$this->obj_utility->getDateDiff($date_range,$this->getDBFormatDate($DueDate2));
									   
									   
									   if($diff1 > 0)
									   {
										   
										$errormsg="BillDate &lt; ".$this->getDBFormatDate($BillDate2)."&gt; "."is &gt; than ".$date_range;
										$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");   
										   
									   }
									   
									   if($diff2 > 0)
									   {
										   
										$errormsg="DueDate &lt;".$this->getDBFormatDate($DueDate2)."&gt;"."is &lt; than ".$date_range;
										$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");   
										   
									   }
									   
									 							$this->obj_utility->logGenerator($errorfile,$rowCount,'Before First Insert');
	  
										$desc = 'data imported..';
										$iLatestChangeID = $this->changeLog->setLog($desc, $_SESSION['login_id'], 'billregister', '--');
										$insert_into_billregister="insert into `billregister`(SocietyID,PeriodID,CreatedBy,BillDate,DueDate,Notes,LatestChangeID) values(".$_SESSION['society_id'].",'".$prevPeriod."','" . $this->m_dbConn->escapeString($_SESSION['login_id']). "','".$this->getDBFormatDate($BillDate2)."','".$this->getDBFormatDate($DueDate2)."','Import Maint Bill Data',".$iLatestChangeID.") ";
									//	echo "insert_into_billregister2 <".$insert_into_billregister.">";
										$datam09=$this->m_dbConn->insert($insert_into_billregister);
									
																
										if($bImportSuppBills)	
										{								
										$insert_into_billregister="insert into `billregister`(SocietyID,PeriodID,CreatedBy,BillDate,DueDate,Notes,LatestChangeID, BillType) values(".$_SESSION['society_id'].",'".$prevPeriod."','" . $this->m_dbConn->escapeString($_SESSION['login_id']). "','".$this->getDBFormatDate($BillDate2)."','".$this->getDBFormatDate($DueDate2)."','Import Supp Bill Data',".$iLatestChangeID.",1) ";
									//	echo "insert_into_billregister2 <".$insert_into_billregister.">";
										$SuppBillRegisterID=$this->m_dbConn->insert($insert_into_billregister);	

										}
									}
								$search_society="select society_id from `society` where society_code='".$society_code."'";
								$result=$this->m_dbConn->select($search_society);
								$society_id=$result[0]['society_id'];
								
								 $search_unit="select unit_id from `unit` where unit_no='".$UnitID."' and society_id='".$society_id."'";
								$result2=$this->m_dbConn->select($search_unit);
								$unit_id=$result2[0]['unit_id'];
								$billDetails_openingBalance = $BillSubTotal + $BillInterest;
								if($bImportSuppBills)	
								{
									$$billDetails_openingBalance = $billDetails_openingBalance + $SuppBillSubTotal + $SuppBillInterest;
								}
								$assetQuery = "SELECT * FROM `assetregister` WHERE `LedgerID` = '".$unit_id."' AND `Is_Opening_Balance` = 1";
								$openingBalance = $this->m_dbConn->select($assetQuery);
								if($openingBalance)
								{
									if($billDetails_openingBalance > 0)
									{
										if(abs($billDetails_openingBalance) <> $openingBalance[0]['Debit'])
										{
											$errormsg = "Ledger &lt;".$UnitID."&gt; Opening Balance &lt;". $openingBalance[0]['Debit'] . " Dr &gt; does not match with Opening Balance of Bill &lt;" . number_format(abs($billDetails_openingBalance),2) . " Dr &gt;";
											$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");  
										}							
									}
									else if($billDetails_openingBalance < 0)
									{
										if(abs($billDetails_openingBalance) <> $openingBalance[0]['Credit'])
										{
											$errormsg =  "Ledger &lt;".$UnitID."&gt; Opening Balance &lt;". $openingBalance[0]['Credit'] . " Cr &gt; does not match with Opening Balance of Bill &lt;" . number_format(abs($billDetails_openingBalance),2) . " Cr &gt;";
											$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");  
										}	
									}
									else
									{
										if((abs($billDetails_openingBalance) <> $openingBalance[0]['Debit']) || (abs($billDetails_openingBalance) <> $openingBalance[0]['Credit']))
										{
											$errormsg =  "Ledger &lt;".$UnitID."&gt; Opening Balance &lt;". $openingBalance[0]['Debit'] . " Dr &gt; does not match with Opening Balance of Bill &lt;" . number_format(abs($billDetails_openingBalance),2) . " Dr &gt;";
											$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");  
										}
									}
								}		
								
								
								 $insert_into_billdetails="insert into `billdetails`(UnitID,PeriodID,BillRegisterID,BillNumber,BillSubTotal,BillInterest,CurrentBillAmount,PrincipalArrears,InterestArrears,TotalBillPayable) 
								values('$unit_id','".$prevPeriod."',".$datam09.",'$BillNumber','$BillSubTotal','$BillInterest','$CurrentBillAmount','$PrincipalArrears2','$InterestArrears2','$TotalBillPayable') ";
								//echo "insert_into_billdetails <".$insert_into_billdetails.">";
								$data=$this->m_dbConn->insert($insert_into_billdetails);
								$isImportSuccess = true;
								$errormsg="bill for unit: &lt;".$UnitID." &gt;:: period: &lt;".$prevPeriod." &gt;imported successfully.";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");  	

								if($bImportSuppBills)
								{
									$isImportSuccess = false;
										 $insert_into_billdetails="insert into `billdetails`(UnitID,PeriodID,BillRegisterID,BillNumber,BillSubTotal,BillInterest,CurrentBillAmount,PrincipalArrears,InterestArrears,TotalBillPayable,BillType) 
									values('$unit_id','".$prevPeriod."',".$SuppBillRegisterID.",'$SuppBillNumber','$SuppBillSubTotal','$SuppBillInterest','$SuppCurrentBillAmount','$SuppPrincipalArrears2','$SuppInterestArrears2','$SuppTotalBillPayable',1) ";
									//echo "insert_into_billdetails <".$insert_into_billdetails.">";
									$data=$this->m_dbConn->insert($insert_into_billdetails);
									$isImportSuccess = true;
									$errormsg="Supp bill for unit: &lt;".$UnitID." &gt;:: period: &lt;".$prevPeriod." &gt;imported successfully.";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");  	
	
								}

						}
						else if($UpdateFlag)
						{
							$this->obj_utility->logGenerator($errorfile,$rowCount,"In the else part"); 
							$linecount++;
							if($linecount == 1)
							{
									$BillDate2=$row[$BillDate];
									$DueDate2=$row[$DueDate];
									if($bImportSuppBills)
									{
										$SuppBillDate2=$row[$SuppBillDate];
										$SuppDueDate2=$row[$SuppDueDate];
									}
									//echo "test3";
									/*//$date_range=$this->get_date($prevPeriod);
									echo "test4";
									 $diff1=$this->obj_utility->getDateDiff($date_range,$this->getDBFormatDate($BillDate2));
									 echo "test5";
								   $diff2=$this->obj_utility->getDateDiff($date_range,$this->getDBFormatDate($DueDate2));
								   echo "test5";
								   
								   if($diff1 > 0)
								   {
									   echo "test3";
									$errormsg="BillDate &lt; ".$this->getDBFormatDate($BillDate2)."&gt; "."is &gt; than ".$date_range;
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");   
									   echo "test4";
								   }
								   
								   if($diff2 > 0)
								   {
									   echo "test5";
									$errormsg="DueDate &lt;".$this->getDBFormatDate($DueDate2)."&gt;"."is &lt; than ".$date_range;
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");   
									   echo "test6";
								   }*/
								}
							   $search_society="select society_id from `society` where society_code='".$society_code."'";
								$result=$this->m_dbConn->select($search_society);
								$society_id=$result[0]['society_id'];
								
								if($UnitID<>'')
								{ 
									if($BillSubTotal=='')
									{  
									$BillSubTotal=0;
									}
									if($BillInterest=='')
									{
										$BillInterest=0;
									}
									if($CurrentBillAmount =='')
									{
										$CurrentBillAmount =0;
									}
									if($bImportSuppBills)
									{
										if($SuppBillSubTotal=='')
										{  
										$SuppBillSubTotal=0;
										}
										if($SuppBillInterest=='')
										{
											$SuppBillInterest=0;
										}
										if($SuppCurrentBillAmount =='')
										{
											$SuppCurrentBillAmount =0;
										}
										$SuppbillDetails_openingBalance = $SuppBillSubTotal + $SuppBillInterest;
									}
							   		 
									$search_unit="select unit_id from `unit` where unit_no='".$UnitID."' and society_id='".$_SESSION['society_id']."'";
									$result2=$this->m_dbConn->select($search_unit);
								 	$unit_id=$result2[0]['unit_id'];
									// print_r($unit_id);
									$billDetails_openingBalance = $BillSubTotal + $BillInterest;
									$assetQuery = "SELECT * FROM `assetregister` WHERE `LedgerID` = '".$unit_id."' AND `Is_Opening_Balance` = 1";
									$openingBalance = $this->m_dbConn->select($assetQuery);
									
									if($_SESSION['society_creation_yearid'] <> "")
									{
										//$OpeningBalanceDate = $this->obj_utility->GetDateByOffset($_SESSION['default_year_start_date'] , -1);
										$OpeningBalanceDate = $this->obj_utility->GetDateByOffset($this->obj_utility->getCurrentYearBeginingDate($_SESSION['society_creation_yearid']) , -1);
										if($OpeningBalanceDate <> "")
										{
											$Date = $OpeningBalanceDate;		
										}		
									}
									$obj_register = new regiser($this->m_dbConn);
									if($openingBalance)
									{
										if(((float)$CurrentBillAmount) < 0)
										{
											$sqlUpdate = "Update ledger SET opening_balance = '" . abs($CurrentBillAmount) .  "', `opening_type` = '1'  Where id = '" .$unit_id . "'" ;
											$result = $this->m_dbConn->update($sqlUpdate);
						
											$sqlUpdate = "Update assetregister SET Credit = '" . abs($CurrentBillAmount) . "', Debit = 0 where LedgerID = '" .$unit_id . "' and Is_Opening_Balance = 1";
											$result = $this->m_dbConn->update($sqlUpdate);
											
											//$insertAsset = $obj_register->SetAssetRegister(getDBFormatDate($Date), $_POST['id'], 0, 0, TRANSACTION_CREDIT, abs($opening_balance), 1);
										}
										else
										{
											$sqlUpdate = "Update ledger SET opening_balance = '" . abs($CurrentBillAmount) .  "', `opening_type` = '2'  Where id = '" .$unit_id . "'" ;
											$result = $this->m_dbConn->update($sqlUpdate);
						
											$sqlUpdate = "Update assetregister SET Debit = '" . abs($CurrentBillAmount) . "', Credit = 0 where LedgerID = '" . $unit_id . "' and Is_Opening_Balance = 1";
											$result = $this->m_dbConn->update($sqlUpdate);
										
											//echo getDBFormatDate($Date);
											//$insertAsset = $obj_register->SetAssetRegister(getDBFormatDate($Date), $_POST['id'], 0, 0, TRANSACTION_DEBIT, abs($opening_balance), 1);	
										
									}										
										
								  }
								  else
								  {
									  if(((float)$CurrentBillAmount) < 0)
										{
											$sqlUpdate = "Update ledger SET opening_balance = '" . abs($CurrentBillAmount) .  "', `opening_type` = '1'  Where id = '" .$unit_id . "'" ;
											$result = $this->m_dbConn->update($sqlUpdate);
						
											$sqlUpdate = "Update assetregister SET Credit = '" . abs($CurrentBillAmount) . "', Debit = 0 where LedgerID = '" .$unit_id . "' and Is_Opening_Balance = 1";
											$result = $this->m_dbConn->update($sqlUpdate);
											
											$insertAsset = $obj_register->SetAssetRegister(getDBFormatDate($Date), $unit_id, 0, 0, TRANSACTION_CREDIT, abs($CurrentBillAmount), 1);
										}
										else
										{
											$sqlUpdate = "Update ledger SET opening_balance = '" . abs($CurrentBillAmount) .  "', `opening_type` = '2'  Where id = '" .$unit_id . "'" ;
											$result = $this->m_dbConn->update($sqlUpdate);
						
											
											//echo getDBFormatDate($Date);
											$insertAsset = $obj_register->SetAssetRegister(getDBFormatDate($Date), $unit_id, 0, 0, TRANSACTION_DEBIT, abs($CurrentBillAmount), 1);	
										
									}	
								  }
								
								$currentYear = $_SESSION['society_creation_yearid'];
								//fetching scoiety start period id 
							  	$sql = "Select  ID from period  where YearID = '" . ($currentYear - 1) . "' and IsYearEnd = 1 ORDER BY  ID ASC";
								$result = $this->m_dbConn->select($sql);
							
								$sqlbillregister = "Select  * from `billregister`  where SocietyID = '" . $society_id . "' and PeriodID = '" . $result[0]['ID'] . "' ";
								$resultbillregister = $this->m_dbConn->select($sqlbillregister);
							
								$sqlbilldetails = "Select  * from `billdetails`  where UnitID = '" . $unit_id . "' and PeriodID = '" . $result[0]['ID'] . "' ";
								$resultbilldetails = $this->m_dbConn->select($sqlbilldetails);
								if(sizeof($resultbillregister) == 0)
								{
									$insert_into_billregister="insert into `billregister`(SocietyID,PeriodID,CreatedBy,BillDate,DueDate,Notes,LatestChangeID) values(".$_SESSION['society_id'].",'".$result[0]['ID']."','" . $this->m_dbConn->escapeString($_SESSION['login_id']). "','".$this->getDBFormatDate($BillDate2)."','".$this->getDBFormatDate($DueDate2)."','Import Data','0') ";
								//echo "Billregister <".$insert_into_billregister.">";
								$datam09 = $this->m_dbConn->insert($insert_into_billregister);
								//print_r ($datam09);
								
									if($bImportSuppBills)
{
									$insert_into_billregister="insert into `billregister`(SocietyID,PeriodID,CreatedBy,BillDate,DueDate,Notes,LatestChangeID, BillType) values(".$_SESSION['society_id'].",'".$result[0]['ID']."','" . $this->m_dbConn->escapeString($_SESSION['login_id']). "','".$this->getDBFormatDate($SuppBillDate2)."','".$this->getDBFormatDate($SuppDueDate2)."','Import Supp Bill Data','0','1') ";
								//echo "Billregister <".$insert_into_billregister.">";
								$Supp_datam09 = $this->m_dbConn->insert($insert_into_billregister);
								//print_r ($datam09);
}
								}
								if(sizeof($resultbilldetails) == 0)
								{
							  		$insert_into_billdetails="insert into `billdetails`(UnitID,PeriodID,BillRegisterID,BillNumber,BillSubTotal,BillInterest,CurrentBillAmount,PrincipalArrears,InterestArrears,TotalBillPayable) 
								values('".$unit_id."','".$result[0]['ID']."',".$datam09.",'$BillNumber','$BillSubTotal','$BillInterest','$CurrentBillAmount','$PrincipalArrears2','$InterestArrears2','$TotalBillPayable') ";
								
								//echo "BillDetail <".$insert_into_billdetails.">";
								
									$data=$this->m_dbConn->insert($insert_into_billdetails);
									$isImportSuccess = true;
									//print_r ($data);
									if($bImportSuppBills)
									{
										$isImportSuccess = false;
							  			$insert_into_billdetails="insert into `billdetails`(UnitID,PeriodID,BillRegisterID,BillNumber,BillSubTotal,BillInterest,CurrentBillAmount,PrincipalArrears,InterestArrears,TotalBillPayable,BillType) 
								values('".$unit_id."','".$result[0]['ID']."',".$Supp_datam09.",'$SuppBillNumber','$SuppBillSubTotal','$SuppBillInterest','$SuppCurrentBillAmount','$SuppPrincipalArrears2','$SuppInterestArrears2','$SuppTotalBillPayable',1) ";
								
								//echo "BillDetail <".$insert_into_billdetails.">";
								
										$data=$this->m_dbConn->insert($insert_into_billdetails);
										$isImportSuccess = true;
									$this->obj_utility->logGenerator($errorfile,$rowCount,"SuppBill inserted","I");
									}
	
								 }
								else
								{ 
									$bill_Detail= implode(' | ',$resultbilldetails[0]);
									$changeLog="insert into `change_log` (ChangedLogDec,ChangedBy,ChangedTable)  values('<br />Original Record :  ".$bill_Detail."','".$_SESSION['login_id']."','billdetails')";
									$changeLogDetail=$this->m_dbConn->insert($changeLog);
							
									$updatebilldetails = "Update `billdetails` set ";
									$updatebilldetails .= "BillSubTotal = '".$BillSubTotal."', ";
									$updatebilldetails .= "BillInterest = '".$BillInterest."', ";
									$updatebilldetails .= "CurrentBillAmount = '".$CurrentBillAmount."', ";
									$updatebilldetails .= "PrincipalArrears = '".$PrincipalArrears2."', ";
									$updatebilldetails .= "InterestArrears = '".$InterestArrears2."', ";
									$updatebilldetails .= "LatestChangeID = '".$changeLogDetail."', ";
									$updatebilldetails .= "TotalBillPayable = '".$TotalBillPayable."' ";
									$updatebilldetails .= "where  `UnitID` = '".$unit_id."' and `PeriodID` = '".$result[0]['ID']."'";
								  	$this->m_dbConn->update($updatebilldetails);	
								  
								  	$isImportSuccess = true;
								}
								$errormsg="bill for unit: &lt;".$UnitID." &gt;:: period: &lt;".$prevPeriod." &gt;imported successfully.";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");  	
							}
							else
							{
								if($UnitID=='')
							{
								$errormsg= "Column names in file ".$fileName." line " .$rowCount. "FCode does not exist in data\n";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							}
						else	
						{
						$errormsg="Column names in file ".$fileName." line " .$rowCount. "not match\n";
						$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
						}
						//$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
					//return $result;
					
								//$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");  
							}
						}
					}
				}
			else
				{
					$result = '<p>Column Names Not Found Cant Proceed Further......</p>'.'Go Back';
					//$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
					}
				
	}
		
	
		if($isImportSuccess)
		{
			$update_import_history="update `import_history` set billdetails_flag=1 where society_id='".$_SESSION['society_id']."'";							
			$res123=$this->m_dbConn->update($update_import_history);
		}
		else
		{
			$errormsg="bill details not imported";
			$this->obj_utility->logGenerator($errorfile,'Error',$errormsg,"E");	
		}
		$errormsg="[End of BillMainPrevYear]";
		$this->obj_utility->logGenerator($errorfile,'End',$errormsg);			
		
		return $isImportSuccess;
						
	}
	
	function getDateTime()
	{
		$dateTime = new DateTime();
		$dateTimeNow = $dateTime->format('Y-m-d H:i:s');
		return $dateTimeNow;
	}
	
	function getDBFormatDate($ddmmyyyy)
	{
		if($ddmmyyyy <> '')
		{
			$ddmmyyyy = str_replace('/', '-', $ddmmyyyy);
			return date('Y-m-d', strtotime($ddmmyyyy));
		}
		else
		{
			return '';
		}
	}
	
	
	public function get_date($id)
	{
		
	$sql="select BeginingDate from `period` where id=".$id." ";
	$data=$this->m_dbConn->select($sql);
	
	return $data[0]['BeginingDate'];
		
	}
	
	function getPrevPeriod($curPeriod)
	{
		$sql = "Select PrevPeriodID from period where ID = '" . $curPeriod . "'";
		$result = $this->m_dbConn->select($sql);
		
		return $result[0]['PrevPeriodID'];
	}
	
	
}

?>