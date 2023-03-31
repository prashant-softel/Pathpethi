<?php
	//error_reporting(7);
	include_once "dbconst.class.php";	
	include_once "adduser.class.php";
	include_once "initialize.class.php";
	include_once("../GDrive.php");
		
	class activation
	{
		
		private $m_dbConn;
		public $m_dbConnRoot;
		public $obj_addduser;
		public $obj_initialize;
		public $obj_utility;
		public $m_bShowTrace;
		function __construct($dbConn, $dbConnRoot = "")
		{
			//echo "ctor";
			$this->m_bShowTrace = 1;
			$this->m_dbConn = $dbConn;
			$this->m_dbConnRoot = $dbConnRoot;
			$this->obj_addduser = new adduser($this->m_dbConnRoot,$this->m_dbConn);
			$this->obj_initialize = new initialize($this->m_dbConnRoot);
			//$this->obj_utility = new utility($this->m_dbConn, $this->m_dbConnRoot);
		}
		
		function AddMappingAndSendActivationEmail($role, $unit_id, $society_id, $code, $NewUserEmailID, $name)
		{
			//echo "trace:";
			$result = $this->obj_addduser->addUser($role, $unit_id, $society_id, $code);
						
				//		echo "trace2".$result ;
			if($result > 0)
			{
				$ActivationStatus = $this->obj_initialize->sendNewUserActivationEmail( $NewUserEmailID, $name,$code, "1", $society_id);
				//echo "ActivationStatus:".$ActivationStatus;
				return $ActivationStatus;
			}
		}
	}
	class utility
	{
		private $m_dbConn;
		public $m_dbConnRoot;
	
		function __construct($dbConn, $dbConnRoot = "")
		{
			//echo "ctor";
			$this->m_dbConn = $dbConn;
			$this->m_dbConnRoot = $dbConnRoot;
		}

		function GetPrevUsedEmailID()
		{
			$EMailIDs = $this->m_dbConnRoot->select("select * from `loginemailids` where LastUsed = 1");
			//print_r($EMailIDs);
			return ($EMailIDs);
		}
		function GetEmailIDDetails($EmailID, &$id, &$MaxLimit, &$EmailIDSentCounter, &$UsedTimeStamp)
		{
			$id = $EmailID['id'];
			$MaxLimit = $EmailID['MaxLimit'];
			$EmailIDSentCounter = $EmailID['EmailSentCounter'];
			$UsedTimeStamp = $EmailID['LastUsedTimeStamp'];
		}
		
		function GetEmailIDToUse($bSendEMailInQueue, $EmailSourceModule ,$PeriodID = "", $UnitID = "", $CronJobProcess = "", $DBName = "", $SocietyID = "", $iNoticeID = "", $bccUnitsArray)
		{
			//echo "bSendEMailInQueue:<".$bSendEMailInQueue .">EmailSourceModule:<".$EmailSourceModule."> PeriodID:<".$PeriodID .">UnitID <".$UnitID ."> CronJobProcess <".$CronJobProcess ."> DBName:<".$DBName .">SocietyID <". $SocietyID ."> iNoticeID<" .$iNoticeID ."> bccUnitsArray <";
			$ResultAry = array();
			error_reporting(0);
			$ResultAry['status'] = 0;
			$ResultAry['msg'] = 0;
			$ResultAry['email'] = '';
			$ResultAry['password'] = '';
		
			$ClientID = 0;

			if($SocietyID != "" && $SocietyID != 0)
			{
				//echo "1";
				  $ClientQuery =$this->m_dbConnRoot->select("select client_id from `society` where society_id = '" . $SocietyID . "'");
				$ClientID = $ClientQuery[0]['client_id'];
				//echo "2";
			}
			
			$ListOfEmailIDs = $this->m_dbConnRoot->select("select * from `loginemailids` where client_id = '" . $ClientID . "'");
			$iEmailCount = sizeof($ListOfEmailIDs);
			
			//echo 'Email Count : ' . $iEmailCount;
			$sql_query = "update `loginemailids` set `EmailSentCounter`=0 where `LastUsedTimeStamp` < TIMESTAMPADD( HOUR , -1, NOW( )+ INTERVAL 5 HOUR + INTERVAL 30 MINUTE)";
			//$sql_query = "update `loginemailids` set `EmailSentCounter`=0 where `LastUsedTimeStamp` < TIMESTAMPADD( HOUR , -1, NOW( ))";
			$this->m_dbConnRoot->update($sql_query);
			
			//$EMailDetails = array();
			if($iEmailCount > 0)
			{
				//echo 'getting email ids';
				//$PrevEmailID = $this->GetPrevUsedEmailID();
				//$iNextID = $PrevEmailID[0]['id'];
				
				
				for($iEmailIDCounter = 0; $iEmailIDCounter < $iEmailCount; $iEmailIDCounter++ )
				{
					//echo "iCounter:".$iEmailIDCounter;
					$LastUsedFlag = $ListOfEmailIDs[$iEmailIDCounter]["LastUsed"];
					if($LastUsedFlag == "1")
					{ 
						//echo "<br/>lastUsedFlag at index:<".$iEmailIDCounter.">";
						$arNextID = $ListOfEmailIDs[$iEmailIDCounter];
						//print_r($arNextID);
						$PrevID = "";
						$MaxLimit = "";
						$PrevCounter = "";
						$PrevTimeStamp = "";
						$this->GetEmailIDDetails($arNextID, $PrevID, $MaxLimit, $PrevCounter, $PrevTimeStamp);
						//echo "<br/>fetched details : id<".$PrevID .">,Limit<".$MaxLimit.">,Counter<".$PrevCounter.">,Last used Timestamp<".$PrevTimeStamp .">";
	
						$iMaxLimit = (int)$MaxLimit;
						$iPrevCounter = (int)$PrevCounter;
						$iPrevID = (int)$PrevID;
						$iEMailCount = (int)$iEmailCount;
						
						//echo "<br/>iEMailCount:<". $iEMailCount .">";
						$sCurrentTimeStamp = getCurrentTimeStamp();
						//echo "<br/>sCurrentTimeStamp:".$sCurrentTimeStamp['DateTime'];
						$iDiffInMins = $this->getTimeDiff($PrevTimeStamp, $sCurrentTimeStamp['DateTime'], HOUR) ;
						//echo "maxlimit <".$iMaxLimit .">PrevCounter:<".$iPrevCounter.">";
						if($iPrevCounter < $iMaxLimit)
						{
							$NextCounter = $iPrevCounter + 1;
							
							$sql_query = "update `loginemailids` SET LastUsed=1,LastUsedTimeStamp='".$sCurrentTimeStamp['DateTime']."',EmailSentCounter='".$NextCounter."'  where id='". $iPrevID ."'";
							$this->m_dbConnRoot->update($sql_query);
							$RetVal = $this->m_dbConnRoot->select("select * from `loginemailids` where id='".$iPrevID."'");
							$ResultAry['email'] = $RetVal[0]['EmailID'];
							$ResultAry['password'] = $RetVal[0]['Password'];
						}
						else
						{
							 if($iDiffInMins > 0)
							 {
								$NextCounter = 1;
								$sql_query = "update `loginemailids` SET LastUsed=1,LastUsedTimeStamp='".$sCurrentTimeStamp['DateTime']."',EmailSentCounter='".$NextCounter."'  where id='". $iPrevID ."'";
								$this->m_dbConnRoot->update($sql_query);
								$RetVal = $this->m_dbConnRoot->select("select * from `loginemailids` where id='".$iPrevID."'");
								$ResultAry['email'] = $RetVal[0]['EmailID'];
								$ResultAry['password'] = $RetVal[0]['Password'];
							 }
							 else
							 {
								$Query = "select id,EmailSentCounter from `loginemailids` where EmailSentCounter<>$MaxLimit and client_id = '" . $ClientID . "'"; 
								
								$NextAvailableID = $this->m_dbConnRoot->select($Query);
								//echo 'next available id:';
								//print_r($NextAvailableID);
								if(sizeof($NextAvailableID) > 0)
								{
									$iNextID = (int)$NextAvailableID[0]["id"];
									
									$NextCounter2 = (int)$NextAvailableID[0]["EmailSentCounter"];
									$NextCounter2 = $NextCounter2 + 1;
									$sql_query = "update `loginemailids` SET LastUsed=1,LastUsedTimeStamp='".$sCurrentTimeStamp['DateTime']."',EmailSentCounter='".$NextCounter2."'  where id='". $iNextID ."'";
									$this->m_dbConnRoot->update($sql_query);
									$NextCounter = $iPrevCounter + 1;
									$sql_query = "update `loginemailids` SET LastUsed=0,LastUsedTimeStamp='".$sCurrentTimeStamp['DateTime']."',EmailSentCounter='".$iPrevCounter."'  where id='". $iPrevID ."'";
									$this->m_dbConnRoot->update($sql_query);
									$RetVal = $this->m_dbConnRoot->select("select * from `loginemailids` where id='".$iNextID."'");
									$ResultAry['email'] = $RetVal[0]['EmailID'];
									$ResultAry['password'] = $RetVal[0]['Password'];
								}
								else
								{
									if($CronJobProcess)
									{
										//$ResultAry['msg'] = "All the email servers are busy at the moment. Email will be send shortly.";
										$ResultAry['status'] = 1;
									}
									else
									{
										//echo "<br/>SendEMailInQueue".$bSendEMailInQueue."<br/>";
										if($bSendEMailInQueue)
										{
											$ResultAry['status'] = 2;
											switch($EmailSourceModule)
											{
												case "0":
														//echo "M-Bill Mode";
														//$ResultAry['msg'] = "All the email servers are busy at the moment. Please try sending emails after sometime.";
														$SQL_query_existCheck = "select * from `emailqueue` where `dbName`='".$DBName."' and `PeriodID`='".$PeriodID."' and `SocietyID`='".$SocietyID."' and `UnitID`='".$UnitID."' and `Status`=0 and `ModuleTypeID`='".$EmailSourceModule."'";
														$SQL_query_existCheckRes = $this->m_dbConnRoot->select($SQL_query_existCheck);
														//echo "Already exist count:".sizeof($SQL_query_existCheckRes);
														if(sizeof($SQL_query_existCheckRes) == 0)
														{
															$queue_query = "insert into `emailqueue`(`dbName`,`PeriodID`, `SocietyID`, `UnitID`, `ModuleTypeID`) values ('".$DBName."','".$PeriodID."','".$SocietyID."','".$UnitID."','".$EmailSourceModule."')";
															//echo "<br/>".$queue_query;
															$this->m_dbConnRoot->insert($queue_query);
														}
														break;
												case "1":
												case "2":
												case "3":
														//echo "Notice Mode";
														//print_r($bccUnitsArray);
														foreach($bccUnitsArray as $aryBCCEmailUnit)
														{ 
															
															//echo "aryBCCEmailUnit:<".$aryBCCEmailUnit.">";
															$SQL_query_existCheck = "select * from `emailqueue` where `dbName`='".$DBName."' and `SocietyID`='".$SocietyID."' and `UnitID`='".$aryBCCEmailUnit."' and `SourceTableID`='".$iNoticeID."' and `Status`=0 and `ModuleTypeID`='".$EmailSourceModule."'";
															$SQL_query_existCheckRes = $this->m_dbConnRoot->select($SQL_query_existCheck);
															//echo "<br/>".$SQL_query_existCheck;
															//echo "Already exist count:".sizeof($SQL_query_existCheckRes);
															if(sizeof($SQL_query_existCheckRes) == 0)
															{
																$queue_query = "insert into `emailqueue`(`dbName`,`SourceTableID`, `SocietyID`, `UnitID`, `ModuleTypeID`) values ('".$DBName."','".$iNoticeID."','".$SocietyID."','".$aryBCCEmailUnit."','".$EmailSourceModule."')";
																//echo "<br/>".$queue_query;
																$this->m_dbConnRoot->insert($queue_query);
															}
															else
															{
																$ResultAry['msg'] = "Email already in Queue.";
																$ResultAry['status'] = 3;
															}
														}
														break;
												default:
														//echo "None";
											}
										}
										
									}
										//echo "All the email servers are busy at the moment. Please try sending emails after sometime.";
									if($ResultAry['status'] == 3)
									{
										$ResultAry['msg'] = "Email already in Queue.";
									}
									else
									{
										$ResultAry['msg'] = "All the email servers are busy at the moment. Please try sending emails after sometime.";
									}
								}
							}
							
						}
						break;
					}
				}
			}
			else
			{
				$ResultAry['status'] = 1;
				$ResultAry['msg'] = 'No EMail ID available to send email';
			}
			
			if($ResultAry['status'] == 0)
			{
				date_default_timezone_set('Asia/Kolkata');	
				$current_dateTime = date('Y-m-d H:i:s ');
				
				$sqlInsertLog = "INSERT INTO `loginemail_utilized_log`(`client_id`, `society_id`, `email_id`, `timestamp`) VALUES ('" . $ClientID . "', '" . $SocietyID . "', '" . $ResultAry['email'] . "', '" . $current_dateTime . "')";
				$this->m_dbConnRoot->insert($sqlInsertLog);
			}

			return $ResultAry;
		}
		
		public function getParentOfCategory($categoryID)
		{
			$sqlSelect = "select categorytbl.group_id,grouptbl.groupname from account_category As categorytbl JOIN `group` As grouptbl where categorytbl.category_id = '" . $categoryID . "' and grouptbl.id=categorytbl.group_id ";
			$result = $this->m_dbConn->select($sqlSelect);
			
			$aryParent = array();
			$aryParent['group'] = $result[0]['group_id'];
			$aryParent['groupname'] = $result[0]['groupname'];
			//$aryParent['category'] = $result[0]['categoryid'];
			
			return $aryParent;
		}

		public function getLedgerID($FDName)
		{
			$LedgerID = 0;
			$sql="select `id` from `ledger` where `ledger_name`='".$FDName."' ";
			$result = $this->m_dbConn->select($sql);
			
			if(!empty($result) && !empty($result[0]['id']))
			{
				$LedgerID = $result[0]['id'];
			}
					
			return $LedgerID;	
			
		}
		
		function getDateTime()
		{
			$dateTime = new DateTime();
			$dateTimeNow = $dateTime->format('Y-m-d H:i:s');
			return $dateTimeNow;
		}
		
		
		public function getParentOfLedger($ledgerID)
		{
			//echo $ledgerID;
			$sqlSelect = "select categorytbl.group_id, categorytbl.category_name, ledgertbl.categoryid,ledgertbl.ledger_name from ledger As ledgertbl JOIN account_category As categorytbl ON ledgertbl.categoryid = categorytbl.category_id where ledgertbl.id = '" . $ledgerID . "'";
			$result = $this->m_dbConn->select($sqlSelect);
			$sqlGroup="select `groupname` from `group` where `id`='".$result[0]['group_id']."' ";
			$resultGroupName = $this->m_dbConn->select($sqlGroup);
			$aryParent = array();
			$aryParent['group'] = $result[0]['group_id'];
			$aryParent['group_name'] = $resultGroupName[0]['groupname'];
			$aryParent['category'] = $result[0]['categoryid'];
			$aryParent['category_name'] = $result[0]['category_name'];
			$aryParent['ledger_name'] = $result[0]['ledger_name'];
			//print_r($aryParent);			
			//return json_encode($aryParent);
			return $aryParent;
		}
		public function getParentOfLedgerGroup($ledgerID)
		{
			//echo $ledgerID;
			$sqlSelect = "select categorytbl.group_id, categorytbl.category_name, ledgertbl.categoryid,ledgertbl.ledger_name from ledger As ledgertbl JOIN account_category As categorytbl ON ledgertbl.categoryid = categorytbl.category_id where ledgertbl.id = '" . $ledgerID . "'";
			$result = $this->m_dbConn->select($sqlSelect);
			$sqlGroup="select `groupname` from `group` where `id`='".$result[0]['group_id']."' ";
			$resultGroupName = $this->m_dbConn->select($sqlGroup);
			$aryParent = array();
			$aryParent['group'] = $result[0]['group_id'];
			//$aryParent['group_name'] = $resultGroupName[0]['groupname'];
			//$aryParent['category'] = $result[0]['categoryid'];
			//$aryParent['category_name'] = $result[0]['category_name'];
			//$aryParent['ledger_name'] = $result[0]['ledger_name'];
			//print_r($aryParent);			
			//return json_encode($aryParent);
			return $aryParent;
		}
		
		public function getDateDiff($date1, $date2, $Interval = "day")
		{
			//$sql = "SELECT DATEDIFF($Interval,'" . $date1 . "','" . $date2 . "') AS DiffDate";
			$sql = "SELECT TIMESTAMPDIFF(".$Interval.",'" . $date2 . "','" . $date1 . "') AS DiffDate";
			//echo $sql;
			$result = $this->m_dbConn->select($sql);
			return $result[0]['DiffDate'];
		}
					
		public function getDateDiffForPeriod($yyyymmdd1,$yyyymmdd2)
		{
			$date1 = new DateTime($yyyymmdd1);
			$date2 = new DateTime($yyyymmdd2);
			$diff = $date1->diff($date2);
			
			//echo "difference " . $diff->y . " years, " . $diff->m." months, ".$diff->d." days ";
			return $diff;
		}

		public function getTimeDiff($date1, $date2, $Interval = "DAY")
		{
			$sql = "SELECT TIMESTAMPDIFF(".$Interval.",'" . $date1 . "','" . $date2 . "') AS DiffDate";
			//echo $sql;
			$result = $this->m_dbConn->select($sql);
			//var_dump($result);
			return $result[0]['DiffDate'];
		}
		
		public function getIsDateInRange($dateToCheck, $date1, $date2)
		{
			$ts_dateToCheck = strtotime($dateToCheck);
			$ts_date1 = strtotime($date1);
			$ts_date2 = strtotime($date2);
			
			//echo "********getIsDateInRange : " . $dateToCheck . ":" . $ts_dateToCheck . ":" . $date1 . ":" . $ts_date1 . ":"  . $date2 . ":" . $ts_date2 . "********";
			
			return (($ts_dateToCheck >= $ts_date1) && ($ts_dateToCheck <= $ts_date2));	
		}
		
		public function getLedgerName($LedgerID)
		{
			$sql="select ledger_name from `ledger` where id=".$LedgerID." ";
			$result = $this->m_dbConn->select($sql);	
			$ledger = $result[0]['ledger_name'];
			$arParentDetails = $this->getParentOfLedger($LedgerID);
			if(!(empty($arParentDetails)))
			{			
				$categoryID = $arParentDetails['category'];
				if($categoryID == DUE_FROM_MEMBERS)
				{
					$sqlQuery = "SELECT `owner_name` FROM `member_main` WHERE `unit` = '".$LedgerID."' and  `ownership_status` = 1";
					$memberName = $this->m_dbConn->select($sqlQuery);
					if(sizeof($memberName) > 0)
					{
						$ledger .= " - ".$memberName[0]['owner_name'];	
					}
				}			
			}		
			return $ledger;	
		}
		
		
		
		public function logGeneratorOLD($errorfile,$line_no,$errormsg)
		{
							
			if($line_no=='')
			{
				$msgFormat=$errormsg."\r\n";
				fwrite($errorfile,$msgFormat);	
			}
			else if($line_no=='start')
			{
				$msgFormat=$errormsg."\r\n";
				fwrite($errorfile,$msgFormat);
			}
			else if($line_no=='End')
			{
				$msgFormat=$errormsg."\r\n\n";
				fwrite($errorfile,$msgFormat);
			}
			else
			{
				$msgFormat="ErrorLog:[Line No:" .$line_no."]".$errormsg."\r\n";
				fwrite($errorfile,$msgFormat);
			}
			
		}
		
		
		
		public function logGenerator($errorfile,$line_no,$errormsg,$logType="")
		{
							
			if($line_no=='')
			{
				$msgFormat=$errormsg."\r\n";
				fwrite($errorfile,$msgFormat);	
			}
			else if($line_no=='start')
			{
				$msgFormat=$errormsg."\r\n";
				fwrite($errorfile,$msgFormat);
			}
			else if($line_no=='End')
			{
				$msgFormat=$errormsg."\r\n\n";
				fwrite($errorfile,$msgFormat);
			}
			else
			{
				if($logType=="E")
				{
					$msgFormat="<ul type='disc'><li><font style='background-color:#F88'> Error:[CSV Line No:" .$line_no."] ".$errormsg."</font></li></ul>";
					fwrite($errorfile,$msgFormat);
				}
				else if($logType=="W")
				{
					$msgFormat="<ul type='disc'><li><font style='background-color:#FF9;'>Warning:[CSV Line No:" .$line_no."] ".$errormsg."</font></li></ul>";
					fwrite($errorfile,$msgFormat);
				}
				else if($logType=="I")
				{
					$msgFormat="<ul type='disc'><li>Information:[CSV Line No:" .$line_no."] ".$errormsg."</li></ul>";
					fwrite($errorfile,$msgFormat);
				}
				else
				{
					$msgFormat="ErrorLog:[Line No:" .$line_no."]".$errormsg."\r\n";
					fwrite($errorfile,$msgFormat);
				}
			}
			
		}
		
		public function getCurrentYearBeginingDate($YearID)
		{
			$getPerod="select `BeginingDate` from `year` where `YearID`= ".$YearID." ";
			$period = $this->m_dbConn->select($getPerod);
			$from =	$period[0]['BeginingDate'];
			return $from;
		}
		
		public function encryptData($input)
		{
			$output = '';
			$key = (string)bin2hex('ajshdj9wieuroweurkscne98rw84fjkdnfiwfndsf4nf94hfinw4hr94heirh9fn');
			$output = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $input, MCRYPT_MODE_CBC, md5(md5($key))));
			$output = rtrim(strtr(base64_encode($output), '+/', '-_'), '='); 
			return $output;
		}
		
		public function decryptData($input)
		{
			$output = '';
			$key = (string)bin2hex('ajshdj9wieuroweurkscne98rw84fjkdnfiwfndsf4nf94hfinw4hr94heirh9fn');
			$input = base64_decode(str_pad(strtr($input, '-_', '+/'), strlen($input) % 4, '=', STR_PAD_RIGHT)); 
			$output = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($input), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
			return $output;
		}
		
		/*Get Supplementary Bill Opening Balance for Member Leder Reports*/
		//NOTE : JV are not considered in this balance
		public function getOpeningBalance_ForBillType($LedgerID, $date, $BillType = 1)
		{

			$openingBalance = array("Credit" => 0 ,"Debit" => 0 ,"Total" => 0, "OpeningType" => 0 ,"OpeningDate" => $date);

			 $sqlBill = "select sum(assettbl.Debit) as Debit, sum(assettbl.Credit) as Credit, assettbl.LedgerID as LedgerID, unittbl.unit_id from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN `billdetails` as billdet on vchrtbl.RefNo=billdet.ID where vchrtbl.RefTableID='1' AND billdet.BillType = '" . $BillType . "' and unittbl.unit_id = '" . $LedgerID . "' and assettbl.Date < '" . $date . "'";

			$resultBill = $this->m_dbConn->select($sqlBill);
/*			
echo "<BR>sqlBill :" . $sqlBill . "<BR>";
print_r($resultBill );
echo "<BR>";
*/
			$openingBalance['Credit'] = $resultBill[0]['Credit'];
			$openingBalance['Debit'] = $resultBill[0]['Debit'];
			
			$sSqlReceipt = "select sum(assettbl.Debit) as Debit, sum(assettbl.Credit) as Credit, assettbl.LedgerID as LedgerID, unittbl.unit_id from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN `chequeentrydetails` as chqdet on vchrtbl.RefNo=chqdet.ID where vchrtbl.RefTableID='2' AND chqdet.BillType = '" . $BillType . "' and unittbl.unit_id = '" . $LedgerID . "' and assettbl.Date < '" . $date . "'";

			$resultReceipt = $this->m_dbConn->select($sSqlReceipt);
/*
echo "<BR>sSqlReceipt :" . $sSqlReceipt . "<BR>";
print_r($resultReceipt);
echo "<BR>";
*/
			$openingBalance['Credit'] += $resultReceipt[0]['Credit'];
			$openingBalance['Debit'] += $resultReceipt[0]['Debit'];

//Get returned cheques
			$sSqlReceipt = "SELECT * FROM `chequeentrydetails` where isreturn = 1 and paidby = '" . $LedgerID . "'  and BillType = '" . $BillType . "' and VoucherDate < '" . $date . "'";

			$resultReceipt = $this->m_dbConn->select($sSqlReceipt);
//			$openingBalance['Credit'] += $resultReceipt[0]['Credit'];
			$openingBalance['Debit'] = $openingBalance['Debit'] + $resultReceipt[0]['Amount'];

			$openingBalance['Total'] = ($openingBalance['Debit'] - $openingBalance['Credit']);

			if($openingBalance['Total'] < 0)
			{
				$openingBalance['OpeningType'] = TRANSACTION_CREDIT;
				$openingBalance['Total'] = abs($openingBalance['Total']);			
			}
			else
			{
				$openingBalance['OpeningType'] = TRANSACTION_DEBIT;	
			}
/*
echo "<BR>openingBalance :" . $openingBalance . "<BR>";
print_r($openingBalance);
echo "<BR>";
*/
			return $openingBalance;
		}

	public function getOpeningBalancePeriodID()
	{
			$sqlFetch="SELECT `society_creation_yearid` FROM `society` where society_id = '".$_SESSION['society_id']."'";
			$res = $this->m_dbConn->select($sqlFetch);
			
			$currentYear = $res[0]['society_creation_yearid'];
			$sql = "Select  ID from period  where YearID = '" . ($currentYear - 1) . "' and IsYearEnd = 1 ORDER BY  ID ASC";
		
			$result = $this->m_dbConn->select($sql);
			
			$PeriodID = $result[0]['ID'];
			return $PeriodID; 
	}
	
	public function getInceptionOpeningBalanceSplit($UnitID)
	{
			//Get opening balance periodID to fetch Maint and Supp opening balance when soc created
			$OpBalPeriodID = $this->getOpeningBalancePeriodID();
			
			$sqlbill = "select bill.PeriodID, bill.PrincipalArrears, bill.InterestArrears, bill.BillSubTotal, bill.BillInterest, bill.TotalBillPayable, prd.YearID from billdetails as bill JOIN period as prd ON bill.PeriodID = prd.ID where BillType = 0 and UnitID = '" . $UnitID . "' and PeriodID = '" . $OpBalPeriodID . "'";

			$resultbill = $this->m_dbConn->select($sqlbill);
			//$var[0]['year'] = $result[0]['YearID'];
			$var[0]['year'] = $currentYear - 1;
			$var[0]['period'] = $OpBalPeriodID;
			$var[0]['principle'] = $resultbill[0]['PrincipalArrears'];
			$var[0]['interest'] = $resultbill[0]['InterestArrears'];
			$var[0]['billsubtotal'] = $resultbill[0]['BillSubTotal'];
			$var[0]['billinterest'] = $resultbill[0]['BillInterest'];
			$var[0]['TotalBillPayable'] = $resultbill[0]['TotalBillPayable'];

			$sqlbill = "select bill.PeriodID, bill.PrincipalArrears, bill.InterestArrears, bill.BillSubTotal, bill.BillInterest, bill.TotalBillPayable, prd.YearID from billdetails as bill JOIN period as prd ON bill.PeriodID = prd.ID where BillType = 1 and UnitID = '" . $UnitID . "' and PeriodID = '" . $OpBalPeriodID . "'";
			
			$resultbill = $this->m_dbConn->select($sqlbill);
/*			echo  "<BR>";
			print_r($resultbill );
			echo  "<BR>";
*/			
			if($resultbill <> '')
			{
				//$var[0]['year'] = $result[0]['YearID'];
				$var[0]['supp_principle'] = $resultbill[0]['PrincipalArrears'];
				$var[0]['supp_interest'] = $resultbill[0]['InterestArrears'];
				$var[0]['supp_billsubtotal'] = $resultbill[0]['BillSubTotal'];
				$var[0]['supp_billinterest'] = $resultbill[0]['BillInterest'];
				$var[0]['supp_TotalBillPayable'] = $resultbill[0]['TotalBillPayable'];
			}
			else
			{
				//$var[0]['year'] = $result[0]['YearID'];
				$var[0]['supp_principle'] = 0;
				$var[0]['supp_interest'] = 0;
				$var[0]['supp_billsubtotal'] = 0;
				$var[0]['supp_billinterest'] = 0;
				$var[0]['supp_TotalBillPayable'] = 0;
			}
			
			return $var;
	}

		public function getOpeningBalance($LedgerID,$date)
		{
		
			$openingBalance = array("LedgerName" => "","Credit" => 0 ,"Debit" => 0 ,"Total" => 0,"OpeningType" => 0 ,"OpeningDate" => $date);
			
			$arParentDetails = $this->getParentOfLedger($LedgerID);
			if(!(empty($arParentDetails)))
			{
				$LedgerGroupID = $arParentDetails['group'];
				$LedgerCategoryID = $arParentDetails['category'];
				
				if($LedgerGroupID == LIABILITY)
				{
					 $sqlLiability = "SELECT SUM(Credit) as Credit,
									SUM(Debit) as Debit,(SUM(Credit) - SUM(Debit)) as Total
									FROM `liabilityregister` where LedgerID = '".$LedgerID."' and  Date < '".getDBFormatDate($date)."' ";
					$result = $this->m_dbConn->select($sqlLiability);
					$openingBalance['Credit'] = $result[0]['Credit'];
					$openingBalance['Debit'] = $result[0]['Debit'];
					$openingBalance['Total'] = $result[0]['Total'];
					
					
					if($openingBalance['Total'] < 0)
					{
						$openingBalance['OpeningType'] = TRANSACTION_DEBIT;	
						$openingBalance['Total'] = abs($openingBalance['Total']);	
					}
					else
					{
						$openingBalance['OpeningType'] = TRANSACTION_CREDIT;		
					}
					
				}
				else if($LedgerGroupID == ASSET && ($LedgerCategoryID == BANK_ACCOUNT || $LedgerCategoryID == CASH_ACCOUNT))
				{
					 $sqlBank = "SELECT SUM(ReceivedAmount) as Credit,
								SUM(PaidAmount) as Debit,(SUM(ReceivedAmount) - SUM(PaidAmount)) as Total 
								FROM `bankregister` where LedgerID = '".$LedgerID."' and  Date < '".getDBFormatDate($date)."' ";								
					$result = $this->m_dbConn->select($sqlBank);
					$openingBalance['Credit'] = $result[0]['Credit'];
					$openingBalance['Debit'] = $result[0]['Debit'];
					$openingBalance['Total'] = $result[0]['Total'];
					
					if($openingBalance['Total'] < 0)
					{
						$openingBalance['OpeningType'] = TRANSACTION_CREDIT;
						$openingBalance['Total'] = abs($openingBalance['Total']);			
					}
					else
					{
						$openingBalance['OpeningType'] = TRANSACTION_DEBIT;	
					}

						
					
				}
				else if($LedgerGroupID == ASSET)
				{
					$sqlAsset = "SELECT SUM(Credit) as Credit,
								SUM(Debit) as Debit,(SUM(Debit) - SUM(Credit)) as Total 
								FROM `assetregister` where LedgerID  = '".$LedgerID."' and  Date < '".getDBFormatDate($date)."' ";
					$result = $this->m_dbConn->select($sqlAsset);
					$openingBalance['Credit'] = $result[0]['Credit'];
					$openingBalance['Debit'] = $result[0]['Debit'];
					$openingBalance['Total'] = $result[0]['Total'];
					
					if($openingBalance['Total'] < 0)
					{
						
						$openingBalance['OpeningType'] = TRANSACTION_CREDIT;
						$openingBalance['Total'] = abs($openingBalance['Total']);			
					}
					else
					{
						$openingBalance['OpeningType'] = TRANSACTION_DEBIT;	
						
					}
						
					
				}
				else if($LedgerGroupID == INCOME)
				{
					$sqlIncome = "SELECT SUM(Credit) as Credit,
								SUM(Debit) as Debit,(SUM(Credit) - SUM(Debit)) as Total 
								FROM `incomeregister` where LedgerID  = '".$LedgerID."' and  Date < '".getDBFormatDate($date)."' ";
					$result = $this->m_dbConn->select($sqlIncome);
					$openingBalance['Credit'] = $result[0]['Credit'];
					$openingBalance['Debit'] = $result[0]['Debit'];
					$openingBalance['Total'] = $result[0]['Total'];
					
					if($openingBalance['Total'] < 0)
					{
						$openingBalance['OpeningType'] = TRANSACTION_DEBIT;
						$openingBalance['Total'] = abs($openingBalance['Total']);			
					}
					else
					{
						$openingBalance['OpeningType'] = TRANSACTION_CREDIT;
						
					}
					
				}
				else if($LedgerGroupID == EXPENSE)
				{
					$sqlExpense = "SELECT SUM(Credit) as Credit,
								SUM(Debit) as Debit,(SUM(Debit) - SUM(Credit)) as Total 
								FROM `expenseregister` where LedgerID  = '".$LedgerID."' and  Date < '".getDBFormatDate($date)."' ";
					$result = $this->m_dbConn->select($sqlExpense);
					$openingBalance['Credit'] = $result[0]['Credit'];
					$openingBalance['Debit'] = $result[0]['Debit'];
					$openingBalance['Total'] = $result[0]['Total'];
					
					if($openingBalance['Total'] < 0)
					{
						$openingBalance['OpeningType'] = TRANSACTION_CREDIT;
						$openingBalance['Total'] = abs($openingBalance['Total']);
									
					}
					else
					{
						$openingBalance['OpeningType'] = TRANSACTION_DEBIT;	
					}
				}
				
			}
			if($result <> "")
			{
				$sql = "select `ledger_name` from `ledger` where `id` = '".$LedgerID."'";
				$res = $this->m_dbConn->select($sql);
				if($res <> "")
				{
					$openingBalance['LedgerName'] = $res[0]['ledger_name'];		
				}	
			}
				//print_r($openingBalance);		
			return $openingBalance;
			
		}
		
		
		
		public function getOpeningBalanceOfCategory($CategoryID,$date , $isGroupCall = false)
		{
			$openingBalance = array("CategoryName" => "","Credit" => 0 ,"Debit" => 0 ,"Total" => 0,"OpeningType" => 0);
			
			if($isGroupCall == false)
			{
				$arParentDetails = $this->getParentOfCategory($CategoryID);
			}
			if(!(empty($arParentDetails)) || $isGroupCall == true)
			{
				$CategoryGroupID = $arParentDetails['group'];
				if($isGroupCall == true)
				{
					$CategoryGroupID = $CategoryID;		
				}
								
				if($CategoryGroupID == LIABILITY)
				{
					$sqlLiability = "SELECT SUM(Credit) as Credit,
									SUM(Debit) as Debit,(SUM(Credit) - SUM(Debit)) as Total ,CategoryID,SubCategoryID
									FROM `liabilityregister` ";
					if($isGroupCall == false)
					{				
						$sqlLiability .= "  where SubCategoryID = '".$CategoryID."'  ";	
					}
					else
					{
						$sqlLiability .= "  where CategoryID = '".$CategoryID."'  ";			
					}
					
					$sqlLiability .= "  and  Date < '".getDBFormatDate($date)."' ";	
					$result = $this->m_dbConn->select($sqlLiability);
					$openingBalance['Credit'] = $result[0]['Credit'];
					$openingBalance['Debit'] = $result[0]['Debit'];
					$openingBalance['Total'] = $result[0]['Total'];
					
					
					if($openingBalance['Total'] < 0)
					{
						$openingBalance['OpeningType'] = TRANSACTION_DEBIT;	
						$openingBalance['Total'] = abs($openingBalance['Total']);	
					}
					else
					{
						$openingBalance['OpeningType'] = TRANSACTION_CREDIT;		
					}
					
				}
				else if($CategoryGroupID == ASSET && ($CategoryID == BANK_ACCOUNT || $CategoryID == CASH_ACCOUNT))
				{
					//do nothing not required now
				}
				else if($CategoryGroupID == ASSET)
				{
					$sqlAsset = "SELECT SUM(Credit) as Credit,
								SUM(Debit) as Debit,(SUM(Debit) - SUM(Credit)) as Total,CategoryID,SubCategoryID 
								FROM `assetregister`  ";
								
					if($isGroupCall == false)
					{				
						$sqlAsset .= "  where SubCategoryID = '".$CategoryID."'  ";	
					}
					else
					{
						$sqlAsset .= "  where CategoryID = '".$CategoryID."'  ";			
					}
					
					$sqlAsset .= "  and  Date < '".getDBFormatDate($date)."' ";	
								
					$result = $this->m_dbConn->select($sqlAsset);
					$openingBalance['Credit'] = $result[0]['Credit'];
					$openingBalance['Debit'] = $result[0]['Debit'];
					$openingBalance['Total'] = $result[0]['Total'];
					
					if($openingBalance['Total'] < 0)
					{
						$openingBalance['OpeningType'] = TRANSACTION_CREDIT;
						$openingBalance['Total'] = abs($openingBalance['Total']);			
					}
					else
					{
						$openingBalance['OpeningType'] = TRANSACTION_DEBIT;	
					}
				}
				else if($CategoryGroupID == INCOME)
				{
					$sqlIncome = "SELECT SUM(Credit) as Credit,
								SUM(Debit) as Debit,(SUM(Credit) - SUM(Debit)) as Total 
								FROM `incomeregister`  ";
								
					$sqlIncome .= "  where  Date < '".getDBFormatDate($date)."' ";	
					
					
					$result = $this->m_dbConn->select($sqlIncome);
					$openingBalance['Credit'] = $result[0]['Credit'];
					$openingBalance['Debit'] = $result[0]['Debit'];
					$openingBalance['Total'] = $result[0]['Total'];
					
				}
				else if($CategoryGroupID == EXPENSE)
				{
					$sqlExpense = "SELECT SUM(Credit) as Credit,
								SUM(Debit) as Debit,(SUM(Debit) - SUM(Credit)) as Total
								FROM `expenseregister`  ";
								
					$sqlExpense .= "  where  Date < '".getDBFormatDate($date)."' ";	
					
					$result = $this->m_dbConn->select($sqlExpense);
					$openingBalance['Credit'] = $result[0]['Credit'];
					$openingBalance['Debit'] = $result[0]['Debit'];
					$openingBalance['Total'] = $result[0]['Total'];
					
				}
			}
									
			return $openingBalance;
			
		}
		
		
		public function getPreviousYearEndingDate($CurrentYearID)
		{
			//$prevYearID = $CurrentYearID ;
			$prevYearID = $CurrentYearID - 1;
		  $getPerod = "select yeartbl.EndingDate from `period` as periodtbl JOIN `society` as societytbl on periodtbl.Billing_cycle = societytbl.bill_cycle  JOIN `year` as yeartbl on yeartbl.YearID = periodtbl.YearID where societytbl.society_id = ".$_SESSION['society_id']." and  yeartbl.YearID = ".$prevYearID." ";
			
			$period = $this->m_dbConn->select($getPerod);
			$EndingDate =	$period[0]['EndingDate'];
			//echo "End Date :".$EndingDate;
			return $EndingDate;
		}
		
		public function getBeginningAndEndingDate($YearID)
		{
			//$prevYearID = $CurrentYearID - 1;
			$getPerod = "select yeartbl.BeginingDate,yeartbl.EndingDate from `society` as societytbl  JOIN `year` as yeartbl where societytbl.society_id = ".$_SESSION['society_id']." and  yeartbl.YearID = ".$YearID." ";
			$period = $this->m_dbConn->select($getPerod);
			$EndingDate =	$period[0];
			return $EndingDate;
		}
		
		public function GetDateByOffset($myDate, $Offset)
		{
			$datetime1 = new DateTime($myDate);
			$newDate = $datetime1->modify($Offset . ' day');
			return $newDate->format('Y-m-d');	
		}
		
		
		public function getDueAmount($unitID)
		{
			$sql = "SELECT SUM(`Debit`) as Debit , SUM(`Credit`) as Credit, (SUM(Debit) - SUM(Credit)) as Total FROM `assetregister` WHERE `LedgerID` = '".$unitID."'  ";
			if(isset($_SESSION['default_year_start_date']) && $_SESSION['default_year_start_date'] <> 0  && isset($_SESSION['default_year_end_date']) && $_SESSION['default_year_end_date'] <> 0)
			{
				//$sql .= "  and Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";	
				$sql .= "  and Date <= '".getDBFormatDate($_SESSION['default_year_end_date'])."'";									
			}
			$sql .= " GROUP BY LedgerID ";	
			
			
			$details = $this->m_dbConn->select($sql);
			//$details[0]['Total'] = 0;
			/*$OpeningBalance = $this->getOpeningBalance($unitID , getDBFormatDate($_SESSION['default_year_start_date']));
				
			if($OpeningBalance <> "")
			{
				if($OpeningBalance['OpeningType'] == TRANSACTION_CREDIT)
				{
					$details[0]['Credit'] = $details[0]['Credit'] + $OpeningBalance['Credit']; 		
				}
				else if($OpeningBalance['OpeningType'] == TRANSACTION_DEBIT)
				{
					$details[0]['Debit'] = $details[0]['Debit'] + $OpeningBalance['Debit']; 		
				}
				$details[0]['Total'] =  $details[0]['Debit'] - $details[0]['Credit'];
			}*/
			return $details[0]['Total'];	
		}
		public function getDueAmountTillDate($unitID)
		{
			$sql = "SELECT SUM(`Debit`) as Debit , SUM(`Credit`) as Credit, (SUM(Debit) - SUM(Credit)) as Total FROM `assetregister` WHERE `LedgerID` = '".$unitID."'  ";
			
			$sql .= " GROUP BY LedgerID ";	
			
			//echo "<br>due qry:".$sql;

			//$sql = "SELECT * FROM  `commitee` ";		
			//print_r($this->m_dbConn);
			$details = $this->m_dbConn->select($sql);
			//print_r($details);
			//$details[0]['Total'] = 0;
			/*$OpeningBalance = $this->getOpeningBalance($unitID , getDBFormatDate($_SESSION['default_year_start_date']));
				
			if($OpeningBalance <> "")
			{
				if($OpeningBalance['OpeningType'] == TRANSACTION_CREDIT)
				{
					$details[0]['Credit'] = $details[0]['Credit'] + $OpeningBalance['Credit']; 		
				}
				else if($OpeningBalance['OpeningType'] == TRANSACTION_DEBIT)
				{
					$details[0]['Debit'] = $details[0]['Debit'] + $OpeningBalance['Debit']; 		
				}
				$details[0]['Total'] =  $details[0]['Debit'] - $details[0]['Credit'];
			}*/
			//echo "due:".$details[0]['Total'];
			return $details[0]['Total'];	
		}
		public function getDueAmountByBillType($UnitID,$BillType)
		{
			$sql = "SELECT TotalBillPayable FROM `billdetails` WHERE `UnitID` = '".$UnitID."' and `BillType`='".$BillType."' order by ID desc limit 0,1 ";
			
			//echo "<br>due qry:".$sql;

			//$sql = "SELECT * FROM  `commitee` ";		
			//print_r($this->m_dbConn);
			$details = $this->m_dbConn->select($sql);
			//print_r($details);
			//$details[0]['Total'] = 0;
			/*$OpeningBalance = $this->getOpeningBalance($unitID , getDBFormatDate($_SESSION['default_year_start_date']));
				
			if($OpeningBalance <> "")
			{
				if($OpeningBalance['OpeningType'] == TRANSACTION_CREDIT)
				{
					$details[0]['Credit'] = $details[0]['Credit'] + $OpeningBalance['Credit']; 		
				}
				else if($OpeningBalance['OpeningType'] == TRANSACTION_DEBIT)
				{
					$details[0]['Debit'] = $details[0]['Debit'] + $OpeningBalance['Debit']; 		
				}
				$details[0]['Total'] =  $details[0]['Debit'] - $details[0]['Credit'];
			}*/
			//echo "due:".$details[0]['Total'];
			return $details[0]['TotalBillPayable'];	
		}
		
		public function displayFormatBillFor($BillFor)
		{
			$tmpString = '';
			$monthsArrayI = array('April','May','June','July','August','September','October','November','December');
			$monthsArrayII = array('January','February','March');
			
			$tmpArray = explode(' ', $BillFor);
			
			//explode period description
			$tmpArray2 = explode('-', $tmpArray[0]);
			$tmpArray3 = explode('-', $tmpArray[1]);
			
			//explode year description
			$tmpArray3[1] =  substr_replace($tmpArray[1], '', 2, -2);
			$tmpArray3[0] =  substr_replace($tmpArray[1], '', 4, strlen($tmpArray[1]));
						
			for($i = 0 ;$i < sizeof($tmpArray2); $i++)
			{
				if($tmpString <> "")
				{
					$tmpString .= " - ";		
				}
				if (in_array($tmpArray2[$i], $monthsArrayI, true)) 
				{
					//echo "found I\n";
					if($tmpString == "")
					{
						$tmpString .= $tmpArray2[$i] ." ". $tmpArray3[0];	
					}
					else
					{
						$tmpString .= " ".$tmpArray2[$i] ." ". $tmpArray3[0];
					}
				}
				else if (in_array($tmpArray2[$i], $monthsArrayII, true))  
				{
					//echo "found II\n";
					if($tmpString == "")
					{
						$tmpString .= $tmpArray2[$i] ." ". $tmpArray3[1];	
					}
					else
					{
						$tmpString .= " ".$tmpArray2[$i] ." ". $tmpArray3[1];
					}
				}
			}
			
			return $tmpString;
		}
		
		public function getPeriodBeginAndEndDate($PeriodID)
		{
			$aryDate = array();
			
			$sql = "select BeginingDate, EndingDate	from period where ID = '" . $PeriodID . "'";
			$res = $this->m_dbConn->select($sql);
			if($res <> '')
			{
				$aryDate['BeginDate'] = $res[0]['BeginingDate'];
				$aryDate['EndDate'] = $res[0]['EndingDate'];
			}
			
			return $aryDate;
		}
		
		public function convert_number_to_words($number) 
		{
			$number = str_replace(',', '', $number);
		   	$hyphen      = '-';
			$conjunction = ' ';
			$separator   = '  ';
			$negative    = 'negative ';
			$decimal     = ' and ';
			$dictionary  = array(
				0                   => 'Zero',
				1                   => 'One',
				2                   => 'Two',
				3                   => 'Three',
				4                   => 'Four',
				5                   => 'Five',
				6                   => 'Six',
				7                   => 'Seven',
				8                   => 'Eight',
				9                   => 'Nine',
				10                  => 'Ten',
				11                  => 'Eleven',
				12                  => 'Twelve',
				13                  => 'Thirteen',
				14                  => 'Fourteen',
				15                  => 'Fifteen',
				16                  => 'Sixteen',
				17                  => 'Seventeen',
				18                  => 'Eighteen',
				19                  => 'Nineteen',
				20                  => 'Twenty',
				30                  => 'Thirty',
				40                  => 'Fourty',
				50                  => 'Fifty',
				60                  => 'Sixty',
				70                  => 'Seventy',
				80                  => 'Eighty',
				90                  => 'Ninety',
				100                 => 'Hundred',
				1000                => 'Thousand',
				100000             => 'Lakh',
	        	10000000          => 'Crore'
			);
		   
			if (!is_numeric($number)) {
				return false;
			}
		   
			if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
				// overflow
				trigger_error(
					'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
					E_USER_WARNING
				);
				return false;
			}
		
			if ($number < 0) {
				//return $negative . convert_number_to_words(abs($number));
				return  $this->convert_number_to_words(abs($number)) . ' Credit';
			}
		   
			$string = $fraction = null;
		   
			if (strpos($number, '.') !== false) {
				list($number, $fraction) = explode('.', $number);
			}
		   
			switch (true) {
				case $number < 21:
					$string = $dictionary[$number];
					break;
				case $number < 100:
					$tens   = ((int) ($number / 10)) * 10;
					$units  = $number % 10;
					$string = $dictionary[$tens];
					if ($units) {
						$string .= $hyphen . $dictionary[$units];
					}
					break;
				case $number < 1000:
					$hundreds  = $number / 100;
					$remainder = $number % 100;
					$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
					if ($remainder) {
						$string .= $conjunction . $this->convert_number_to_words($remainder);
					}
					break;
				  case $number < 100000:
					$thousands   = ((int) ($number / 1000));
					$remainder = $number % 1000;
		
					$thousands = $this->convert_number_to_words($thousands);
		
					$string .= $thousands . ' ' . $dictionary[1000];
					if ($remainder) {
						$string .= $separator .$this->convert_number_to_words($remainder);
					}
					break;
				case $number < 10000000:
					$lakhs   = ((int) ($number / 100000));
					$remainder = $number % 100000;
		
					$lakhs = $this->convert_number_to_words($lakhs);
		
					$string = $lakhs . ' ' . $dictionary[100000];
					if ($remainder) {
						$string .= $separator .$this->convert_number_to_words($remainder);
					}
					break;
				case $number < 1000000000:
					$crores   = ((int) ($number / 10000000));
					$remainder = $number % 10000000;
		
					$crores =$this->convert_number_to_words($crores);
		
					$string = $crores . ' ' . $dictionary[10000000];
					if ($remainder) {
						$string .= $separator . $this->convert_number_to_words($remainder);
					}
					break;	
				default:
					$baseUnit = pow(1000, floor(log($number, 1000)));
					$numBaseUnits = (int) ($number / $baseUnit);
					$remainder = $number % $baseUnit;
					$string = $this->convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
					if ($remainder) {
						$string .= $remainder < 100 ? $conjunction : $separator;
						$string .= $this->convert_number_to_words($remainder);
					}
					break;
			}
		   
			if (null !== $fraction && is_numeric($fraction)) 
			{
				
				$words = array();
				if((int)$fraction == 0)
				{
					//$string .= " Zero";
				}
				else
				{
					$string .= $decimal;
					/*foreach (str_split((string) $fraction) as $number) 
					{
						$words[] = $dictionary[$number];
					}*/
					
					$words[]=  $this->convert_number_to_words((int)$fraction);
					$string .= implode(' ', $words);
					$string .= " Paise ";
				}
			}
		   
			return $string;
		}
		
		public  function getMemberIDs($date)
		{
			$keys = array(0);
			
			//$sql = "SELECT member_id, owner_name, ownership_date FROM ( SELECT member_id, unit, owner_name, ownership_date FROM member_main where ownership_date <= '".$date."' ORDER BY ownership_date DESC ) AS t1 GROUP BY unit";
			//$res = $this->m_dbConn->select($sql);	
			
			$res = $this->getUnitData(0,$date);
			if(sizeof($res) > 0)
			{
				/*for($i = 0;$i < sizeof($res);$i++)
				{
					array_push($keys,$res[$i]['member_id']);	
				}	*/
				foreach($res as  $k => $v)
				{
					array_push($keys,$res[$k]['member_id']);		
				}
			}
			$strKeys = implode(',', $keys);
			return $strKeys;
		}
		
		public  function getUnitData($uid = 0,$date)
		{
			$data = array();
			
			$sqlII = "SELECT member_id, unit, owner_name, ownership_date FROM member_main where ownership_date <= '".$date."' ";
			if($uid > 0)
			{
				$sqlII .= " and   unit = '".$uid."'  ";	
			}
			$sqlII .= "  ORDER BY ownership_date DESC";
			
			$sql = "SELECT member_id, owner_name, ownership_date,unit FROM (".$sqlII.") AS t1 ";
			if($uid > 0)
			{
				$sql .= "where  unit = '".$uid."'  ";	
			}
			$sql .= "  GROUP BY unit";
			$res = $this->m_dbConn->select($sql);	
			
			
			if(sizeof($res) > 0)
			{
				for($i = 0;$i < sizeof($res);$i++)
				{
					$data[$res[$i]['unit']]['member_id'] =$res[$i]['member_id'] ;
					$data[$res[$i]['unit']]['owner_name'] =$res[$i]['owner_name'] ;
					$data[$res[$i]['unit']]['ownership_date'] =$res[$i]['ownership_date'] ;
				}
			}
			return $data;
		}
		
		public function getSocietyAddress()
		{
			$sHeader = "";
			$sql = "SELECT society_add from  `society`";
			$res = $this->m_dbConn->select($sql);
			return  $res; 
		}
		public function getSocietyDetails()
		{
			$sHeader = "";
			$sql = "SELECT * from  `society` where `society_id` = '".$_SESSION['society_id']."' ";
			$res = $this->m_dbConn->select($sql);
			$sHeader .= '<center><div style="text-align:center;"  class="PrintClass"><br><br> <div  style="font-weight:bold; font-size:18px;" class="PrintClass">'.$res[0]['society_name'] .'</div>';
		   
		    if($res[0]['registration_no'] <> "")
		    {
				$sHeader .= '<div style="font-size:14px;"  class="PrintClass">'.$res[0]['registration_no'] .'</div>';
		    }
			$sHeader .= '<div style="font-size:14px;"  class="PrintClass">'.$res[0]['society_add'].'</div></div><br></center>';
			return  addcslashes($sHeader,"\\\'\"\n\r"); 
		}
		public function getSMSTemplateString($MobileNumber, $SMSBody,$client_id=0)
		{
			$sql_query = "select `sms_userid`,`sms_key`,`sms_senderid` from `client` where `id` = '" . $client_id. "'";
			$sms_data = $this->m_dbConnRoot->select($sql_query);
			$smsTemplate ='<?xml version="1.0"?>
								<parent>
								<child>
								<user>'.$sms_data[0]['sms_userid'].'</user>
								<key>'.$sms_data[0]['sms_key'].'</key>
								<mobile>+91'.$MobileNumber.'</mobile>
								<message>'.$SMSBody.'</message>
								<senderid>'.$sms_data[0]['sms_senderid'].'</senderid>
								<accusage>1</accusage>
								
								</child>						
								</parent>';	
			return $smsTemplate;					
		}
		public function GetChildTags($arMobileNumber)
		{
			
											
								
		}
		
		public function SendSMS($mobileNumber, $SMSBody,$client_id = 0)
		{
			$sql_query = "select `sms_userid`,`sms_key`,`sms_domain` from `client` where `id` = '" . $client_id. "'";
			$sms_data = $this->m_dbConnRoot->select($sql_query);
			$Template = $this->getSMSTemplateString($mobileNumber, $SMSBody,$client_id);
			$URL = $sms_data[0]['sms_domain']."submitsms.jsp?";
			$xml = simplexml_load_string($Template);
			$response = "";
			if($Template <> "" && $xml->child->user <> "" && $xml->child->key <> "")
			{
				$ch = curl_init($URL);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
				curl_setopt($ch, CURLOPT_POSTFIELDS, "$Template");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$response = curl_exec($ch);
				curl_close($ch);
				
				
					$sql_query2="update `client` set `sms_counter`= (`sms_counter` + 1) where `id`='".$client_id. "'";
					$this->m_dbConnRoot->update($sql_query2);
				//}
			}
			else
			{
				$response = "Invalid or empty credentials User: ".$xml->child->user." key : ".$xml->child->key;
			}
			return $response;
		}
	
		public function GetSMSDeliveryReport($MessageID,$client_id=0)
		{
			if($client_id <> 0)
			{
				$sql_query = "select `sms_userid`,`sms_key`,`sms_domain` from `client` where `id` = '" .$client_id. "'";
				$sms_data = $this->m_dbConnRoot->select($sql_query);
				
			
				$data	= '<?xml version="1.0"?>
							<parent>
							<child>
							<user>'.$sms_data[0]['sms_userid'].'</user>
							<key>'.$sms_data[0]['sms_key'].'</key>
							<messageid>'.$MessageID.'</messageid>
							</child>	
							</parent>';
				
				$xml = simplexml_load_string($data);
				$response = "";
			
				if($data <> "" && $xml->child->user <> "" && $xml->child->key <> "")
				{			
					$url = $sms_data[0]['sms_domain'].'getreport.jsp?userid='.$xml->child->user.'&key='.$xml->child->key.'&messageid='.$MessageID.'';
					$ch = curl_init($url);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$response = curl_exec($ch);
					curl_close($ch);
				}
				else
				{
					$response = "Invalid or empty credentials User: ".$xml->child->user." key : ".$xml->child->key;
				}
			}
			return $response;
		}
	
		public function getYearIDFromDates($date1,$date2) // date forma must be tdd-mm-yy
		{
			$sql = "SELECT * FROM `year` where (`BeginingDate` BETWEEN '".$date1."' AND '".$date2."') OR (`EndingDate` BETWEEN '".$date1."' AND '".$date2."')";
			$res = $this->m_dbConn->select($sql);
			
			if(sizeof($res) > 0 )
			{
				//fetch previous year start and end date
				$sqlPrev = "SELECT `BeginingDate`,`EndingDate` FROM `year` where `YearID` = '".$res[0]['PrevYearID']."' ";
				$resPrev = $this->m_dbConn->select($sqlPrev);
				if(sizeof($resPrev) > 0 )
				{
					$res[0]['BeginingDatePrevYear'] = $resPrev[0]['BeginingDate'];
					$res[0]['EndingDatePrevYear'] = $resPrev[0]['EndingDate'];
				}
				
			}
			return $res;	
		}
	
	
		public function IsCurrentYearAndCreationYrMatch()
		{
			if(($_SESSION['society_creation_yearid'] <> $_SESSION['default_year']) || $_SESSION['is_year_freeze'] == 1)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		
		public function getBillnReceiptsCollection($iUnitId = 0,$IsSupplementary,$IsRegularBill,$IsFetchReceipts,$bFetchReverseCharges = false, $bFetchJournal = true, $bFetchOpening= true)
		{
				$BillType = 0;
				$finalArray = array();
				$LastBillDate="0000-00-00";
				//echo $iUnitId;
				if($bFetchOpening = true)
				{ 
					$sql10= "select DATE_FORMAT(Date,'%d-%m-%Y') as Date, Date as sdate,VoucherID,Debit,Credit from assetregister where LedgerID = '".$iUnitId."' AND Is_Opening_Balance=1";
					$openres = $this->m_dbConn->select($sql10);
					//$OpBalPeriodID = $this->getOpeningBalancePeriodID();
					//echo "<BR> opening balnce period id (" . $OpBalPeriodID . ")<BR>";
					$openres[0]['PeriodID']=2; //why period id = 2 hardcoded?? its actually 1
					$openres[0]['mode'] = "Opening";
					$openres[0]['Amount'] = -$openres[0]['Credit'] + $openres[0]['debit'];
					array_push($finalArray, $openres[0]);
					/*echo "<pre>";
					print_r($finalArray);
					echo "</pre>";*/
				}
				if($IsRegularBill == true)		
				{	
					$sSqlBills =  "Select  bill.BillNumber as VoucherNo,DATE_FORMAT(billreg.BillDate,'%d-%m-%Y') as Date,DATE_FORMAT(billreg.DueDate,'%d-%m-%Y') as DueDate,DATE_FORMAT(billreg.BillDate,'%Y-%m-%d') as sdate,bill.UnitID as UnitID,(bill.`BillSubTotal` + bill.`AdjustmentCredit`  + bill.`BillInterest`+ bill.`IGST`+ bill.`CGST`+ bill.`SGST`+ bill.`CESS`) as Amount,bill.BillType,bill.PeriodID from billdetails as bill JOIN period as period ON bill.PeriodID = period.id JOIN year as yr ON yr.YearID=period.YearID JOIN billregister as billreg ON bill.PeriodID = billreg.PeriodID where bill.UnitID= '" . $iUnitId  . "' AND bill.BillNumber <> 0 ";
					
					$sSqlBills .= " and bill.BillType =0 ";
					$sSqlBills .= " group by bill.PeriodID";
					
					$resBills = $this->m_dbConn->select($sSqlBills);	
					if(sizeof($resBills) > 0)
					{
						for($bill = 0;$bill < sizeof($resBills);$bill ++)
						{
							$resBills[$bill] = array(" " => "") + $resBills[$bill];
							$resBills[$bill]["ChequeNumber"] = 'Bill';
							//$resBills[$bill][" "] = "";
							$resBills[$bill]["mode"] = 'Bill';
							//$finalArray[$bill] = $resBills[$bill];
							array_push($finalArray,$resBills[$bill]);
						}
						
						$LastBillDate=$resBills[sizeof($resBills)-1]["Date"];
					}
				}
				
				
				if($IsSupplementary == true)		
				{	
					$sSqlBills =  "Select  bill.BillNumber as VoucherNo,DATE_FORMAT(billreg.BillDate,'%d-%m-%Y') as Date,DATE_FORMAT(billreg.DueDate,'%d-%m-%Y') as DueDate,DATE_FORMAT(billreg.BillDate,'%Y-%m-%d') as sdate,bill.UnitID as UnitID,(bill.`BillSubTotal` + bill.`AdjustmentCredit`  + bill.`BillInterest`+ bill.`IGST`+ bill.`CGST`+ bill.`SGST`+ bill.`CESS`) as Amount,bill.BillType,bill.PeriodID from billdetails as bill JOIN period as period ON bill.PeriodID = period.id JOIN year as yr ON yr.YearID=period.YearID JOIN billregister as billreg ON bill.PeriodID = billreg.PeriodID where bill.UnitID= '" . $iUnitId . "'  ";
					
					$sSqlBills .= " and bill.BillType =1 ";
					$sSqlBills .= " group by bill.PeriodID";
					
					$resBillsSupp = $this->m_dbConn->select($sSqlBills);	
					
					if(sizeof($resBillsSupp) > 0)
					{
						for($bill = 0;$bill < sizeof($resBillsSupp);$bill ++)
						{
							$resBillsSupp[$bill] = array(" " => "") + $resBillsSupp[$bill];
							$resBillsSupp[$bill]["ChequeNumber"] = 'Bill';
							//$resBillsSupp[$bill][" "] = "";
							$resBillsSupp[$bill]["mode"] = 'Bill';
							//$finalArray[$bill] = $resBillsSupp[$bill];
							array_push($finalArray,$resBillsSupp[$bill]);
						}
						$LastBillDate=$resBillsSupp[sizeof($resBillsSupp)-1]["Date"];
					}
					
				}
				$LastBillDate = getDBFormatDate($LastBillDate);
				//fetch receipts
				if($IsFetchReceipts == true)
				{
					$sSqlReceipts =  "select vch.VoucherNo as VoucherNo,DATE_FORMAT(ChequeDate,'%d-%m-%Y') as Date,DATE_FORMAT(ChequeDate,'%Y-%m-%d') as sdate,PaidBy as UnitID,Amount,BillType,ChequeNumber,PayerBank,periodtbl.ID as PeriodID from chequeentrydetails JOIN `voucher` as vch on chequeentrydetails.ID = vch.RefNo  JOIN `period` as periodtbl on chequeentrydetails.voucherdate >= periodtbl.BeginingDate and  chequeentrydetails.voucherdate <= periodtbl.EndingDate where PaidBy='" .$iUnitId. "' ";
					
					
					if($IsSupplementary ==false || $IsRegularBill == false)
					{
						if($IsRegularBill == true)
						{
							$sSqlReceipts .= " and BillType = 0 ";
						}
						else if($IsSupplementary == true)
						{
							$sSqlReceipts .= " and BillType =1 ";
						}
					}
					$sSqlReceipts .= "  and  vch.RefTableID = ".TABLE_CHEQUE_DETAILS." group by  vch.VoucherNo order by VoucherDate DESC";
					
					$resReceipts = $this->m_dbConn->select($sSqlReceipts);
					if(sizeof($resReceipts) > 0)
					{
						for($Receipt = 0;$Receipt < sizeof($resReceipts);$Receipt ++)
						{
							$resReceipts[$Receipt] = array(" " => "") + $resReceipts[$Receipt];
							//$resReceipts[$Receipt][" "] = "";
							$resReceipts[$Receipt]['mode'] = 'Receipt';
							//$finalArray[sizeof($finalArray) -1] = $resReceipts[$Receipt];
							array_push($finalArray,$resReceipts[$Receipt]);
							//array_push($comArray,$resReceipts[$Receipt]);
						}
					}
				}
				
				
				if($bFetchReverseCharges == true)
				{
					$ledgername_array=array();
					
					//echo $sql = "SELECT *,max(PeriodID) as PeriodID FROM `billdetails` where  `UnitID` = '" . $iUnitId."'";
					//$res =$this->m_dbConn->select($sql);
					
					$sqlCheck = "select DATE_FORMAT(Date,'%d-%m-%Y') as Date,DATE_FORMAT(Date,'%Y-%m-%d') as sdate,Amount,LedgerID from `reversal_credits` where Date >= '".$LastBillDate. "' AND `UnitID` = '" . $iUnitId."' ";
					//echo "reverse :".$sqlCheck;
					$resultCheck = $this->m_dbConn->select($sqlCheck);
					//echo gettype($resultCheck);
					if(isset($resultCheck))
					{
						$get_ledger_name="select id,ledger_name from `ledger`";
						$result02=$this->m_dbConn->select($get_ledger_name);
					
						for($i = 0; $i < sizeof($result02); $i++)
						{
							$ledgername_array[$result02[$i]['id']]=$result02[$i]['ledger_name'];
					
						}
						for($i = 0; $i < sizeof($resultCheck); $i++)
						{
							$resultCheck[$i]['mode'] = 'ReverseCharge';
							$resultCheck[$i]['PaidTo'] = $ledgername_array[$resultCheck[$i]['LedgerID']];
							array_push($finalArray,$resultCheck[$i]);
						}	
					}
				}

				if($bFetchJournal == true)
				{
					$ledgername_array=array();
					
					//echo $sql = "SELECT *,max(PeriodID) as PeriodID FROM `billdetails` where  `UnitID` = '" . $iUnitId."'";
					//$res =$this->m_dbConn->select($sql);
					
					$sqlCheck = "select DATE_FORMAT(Date,'%d-%m-%Y') as Date,DATE_FORMAT(Date,'%Y-%m-%d') as sdate,Credit,Debit,LedgerID,VoucherID from `assetregister` where `VoucherTypeID`=5 AND `LedgerID` = '" . $iUnitId."' ";
					$resultCheck = $this->m_dbConn->select($sqlCheck);
					//echo gettype($resultCheck);
					if(isset($resultCheck))
					{
						$vid=$resultCheck[0]['VoucherID']; 
						$sql1 = "select `desc` from `vouchertype` where id=5";		
						$data1 = $this->m_dbConn->select($sql1);
						
						$voucher = $data1[0]['desc'];	
						$sql2 = "select RefNo,RefTableID,VoucherNo from `voucher` where id='".$vid."' ";
						//echo $sql2;
						$data2 = $this->m_dbConn->select($sql2);
						if(isset($data2))
						{
							$RefNo = $data2[0]['RefNo'];
							$RefTableID = $data2[0]['RefTableID'];
							$VoucherNo = $data2[0]['VoucherNo']; 
							if($voucher == "Journal Voucher")
							{
								$get_ledger_name= "select ledgertbl.id,`ledger_name`,vouchertbl.Note,vouchertbl.RefNo,vouchertbl.VoucherNo,vouchertbl.By as 'To' from `voucher` as vouchertbl JOIN `ledger` as ledgertbl on vouchertbl.By=ledgertbl.id where vouchertbl.RefNo='".$RefNo."' AND vouchertbl.RefTableID='".$RefTableID."' and vouchertbl.VoucherNo='".$VoucherNo."'";
								$result02=$this->m_dbConn->select($get_ledger_name);
							}
							if(isset($result02))
							{
								for($i = 0; $i < sizeof($result02); $i++)
								{
									$ledgername_array[$result02[$i]['id']]=$result02[$i]['ledger_name'];
					
								}
								for($i = 0; $i < sizeof($resultCheck); $i++)
								{
									$resultCheck[$i]['mode'] = 'Journal';
									$resultCheck[$i]['PaidTo'] = $ledgername_array[$result02[$i]['id']];
									$resultCheck[$i]['VoucherNo']=$voucher;
									array_push($finalArray,$resultCheck[$i]);
									//array_push($comArray,$resultCheck[$i]);
								}
							}
						}	
					}		
				}

			/*$sql6 = "select opening_balance from ledger where id = '". $iUnitId ."' ";
			$blk = $this->m_dbConn->select($sql6);
			//echo "<pre>";
			//print_r($blk);
			//echo"</pre>";
			array_push($comArray, $blk);*/
			
			//print_r($p);


			/*echo "<pre>";
			print_r($finalArray);
			echo "</pre>";*/


			$finalArray  = $this->sortArray($finalArray,"sdate");
			/*echo "<pre>";
			print_r($finalArray);
			echo "</pre>";*/

			return $finalArray;
			
			
		}
		function compare($iUnitId = 0, $IsSupplementary, $IsRegularBill)
		{
			$comArray=array();
			$sql10= "select Date,Debit,Credit from assetregister where LedgerID = '".$iUnitId."' AND Is_Opening_Balance=1";
					$openres = $this->m_dbConn->select($sql10);
					$openres[0]['PeriodID']=2;
					$openres[0]['mode'] = "Opening";
					$openres[0]['Amount'] = $openres[0]['Debit']+$openres[0]['Credit']; 
					
					array_push($comArray, $openres[0]);
			

			$sql7 ="select bill.UnitID as UnitID,(bill.`BillSubTotal` + bill.`AdjustmentCredit`  + bill.`BillInterest`+ bill.`IGST`+ bill.`CGST`+ bill.`SGST`+ bill.`CESS`) as Amount,bill.BillType,bill.PeriodID,billreg.BillDate as Date from billdetails as bill JOIN period as period ON bill.PeriodID = period.id JOIN year as yr ON yr.YearID=period.YearID JOIN billregister as billreg ON bill.PeriodID = billreg.PeriodID where bill.UnitID= '" . $iUnitId  . "' AND bill.BillNumber <> 0 ";
					
				
				$sql7 .= " group by bill.PeriodID"; 
			$block = $this->m_dbConn->select($sql7);
			
			$balanceAmt=0;
			if(sizeof($block) > 0)
			{
				for($i=0;$i < sizeof($block);$i++)
				{
					$block[$i] = array(" " => "") + $block[$i];
					$block[$i]['mode'] = 'Bill';
					//$balanceAmt=$balanceAmt+$block[$i]['Amount'];
					array_push($comArray, $block[$i]);
				}
			}
			

			$sql8 =  "select PaidBy as UnitID,Amount,BillType,ChequeDate as Date,periodtbl.ID as PeriodID from chequeentrydetails JOIN `voucher` as vch on chequeentrydetails.ID = vch.RefNo  JOIN `period` as periodtbl on chequeentrydetails.voucherdate >= periodtbl.BeginingDate and  chequeentrydetails.voucherdate <= periodtbl.EndingDate where PaidBy='" .$iUnitId. "' ";
					
					
					if($IsSupplementary ==false || $IsRegularBill == false)
					{
						if($IsRegularBill == true)
						{
							$sql8 .= " and BillType = 0 ";
						}
						else if($IsSupplementary == true)
						{
							$sql8 .= " and BillType =1 ";
						}
					}
					$sql8 .= "  and  vch.RefTableID = ".TABLE_CHEQUE_DETAILS." group by  vch.VoucherNo order by VoucherDate ASC";
					
			
				$block1 = $this->m_dbConn->select($sql8);
				//print_r($block1);
			if(sizeof($block1) > 0)
			{
				for($i=0;$i < sizeof($block1);$i++)
				{
					$block1[$i] = array(" " => "") + $block1[$i];
					$block1[$i]['mode'] = 'Receipt';
					//$balanceAmt=$balanceAmt+$block[$i]['Amount'];
					array_push($comArray, $block1[$i]);
				}
			}
			$checkArr = array();
			$checkArr[0] = $comArray[0]['Amount'];
			$comArray = $this->sortArray($comArray,"Date");
			for($i=1;$i < sizeof($comArray);$i++)
			{
				if($comArray[$i]['mode']=='Bill')
				{
					$balanceAmt=$balanceAmt+$comArray[$i]['Amount'];
					$checkArr[$i]=$balanceAmt;
				}

				elseif($comArray[$i]['mode']=='Receipt')
				{
					$balanceAmt=$balanceAmt - $comArray[$i]['Amount'];
					$checkArr[$i]=$balanceAmt;
				}
			}
			
			$sql9 = "select Debit,Credit,VoucherID,Date from assetregister where LedgerID = '" . $iUnitId."' ";
			$block2 = $this->m_dbConn->select($sql9);
			$block2 = $this->sortArray($block2,"Date");
			$blockArr = array();
			for($i=0;$i < sizeof($block2);$i++)
			{
				$totalAmt=$totalAmt + $block2[$i]['Debit'] - $block2[$i]['Credit'];
				$blockArr[$i]=$totalAmt;
				
			}

			$p=array();
			for($i=0;$i<sizeof($blockArr);$i++)
			{
				if($checkArr[$i]!=$blockArr[$i])
				{
					$p[$i]=1;

				}
				else
					$p[$i]=0;

			}
			return $p;
		}
		
		//function compareBillAmount($iUnitId, $BillType)
		function FetchBillAmountGroupByPeriod($iUnitId)
		{
			
			 $sql = "Select UnitID, PeriodID, SUM(BillSubTotal + AdjustmentCredit + BillTax + IGST + CGST + SGST + CESS + BillInterest) as CurrentBillAmountCalculated, CurrentBillAmount, SUM(CurrentBillAmount + PrincipalArrears +InterestArrears) as TotalBillPayableCalculated, TotalBillPayable, BillType from `billdetails` WHERE UnitID = '" . $iUnitId."' group by PeriodID, BillType";
	
			$result = $this->m_dbConn->select($sql);
			
			$resultArray = array();
			for($i = 0; $i < sizeof($result); $i++)
			{
				$resultArray[$result[$i]['PeriodID']][$result[$i]['BillType']]['TotalBillPayable']= $result[$i]['TotalBillPayable'];
				$resultArray[$result[$i]['PeriodID']][$result[$i]['BillType']]['TotalBillPayableCalculated'] = $result[$i]['TotalBillPayableCalculated'];
				$resultArray[$result[$i]['PeriodID']][$result[$i]['BillType']]['CurrentBillAmount'] = $result[$i]['CurrentBillAmount'];
				$resultArray[$result[$i]['PeriodID']][$result[$i]['BillType']]['CurrentBillAmountCalculated'] = $result[$i]['CurrentBillAmountCalculated'];
			}
			
			return $resultArray;
		}
		
		/*function compareBillAmountForAdmin($iUnitId)
		{
			
			//echo $sql = "Select UnitID, PeriodID,  TotalBillPayable, BillType from `billdetails` WHERE UnitID = " . $iUnitId;
		$sql = "Select UnitID, PeriodID, SUM(BillSubTotal + AdjustmentCredit + BillTax + IGST + CGST + SGST + CESS + BillInterest) as CurrentBillAmountCalculated, CurrentBillAmount, SUM(CurrentBillAmount + PrincipalArrears +InterestArrears) as TotalBillPayableCalculated, TotalBillPayable, BillType from `billdetails` WHERE UnitID = '" . $iUnitId."' group by PeriodID, BillType";
			
			
			$result = $this->m_dbConn->select($sql);
			
			$resultArray = array();
			for($i = 0; $i < sizeof($result); $i++)
			{
				$resultArray[$result[$i]['PeriodID']][$result[$i]['BillType']]['TotalBillPayable']= $result[$i]['TotalBillPayable'];
				//$resultArray[$result[$i]['PeriodID']][$result[$i]['BillType']]['TotalBillPayable']= $result[$i]['TotalBillPayable'];
				//$resultArray[$result[$i]['PeriodID']][$result[$i]['BillType']]['TotalBillPayableCalculated'] = $result[$i]['TotalBillPayableCalculated'];
				//$resultArray[$result[$i]['PeriodID']][$result[$i]['BillType']]['CurrentBillAmount'] = $result[$i]['CurrentBillAmount'];
				//$resultArray[$result[$i]['PeriodID']][$result[$i]['BillType']]['CurrentBillAmountCalculated'] = $result[$i]['CurrentBillAmountCalculated'];
			}
			//print_r($resultArray);
			return $resultArray;
		}
		*/
		function compareBill($iUnitId)
		{	
			
			
			$sql13 = "select ID,(BillSubTotal + BillInterest + PrincipalArrears + InterestArrears) as Amount,BillType,PeriodID from billdetails where UnitId ='".$iUnitId."'AND BillNumber <> 0 AND BillType = 0 ORDER by PeriodID ASC";
			$regbill = $this->m_dbConn->select($sql13);
			
			$sql14 = "select ID,(BillSubTotal+BillInterest+PrincipalArrears+InterestArrears) as Amount,BillType,PeriodID from billdetails where UnitId ='".$iUnitId."' AND BillType = 1";
			$supbill = $this->m_dbConn->select($sql14);//todo: order by periodid;
			
			$sql15 = "select a.Date,a.Debit,b.RefNo as ID from assetregister as a join Voucher as b on a.VoucherID= b.id where a.LedgerId ='".$iUnitId."' AND a.VoucherTypeID = 1 ORDER by Date"; //todo : use VoucherId; via imp;
			$p=array();
			$j=0;
			$k=0;
			$sales = $this->m_dbConn->select($sql15);
			$pay=0;
			$reg_pay=0;
			$sup_pay=0;
		
			for($i=0;$i<sizeof($sales);$i++)
			{
				if ($sales[$i]['ID']==$regbill[$j]['ID'])
				{
					
					if($pay+$sales[$i]['Debit'] == $regbill[$j]['Amount']+$sup_pay)
					{
						$p[$i]=0;// todo:convert into multi dimensional and use period;
						$pay=$pay+$sales[$i]['Debit'];
						$reg_pay=$reg_pay + $sales[$i]['Debit'];
						
					}
					else
					{
						$p[$i]=1;
					}
					$j=$j+1;

				}
				else if($sales[$i]['ID']==$supbill[$k]['ID'])
				{	
					if($pay+$sales[$i]['Debit'] == $supbill[$k]['Amount']+$reg_pay)
					{
						$p[$i]=0;
						$pay=$pay + $sales[$i]['Debit'];
						$sup_pay=$sup_pay+ $sales[$i]['Debit'];
					
					}
					else
					{
						$p[$i]=1;
					}
					$k=$k+1;
				}
			}

			
			return $p;
		}
		
		function sortArray($array,$inputKey)
		{
			foreach($array as $key=>$value){
				$arr_Ref[$key] = $array[$key][$inputKey];
				}
			array_multisort($arr_Ref, SORT_ASC,$array);	
			return $array;	
		}
		
		function GetEmailHeader()
		{
			$mailText = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
						<html xmlns="http://www.w3.org/1999/xhtml">
						 <head>
						  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />  
						  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
						</head>
						<body style="margin: 0; padding: 0;">					 
							<table align="center" border="1" bordercolor="#CCCCCC" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;">
							   <tr>
								 <td align="center" bgcolor="#D9EDF7" style="padding: 30px 0 20px 0;border-bottom:none;">
								  <img src="http://way2society.com/images/logo.png" alt="Way2Society.com"  style="display: block;" />
								  <br />
								  <i><font color="#43729F" size="4"><b> Way2Society.com - Housing Society Social & Accounting Software </b></font></i>
								 </td>
							   </tr>
							   <tr>
								 <td bgcolor="#ffffff" style="padding-top:20px; padding-bottom:20px; padding-left:10px; padding-right:10px;border-top:none;border-bottom:none;" >
								   <table width="100%">';
			return $mailText;							  	
		}
	
		function GetEmailFooter()
		{
			$mailText = '<tr><td><br /></td></tr>
									<tr>
										<td font="colr:#999999;">Thank You,<br>way2society.com</td>
									</tr>
								   </table>
								 </td>
							   </tr>
							   <tr>
								 <td bgcolor="#CCCCCC" style="padding: 20px 20px 20px 20px;border-top:none;">
								   <table cellpadding="0" cellspacing="0" width="100%">           
									 <td >             
										<a href="http://way2society.com/" target="_blank"><i>Way2Society</i></a>              
									 </td>
									 <td align="center"  style="padding: 0px 50px 0px 1px;">
								 		<table>
                                 		<tr>
                                 		<td><a href="https://play.google.com/store/apps/details?id=com.ionicframework.way2society869487&amp;rdid=com.ionicframework.way2society869487" target="_blank">
										<img src="http://way2society.com/images/app.png" width="120" height="50" style="style=" top:10px;"></a></td></tr>				
										</table>
                                	 </td>
									 <td align="right">
									  <table border="0" cellpadding="0" cellspacing="0">
									   <tr>
										<td>
											<a href="https://twitter.com/way2society" target="_blank"><img src="http://way2society.com/images/icon2.jpg" alt=""></a>                  
										</td>
										<td style="font-size: 0; line-height: 0;" width="20">&nbsp;&nbsp;</td>
										<td>
											<a href="https://www.facebook.com/way2soc" target="_blank"><img src="http://way2society.com/images/icon1.jpg" alt=""></a>                 
										</td>
									   </tr>
									  </table>
									 </td>             
								   </table>
								 </td>
							   </tr>
							 </table>   
						</body>
						</html>';
			return $mailText;					
		}
		
		function GetLedgerDetails($LedgerID = 0)
		{
			$LedgerDetails = array();

			if($LedgerID == 0)
			{
				$sql = "SELECT * from `ledger`";		
			}
			else
			{
				$sql = "SELECT * from `ledger` WHERE `id` = '" . $LedgerID . "'";  
			}

			$result = $this->m_dbConn->select($sql);

			for($iCnt = 0; $iCnt < sizeof($result); $iCnt++)
			{
				$LedgerDetails[$result[$iCnt]['id']]['General'] = $result[$iCnt];
			}

			return $LedgerDetails;
		}

		function GetMemberPersonalDetails($UnitID)
		{
			$sqlQry  =  "select `email`,`mob` from `member_main` where `unit` = '".$UnitID."' AND `ownership_status` = 1";
			return $this->m_dbConn->select($sqlQry);
		}
		function GetUnitNo($UnitID)
		{
			//SELECT u.unit_no, m.primary_owner_name FROM `member_main` m, `unit` u where u.unit_id=m.unit and m.unit=16
			$sqlQry1 = "select u.`unit_no`, m.`primary_owner_name` from `member_main` m, `unit` u where u.`unit_id` = m.`unit` and m.`unit`='".$UnitID."' and m.ownership_status = 1";
			return $this->m_dbConn->select($sqlQry1);
		}
		function GetUnitDetailsByMobileNo($MobileNumber)
		{
			//SELECT u.unit_no, m.primary_owner_name FROM `member_main` m, `unit` u where u.unit_id=m.unit and m.unit=16
			$sqlQry1 = "select u.`unit_id`, u.`unit_no`, m.`primary_owner_name` from `member_main` m, `unit` u where u.`unit_id` = m.`unit` and m.`mob`='".$MobileNumber."'";
			$sqlQry1 = "SELECT mem_other_family.mem_other_family_id as id, mem_other_family.other_email as to_email, mem_other_family.other_mobile as mobile_no, mem_other_family.other_name as to_name, IF(mem_other_family.mem_other_family_id > 0, mem_other_family.coowner, 0) as type, unit.unit_id as unit FROM `mem_other_family` JOIN `member_main` on mem_other_family.member_id = member_main.member_id JOIN `unit` on unit.unit_id = member_main.unit where mem_other_family.send_commu_emails = 1 and member_main.member_id IN (SELECT member_main.`member_id` FROM (select `member_id` from `member_main` where `ownership_date` <= NOW() ORDER BY `ownership_date` desc) as member_id Group BY unit) UNION select tmem.tmember_id as id, tmem.email as to_email,tmem.contact_no as mobile_no, tmem.mem_name as to_name, IF(tmem.tmember_id > 0, 10, 0) as type, tmod.unit_id as unit from tenant_member as tmem JOIN tenant_module as tmod ON tmem.tenant_id = tmod.tenant_id where tmod.end_date > NOW() AND tmem.send_commu_emails=1";
			return $this->m_dbConn->select($sqlQry1);
		}
		function IsUnitExist($UnitID)
		{
			$sqlQry1 = "select unit_id,`unit_no` from unit where `unit_id`='".$UnitID."'";
			
			return $this->m_dbConn->select($sqlQry1);	
		}
		function GetUnitNoForDD()
		{
			$sqlQry2 = "select u.`unit_no`, m.`primary_owner_name` from `member_main` m, `unit` u where u.`unit_id` = m.`unit`";
			return $this->m_dbConn->select($sqlQry2);
		}


		function GetSocietyInformation($SocietyID)
		{
		  $sql = "Select `apply_service_tax`, `service_tax_threshold`, `igst_tax_rate`,`gstin_no`,`cgst_tax_rate`, `sgst_tax_rate`, `cess_tax_rate`, `society_name`, `apply_GST_on_Interest`, `apply_GST_above_Threshold` from society where `society_id` = '" . $SocietyID . "'";
			$result = $this->m_dbConn->select($sql);
			return $result[0];
		}
		function GetPaymentGatewayDetails($SocietyID)
        {
          $sql = "Select `Allow_Payment_Gateway`, `PGSalt`, `PGKey` from society where `society_id` = '" . $SocietyID . "'";
            $result = $this->m_dbConn->select($sql);
            return $result[0];
        }
        function GetPaymentGatewayBankID()
        {
        	$result = $this->m_dbConn->select("select `PGBeneficiaryBank` from society");
        	//echo $result[0]['PGBeneficiaryBank'];
        	return $result[0]['PGBeneficiaryBank'];
        }
		function GetGDriveDetails()
		{
			$sqlSelect = "select GDrive_W2S_ID,GDrive_Credentials,GDrive_UserID from `society`";
			//echo $sqlSelect;
			//print_r($m_dbConn);
			$res = $this->m_dbConn->select($sqlSelect);
			return $res;
		}
		function SetGDriveCredentails($credentials)
		{
			$sqlUpdate = "update society set GDrive_Credentials='".$credentials."'";
			$res = $this->m_dbConn->update($sqlUpdate);
			//echo "updated id:".$res;
		}
		function GetUnitDesc($UnitID)
		{

			$sqlUnit = "select unit_no from `unit` where unit_id='".$UnitID."'";
			$resUnit = $this->m_dbConn->select($sqlUnit);
			//print_r($resUnit);
			return $resUnit;
		}
		function GetDocTypeByID($sDocID)
		{
			$sqlDocName = "select * from `document_type` where ID='".$sDocID."'";
			//echo "sz:".$sqlDocName;
			$resDocName = $this->m_dbConn->select($sqlDocName);
			//echo "sz:".$sizeof($resDocName);
			return $resDocName;	
		}
		public function GetMyLoginDetails()
		{
			$LoginID = $_SESSION["login_id"];
			$sqllogin = "select name, member_id from login where login_id='".$LoginID."'";
			$resLogin = $this->m_dbConnRoot->select($sqllogin);
			
			return $resLogin;		
		}
		public function comboboxEx($query, $bShowPleaseSelect = true)
		{
			$id=0;
			//echo "<script>alert('test')<script>";
			if($bShowPleaseSelect)
			{
				$str.="<option value=''>Please Select</option>";
			}
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
		public function GetListMobileAppUsers()
		{
			$sql = "select m.unit_id,m.desc from device_details as dd JOIN login as lg on  dd.login_id = lg.login_id JOIN mapping as m on lg.login_id = m.login_id where m.society_id = '".$_SESSION['society_id']."'  and dd.device_id !='' group by m.`unit_id` order by m.`society_id`";
			//echo $sql;
			$data = $this->m_dbConnRoot->select($sql);
			//echo "<script>alert('test2')<//script>";
			//print_r($data);
			return $data;
		}

		public function GetBlockedUsersDesc()
		{
			$sql = "select block_desc from unit where unit_id='".$_SESSION["unit_id"]."'";
			
			$data = $this->m_dbConn->select($sql);
			
			return $data;
		}
		function UploadAttachment($arFILES, $doc_type_id,$PostDate, $DestFolderName, $FileIndexCount = "", $bTenantModule = false, $UnitNo = "")
		{
			$docGDriveID = "";
			$arResponse =  array();
			try
			{
				//echo "start";
				//die();
				$fileTempName = $arFILES['userfile'.$FileIndexCount]['tmp_name'];  
				$fileSize = $arFILES['userfile'.$FileIndexCount]['size'];
				$resSociety = $this->GetGDriveDetails();
				$sGDrive_W2S_ID = $resSociety["0"]["GDrive_W2S_ID"];
				if($sGDrive_W2S_ID != "")
				{
					$fileName = basename($arFILES['userfile'.$FileIndexCount]['name']);
				}
				else
				{
					$fileName = time().'_'.basename($arFILES['userfile'.$FileIndexCount]['name']);	
				}
				if($_SERVER['HTTP_HOST']=="localhost")
				{		
					$uploaddir = $_SERVER['DOCUMENT_ROOT']."/beta_aws_12/".$DestFolderName;			   
				}
				else
				{
					$uploaddir = $_SERVER['DOCUMENT_ROOT']."/".$DestFolderName;			   
				}
				$uploadfile = $uploaddir ."/". $fileName;	
				
				if($this->m_bShowTrace)
				{
					echo $uploadfile;	
				}
				
				//die();
				if($this->m_bShowTrace)
				{
					echo "uploading to gdrive:".$uploadfile . " doctype:".$doc_type_id;	
				}
				$mimeType = $arFILES['userfile'.$FileIndexCount]['type'];
				//$documentName = time() . "_" . $arFILES['userfile']['name'] ;
				//$noticeFileName = $documentName;

				//$sqlDocName = "select doc_type from `document_type` where ID='".$doc_type_id."'";
				if($doc_type_id != "Events")
				{
					$resDocName = $this->GetDocTypeByID($doc_type_id);
					$NoticeAlias = $resDocName[0]["doc_type"];
				}
				else
				{
					$NoticeAlias = "Events";
				}

				if($this->m_bShowTrace)
				{
					echo "doctype:".$NoticeAlias;
					echo "doc_type_id:".$doc_type_id;
				}
				//echo "doctype:".$NoticeAlias = $resDocName[0]["doc_type"];
				//echo "doc_type_id:".$doc_type_id;
				//die();
				//$str = "Lease//".$start;
				$bAddDate = false;
				if($bTenantModule)
				{
					if($UnitNo != "")
					{
						if($bAddDate)
						{
							$folderName = $UnitNo . "//". $NoticeAlias . "//".$PostDate; 
						}
						else
						{
							$folderName = $UnitNo . "//". $NoticeAlias;	
						}
					}
					else
					{
						if($bAddDate)
						{
							$folderName = $NoticeAlias . "//".$PostDate; 
						}
						else
						{
							$folderName = $NoticeAlias;
						}
					}
				}
				else
				{
					if($bAddDate)
					{
						$folderName = $NoticeAlias . "//".$PostDate; 
					}
					else
					{
						$folderName = $NoticeAlias;	
					}
				}
				if($this->m_bShowTrace)
				{
					echo "path:".$folderName;
				}
				//$sql = "select GDrive_W2S_ID from `society`";
				//$res = $this->m_dbConn->select($sql);
				//print_r($res);
				//$rootid = $res[0]['GDrive_W2S_ID'];
				
				//echo "GD:".$sGDrive_W2S_ID;
				$sStatus = "";
				$sMode = "";
				$sFile = "";
				//$sUploadingFileName = $fileName;
				
				if($sGDrive_W2S_ID != "")
				{
					$ObjGDrive = new GDrive($this->m_dbConn, "0", $sGDrive_W2S_ID, 0);
					
					//echo "filename:".$noticeFileName ." mime:". $mimeType ." tmpname:". $arFILES['userfile'.$FileIndexCount]['tmp_name'] ." folderName:". $folderName;
					//$mimeType = 'application/vnd.google-apps.file';
					$UploadedFiles = $ObjGDrive->UploadFiles($fileName , $fileName, $mimeType, $arFILES['userfile'.$FileIndexCount]['tmp_name'], $folderName, $folderName, "", "", $sGDrive_W2S_ID, "0");
					
					$sStatus = "1";
					$sMode = "2";
					$sFile = $UploadedFiles["response"]["id"];
					//echo "uploaded:".$sFile;

				}
				else
				{
					if(move_uploaded_file($arFILES['userfile'.$FileIndexCount]['tmp_name'], $uploadfile))
					{
						//$_POST['note'] = $fileName;
						
						$sStatus = "1";
						$sMode = "1";
						$sFile = $fileName;
						$arResponse["note"] = $fileName;
					}
					else
					{
						echo "Error uploading file - check destination is writeable.";
						$sStatus = "0";
						$sMode = "0";
						$sFile = "";
						$arResponse["note"] = "";
						//return "";
					}
				}

				if($bTenantModule)
				{
					$arResponse["doc_name"] = $fileName;
				}
				$arResponse["status"] = $sStatus;
				$arResponse["mode"] = $sMode;
				$arResponse["response"] = $sFile;
				$arResponse["FileName"] = $fileName;
				//die();
				if($this->m_bShowTrace)
				{
					echo "<br>uploadfile:";
					echo "<pre>";
					print_r($UploadedFiles);

					echo "</pre>";
				}
				//$_POST["note"] = $noticeFileName;
				/*if($UploadedFiles["status"] == 1)
				{
					$docGDriveID = $UploadedFiles["response"]["id"];
					echo "file uploaded successfully to gdrive.";
				}
				else
				{
					//$docGDriveID = $UploadedFiles["status"][0][""];
					$docGDriveID = "Error while uploading document.";
				}*/
			}
			catch(Exception $exp)
			 {
				echo "Error occured in uploading document. Details are:".$exp->getMessage();
				die();
			 }
			return $arResponse;
		}
		function UploadAttachmentAndroid($arFILES, $doc_type_id,$PostDate, $DestFolderName, $FileIndexCount = "", $bTenantModule = false, $UnitNo = "")
		{
			$docGDriveID = "";
			$arResponse =  array();
			try
			{
				//echo "start";
				//die();
				$errorfile_name = 'image_upload_errorlog_'.date("d.m.Y").'.html';
				//$this->errorLog = $this->errorfile_name;
				$errorfile = fopen($errorfile_name, "a");
				
				$fileTempName = $arFILES['file'.$FileIndexCount]['tmp_name'];  
				$fileSize = $arFILES['file'.$FileIndexCount]['size'];
				
				$errormsg = "get drive details";
				$msgFormat=$errormsg."\r\n";
				fwrite($errorfile,$msgFormat);
				
				$resSociety = $this->GetGDriveDetails();
				$sGDrive_W2S_ID = $resSociety["0"]["GDrive_W2S_ID"];
				if($sGDrive_W2S_ID != "")
				{
					$fileName = basename($arFILES['file'.$FileIndexCount]['name']);
				}
				else
				{
					$fileName = time().'_'.basename($arFILES['file'.$FileIndexCount]['name']);	
				}
				if($_SERVER['HTTP_HOST']=="localhost")
				{		
					$uploaddir = $_SERVER['DOCUMENT_ROOT']."/beta_aws_12/".$DestFolderName;			   
				}
				else
				{
					$uploaddir = $_SERVER['DOCUMENT_ROOT']."/".$DestFolderName;			   
				}
				$uploadfile = $uploaddir ."/". $fileName;	
				
				if($this->m_bShowTrace)
				{
					echo $uploadfile;	
				}
				
				//die();
				if($this->m_bShowTrace)
				{
					echo "uploading to gdrive:".$uploadfile . " doctype:".$doc_type_id;	
				}
				$mimeType = $arFILES['file'.$FileIndexCount]['type'];
				//$documentName = time() . "_" . $arFILES['userfile']['name'] ;
				//$noticeFileName = $documentName;

				//$sqlDocName = "select doc_type from `document_type` where ID='".$doc_type_id."'";
				$resDocName = $this->GetDocTypeByID($doc_type_id);
				$NoticeAlias = $resDocName[0]["doc_type"];
				$NoticeAlias = "FINE";
				if($this->m_bShowTrace)
				{
					echo "doctype:".$NoticeAlias;
					echo "doc_type_id:".$doc_type_id;
				}
				$errormsg = "type:".$NoticeAlias;
				$msgFormat=$errormsg."\r\n";
				fwrite($errorfile,$msgFormat);
				
				//echo "doctype:".$NoticeAlias = $resDocName[0]["doc_type"];
				//echo "doc_type_id:".$doc_type_id;
				//die();
				//$str = "Lease//".$start;
				$bAddDate = false;
				if($bTenantModule)
				{
					if($UnitNo != "")
					{
						if($bAddDate)
						{
							$folderName = $UnitNo . "//". $NoticeAlias . "//".$PostDate; 
						}
						else
						{
							$folderName = $UnitNo . "//". $NoticeAlias;	
						}
					}
					else
					{
						if($bAddDate)
						{
							$folderName = $NoticeAlias . "//".$PostDate; 
						}
						else
						{
							$folderName = $NoticeAlias;
						}
					}
				}
				else
				{
					if($bAddDate)
					{
						$folderName = $NoticeAlias . "//".$PostDate; 
					}
					else
					{
						$folderName = $NoticeAlias;	
					}
				}
				if($this->m_bShowTrace)
				{
					echo "path:".$folderName;
				}
				//$sql = "select GDrive_W2S_ID from `society`";
				//$res = $this->m_dbConn->select($sql);
				//print_r($res);
				//$rootid = $res[0]['GDrive_W2S_ID'];
				$errormsg = "gdrive ctor".$sGDrive_W2S_ID;
				$msgFormat=$errormsg."\r\n";
				fwrite($errorfile,$msgFormat);
				
				//echo "GD:".$sGDrive_W2S_ID;
				$sStatus = "";
				$sMode = "";
				$sFile = "";
				//$sUploadingFileName = $fileName;
				$sGDrive_W2S_ID = "";
				if($sGDrive_W2S_ID != "")
				{
					$ObjGDrive = new GDrive($this->m_dbConn, "0", $sGDrive_W2S_ID, 0);
					
					//echo "filename:".$noticeFileName ." mime:". $mimeType ." tmpname:". $arFILES['userfile'.$FileIndexCount]['tmp_name'] ." folderName:". $folderName;
					//$mimeType = 'application/vnd.google-apps.file';

					$errormsg = "ready to image upload through gdrive";
					$msgFormat=$errormsg."\r\n";
					fwrite($errorfile,$msgFormat);
				
					$UploadedFiles = $ObjGDrive->UploadFiles($fileName , $fileName, $mimeType, $arFILES['file'.$FileIndexCount]['tmp_name'], $folderName, $folderName, "", "", $sGDrive_W2S_ID, "0");
					
					$sStatus = "1";
					$sMode = "2";
					$sFile = $UploadedFiles["response"]["id"];
					//echo "uploaded:".$sFile;

				}
				else
				{

					$errormsg = "ready to image upload from non-gdrive";
					$msgFormat=$errormsg."\r\n";
					fwrite($errorfile,$msgFormat);

					if(move_uploaded_file($arFILES['file'.$FileIndexCount]['tmp_name'], $uploadfile))
					{
						//$_POST['note'] = $fileName;
						
						$sStatus = "1";
						$sMode = "1";
						$sFile = $fileName;
						$arResponse["note"] = $fileName;
					}
					else
					{
						echo "Error uploading file - check destination is writeable.";
						$sStatus = "0";
						$sMode = "0";
						$sFile = "";
						$arResponse["note"] = "";
						//return "";
					}
				}

				if($bTenantModule)
				{
					$arResponse["doc_name"] = $fileName;
				}
				$arResponse["status"] = $sStatus;
				$arResponse["mode"] = $sMode;
				$arResponse["response"] = $sFile;
				$arResponse["FileName"] = $fileName;
				//die();
				if($this->m_bShowTrace)
				{
					echo "<br>uploadfile:";
					echo "<pre>";
					print_r($UploadedFiles);

					echo "</pre>";
				}
				//$_POST["note"] = $noticeFileName;
				/*if($UploadedFiles["status"] == 1)
				{
					$docGDriveID = $UploadedFiles["response"]["id"];
					echo "file uploaded successfully to gdrive.";
				}
				else
				{
					//$docGDriveID = $UploadedFiles["status"][0][""];
					$docGDriveID = "Error while uploading document.";
				}*/
			}
			catch(Exception $exp)
			 {
				echo "Error occured in uploading document. Details are:".$exp->getMessage();
				die();
			 }
			return $arResponse;
		}
	}
?>