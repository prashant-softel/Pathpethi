<?php
include_once("include/display_table.class.php");
include_once("dbconst.class.php");
include_once("register.class.php");
include_once("voucher.class.php");
include_once("latestcount.class.php");
include_once("changelog.class.php");
//include_once("ChequeDetails.class.php");
include_once ("utility.class.php");
include_once("include/fetch_data.php");
//include_once('../swift/swift_required.php');


class FixedDeposit extends dbop
{
	public $actionPage;
	// = "../FixedDeposit.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	private $m_register;
	public $m_voucher;
	public $m_objLog;
	public $display_pg;
	//public $m_objChequeDetails;
	public $m_objUtility;
	public $obj_fetchData;
	public $m_latestcount;
	public $m_ShowDebugTrace;
	public $unsetArray = array();
	
	function __construct($dbConn , $dbConnRoot = "")
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->display_pg=new display_table($this->m_dbConn);
		$this->m_register = new regiser($this->m_dbConn);
		$this->m_voucher = new voucher($this->m_dbConn);
		$this->m_objLog = new changeLog($this->m_dbConn);
		//$this->m_objChequeDetails = new ChequeDetails($this->m_dbConn);
		$this->m_objUtility = new utility($this->m_dbConn,$this->m_dbConnRoot);
		$this->obj_fetchData = new FetchData($this->m_dbConn);
		$this->m_latestcount = new latestCount($this->m_dbConn);
		$this->m_ShowDebugTrace = 1;
	}

	public function startProcess()
	{
		try
		{	
			$OpeningBalanceDate;
			$changeLogID = "";
			$LogMsg = "";
			$transactionStatus = ""; 
			
			$this->m_dbConn->begin_transaction();	
			$this->m_dbConnRoot->begin_transaction();	
			
			if($_SESSION['society_creation_yearid'] <> "")
			{
				//$OpeningBalanceDate = $this->m_objUtility->GetDateByOffset($_SESSION['default_year_start_date'] , -1);
				$OpeningBalanceDate = $this->m_objUtility->GetDateByOffset($this->m_objUtility->getCurrentYearBeginingDate($_SESSION['society_creation_yearid']) , -1);
			}
			//$_REQUEST['mode'] = "UpdateInterest";
			if($this->m_ShowDebugTrace == 1)
			{
				echo "<BR>Mode: ".$_REQUEST['mode']."<br>";
			}
			if($_REQUEST['mode'] == 'Insert' || $_REQUEST['mode'] == 'Renew')
			{
				$isFDExist = false;
				$LedgerID = 0;
								
				$sqlLedger = "select count(*) as cnt,id from `ledger` where `ledger_name` = '".$_POST['FD_Name']."'";
				$res = $this->m_dbConn->select($sqlLedger);
				
				if($res[0]['cnt'] > 0)
				{
					$LedgerID = $res[0]['id'];		
				}
				
				$sqlFD = "select count(*) as cnt from `fd_master` where `LedgerID` = '".$LedgerID."' or  `fdr_no` = '".$_POST['FDR_No']."' ";
				$resFD = $this->m_dbConn->select($sqlFD);
					
				if($resFD[0]['cnt'] > 0)
				{
					$isFDExist = true;
				}
							
				if($LedgerID == '0' && $_POST['FD_Name'] <> "" && $isFDExist == false)
				{
					//New FD
					if($LedgerID == '0')
					{
						//create new ledger for fd account
						$LedgerName = $_POST['FD_Name'];
						
						$sqlNewLedger = "INSERT INTO `ledger`(`society_id`, `categoryid`, `ledger_name`, `payment`, `receipt`,`opening_type`,`opening_date`,`opening_balance`) 
													VALUES ('".$_SESSION['society_id']."', '" .$_POST['Category'] . "', '" . $LedgerName . "', 1, 1, 2,'".getDBFormatDate($OpeningBalanceDate)."','" .$_POST['Principal_Amount'] . "' )";	
						if($this->m_ShowDebugTrace == 1)
						{
							echo "<BR>sqlNewLedger : " . $sqlNewLedger ;
						}
						$LedgerID = $this->m_dbConn->insert($sqlNewLedger);
						
						if($this->m_ShowDebugTrace == 1)
						{
							echo "<BR>insertAsset : " . getDBFormatDate($OpeningBalanceDate);
							echo "<BR>LedgerID : " . $LedgerID ;
							echo "<BR>insertAsset : " . $$_POST['Principal_Amount'] ;
							echo "<BR>insertAsset : " . $insertAsset ;
						}
						
						$insertAsset = $this->m_register->SetAssetRegister(getDBFormatDate($OpeningBalanceDate), $LedgerID, 0, 0, TRANSACTION_DEBIT,$_POST['Principal_Amount'], 1);
						if($this->m_ShowDebugTrace == 1)
						{
							echo "<BR>insertAsset : " . $insertAsset ;
						}
						
						$sqlFDmaster = "INSERT INTO `fd_master`(`LedgerID`,`fdr_no`, `deposit_date`, `maturity_date`, `int_rate`
												, `principal_amt`, `maturity_amt`,`fd_period`, `fd_close`, `fd_renew`,`note`,`status`,`BankID`) 
												VALUES ('".$LedgerID."' ,'".$_POST['FDR_No']."' ,'".getDBFormatDate($_POST['Deposit_Date'])."' 
												,'".getDBFormatDate($_POST['Maturity_Date'])."','".$_POST['Interest_Rate']."','".$_POST['Principal_Amount']."'
												,'".$_POST['Maturity_Amount']."','".$_POST['FD_Period']."','0'
												,'0','".$_POST['Note']."' ,'Y','".$_POST['FD_Bank_Name']."')";	
						
						if($this->m_ShowDebugTrace == 1)
						{
							echo "<BR>resFDmaster : " . $resFDmaster ;
						}
						$resFDmaster  = $this->m_dbConn->insert($sqlFDmaster);
						
						$sqlInsert = "Insert into `fd_close_renew`(`StartDate`,`EndDate`,`LedgerID`,`DepositAmount`,`MaturityAmount`,				
									`ActionType`,`RefNo`) values('".getDBFormatDate($_POST['Deposit_Date'])."',
									'".getDBFormatDate($_POST['Maturity_Date'])."','".$LedgerID."','".$_POST['Principal_Amount']."',
									'".$_POST['Maturity_Amount']."','".FD_CREATED."','".$_POST['ref']."')";

						if($this->m_ShowDebugTrace == 1)
						{
							echo "<BR>sqlInsert : " . $sqlInsert ;
						}
						$resInsert = $this->m_dbConn->insert($sqlInsert);
						
						$this->sendFDMaturityReminderEmail($_SESSION['society_id'],$LedgerID,$_POST['Maturity_Date']);
						
						$LogMsg  .= 'New FD Record Inserted frm fd Master(LedgerID | name | fdr_no | deposit_date | maturity_date | int_rate 
											| principal_amt | maturity_amt | period | fd_close | fd_renew | note |status)';
						
						$LogMsg .= '('.$LedgerID.'|'. $LedgerName . '|'.$_POST['FDR_No'].'|'.getDBFormatDate($_POST['Deposit_Date'])
											.'|'.getDBFormatDate($_POST['Maturity_Date']).'|'.$_POST['Principal_Amount']
											.'|'.$_POST['Maturity_Amount'].'|'.$_POST['FD_Period'].'|'.$_POST['FD_Close']
											.'|'.$_POST['FD_Renew'].'|'.$_POST['Note'].',Y)';
						if($this->m_ShowDebugTrace == 1)
						{
							echo "<BR>LogMsg : " . $LogMsg ;
						}
						$changeLogID =  $resFDmaster;
					}
					$transactionStatus =  "Insert"; 
				}
				else if($LedgerID <> '0' && $_POST['FD_Name'] <> "" && $isFDExist == false)
				{
					//ledger exist in ledger table so add to fd master table
					$sqlFDmaster = "INSERT INTO `fd_master`(`LedgerID`,`fdr_no`, `deposit_date`, `maturity_date`, `int_rate`
											, `principal_amt`, `maturity_amt`,`fd_period`, `fd_close`, `fd_renew`,`note`,`status`,`BankID`) 
											VALUES ('".$LedgerID."' ,'".$_POST['FDR_No']."' ,'".getDBFormatDate($_POST['Deposit_Date'])."' 
											,'".getDBFormatDate($_POST['Maturity_Date'])."','".$_POST['Interest_Rate']."','".$_POST['Principal_Amount']."'
											,'".$_POST['Maturity_Amount']."','".$_POST['FD_Period']."'
											,'0','0','".$_POST['Note']."','Y','".$_POST['FD_Bank_Name']."')";	
					
					$resFDmaster  =  $this->m_dbConn->insert($sqlFDmaster);
										
					$sqlInsert = "Insert into `fd_close_renew`(`StartDate`,`EndDate`,`LedgerID`,`DepositAmount`,`MaturityAmount`,`ActionType`,`RefNo`) 
									values('".getDBFormatDate($_POST['Deposit_Date'])."','".getDBFormatDate($_POST['Maturity_Date'])."',
									'".$LedgerID."','".$_POST['Principal_Amount']."','".$_POST['Maturity_Amount']."','".FD_CREATED."','".$_POST['ref']."')";
				
					$resInsert = $this->m_dbConn->insert($sqlInsert);
					
					$this->sendFDMaturityReminderEmail($_SESSION['society_id'],$LedgerID,$_POST['Maturity_Date']);
					
					$LogMsg  .= 'New FD Record Inserted frm fd Master(LedgerID | name | fdr_no | deposit_date | maturity_date | int_rate 
										| principal_amt | maturity_amt |  fd_deposit_period | fd_close | fd_renew | note | status)';
					
					$LogMsg .= '('.$LedgerID.' | '. $LedgerName . '|'.$_POST['FDR_No'].'|'.getDBFormatDate($_POST['Deposit_Date'])
										.'|'.getDBFormatDate($_POST['Maturity_Date']).'|'.$_POST['Interest_Rate'].'|'.$_POST['Principal_Amount']
										.'|'.$_POST['Maturity_Amount'].'|'.$_POST['FD_Period'].'|'.$_POST['FD_Close']
										.'|'.$_POST['FD_Renew'].'|'.$_POST['Note'].',Y)';
						
					$changeLogID =  $resFDmaster;	
					$transactionStatus =  "Insert"; 
				}
				else if($isFDExist == true)
				{
					$transactionStatus =  "Fixed Deposit Name Or Number Already Exists"; 
				}
				else
				{
					//return "All * Field Required..";
					$transactionStatus =  "All * Field(s) Required..";
				}
				$this->m_objLog->setLog($LogMsg, $_SESSION['login_id'], 'fd_master', $changeLogID);
				
				//renew fd 
				echo "<BR>test ".$isFDExist;
				echo "<BR>ref ".$_POST['ref'];
				
				if($_REQUEST['mode'] == 'Renew' && $isFDExist == false &&  $_POST['ref'] > 0)
				{
					echo "<BR>Inside Renew mode : " . $_REQUEST['mode'] ;
						$sqlCheck = "SELECT count(*)  as cnt  FROM `fd_close_renew`  where `LedgerID` = '".$LedgerID."'
											 and  `StartDate` =  '" . getDBFormatDate($_POST['Deposit_Date']) . "'  
											 and  `EndDate`  =  '" . getDBFormatDate($_POST['Maturity_Date']) . "'  and  `ActionType` = '".FD_RENEW."' ";
						
						$resCheck = $this->m_dbConn->select($sqlCheck);
						
						$sqlFetchRecord=  "select * from `fd_master` where `id` = '".$_POST['ref']."'";
						$resFetchRecord = $this->m_dbConn->select($sqlFetchRecord);
						
						$sqlPayment = "select  count(*)  as cnt   from  `paymentdetails` where `PaidTo` = '".$resFetchRecord[0]['LedgerID']."'";
						$resCheck2 = $this->m_dbConn->select($sqlPayment);
						
						$sqlcheckBalance  = "select  Count(*) as cnt  from `assetregister` where `LedgerID` = '".$_POST['id']."' and  `Is_Opening_Balance` = 1 and `Debit` <> 0";
						$dataBalance = $this->m_dbConn->select($sqlcheckBalance);
					echo "<BR>Test 11 ";	
						if($resCheck2[0]['cnt'] >  0 || $dataBalance[0]['cnt'] >  0)
						{
							if($resCheck[0]['cnt'] == 0)
							{
								//closing fd account
								$renewArray = $_POST;
								//$renewArray['NewLedgerID'] = $LedgerID;
								echo "<BR>Before fdRenewProcess2 ";
								$returnMsg = $this->fdRenewProcess2($renewArray);
								if($returnMsg  == 'Success')
								{
									echo "<BR>update master " . $_POST['ref'];
									$updateFDMaster = "UPDATE `fd_master` SET `accrued_interest_legder` = '".$_POST['accrued_interest_legder']."',`interest_accrued` = '".$_POST['accrued_interest_amt']."',`interest_legder` = '".$_POST['interest_legder']."',`interest` = '".$_POST['interest_amt']."'
														,`fd_renew` = '1',`status` = 'Y'   WHERE `id` = '".$_POST['ref']."' ";
						
									$dataFDMaster = $this->m_dbConn->update($updateFDMaster);
									
									$transactionStatus =  "Renew";
									$LogMsg  .= "FD Renewed.";
								}
								else
								{
									$transactionStatus = $returnMsg;	
								}
							}
							else
							{
								//return 'FD Already Renewed';	
								$transactionStatus =  "FD Already Renewed";
								$LogMsg  .= "FD Already Renewed.";
							}
						}
						else
						{
								$transactionStatus =  "FD Not Renewed";
								$LogMsg  .= "FD Not Renewed.";
								$updateFDMaster2 = "UPDATE `fd_master` SET  `fd_renew` = '0'  WHERE `id` = '".$_POST['ref']."'  ";
								$dataFDMaster2 = $this->m_dbConn->update($updateFDMaster2);
						}
				}
				
			}
			else if($_REQUEST['mode'] == 'Update')
			{
				echo "Coming here?<br>";
				$LedgerID = $_POST['id'];
				$EntryExistsInFDMaster = false; 
				if($LedgerID <> '0' && $_POST['FD_Name'] <> "")
				{
					echo $sql = "select count(*) as cnt from `fd_master` where LedgerID = '".$_POST['id']."' and  `fdr_no` = '".$_POST['FDR_No']."' ";
					$data = $this->m_dbConn->select($sql);	
					
					if($data[0]['cnt'] > 0)
					{
						$EntryExistsInFDMaster = true; 
					}
				}
				if($LedgerID <> '0' && $EntryExistsInFDMaster == false)
				{
					echo "<BR>if Ledger <> 0 part ";
					//ledger exist in ledger table so add to fd master table
					$sqlFDmaster = "INSERT INTO `fd_master`(`LedgerID`,`fdr_no`, `deposit_date`, `maturity_date`, `int_rate`, `principal_amt`
											, `maturity_amt`,`fd_period`, `fd_close`, `fd_renew`,`note`,`status`,`BankID`,`accrued_interest_legder`,`interest_accrued`,`interest_legder`,`interest`) 
											VALUES ('".$LedgerID."' ,'".$_POST['FDR_No']."' ,'".getDBFormatDate($_POST['Deposit_Date'])."' ,'".getDBFormatDate($_POST['Maturity_Date'])."'
											,'".$_POST['Interest_Rate']."','".$_POST['Principal_Amount']."','".$_POST['Maturity_Amount']."','".$_POST['FD_Period']."'
											,'".$_POST['FD_Close']."','".$_POST['FD_Renew']."','".$_POST['Note']."','Y','".$_POST['FD_Bank_Name']."' ,'".$_POST['accrued_interest_legder']."','".$_POST['accrued_interest_amt']."','".$_POST['interest_legder']."','".$_POST['interest_amt']."')";	
					$resFDmaster  =  $this->m_dbConn->insert($sqlFDmaster);
					
					$sqlInsert = "Insert into `fd_close_renew`(`StartDate`,`EndDate`,`LedgerID`,`DepositAmount`,`MaturityAmount`,`ActionType`,`RefNo`) 
									values('".getDBFormatDate($_POST['Deposit_Date'])."','".getDBFormatDate($_POST['Maturity_Date'])."',
									'".$LedgerID."','".$_POST['Principal_Amount']."','".$_POST['Maturity_Amount']."','".FD_CREATED."','".$_POST['ref']."')";
				
					$resInsert = $this->m_dbConn->insert($sqlInsert);
					
					$this->sendFDMaturityReminderEmail($_SESSION['society_id'],$LedgerID,$_POST['Maturity_Date']);
					
						
					$LogMsg  .= 'New FD Record Inserted from fd update(LedgerID | fd_name | fdr_no | deposit_date | maturity_date | int_rate 
										| principal_amt | maturity_amt | fd_deposit_period  fd_close | fd_renew | note)';
					
					$LogMsg .= '('.$LedgerID.'|'. $LedgerName . '|'.$_POST['FDR_No'].'|'.getDBFormatDate($_POST['Deposit_Date']).'|'.getDBFormatDate($_POST['Maturity_Date'])
										.'|'.$_POST['Interest_Rate'].'|'.$_POST['Principal_Amount'].'|'.$_POST['Maturity_Amount'].'|'.$_POST['FD_Period']
										.'|'.$_POST['FD_Close'].'|'.$_POST['FD_Renew'].'|'.$_POST['Note'].')';
						
					$changeLogID = $resFDmaster;
				}
				else if($EntryExistsInFDMaster == true)
				{
					/*if($_POST['FD_Close'] != 1)
					{*/
						//Pending : Need to call new function here to create JV for year end updates or when interest is paid to account					
						$updateFDMaster = "UPDATE `fd_master` SET  `fdr_no` = '".$_POST['FDR_No']."',`maturity_date` = '".getDBFormatDate($_POST['Maturity_Date'])."'
													,`int_rate` = '".$_POST['Interest_Rate']."',`principal_amt` = '".$_POST['Principal_Amount']."',`maturity_amt` = '".$_POST['Maturity_Amount']."'
													,`accrued_interest_legder` = '".$_POST['accrued_interest_legder']."',`interest_accrued` = '".$_POST['accrued_interest_amt']."',`interest_legder` = '".$_POST['interest_legder']."',`interest` = '".$_POST['interest_amt']."',`fd_period` = '".$_POST['FD_Period']."',`fd_close` = '".$_POST['FD_Close']."'
													,`fd_renew` = '".$_POST['FD_Renew']."',`note` = '".$_POST['Note']."' , `status` = 'Y' ,`BankID` = '".$_POST['FD_Bank_Name']."'  WHERE `LedgerID` = '".$_POST['id']."' ";
					
						$dataFDMaster = $this->m_dbConn->update($updateFDMaster);
				
					
						$sqlfetchID = "select `id` from `fd_master` where  `LedgerID` = '".$_POST['id']."'";
						$resfetchID = $this->m_dbConn->select($sqlfetchID);
					
						$LogMsg  .= 'Existing  FD Record Updated frm fd Master(LedgerID | fdr_no | deposit_date | maturity_date | int_rate | 
										principal_amt | maturity_amt| fd_deposit_period  | fd_close | fd_renew | note)';
					
						$LogMsg .= '('.$_POST['id'].'|'.$_POST['FDR_No'].'|'.getDBFormatDate($_POST['Deposit_Date'])
										.'|'.getDBFormatDate($_POST['Maturity_Date']).'|'.$_POST['Interest_Rate'].'|'.$_POST['Principal_Amount']
										.'|'.$_POST['Maturity_Amount'].'|'.$_POST['FD_Period']
										.'|'.$_POST['FD_Close'].'|'.$_POST['FD_Renew'].'|'.$_POST['Note'].')';
						
						$changeLogID = 	$resfetchID[0]['id'];
						$this->sendFDMaturityReminderEmail($_SESSION['society_id'],$_POST['id'],$_POST['Maturity_Date']);
						//$this->m_objLog->setLog($LogMsg, $_SESSION['login_id'], 'fd_master', $resfetchID[0]['id']);
					
				}
				
				//if($_POST['FD_Close'] != 1)
				//{				
					$sqlII = "select  count(*)  as cnt   from  `paymentdetails` where `PaidTo` = '".$LedgerID."'";
					$resII = $this->m_dbConn->select($sqlII);
				
					if($resII[0]['cnt'] == 0)
					{
						$updateAsset = "UPDATE `assetregister` SET `Debit` = '".$_POST['Principal_Amount']."'   WHERE `LedgerID` = '".$LedgerID."' ";
						
						$dataAsset = $this->m_dbConn->update($updateAsset);
						//Pending : This could be a potential problem to mismatch balance sheet. Take a look later
						$updateII = "UPDATE `ledger` SET `opening_balance` = '".$_POST['Principal_Amount']."'   WHERE `id` = '".$LedgerID."' ";
						
						$dataII = $this->m_dbConn->update($updateII);
					}			
				
					$updateIII = "UPDATE `ledger` SET `ledger_name` = '".$_POST['FD_Name']."'   WHERE `id` = '".$LedgerID."' ";
							
					$dataIII = $this->m_dbConn->update($updateIII);
				//}
				
				if($_POST['FD_Close'] == 1)
				{
					//will never come here
					$sql02 = "select * from fd_master where LedgerID = '".$_POST['id']."'";
					$sql22 = $this->m_dbConn->select($sql02);
					
					echo $sqlCheck = "SELECT count(*)  as cnt  FROM `fd_close_renew`  where `LedgerID` = '".$_POST['id']."'
										 and  `StartDate` =  '" . getDBFormatDate($sql22[0]['deposit_date']) . "'  
										 and  `EndDate`  =  '" . getDBFormatDate($sql22[0]['maturity_date']) . "'  and  `ActionType` = '".FD_CLOSED."' ";
					
					$resCheck = $this->m_dbConn->select($sqlCheck);
					echo $sqlcheckBalance  = "select  Count(*) as cnt  from `assetregister` where `LedgerID` = '".$_POST['id']."' and  `Is_Opening_Balance` = 1 and `Debit` <> 0";
					$dataBalance = $this->m_dbConn->select($sqlcheckBalance);
					
					echo $sqlPayment = "select  count(*)  as cnt   from  `paymentdetails` where `PaidTo` = '".$_POST['id']."'";
					$resCheck2 = $this->m_dbConn->select($sqlPayment);
					if($resCheck2[0]['cnt'] >  0 || $dataBalance[0]['cnt'] >  0)
					{
						if($resCheck[0]['cnt'] == 0)
						{
							//closing fd account
							echo "<BR>Interest Accoured amount " .  $InterestAccuredSoFar_plus_PrincipalAmt = $this->getAccuedInterestFromFD_JVTable($_POST['ref'], $_POST['accrued_interest_legder']);
							echo "<BR>Interest amount " .  $InterestEarnedSoFar = $this->getTotalInterestFromFD_JVTable($_POST['ref'], $_POST['interest_legder']);
							//echo "<BR>Final amount " . $final_amt = $InterestAccuredSoFar_plus_PrincipalAmt + $InterestEarnedSoFar + $_POST['interest_amt'];
							echo "<BR>Final amount " . $final_amt = $InterestEarnedSoFar + $_POST['interest_amt'];

							//echo "<br>Bank Name: ".$_POST['FD_Bank_Name'];
							//echo "Matu date ".$sql22[0]['maturity_date']."<br>p_amt ".$sql22[0]['principal_amt']."<br>matu amt: ".$sql22[0]['maturity_amt']."<br>aacr. int. ledger: ".$_POST['accrued_interest_legder']."<br>acc. int.: ".$final_amt."<br>int. led: ".$_POST['interest_legder']."<br>int amt: ".$_POST['interest_amt']."<br>Note: ".$_POST['Interest_Note'];
							//Validate finalamounts and maturity amount
							$returnMsg = $this->fdCloseProcess($_POST['id'] ,$_POST['FD_Bank_Name'],$sql22[0]['maturity_date'],$sql22[0]['principal_amt'],$sql22[0]['maturity_amt'], $_POST['accrued_interest_legder'],$final_amt,$_POST['interest_legder'],$_POST['interest_amt'],$_POST['Interest_Note'] );
							//public function fdCloseProcess($LedgerID , $BankID ,$MaturityDate , $PrincipalAmount ,$MaturityAmount ,$AccruedInterestLegder ,$AccruedInterestAmt ,$InterestLedger,$InterestAmt,$Note)
							echo "<BR>return msg " . $returnMsg;
							if($returnMsg  == 'Success')
							{
								$transactionStatus = 'Closed';
								$LogMsg  .= "FD Closed.";
							}
							else
							{
									$transactionStatus = $returnMsg;
							}
						}
						else
						{
							//return 'FD Already Closed';	
							$transactionStatus =  "FD Already Closed";
							$LogMsg  .= "FD Already Closed.";
						}
					}
					else
					{
							$transactionStatus =  "FD Not Closed";
							$LogMsg  .= "FD Not Closed.";
							$updateFDMaster2 = "UPDATE `fd_master` SET  `fd_close` = '0'  WHERE `LedgerID` = '".$_POST['id']."' ";
							$dataFDMaster2 = $this->m_dbConn->update($updateFDMaster2);
					}
						
				}
				/*else
				{
					echo "<BR>Inside else part to update";
					//Update fd interest
					
					$returnMsg = $this->fdInterestUpdate($_POST['id'] ,$_POST['FD_Bank_Name'],$_POST['Maturity_Date'] ,$_POST['Principal_Amount'] ,$_POST['Maturity_Amount'] , $_POST['accrued_interest_legder'],$_POST['accrued_interest_amt'],$_POST['interest_legder'],$_POST['interest_amt'],$_POST['Interest_Note'], $_POST['FD_Bank_Payout'], $_POST['Interest_Date'] );
					echo "<BR>AFter fdInterestUpdate";
					if($returnMsg  <> 0)
					{					
						echo "<BR>VoucherID : " . $returnMsg;
						$transactionStatus = 'Interest Updated';
						$LogMsg  .= "FD Closed.";
					}
					else
					{
							$transactionStatus = "Error updating Interest ";
					}												
				}	
				
				$this->m_objLog->setLog($LogMsg, $_SESSION['login_id'], 'fd_master', $changeLogID);
				if($transactionStatus == "")
				{
					$transactionStatus = "Update";
				}*/				
			}
			else if($_REQUEST['mode'] == 'UpdateInterest')
			{
				$LedgerID = $_POST['id'];
				
				//updating accrued interest and interest ledgers
				$fd_id  = $_POST['ref'];
				echo "Ref:".$fd_id."<br>";
				$acc_int_ledger = $_POST['accrued_interest_legder'];
				$int_ledger = $_POST['interest_legder'];
				
				echo "Bank Name: ".$_POST['FD_Bank_Name']."<br>";
				echo "Category: " .$_POST['Category']."<br>";
				echo "Acc. int. led: ".$acc_int_ledger."<br>";
				echo "Int led: ".$int_ledger;
				//die();
				$this->update_ledgers($fd_id,$int_ledger,$acc_int_ledger);
				
				$EntryExistsInFDMaster = false; 
				echo $sql01 = "select * from fd_master where id = '".$fd_id."'";
				$sql11 = $this->m_dbConn->select($sql01);
				
				if($LedgerID <> '0' && $sql11[0]['fdr_no'] <> "")
				{
					$sql = "select count(*) as cnt from `fd_master` where LedgerID = '".$_POST['id']."' and  `fdr_no` = '".$sql11[0]['fdr_no']."' ";
					$data = $this->m_dbConn->select($sql);	
					
					if($data[0]['cnt'] > 0)
					{
						$EntryExistsInFDMaster = true;
						echo "<BR>FD Close" . $_POST['FD_Close']; 
						echo "<BR>FD Renew" . $_POST['FD_Renew']; 
						echo "<BR>";
						if($_POST['FD_Close'])
						{
							
							$InterestAccuredSoFar = $this->getAccuedInterestFromFD_JVTable($sql11 [0]['id'], $_POST['accrued_interest_legder']);
							echo "<BR>InterestAccuredSoFar " . $InterestAccuredSoFar;
							echo "<BR>Accured interest_amt in edit control " . $_POST['accrued_interest_amt'];
							echo "<BR>interest_amt in edit control " . $_POST['interest_amt'];
//							echo "<BR>Final amount " . $final_amt = $InterestAccuredSoFar_plus_PrincipalAmt + $_POST['interest_amt'];
							
							//closing fd account
							echo "<BR>Interest amount " .  $InterestEarnedSoFar = $this->getTotalInterestFromFD_JVTable($sql11 [0]['id'], $_POST['interest_legder']);
							echo "<BR>Final amount " . $final_amt = $InterestEarnedSoFar + $_POST['interest_amt'];

							
							$returnMsg = $this->fdCloseProcess($_POST['ref'],$sql11 [0]['BankID'],$sql11[0]['maturity_date'],$sql11[0]['principal_amt'],$sql11[0]['maturity_amt'], $_POST['accrued_interest_legder'],$InterestAccuredSoFar,$_POST['interest_legder'],$_POST['interest_amt'],$_POST['Interest_Note'], $_POST['Interest_Date'] );

							if($returnMsg  == 'Success')
							{
								$transactionStatus = 'Closed';
								$LogMsg  .= "FD Closed.";
							}
							else
							{
								$transactionStatus = $returnMsg;
							}												
						}
						else if($_POST['FD_Renew'])
						{							
							echo "<BR>Renew New test1<BR>";
								$renewArray = $_POST;
								//$renewArray['NewLedgerID'] = $LedgerID;
								//var_dump($renewArray );
								$returnMsg = $this->fdRenewProcess2($renewArray);
								
								if($returnMsg  == 'Success')
								{
									$updateFDMaster = "UPDATE `fd_master` SET `accrued_interest_legder` = '".$_POST['accrued_interest_legder']."',`interest_accrued` = '".$_POST['accrued_interest_amt']."',`interest_legder` = '".$_POST['interest_legder']."',`interest` = '".$_POST['interest_amt']."'
														,`fd_renew` = '1',`status` = 'Y'   WHERE `id` = '".$_POST['ref']."' ";
						
									//$dataFDMaster = $this->m_dbConn->update($updateFDMaster);
									echo "<BR>$updateFDMaster ". $updateFDMaster ;
									$transactionStatus =  "Renew";
									$LogMsg  .= "FD Renewed.";
								}
								else
								{
									$transactionStatus = $returnMsg;	
								}
							
						}
						else
						{
							//Update fd interest
							echo "<BR>New fdInterestUpdate";
	
							$returnMsg = $this->fdInterestUpdate($_POST['ref'], $_POST['accrued_interest_legder'],$_POST['accrued_interest_amt'],$_POST['interest_legder'],$_POST['interest_amt'],$_POST['Interest_Note'], $_POST['FD_Bank_Payout'], $_POST['Interest_Date'] );
	
							echo "<BR>AFter new fdInterestUpdate";
						
							if($returnMsg  == 'Success')
							{
								$transactionStatus = "Interest " . $_POST['interest_amt'] . " Updated";
								$LogMsg  .= "Interest " . $_POST['interest_amt'] . " Updated.";
							}
							else
							{
								$transactionStatus = $returnMsg;
							}												
						
						}						
					}
				}
				
				if($LedgerID <> '0' && $EntryExistsInFDMaster == false)
				{
					echo "<BR>FD with id " . $_POST['ref'] . " not found<BR>";
				}
			}	
			else
			{
				$transactionStatus =  $errString;
			}
			//echo $transactionStatus;
			$this->m_dbConn->commit();
			$this->m_dbConnRoot->commit();
			return $transactionStatus;
		}
		catch(Exception $exp)
		{
			$this->m_dbConn->rollback();
			$this->m_dbConnRoot->rollback();
			return $exp->getMessage();
		}
		
	}
	public function combobox($query,$id, $defaultText = 'Please Select', $defaultValue = '')
	{
		$str = '';
		
		if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "'>" . $defaultText . "</option>";
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
	
	
	public function pgnation($type)
	{
		
		$fdAccountArray =  $this->FetchFdCategories();
		$fdAccountArray = implode(',', $fdAccountArray);
		$ledgername_array = array();
		
		$get_ledger_name = "select id,ledger_name from `ledger`";
		$result02 = $this->m_dbConn->select($get_ledger_name);
		
		for($i = 0; $i < sizeof($result02); $i++)
		{
			$ledgername_array[$result02[$i]['id']] = $result02[$i]['ledger_name'];
		}
		
		//$sql1 = "select led.id as ledger_id,concat_ws('-',led.ledger_name,led.id) as name, led.id as status,fd.fdr_no,DATE_FORMAT(led.opening_date  + INTERVAL 1 DAY, '%d-%m-%Y') as opening_date,DATE_FORMAT(fd.maturity_date, '%d-%m-%Y') as maturity_date,fd.int_rate,fd.principal_amt,fd.maturity_amt,fd.interest_accrued,fd.fd_deposit_period,fd.interest_frequency,fd.note,fd.fd_close,fd.fd_renew from ledger as led LEFT JOIN fd_master as fd ON led.id = fd.LedgerID  where led.categoryid IN (" . $fdAccountArray . ")and led.society_id=".$_SESSION['society_id']." and led.status = 'Y'";
		$sql1 = "select led.id as ledger_id,concat_ws('-',led.ledger_name,led.id) as name,ac.category_name,fd.BankID as Bank ,led.id as status,fd.fdr_no,DATE_FORMAT(fd.deposit_date, '%d-%m-%Y') as deposit_date,DATE_FORMAT(fd.maturity_date, '%d-%m-%Y') as maturity_date,fd.int_rate,fd.principal_amt,fd.maturity_amt,fd.fd_period,fd.accrued_interest_legder,fd.interest_accrued,fd.interest_legder,fd.interest,fd.note,fd.fd_close,fd.fd_renew,fd.id as fd_id from ledger as led LEFT JOIN fd_master as fd ON led.id = fd.LedgerID JOIN account_category as ac ON led.categoryid = ac.category_id where led.categoryid IN (" . $fdAccountArray . ")and led.society_id=".$_SESSION['society_id']." and led.status = 'Y'";
		
		if($type == 4)
		{
			$sql1 .= " and fd.fd_renew =1 and fd.fd_close = 0";			
		}
		else if($type == 5)
		{
			$sql1 .= " and fd.fd_close =1 and  fd.fd_renew = 0";	
		}
		
		//echo $sql1;
		$result = $this->m_dbConn->select($sql1);
		
		for($i = 0;$i < sizeof($result);$i++)
		{
				$sqlcheck = "select  Count(*) as cnt  from `paymentdetails` where `PaidTo` = '".$result[$i]['ledger_id']."' ";
				$data = $this->m_dbConn->select($sqlcheck);
				
//				$sqlcheckBalance  = "select  Count(*) as cnt  from `assetregister` where `LedgerID` = '".$result[$i]['ledger_id']."' and  `Is_Opening_Balance` = 1 and `Debit` <> 0";
				$sqlcheckBalance  = "select  Count(*) as cnt  from `assetregister` where `LedgerID` = '".$result[$i]['ledger_id']."' and `Debit` <> 0";
				$dataBalance = $this->m_dbConn->select($sqlcheckBalance);
				
				$result[$i]['Bank'] = $ledgername_array[$result[$i]['Bank']];
				
				if(($data[0]['cnt'] > 0 || $dataBalance[0]['cnt'] > 0)  && $result[$i]['fd_close'] == 1)
				{
						$result[$i]['status'] = '<font color="#00F" ><b>Closed</b></font>';
				}
				else if(($data[0]['cnt'] > 0 || $dataBalance[0]['cnt'] > 0)   && $result[$i]['fd_renew'] == 1)
				{
						$result[$i]['status'] = '<font color="#00CC33" ><b>Renewed</b></font>';	
				}
				else if(($data[0]['cnt'] > 0 || $dataBalance[0]['cnt'] > 0) && $result[$i]['fd_close'] == 0  && $result[$i]['fd_renew'] == 0 )
				{
						$result[$i]['status'] = '<font color="#00CC33" ><b>Active</b></font>';
				}
				else if($data[0]['cnt'] == 0 && $dataBalance[0]['cnt'] == 0 && $result[$i]['fd_close'] == 0  && $result[$i]['fd_renew'] == 0)
				{
						$result[$i]['status'] = '<font color="#FF0000" ><b>Pending</b></font>';	
				}
				
				$result[$i]['accrued_interest_legder'] = $ledgername_array[$result[$i]['accrued_interest_legder']];
				$result[$i]['interest_legder'] = $ledgername_array[$result[$i]['interest_legder']];
				unset($result[$i]['fd_close']);
				unset($result[$i]['fd_renew']);
				
				if($result[$i]['status'] == '<font color="#00F" ><b>Closed</b></font>' || $result[$i]['status'] == '<font color="#00CC33" ><b>Renewed</b></font>')
				{
					$this->array_splice_assoc ($result[$i] ,"name", 0, array('Renew' =>'<center><font  color="#FF0000"><b>Not Allowed</center></b></font>'));
				}
				else
				{
					$this->array_splice_assoc ($result[$i] ,"name", 0, array('Renew' =>'<center><a  onClick="Renew('.$result[$i]["ledger_id"].')"><img src="images/renew1.png" border="0" alt="Renew" style="cursor:pointer;"   width="18" height="15"/></a></center>'));	
				}
				
				if($type == 2 && $result[$i]['status'] <>  '<font  color="#FF0000"><b>Pending</b></font>')
				{
					array_push($this->unsetArray,$i);		
				}
				else if($type == 3 && $result[$i]['status'] <> '<font color="#00CC33" ><b>Active</b></font>')
				{
					array_push($this->unsetArray,$i);		
				}
				$URL = "UpdateFDInterest.php?edt=".$result[$i]["ledger_id"]."&fdreadonly=1&fd_id=".$result[$i]['fd_id']."&status=".strip_tags($result[$i]["status"]);
				$result[$i]["principal_amt"] = "<a href='".$URL."'>".$result[$i]["principal_amt"]."</a>";
				$result[$i]["fdr_no"] = "<a href='".$URL."' target='_blank'>".$result[$i]["fdr_no"]."</a>";
				$result[$i]["Bank"] = "<a href='BankAccountDetails.php'>".$result[$i]["Bank"]."</a>";
				
				//view_ledger_details.php?lid=' + iden[1] + '&gid=' + Groupid
				$ledgerParent = $this->m_objUtility->getParentOfLedger($result[$i]["ledger_id"]);
				$group_id = $ledgerParent['group'];
				
				$result[$i]["name"] = "<a href='#' onClick=window.open('view_ledger_details.php?lid=".$result[$i]["ledger_id"]."&gid=".$group_id."','popup','type=fullWindow,fullscreen,scrollbars=yes');>".$result[$i]["name"]."</a>";
				unset($result[$i]['fd_id']);
				unset($result[$i]['Renew']);
					
				//print_r($result[$i]);
				//echo "<br>";
		}
		//print_r($result);
		$result = $this->UnsetArray($result);
				
		if(sizeof($result) > 0)
		{
			$this->display1($result);
		}
		else
		{
			$result =  '<font color="#FF0000" >No Records Found</font>';	
			return $result;
		}
	}
	
	public function display1($rsas)
	{
		$thheader = array('FD Name','Category','Bank Name','Status','FDR No','Date of Deposit','Date of Maturity','Rate of Interest (%)','Principal Amount (Rs.)','Maturity Amount (Rs.)','Period of Deposit','Accrued Interest Legder','Accrued Interest (Rs.)','Interest Legder','Interest (Rs.)','Note');
		$this->display_pg->edit		= "getDetails";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "FixedDeposit.php";
		$this->display_pg->view		= "getDetails";
		$this->display_pg->renew		= "getDetails";
		$res = $this->display_pg->display_datatable($rsas,true,false,true);
		return $res;
	}
	
	public function selecting()
	{
		$FDArray = array();
		$sqlI = "select `id`,`ledger_name`,`opening_balance`,`categoryid`  from ledger  where `society_id` = ".$_SESSION['society_id']." and `status` = 'Y' and `id` = '".$_REQUEST['LedgerID']."' ";
		$resI = $this->m_dbConn->select($sqlI);
		
		$sqlII = "select  fd.id,fd.BankID, fd.fdr_no,principal_amt,DATE_FORMAT(fd.maturity_date, '%d-%m-%Y') as maturity_date
					,DATE_FORMAT(fd.deposit_date, '%d-%m-%Y') as deposit_date,fd.int_rate,fd.fd_period
					,fd.maturity_amt,fd.accrued_interest_legder,fd.interest_accrued,fd.interest_legder,fd.interest,fd.note,fd.fd_close,fd.fd_renew from fd_master as fd 
					JOIN ledger as led ON led.id = fd.LedgerID where led.society_id=".$_SESSION['society_id']." 
					and fd.status='Y' and fd.LedgerID = '".$_REQUEST['LedgerID']."' ";
		
		$resII = $this->m_dbConn->select($sqlII);
		
		$sqlcheck = "select  Count(*) as cnt  from `paymentdetails` where `PaidTo` = '".$_REQUEST['LedgerID']."' ";
		$data = $this->m_dbConn->select($sqlcheck);
		
//		$sqlcheckBalance  = "select  Count(*) as cnt  from `assetregister` where `LedgerID` = '".$_REQUEST['LedgerID']."' and  `Is_Opening_Balance` = 1 and `Debit` <> 0";
		$sqlcheckBalance  = "select  Count(*) as cnt  from `assetregister` where `LedgerID` = '".$_REQUEST['LedgerID']."' and `Debit` <> 0";
		$dataBalance = $this->m_dbConn->select($sqlcheckBalance);
		
		if(($data[0]['cnt'] > 0 || $dataBalance[0]['cnt'] > 0)&& $resII[0]['fd_close'] == 1)
		{
				$Status = 'In Active';	
				$SqlCheckDetails = "SELECT `BankID`,`ChequeNumber` FROM `chequeentrydetails` where `PaidBy`='".$_REQUEST['LedgerID']."' and `ChequeDate`= '".getDBFormatDate($resII[0]['maturity_date'])."' ";
				$dataII = $this->m_dbConn->select($SqlCheckDetails);
		}
		else if(($data[0]['cnt'] > 0 || $dataBalance[0]['cnt'] > 0) && $resII[0]['fd_renew']  == 1)
		{
				$Status = 'Renewed';	
		}
		else if(($data[0]['cnt'] > 0 || $dataBalance[0]['cnt'] > 0) && $resII[0]['fd_close'] == 0  && $resII[0]['fd_renew']  == 0 )
		{
				$Status = 'Active';	
		}
		else if($data[0]['cnt'] == 0 && $dataBalance[0]['cnt'] >0 && $resII[0]['fd_close'] == 0  && $resII[0]['fd_renew'] == 0)
		{
				$Status = 'Active';	
		}
		else if($data[0]['cnt'] == 0 && $dataBalance[0]['cnt'] == 0 && $resII[0]['fd_close'] == 0  && $resII[0]['fd_renew'] == 0)
		{
				$Status = 'Pending';	
		}
		
		if($resI <> "")
		{
			$FDArray[0]['LedgerID'] = $resI[0]['id'];
			$FDArray[0]['LedgerName'] = $resI[0]['ledger_name'];
		}

		if($resII <> "")
		{
			$FDArray[0]['PrincipalAmount'] = $resII[0]['principal_amt'];
			$FDArray[0]['DepositDate'] = $resII[0]['deposit_date'];
			$FDArray[0]['FDRNO'] = $resII[0]['fdr_no'];
			$FDArray[0]['MaturityDate'] = $resII[0]['maturity_date'];
			$FDArray[0]['InterestRate'] = $resII[0]['int_rate'];
			$FDArray[0]['FDPeriod'] = $resII[0]['fd_period'];
			$FDArray[0]['MaturityAmount'] = $resII[0]['maturity_amt'];
			$FDArray[0]['AccruedInterestLegder'] = $resII[0]['accrued_interest_legder'];
			$FDArray[0]['InterestAccrued'] = $resII[0]['interest_accrued'];
			$FDArray[0]['InterestLegder'] = $resII[0]['interest_legder'];
			$FDArray[0]['Interest'] = $resII[0]['interest'];
			$FDArray[0]['Note'] = $resII[0]['note'];
			$FDArray[0]['FDClose'] = $resII[0]['fd_close'];
			$FDArray[0]['FDRenew'] = $resII[0]['fd_renew'];
			$FDArray[0]['BankID'] = $resII[0]['BankID'];
			$FDArray[0]['Status'] = $Status;
			$FDArray[0]['CategoryID'] = $resI[0]['categoryid'];
			$FDArray[0]['Ref'] = $resII[0]['id'];
		}
		else
		{
			$FDArray[0]['PrincipalAmount'] = '';
			$FDArray[0]['DepositDate'] = '';
			$FDArray[0]['FDRNO'] = '';
			$FDArray[0]['MaturityDate'] = '';
			$FDArray[0]['InterestRate'] = '';
			$FDArray[0]['FDPeriod'] ='';
			$FDArray[0]['MaturityAmount'] = '';
			$FDArray[0]['AccruedInterestLegder'] = 0;
			$FDArray[0]['InterestAccrued'] = '';
			$FDArray[0]['InterestLegder'] = 0;
			$FDArray[0]['Interest'] = '';
			$FDArray[0]['Note'] = '';
			$FDArray[0]['FDClose'] = 0;
			$FDArray[0]['FDRenew'] = 0;	
			$FDArray[0]['BankID'] = 0;	
			$FDArray[0]['Status'] = $Status;
			$FDArray[0]['CategoryID'] = $resI[0]['categoryid'];
			$FDArray[0]['Ref'] = 0;
		}
		
		
		if(sizeof($dataII) > 0 )
		{
				$FDArray[0]['PaidToBankID'] = $dataII[0]['BankID'];
				$FDArray[0]['ChequeNumber'] = $dataII[0]['ChequeNumber'];
		}
		return $FDArray;
	}
	
	public function deleting()
	{
		$sql = "update fd_master set status='N' where id ='".$_REQUEST['FdDetailsId']."'";
		$res = $this->m_dbConn->update($sql);
		//Pending:Add code to remove related JVs
	}
	

	public function getAccuedInterestFromFD_JVTable($fd_id, $accrued_interest_legder)
	{
		$Accured_amt = 0;
		try
		{
			//echo "<BR>Inside new code to find accured int";
			$SrNo = 1;
			$sql02 = "select * from fd_master where id = '".$fd_id."'";
			$sql22 = $this->m_dbConn->select($sql02);
			
			$sql03 = "SELECT SUM(`Debit`) as Total_Acc_Int FROM `voucher` where `RefTableID` =  ". TABLE_FD_MASTER ." and `RefNo` = '".$fd_id."' and `By` = '".$accrued_interest_legder."'";
			$sql33 = $this->m_dbConn->select($sql03);
			if($this->m_ShowDebugTrace == 1)
			{
				//echo "<br> sql03 : " . $sql03 ; 
				//var_dump($sql33);
			}
			$Accured_amt = $sql33[0]['Total_Acc_Int'];
//			return $Accured_amt;
		}
		catch(Exception $e)
		{
			//$this->m_dbConn->rollback();
			echo "<BR>Exception:".$e->getMessage();
			//return 0;
		}
		return $Accured_amt;
		
	}

	public function getTotalInterestFromFD_JVTable($fd_id, $interest_legder)
	{
		$Int_amt = 0;
		try
		{
			$SrNo = 1;
			
			
			$sql02 = "select * from fd_master where id = '".$fd_id."'";
			$sql22 = $this->m_dbConn->select($sql02);

			$sql02 = "select SUM(Credit) as Total_Int FROM `voucher` where `RefTableID` =  ". TABLE_FD_MASTER ."  and `RefNo` = '".$fd_id."' and `To` = '". $interest_legder . "'";;

			$sql12 = $this->m_dbConn->select($sql02);
			$Int_amt = $sql12[0]['Total_Int'];
			if($this->m_ShowDebugTrace == 1)
			{
				echo "<br> sql02 : " . $sql02 ; 
				var_dump($sql12);
			}
			//echo "Total amount " . $total_amt = $principal_amt + $sql11[0]['Total_Acc_Int'];
			//Get JVs from table and iterate and fetch accured interest
			return $Int_amt;
		}
		catch(Exception $e)
		{
			echo "<BR>Exception:".$e->getMessage();
			return 0;
		}
		return $Int_amt;
		
	}

//This function is called when interest is paid or accured before the maturity or renewal of the FD. For example year end or quarter end payout.

	public function fdInterestUpdate($fd_id , $AccruedInterestLegder ,$AccruedInterestAmt ,$InterestLedger,$InterestAmt,$InterestNote, $bBankPayout, $InterestDate )

	{

		$LatestVoucherNo = 0;
		echo $sql = "select * from  `fd_master` where `id` = '". $fd_id ."'";
		$data = $this->m_dbConn->select($sql); 
		
		if($this->m_ShowDebugTrace == 1)
		{
			var_dump($data);
				echo "<BR>BankID = " . $data['BankID'];
				echo "<BR>BankID [0]= " . $data[0]['BankID'];
		}
		
		if($data[0]['id'] <> 0 && $InterestAmt <> 0)
		{
			try
			{
				$this->m_dbConn->begin_transaction();	
				$SrNo = 1;
				
				$LedgerID = $data[0]['LedgerID'];
				$BankID = $data[0]['BankID'];
				$MaturityDate = $data[0]['maturity_date']; 
				$PrincipalAmount = $data[0]['principal_amt'];
				$MaturityAmount = $data[0]['maturity_amt'];
				
				$LatestVoucherNo = $this->m_latestcount->getLatestVoucherNo($_SESSION['society_id']);
	
				//Pending : Add validation date has to be in this financial year
				if($this->ShowDebugTrace == 1)
				{			
					echo "<BR>LedgerID : " . $LedgerID;
					echo "<BR>BankID : " . $BankID;
					echo "<BR>AccruedInterestLegder :" . $AccruedInterestLegder;
					echo "<BR>AccruedInterestAmt : " . $AccruedInterestAmt;
					echo "<BR>InterestLedger :" . $InterestLedger;
					echo "<BR>InterestAmt : " . $InterestAmt;
					echo "<BR>InterestDate :" . $InterestDate;
					echo "<BR>Note : " . $Note;
					echo "<BR>InterestNote: " . $InterestNote;				
					echo "<BR>bBankPayout : " . $bBankPayout;
					echo "<BR>InterestDate :" . $InterestDate;
				}
				echo "<BR>Test1";
				$Voucher_Type = VOUCHER_JOURNAL;
	 echo $reconcileDate = getDBFormatDate('00-00-0000');
				if($bBankPayout == 1)
				{
					$Voucher_Type = VOUCHER_RECEIPT;
					echo "<BR>Test2";
					//new fd JV i.e Bank A/C   Dr    
					$dataVoucher1 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($InterestDate),$data[0]['id'] ,TABLE_FD_MASTER,
					$LatestVoucherNo,$SrNo,$Voucher_Type,$BankID,TRANSACTION_DEBIT,$InterestAmt,$InterestNote);
	 $depositGroup = -1;
	 $chequeDetailID = 0;
	 //Pending this function makes date '1970-01-01'
	 
					$this->m_register->SetBankRegister(getDBFormatDate($InterestDate),$BankID, $dataVoucher1, $Voucher_Type,
							TRANSACTION_RECEIVED_AMOUNT, $InterestAmt, $depositGroup, $chequeDetailID, 0, getDBFormatDate($InterestDate), 0, $reconcileDate, '0', '0', '0');
					echo "<BR>Updating Bank: " . $BankID . "   Voucher No" . $dataVoucher1 . "   SrNo " . $SrNo . " Ledger No" . $BankID . "   Amount : " . $InterestAmt;
					
					$SrNo++;
					
	/*				//Old fd JV To Fixed Deposit A/C Cr
					//$MaturityAmt  = $res['Principal_Amount'] - ($res['accrued_interest_amt'] - $res['interest_amt']);
					$dataVoucher2 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($InterestDate),$data[0]['id'],TABLE_FD_MASTER,
					$LatestVoucherNo,$SrNo,VOUCHER_JOURNAL,$LedgerID,TRANSACTION_CREDIT,$InterestAmt,$InterestNote);
					
					$regResult2 = $this->m_register->SetAssetRegister(getDBFormatDate($InterestDate), $LedgerID, $dataVoucher2,VOUCHER_JOURNAL, TRANSACTION_CREDIT, $InterestAmt, 0);	
					
					$SrNo++;
	*/				
				}
				else
				{
					echo "<BR>In else part Asset";
					//check InterestAmt and AccruedInterestAmt must be same.
					$AccruedInterestAmt =  $InterestAmt ; 
					//To Accrued Interest on FD JV debit (BY)
					$dataVoucher3 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($InterestDate),$data[0]['id'],TABLE_FD_MASTER,
					$LatestVoucherNo,$SrNo,$Voucher_Type,$AccruedInterestLegder,TRANSACTION_DEBIT,$AccruedInterestAmt,$InterestNote);
			
					$regResult3 = $this->m_register->SetRegister(getDBFormatDate($InterestDate), $AccruedInterestLegder, $dataVoucher3,$Voucher_Type, TRANSACTION_DEBIT, $AccruedInterestAmt, 0);	
	
					echo "<BR>Updated By: " . $AccruedInterestLegder . "   Voucher No" . $dataVoucher3 . "   SrNo " . $SrNo . " Amount : " . $AccruedInterestAmt;
					
					$SrNo++;
				}
				echo "<BR>out of if-else part ";
				//Interest ledger could be liability or Income type
				//To Interest on FD JV credit
				$dataVoucher4 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($InterestDate),$data[0]['id'],TABLE_FD_MASTER,
				$LatestVoucherNo,$SrNo,$Voucher_Type,$InterestLedger,TRANSACTION_CREDIT,$InterestAmt,$InterestNote);
	
			
				$regResult4 = $this->m_register->SetRegister(getDBFormatDate($InterestDate), $InterestLedger, $dataVoucher4,$Voucher_Type, TRANSACTION_CREDIT, $InterestAmt, 0);
					echo "<BR>Updated To: " . $InterestLedger . "   Voucher No" . $dataVoucher4 . "   SrNo " . $SrNo . " Amount : " . $InterestAmt;
				
				$LogMsg = 'FD interest updated LedgerID  <'.$LedgerID.' > (deposit_date | principal_amt | interest_date | interest_amt | InterestNote)';
								  
				$LogMsg  .="Record Details: (".$DepositDate ."|| ". $PrincipalAmount."|| ". $InterestDate."|| ".$InterestAmt."||".$InterestNote.")";
			
				$this->m_objLog->setLog($LogMsg, $_SESSION['login_id'], TABLE_FD_MASTER,$data[0]['id']);
				$this->m_dbConn->commit();
				return $LatestVoucherNo;
			}
			catch(Exception $e)
			{
				$this->m_dbConn->rollback();
				echo "<BR>Exception:".$e->getMessage();
				return 0;
			}
		}
		return $LatestVoucherNo;
		
	}
	
	public function show_Vouchers($fd_id)
	{
		/*$sql01 = "select * from `fd_master` where `LedgerID` = '".$LedgerID."'";
		$sql11 = $this->m_dbConn->select($sql01);
		$fd_no = $sql11[0]['id'];*/
		
		/*$sql02 = "select * from `fd_voucher` where `fd_id` = '".$fd_id."'";
		$sql22 = $this->m_dbConn->select($sql02);*/
		
		$sql02 = "SELECT Distinct(`VoucherNo`) as voucher_no FROM `voucher` where `RefTableID` = 6 and `RefNo` = '".$fd_id."'";
		$sql22 = $this->m_dbConn->select($sql02);
		
		$all_vouchers_array = array();
		
		for($i = 0; $i < sizeof($sql22); $i++)
		{
			//$sql03="SELECT v.id, v.Date,v.VoucherNo,v.RefNo,v.By,v.Debit,v.Credit,v.Note,v.VoucherTypeID,l.id as ledger_id, l.ledger_name,vt.desc,p.ID as 'PeriodID' FROM `voucher` as v join vouchertype as vt on v.VouchertypeID=vt.id join ledger as l on v.`By`=l.id join period as p on (v.Date BETWEEN p.BeginingDate and p.EndingDate) where v.VoucherNo = '".$sql22[$i]['voucher_no']."' group by v.`voucherNo`";
			//echo $sql03="SELECT v.id, v.Date,v.VoucherNo,v.RefNo,v.By,v.Debit,v.Credit,v.Note,v.VoucherTypeID,l.id as ledger_id, l.ledger_name,vt.desc FROM `voucher` as v join vouchertype as vt on v.VouchertypeID=vt.id join ledger as l on v.`By`=l.id where v.VoucherNo = '".$sql22[$i]['voucher_no']."' group by v.`voucherNo`";
			$sql03 = "SELECT v.id, v.Date,v.VoucherNo,v.RefNo,v.By,v.Debit,v.Credit,v.Note,v.VoucherTypeID,l.id as ledger_id, l.ledger_name,vt.desc,ac.group_id FROM `voucher` as v join vouchertype as vt on v.VouchertypeID=vt.id join ledger as l on v.`By`=l.id join account_category as ac on l.categoryid=ac.category_id where v.VoucherNo = '".$sql22[$i]['voucher_no']."' group by v.`voucherNo`";
			$sql33 = $this->m_dbConn->select($sql03);
			array_push($all_vouchers_array,$sql33);
		}
		
		if($this->m_ShowDebugTrace == 1)
		{
			//echo "<BR>Var dump of vouchers";
			//var_dump($all_vouchers_array );
		}
		return $all_vouchers_array;
	}

	//Pass openig balance if FD already existing when First year is created
	//Pending : Give option to have opening balance and accured interest while creating FD
	public function Create_New_Ledger($LedgerName, $Category, $OpeningBalanceDate, $OpeningBalance )
	{
		echo "<BR>Inside Create_New_Ledger " . $LedgerName . "   Category : " . $Category;
		$NewLedgerID = 0;
		if($LedgerName <> "")
		{
			//Pending : check if LedgerName exist, then throw exception. Cannot create duplicate ledgers
			
			//create new ledger for fd account
			$sqlNewLedger = "INSERT INTO `ledger`(`society_id`, `categoryid`, `ledger_name`, `payment`, `receipt`,`opening_type`,`opening_date`,`opening_balance`) 
										VALUES ('".$_SESSION['society_id']."', '" . $Category . "', '" . $LedgerName . "', 1, 1, 2,'".getDBFormatDate($OpeningBalanceDate)."','" .  $OpeningBalance . "' )";
			echo "<BR>sqlNewLedger : " . $sqlNewLedger ;
			$NewLedgerID = $this->m_dbConn->insert($sqlNewLedger);
			echo "<BR>NewLedgerID : " . $NewLedgerID ;

			//$insertAsset = $this->m_register->SetAssetRegister((getDBFormatDate($OpeningBalanceDate), $LedgerID, 0, 0, TRANSACTION_DEBIT, $OpeningBalance, 1);
			
		}
		else
		{
			//Pending : Error handling	
			echo "Create_New_Ledger:LedgerName cannot be blank";
		}
		return $NewLedgerID;
	}
	
	public function GetOrUpdateFD_Master($Old_FDR_ID, $LedgerID, $FDR_No, $Principal_Amount, $Deposit_Date, $Maturity_Amount, $Maturity_Date, $Interest_Rate, $FD_Interest_Frequency, $FD_Type,  $FD_Period, $FD_Bank_ID , $Note )
	{
		echo "<BR><BR>Inside GetOrUpdateFD_Master<BR>";
//		$sql = "select * from  `fd_master` where `id` = '".$FDR_ID . "'";
//		$data = $this->m_dbConn->select($sql);
//		var_dump($data); 
		
//		if($data[0]['id'] <> 0)
		{
//			return $data[0]['id'];ss
		}
		echo "<BR>Inserting new fd_master";
		$sqlFDmaster = "INSERT INTO `fd_master` (`LedgerID`,`fdr_no`, `deposit_date`, `maturity_date`, `int_rate`, `interest_frequency`
								, `principal_amt`, `maturity_amt`,`fd_period`, `fd_close`, `fd_renew`,`note`,`status`,`BankID`) 
								VALUES ('".$LedgerID."' ,'".$FDR_No ."' ,'".getDBFormatDate($Deposit_Date)."' 
								,'".getDBFormatDate($Maturity_Date)."','".$Interest_Rate."','".$FD_Interest_Frequency."','".$Principal_Amount."'
								,'".$Maturity_Amount."','".$FD_Period."','0'
								,'0','".$Note."' ,'Y','".$FD_Bank_ID."')";	
		
		echo "<BR>sqlFDmaster : " . $sqlFDmaster ;
		$resFDmaster  = $this->m_dbConn->insert($sqlFDmaster);
		echo "<BR>Created new FD_Master records : " . $resFDmaster ;
		return $resFDmaster ;
	}
	
	public function Create_New_FD_Close_Renew($Deposit_Date, $Maturity_Date, $LedgerID, $Principal_Amount, $Maturity_Amount, $Renew, $ref_id )
	{
		if($this->m_ShowDebugTrace==1)
		{
			echo "<BR>Inside Create_New_FD_Close_Renew";
			echo "<BR>Deposit_Date : " . $Deposit_Date;
			echo "<BR>Maturity_Date : " .  $Maturity_Date;
			echo "<BR>LedgerID : " . $LedgerID;
			echo "<BR>Principal_Amount : " . $Principal_Amount;
			echo "<BR>Maturity_Amount :" . $Maturity_Amount;
			echo "<BR>Renew : " . $Renew;
			echo "<BR>ref_id : " . $ref_id ;
		}
		
		$Action_Type = FD_CREATED;
		if($Renew)
		{
			$Action_Type = FD_RENEW;
		}
				
		$sqlInsert = "Insert into `fd_close_renew`(`StartDate`,`EndDate`,`LedgerID`,`DepositAmount`,`MaturityAmount`,`ActionType`,`RefNo`) 
					values('".getDBFormatDate($Deposit_Date)."','".getDBFormatDate($Maturity_Date)."',
					'".$LedgerID."','".$Principal_Amount."','".$Maturity_Amount."','". $Action_Type."','".$ref_id."')";
				
		echo "<BR>sqlInsert : " . $sqlInsert ;
		echo $resInsert = $this->m_dbConn->insert($sqlInsert);
		return $resInsert;		
	}
	
	public function fdRenewProcess2($res)
	{
		var_dump($res);

		try
		{
			
			$sql = "select * from  `fd_master` where `id` = '".$res['ref']."'";
			$data = $this->m_dbConn->select($sql);
			if($this->ShowDebugTrace == 1)
			{			
				var_dump($data); 
			}
			//Pending : validate if record exist
			
					
			$bBankPayout = 1;	
			$bBankPayout  = $_POST['FD_Bank_Payout'] ;	
			$InterestDate = $_POST['Interest_Date'] ;	
			$InterestNote = $_POST['Interest_Note'] ;	
			$FD_ID = $res['ref'];
			$BankID = $data[0]['BankID'];
			$LedgerID= $data[0]['LedgerID'];

	  
			//$FDR_No = $_POST['FDR_No'];
			$FDR_No = $data[0]['fdr_no'];
			$Deposit_Date = $_POST['Deposit_Date'];
			$Maturity_Date = $_POST['Maturity_Date'];
			$Interest_Rate = $_POST['Interest_Rate'];
			//$Principal_Amount = $_POST['Principal_Amount'];
			//$Maturity_Amount = $_POST['Maturity_Amount'];
			$Principal_Amount = $data[0]['principal_amt'];
			$Maturity_Amount = $data[0]['maturity_amt'];
			
			$FD_Period = $_POST['FD_Period'];
			$FD_Bank_Name = $_POST['FD_Bank_Name'];

			$AccruedInterestLedger = $res['accrued_interest_legder'];
			$AccruedInterestAmt  = $res['accrued_interest_amt'];
			$InterestLedger = $res['interest_legder'];
			$InterestAmt =  $res['interest_amt'];	

			if($this->ShowDebugTrace == 1)
			{			
				echo "<BR>LedgerID : " . $LedgerID;
				echo "<BR>BankID : " . $BankID;
				echo "<BR>AccruedInterestLedger :" . $AccruedInterestLedger;
				echo "<BR>AccruedInterestAmt : " . $AccruedInterestAmt;
				echo "<BR>InterestLedger :" . $InterestLedger;
				echo "<BR>InterestAmt : " . $InterestAmt;
				echo "<BR>InterestDate :" . $InterestDate;
				//echo "<BR>Note : " . $Note;
				echo "<BR>InterestNote: " . $InterestNote;				
				echo "<BR>bBankPayout : " . $bBankPayout;
				echo "<BR>FD_Bank_Name :" . $FD_Bank_Name;
//				echo "<BR>InterestDate :" . $InterestDate;
//				echo "<BR>InterestDate :" . $InterestDate;
			}
		
			
			$this->m_dbConn->begin_transaction();	
			$SrNo = 1;
			$LatestVoucherNo = $this->m_latestcount->getLatestVoucherNo($_SESSION['society_id']);

				if($bBankPayout == 1)
				{
					//Interest paid in Bank and FD would be renewed with Principal amount.
					$Voucher_Type = VOUCHER_RECEIPT;
					echo "<BR>Bank Payout processing";
					//new fd JV i.e Bank A/C   Dr    
					$dataVoucher1 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($InterestDate),$data[0]['id'] ,TABLE_FD_MASTER,
					$LatestVoucherNo,$SrNo,$Voucher_Type,$BankID,TRANSACTION_DEBIT,$InterestAmt,"Bank Payout for " . $InterestNote);

					echo "<BR>Updating Bank: " . $BankID . "   Voucher No" . $dataVoucher1 . "   SrNo " . $SrNo . " Ledger No" . $BankID . "   Amount : " . $InterestAmt;
					 $depositGroup = -1;
					 $chequeDetailID = 0;
	 
					$this->m_register->SetBankRegister(getDBFormatDate($InterestDate),$BankID, $dataVoucher1, $Voucher_Type,
							TRANSACTION_RECEIVED_AMOUNT, $InterestAmt, $depositGroup, $chequeDetailID, 0, getDBFormatDate($InterestDate), 0, '00-00-0000', '0', '0', '0');
						//Pending : with ' 0000-00-00' reconcile date is going as 1970-01-01
					
					$SrNo++;
					//Interest ledger could be liability or Income type
					//To Interest on FD JV credit
					$dataVoucher4 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($InterestDate),$data[0]['id'],TABLE_FD_MASTER,
					$LatestVoucherNo,$SrNo,$Voucher_Type,$InterestLedger,TRANSACTION_CREDIT,$InterestAmt,"Interest bank payout");
		
					echo "<BR>Updated Interest payout To: " . $InterestLedger . "   Voucher No" . $dataVoucher4 . "   SrNo " . $SrNo . " Amount : " . $InterestAmt;
				
					$regResult4 = $this->m_register->SetRegister(getDBFormatDate($InterestDate), $InterestLedger, $dataVoucher4,$Voucher_Type, TRANSACTION_CREDIT, $InterestAmt, 0);
					$InterestAmt = 0;
/*					$LogMsg = 'FD interest payout LedgerID  <'.$LedgerID.' > (deposit_date | principal_amt | interest_date | interest_amt | InterestNote)'; 
					$LogMsg  .="Record Details: (".$DepositDate ."|| ". $PrincipalAmount."|| ". $InterestDate."|| ".$InterestAmt."||".$InterestNote.")";						
					$this->m_objLog->setLog($LogMsg, $_SESSION['login_id'], TABLE_FD_MASTER,$data[0]['id']);
*/

			}
			
			//If no bank payout then add interest amount to FD principal and create FD of added amount

				$New_FDR_No = $_POST['FDR_No_RN'];
				$New_Deposit_Date = $_POST['DoD_RN'];
				//pending : validate New_Deposit_Date to be in current financial year  and is equal to maturity date of old FD
				$New_Maturity_Date = $_POST['DoM_RN'];
				$New_Principal_Amount = $res['principal_amt_RN']; 
				$New_ROI = $_POST['ROI_RN'];
				$New_Maturity_Amount = $_POST['maturity_amt_RN'];

				if($this->m_ShowDebugTrace == 1)
				{ 
					echo "<BR>Old_FDR_No:" . $FDR_No;
					echo "<BR>New_FDR_No :" . $New_FDR_No ; //LedgerName
					echo "<BR>New_Deposit_Date:" . $New_Deposit_Date;
					echo "<BR>New_Maturity_Date:" . $New_Maturity_Date;
					echo "<BR>New_Principal_Amount:" . $New_Principal_Amount;
					echo "<BR>New_ROI:" . $New_ROI;
					echo "<BR>New_Maturity_Amount:" . $New_Maturity_Amount;
				}

				$New_Principal_Amount  = $New_Principal_Amount + 10;
				$New_Principal_Amount  = $New_Principal_Amount - 10;
				$FD_Calc_NewPrincipal = $Principal_Amount  + $AccruedInterestAmt + $InterestAmt;
					
				if ($New_Principal_Amount  == $FD_Calc_NewPrincipal)
				{				
					echo "<BR>Renewing New Principal amount " . $New_Principal_Amount . " and Calculated Principal amount " . $FD_Calc_NewPrincipal ; //$FD_Calc_NewPrincipal = 
					echo "<BR>New_FDR_No :" . $New_FDR_No ; //LedgerName

					if ($FDR_No <> $New_FDR_No )
					{
						$bCreateNewLedger = 1;
					}
					
					//$bCreateNewLedger = $_POST['FD_CreateNewLedger'];
					echo "<BR>bCreateNewLedger :" . $bCreateNewLedger ; //LedgerName
					if($bCreateNewLedger == 1)
					{
						echo "<BR>creating new ledger.. get parent of .." . $LedgerID;
						
						$aParent = $this->m_register->getLedgerParent($LedgerID);
						echo "<BR>Not going beyond this point";
						$groupID = $aParent['group'];
						$Category = $aParent['category'];
						if($this->ShowDebugTrace == 1)
						{
							var_dump($aryParent );
							echo "<BR>GroupID :". $groupID . " Category :" . $categoryID . " LedgerID : " . $LedgerID . "<BR>";
						}
						
//						$New_FDR_No = $_POST['FDR_No_RN'];// => string 'NewFD 999' (length=9)
						$OpeningBalance = 0;//$_POST['principal_amt_RN']; This is for FD exist when society cretaed, but otherwise opening bal would be 0
	
						$NewLedgerID = $this->Create_New_Ledger($New_FDR_No, $Category, $OpeningBalanceDate, $OpeningBalance );
						//echo "<BR>Renewing New Principal amount " . $New_Principal_Amount . " and Calculated Principal amount " . $FD_Calc_NewPrincipal ; //$FD_Calc_NewPrincipal = 
						echo "<BR>Created new Ledger" . $NewLedgerID  . " and old LedgerID was " . $LedgerID;
						//Insert into Ledger	

					}
					else
					{
						echo "<BR>LedgerID is same " . $LedgerID;
						$NewLedgerID = $LedgerID;
					
					}


				//Pending : Add this to dbconst.class			
				
				//Interest frequency
				$FD_Interest_Frequency = FD_QUARTERLY;
				
				//FD Interest Type
				$FD_Type = FD_CUMULATIVE; //FD_Non_CUMULATIVE
				//$FD_Interest_Payment = FD_ACCURED
			
				$FD_Master_ID = $this->GetOrUpdateFD_Master($FDR_No, $NewLedgerID, $New_FDR_No, $New_Principal_Amount, $New_Deposit_Date, $New_Maturity_Amount, $New_Maturity_Date, $Interest_Rate, $FD_Interest_Frequency, $FD_Type,  $FD_Period, $BankID , $InterestNote  );

				echo "<BR>New FD Master " . $FD_Master_ID;

				//Create new fd_master
				if($bBankPayout == 1)
				{
					$SrNo = 1;
					$LatestVoucherNo = $this->m_latestcount->getLatestVoucherNo($_SESSION['society_id']);	
				}
				else
				{
					$SrNo++;					
				}
				
				echo "<BR>SetVoucherDetails for NewLedgerID" . $NewLedgerID . " New Principal amt : " . $New_Principal_Amount . " Note: " . $InterestNote;
				$dataVoucher1 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($New_Deposit_Date),$FD_Master_ID ,TABLE_FD_MASTER,
				$LatestVoucherNo,$SrNo,VOUCHER_JOURNAL,$NewLedgerID,TRANSACTION_DEBIT,$New_Principal_Amount,$InterestNote );
				echo "<BR>SetAssetRegister";
				$regResult1 = $this->m_register->SetAssetRegister(getDBFormatDate($New_Deposit_Date), $NewLedgerID, $dataVoucher1, VOUCHER_JOURNAL, TRANSACTION_DEBIT, $New_Principal_Amount, 0);
				
				$SrNo++;
				echo "<BR>Test3";
				//Old fd JV credit 
				//$MaturityAmt  = $res['Principal_Amount'] - ($res['accrued_interest_amt'] - $res['interest_amt']);
				$dataVoucher2 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($New_Deposit_Date), $FD_ID, TABLE_FD_MASTER,
											$LatestVoucherNo,$SrNo,VOUCHER_JOURNAL,$LedgerID,TRANSACTION_CREDIT,$Principal_Amount,$InterestNote);
				echo "<BR>Test4";
				$regResult2 = $this->m_register->SetAssetRegister(getDBFormatDate($New_Deposit_Date), $LedgerID, $dataVoucher2, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $Principal_Amount, 0);	
				
				echo "<BR>AccruedInterestAmt : " . $AccruedInterestAmt;
				if($AccruedInterestAmt <> 0)
				{
				$SrNo++;
				echo "<BR>Test5";
				//Accrued Interest on FD JV credit
				$dataVoucher3 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($New_Deposit_Date),$FD_ID,TABLE_FD_MASTER,
						$LatestVoucherNo,$SrNo,VOUCHER_JOURNAL,$AccruedInterestLedger,TRANSACTION_CREDIT,$AccruedInterestAmt,$InterestNote);
				echo "<BR>Test6";
				$regResult3 = $this->m_register->SetAssetRegister(getDBFormatDate($New_Deposit_Date), $AccruedInterestLedger, $dataVoucher3,VOUCHER_JOURNAL, TRANSACTION_CREDIT, $AccruedInterestAmt, 0);	
				}
				
				echo "<BR>InterestAmt" . $InterestAmt;
				if($InterestAmt <> 0)
				{
					$SrNo++;
					echo "<BR>Test7";
					//Interest on FD JV credit
					$dataVoucher4 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($New_Deposit_Date),$FD_ID,TABLE_FD_MASTER, $LatestVoucherNo,$SrNo,VOUCHER_JOURNAL,$InterestLedger,TRANSACTION_CREDIT,$InterestAmt,$InterestNote);
					echo "<BR>Test8";
					$regResult4 = $this->m_register->SetRegister(getDBFormatDate($New_Deposit_Date), $InterestLedger, $dataVoucher4,VOUCHER_JOURNAL, TRANSACTION_CREDIT, $InterestAmt, 0);
				}

/*
				$sqlInsert = "Insert into `fd_close_renew`(`StartDate`,`EndDate`,`LedgerID`,`DepositAmount`,`MaturityAmount`,`ActionType`,`RefNo`)  values('".$data[0]['deposit_date']."','".$data[0]['maturity_date']."','".$data[0]['LedgerID']."','".$data[0]['principal_amt']."','".$data[0]['maturity_amt']."','".FD_RENEW."','" . $FD_ID . "')";

				echo "<BR>sqlInsert : " . $sqlInsert ;
				$resInsert = $this->m_dbConn->insert($sqlInsert);
				echo "<BR>resInsert : " . $resInsert ;
*/		

				$Renew = 1;
				$renew_id= $this->Create_New_FD_Close_Renew($data[0]['deposit_date'], $data[0]['maturity_date'], $LedgerID, $data[0]['principal_amt'],  $data[0]['maturity_amt'], $Renew, $FD_ID );
				echo "<BR>Old renew_id " . $renew_id;
				$Renew = 0;
				$renew_id= $this->Create_New_FD_Close_Renew($New_Deposit_Date, $New_Maturity_Date, $NewLedgerID, $New_Principal_Amount,  $New_Maturity_Amount, $Renew, $FD_Master_Id );
		
				echo "<BR>new renew_id " . $renew_id;
		
				$this->sendFDMaturityReminderEmail($_SESSION['society_id'],$NewLedgerID,$New_Maturity_Date);
		
				$LogMsg  .= 'New FD Record Inserted frm fd Master(LedgerID | name | fdr_no | deposit_date | maturity_date | int_rate 
									| principal_amt | maturity_amt | period | fd_close | fd_renew | note |status)';
				$New_FD_Period= "";
				$LogMsg .= '('.$LedgerID.'|'. $New_FDR_No . '|'.$New_FDR_No.'|'.getDBFormatDate($New_Deposit_Date)
									.'|'.getDBFormatDate($New_Maturity_Date).'|'.$New_Principal_Amount
									.'|'.$New_Maturity_Amount.'|'.$New_FD_Period.'|'.$_POST['FD_Close']
									.'|'.$_POST['FD_Renew'].'|'.$InterestNote.',Y)';
				echo "<BR>LogMsg : " . $LogMsg ;
				$changeLogID =  $FD_Master_ID;
		//		$transactionStatus =  "Insert"; 
				
		
				$updateFDMaster2 = "UPDATE `fd_master` SET  `fd_renew` = '1'  WHERE `id` = '".$FD_ID."'  ";
				echo "<BR>updateFDMaster2 : " . $updateFDMaster2 ;
				$dataFDMaster2 = $this->m_dbConn->update($updateFDMaster2);
				echo "<BR>dataFDMaster2 : " . $dataFDMaster2 ;

	
				$LogMsg = 'FD Renewed  NewFDID  <'.$FD_Master_ID.' > New_FDR_No  <'.$New_FDR_No.' > LedgerID  <'.$NewLedgerID.' > (deposit_date | maturity_date | int_rate | principal_amt | maturity_amt |  note |status)';
				$LogMsg .= '('.getDBFormatDate($New_Deposit_Date)
									.'|'.getDBFormatDate($New_Maturity_Date).'|'.$New_Principal_Amount
									.'|'.$New_Maturity_Amount.'|'.$InterestNote.',Y)';
								  
				$LogMsg  .="Previous Record Details: (".$data[0]['deposit_date'] ."|| ". $data[0]['maturity_date'] ."|| ". $data[0]['principal_amt'] ."|| ".$data[0]['maturity_amt'] ."||".$data[0]['Note'].")";
				
				echo "<BR>" . $LogMsg ;
				$this->m_objLog->setLog($LogMsg, $_SESSION['login_id'], TABLE_FD_MASTER,$FD_Master_ID);
				//alert("FD Renewed successfully");
				$this->m_dbConn->commit();
							  
			}
			else
			{
				$Msg = "<BR>Principal amount " . $New_Principal_Amount . " and Calculated Principal amount " . $FD_Calc_NewPrincipal . " are not same"; //$FD_Calc_NewPrincipal = $New_Principal_Amount  + $AccruedInterestAmt + $InterestAmt;
				echo $Msg;
				//alert("Failed " . $Msg);
				$this->m_dbConn->rollback();
				return 'Failed';		
			}
		
			return 'Success';
		}
		catch(Exception $e)
		{
			$this->m_dbConn->rollback();
			echo "Exception:".$e->getMessage()."Line No:".$e->getLine();
			return 'Failed';
		}
	}
	
	
	public function fdCloseProcess($fd_id, $BankID ,$MaturityDate , $PrincipalAmount ,$MaturityAmount ,$AccruedInterestLegder ,$AccruedInterestAmt ,$InterestLedger,$InterestAmt,$Note, $ClosingDate)
	{
		echo "<BR>Inside fdCloseProcess<BR>";
		try
		{	
			//echo "<br>Matu date ".$MaturityDate."<br>p_amt ".$PrincipalAmount."<br>matu amt: ".$MaturityAmount."<br>aacr. int. ledger: ".$AccruedInterestLegder."<br>acc. int.: ".$AccruedInterestAmt."<br>int. led: ".$InterestLedger."<br>int amt: ".$InterestAmt."<br>Note: ".$Note;
			//Pending : validate MaturityAmt = PrincipalAmount + $AccruedInterestAmt + $InterestAmt
			//Validate $MaturityDate = Principal+ Accur + Interest
			if($this->m_ShowDebugTrace == 1)
			{
				echo "<BR>PrincipalAmount : " . $PrincipalAmount;
				echo "<BR>AccruedInterestAmt : " . $AccruedInterestAmt;
				echo "<BR>InterestAmt : " . $InterestAmt;
				echo "<BR>CalcMaturityAmt  " . $CalcMaturityAmt =  $PrincipalAmount + $AccruedInterestAmt+ $InterestAmt;
				echo "<BR>Maturity amount : " . $MaturityAmount;				
				echo "<BR>MaturityDate : " . $MaturityDate;				
				echo "<BR>ClosingDate : " . $ClosingDate;				
			}
			/* Pending : Add validation for maturity date not equal to closing date
			if($MaturityDate <> $ClosingDate)
			{
				$Diff = $MaturityDate - $ClosingDate;
				echo "<BR><BR>Error: Cannot close FD. Maturity date ". $MaturityDate > " and closing date "  . $ClosingDate . "mismatched by " . $Diff . "<BR>"	;
				return "Failed";		
			}
*/
//Pending: Need to get Principal amount when FD is opened and then amount xfered from bank
//Pending : External counter
/*

			if($MaturityAmount <> $CalcMaturityAmt)
			{
//Pending: For this condition, add in log that its pre mature withdrawal
				$Diff = $MaturityAmount - $CalcMaturityAmt;
				echo "<BR><BR>Error: Cannot close FD. Maturity amount mismatched by " . $Diff . "<BR>"	;
				//Alert ("Error: Cannot close FD. Maturity amount mismatched by " . $Diff );
				$this->m_dbConn->rollback();
				return "Failed";		
			}
*/
			echo $sql = "select * from `fd_master` where `id` = '".$fd_id."'"; //where fd_close = 0 and fd_renew = 0";
			$data = $this->m_dbConn->select($sql);
			var_dump($data);
			//if($data[0]['id'] <> 0 && $InterestAmt <> 0)
			if($data[0]['id'] <> 0)
			{

				$this->m_dbConn->begin_transaction();	

				$LedgerID = $data[0]['LedgerID'];
				$FDR_No = $data[0]['fdr_no'];
				$DepositDate = $data[0]['deposit_date'];
//				$MaturityDate = $data[0]['maturity_date'];
//				$PrincipalAmount = $data[0]['principal_amt'];
//				$MaturityAmount = $data[0]['maturity_amt'];

				$sqlInsert = "Insert into `fd_close_renew`(`StartDate`,`EndDate`,`LedgerID`,`DepositAmount`,`MaturityAmount`,`ActionType`,`RefNo`)
									values('".getDBFormatDate($DepositDate)."','".getDBFormatDate($ClosingDate)."',
									'".$LedgerID."','".$PrincipalAmount."','".$MaturityAmount."','".FD_CLOSED."','".$fd_id."')";
				if($this->m_ShowDebugTrace == 1)
				{
					echo "sqlInsert ". $sqlInsert ;
				}
				$resInsert = $this->m_dbConn->insert($sqlInsert);
				
				$SrNo = 1;
				$LatestVoucherNo = $this->m_latestcount->getLatestVoucherNo($_SESSION['society_id']);
				$Voucher_Type = VOUCHER_RECEIPT;
				
				//new fd JV i.e Bank A/C   Dr    sd
				$dataVoucher1 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($ClosingDate),$data[0]['id'] ,TABLE_FD_MASTER,
				$LatestVoucherNo,$SrNo,$Voucher_Type,$BankID,TRANSACTION_DEBIT,$CalcMaturityAmt,$Note);
				$depositGroup = -1;
				$chequeDetailID = 0;
				$this->m_register->SetBankRegister(getDBFormatDate($ClosingDate),$BankID, $dataVoucher1, $Voucher_Type, TRANSACTION_RECEIVED_AMOUNT, $CalcMaturityAmt, $depositGroup, $chequeDetailID, 0, getDBFormatDate($ClosingDate), 0, '00-00-0000', '0', '0', '0');
				
				$SrNo++;
				//Old fd JV To Fixed Deposit A/C Cr
				//$MaturityAmt  = $res['Principal_Amount'] - ($res['accrued_interest_amt'] - $res['interest_amt']);
				$dataVoucher2 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($ClosingDate),$data[0]['id'],TABLE_FD_MASTER,
				$LatestVoucherNo,$SrNo,$Voucher_Type,$LedgerID,TRANSACTION_CREDIT,$PrincipalAmount,"");
				$regResult2 = $this->m_register->SetAssetRegister(getDBFormatDate($ClosingDate), $LedgerID, $dataVoucher2,$Voucher_Type, TRANSACTION_CREDIT, $PrincipalAmount, 0);	
				$SrNo++;
				
				//To Accrued Interest on FD JV credit
				if($AccruedInterestAmt <> 0)
				{
					$dataVoucher3 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($ClosingDate),$data[0]['id'],TABLE_FD_MASTER,$LatestVoucherNo,$SrNo,$Voucher_Type,$AccruedInterestLegder,TRANSACTION_CREDIT,$AccruedInterestAmt,"");
					$regResult3 = $this->m_register->SetRegister(getDBFormatDate($ClosingDate), $AccruedInterestLegder, $dataVoucher3,$Voucher_Type, TRANSACTION_CREDIT, $AccruedInterestAmt, 0);	
					
					$SrNo++;
				}
				
				//To Interest on FD JV credit
				if($InterestAmt <> 0)
				{
					$dataVoucher4 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($ClosingDate),$data[0]['id'],TABLE_FD_MASTER,$LatestVoucherNo,$SrNo,$Voucher_Type,$InterestLedger,TRANSACTION_CREDIT,$InterestAmt,"");
					$regResult4 = $this->m_register->SetRegister(getDBFormatDate($ClosingDate), $InterestLedger, $dataVoucher4,$Voucher_Type, TRANSACTION_CREDIT, $InterestAmt, 0);
				}
				
				$updateFDMaster2 = "UPDATE `fd_master` SET  `fd_close` = '1'  WHERE `id` = '". $fd_id ."' ";
				$dataFDMaster2 = $this->m_dbConn->update($updateFDMaster2);
				
				$LogMsg = 'FD Closed  : FD id <'. $fd_id .' > LedgerID  <'.$LedgerID.' > (deposit_date | maturity_date | closing_date | principal_amt | maturity_amt | note)';
								  
				$LogMsg  .="Record Details: (".$DepositDate ."|| ". $MaturityDate."|| ".$ClosingDate."||". $PrincipalAmount."|| ".$CalcMaturityAmt."|| ".$Note.")";
				if($this->m_ShowDebugTrace == 1)
				{
					echo $LogMsg  ;
				}
				$this->m_objLog->setLog($LogMsg, $_SESSION['login_id'], TABLE_FD_MASTER,$data[0]['id']);
				$this->m_dbConn->commit();
				//alert("FD " . $FDR_No . " closed successfully");
				return 'Success';
			}
			else
			{
			
				echo "<BR><BR>Error: FD ". $fd_id . " not found in master.<BR>"	;
			}
			$this->m_dbConn->rollback();
			//Alert ("Error: Cannot close FD. ");
			return 'Failed';
		}
		catch(Exception $e)
		{
			$this->m_dbConn->rollback();
			echo "Exception:".$e->getMessage();
			//Alert ("Error: Cannot close FD. " . $e->getMessage() );
			return 'Failed';
		}		
		
	}

	public function fdRenewProcess($res)
	{
		try
		{
			$this->m_dbConn->begin_transaction();	
			$SrNo = 1;
			
			$sql = "select * from  `fd_master` where `id` = '".$res['ref']."'";
			$data = $this->m_dbConn->select($sql); 
			
			$sqlInsert = "Insert into `fd_close_renew`(`StartDate`,`EndDate`,`LedgerID`,`DepositAmount`,`MaturityAmount`,`ActionType`,`RefNo`) 
								values('".$data[0]['deposit_date']."','".$data[0]['maturity_date']."',
								'".$data[0]['LedgerID']."','".$data[0]['principal_amt']."','".$data[0]['maturity_amt']."','".FD_RENEW."','0')";
			$resInsert = $this->m_dbConn->insert($sqlInsert);
			
			$LatestVoucherNo = $this->m_latestcount->getLatestVoucherNo($_SESSION['society_id']);
			
			//new fd JV i.e debit new ledger
			$dataVoucher1 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($res['Deposit_Date']),$data[0]['id'] ,TABLE_FD_MASTER,
			$LatestVoucherNo,$SrNo,VOUCHER_JOURNAL,$res['NewLedgerID'],TRANSACTION_DEBIT,$res['Principal_Amount'],$res['Note']);
			
			$regResult1 = $this->m_register->SetAssetRegister(getDBFormatDate($res['Deposit_Date']), $res['NewLedgerID'], $dataVoucher1,VOUCHER_JOURNAL, TRANSACTION_DEBIT, $res['Principal_Amount'], 0);
			
			$SrNo++;
			
			//Old fd JV credit 
			//$MaturityAmt  = $res['Principal_Amount'] - ($res['accrued_interest_amt'] - $res['interest_amt']);
			$dataVoucher2 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($res['Deposit_Date']),$data[0]['id'],TABLE_FD_MASTER,
			$LatestVoucherNo,$SrNo,VOUCHER_JOURNAL,$data[0]['LedgerID'],TRANSACTION_CREDIT,$data[0]['principal_amt'],$res['Note']);
			
			$regResult2 = $this->m_register->SetAssetRegister(getDBFormatDate($res['Deposit_Date']), $data[0]['LedgerID'], $dataVoucher2,VOUCHER_JOURNAL, TRANSACTION_CREDIT, $data[0]['principal_amt'], 0);	
			
			$SrNo++;
			
			//Accrued Interest on FD JV credit
			$dataVoucher3 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($res['Deposit_Date']),$data[0]['id'],TABLE_FD_MASTER,
			$LatestVoucherNo,$SrNo,VOUCHER_JOURNAL,$res['accrued_interest_legder'],TRANSACTION_CREDIT,$res['accrued_interest_amt'],$res['Note']);
			
			$regResult3 = $this->m_register->SetAssetRegister(getDBFormatDate($res['Deposit_Date']), $res['accrued_interest_legder'], $dataVoucher3,VOUCHER_JOURNAL, TRANSACTION_CREDIT, $res['accrued_interest_amt'], 0);	
			
			$SrNo++;
			
			//Interest on FD JV credit
			$dataVoucher4 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($res['Deposit_Date']),$data[0]['id'],TABLE_FD_MASTER,
			$LatestVoucherNo,$SrNo,VOUCHER_JOURNAL,$res['interest_legder'],TRANSACTION_CREDIT,$res['interest_amt'],$res['Note']);
			
			$regResult4 = $this->m_register->SetRegister(getDBFormatDate($res['Deposit_Date']), $res['interest_legder'], $dataVoucher4,VOUCHER_JOURNAL, TRANSACTION_CREDIT, $res['interest_amt'], 0);
			
			$LogMsg = 'FD Renewed  LedgerID  <'.$res['ref'].' > (deposit_date | maturity_date | principal_amt | maturity_amt | note)';
							  
			$LogMsg  .="Previous Record Details: (".$data[0]['Deposit_Date'] ."|| ". $data[0]['maturity_date'] ."|| ". $data[0]['principal_amt'] ."|| ".$data[0]['maturity_amt'] ."||".$data['Note'].")";
							  
			$LogMsg  .= "New Record Details: (".$res['Deposit_Date'] ."|| ". $res['maturity_date']."|| ". $res['principal_amt'] ."|| ".$res['maturity_amt']."|| 0||".  $res['Note'].")";		  
		
			$this->m_objLog->setLog($LogMsg, $_SESSION['login_id'], TABLE_FD_MASTER,$data[0]['id']);
			$this->m_dbConn->commit();
			return 'Success';
		}
		catch(Exception $e)
		{
			$this->m_dbConn->rollback();
			echo "Exception:".$e->getMessage()."Line No:".$e->getLine();
			return 'Failed';
		}
	}
	
	public function GetYearDesc($YearID)
	{
		$SqlVal = $this->m_dbConn->select("SELECT `YearDescription` FROM `year` where `YearID`=". $YearID);
		return $SqlVal[0]['YearDescription'];
	}
	
	
	
	
	public function fetchRecords($ledgerIDArray,$status = 0)
	{
		$ledgername_array = array();
		
		$get_ledger_name = "select id,ledger_name from `ledger`";
		$result02 = $this->m_dbConn->select($get_ledger_name);
		
		for($i = 0; $i < sizeof($result02); $i++)
		{
			$ledgername_array[$result02[$i]['id']] = $result02[$i]['ledger_name'];
		}
			
		//$sql1 = "select led.id as ledger_id,concat_ws('-',led.ledger_name,led.id) as name, led.id as status,fd.fdr_no,DATE_FORMAT(led.opening_date  + INTERVAL 1 DAY, '%d-%m-%Y') as opening_date,DATE_FORMAT(fd.maturity_date, '%d-%m-%Y') as maturity_date,fd.int_rate,fd.principal_amt,fd.maturity_amt,fd.interest_accrued,fd.fd_deposit_period,fd.interest_frequency,fd.note,fd.fd_close,fd.fd_renew from ledger as led LEFT JOIN fd_master as fd ON led.id = fd.LedgerID  where led.categoryid IN (" . $fdAccountArray . ")and led.society_id=".$_SESSION['society_id']." and led.status = 'Y'";
		$sql1 = "select  led.id as ledger_id, led.ledger_name as Name,fd.BankID as Bank , led.id as Status,fd.fdr_no as `FD No`,DATE_FORMAT(fd.deposit_date, '%d-%m-%Y') as `Deposit Date`,fd.principal_amt `Principle Amount`,DATE_FORMAT(fd.maturity_date, '%d-%m-%Y') as `Maturity Date`,fd.maturity_amt as `Maturity Amount`,fd.int_rate as `Interest Rate`,fd.fd_period as `Period`,fd.interest_accrued as `Accrued Interest`,fd.interest as `Interest`,fd.note as `Note`,fd.fd_close,fd.fd_renew from ledger as led LEFT JOIN fd_master as fd ON led.id = fd.LedgerID  where led.id IN (" . $ledgerIDArray . ") and led.society_id=".$_SESSION['society_id']." and led.status = 'Y'";
		if($status == 3)
		{
			$sql1 .= " and fd.fd_renew =1 and fd.fd_close = 0";			
		}
		else if($status == 4)
		{
			$sql1 .= " and fd.fd_close =1 and  fd.fd_renew = 0";	
		}
		//echo $sql1;
		
		$result = $this->m_dbConn->select($sql1);
		//$result[''] = '';
//print_r($result);
		for($i = 0;$i < sizeof($result);$i++)
		{
				//$res = array();
				$sqlcheck = "select  Count(*) as cnt  from `paymentdetails` where `PaidTo` = '".$result[$i]['ledger_id']."' ";
				$data = $this->m_dbConn->select($sqlcheck);
				
//				$sqlcheckBalance  = "select  Count(*) as cnt  from `assetregister` where `LedgerID` = '".$result[$i]['ledger_id']."' and  `Is_Opening_Balance` = 1 and `Debit` <> 0";
				$sqlcheckBalance  = "select  Count(*) as cnt  from `assetregister` where `LedgerID` = '".$result[$i]['ledger_id']."' and `Debit` <> 0";
				$dataBalance = $this->m_dbConn->select($sqlcheckBalance);
				
				//$result[$i][' '] = ''; 
				
				if(($data[0]['cnt'] > 0 || $dataBalance[0]['cnt'] > 0)  && $result[$i]['fd_close'] == 1)
				{
						$result[$i]['Status'] = 'Closed';
				}
				else if(($data[0]['cnt'] > 0 || $dataBalance[0]['cnt'] > 0)  && $result[$i]['fd_renew'] == 1)
				{
						$result[$i]['Status'] = 'Renewed';	
				}
				else if(($data[0]['cnt'] > 0 || $dataBalance[0]['cnt'] > 0) && $result[$i]['fd_close'] == 0  && $result[$i]['fd_renew'] == 0 )
				{
						$result[$i]['Status'] = 'Active';
				}
				else if($data[0]['cnt'] == 0 && $dataBalance[0]['cnt'] == 0 && $result[$i]['fd_close'] == 0  && $result[$i]['fd_renew'] == 0)
				{
						$result[$i]['Status'] = 'Pending';	
				}
				else
				{
					$result[$i]['Status'] = "";
				}
				
				$result[$i]['Bank'] = $ledgername_array[$result[$i]['Bank']];
				unset($result[$i]['fd_close']);
				unset($result[$i]['fd_renew']);
				$this->array_splice_assoc ($result[$i] ,"ledger_id", 0, array(' ' =>''));
				unset($result[$i]['ledger_id']);
				
				if($status == 1 && $result[$i]['Status'] <>  'Pending')
				{
					array_push($this->unsetArray,$i);		
				}
				else if($status == 2 && $result[$i]['Status'] <> 'Active')
				{
					array_push($this->unsetArray,$i);		
				}
				else if($status == 3 && $result[$i]['Status'] <> 'Renewed')
				{
					array_push($this->unsetArray,$i);		
				}
				else if($status == 4 && $result[$i]['Status'] <> 'Closed')
				{
					array_push($this->unsetArray,$i);		
				}
						
		}
		$result = $this->UnsetArray($result);
		//print_r($result);
		$this->displayResults($result);
	}
	
	
	public function displayResults($details)
	{
		$flag = false;
		$skip = false;
		
		if(sizeof($details) > 0)
		{
			
			$this->obj_fetchData->GetSocietyDetails($_SESSION['society_id']);
			
			echo '<table   border="0" width="100%"   style="text-align:center;border: 0px solid #cccccc; border-collapse:collapse; padding-bottom:0px; display:none;" id="society_details"> 
				  <tbody>
						<tr   style="text-align:center; border-bottom:none;"><td  style="text-align:center;border: 0px solid #cccccc;"  colspan="6"><b>'.$this->obj_fetchData->objSocietyDetails->sSocietyName.'</b></td></tr>
						<tr   style="text-align:center; border-bottom:none;"><td  style="text-align:center;border: 0px solid #cccccc;"   colspan="6">';
						if($this->obj_fetchData->objSocietyDetails->sSocietyRegNo <> "")
						{
							echo "Registration No. ".$this->obj_fetchData->objSocietyDetails->sSocietyRegNo; 
						}
			echo			'</td></tr>
						<tr  style="text-align:center; border-bottom:none;"><td  style="text-align:center; border-bottom:none;border: 0px solid #cccccc;"   colspan="6">'.$this->obj_fetchData->objSocietyDetails->sSocietyAddress.'</td></tr>
					 </tbody>
				  </table>'; 
			echo '<br><table style="text-align:center;" class="table table-bordered table-hover table-striped">';
		
			foreach($details as $row2)
			{	
				if(!$flag) 
				{
					echo '<tr style="border:1px solid gray;">';
					echo implode('<td style="border:1px solid gray;">', array_keys($row2)) . "\n";
					$flag = true;
					echo '</tr>';
					echo '<tr style="border:1px solid gray;font-weight:lighter;">';
					echo implode('<td style="border:1px solid gray;font-weight:lighter;">', array_values($row2)) . "\n";
					echo '</tr>';
					
				}
				else
				{
					echo '<tr style="border:1px solid gray;font-weight:lighter;">';
					echo implode('<td style="border:1px solid gray;font-weight:lighter;">', array_values($row2)) . "\n";
					echo '</tr>';
				}
			}
			if(sizeof($details) > 0)
			{
				echo '</table></div>';
			}
		}
	}
	
	public function getMaturedFDs()
	{
		echo $sql = "select * from `fd_master` where `maturity_date`  <= DATE_SUB(NOW(), INTERVAL 15 day) ";
		$data = $this->m_dbConn->select($sql);
		return $data;
	}
	
	public function sendFDMaturityReminderEmail($societyID,$LedgerID ,$MaturityDate)
	{
		try
		{
			$this->m_dbConnRoot->begin_transaction();	
			$sqlcheck = "SELECT count(ID) AS `cnt` FROM `remindersms` WHERE `society_id` = '" . $societyID. "' AND `PeriodId` = '" . $LedgerID. "' 
								AND  `EventDate` = '" . getDBFormatDate($MaturityDate). "' ";
			$count = $this->m_dbConnRoot->select($sqlcheck);	
			
			$reminderDate = $this->m_objUtility->GetDateByOffset($MaturityDate, -15);
			
			if($count[0]['cnt'] == 0)
			{																					
				$sqlII = "INSERT INTO `remindersms`(`society_id`, `PeriodID`, `ReminderType`, `EventDate`, `EventReminderDate`, `LoginID`) 
								VALUES ('" . $this->m_dbConn->escapeString($societyID). "', '" . $this->m_dbConn->escapeString($LedgerID). "'
								,'".SENDFDREMINDER."','" . getDBFormatDate($this->m_dbConn->escapeString($MaturityDate)) . "'
								,'" . $this->m_dbConn->escapeString($reminderDate) . "','".$_SESSION['login_id']."')";												
				$res = $this->m_dbConnRoot->insert($sqlII);
			}
			else
			{
				$sqlUpdate = "UPDATE `remindersms` SET `EventDate`='" . getDBFormatDate($this->m_dbConn->escapeString($MaturityDate)) . "',
									 `EventReminderDate`='" . $this->m_dbConn->escapeString($reminderDate) . "', `LoginID` = '".$_SESSION['login_id']."'
									 , `ReminderType` = '".SENDFDREMINDER."'  WHERE `society_id`='" . $this->m_dbConn->escapeString($societyID). "' AND
									`PeriodID`='" . $this->m_dbConn->escapeString($LedgerID). "'";								
				$this->m_dbConnRoot->update($sqlUpdate);
			}
			$this->m_dbConnRoot->commit();
		}
		catch(Exception  $exp)
		{
			$this->m_dbConnRoot->rollback();
			echo "Exception Occured:".$exp->getMessage();	
		}
					
	}
	
	public function FetchFdCategories()
	{
		$FDArray = array();
		$sql = "SELECT  `category_id` FROM `account_category`  where `is_fd_category`= '1' ";
		$res = $this->m_dbConn->select($sql);	
		
		for($i= 0 ;$i < sizeof($res); $i++)
		{
			array_push($FDArray, $res[$i]['category_id']);		
		}
		array_push($FDArray, '0');		
		return $FDArray;		
	}

	public function getDepositPeriod($deposit_date , $maturity_date)
	{
		$date1 = new DateTime($deposit_date);
		$date2 = new DateTime($maturity_date);
		$diff = $date1->diff($date2);
		$period = $diff->y . " years, " . $diff->m." months, ".$diff->d." days ";
		return $period;
	}
	
	function array_splice_assoc ( &$input ,$key, $length = 0 , $replacement = null )
	{

		$keys = array_keys( $input );
		$offset = array_search( $key, $keys );
	
		if($replacement)
		{
			$values = array_values($input);
			$extracted_elements = array_combine(array_splice($keys, $offset, $length, array_keys($replacement)),array_splice($values, $offset, $length, array_values($replacement)));
			$input = array_combine($keys, $values);
		} 
		else 
		{
			$extracted_elements = array_slice($input, $offset, $length);
		}
		return $extracted_elements;
	}

public function comboboxForReport($status,$query,$id,$defaultText = 'All', $defaultValue = 0)
	{
		$str = '';
		$data = $this->m_dbConn->select($query);
		
		if(sizeof($data) > 0)
		{
			for($i = 0;$i < sizeof($data);$i++)
			{
					$sqlcheck = "select  Count(*) as cnt  from `paymentdetails` where `PaidTo` = '".$data[$i]['id']."' ";
					$data2 = $this->m_dbConn->select($sqlcheck);
					
					$sqlcheck2 = "select * from `fd_master` where `LedgerID` = '".$data[$i]['id']."' ";
					$data3 = $this->m_dbConn->select($sqlcheck2);
					
					$sqlcheckBalance  = "select  Count(*) as cnt  from `assetregister` where `LedgerID` = '".$data[$i]['id']."' and  `Is_Opening_Balance` = 1 and `Debit` <> 0";
					$dataBalance = $this->m_dbConn->select($sqlcheckBalance);
					
					if(($data2[0]['cnt'] > 0 || $dataBalance[0]['cnt'] > 0)  && $data3[0]['fd_close'] == 1)
					{
							$data[$i]['status'] = 'Closed';
					}
					else if(($data2[0]['cnt'] > 0 || $dataBalance[0]['cnt'] > 0)   && $data3[0]['fd_renew'] == 1)
					{
							$data[$i]['status'] = 'Renewed';	
					}
					else if(($data2[0]['cnt'] > 0 || $dataBalance[0]['cnt'] > 0) && $data3[0]['fd_close'] == 0  && $data3[0]['fd_renew'] == 0 )
					{
							$data[$i]['status'] = 'Active';
					}
					else if($data2[0]['cnt'] == 0 && $dataBalance[0]['cnt'] == 0 && sizeof($data3) == 0)
					{
							$data[$i]['status'] = 'Pending';	
					}
					
					$data[$i]['accrued_interest_legder'] = $ledgername_array[$data[$i]['accrued_interest_legder']];
					$data[$i]['interest_legder'] = $ledgername_array[$data[$i]['interest_legder']];
					
					if($status == 1 && $data[$i]['status'] <>  'Pending')
					{
						array_push($this->unsetArray,$i);		
					}
					else if($status == 2 && $data[$i]['status'] <> 'Active')
					{
						array_push($this->unsetArray,$i);		
					}
					else if($status == 3 && $data[$i]['status'] <> 'Renewed')
					{
						array_push($this->unsetArray,$i);		
					}
					else if($status == 4 && $data[$i]['status'] <> 'Closed')
					{
						array_push($this->unsetArray,$i);		
					}
			}
			$data = $this->UnsetArray($data);
		}
		
		if($defaultText != '' && sizeof($data) > 0)
		{?>
        <ul>
			<li>&nbsp;<input type="checkbox"  name="<?php echo $defaultText;?>"    id = '0' class="checkBox  chekAll"/>&nbsp;All</li>
       <?php 
		
		for($i = 0; $i < sizeof($data); $i++)
		{?>
        	<li>&nbsp;<input type="checkbox"  id="<?php echo $data[$i]['id'];?>" class="checkBox"  onChange="uncheckDefaultCheckBox(this.id);"/>&nbsp; <?php echo $data[$i]['ledger_name'];?></li>
       <?php		
		}
		?>
		</ul>
        
        <?php 
		}
		else
		{
			return '<font style="color:#F00;"><b>Records Not Found...</b></font>';	
		}
	}
	
	public function UnsetArray(&$Data)
	{
		//unset array element from main array and return final array
		foreach($this->unsetArray as $key=>$value)
		{
			unset($Data[$value]);	
		}
		$Data = array_values($Data);
		
		return $Data;	
	}
	
	public function fetch_voucher_details($voucher_no, $ledger_name)
	{
		$sql01 = "select id,VoucherTypeID from voucher where VoucherNo = '".$voucher_no."'";
		$sql11 = $this->m_dbConn->select($sql01);
		$voucher_id = $sql11[0]['id'];
		$voucher_type = $sql11[0]['VoucherTypeID'];
		
		$sql02 = "select id from ledger where ledger_name = '".$ledger_name."'";
		$sql22 = $this->m_dbConn->select($sql02);
		$ledger_id = $sql22[0]['id'];
		
		$to_pass = array();
		array_push($to_pass,$voucher_id,$voucher_type,$ledger_id);
		
		return $to_pass;
	}
	
	/*public function getAccuedInterestFromFD_JVTable($fd_id, $LedgerID, $accrued_interest_legder)
	{
		$sql01 = "select voucher_no from fd_voucher where fd_id = '".$fd_id."'";
		$sql11 = $this->m_dbConn->select($sql01);
		$all_voucher_amt = array();
		
		for($i = 0; $i < sizeof($sql11); $i++)
		{
			$sql02 = "select Debit from voucher where VoucherNo = '".$sql11[$i]['voucher_no']."'";
			$sql22 = $this->m_dbConn->select($sql02);
			
			array_push($all_voucher_amt,$sql22[0]['Debit']);
		}
		
	}*/
	
	public function get_details_for_renew($ledger_id)
	{
		$sql01 = "select * from fd_master where LedgerID = '".$ledger_id."'";
		$sql11 = $this->m_dbConn->select($sql01);
		
		return $sql11;
	}
	
	public function get_fd_close_details($fd_id)
	{
		$sql01 = "select * from fd_master where id = '".$fd_id."'";
		$sql11 = $this->m_dbConn->select($sql01);
		
		return $sql11[0]['fd_close'];
	}
	
	public function get_ledgers($fd_id)
	{
		$sql01 = "SELECT accrued_interest_legder, interest_legder FROM `fd_master` WHERE id = '".$fd_id."'";
		$sql11 = $this->m_dbConn->select($sql01);
		
		return $sql11;
	}
	
	public function update_ledgers($fd_id, $interest_legder, $accrued_interest_legder)
	{
		$sql01 = "SELECT accrued_interest_legder,interest_legder FROM fd_master WHERE id = '".$fd_id."'";
		$sql11 = $this->m_dbConn->select($sql01);
		
		if($sql11[0]['accrued_interest_legder'] == 0)
		{
			$sql02 = "UPDATE fd_master SET accrued_interest_legder = '".$accrued_interest_legder."' WHERE id = '".$fd_id."'";
			$sql22 = $this->m_dbConn->update($sql02);
		}
		
		if($sql11[0]['interest_legder'] == 0)
		{
			$sql03 = "UPDATE fd_master SET interest_legder = '".$interest_legder."' WHERE id = '".$fd_id."'";
			$sql33 = $this->m_dbConn->update($sql03);
		}
	}
}

?>