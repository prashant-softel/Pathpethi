<?php 
	if(isset($_REQUEST['SentSMSManually']) && $_REQUEST['SentSMSManually'] == 1)
	{
		include_once ("include/dbop.class.php");
		$dbConn = new dbop();
		
		//$unitID = $_REQUEST['unit'];
		$UnitsCollection = $_REQUEST['unitsArray'];
		$periodID = $_REQUEST['period'];
		
		$Units = "";
		if(isset($_REQUEST['unitsArray']))
		{
			$Units = json_decode($UnitsCollection);	
		}
		
		$ResultAry  = array();
		foreach($Units as $UnitNumber)
		{
			if($UnitNumber <> "")
			{
				SendSMS($dbConn, 0, $UnitNumber, $periodID, $ResultAry);
			}
		}
		//print_r($ResultAry);
		echo json_encode($ResultAry);
	}
	
	function SendSMS($dbConn, $bCronjobProcess, $unitID, $periodID, &$ResultAry)
	{
		try
		{
			if(isset($unitID) && isset($periodID))
			{
				
				include_once ("include/fetch_data.php");
				$obj_fetch = new FetchData($dbConn);
				

				$dbConnRoot = new dbop(true);
				
				include_once("utility.class.php");
				$objUtility  = new utility($dbConn, $dbConnRoot);
						
				include_once ("dbconst.class.php");
				
				//$mailSubject = 'Bill For : ' . $obj_fetch->GetBillFor($unitID);//'Maintainance Bill For March';
				//$mailBody = 'Attached Maintainance Bill For ' . $obj_fetch->GetBillFor($periodID);
				
				//$mailBody = 'Dear Member your bill for Unit No. <unitno> for ' . $obj_fetch->GetBillFor($_REQUEST["period"]) . 'has been generated, due amount is Rs. <amount>, Please pay on or before <due date> to avoid interest.';
				
				$memberDetails = $obj_fetch->GetMemberDetails($unitID);
				
				//$mailToName = $obj_fetch->objMemeberDetails->sMemberName;
				
				$unitNo = $obj_fetch->GetUnitNumber($unitID);		
				
				$smsDetails = $obj_fetch->GetSMSDetails($obj_fetch->GetSocietyID($unitID));
				
				$iBillType = 2;
				if(isset($_REQUEST['BT']))
				{
					$iBillType = $_REQUEST['BT'];
				}

				$totalBillPayable = $obj_fetch->GetTotalBillPayable($periodID, $unitID, $iBillType);
				
				$societyID = $obj_fetch->GetSocietyDetails($obj_fetch->GetSocietyID($unitID));
				$societyName = $obj_fetch->objSocietyDetails->sSocietyName;
				$societyCode = $obj_fetch->objSocietyDetails->sSocietyCode;
				
				$mobile = $obj_fetch->objMemeberDetails->sMobile;
				
				//$detail_values = $obj_fetch->GetValuesFromBillDetails($unitID,$periodID);				
						
				/*$bill_dates = $obj_fetch->GetBillDate($obj_fetch->GetSocietyID($unitID), $periodID);
				
				if($bill_dates <> '')
				{
					$BillDate = getDisplayFormatDate($bill_dates[0]['BillDate']);
					$DueDate = getDisplayFormatDate($bill_dates[0]['DueDate']);
				}*/
				
				//$mailBody = 'Dear Member your bill for Unit No. <' . $unitNo . '> for ' . $obj_fetch->GetBillFor($_REQUEST["period"]) . 'has been generated, due amount is Rs. <amount>, Please pay on or before <due date> to avoid interest.';
				
				//$mailBody = 'Dear Member, Maintenance Bill Generated for June,2015 for your flat 1421, Please pay before due date. (Raheja F CHS Ltd)';
				
				//$mailBody = 'Dear Member, Maintenance Bill Generated for ' . $obj_fetch->GetBillFor($_REQUEST["period"]) . ' for your flat ' . $unitNo . ', Please pay before due date. (Raheja F CHS Ltd)';
				
				//$mailBody = 'Dear Member, your bill of ' . $obj_fetch->GetBillFor($_REQUEST["period"]) . ' has been generated, due amount is Rs.' . $detail_values[0]['TotalBillPayable'] . '/-,pl pay on or before ' . $DueDate . '  to avoid interest.';
				
				//$mailBody = 'Dear Member, Maintenance Bill Generated for ' . $obj_fetch->GetBillFor($_REQUEST["period"]) . ' for your flat ' . $unitNo . ' ,Please pay before due date.' . $societyName;
				if($mobile <> "")
				{		
					$sBillTypeText = 'Maintenance';
					if(isset($_REQUEST['BT']) && $_REQUEST['BT'] == 1)
					{
						$sBillTypeText = 'Supplementary';
					}
					$mailBody = $smsDetails[0]['sms_start_text']. ', ' . $sBillTypeText . ' Bill generated of ' . $obj_fetch->GetBillFor($periodID) . ', Amount due of Rs.' . $totalBillPayable .'/- Unit No. '. $unitNo .', '.$societyCode.' '.$smsDetails[0]['sms_end_text'];		
					$smsText = $smsDetails[0]['sms_start_text']. ', ' . $sBillTypeText . ' Bill generated of ' . $obj_fetch->GetBillFor($periodID) . ', Amount due of Rs. totalBillPayableField /- Unit No. unitNoField ,' .$societyCode.' '.$smsDetails[0]['sms_end_text'];		
					
					//$sendSMS = "http://sms.surewingroup.info/SendSMS/sendmsg.php?uname=pavitr&pass=d$9Zx$0I&send=PAVITR&dest=" . $mobile . "&msg=" . $mailBody;
					//$sendSMS = "http://sms.surewingroup.info/SendSMS/sendmsg.php?uname=waysoc&pass=abc@123&send=PAVITR&dest=" . $mobile . "&msg=" . $mailBody;
					//$sendSMS = "http://sms.transaction.surewingroup.info/submitsms.jsp?user=waysoc&key=7009e8caf1XX&mobile=".$mobile."&message=".$mailBody."&senderid=waysoc";
					$sendSMS = "http://sms.transaction.surewingroup.info/submitsms.jsp?user=waysoc&key=7009e8caf1XX&mobile=".$mobile."&message=".$mailBody."&senderid=waysoc&accusage=1";
					
					
					$ResultAry[$unitID] = $objUtility->SendSMS($mobile, $mailBody,$_SESSION['society_client_id']);
					
					//$ResultAry[$unitID] = "test";
					
					date_default_timezone_set('Asia/Kolkata');	
					$current_dateTime = date('Y-m-d H:i:s ');
					$loginID = "-1";
					if(!$bCronjobProcess)
					{
						$loginID = $_SESSION['login_id'];
					}
					$sql = "INSERT INTO `notification`(`UnitID`, `PeriodID`, `SentBillSMSDate`, `SentBy`,`SMSSentReport`,`Bill_Amount`,`BillType`) VALUES ('".$unitID."','".$periodID."','".$current_dateTime."','".$loginID."','".$ResultAry[$unitID]."','".$totalBillPayable."','".$_REQUEST['BT']."')";			
					$dbConn->insert($sql);
					
					$sqlFetch = "Select Count(*) as cnt from `rsmsdetails` where `PeriodID` = '".$periodID."' and  `sms` = '".$dbConn->escapeString($smsText)."' and `sms_type` =1";			
					 $data = $dbConn->select($sqlFetch);
					if($data[0]['cnt'] == 0)
					{
						$res2 = "INSERT INTO `rsmsdetails`(`PeriodID`, `sms`,`sms_type`,`timestamp`) VALUES ('".$periodID."','".$dbConn->escapeString($smsText)."','1','".date('Y-m-d H:i:s ')."')";
						$dbConn->insert($res2);
					}
				}
				else
				{
					$ResultAry[$unitID] = "Empty";
				}
				
				
				
				
				
				
				/*$response = file_get_contents($sendSMS);
				echo $response;*/
				
				/*$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $sendSMS);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				$response = curl_exec($ch);
				curl_close($ch);
				echo $response;*/
			}
			else
			{
				$ResultAry[$unitID] =  'Missing Parameters';
			}
		}
		catch(Exception $exp)
		{
			$ResultAry[$unitID] = "Error";
			return;
		}
	}
?>