<?php
	echo "before include dbconst";
	include_once("dbconst.class.php");
	echo "after include dbconst";
	
	class utility
	{
		private $m_dbConn;
		public $m_dbConnRoot;
		
		function __construct($dbConn, $dbConnRoot = "")
		{
			echo "ctor 1";
			$this->m_dbConn = $dbConn;
			echo "ctor 2";
			$this->m_dbConnRoot = $dbConnRoot;
			echo "ctor 3";
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
			$ResultAry['status'] = 0;
			$ResultAry['msg'] = 0;
			$ResultAry['email'] = '';
			$ResultAry['password'] = '';
			$ListOfEmailIDs = $this->m_dbConnRoot->select("select * from `loginemailids`");
			$iEmailCount = sizeof($ListOfEmailIDs);
			//echo 'Email Count : ' . $iEmailCount;
			$sql_query = "update `loginemailids` set `EmailSentCounter`=0 where `LastUsedTimeStamp` < TIMESTAMPADD( HOUR , -1, NOW( )+ INTERVAL 5 HOUR + INTERVAL 30 
MINUTE)";
			$this->m_dbConnRoot->update($sql_query);
			//$EMailDetails = array();
			if($iEmailCount > 0)
			{
				//$PrevEmailID = $this->GetPrevUsedEmailID();
				//$iNextID = $PrevEmailID[0]['id'];
				
				
				for($iEmailIDCounter = 0; $iEmailIDCounter < $iEmailCount; $iEmailIDCounter++ )
				{
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
						$iDiffInMins = $this->getTimeDiff($PrevTimeStamp, $sCurrentTimeStamp['DateTime'], HOUR) .">";
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
								$Query = "select id,EmailSentCounter from `loginemailids` where EmailSentCounter<>MaxLimit"; 
								
								$NextAvailableID = $this->m_dbConnRoot->select($Query);
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
		
		public function getParentOfLedger($ledgerID)
		{
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
		
		public function getTimeDiff($date1, $date2, $Interval = "DAY")
		{
			$sql = "SELECT TIMESTAMPDIFF(".$Interval.",'" . $date1 . "','" . $date2 . "') AS DiffDate";
			//echo $sql;
			$result = $this->m_dbConn->select($sql);
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
				else if($CategoryGroupID == INCOME || $CategoryGroupID == EXPENSE)
				{
					//do nothing not required now
				}
			}
									
			return $openingBalance;
			
		}
		
		
		public function getPreviousYearEndingDate($CurrentYearID)
		{
			$prevYearID = $CurrentYearID - 1;
			$getPerod = "select yeartbl.EndingDate from `period` as periodtbl JOIN `society` as societytbl on periodtbl.Billing_cycle = societytbl.bill_cycle  JOIN `year` as yeartbl on yeartbl.YearID = periodtbl.YearID where societytbl.society_id = ".$_SESSION['society_id']." and  yeartbl.YearID = ".$prevYearID." ";
			$period = $this->m_dbConn->select($getPerod);
			$EndingDate =	$period[0]['EndingDate'];
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
			if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
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
		
		$sqlII = "SELECT member_id, unit, owner_name, ownership_date FROM member_main where ownership_date <= '".$date."'  ";
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
	public function getSMSTemplateString($MobileNumber, $SMSBody)
	{
		$smsTemplate ='<?xml version="1.0"?>
							<parent>
							<child>
							<user>waysoc</user>
							<key>7009e8caf1XX</key>
							<mobile>+91'.$MobileNumber.'</mobile>
							<message>'.$SMSBody.'</message>
							<senderid>waysoc</senderid>
							<accusage>1</accusage>
							
							</child>						
							</parent>';	
		return $smsTemplate;					
	}
	public function GetChildTags($arMobileNumber)
	{
		
										
							
	}
	
	public function SendSMS($mobileNumber, $SMSBody)
	{
		$Template = $this->getSMSTemplateString($mobileNumber, $SMSBody);
		$URL = "http://sms.transaction.surewingroup.info/submitsms.jsp?";
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
		return $response;
	}
	
	public function GetSMSDeliveryReport($MessageID)
	{
		//$xml_data ='<?xml version="1.0"
				$data	= '<?xml version="1.0"?>
					<parent>
					<child>
					<userid>waysoc</userid>
                     <key>7009e8caf1XX</key>
					<messageid>'.$MessageID.'</messageid>
					</child>	
					</parent>';
					
		//$url = "http://sms.transaction.surewingroup.info/getreport.jsp?";
		$url = 'http://sms.transaction.surewingroup.info/getreport.jsp?userid=waysoc&key=7009e8caf1XX&messageid="'.$MessageID.'"';
		//$URL = 'http://sms.transaction.surewingroup.info/getreport.jsp?';
		/*$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$response = curl_exec($ch);		
		curl_close($ch);
		return $response;*/
		
		
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		/*curl_setopt($ch, CURLOPT_POST, 1);*/
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
		/*curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, "$data");*/
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		curl_close($ch);
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
	}
?>