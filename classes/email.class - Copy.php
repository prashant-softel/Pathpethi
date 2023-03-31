<?php 
	include_once ("include/dbop.class.php");
	$dbConn = new dbop();
	
	include_once ("include/fetch_data.php");
	$obj_fetch = new FetchData($dbConn);

	require_once('../swift/swift_required.php');
	if(isset($_REQUEST['unit']) && isset($_REQUEST['period']))
	{
		$unitID = $_REQUEST['unit'];
		$periodID = $_REQUEST['period'];
		
		$mailSubject = 'Bill For : ' . $obj_fetch->GetBillFor($_REQUEST["period"]);//'Maintainance Bill For March';
		$mailBody = 'Attached Maintainance Bill For ' . $obj_fetch->GetBillFor($_REQUEST["period"]) .'<br />';
		$EncodeSocID = base64_encode($_SESSION['society_id']);
		$url = "<a href='http://way2society.com/neft.php?SID=".$EncodeSocID."'>Notify Society about NEFT Payment</a>";
		//$url = "<a href='http://localhost/way2society.com/neft.php?SID=".$EncodeSocID."'>View</a>";
		$mailBody .= $url;
		
		$memberDetails = $obj_fetch->GetMemberDetails($unitID);
		
		$societyDetails = $obj_fetch->GetSocietyDetails($obj_fetch->GetSocietyID($unitID));
		$mailToEmail = $obj_fetch->objMemeberDetails->sEmail;
		if($mailToEmail == '')
		{
			echo 'Email ID Missing';
			exit();
		}
		
		$mailToName = $obj_fetch->objMemeberDetails->sMemberName;
		
		$unitNo = $obj_fetch->GetUnitNumber($unitID);
		
		$baseDir = dirname( dirname(__FILE__) );
		
		$fileName =  $baseDir . "/maintenance_bills/" . $obj_fetch->objSocietyDetails->sSocietyCode . "/" . $obj_fetch->GetBillFor($_REQUEST["period"]) . "/bill-" . $obj_fetch->objSocietyDetails->sSocietyCode . '-' . $unitNo . "-" . $obj_fetch->GetBillFor($_REQUEST["period"]) . '.pdf';
		
		if(!file_exists($fileName))
		{
			echo 'Bill PDF does not exist.';
			exit();
		}
		// Create the mail transport configuration
		//$transport = Swift_SmtpTransport::newInstance('md-in-1.webhostbox.net', 465, "ssl")
		$transport = Swift_SmtpTransport::newInstance('md-in-1.webhostbox.net', 465, "ssl")
			  ->setUsername('no-reply@way2society.com')
			  ->setPassword('society123') ;	 
		 
		// Create the message
		$message = Swift_Message::newInstance();
		$message->setTo(array(
		  $mailToEmail => $mailToName
		 ));
		 
		 $societyEmail = $obj_fetch->objSocietyDetails->sSocietyEmail;
		 if($societyEmail == '')
		 {
			 $societyEmail = "societyaccounts@pgsl.in";
		 }
		 
		 $societyName = $obj_fetch->objSocietyDetails->sSocietyName;
		 		
		 $message->setReplyTo(array(
		   $societyEmail => $societyName
		));
		 
		$message->setSubject($mailSubject);
		$message->setBody($mailBody);
		$message->setFrom("no-reply@way2society.com", $obj_fetch->objSocietyDetails->sSocietyName);
		
		$message->setContentType("text/html");	
		 
		$message->attach(Swift_Attachment::fromPath($fileName));
		// Send the email
		$mailer = Swift_Mailer::newInstance($transport);
		$result = $mailer->send($message);
				
		if($result >= 1)
		{
			date_default_timezone_set('Asia/Kolkata');	
			$current_dateTime = date('Y-m-d h:i:s ');
			$sql = "INSERT INTO `notification`(`UnitID`, `PeriodID`, `SentBillEmailDate`, `SentBy`) VALUES ('".$unitID."','".$periodID."','".$current_dateTime."','".$_SESSION['login_id']."')";			
			$obj_fetch->m_dbConn->insert($sql);
			echo 'Success';
		}
		else
		{
			echo 'Failed';
		}
	}
	else
	{
		echo 'Missing Parameters';
	}
?>