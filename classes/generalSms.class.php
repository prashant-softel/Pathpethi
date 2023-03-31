<?php 
	include_once ("include/dbop.class.php");
	if(isset($_REQUEST['SentSMSManually']) )
	{
		$dbConn = new dbop();
		
		$ResultAry  = array();
		if(isset($_REQUEST['unitsArray']))
		{
			$Units = "";
			$UnitsCollection = $_REQUEST['unitsArray'];
			$Units = json_decode($UnitsCollection);	
		
			$msg = $_REQUEST['msgBody'];
			
			foreach($Units as $UnitNumber)
			{
				if($UnitNumber <> "")
				{
			
					SendGeneralSMS($dbConn, 0, $ResultAry, $UnitNumber, $msg);
				}
			}
			echo json_encode($ResultAry);
		}
		else if($_REQUEST['GetDeliveryReport'])
		{
			echo "inside get delivery report";
			SendGeneralSMS($dbConn, 0, $ResultAry);
		}
		
	}
	
	function SendGeneralSMS($dbConn, $bCronjobProcess, &$ResultAry, $unitID, $msg)
	{
		include_once("utility.class.php");
		$dbConnRoot = new dbop(true);
		$objUtility  = new utility($dbConn,$dbConnRoot);
		include_once ("include/fetch_data.php");
		$obj_fetch = new FetchData($dbConn);
		include_once ("dbconst.class.php");
		
		if(isset($unitID) && isset($msg))
		{
			$memberDetails = $obj_fetch->GetMemberDetails($unitID);					
			
			$smsDetails = $obj_fetch->GetSMSDetails($obj_fetch->GetSocietyID($unitID));								
			
			$mobile = $obj_fetch->objMemeberDetails->sMobile;		
			
			$msgBody = $msg;		
			
			if($mobile <> "")
			{		
				//$msgBody = $smsDetails[0]['sms_start_text']. ', '.$msg.' '.$smsDetails[0]['sms_end_text'];
				
				
				//$sendSMS = "http://sms.surewingroup.info/SendSMS/sendmsg.php?uname=pavitr&pass=d$9Zx$0I&send=PAVITR&dest=" . $mobile . "&msg=" . $msgBody;
				$sendSMS = "http://sms.transaction.surewingroup.info/submitsms.jsp?user=waysoc&key=7009e8caf1XX&mobile=".$mobile."&message=".$msgBody."&senderid=waysoc&accusage=1";
				
				$ResultAry[$unitID] =  $sendSMS;
			}
			else
			{
				$ResultAry[$unitID] =  "Empty";
			}
			$response = $objUtility->SendSMS($mobile, $msgBody,$_SESSION['society_client_id']);
		
			$status = "";		
			/*$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $sendSMS);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$response = curl_exec($ch);
			curl_close($ch);*/
			
			
			$ResultAry[$unitID] =  $response;
			$status = explode(',',$response);		
			date_default_timezone_set('Asia/Kolkata');	
			$current_dateTime = date('Y-m-d H:i:s ');
			$login_id  = "";
			if(!$bCronjobProcess)
			{
				$login_id  =  $_SESSION['login_id'];
			}
			//$sql = "INSERT INTO `notification`(`UnitID`, `SentBillSMSDate`, `SentBy`) VALUES ('".$unitID."','".$current_dateTime."','".$_SESSION['login_id']."')";			
			$sql = "INSERT INTO `generalsms_log`(`UnitID`, `SentGeneralSMSDate`, `MessageText`, `SentBy`, `SentReport`, `status`) VALUES ('".$unitID."','".$current_dateTime."','". $obj_fetch->m_dbConn->escapeString($msgBody) ."','".$login_id."', '".$response."', '".$status[1]."')";
			$obj_fetch->m_dbConn->insert($sql);								
		}
		else if(isset($_REQUEST['GetDeliveryReport']) && isset($_REQUEST['MessageID']) && isset($_REQUEST['TableID']))
		{
			$messageID = $_REQUEST['MessageID'];
			$tableID = $_REQUEST['TableID'];
			if( isset($_REQUEST['Type']) && $_REQUEST['Type'] == "0")
			{
				$SelectQuery = "select * from `generalsms_log` WHERE `ID`='".$tableID."'";
				$SQLResult = $obj_fetch->m_dbConn->select($SelectQuery);
				
				if($SQLResult[0]["DeliveryStatus"] == "")
				{
					$DeliveryStatus = "";
					$response = $objUtility->GetSMSDeliveryReport($messageID,$_SESSION['society_client_id'] );
					$statusArray = explode(',',$response);
					
					if(is_array($statusArray))
					{
						if (in_array('"Pending DR not Found"', $statusArray) || in_array('"DR not Found"', $statusArray))
						{
							$DeliveryStatus = "Delivered";
						}
						else
						{
							$DeliveryStatus = $statusArray[4];
						}
					}
					// $UpdateQuery = "UPDATE `generalsms_log` SET `DeliveryReport`='".$response."',`DeliveryStatus`='".$status[1]."' WHERE `ID`='".$tableID."'";
					echo $UpdateQuery = "UPDATE `generalsms_log` SET `DeliveryReport`='". $obj_fetch->m_dbConn->escapeString($response) ."',`DeliveryStatus`='". $obj_fetch->m_dbConn->escapeString($DeliveryStatus)."' WHERE `ID`='".$tableID."'";
					$obj_fetch->m_dbConn->update($UpdateQuery);
					//echo $response;
				}
			}
			else if( isset($_REQUEST['Type']) && $_REQUEST['Type'] <> "0")
			{
				$SelectQuery = "select * from `notification` WHERE `ID`='".$tableID."'";
				$SQLResult = $obj_fetch->m_dbConn->select($SelectQuery);
			
				if($SQLResult[0]["DeliveryStatus"] == "")
				{
					$DeliveryStatus = "";
					$response = $objUtility->GetSMSDeliveryReport($messageID,$_SESSION['society_client_id'] );
					$statusArray = explode(',',$response);
					
					if(is_array($statusArray))
					{
						if (in_array('"Pending DR not Found"', $statusArray) || in_array('"DR not Found"', $statusArray))
						{
							$DeliveryStatus = "Delivered";
						}
						else
						{
							$DeliveryStatus = $statusArray[4];
						}
					}
					
					$UpdateQuery = "UPDATE `notification` SET `DeliveryReport`='".$response."',`DeliveryStatus`='".$DeliveryStatus."' WHERE `ID`='".$tableID."'";
					$obj_fetch->m_dbConn->update($UpdateQuery);
					
				}
			}
		}
		else
		{
			echo 'Missing Parameters';
		}
	}
?>