<?php
include_once ("include/dbop.class.php");
include_once ("email.class.php");
include_once ("dbconst.class.php");
include_once("notice.class.php");
include_once("greetings.class.php");	
include_once("events.class.php");
include_once("utility.class.php");
			
	//print_r($dbConn);
	echo "<pre>";
	echo "code:".getRandomUniqueCode();
	//die();
	$dbConnRoot = new dbop(true);

	//$ids = $dbConnRoot->select("select * from loginemailids");
	//echo "test";
	$ids = $dbConnRoot->select("select * from `emailqueue` where `Status`=0 and `PeriodID`<>0 and `ModuleTypeID`=0");
	if(sizeof($ids) > 0)
	{
		echo "<br/>Sending ".sizeof($ids) . " emails.<br/>";
	}
	else
	{ 
		echo "<br/>No email in queue to send.<br/>";
	}
	for($QueueCount = 0; $QueueCount < sizeof($ids); $QueueCount++)
	{
		//print_r($dbConnRoot);
		//print_r($ids);
		$DBName = $ids[$QueueCount]["dbName"];
		$PeriodID = $ids[$QueueCount]["PeriodID"];
		$SocietyID = $ids[$QueueCount]["SocietyID"];
		$UnitID = $ids[$QueueCount]["UnitID"];
		echo "<db:".$DBName .", Period:".$PeriodID.", SocietyID: ".$SocietyID ." UnitID: ".$UnitID.">";
		
		$dbConn = new dbop(false,$DBName);
		//SendEMail($dbConn, $dbConnRoot, 1, $DBName, $SocietyID, $UnitID, $PeriodID, $ids[$QueueCount]["id"]);
	}
	
	$NoticeEmails = $dbConnRoot->select("select DISTINCT `SourceTableID` from `emailqueue` where `Status`=0 and `PeriodID`=0  and `ModuleTypeID`=1");
	//echo "Notices:<br/>";
	if(sizeof($NoticeEmails) > 0)
	{
		echo "<br/>Sending ".sizeof($NoticeEmails) . " Notices.<br/>";
	}
	else
	{ 
		echo "<br/>No Notice in queue to send.<br/>";
	}
	//print_r($NoticeEmails);
	foreach($NoticeEmails as $Notices)
	{
		$srcTableID = $dbConnRoot->select("select * from `emailqueue` where `SourceTableID`='".$Notices["SourceTableID"]."' and `Status`=0 and `ModuleTypeID`=1");
		$arBCCEmailIDs = array();
		$iBCCCounter = 0;
		$Subject = "";
		$desc = "";
		$arBCCEmailIDs = "";
		$FileName = "";
		$NoticeID = "";
		$objNotice = "";
		foreach($srcTableID as $srcTable)
		{
			$dbConn = new dbop(false, $srcTable["dbName"]);
			if(isset($dbConn))
			{
				$objNotice = new notice($dbConn, $dbConnRoot, $srcTable["SocietyID"]);
					
			//echo "ct:".print_r($srcTable);
			//echo "dbname:".$srcTable["dbName"];
			//echo "UnitID:".$srcTable["UnitID"];
			if($srcTable["UnitID"] > 0)
			{
				
					//$ret =$dbConn->select("select * from `paymentdetails`");
					//print_r($ret);
					//echo "<br/>connected".$srcTable["SocietyID"];
					$memDetails = $objNotice->objFetchData->GetMemberDetails($srcTable["UnitID"]);
					if(isset($objNotice->objFetchData->objMemeberDetails->sEmail))
					{
					$arBCCEmailIDs[$iBCCCounter] = $srcTable["UnitID"];
					//echo "emailID.".$arBCCEmailIDs[$iBCCCounter];
					$iBCCCounter++;
					
					//print_r($memDetails[0]);
					$NoticeDetails = $dbConn->select("select * from `notices` where id='".$srcTable["SourceTableID"]."'");
					//print_r($NoticeDetails);
					$Subject = $NoticeDetails[0]["subject"];
					//echo "<br/>subject:".$Subject;
					$desc = $NoticeDetails[0]["description"];
					//echo "Email:".$memDetails["emailid"];
					$FileName = $NoticeDetails[0]["note"];
					$Type = $NoticeDetails[0]["notice_type_id"];
					$CreationDate = $NoticeDetails[0]["creation_date"];
					$PostDate = $NoticeDetails[0]["post_date"];
					$ExpiryDate = $NoticeDetails[0]["exp_date"];
					$IsNotify = $NoticeDetails[0]["isNotify"];
					//echo "Subject<".$Subject."> Desc:<".$desc."> fileName:<".$FileName."> srcTableID:<".$srcTable["SourceTableID"]."> Note:<".$Note."> DBName <".$srcTable["dbName"]."> Type:<".$Type.">CreationDate<".$CreationDate.">PostDate:<".$PostDate.">";
					}
									}
			}
		}
		//echo "<br/>1bcc size:<".sizeof($arBCCEmailIDs) ."><br/>";
		$loopCounter =  0;
		foreach($arBCCEmailIDs as $arIDs)
		{
			//echo "ids:".$arIDs ."<br/>";
			$loopCounter++;
		}
		//echo "<br/>change<br/>";
		if($loopCounter > 0)
		{
			//echo "ready to send";
			//echo "srcSocID:".$srcTable["SocietyID"];
			//echo "srcDBName:".$srcTable["dbName"];
			  
		//	$objNotice->sendEmail($Subject, $desc, $arBCCEmailIDs, $FileName, $NoticeID, 0, $srcTable["SocietyID"], $srcTable["dbName"], 1, $Notices["SourceTableID"]);
		}
		//echo "<br/>end<br/>";

	}
	$EventEmails = $dbConnRoot->select("select DISTINCT `SourceTableID` from `emailqueue` where `Status`=0 and `PeriodID`=0  and `ModuleTypeID`=2");
	//echo "Events:";
	if(sizeof($EventEmails) > 0)
	{
		echo "<br/>Sending ".sizeof($EventEmails) . " Events.<br/>";
	}
	else
	{ 
		echo "<br/>No events in queue to send.<br/>";
	}
	//print_r($EventEmails);
	foreach($EventEmails as $Event)
	{
		$query =  "select * from `emailqueue` where `SourceTableID`='".$Event["SourceTableID"]."' and `Status`=0 and `ModuleTypeID`=2";
		//echo $query;
		$srcTableID = $dbConnRoot->select($query);
		$arBCCEmailIDs = array();
		$iBCCCounter = 0;
		$Subject = "";
		$desc = "";
		$arBCCEmailIDs = "";
		$FileName = "";
		$EventID = "";
		$objEvent = "";
		//echo "<br/>test<br/>";
		foreach($srcTableID as $srcTable)
		{
			//echo "<br/>test2<br/>";
			$dbConn = new dbop(false, $srcTable["dbName"]);
			if(isset($dbConn))
			{
				//echo "<br/>test3<br/>";
				$objEvent = new events($dbConn, $dbConnRoot, $srcTable["SocietyID"]);
				//echo "<br/>test4<br/>";	
			//echo "ct:".print_r($srcTable);
			//echo "dbname:".$srcTable["dbName"];
			//echo "UnitID:".$srcTable["UnitID"];
			if($srcTable["UnitID"] > 0)
			{
				
					//$ret =$dbConn->select("select * from `paymentdetails`");
					//print_r($ret);
					//echo "<br/>connected".$srcTable["SocietyID"];
					$memDetails = $objEvent->objFetchData->GetMemberDetails($srcTable["UnitID"]);
					//echo "<br/>test5<br/>";
					if(isset($objEvent->objFetchData->objMemeberDetails->sEmail))
					{
					$arBCCEmailIDs[$iBCCCounter] = $srcTable["UnitID"];
					//echo "emailID.".$arBCCEmailIDs[$iBCCCounter];
					$iBCCCounter++;
					$EventID  = $srcTable["SourceTableID"];
					//print_r($memDetails[0]);
					$EventDetails = $dbConnRoot->select("select * from `events` where `events_id`='".$srcTable["SourceTableID"]."'");
					//print_r($NoticeDetails);
					/*$Subject = $EventDetails[0]["events_title"];
					//echo "<br/>subject:".$Subject;
					$desc = $EventDetails[0]["events"];
					//echo "Email:".$memDetails["emailid"];
					$FileName = $EventDetails[0]["Uploaded_file"];
					$Type = $EventDetails[0]["event_type"];
					$CreationDate = $EventDetails[0]["creation_date"];
					$PostDate = $EventDetails[0]["post_date"];
					$ExpiryDate = $EventDetails[0]["exp_date"];
					$IsNotify = $EventDetails[0]["isNotify"];*/
					//echo "Subject<".$Subject."> Desc:<".$desc."> fileName:<".$FileName."> srcTableID:<".$srcTable["SourceTableID"]."> Note:<".$Note."> DBName <".$srcTable["dbName"]."> Type:<".$Type.">CreationDate<".$CreationDate.">PostDate:<".$PostDate.">";
					}
									}
			}
		}
		//echo "<br/>Events bcc size:<".sizeof($arBCCEmailIDs) ."><br/>";
		$loopCounter =  0;
		foreach($arBCCEmailIDs as $arIDs)
		{
			//echo "ids:".$arIDs ."<br/>";
			$loopCounter++;
		}
		//echo "<br/>change<br/>";
		if($loopCounter > 0)
		{
			//echo "ready to send";
			//echo "srcSocID:".$srcTable["SocietyID"];
			//echo "srcDBName:".$srcTable["dbName"];
			  
		//	$objEvent->SendEventInEmail(1, $srcTable["dbName"], $srcTable["SocietyID"], $EventID);
		}
		//echo "<br/>end<br/>";

	}

	
	$CounterLimit = 9; //9 for logintype 1 & 40 for login type 0
	$LoginType = 1;
		
	for($GreetingQueueCount = 0; $GreetingQueueCount < $CounterLimit; $GreetingQueueCount++)
	{
		//echo "Greeting Counter:". $GreetingQueueCount;
		//$ActivationEmail = 0;
		
		//for($LoginTypeCount = 0; $LoginTypeCount < $LoginTypeCounterLimit; $LoginTypeCount++)
		{
			$GreetingQry ="select DISTINCT `SourceTableID` from `emailqueue` where `Status`=0 and `PeriodID`=0  and `ModuleTypeID`=3 and `LoginExist`='".$LoginType."' limit 0,49";
			$GreetingEmails = $dbConnRoot->select($GreetingQry);
			//echo "<br>sqlgreeting:".$GreetingQry;
			if(sizeof($GreetingEmails) > 0)
			{
				//echo "<br/>Sending ".sizeof($GreetingEmails) . " Greetings.<br/>";
			
				//print_r($GreetingEmails);
				foreach($GreetingEmails as $Greetings)
				{
					$sqlLoginTypeQry = "select * from `emailqueue` where `SourceTableID`='".$Greetings["SourceTableID"]."' and `Status`=0 and `ModuleTypeID`=3 and `LoginExist`='".$LoginType."'";
					if($LoginType == 0)
					{
						$sqlLoginTypeQry .=  " limit 0,1";
					}
					else
					{
						$sqlLoginTypeQry .=  " limit 0,49";	
					}
					$srcTableID = $dbConnRoot->select($sqlLoginTypeQry);
					$arBCCEmailIDs = array();
					//print_r($srcTableID);
					//echo "<br/>qry ".$sqlLoginTypeQry . ".<br/>";
					echo "<br/>Sending Greetings to ".sizeof($srcTableID) . " IDs.<br/>";
					$iBCCCounter = 0;
					$Subject = "";
					$desc = "";
					$arBCCEmailIDs = "";
					$sQueueID = "";
					$FileName = "";
					$NoticeID = "";
					$objNotice = "";
					$LastDBName = "";
					$unitNo = "";
					foreach($srcTableID as $srcTable)
					{
						$CurDBName = $srcTable["dbName"];
						if($LastDBName == "")
						{
							$LastDBName = $CurDBName;
						}
						//echo "<br/>Last DB Name :".$LastDBName ." Current DB Name :".$CurDBName ." ID :".$srcTable["id"]."<br/>";		
						if($LastDBName != "" && $LastDBName == $CurDBName)
						{
							$dbConn = new dbop(false, $CurDBName);
							if(isset($dbConn))
							{
								if($sQueueID == "")
								{
									$sQueueID = $srcTable["id"];
								}
								else
								{
									$sQueueID .= "," .$srcTable["id"];
								}
								$objGreeting = new greetings($dbConn, $dbConnRoot, $srcTable["SocietyID"]);
									
								//echo "ct:".print_r($srcTable);
								//echo "dbname:".$srcTable["dbName"];
								//echo "UnitID:".$srcTable["UnitID"] ;
								//echo "<br>";
								if($srcTable["UnitID"] > 0)
								{
								
									//$ret =$dbConn->select("select * from `paymentdetails`");
									//print_r($ret);
									//echo "<br/>connected".$srcTable["SocietyID"];
									$memDetails = $objGreeting->objFetchData->GetMemberDetails($srcTable["UnitID"]);
									$unitNo = $objGreeting->objFetchData->GetUnitNumber($srcTable["UnitID"]);
									$specialChars = array('/','.', '*', '%', '&', ',', '(', ')', '"');
									$unitNo = str_replace($specialChars,'',$unitNo);
									
									//echo "member details<br>";
									//print_r($memDetails);
									//echo "member details fetched.";
									echo "email:".$objGreeting->objFetchData->objMemeberDetails->sEmail. ".<br>";
									if(isset($objGreeting->objFetchData->objMemeberDetails->sEmail))
									{
										echo "email exist <br>";
										$arBCCEmailIDs[$iBCCCounter] = $srcTable["UnitID"];
										//echo "emailID.".$arBCCEmailIDs[$iBCCCounter];
										$iBCCCounter++;
										
										//print_r($memDetails[0]);
										$greeting_query = "select * from `greetings` where id='".$srcTable["SourceTableID"]."'";
										//echo "query:".$greeting_query;
										$GreetingDetails = $dbConnRoot->select($greeting_query);
										//echo "greeting details<br>";
										//print_r($GreetingDetails);
										$Subject = $GreetingDetails[0]["subject"];
										//echo "<br/>subject:".$Subject;
										$desc = $GreetingDetails[0]["description"];
										//echo "Email:".$memDetails["emailid"];
										$Note = $GreetingDetails[0]["note"];
										$Type = $GreetingDetails[0]["notice_type_id"];
										$CreationDate = $GreetingDetails[0]["creation_date"];
										$PostDate = $GreetingDetails[0]["post_date"];
										$ExpiryDate = $GreetingDetails[0]["exp_date"];
										$IsNotify = $GreetingDetails[0]["isNotify"];
										//echo "Subject : ".$Subject." Desc: ".$desc." fileName: ".$FileName." srcTableID: ".$srcTable["SourceTableID"]." Note: ".$Note." DBName: ".$srcTable["dbName"]." Type: ".$Type." CreationDate: ".$CreationDate.">PostDate: ".$PostDate." ";
									}
								}
							}
							else
							{
								echo "<br/>DB Connection failed for :".$srcTable["dbName"] ."<br/>";		
							}
						}
						else
						{
							echo "<br/>DB pushed for next batch :".$srcTable["dbName"] ."<br/>";		
						}
					}
					//echo "<br/>1bcc size:<".sizeof($arBCCEmailIDs) ."><br/>";
					$loopCounter =  0;
					foreach($arBCCEmailIDs as $arIDs)
					{
						//echo "ids:".$arIDs ."<br/>";
						$loopCounter++;
					}
					//echo "<br/>loopCounter: ".$loopCounter ."<br/>";
					if($loopCounter > 0)
					{
						//echo "ready to send";
						//echo "srcSocID:".$srcTable["SocietyID"];
						//echo "srcDBName:".$srcTable["dbName"];
						  
						$objGreeting->sendGreetingsEmail($Subject, $desc, $arBCCEmailIDs, $FileName, $NoticeID, 0, $srcTable["SocietyID"], $LastDBName, 1, $Greetings["SourceTableID"], $sQueueID, $LoginType, $unitNo);
					}
				}
			}
			else
			{ 
				echo "<br/>No Greeting in queue to send.<br/>";
			}
		//echo "<br/>end<br/>";
		}
	}

	$hostname = 'localhost';
	$username = 'root';
	$password = '';
	$dbPrefix = 'hostmjbt_society';
	
	$hostname = DB_HOST;
	$username = DB_USER;
	$password = DB_PASSWORD;
	$dbPrefix = 'hostmjbt_society';
	
	$startNo = 1;
	$endNo = 120;
	
	
	//echo '<br/><br/>Updating DB ' . $dbPrefix . $startNo . ' to ' . $dbPrefix . $endNo   ;
			
			for($iCount = $startNo; $iCount <= $endNo; $iCount++)
			{
				try
				{
					$dbName = $dbPrefix . $iCount;
					//echo '<br/><br/>Connecting DB : ' . $dbName;
					 
					$mMysqli = mysqli_connect($hostname, $username, $password, $dbName);
					if(!$mMysqli)
					{
						echo '<br/>Connection Failed';
					}
					else
					{
						if($dbName == "hostmjbt_society246")
						{ 
							//echo '<br/>Connected';
							$dbConn = new dbop(false,$dbName);
							//$dbConnRoot = new dbop(true);
							$objUtility  = new utility($dbConn,$dbConnRoot);


							
							$query = "select * from `generalsms_log` WHERE `DeliveryStatus`=''";
							
							$result = $dbConn->select($query);
							
							$queryclient = "SELECT `client_id`,`society_id` FROM  `society` WHERE  `dbname` ='".$dbName."' ";
							
							$resultclient = $dbConnRoot->select($queryclient);
							$client_id = 0;
							$society_id = 0;
							if(sizeof($resultclient) > 0)
							{
								$client_id = $resultclient[0]['client_id'];
								$society_id = $resultclient[0]['society_id'];
							}
							//$queryGetUnits = "select DISTINCT(member_main.unit) from member_main JOIN mem_other_family ON mem_other_family.member_id = member_main.member_id where mem_other_family.send_commu_emails=1";
							$queryGetUnits = "SELECT mem_other_family.mem_other_family_id as id, mem_other_family.other_email as to_email, mem_other_family.other_name as to_name, IF(mem_other_family.mem_other_family_id > 0, mem_other_family.coowner, 0) as type, unit.unit_id as unit FROM `mem_other_family` JOIN `member_main` on mem_other_family.member_id = member_main.member_id JOIN `unit` on unit.unit_id = member_main.unit where mem_other_family.send_commu_emails = 1 and member_main.member_id IN (SELECT member_main.`member_id` FROM (select `member_id` from `member_main` where `ownership_date` <= NOW() ORDER BY `ownership_date` desc) as member_id Group BY unit) UNION select tmem.tmember_id as id, tmem.email as to_email, tmem.mem_name as to_name, IF(tmem.tmember_id > 0, 10, 0) as type, tmod.unit_id as unit from tenant_member as tmem JOIN tenant_module as tmod ON tmem.tenant_id = tmod.tenant_id where tmod.end_date > NOW() AND tmem.send_commu_emails=1";
							//echo "Query:".$queryGetUnits;
							echo "<br>";
							$resultUnit = $dbConn->select($queryGetUnits);
							//echo "Unit count:" .count($resultUnit );

							
							/*foreach($resultUnit as $Units)
							{
								$ReqUnitID = $Units["unit"];
								$MemberEmailID = $Units["to_email"];
								if($MemberEmailID != "")
								{
									$sqlLoginQry = "select login_id from login where member_id='".$MemberEmailID."'";
									$resLogin =$dbConnRoot->select($sqlLoginQry);
									$loginExist = 0;
									if($resLogin[0]["login_id"] > 0)
									{
										$loginExist = 1;
									}
									$queryInsQ = "insert into `emailqueue` (`dbName`,`PeriodID`,`SocietyID`,`UnitID`,`Status`,`SourceTableID`,`ModuleTypeID`,`loginExist`) values ('".$dbName."',0,'".$society_id."','".$ReqUnitID."','0',1,3,'".$loginExist."')";
									//echo "Query:".$queryInsQ;
									echo "<br>";
									$resultIns = $dbConnRoot->insert($queryInsQ);
								}
							}*/
							echo "<br/>dbname ::".$dbName.">";
							echo "<br/>ClientID <".$client_id . ">";
							if($result)
							{
								foreach($result as $Record)
								{ 
										$SentReport = $Record['SentReport'];
										$arSentReport = explode(',',$SentReport);
												$arDeliveryReport = "";
												if($Record['DeliveryReport'] <> "")
												{
													$DeliveryReport = $Record['DeliveryReport'];
													$arDeliveryReport = explode(',',$DeliveryReport);
												}
												//echo '<br/>ID '.$Record['ID'].' MSGID ' .$arSentReport[2]. ' DeliveryReport '.$Record['DeliveryReport'] ;
												/*if((isset($arSentReport[2]) && $arSentReport[2] <> "" && $arSentReport[2] <> "0") && ( ($arDeliveryReport <> "" && sizeof($arDeliveryReport) > 0 && $arDeliveryReport[4] <> "Delivered") || $arDeliveryReport == "" ) )
												{
													$response = $objUtility->GetSMSDeliveryReport($arSentReport[2],$client_id);
													$status = explode(',',$response);
													$UpdateQuery = "UPDATE `generalsms_log` SET `DeliveryReport`='".$response."',`DeliveryStatus`='".$status[4]."' WHERE `ID`='".$Record['ID']."'";
													//echo "<br/>".$UpdateQuery;
													$dbConn->update($UpdateQuery);
												}*/
								}
							}
							else
							{
								echo '<br/>Update Failed';
							}
							//------------------------------------------------------------------------------------------------
							//									Notification SMS
							//------------------------------------------------------------------------------------------------
							$NFT_Query = "select * from `notification` WHERE `DeliveryStatus`=''";
							
							$NFT_Result = $dbConn->select($NFT_Query);
							if($NFT_Result)
							{
								foreach($NFT_Result as $NFT_Record)
								{ 
										$SentReport = $NFT_Record['SMSSentReport'];
										if($SentReport <> "")
										{
											$arSentReport = explode(',',$SentReport);
											
											$arnDeliveryReport ="";
											if($NFT_Record['DeliveryReport'] <> "")
											{
												$nDeliveryReport = $NFT_Record['DeliveryReport'];
												$arnDeliveryReport = explode(',',$nDeliveryReport);
											}
											$queryclient = "SELECT `client_id` FROM  `society` WHERE  `dbname` ='".$dbName."' ";
							
											$resultclient = $dbConnRoot->select($queryclient);
											$client_id = 0;
											if(sizeof($resultclient) > 0)
											{
												echo $client_id = $resultclient[0]['client_id'];
											}

											
											//echo '<br/>ID '.$NFT_Record['ID'].' MSGID ' .$arSentReport[2]. ' DeliveryReport '.$NFT_Record['DeliveryReport'] ;
											/*if((isset($arSentReport[2]) && $arSentReport[2] <> "" && $arSentReport[2] <> "0")&& ( ($arnDeliveryReport <> "" && sizeof($arnDeliveryReport) > 0 && $arnDeliveryReport[4] <> "Delivered") || $arnDeliveryReport == "" ) )
											{
												$response = $objUtility->GetSMSDeliveryReport($arSentReport[2],$client_id);
												$status = explode(',',$response);
												$UpdateQuery = "UPDATE `notification` SET `DeliveryReport`='".$response."',`DeliveryStatus`='".$status[4]."' WHERE `ID`='".$NFT_Record['ID']."'";
												//echo "<br/>".$UpdateQuery;
												$dbConn->update($UpdateQuery);
													//exit;
												
											}*/
										}
								}
							}
							else
							{
								//echo '<br/>Update Failed';
							}
							mysqli_close($mMysqli);
							//echo '<br/>Connection Closed';
						}
						else
						{
							//echo "</br>skipped db ".$dbName;
						}
					}
				}
				catch(Exception $ex)
				{
					echo "Exception:".$ex;
				}
			}
	?>