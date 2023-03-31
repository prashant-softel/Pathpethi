<?php
include_once("utility.class.php");
include_once("view_ledger_details.class.php");
include_once("dbconst.class.php");
include_once ("include/fetch_data.php");
include_once('../swift/swift_required.php');
class unit_report
{
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_utility;
	public $obj_fetch;
	public $obj_ledger_details;

	function __construct($dbConn, $dbConnRoot = "")
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->obj_utility = new utility($this->m_dbConn, $this->m_dbConnRoot);
		$this->obj_ledger_details=new view_ledger_details($this->m_dbConn);
		$this->obj_fetch = new FetchData($this->m_dbConn);
	}
	
	public function show_owner_name($uid,$to = "")
	{
	  	  
		  
		  if($to <> "")
		  {
			  $memberIDS = $this->obj_utility->getMemberIDs($to);	
			 $sql="select owner_name,resd_no,mob,email,unittbl.unit_no,wingtbl.wing from member_main as membertbl JOIN `unit` as unittbl on unittbl.unit_id=membertbl.unit JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id where membertbl.unit='".$uid."'  and  membertbl.member_id IN (".$memberIDS.") Group BY membertbl.unit  ";
		  }
		  else
		  {
			  $memberIDS = $this->obj_utility->getMemberIDs($_SESSION['default_year_end_date']);	
			 $sql="select owner_name,resd_no,mob,email,unittbl.unit_no,wingtbl.wing from member_main as membertbl JOIN `unit` as unittbl on unittbl.unit_id=membertbl.unit JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id where membertbl.unit='".$uid."'  and  membertbl.member_id IN (".$memberIDS.") Group BY membertbl.unit  ";
		 }
		  $data=$this->m_dbConn->select($sql);
		  return $data;
	
	  
	}
	
	
	public function show_due_details($uid,$from = "",$to ="")
	{	  	  
	 // $sql = "select assettbl.Date, ledgertbl.ledger_name as Particular, Debit, Credit,VoucherID,VoucherTypeID from `assetregister` as assettbl JOIN `ledger` as ledgertbl on assettbl.LedgerID=ledgertbl.id where assettbl.LedgerID='".$uid."' and assettbl.Is_Opening_Balance= 0";
	//$sql="select assettbl.Date, ledgertbl.ledger_name as Particular,bdetail.BillSubTotal,bdetail.AdjustmentCredit,bdetail.BillInterest,assettbl.Debit, assettbl.Credit,bdetail.PrincipalArrears, bdetail.InterestArrears,assettbl.VoucherID,assettbl.VoucherTypeID,v.ID,v.RefNo from `assetregister` as assettbl JOIN `ledger` as ledgertbl on assettbl.LedgerID=ledgertbl.id join voucher as v on v.ID=assettbl.VoucherID join billdetails as bdetail on v.RefNo=bdetail.ID where assettbl.LedgerID='".$uid."' and assettbl.Is_Opening_Balance= 0";
	$sql ="select assettbl.Date, ledgertbl.ledger_name as Particular,bdetail.BillSubTotal,bdetail.AdjustmentCredit,bdetail.BillInterest,assettbl.Debit, assettbl.Credit,bdetail.PrincipalArrears, bdetail.InterestArrears,assettbl.VoucherID,assettbl.VoucherTypeID,v.ID,v.RefNo from `assetregister` as assettbl JOIN `ledger` as ledgertbl on assettbl.LedgerID=ledgertbl.id  left join voucher as v on v.ID=assettbl.VoucherID left join billdetails as bdetail on v.RefNo=bdetail.ID where assettbl.LedgerID='".$uid."' and assettbl.Is_Opening_Balance= 0";
	  	if($from <> "" && $to <> "")
		{
			$sql .= "  and assettbl.Date BETWEEN '".$from."' AND '".$to."'";		
		}
		else if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
	   {
		   $sql .= "  and assettbl.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
	   }
	   $sql .= " ORDER BY assettbl.Date";
	   $data = $this->m_dbConn->select($sql);
	   return $data;
	}
	
	public function getParticularDetails($uid,$voucherdate)
	{
		$aryParent = $this->obj_utility->getParentOfLedger($uid);
		$res45 = $this->details($aryParent['group'],$uid,$voucherdate);	
		if($res45[0]['VoucherID'] == 0)
		{
			 return 'Opening Balanace';
		}
		else
		{
			$res46 = $this->details2($uid,$res45[0]['VoucherID'],$res45[0]['VoucherTypeID']);
			return $res46;
		 }
	}
	
	public function details2($lid, $vid, $vtype = 0, $debit = 0, $credit = 0,  $byORto = "")
	{		
		$sql1 = "select `desc` from `vouchertype` where id='".$vtype."'";		
		$data1 = $this->m_dbConn->select($sql1);
		$voucher = $data1[0]['desc'];		
		$sql2 = "select RefNo,RefTableID,VoucherNo from `voucher` where id='".$vid."' ";
	
		$data2 = $this->m_dbConn->select($sql2);
		$RefNo = $data2[0]['RefNo'];
		$RefTableID = $data2[0]['RefTableID'];
		$VoucherNo = $data2[0]['VoucherNo'];	
		if($byORto <> "" && $voucher == "Journal Voucher")
		{	
			 $sql3 = "select ledgertbl.id,`ledger_name`,vouchertbl.Note,vouchertbl.RefNo,vouchertbl.VoucherNo,vouchertbl.".$byORto." as 'To' from `voucher` as vouchertbl JOIN `ledger` as ledgertbl on vouchertbl.".$byORto."=ledgertbl.id where vouchertbl.RefNo='".$RefNo."' and vouchertbl.RefTableID='".$RefTableID."' and vouchertbl.VoucherNo='".$VoucherNo."'";		
		}
		else
		{
		 	$sql3 = "select ledgertbl.id,`ledger_name`,vouchertbl.Note,vouchertbl.RefNo,vouchertbl.VoucherNo,vouchertbl.To as 'To' from `voucher` as vouchertbl JOIN `ledger` as ledgertbl on vouchertbl.To=ledgertbl.id where vouchertbl.RefNo='".$RefNo."' and vouchertbl.RefTableID='".$RefTableID."' and vouchertbl.VoucherNo='".$VoucherNo."'";		
		}
		$data3 = $this->m_dbConn->select($sql3);		
		$data3[0]['voucher_name'] = $voucher;
		$data3[0]['BillType'] = 1;			
		if($voucher == 'Sales Voucher')
		{
			$sqlBill = "SELECT `BillNumber`,`PeriodID`,`BillType` FROM `billdetails` where `ID`= '".$data3[0]['RefNo']."' ";
			$billresult = $this->m_dbConn->select($sqlBill);
			$data3[0]['BillNumber'] = $billresult[0]['BillNumber'];	
			$data3[0]['PeriodID'] = $billresult[0]['PeriodID'];	
			$data3[0]['BillType'] = $billresult[0]['BillType'];	
		}		
		$status = "";
		
		if($RefNo <> "")
		{
			$category = $this->obj_utility->getParentOfLedger($data3[0]['To']);	
			$CategoryID = $category['category'];
			/*if($CategoryID == CASH_ACCOUNT)
			{
				$status = "Cash Balance";	
			}
			else
			{*/
			//print_r($data3[0]['RefNo']);
			//echo  $sqlCheque="SELECT ChequeNumber,PaidTo FROM `paymentdetails` where id='".$data3[0]['RefNo']."'";
			$sqlCheque="SELECT pmtdetail.ChequeNumber, pmtdetail.PaidTo,chkdetail.ID,chkdetail.BillType, chkdetail.BankID FROM `paymentdetails` as pmtdetail join `chequeentrydetails` as chkdetail on pmtdetail.ChequeNumber=chkdetail.ChequeNumber and pmtdetail.PaidTo=chkdetail.PaidBy where pmtdetail.id='".$data3[0]['RefNo']."'";
			$Chequedetails = $this->m_dbConn->select($sqlCheque);
			
			if($Chequedetails <> "")
			{
				$sqlChkEntry="SELECT `Return`,`ReceivedAmount` FROM `bankregister` where ChkDetailID='".$Chequedetails[0]['ID']."'";
				$ChequeEntrydetails = $this->m_dbConn->select($sqlChkEntry);
				$sqlBank="SELECT * FROM `ledger` where id='".$Chequedetails[0]['BankID']."'";
				$BankDetail = $this->m_dbConn->select($sqlBank);
				$data3[0]['Return'] = $ChequeEntrydetails[0]['Return'];	
				$data3[0]['BillType'] =$Chequedetails[0]['BillType'];
				$data3[0]['ChequeNumber'] =$Chequedetails[0]['ChequeNumber'];
				$data3[0]['PayerBank'] =$BankDetail[0]['ledger_name'];	
				
			}
			
				if($credit > 0)
				{
					$bankRegQuery = 'SELECT `Reconcile`, `Return`, `ChkDetailID`,`DepositGrp` FROM `bankregister` WHERE `ChkDetailID` = "'.$RefNo.'" AND `ReceivedAmount` > 0';
					$res = $this->m_dbConn->select($bankRegQuery);
					$data3[0]['DepositGrp'] = $res[0]['DepositGrp'];
				}
				elseif($debit > 0)
				{
					$bankRegQuery = 'SELECT `Reconcile`, `Return`, `ChkDetailID`,`DepositGrp` FROM `bankregister` WHERE `ChkDetailID` = "'.$RefNo.'" AND `PaidAmount` > 0';
					$res = $this->m_dbConn->select($bankRegQuery);
					$data3[0]['DepositGrp'] = $res[0]['DepositGrp'];
				}	
				
				
				
				if($res[0]['Reconcile'] > 0)
				{
					$status = "Cleared";
				}
				elseif($res[0]['Return'] > 0)
				{
					$status = "Rejected";	
				}
				else
				{
					$status = "Unclear";	
				}
			//}
			
			if($RefTableID <> "")
			{											
				if($RefTableID == 2)
				{	
					$sql = "SELECT cheque_details.`ChequeNumber`, cheque_details.`PayerBank`, cheque_details.`PayerChequeBranch`,cheque_details.`BillType`,period.`ID` as PeriodID FROM `chequeentrydetails` as cheque_details join period on cheque_details.ChequeDate between period.BeginingDate and period.EndingDate WHERE cheque_details.`ID` = '".$res[0]['ChkDetailID']."'";				
					//echo $sql = "SELECT `ChequeNumber`, `PayerBank`, `PayerChequeBranch`,`BillType` FROM `chequeentrydetails` WHERE `ID` = '".$res[0]['ChkDetailID']."'";									
					$result = $this->m_dbConn->select($sql);					
					$data3[0]['ChequeNumber'] = $result[0]['ChequeNumber'];
					$data3[0]['PayerBank'] = $result[0]['PayerBank'];
					$data3[0]['PayerChequeBranch'] =  $result[0]['PayerChequeBranch'];
					$data3[0]['BillType'] = $result[0]['BillType'];	
					$data3[0]['PeriodID'] = $result[0]['PeriodID'];
				}														
			}
			
			if($voucher=="Sales Voucher")
			{
				$sqlQuery = 'SELECT `PeriodID`,`BillType` FROM `billdetails` WHERE `ID` = '.$RefNo;
				$res = $this->m_dbConn->select($sqlQuery);
				if($res <> "")
				{
					$sqlPeriod = "Select periodtbl.type, yeartbl.YearDescription from period as periodtbl JOIN year as yeartbl ON periodtbl.YearID = yeartbl.YearID where periodtbl.ID = '" . $res[0]['PeriodID'] . "'";
				
					$sqlResult = $this->m_dbConn->select($sqlPeriod);
					$data3[0]['billFor'] =  $sqlResult[0]['type'] . " "  . $sqlResult[0]['YearDescription'];
					$data3[0]['BillType'] = $res[0]['BillType'];



					$sqlBillRegister = "SELECT  `BillFor_Message` FROM `billregister` where `PeriodID`='".$res[0]['PeriodID']."' AND `BillType`='".$res[0]['BillType']."' ";
					//echo $sqlBillRegister;
					$BillRegResult = $this->m_dbConn->select($sqlBillRegister);
					//print_r($BillRegResult);
					//echo "Msg:".$BillRegResult[0]["BillFor_Message"];
					$BillMsg =$BillRegResult[0]["BillFor_Message"];
					if($BillMsg != "")
					{
						$data3[0]['billFor'] =  $BillMsg;
					}
				}
			}
		}
		$data3[0]['Status'] = $status;
		return $data3;
			
	}
	
	
	public function details($gid,$lid,$voucherdate)
	{
		  if($gid == 1)
		  {
			$sql = "select ledgertbl.id,Date,ledgertbl.ledger_name as Particular, Debit, Credit,VoucherID,VoucherTypeID,Is_Opening_Balance from `liabilityregister` as liabilitytbl JOIN `ledger` as ledgertbl on liabilitytbl.LedgerID=ledgertbl.id where liabilitytbl.LedgerID='".$lid."' and Date='".$voucherdate."' ORDER BY Date ASC";
			$data = $this->m_dbConn->select($sql);
		  }
		  else if($gid == 2)
		  {
			
			$categoryid = $this->obj_utility->getParentOfLedger($lid);
			if($categoryid['category'] == BANK_ACCOUNT)
			{ 
				$sql = "select ledgertbl.id,Date,ledgertbl.ledger_name as Particular,PaidAmount as Debit,ReceivedAmount as  Credit,VoucherID,VoucherTypeID,Is_Opening_Balance from `bankregister` as banktbl JOIN `ledger` as ledgertbl on banktbl.LedgerID=ledgertbl.id  where banktbl.LedgerID='".$lid."' and Date='".$voucherdate."' ORDER BY Date ASC";		  
			}
			else
			{
			   $sql = "select ledgertbl.id,Date,ledgertbl.ledger_name as Particular, Debit, Credit,VoucherID,VoucherTypeID,Is_Opening_Balance	 from `assetregister` as assettbl JOIN `ledger` as ledgertbl on assettbl.LedgerID=ledgertbl.id  where assettbl.LedgerID='".$lid."'  and Date='".$voucherdate."' ORDER BY Date ASC";
			}
			
			
			$data = $this->m_dbConn->select($sql);
		 }
	  	 else if($gid == 3)
		 {
			$sql = "select ledgertbl.id,Date, ledgertbl.ledger_name as Particular, Debit, Credit,VoucherID,VoucherTypeID from `incomeregister` as incometbl JOIN `ledger` as ledgertbl on incometbl.LedgerID=ledgertbl.id where incometbl.LedgerID='".$lid."' and Date='".$voucherdate."' ORDER BY Date ASC";
			$data = $this->m_dbConn->select($sql);
		 }
		 else if($gid == 4)
		 {
			$sql = "select ledgertbl.id,Date,ledgertbl.ledger_name as Particular, Debit, Credit,VoucherID,VoucherTypeID from `expenseregister` as expensetbl JOIN `ledger` as ledgertbl on expensetbl.LedgerID=ledgertbl.id where expensetbl.LedgerID='".$lid."' and Date='".$voucherdate."' ORDER BY Date ASC";
			$data = $this->m_dbConn->select($sql);
		 }
	  	return $data;
	}	
	
	public function sendEmail()
	{
		if(isset($_REQUEST['unitID']))
		{
			try
			{
				$unitID = $_REQUEST['unitID'];
				
				$unitNo = 'All';
				
				$memberDetails = $this->obj_fetch->GetMemberDetails($unitID);
				if($unitID <> 0)
				{
					$unitNo = $this->obj_fetch->GetUnitNumber($unitID);
				}
	
				$mailSubject = 'Memeber Ledger Report For All Unit';
				$mailBody = 'Attached Memeber Ledger Report For All Unit';
				
				if($_REQUEST['emailMessage'] <> '')
				{
					$mailBody = $_REQUEST['emailMessage'];
				}
				if($_REQUEST['emailSubjectHead'] <> '')
				{
					$mailSubject = $_REQUEST['emailSubjectHead'];
				}
				
				//$societyDetails = $this->obj_fetch->GetSocietyDetails($this->obj_fetch->GetSocietyID($unitID));
				$societyDetails = $this->obj_fetch->GetSocietyDetails($_SESSION['society_id']);
				$societyCCEmailID = $this->obj_fetch->objSocietyDetails->sSocietyCC_Email;
				
				$mailToEmail = $this->obj_fetch->objMemeberDetails->sEmail;
				//$mailToEmail = "societyaccounts@pgsl.in";
				
				/*if($mailToEmail == '')
				{
					echo 'Email ID Missing';
					return 'Email ID Missing';
					exit();
				}*/
				
				if($_REQUEST['emailID'] <> '')
				{
					$mailToEmail = $_REQUEST['emailID'];
				}
				
				$mailToName = $this->obj_fetch->objMemeberDetails->sMemberName;
				
				$baseDir = dirname( dirname(__FILE__) );
				 
				 $specialChars = array('/','.', '*', '%', '&', ',', '(', ')', '"');
				$unitNo = str_replace($specialChars,'',$unitNo);
			
				$fileName =  $baseDir . "/Reports/" . $this->obj_fetch->objSocietyDetails->sSocietyCode . "/MemberLedgerReport-" . $this->obj_fetch->objSocietyDetails->sSocietyCode . '-' . $unitNo .'.pdf';
				
				if(!file_exists($fileName))
				{
					return 'Report does not exist.';
					exit();
				}
				
				$EMailIDToUse = $this->obj_utility->GetEmailIDToUse(false, 0, 0, 0, 0, 0, $_SESSION['society_id']);
				
				//print_r($EMailIDToUse);

				$EMailID = $EMailIDToUse['email'];
				$Password = $EMailIDToUse['password'];

				// Create the mail transport configuration
				$transport = Swift_SmtpTransport::newInstance('cs10.webhostbox.net', 465, "ssl")
					  ->setUsername($EMailID)
					  ->setSourceIp('0.0.0.0')
					  ->setPassword($Password) ;	 
				
					  
				// Create the message
				$message = Swift_Message::newInstance();
				
				if($mailToEmail != "")
				 {
					 $arEmailDs = explode(";", $mailToEmail);
					 $arIDs = array();
					 
					 foreach($arEmailDs as $sKey => $sCurEMailID)
					 {
					 	if($sCurEMailID <> '')
					 	{
						 	array_push($arIDs, $sCurEMailID);
					 	}
					 }
					 $message->setTo($arIDs);
				 }

				/*$message->setTo(array(
				  $mailToEmail => $mailToName
				 ));*/
				 
				// $societyEmail = "societyaccounts@pgsl.in";
				 
				 $societyName = $this->obj_fetch->objSocietyDetails->sSocietyName;
				 $sSocietyEmail = $this->obj_fetch->objSocietyDetails->sSocietyEmail;
				 
				 if($_REQUEST['CC'] != "")
				 {
					 $arEmailDs = explode(";", $_REQUEST['CC']);
					 $arIDs = array();
					 
					 foreach($arEmailDs as $sKey => $sCurEMailID)
					 {
						 array_push($arIDs, $sCurEMailID);
					 }
					 $message->SetCc($arIDs);
				 }
				
				//send email to society cc email
				 if($societyCCEmailID  <> "")
				 {
						$message->SetCc(array($societyCCEmailID => $societyName));
				 }
				 
				 //send email to society email
				  if($sSocietyEmail  <> "")
				 {
						$message->setReplyTo(array($sSocietyEmail => $societyName));
				 }
				 
				$message->setSubject($mailSubject);
				$message->setBody($mailBody);
				$message->setFrom("no-reply@way2society.com", $this->obj_fetch->objSocietyDetails->sSocietyName);
				 
				$message->attach(Swift_Attachment::fromPath($fileName));
				// Send the email
				$mailer = Swift_Mailer::newInstance($transport);
		
				$result = $mailer->send($message);
			
				if($result > 0)
				{
					echo 'Success';
					return 'Success';
				}
				else
				{
					echo 'Failed';
					return 'Failed';
				}
			}
			catch(Exception $exp)
			{
					echo $exp;
			}
			
	}
	else
	{
		//echo 'Missing Parameters';
		return 'Missing Parameters';
	}
		
	}
	
	public function getAllUnits()
	{
		$sql="select `unit_id` from `unit` where `society_id` = ".$_SESSION['society_id']." and `status` = 'Y' order by sort_order asc";
		$res=$this->m_dbConn->select($sql);
		$flatten = array();
    	foreach($res as $key)
		{
			$flatten[] = $key['unit_id'];
		}

    	return $flatten;
	}
	
	
}
?> 