<?php
include_once("utility.class.php");
include_once("view_ledger_details.class.php");
include_once("dbconst.class.php");
include_once ("include/fetch_data.php");
include_once('../swift/swift_required.php');
class unit_tariff_details
{
	public $m_dbConn;
	public $obj_utility;
	public $obj_fetch;
	public $obj_ledger_details;

	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->obj_utility = new utility($this->m_dbConn);
		$this->obj_ledger_details=new view_ledger_details($this->m_dbConn);
		$this->obj_fetch = new FetchData($this->m_dbConn);
	}
	
	public function show_owner_name($uid)
	{
	  
	    $sql="select owner_name,resd_no,mob,email,unittbl.unit_no,wingtbl.wing from member_main as membertbl JOIN `unit` as unittbl on unittbl.unit_id=membertbl.unit JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id where membertbl.unit='".$uid."'";
	  
	  $data=$this->m_dbConn->select($sql);
	  //echo $sql;
	  //print_r($data[0]['society_name']);
	  return $data;
	
	  
	}
	
	
	public function get_tariff_details($uid, $bill_type)
	{	  	  
	   	$sql="select tarifftbl.UnitID, tarifftbl.AccountHeadID, tarifftbl.AccountHeadAmount, tarifftbl.BeginPeriod, tarifftbl.EndPeriod, ledgertbl.ledger_name from unitbillmaster as tarifftbl JOIN ledger as ledgertbl ON tarifftbl.AccountHeadID = ledgertbl.id where tarifftbl.UnitID = '" . $uid . "' and tarifftbl.BillType = '" . $bill_type . "' ORDER BY tarifftbl.AccountHeadID, tarifftbl.BeginPeriod ASC";
	  
	  	$data=$this->m_dbConn->select($sql);
	  	return $data;
	}
	
	public function getParticularDetails($uid,$voucherdate)
	{
		$aryParent = $this->obj_utility->getParentOfLedger($uid);
		//print_r($aryParent);
		$res45=$this->details($aryParent['group'],$uid,$voucherdate);	
		//echo 'parent group:'.$aryParent['group'];
		//print_r('test1');
		//echo $voucherdate;
		//print_r($res45);
		//print_r('test2');
		//echo 'voucherid is:'.$res45[0]['VoucherID'];
		if($res45[0]['VoucherID']==0)
		{
			 //echo '1';
			 return 'Opening Balanace';
		}
		else
		{
			//echo 'voucherid nt 0:'.$res45[0]['VoucherID'];
			$res46=$this->details2($uid,$res45[0]['VoucherID'],$res45[0]['VoucherTypeID']);
			//echo $aryParent['group'];
			//print_r($res45);
			return $res46;
		 }
	}
	
	public function details2($lid, $vid, $vtype = 0, $debit = 0, $credit = 0)
	{		
		$sql1="select `desc` from `vouchertype` where id='".$vtype."'";		
		$data1=$this->m_dbConn->select($sql1);
		$voucher=$data1[0]['desc'];		
		$sql2="select RefNo,RefTableID,VoucherNo from `voucher` where id='".$vid."' ";
	
		$data2=$this->m_dbConn->select($sql2);
		$RefNo=$data2[0]['RefNo'];
		$RefTableID=$data2[0]['RefTableID'];
		$VoucherNo=$data2[0]['VoucherNo'];						
		$sql3="select ledgertbl.id,`ledger_name`,vouchertbl.Note,vouchertbl.RefNo,vouchertbl.VoucherNo,vouchertbl.To as 'To' from `voucher` as vouchertbl JOIN `ledger` as ledgertbl on vouchertbl.To=ledgertbl.id where vouchertbl.RefNo='".$RefNo."' and vouchertbl.RefTableID='".$RefTableID."' and vouchertbl.VoucherNo='".$VoucherNo."'";		
		$data3=$this->m_dbConn->select($sql3);		
		$data3[0]['voucher_name']=$voucher;
				
		if($voucher == 'Sales Voucher')
		{
			$sqlBill="SELECT `BillNumber`,`PeriodID` FROM `billdetails` where `ID`= ".$data3[0]['RefNo']." ";
			$billresult=$this->m_dbConn->select($sqlBill);
			$data3[0]['BillNumber']= $billresult[0]['BillNumber'];	
			$data3[0]['PeriodID']= $billresult[0]['PeriodID'];	
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
					$sql = "SELECT `ChequeNumber`, `PayerBank`, `PayerChequeBranch` FROM `chequeentrydetails` WHERE `ID` = '".$res[0]['ChkDetailID']."'";									
					$result = $this->m_dbConn->select($sql);					
					$data3[0]['ChequeNumber'] = $result[0]['ChequeNumber'];
					$data3[0]['PayerBank'] = $result[0]['PayerBank'];
					$data3[0]['PayerChequeBranch'] =  $result[0]['PayerChequeBranch'];
				}														
			}
			
			if($voucher=="Sales Voucher")
			{
				$sqlQuery = 'SELECT `PeriodID` FROM `billdetails` WHERE `ID` = '.$RefNo;
				$res = $this->m_dbConn->select($sqlQuery);
				if($res <> "")
				{
					$sqlPeriod = "Select periodtbl.type, yeartbl.YearDescription from period as periodtbl JOIN year as yeartbl ON periodtbl.YearID = yeartbl.YearID where periodtbl.ID = '" . $res[0]['PeriodID'] . "'";
				
					$sqlResult = $this->m_dbConn->select($sqlPeriod);
					$data3[0]['billFor'] =  $sqlResult[0]['type'] . " "  . $sqlResult[0]['YearDescription'];
				}
			}
		}
		$data3[0]['Status'] = $status;
		return $data3;
			
	}
	
	
	public function details($gid,$lid,$voucherdate)
	{
	  if($gid==1)
	  {
	    $sql="select ledgertbl.id,Date,ledgertbl.ledger_name as Particular, Debit, Credit,VoucherID,VoucherTypeID,Is_Opening_Balance from `liabilityregister` as liabilitytbl JOIN `ledger` as ledgertbl on liabilitytbl.LedgerID=ledgertbl.id where liabilitytbl.LedgerID='".$lid."' and Date='".$voucherdate."' ORDER BY Date ASC";
	  
	  $data=$this->m_dbConn->select($sql);
	  //echo $sql;
	  //print_r($data);
	  //return $data;
	  }
	  
	  if($gid==2)
	  {
	    
		//$sql="select Date, Debit, Credit,VoucherID,VoucherTypeID,Is_Opening_Balance	 from `assetregister` as assettbl JOIN `ledger` as ledgertbl on assettbl.LedgerID=ledgertbl.id where assettbl.LedgerID='".$lid."' ORDER BY Date ASC";
			  
			  $categoryid=$this->obj_utility->getParentOfLedger($lid);
		//echo $categoryid['category'];
				if($categoryid['category']==BANK_ACCOUNT)
				{ 
				$sql="select ledgertbl.id,Date,ledgertbl.ledger_name as Particular,PaidAmount as Debit,ReceivedAmount as  Credit,VoucherID,VoucherTypeID,Is_Opening_Balance from `bankregister` as banktbl JOIN `ledger` as ledgertbl on banktbl.LedgerID=ledgertbl.id  where banktbl.LedgerID='".$lid."' and Date='".$voucherdate."' ORDER BY Date ASC";		  
				 }
			   else
			   {
				   
				$sql="select ledgertbl.id,Date,ledgertbl.ledger_name as Particular, Debit, Credit,VoucherID,VoucherTypeID,Is_Opening_Balance	 from `assetregister` as assettbl JOIN `ledger` as ledgertbl on assettbl.LedgerID=ledgertbl.id  where assettbl.LedgerID='".$lid."'  and Date='".$voucherdate."' ORDER BY Date ASC";
				
			   }
	  $data=$this->m_dbConn->select($sql);
	  
	 //echo $sql;
	  //print_r($data);
	  //return $data;
	  }
	  
	  if($gid==3)
	  {
	    $sql="select ledgertbl.id,Date, ledgertbl.ledger_name as Particular, Debit, Credit,VoucherID,VoucherTypeID from `incomeregister` as incometbl JOIN `ledger` as ledgertbl on incometbl.LedgerID=ledgertbl.id where incometbl.LedgerID='".$lid."' and Date='".$voucherdate."' ORDER BY Date ASC";
	  
	  $data=$this->m_dbConn->select($sql);
	  //echo $sql;
	  //print_r($data);
	  //return $data;
	  }
	  
	  if($gid==4)
	  {
	    $sql="select ledgertbl.id,Date,ledgertbl.ledger_name as Particular, Debit, Credit,VoucherID,VoucherTypeID from `expenseregister` as expensetbl JOIN `ledger` as ledgertbl on expensetbl.LedgerID=ledgertbl.id where expensetbl.LedgerID='".$lid."' and Date='".$voucherdate."' ORDER BY Date ASC";
	  
	  $data=$this->m_dbConn->select($sql);
	  //echo $sql;
	  //print_r($data);
	  //return $data;
	  }
	  return $data;
	}	
	
	public function sendEmail()
	{
		//echo "inside sendEmail()";
		if(isset($_REQUEST['unitID']))
		{
			//echo "unitID:".$_REQUEST['unitID'];
			//echo "emailID:".$_REQUEST['emailID'];
			//echo "emailMessage:".$_REQUEST['emailMessage'];
			//echo "emailSubjectHead:".$_REQUEST['emailSubjectHead'];
			
			//require_once('../swift/swift_required.php');
			//echo "test 2";
			$unitID = $_REQUEST['unitID'];
			
			$memberDetails = $this->obj_fetch->GetMemberDetails($unitID);
			$unitNo = $this->obj_fetch->GetUnitNumber($unitID);
			//echo "test 37";
			$mailSubject = 'Memeber Ledger Report For Unit ' . $unitNo;//'Maintainance Bill For March';
			$mailBody = 'Attached Memeber Ledger Report For Unit ' . $unitNo;
			if($_REQUEST['emailMessage'] <> '')
			{
				$mailBody = $_REQUEST['emailMessage'];
			}
			if($_REQUEST['emailSubjectHead'] <> '')
			{
				$mailSubject = $_REQUEST['emailSubjectHead'];
			}
			
			$societyDetails = $this->obj_fetch->GetSocietyDetails($this->obj_fetch->GetSocietyID($unitID));
			$mailToEmail = $this->obj_fetch->objMemeberDetails->sEmail;
			
			//echo "mailToEmail:".$mailToEmail;
			if($mailToEmail == '')
			{
				//echo 'Email ID Missing';
				return 'Email ID Missing';
				exit();
			}
			if($_REQUEST['emailID'] <> '')
			{
				$mailToEmail = $_REQUEST['emailID'];
			}
			//echo "Email ID";
			
			$mailToName = $this->obj_fetch->objMemeberDetails->sMemberName;
			
			//echo "test 4";
			
			$baseDir = dirname( dirname(__FILE__) );
			 
			$fileName =  $baseDir . "/Reports/" . $this->obj_fetch->objSocietyDetails->sSocietyCode . "/MemberLedgerReport-" . $this->obj_fetch->objSocietyDetails->sSocietyCode . '-' . $unitNo .'.pdf';
			//echo "test 5";
			if(!file_exists($fileName))
			{
				//echo 'Report does not exist.';
				return 'Report does not exist.';
				exit();
			}
			
			// Create the mail transport configuration
			//$transport = Swift_SmtpTransport::newInstance('cs10.webhostbox.net', 465, "ssl")
			$transport = Swift_SmtpTransport::newInstance('cs10.webhostbox.net', 465, "ssl")
				  ->setUsername('no-reply@way2society.com')
				  ->setSourceIp('0.0.0.0')
				  ->setPassword('society123') ;	 
			 //echo "test 6";
			// Create the message
			$message = Swift_Message::newInstance();
			$message->setTo(array(
			  $mailToEmail => $mailToName
			 ));
			 //echo "test 7";
			//s$societyEmail = $obj_fetch->objSocietyDetails->sSocietyEmail;
			 //if($societyEmail == '')
			 //{
				 $societyEmail = "techsupport@way2society.com";
			 //}
			 
			 $societyName = $this->obj_fetch->objSocietyDetails->sSocietyName;
					
			 /*$message->setReplyTo(array(
			   $societyEmail => $societyName
			));*/
			echo "test 8"; 
			$message->setSubject($mailSubject);
			$message->setBody($mailBody);
			$message->setFrom("no-reply@way2society.com", $this->obj_fetch->objSocietyDetails->sSocietyName);
			 
			$message->attach(Swift_Attachment::fromPath($fileName));
			// Send the email
			$mailer = Swift_Mailer::newInstance($transport);
			$result = $mailer->send($message);
			
			if($result == 1)
			{
				echo 'Success';
				return 'Success';
			}
			else
			{
				//echo 'Failed';
				return 'Failed';
			}
	}
	else
	{
		//echo 'Missing Parameters';
		return 'Missing Parameters';
	}
		
	}
	
	
	
}
?> 