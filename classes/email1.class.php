<?php 
	if(isset($_REQUEST['SentEmailManually']) && $_REQUEST['SentEmailManually'] == 1)
	{
		include_once ("dbconst.class.php");
		include_once ("include/dbop.class.php");
		$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		$societyID = $_SESSION['society_id'];
		$DBName = $_SESSION['dbname'];
		$UnitID = $_REQUEST['unit'];
		$UnitsCollection = $_REQUEST['unitsArray'];
		$PeriodID = $_REQUEST['period'];
		
		$Units = "";
		if(isset($_REQUEST['unitsArray']))
		{
			$Units = json_decode($UnitsCollection);	
		}
		else
		{
			$Units   = array();
			$Units[0] = $UnitID;
		}
		$ResultAry  = array();
		foreach($Units as $UnitNumber)
		{
			if($UnitNumber <> "")
			{
				SendEMail($dbConn, $dbConnRoot, 0, $DBName, $societyID, $UnitNumber, $PeriodID, "-1", $ResultAry,$_REQUEST['BT']);
			}
		}
		echo json_encode($ResultAry);
		
	}
	
	function SendEMail($dbConn, $dbConnRoot, $CronJobProcess, $DBName, $societyID, $unitID, $periodID, $QueueID, &$ResultAry,$Supplemenary_bills = 0)
	{
		try
		{
		include_once ("include/fetch_data1.php");
		$obj_fetch = new FetchData($dbConn);
		include_once("utility.class.php");
		$obj_Utility = new utility($dbConn, $dbConnRoot);
		require_once('../swift/swift_required.php');
		
		if(isset($unitID) && isset($periodID))
		{
			$EncodeSocID = base64_encode($societyID);
			$EncodeUnitID = base64_encode($unitID);
			$url = "<a href='http://way2society.com/neft.php?SID=".$EncodeSocID."&UID=".$EncodeUnitID."'>Notify Society about NEFT Payment</a>";
			//$url = "<a href='http://localhost/way2society.com/neft.php?SID=".$EncodeSocID."'>View</a>";
			//-$mailBody .= $url;
			
			$memberDetails = $obj_fetch->GetMemberDetails($unitID);
			
			$societyDetails = $obj_fetch->GetSocietyDetails($obj_fetch->GetSocietyID($unitID));
			$mailToEmail = $obj_fetch->objMemeberDetails->sEmail;
			if($mailToEmail == '')
			{
				$ResultAry[$unitID] = "Email ID Missing";
				return;
			}
			else if(filter_var($mailToEmail, FILTER_VALIDATE_EMAIL) == false)
			{
				$ResultAry[$unitID] = "Incorrect Email ID  ".$mailToEmail." ";
				return;
			}
			
			$socContactNo = '<div>'.$obj_fetch->objSocietyDetails->sSocietyEmailContactNo .'</div>';
			$mailToName = $obj_fetch->objMemeberDetails->sMemberName;
			$newUserUrl = "";
			$isNewAccountCreate = false;
			
			$loginExist = $dbConnRoot->select("SELECT * FROM `login` WHERE `member_id` = '".$mailToEmail."'");
			$sqlClientDetails = "SELECT * FROM `client` WHERE `id` = '".$_SESSION['society_client_id']."'";
			//$ResultAry[$unitID] = $sqlClientDetails;
			$ClientDetails = $dbConnRoot->select($sqlClientDetails);
			if(isset($ClientDetails))
			{
				//echo "invalid client details";
			}
			
			$encryptedEmail = $obj_Utility->encryptData($mailToEmail);	
			if(sizeof($loginExist) == 0)		
			{			
				//$mappingDetails = $dbConnRoot->select("SELECT * FROM `mapping` WHERE `society_id` = '".$obj_fetch->GetSocietyID($unitID)."' AND `unit_id` = '".$unitID."' AND `status` = 1");
				//if(sizeof($mappingDetails) > 0)
				//{		
					//$encryptedEmail = $obj_Utility->encryptData($mailToEmail);					
					//$newUserUrl = "http://way2society.com/newuser.php?reg&u=".$mailToEmail."&n=".$mailToName."&c=".$mappingDetails[0]['code']."&tkn=".$encryptedEmail;
					//$newUserUrl = "http://way2society.com/newuser.php?reg&u=".$mailToEmail."&n=".$mailToName."&tkn=".$encryptedEmail;
					$newUserUrl = "http://way2society.com/newuser.php?reg&u=".$mailToEmail."&n=".$mailToName."&tkn=".$encryptedEmail;
					
					$onclickURL = $newUserUrl.'&URL=http://way2society.com/Dashboard.php?View=MEMBER';
					$userURL = $newUserUrl.'&url=http://way2society.com/Maintenance_bill.php?e=1_**_UnitID='.$unitID.'_**_PeriodID='.$periodID.'_**_BT='.$Supplemenary_bills;		
				//}
			}
			else
			{
				$iSocietyId =  $obj_fetch->GetSocietyID($unitID);
				$mappingDetails = $dbConnRoot->select("SELECT * FROM `mapping` WHERE `society_id` = '".$iSocietyId."' AND `unit_id` = '".$unitID."' AND `login_id` = '".$loginExist[0]['login_id']."' ");		
				if(sizeof($mappingDetails) > 0)
				{	
						$onclickURL = "http://way2society.com/Dashboard.php?View=MEMBER";
						$userURL ='http://way2society.com/Maintenance_bill.php?e=1_**_UnitID='.$unitID.'_**_PeriodID='.$periodID.'_**_BT='.$Supplemenary_bills.'&u='.$encryptedEmail;		
				}
				else
				{
						
						$mappingDetails = $dbConnRoot->select("SELECT * FROM `mapping` WHERE `society_id` = '".$iSocietyId."' AND `unit_id` = '".$unitID."' AND `status` = '1' ");		
						if(sizeof($mappingDetails) > 0)
						{	
							$sAccountActivationCode = $mappingDetails[0]['code'];
							$onclickURL = 'http://way2society.com/login.php?mCode='.$sAccountActivationCode.'&u='.$encryptedEmail;
							$userURL ='http://way2society.com/login.php?mCode='.$sAccountActivationCode.'&u='.$encryptedEmail .'&url=Maintenance_bill.php?e=1_**_UnitID='.$unitID.'_**_PeriodID='.$periodID.'_**_BT='.$Supplemenary_bills;	
						}
						else
						{
							$sAccountActivationCode = getRandomUniqueCode();
							
							$arPaidToParentDetails = $obj_Utility->getParentOfLedger($unitID);
							if(!(empty($arPaidToParentDetails)))
							{
								$unitNo = $arPaidToParentDetails['ledger_name'];
							}
							
							$insert_mapping = "INSERT INTO `mapping`(`login_id`,`society_id`, `unit_id`, `desc`, `code`, `role`, `created_by`, `view`,`status`) VALUES ('" . $loginExist[0]['login_id'] . "','" . $iSocietyId . "', '" . $unitID . "', '" . $unitNo . "', '" . $sAccountActivationCode . "', '" . ROLE_MEMBER . "', '" . $loginExist[0]['login_id'] . "', 'MEMBER','2')";
							$result_mapping = $dbConnRoot->insert($insert_mapping);
							
							$onclickURL = 'http://way2society.com/login.php?mCode='.$sAccountActivationCode.'&u='.$encryptedEmail;
							$userURL ='http://way2society.com/login.php?mCode='.$sAccountActivationCode.'&u='.$encryptedEmail .'&url=Maintenance_bill.php?e=1_**_UnitID='.$unitID.'_**_PeriodID='.$periodID.'_**_BT='.$Supplemenary_bills;	
						}
				}
			
			}
			
			if($newUserUrl <> "")
			{			
				$buttonValue = "My Account";	
				$isNewAccountCreate = true;
				$sText = "Please click below button to check your account balance, bills and previous payments. ";
				//$sText = "Your email id <".$mailToEmail."> is not registered to way2society.com yet. You can register now by clicking on ".$buttonValue.".";
				$neftURL = $newUserUrl.'&URL=http://way2society.com/Dashboard.php?View=MEMBER';		
			}
			else
			{			
				$buttonValue = "My Account";
				$sText = "Please click below button to check your account balance, bills and previous payments.";	
				$neftURL = 	'http://way2society.com/neft.php?SID='.$EncodeSocID.'&UID='.$EncodeUnitID;		
			}
			
			$sBillTypeText  = 'Maintenance ';
			if($_REQUEST['BT'] == 1)
			{
				$sBillTypeText = 'Supplementary ';
			}
			$mailSubject = $sBillTypeText . 'Bill For : ' . $obj_fetch->GetBillFor($periodID);//'Maintainance Bill For March';
			$mailBody ="";
			// $mailBody = 'Attached Maintainance Bill For ' . $obj_fetch->GetBillFor($_REQUEST["period"]) .'<br />';
			//Dear '.$mailToName. ',<br /><br />Please find attached  '.$billText.'  bill for ' . $obj_fetch->GetBillFor($periodID) .'.<br />
			/*$billText = "Maintenance"	;
			if($Supplemenary_bills == '1')
			{
				$billText = "Supplementary";
			}*/
			$societyEmailID = $obj_fetch->objSocietyDetails->sSocietyEmail;
			 if($societyEmailID == '')
			 {
				 $societyEmailID = "societyaccounts@pgsl.in";
				// $societyEmail = "rohit.shinde2010@gmail.com";
			 }
			if($obj_fetch->objSocietyDetails->sSocietySendBillAsLink == 1)
			 {
				$mailBodyHeader= " Dear ".$mailToName.",<br /><br />".$sBillTypeText."  bill for " . $obj_fetch->GetBillFor($periodID).' is generated.<br />';
			 }
			 else
			 {
				 $mailBodyHeader= " Dear ".$mailToName.",<br /><br />Please find attached ".$sBillTypeText."  bill for " . $obj_fetch->GetBillFor($periodID).'.<br />';
			 }
			
			$mailBody .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
								   <table width="100%">
								   
									<tr><td>'.$ClientDetails[0]['email_header'].'</td></tr>
									<tr><td>'.$mailBodyHeader.'<br />';
								//if($isNewAccountCreate != true)
								{
									$mailBody .= ' 
									If you have made NEFT payment, please click below button to enter NEFT transaction details. </td>                   
									</tr>
									<tr><td><br /></td></tr>
									<tr>
										<td align="center">
											<table width="250px" cellspacing="0" cellpadding="0" border="0">
												<tbody>
													<tr>
														<td valign="middle" bgcolor="#337AB7" height="40" align="center">
															<a target="_blank" style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif" href="'.$neftURL.'">Enter NEFT transaction details</a>
														</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<tr><td><br /></td></tr>';
								}
								
								if($obj_fetch->objSocietyDetails->sSocietySendBillAsLink == "1")
								{
									$mailBody .= '<tr>
											<td>Please click below button to unsubscribe  '.$sBillTypeText.'  Bill for ' . $obj_fetch->GetBillFor($periodID).'</td>									
										</tr>
										<tr><td><br /></td></tr>
										<tr>
											<td align="center">
												<table width="200px" cellspacing="0" cellpadding="0" border="0">
													<tbody>
														<tr>
															<td valign="middle" bgcolor="#337AB7" height="40" align="center" style="cursor:pointer;">
																<a href="'.$userURL.'"  style="cursor:pointer;text-decoration: none;">
																	<div style="cursor:pointer;text-decoration: none;" onClick=\'window.open("//'.$userURL.'");\' >
																		<font  style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif;"><b>unsubscribe </b></font>
																	</div>
																</a>
														  </td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
										<tr><td><br /></td></tr>';
								}
								$mailBody .= '<tr>
										<td>'.$sText.'</td>									
									</tr>
									<tr><td><br /></td></tr>
									<tr>
										<td align="center">
											<table width="200px" cellspacing="0" cellpadding="0" border="0">
												<tbody>
													<tr>
														<td valign="middle" bgcolor="#337AB7" height="40" align="center" style="cursor:pointer;">
															<a href="'.$onclickURL.'"  style="cursor:pointer;text-decoration: none;">
																<div style="cursor:pointer;text-decoration: none;" onClick=\'window.open("//'.$onclickURL.'");\' >
																	<font  style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif;"><b>'.$buttonValue.'</b></font>
																</div>
															</a>
                                                      </td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<tr><td><br /></td></tr>
									<tr><td>If you are a new user, we will take you through a simple process to create your account. </td></tr>
									<tr><td><br /></td></tr>
									<tr><td>'.$ClientDetails[0]['email_footer'].'</td></tr>
									<tr><td>Email : '.$societyEmailID .'</td></tr>
									<tr><td>'. $socContactNo .'</td></tr>
									<tr>
									<td><br /></td>
									</tr>
									<tr>
									<td style="color:#808080;font-size:10px" >
									Note: This is an automated email. Sometimes spam filters block automated emails. If you do not find the email in your inbox, please check your spam filter or bulk email folder. Mark this email as "Not Spam" to get further emails in inbox.
									</td>
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
									 <td align="right">
									  <table border="0" cellpadding="0" cellspacing="0">
									   <tr>
										<td>
											<a href="https://twitter.com/pavitraglobal" target="_blank"><img src="http://way2society.com/images/icon2.jpg" alt=""></a>                  
										</td>
										<td style="font-size: 0; line-height: 0;" width="20">&nbsp;&nbsp;</td>
										<td>
											<a href="https://www.facebook.com/PavitraGlobalServicesLtd" target="_blank"><img src="http://way2society.com/images/icon1.jpg" alt=""></a>                 
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
							
			
		$unitNo = $obj_fetch->GetUnitNumber($unitID);
		$specialChars = array('/','.', '*', '%', '&', ',', '(', ')', '"');
		$unitNo = str_replace($specialChars,'',$unitNo);
		
		//$baseDir = dirname( dirname(__FILE__) );
		$baseDir =( __DIR__ );
		$baseDir = substr($baseDir, 0,strrpos($baseDir, '/') );
		
		$fileName =  $baseDir . "/maintenance_bills/" . $obj_fetch->objSocietyDetails->sSocietyCode . "/" . $obj_fetch->GetBillFor($periodID) . "/bill-" . $obj_fetch->objSocietyDetails->sSocietyCode . '-' . $unitNo . "-" . $obj_fetch->GetBillFor($periodID) . '-'.$Supplemenary_bills.'.pdf';
		//$fileName =  $baseDir . "\\maintenance_bills\\DVT\\April-2016-17\\bill-DVT-101-April-2016-17-0.pdf";
		
		if(!file_exists($fileName) && $obj_fetch->objSocietyDetails->sSocietySendBillAsLink == 0)
		{
			$ResultAry[$unitID] = "Bill PDF does not exist.";
			return;
		}
		$EMailIDToUse = $obj_Utility->GetEmailIDToUse(true, 0, $periodID, $unitID, $CronJobProcess, $DBName, $societyID, 0, 0);
		if($EMailIDToUse['status'] == 0)
		{
			$EMailID = $EMailIDToUse['email'];
			$Password = $EMailIDToUse['password'];
			// Create the mail transport configuration
			$transport = Swift_SmtpTransport::newInstance('cs10.webhostbox.net', 465, "ssl")
				  ->setUsername($EMailID)
				  ->setSourceIp('0.0.0.0')
				  ->setPassword($Password)
				  ->setLocalDomain('[35.154.83.226]');	 
			 
			// Create the message
			$message = Swift_Message::newInstance();
			$message->setTo(array(
			  $mailToEmail => $mailToName
			 ));
			 
			 $societyEmail = $obj_fetch->objSocietyDetails->sSocietyEmail;
			 if($societyEmail == '')
			 {
				 $societyEmail = "societyaccounts@pgsl.in";
				// $societyEmail = "rohit.shinde2010@gmail.com";
			 }
			 
			 $societyName = $obj_fetch->objSocietyDetails->sSocietyName;
					
			 $society_CCEmail = $obj_fetch->objSocietyDetails->sSocietyCC_Email;
			 if($society_CCEmail <> "")
			 {
				// $message->setCc(array($society_CCEmail => $societyName));
				$message->setReplyTo(array($societyEmail => $societyName,$society_CCEmail => $societyName));
			 }
			 else
			 {		
				 $message->setReplyTo(array(
				   $societyEmail => $societyName
				));
			 }
			 
			$message->setSubject($mailSubject);
			$message->setBody($mailBody);
			$message->setFrom("no-reply@way2society.com", $obj_fetch->objSocietyDetails->sSocietyName);
			
			$message->setContentType("text/html");	
			 if($obj_fetch->objSocietyDetails->sSocietySendBillAsLink == 0)
			 {
				$message->attach(Swift_Attachment::fromPath($fileName));
			 }
			// Send the email
			$mailer = Swift_Mailer::newInstance($transport);
			$result = $mailer->send($message);
			
			if($result >= 1)
			{
				date_default_timezone_set('Asia/Kolkata');	
				$current_dateTime = date('Y-m-d H:i:s ');
				if($CronJobProcess)
				{
					$sql = "INSERT INTO `notification`(`UnitID`, `PeriodID`, `SentBillEmailDate`, `SentBy`) VALUES ('".$unitID."','".$periodID."','".$current_dateTime."','-1')";
				}
				else
				{
					$sql = "INSERT INTO `notification`(`UnitID`, `PeriodID`, `SentBillEmailDate`, `SentBy`) VALUES ('".$unitID."','".$periodID."','".$current_dateTime."','".$_SESSION['login_id']."')";
				}
				$obj_fetch->m_dbConn->insert($sql);
				$ResultAry[$unitID] = "Success";
				
			}
			else
			{
				$ResultAry[$unitID] = "Failed";
				
			}
			if($CronJobProcess)
			{
				$sqlUpdate = "Update `emailqueue` set `Status`=1 WHERE `id` = '".$QueueID."'"; 
				$dbConnRoot->update($sqlUpdate);
			}
				
		
		}
		else
		{
			$ResultAry[$unitID] = $EMailIDToUse['msg'];
		}
	}
		
	}
	catch(Exception $exp)
	{
		$ResultAry[$unitID] = $exp;
		return;
	}

}
	function SendEMail2($dbConn, $dbConnRoot, $CronJobProcess, $DBName, $societyID, $unitID, $periodID, $QueueID, &$ResultAry)
	{
		try
		{
			include_once ("include/fetch_data.php");
			$obj_fetch = new FetchData($dbConn);
			echo "<br/>after fectch data object";
			include_once("utility.class.php");
			$obj_Utility = new utility($dbConn, $dbConnRoot);
			echo "after utility object 1";
			include_once('../swift/swift_required.php');
			echo "cp0";
		
			if(isset($unitID) && isset($periodID))
			{
				$EncodeSocID = base64_encode($societyID);
				$EncodeUnitID = base64_encode($unitID);
				$url = "<a href='http://way2society.com/neft.php?SID=".$EncodeSocID."&UID=".$EncodeUnitID."'>Notify Society about NEFT Payment</a>";
				//$url = "<a href='http://localhost/way2society.com/neft.php?SID=".$EncodeSocID."'>View</a>";
				//-$mailBody .= $url;
				echo "cp1";
				$memberDetails = $obj_fetch->GetMemberDetails($unitID);
				echo "cp2";
				$societyDetails = $obj_fetch->GetSocietyDetails($obj_fetch->GetSocietyID($unitID));
				$mailToEmail = $obj_fetch->objMemeberDetails->sEmail;
				if($mailToEmail == '')
				{
					$ResultAry[$unitID] = "Email ID Missing";
					return;
				}
				else if(filter_var($mailToEmail, FILTER_VALIDATE_EMAIL) == false)
				{
					$ResultAry[$unitID] = "Incorrect Email ID  ".$mailToEmail." ";
					return;
				}
				echo "cp3";
				$mailToName = $obj_fetch->objMemeberDetails->sMemberName;
				$newUserUrl = "";
				$isNewAccountCreate = false;
				
				$loginExist = $dbConnRoot->select("SELECT * FROM `login` WHERE `member_id` = '".$mailToEmail."'");
				echo "cp3";
				$encryptedEmail = $obj_Utility->encryptData($mailToEmail);	
				if(sizeof($loginExist) == 0)		
				{			
					//$mappingDetails = $dbConnRoot->select("SELECT * FROM `mapping` WHERE `society_id` = '".$obj_fetch->GetSocietyID($unitID)."' AND `unit_id` = '".$unitID."' AND `status` = 1");
					//if(sizeof($mappingDetails) > 0)
					//{		
						//$encryptedEmail = $obj_Utility->encryptData($mailToEmail);					
						//$newUserUrl = "http://way2society.com/newuser.php?reg&u=".$mailToEmail."&n=".$mailToName."&c=".$mappingDetails[0]['code']."&tkn=".$encryptedEmail;
						$newUserUrl = "http://way2society.com/newuser.php?reg&u=".$mailToEmail."&n=".$mailToName."&tkn=".$encryptedEmail;
						$onclickURL = $newUserUrl.'&URL=http://way2society.com/Dashboard.php?View=MEMBER';
					//}
					echo "cp4";
				}
				else
				{
					$iSocietyId =  $obj_fetch->GetSocietyID($unitID);
					$mappingDetails = $dbConnRoot->select("SELECT * FROM `mapping` WHERE `society_id` = '".$iSocietyId."' AND `unit_id` = '".$unitID."' AND `login_id` = '".$loginExist[0]['login_id']."' ");		
					if(sizeof($mappingDetails) > 0)
					{	
							$onclickURL = "http://way2society.com/Dashboard.php?View=MEMBER";
					}
					else
					{
							echo "cp5";
							$mappingDetails = $dbConnRoot->select("SELECT * FROM `mapping` WHERE `society_id` = '".$iSocietyId."' AND `unit_id` = '".$unitID."' AND `status` = '1' ");		
							if(sizeof($mappingDetails) > 0)
							{	
								$sAccountActivationCode = $mappingDetails[0]['code'];
								$onclickURL = 'http://way2society.com/login.php?mCode='.$sAccountActivationCode.'&u='.$encryptedEmail;
							}
							else
							{
								echo "cp6";
								$sAccountActivationCode = getRandomUniqueCode();
								
								$arPaidToParentDetails = $obj_Utility->getParentOfLedger($unitID);
								if(!(empty($arPaidToParentDetails)))
								{
									$unitNo = $arPaidToParentDetails['ledger_name'];
								}
								
								$insert_mapping = "INSERT INTO `mapping`(`login_id`,`society_id`, `unit_id`, `desc`, `code`, `role`, `created_by`, `view`,`status`) VALUES ('" . $loginExist[0]['login_id'] . "','" . $iSocietyId . "', '" . $unitID . "', '" . $unitNo . "', '" . $sAccountActivationCode . "', '" . ROLE_MEMBER . "', '" . $loginExist[0]['login_id'] . "', 'MEMBER','2')";
								$result_mapping = $dbConnRoot->insert($insert_mapping);
								
								$onclickURL = 'http://way2society.com/login.php?mCode='.$sAccountActivationCode.'&u='.$encryptedEmail;
								echo "cp7";
							}
					}
				
				}
				
				if($newUserUrl <> "")
				{			
					$buttonValue = "My Account";	
					$isNewAccountCreate = true;
					$sText = "Please click below button to check your account balance, bills and previous payments. ";
					//$sText = "Your email id <".$mailToEmail."> is not registered to way2society.com yet. You can register now by clicking on ".$buttonValue.".";
					$neftURL = $newUserUrl.'&URL=http://way2society.com/Dashboard.php?View=MEMBER';		
				}
				else
				{			
					$buttonValue = "My Account";
					$sText = "Please click below button to check your account balance, bills and previous payments.";	
					$neftURL = 	'http://way2society.com/neft.php?SID='.$EncodeSocID.'&UID='.$EncodeUnitID;		
				}
				
				echo "cp8";
				$mailSubject = 'Bill For : ' . $obj_fetch->GetBillFor($periodID);//'Maintainance Bill For March';
				//$mailBody = 'Attached Maintainance Bill For ' . $obj_fetch->GetBillFor($_REQUEST["period"]) .'<br />';
				
				$mailBody = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
									   <table width="100%">
										<tr>
											<td>Dear '.$mailToName. ',<br /><br />Please find attached maintenance bill for ' . $obj_fetch->GetBillFor($periodID) .'.<br />';
									//if($isNewAccountCreate != true)
									{
										$mailBody .= ' 
										If you have made NEFT payment, please click below button to enter NEFT transaction details. </td>                   
										</tr>
										<tr><td><br /></td></tr>
										<tr>
											<td align="center">
												<table width="250px" cellspacing="0" cellpadding="0" border="0">
													<tbody>
														<tr>
															<td valign="middle" bgcolor="#337AB7" height="40" align="center">
																<a target="_blank" style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif" href="'.$neftURL.'">Enter NEFT transaction details</a>
															</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
										<tr><td><br /></td></tr>';
									}
									
									$mailBody .= '<tr>
											<td>'.$sText.'</td>									
										</tr>
										<tr><td><br /></td></tr>
										<tr>
											<td align="center">
												<table width="200px" cellspacing="0" cellpadding="0" border="0">
													<tbody>
														<tr>
															<td valign="middle" bgcolor="#337AB7" height="40" align="center" style="cursor:pointer;">
																<a href="'.$onclickURL.'"  style="cursor:pointer;text-decoration: none;">
																	<div style="cursor:pointer;text-decoration: none;" onClick=\'window.open("//'.$onclickURL.'");\' >
																		<font  style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif;"><b>'.$buttonValue.'</b></font>
																	</div>
																</a>
														  </td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
										<tr><td><br /></td></tr>
										<tr><td>If you are a new user, we will take you through a  simple process to create your account. </td></tr>
										<tr><td><br /></td></tr>
										<tr>
											<td font="colr:#999999;">Thank You,<br>Pavitra! <br />
											G-6, Shagun, Dindoshi, Malad East, Mumbai - 400 097 <br />
											Tel : 022 450 44 699 &nbsp;
											Mob : 09833765243 <br />
											Email : info@way2society.com <br /></td>
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
										 <td align="right">
										  <table border="0" cellpadding="0" cellspacing="0">
										   <tr>
											<td>
												<a href="https://twitter.com/pavitraglobal" target="_blank"><img src="http://way2society.com/images/icon2.jpg" alt=""></a>                  
											</td>
											<td style="font-size: 0; line-height: 0;" width="20">&nbsp;&nbsp;</td>
											<td>
												<a href="https://www.facebook.com/PavitraGlobalServicesLtd" target="_blank"><img src="http://way2society.com/images/icon1.jpg" alt=""></a>                 
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
								
				
			$unitNo = $obj_fetch->GetUnitNumber($unitID);
			$specialChars = array('/','.', '*', '%', '&', ',', '(', ')', '"');
			$unitNo = str_replace($specialChars,'',$unitNo);
			echo "cp9";
			$baseDir = dirname( dirname(__FILE__) );
			
			$fileName =  $baseDir . "/maintenance_bills/" . $obj_fetch->objSocietyDetails->sSocietyCode . "/" . $obj_fetch->GetBillFor($periodID) . "/bill-" . $obj_fetch->objSocietyDetails->sSocietyCode . '-' . $unitNo . "-" . $obj_fetch->GetBillFor($periodID) . '.pdf';
			
			if(!file_exists($fileName))
			{
				$ResultAry[$unitID] = "Bill PDF does not exist.";
				return;
			}
			echo "cp10";
			$EMailIDToUse = $obj_Utility->GetEmailIDToUse(true, 0, $periodID, $unitID, $CronJobProcess, $DBName, $societyID, 0, 0);
			if($EMailIDToUse['status'] == 0)
			{
				$EMailID = $EMailIDToUse['email'];
				$Password = $EMailIDToUse['password'];
				// Create the mail transport configuration
				$transport = Swift_SmtpTransport::newInstance('cs10.webhostbox.net', 465, "ssl")
					  ->setUsername($EMailID)
					  ->setSourceIp('0.0.0.0')
					  ->setPassword($Password) ;	 
				 
				// Create the message
				$message = Swift_Message::newInstance();
				$message->setTo(array(
				  $mailToEmail => $mailToName
				 ));
				 
				 $societyEmail = $obj_fetch->objSocietyDetails->sSocietyEmail;
				 if($societyEmail == '')
				 {
					 $societyEmail = "societyaccounts@pgsl.in";
					 $societyEmail = "rohit.shinde2010@gmail.com";
				 }
				 
				 $societyName = $obj_fetch->objSocietyDetails->sSocietyName;
						
				 $society_CCEmail = $obj_fetch->objSocietyDetails->sSocietyCC_Email;
				 if($society_CCEmail <> "")
				 {
					// $message->setCc(array($society_CCEmail => $societyName));
					$message->setReplyTo(array($societyEmail => $societyName,$society_CCEmail => $societyName));
				 }
				 else
				 {		
					 $message->setReplyTo(array(
					   $societyEmail => $societyName
					));
				 }
				 
				$message->setSubject($mailSubject);
				$message->setBody($mailBody);
				$message->setFrom("no-reply@way2society.com", $obj_fetch->objSocietyDetails->sSocietyName);
				
				$message->setContentType("text/html");	
				 
				$message->attach(Swift_Attachment::fromPath($fileName)->setDisposition('inline'));
				// Send the email
				$mailer = Swift_Mailer::newInstance($transport);
				$result = $mailer->send($message);
				echo "cp11";
				if($result >= 1)
				{
					date_default_timezone_set('Asia/Kolkata');	
					$current_dateTime = date('Y-m-d H:i:s ');
					if($CronJobProcess)
					{
						$sql = "INSERT INTO `notification`(`UnitID`, `PeriodID`, `SentBillEmailDate`, `SentBy`) VALUES ('".$unitID."','".$periodID."','".$current_dateTime."','-1')";
					}
					else
					{
						$sql = "INSERT INTO `notification`(`UnitID`, `PeriodID`, `SentBillEmailDate`, `SentBy`) VALUES ('".$unitID."','".$periodID."','".$current_dateTime."','".$_SESSION['login_id']."')";
					}
					$obj_fetch->m_dbConn->insert($sql);
					$ResultAry[$unitID] = "Success";
					
				}
				else
				{
					$ResultAry[$unitID] = "Failed";
					
				}
				if($CronJobProcess)
				{
					$sqlUpdate = "Update `emailqueue` set `Status`=1 WHERE `id` = '".$QueueID."'"; 
					$dbConnRoot->update($sqlUpdate);
				}
					
			
			}
			else
			{
				$ResultAry[$unitID] = $EMailIDToUse['msg'];
			}
		}
		
	}
	catch(Exception $exp)
	{
		$ResultAry[$unitID] = $exp;
		return;
	}

}		
?>