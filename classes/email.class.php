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
		include_once ("include/fetch_data.php");
		$obj_fetch = new FetchData($dbConn);
		include_once("utility.class.php");
		$obj_Utility = new utility($dbConn, $dbConnRoot);
		require_once('../swift/swift_required.php');
		
		if(isset($unitID) && isset($periodID))
		{
			$EncodeSocID = base64_encode($societyID);
			$EncodeUnitID = base64_encode($unitID);
			$EncodePeriodID = base64_encode($periodID);
			$EncodeDbName = base64_encode($DBName);
			
			$url = "<a href='http://way2society.com/neft.php?SID=".$EncodeSocID."&UID=".$EncodeUnitID."'>Notify Society about NEFT Payment</a>";
			//$url = "<a href='http://localhost/way2society.com/neft.php?SID=".$EncodeSocID."'>View</a>";
			//-$mailBody .= $url;
			
			$memberDetails = $obj_fetch->GetMemberDetails($unitID);
			
			$societyDetails = $obj_fetch->GetSocietyDetails($obj_fetch->GetSocietyID($unitID));
			$mailToEmail = $obj_fetch->objMemeberDetails->sEmail;
			$OtherMembers = $obj_fetch->objMemeberDetails->arListOfMembers;
			$OtherTenants = $obj_fetch->objMemeberDetails->arListOfTenants;

			if(sizeof($OtherTenants) > 0)
			{
				array_push($OtherMembers, $OtherTenants[0]);
			}
			
			if(sizeof($OtherMembers) == 0)
			{
				$ResultAry[$unitID] = "Subscribe to society email checkbox is not set";
				return;
			}

			foreach($OtherMembers as $OtherMemberEmails)
			{
				$mailToEmail = $OtherMemberEmails[1];
				//echo $mailToEmail;
				//die();
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
				
				$socContactNo = $obj_fetch->objSocietyDetails->sSocietyEmailContactNo;
				$socAddress = $obj_fetch->objSocietyDetails->sSocietyAddress;
				$showSocietyAddressInEmail = $obj_fetch->objSocietyDetails->bSocietyAddressInEmail;
				$mailToName = $OtherMemberEmails[0];
				$newUserUrl = "";
				$isNewAccountCreate = false;
				
				$loginExist = $dbConnRoot->select("SELECT * FROM `login` WHERE `member_id` = '".$mailToEmail."'");
				$sqlClientDetails = "SELECT * FROM `client` WHERE `id` = '".$_SESSION['society_client_id']."'";
				//$ResultAry[$unitID] = $sqlClientDetails;
				$ClientDetails = $dbConnRoot->select($sqlClientDetails);
				$EmailFooter=$ClientDetails[0]['email_footer'];
				if($showSocietyAddressInEmail==1)
				{
					$EmailFooter =$socAddress;
				}
				if(isset($ClientDetails))
				{
					//echo "invalid client details";
				}
				
				$encryptedEmail = $obj_Utility->encryptData($mailToEmail);	
				if(sizeof($loginExist) == 0)		
				{
					$iSocietyId =  $obj_fetch->GetSocietyID($unitID);
					$mapping_code = '';			
					$mappingDetails = $dbConnRoot->select("SELECT * FROM `mapping` WHERE `society_id` = '".$iSocietyId."' AND `unit_id` = '".$unitID."' AND `status` = 1");
					if(sizeof($mappingDetails) == 0)
					{
						//$sAccountActivationCode = getRandomUniqueCode();
						$sCode = getRandomUniqueCode();
						//echo "code:".$sCode;
						//echo "email :".$mailToEmail;
						if($mailToEmail <> '')
						{
						 	$sAccountActivationCode=substr(($sCode),0, 4);
							$sActivCode=$sAccountActivationCode;
							//echo "acive for mail:" .$sActivCode;
							$sAccountActivationCode=$mailToEmail . $sActivCode;									
							//echo "sAccountActivationCode:".$sAccountActivationCode;
						}
						else
						{
							$sAccountActivationCode=$sCode;
						}		
						$arPaidToParentDetails = $obj_Utility->getParentOfLedger($unitID);
						if(!(empty($arPaidToParentDetails)))
						{
							$unitNo = $arPaidToParentDetails['ledger_name'];
						}
						if($mailToEmail <> '')
						{
						 $codeType=1;	
						}
						else
						{
							$codeType=0;
						}
						$insert_mapping = "INSERT INTO `mapping`(`society_id`, `unit_id`, `desc`, `code`,`code_type`, `role`, `created_by`, `view`,`status`) VALUES ('" . $iSocietyId . "', '" . $unitID . "', '" . $unitNo . "', '" . $sAccountActivationCode . "','".$codeType."', '" . ROLE_MEMBER . "', '" . $_SESSION['login_id'] . "', 'MEMBER','1')";
						$result_mapping = $dbConnRoot->insert($insert_mapping);

						$mapping_code = $sAccountActivationCode;
					}
					else
					{
						$mapping_code = $mappingDetails[0]['code'];
					}
					//{		
						//$encryptedEmail = $obj_Utility->encryptData($mailToEmail);					
						//$newUserUrl = "http://way2society.com/newuser.php?reg&u=".$mailToEmail."&n=".$mailToName."&c=".$mappingDetails[0]['code']."&tkn=".$encryptedEmail;
						//$newUserUrl = "http://way2society.com/newuser.php?reg&u=".$mailToEmail."&n=".$mailToName."&tkn=".$encryptedEmail;
						$newUserUrl = "http://way2society.com/newuser.php?reg&u=".$mailToEmail."&n=".$mailToName."&tkn=".$encryptedEmail."&c=".$mapping_code;
						
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
								$sCode = getRandomUniqueCode();
								//echo "code:".$sCode;
								//echo "email :".$mailToEmail;
								if($mailToEmail <> '')
								{
						 			$sAccountActivationCode=substr(($sCode),0, 4);
									$sActivCode=$sAccountActivationCode;
									//echo "acive for mail:" .$sActivCode;
									$sAccountActivationCode=$mailToEmail . $sActivCode;									
									//echo "sAccountActivationCode:".$sAccountActivationCode;
								}
								else
								{
									$sAccountActivationCode=$sCode;
								}									
									$arPaidToParentDetails = $obj_Utility->getParentOfLedger($unitID);
								if(!(empty($arPaidToParentDetails)))
								{
									$unitNo = $arPaidToParentDetails['ledger_name'];
								}
								if($mailToEmail <> '')
								{
						 			$codeType=1;	
								}
								else
								{
									$codeType=0;
								}
								$insert_mapping = "INSERT INTO `mapping`(`society_id`, `unit_id`, `desc`, `code`,`code_type`, `role`, `created_by`, `view`,`status`) VALUES ('" . $iSocietyId . "', '" . $unitID . "', '" . $unitNo . "', '" . $sAccountActivationCode . "','".$codeType."', '" . ROLE_MEMBER . "', '" . $_SESSION['login_id'] . "', 'MEMBER','1')";
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
									//{
										if($obj_fetch->objSocietyDetails->sSocietySendBillAsLink == "1")
									{
										$mailBody .= '<tr>
												<td>Please click below button to check '.$sBillTypeText.'  Bill for ' . $obj_fetch->GetBillFor($periodID).'</td>									
											</tr>
											<tr><td><br /></td></tr>';
											//if($newUserUrl <> "")
											//{
										//$mailBody .='<br /><br /><tr  align="center"><td colspan="3">
                                       /*<table style="height:50px;"><tr style="background-color:#f8f8f8; height:40px;">
                                       <td align="center" style="width:160px;"><span style="font-size:16px">Access Code :</span></td>
                                       <td align="center" style="background-color:rgb(217, 237, 247);width: 200px;height: 40px;"><span style="font-size:16px">'.$sActivCode.'</span></td>
                                       </tr></table></td></tr><tr><td><br></td></tr>';*/	
											//}
											$mailBody .= '<tr>
												<td align="center">
													<table width="200px" cellspacing="0" cellpadding="0" border="0">
														<tbody>
															<tr>
																<td valign="middle" bgcolor="#337AB7" height="40" align="center" style="cursor:pointer;">
																	<a href="'.$userURL.'"  style="cursor:pointer;text-decoration: none;">
																		<div style="cursor:pointer;text-decoration: none;" onClick=\'window.open("//'.$userURL.'");\' >
																			<font  style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif;"><b>View Bill</b></font>
																		</div>
																	</a>
															  </td>
															</tr>
														</tbody>
													</table>
												</td>
											</tr>
											<tr><td><br /></td></tr>';
											if($newUserUrl <> "")
											{
											$mailBody .='<br /><br /><tr><td>Alternativaly, You can install our Android mobile App "Way2Society" from Google Playstore using following Activation Code when prompted during installation.</td></tr><tr><td>Your username is your email id :  '.$mailToEmail.' and you can set password of your choice. </td></tr><tr  align="center"><td colspan="3">
                                       		<table style="height:50px;"><tr style="background-color:#f8f8f8; height:40px;">
                                      		<td align="center" style="width:160px;"><span style="font-size:16px">Access Code :</span></td>
                                       <td align="center" style="background-color:rgb(217, 237, 247);width: 200px;height: 40px;"><span style="font-size:16px">'.$sActivCode.'</span></td><td>&nbsp;&nbsp;&nbsp;</td><td><a rel="nofollow" target="_blank" href="https://play.google.com/store/apps/details?id=com.ionicframework.way2society869487&amp;rdid=com.ionicframework.way2society869487">
										<img src="http://way2society.com/images/app.png" width="120" height="50" style="" class="yiv1843970569ycb7204091656"></a></td>
                                       </tr></table></td></tr><tr><td><br></td></tr>';
											}
									}
										$mailBody .= '<tr><td>If you have made NEFT payment, please click below button to enter NEFT transaction details. </td>                   
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
										<tr><td align="center">
                                            <table style="background-color:#f8f8f8; padding-top:10px; padding-bottom:10px;" width="100%">
                                            <tr ><td align="center"><span style="font-size:14px;font-weight:bold">NEFT Payment</span>
                                            </td>
                                            <td align="center"><span style="font-size:14px;font-weight:bold">Check Maintenance Bill</span>
                                            </td></tr>
                                            <tr><td  align="center">
											<video poster="../images/images.png" width="100%" height="50%" controls="controls">
											<a href="https://www.youtube.com/watch?v=-KNLJ44TB8Q" >
											<img src="http://way2society.com/images/NEFT.jpg" width="250px" height="140px" alt="image instead of video" />
											</a>
											</video>
											</td>
                                            <td  align="center">
											<video poster="../images/images.png" width="100%" height="50%" controls="controls">
											<a href="https://www.youtube.com/watch?v=38RXe5k-fFM&t=4s" >
										<img src="http://way2society.com/images/VIEW_BILLS.jpg" width="250px" height="140px" alt="image instead of video" />
											</a>
											</video>
											</td></tr>
                                            </table></td></tr>
										<tr><td><br /></td></tr>';
										if($newUserUrl <> "")
										{
										$mailBody .='<tr><td>If you are a new user, we will take you through a simple process to create your account. </td></tr>';
										}
										$mailBody .='<tr><td><br /></td></tr>
										<tr><td>'.$EmailFooter.'</td></tr>
										<tr><td>Email : '.$societyEmailID .'</td></tr>';
										if($_SESSION['society_client_id'] == "1" || $_SESSION['society_client_id'] == "9")
										{
											 $mailBody .= '<tr><td>Contact No : '. $socContactNo .'</td></tr>';	
										}
										else if(strlen($socContactNo) > 0)
										{
											 $mailBody .= '<tr><td>Contact No : '. $socContactNo .'</td></tr>';	
										}
										$mailBody .= '<tr>
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
									 <td bgcolor="#CCCCCC" style="padding: 2px 20px 2px 20px;border-top:none;">
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

			/*echo '<pre>';
			print_r($EMailIDToUse);
			echo '</pre>';*/

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
					//$societyEmail = "societyaccounts@pgsl.in";
					// $societyEmail = "rohit.shinde2010@gmail.com";
					$societyEmail = "techsupport@way2society.com";
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
						$sql = "INSERT INTO `notification`(`UnitID`, `PeriodID`, `SentBillEmailDate`, `SentBy`, `Mem_Other_ID`) VALUES ('" . $unitID . "','" . $periodID . "','" . $current_dateTime . "','-1', '" . $OtherMemberEmails[2] . "')";
					}
					else
					{
						$sql = "INSERT INTO `notification`(`UnitID`, `PeriodID`, `SentBillEmailDate`, `SentBy`, `Mem_Other_ID`) VALUES ('" . $unitID . "','" . $periodID . "','" . $current_dateTime . "','" . $_SESSION['login_id'] . "', '" . $OtherMemberEmails[2] . "')";
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