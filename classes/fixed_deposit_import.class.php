<?php
include_once("utility.class.php");
include_once("changelog.class.php");
include_once("dbconst.class.php");
include_once("register.class.php");

class fdImport
{
	
	public $m_dbConn;
	public $errorLog;
	public $m_dbConnRoot;
	private $m_register;
	public $obj_utility;
	public $errofile_name;
	public $m_objLog;
	public $actionPage = "../import_fixed_deposit.php";
	public $debug_mode = 1;
	
	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
		$this->obj_utility = new utility($this->m_dbConn);
		$this->m_objLog = new changeLog($this->m_dbConn);
		$this->m_register = new regiser($this->m_dbConn);
		
		date_default_timezone_set('Asia/Kolkata');		
		$this->errofile_name = 'fd_import_errorlog_'.date("d.m.Y").'_'.rand().'.html';
		$this->errorLog = $this->errofile_name;
	}
	
	public function ImportData($SocietyID)
	{
		$errorfile = fopen($this->errofile_name, "a");
		$result = $this->UploadData($_FILES['upload_files']['tmp_name'][0],$errorfile);
		return $result;
	}
	
	
	public function UploadData($fileName,$errorfile)
	{
		$import_only_newfd = false;
		$file = fopen($fileName,"r");
		
		$errormsg ="[Importing Fixed Deposits Details.....]";
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		
		
	    $sqlYearDetails = "select `BeginingDate` from `year` where `YearID` = '".$_SESSION['society_creation_yearid']."' and `status` ='Y' ";
		$resYearDetails = $this->m_dbConn->select($sqlYearDetails);

		$societyCreationStartDate = (isset($resYearDetails[0]['BeginingDate']) && !empty($resYearDetails[0]['BeginingDate']) )
							? $resYearDetails[0]['BeginingDate'] : "" ;
		
		$rowCount = 0;
		
		//$OpeningBalanceDate;
		//if($_SESSION['default_year_start_date'] <> "")
		//{
			//$OpeningBalanceDate = $this->obj_utility->GetDateByOffset($_SESSION['default_year_start_date'] , -1);
		//}
		
		$OpeningBalanceDate = $this->obj_utility->GetDateByOffset($this->obj_utility->getCurrentYearBeginingDate($_SESSION['society_creation_yearid']) , -1);
		if($debug_mode)
		{
			$errormsg ="\nSociety creation Date :".$societyCreationStartDate;
			$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
			$errormsg ="\nBegining Date". $resYearDetails[0]['BeginingDate'] ;
			$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
			$errormsg ="\nOpening Balance Date :".$OpeningBalanceDate;
			$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		}
		
		$FDImportedCount = 0;
		while (($row = fgetcsv($file)) !== FALSE)
		{
			if($row[0] <> '')
				{
					$rowCount++;
					
					if($rowCount == 1)
					{
						
						$csvColFDNo = array_search(FDRNo,$row,true);
						$csvColFDCategoryName = array_search(Category,$row,true);
						$csvColFDName = array_search(FDName,$row,true);
						$csvColFDBankName = array_search(BankName,$row,true);
						
						$csvColDateofDeposit  = array_search(DateofDeposit,$row,true);
						$csvColDateofMaturity = array_search(DateofMaturity,$row,true);
						$csvColRateofInterest = array_search(RateofInterest,$row,true);
						
						$csvColPrincipalAmt  = array_search(PrincipalAmount,$row,true);
						$csvColMaturityAmt  = array_search(MaturityAmount,$row,true);
						$csvColInterestAccruedOrReceived = array_search(InterestAccruedOrReceived,$row,true);
						$csvColNote  = array_search(Note,$row,true);
						$csvColStatus  = array_search(Status,$row,true);
						
						/*****************************************************************************************************
						echo var_dump(empty($csvColFDNo));
						echo "<br/>csvColFDNo set : ".var_dump(empty($csvColFDNo))." :: " .$csvColFDNo;
						echo "<br/>csvColFDCategoryName set : ".var_dump(empty($csvColFDCategoryName))." :: " .$csvColFDCategoryName;
						echo "<br/>csvColFDName set : ".var_dump(empty($csvColFDName))." :: " .$csvColFDName;
						echo "<br/>csvColFDBankName set : ".var_dump(empty($csvColFDBankName))." :: " .$csvColFDBankName;
						echo "<br/>csvColDateofDeposit set : ".var_dump(empty($csvColDateofDeposit))." :: " .$csvColDateofDeposit;
						echo "<br/>csvColDateofMaturity set : ".var_dump(empty($csvColDateofMaturity))." :: " .$csvColDateofMaturity;
						echo "<br/>csvColRateofInterest set : ".var_dump(empty($csvColRateofInterest))." :: " .$csvColRateofInterest;
						echo "<br/>csvColPrincipalAmt set : ".var_dump(empty($csvColPrincipalAmt))." :: " .$csvColPrincipalAmt;
						echo "<br/>csvColMaturityAmt set : ".var_dump(empty($csvColMaturityAmt))." :: " .$csvColMaturityAmt;
						echo "<br/>csvColInterestAccruedOrReceived set : ".var_dump(empty($csvColInterestAccruedOrReceived))." :: " .$csvColInterestAccruedOrReceived;
						//exit;
						**********************************************************************************************************/
						
						
						if($csvColFDName === "" || $csvColFDNo === "" ||  $csvColFDCategoryName === ""
							|| $csvColFDBankName === "" || $csvColDateofDeposit === ""
							|| $csvColDateofMaturity === "" || $csvColRateofInterest === "" 
							|| $csvColPrincipalAmt === ""|| $csvColMaturityAmt === "" 
							|| $csvColNote === ""|| $csvColStatus === "")
//							|| $csvColInterestAccruedOrReceived === "" || $csvColNote === ""|| $csvColStatus === "")
						{
							$result = ' Required Column Names Not Found in the file being imported. Cant Proceed Further......';
							$errormsg=" Required Column Names Not Found in the file being imported.";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							return $result;
							exit(0);
						}
				   }
				   else
				   {
					   try 
					   {
							$FDNo = $row[$csvColFDNo];
							trim($FDNo);
							$FDName = $row[$csvColFDName];
							trim($FDName);
							$FDledgerId = $this->obj_utility->getLedgerID($FDName);
							$FDCategoryName = $row[$csvColFDCategoryName];
							trim($FDCategoryName);
							$FDBankName = $row[$csvColFDBankName];
							trim($FDBankName);
							$FDBankLedgerId = $this->obj_utility->getLedgerID($FDBankName);
							$DateofDeposit = getDBFormatDate($row[$csvColDateofDeposit]);
							$DateofMaturity = getDBFormatDate($row[$csvColDateofMaturity]);
							$RateofInterest = $row[$csvColRateofInterest];
							$PrincipalAmt = $row[$csvColPrincipalAmt];
							$MaturityAmt = $row[$csvColMaturityAmt];
							$InterestAccruedOrReceived = $row[$csvColInterestAccruedOrReceived];
							
							if($debug_mode)
							{
								//echo "<pre>";print_r($_SESSION);echo "</pre>"; //exit;
								print_r($row);	echo "\n";
							}
							
							$FDCategoryID = 0;
							
							$Note = $row[$csvColNote];
							if(empty($Note))
							{
								//$Note = "This category created from fd import module";
							}
							
							$Status = $row[$csvColStatus]; 							
							$FDPeriod = "";			
							$isFdEligibleForImport = false;
							
							if(!empty($DateofDeposit) && !empty($DateofMaturity))
							{
								$DateDiff = $this->obj_utility->getDateDiffForPeriod($DateofDeposit,$DateofMaturity);
								/*echo "<br/>DateofDeposit".$DateofDeposit;
								echo "<br/>DateofMaturity".$DateofMaturity;
								echo "<br/>societyCreationStartDate".$societyCreationStartDate;*/
								
								$DateDiff2 = $this->obj_utility->getDateDiff($DateofDeposit, $DateofMaturity);
								//$DateDiff3 = $this->obj_utility->getDateDiff($DateofMaturity, $societyCreationStartDate);
								
								if($debug_mode)
								{
									$errormsg ="\nDate of deposit :".$DateofDeposit . ".  Date of Maturity :".$DateofMaturity;
									$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
								}
								
								//if(($DateDiff2 < 0) && ($DateDiff3 >= 0) && $Status == 'Active')
								if($DateDiff2 >= 0)
								{								
									$LogMsg = "Fixed deposit not eligible for import [".$FDName."] because Date of Deposit [".$DateofDeposit."] is not less than date of maturity [".$DateofMaturity."]";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$LogMsg,"E"); 
									
								}
								else if ($FDBankLedgerId <= 0) 
								{
									$LogMsg = "Fixed deposit bank name [".$FDBankName."] does not exists";
								    $this->obj_utility->logGenerator($errorfile,$rowCount,$LogMsg,"E"); 	
								}
								else if(strlen($FDCategoryName) <= 0)
								{
									$LogMsg = "Fixed deposit category [".$FDCategoryName."] cannot be empty";
								    $this->obj_utility->logGenerator($errorfile,$rowCount,$LogMsg,"E"); 	
								}
								else if($Status == 'Active')
								{
									$isFdEligibleForImport = true;	
									if(!empty($DateDiff))
									{
										$FDPeriod = $DateDiff->y . " years, " . $DateDiff->m." months, ".$DateDiff->d." days ";
									}																										
									//$LogMsg = "Fixed deposit eligible for import [".$FDName."]";
									//$this->obj_utility->logGenerator($errorfile,$rowCount,$LogMsg,"I");
								}
								else
								{								
									$LogMsg = "Fixed deposit not eligible for import [".$FDName."] because status [".$Status."] is not Active";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$LogMsg,"E"); 
									
								}
							}
							else
							{
								//DateofDeposit or DateofMaturity empty
								$LogMsg = "Fixed deposit not eligible for import [".$FDName."] hence not imported because Date of Deposit [".$DateofDeposit."] or [".$DateofMaturity."] empty";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$LogMsg,"E"); 
							}

							//Validation completed now FD can be imported
							if($isFdEligibleForImport)
							{
								
								//fd creation process
								if($this->isEntryExistsInFDMaster($FDledgerId,$FDNo))
								{
									//Fixed Deposit Name Or Number Already Exists
									$LogMsg = "Fixed Deposit Name [".$FDName."] or FDNameumber Already Exists [".$FDNo."] hence not imported";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$LogMsg,"E");
								}
								else
								{
									
									$FDCategoryID = $this->getFDCategoryID($FDCategoryName,$errorfile,$rowCount);
									echo "FDCategoryID = " . $FDCategoryID ;
									if($FDledgerId == 0 )
									{
										//If ledger does not exist, then create it
										$opening_balance = $PrincipalAmt;
									
										//only bank ledger exists
										$sqlNewLedger = "INSERT INTO `ledger`(`society_id`, `categoryid`, `ledger_name`, `payment`, `receipt`,`opening_type`,`opening_date`,`opening_balance`) VALUES ('".$_SESSION['society_id']."', '" .$FDCategoryID. "', '" .$FDName. "', 1, 1, 2,'". getDBFormatDate($OpeningBalanceDate)."','" .$opening_balance. "' )";	
							
										$FDledgerId = $this->m_dbConn->insert($sqlNewLedger);
										
										$insertAsset = $this->m_register->SetAssetRegister(getDBFormatDate($OpeningBalanceDate), $FDledgerID, 0, 0, TRANSACTION_DEBIT, $opening_balance, 1);
	
											$LogMsg = "New FD Ledger [".$FDName."] created";
											//$this->obj_utility->logGenerator($errorfile,$rowCount,$LogMsg,"I");																		
									}
									if($FDledgerId > 0)
									{
										//ledger exist in ledger table so add to fd master table
										$sqlFDmaster = "INSERT INTO `fd_master`(`LedgerID`,`fdr_no`, `deposit_date`, `maturity_date`, `int_rate`
																, `principal_amt`, `maturity_amt`,`fd_period`, `fd_close`, `fd_renew`,`note`,`status`,`BankID`) 
																VALUES ('".$FDledgerId."' ,'".$FDNo."' ,'" . $DateofDeposit . "' 
																,'" . $DateofMaturity . "','".$RateofInterest."','".$PrincipalAmt."'
																,'".$MaturityAmt."','".$FDPeriod."'
																,'0','0','".$Note."','Y','".$FDBankLedgerId."')";	
										
										$resFDmaster  =  $this->m_dbConn->insert($sqlFDmaster);
															
										$sqlInsert = "Insert into `fd_close_renew`(`StartDate`,`EndDate`,`LedgerID`,`DepositAmount`,`MaturityAmount`,`ActionType`,`RefNo`) 
														values('" . $DateofDeposit . "','" . $DateofMaturity . "',
														'".$FDledgerId."','".$PrincipalAmt."','".$MaturityAmt."','".FD_CREATED."','".$resFDmaster."')";
									
										$resInsert = $this->m_dbConn->insert($sqlInsert);
										
										if($resFDmaster > 0 && $resInsert > 0)
										{
											$FDImportedCount ++;
											if($debug_mode)
											{
												$LogMsg .= ' New FD Record Inserted in fd Master(LedgerID | name | fdr_no | deposit_date | maturity_date | int_rate 
																| principal_amt | maturity_amt |  fd_deposit_period | fd_close | fd_renew | note | status)';
											
												$LogMsg .= ' ('.$FDledgerId.' | '. $FDName . '|'.$FDNo.'|'.$DateofDeposit
																.'|'.$DateofMaturity.'|'.$RateofInterest.'|'.$PrincipalAmt
																.'|'.$MaturityAmt.'|'.$FDPeriod.'|0|0|'.$Note.',Y)';
											}
											$changeLogID =  $resFDmaster;
											$this->m_objLog->setLog($LogMsg, $_SESSION['login_id'], 'fd_master', $changeLogID);	
											$this->obj_utility->logGenerator($errorfile,$rowCount,$LogMsg,"I");
										}
										else
										{
											$LogMsg  .= 'Error creating FD (' . $FDName . '|'.$FDNo.'|'.$DateofDeposit
																.'|'.$DateofMaturity.')';
											$this->obj_utility->logGenerator($errorfile,$rowCount,$LogMsg,"E");
										}
										
										
									}
									else
									{
										//fd and bank ledgers not exists
										$LogMsg = "Error creating Ledger [".$FDName."]. FD not imported";
										$this->obj_utility->logGenerator($errorfile,$rowCount,$LogMsg,"E");
									}	
								}
							}
							else
							{
								$LogMsg = "Fixed deposit [".$FDName."] not imported.";
								// because Date of Deposit [".$DateofDeposit."] not Smaller Than Date of Maturity [".$DateofMaturity."] or  Date of Maturity not Greater Than Date of Society Creation date[".$societyCreationStartDate."]";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$LogMsg,"E"); 
							}							
							//exit;						  
				 		}
						catch ( Exception $e )
						{
							$errormsg=implode(' | ',$row);
						   	$errormsg .="FD record not imported";
							$this->obj_utility->logGenerator($errorfile,$sr,$errormsg, "E");
							$this->obj_utility->logGenerator($errorfile,$sr,$e);
						}
						
				}
		}
		
	}
	
	
		$rowCount = $rowCount - 1 ;
		$LogMsg = 'Total '.$FDImportedCount.' FDs imported out of '. $rowCount . ' records in the file';
		$this->obj_utility->logGenerator($errorfile,$rowCount,$LogMsg,"I");

		$errormsg="[End of Fixed Deposits]";
		$this->obj_utility->logGenerator($errorfile,'End',$errormsg);
		return  "file imported successfully..";
	
	}
	

	public function isEntryExistsInFDMaster($LedgerID , $FDR_No)
	{
		$isFDExist = false;
		$sqlFD = "select count(*) as cnt from `fd_master` where `LedgerID` = '".$LedgerID."' or  `fdr_no` = '".$FDR_No."' ";
		$resFD = $this->m_dbConn->select($sqlFD);
		
		if(!empty($resFD) && $resFD[0]['cnt'] > 0)
		{
			$isFDExist = true;
		}
		
		return $isFDExist;
	}
	
	public function getCategoryDetailsByCategoryName($category_name)
	{
		
		$query1 = "select category_name,category_id,is_fd_category from `account_category` where `category_name`='".$category_name."' ";
		$data1 = $this->m_dbConn->select($query1);
		
		//If category exist, what is the validity and risk to change its `is_fd_category` flag to true??
		if(sizeof($data1) > 0)
		{
			$is_fd_category = $data1[0]['is_fd_category'];
			//echo "is_fd_category " . $is_fd_category ;
			if($is_fd_category == 0)
			{
				//First need to check if `is_fd_category` already true.
				$query2 = "update `account_category` set `is_fd_category` = '1' where `category_name` = '".$category_name."' and `category_id` = '".$data1[0]['category_id']."'  ";
				$this->m_dbConn->update($query2);
			}
		}
	
		return $data1;
		
	}

	public function getFDCategoryID($FDCategoryName,$errorfile,$rowCount)
	{
		$FDCategoryID = 0;
		if(strlen($FDCategoryName) > 0)
		{
			$FDCategoryData = $this->getCategoryDetailsByCategoryName($FDCategoryName);
		}
										
		//Get or create FD Category
		if(sizeof($FDCategoryData) > 0)
		{
			//If FDCategory already exist
			$FDCategoryID = $FDCategoryData[0]['category_id'];
			$LogMsg = "Catgory [".$FDCategoryName."] exists";
			$this->obj_utility->logGenerator($errorfile,$rowCount,$LogMsg,"I");
		}
		else
		{
			//Else create new FD category 
			$PrimaryCategoryID = 1;	//If we need to give primary category in import file, update this
			$insert_query="insert into account_category (`group_id`,`parentcategory_id`,`category_name`,`description`,`enteredby`,`is_fd_category`)
				 values ('".ASSET."','".$PrimaryCategoryID."','".$FDCategoryName."','This category created from fd import module','".$_SESSION['login_id']."','1')";
			$FDCategoryID = $this->m_dbConn->insert($insert_query);
			if($FDCategoryID >0)
			{
				$LogMsg = "New Category [".$FDCategoryName."] created";
				$this->obj_utility->logGenerator($errorfile,$rowCount,$LogMsg,"I");									
			}
			else
			{
				$LogMsg = "Error creating new category [".$FDCategoryName."]";
				$this->obj_utility->logGenerator($errorfile,$rowCount,$LogMsg,"E");									
			}
		}
		return $FDCategoryID;
	}
}

?>