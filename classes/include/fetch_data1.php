<?php if(!isset($_SESSION)){ session_start(); }

include_once("../dbconst.class.php");
class CBillRegister
{
	public $sBillNumber;   
	public $sBillDate;
	public $sBillDueDate;
	public $sHeader;
	public $sHeaderAmount;
	public $sNotes;
	public $sVoucherID;
	
	public function __construct($dbConn)
	{
		$this->sBillNumber = "";
		$this->sBillDate = "";
		$this->sBillDueDate = "";
		$this->sHeader = "";
		$this->sHeaderAmount = "";
		$this->sNotes = "";
		$this->sVoucherID = "";
	}
}

class CSocietyDetails
{	
	public $sSocietyName;
	public $sSocietyAddress ;
	public $sSocietyRegNo ;
	public $iSocietyID;
	public $sSocietyCode;
	public $sSocietyEmail;
	public $sSocietyCC_Email;
	public $sSocietySendBillAsLink;
	public $sSocietyEmailContactNo;
	
	public function __construct($dbConn)
	{
		$this->sSocietyName = "";
	    $this->sSocietyAddress = "";
	    $this->sSocietyRegNo = "";
	    $this->iSocietyID = 0;
		$this->sSocietyCode = '';
		$this->sSocietyEmail = '';
		$this->sSocietyCC_Email = '';
		$this->sSocietySendBillAsLink = 0;
		$this->sSocietyEmailContactNo = '';
	}
} 
class CMemberDetails
{
	
	public $iMemberID;
	public $sMemberName ;
	public $sUnitNumber;	
	public $sParkingNumber;
	public $sGender;
	public $sEmail;
	public $sMobile;
	
	public function __construct($dbConn)
	{
			$this->iMemberID = "";
			$this->sMemberName = "";
			$this->sUnitNumber = "";	
			$this->sParkingNumber = "";
			$this->sGender = "";
			$this->sEmail = "";
			$this->sMobile = "";
	}
}
class FetchData
{
	public $objSocietyDetails;
	public $objMemeberDetails;
	
	public $m_dbConn;
	public function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->objSocietyDetails = new CSocietyDetails($this->m_dbConn);
		$this->objMemeberDetails = new CMemberDetails($this->m_dbConn);
	}
	
	/*function GetSociety_oldDetails($ReqSocietyID)
	{
		$sqlFetch = "select * from society_old";
		$res02 = $this->m_dbConn->select($sqlFetch); 
		if($res02 <> "")
		{
			foreach($res02 as $row => $v )
			{
				//if($row['SocietyID'] ==  $ReqSocietyID)
				{
					$this->objSocietyDetails->sSocietyID = $res02[$row]['SocietyID'];
					$this->objSocietyDetails->sSocietyName = $row['Name'];
					$this->objSocietyDetails->sSocietyRegNo = $row['RegNumber'];
					$this->objSocietyDetails->sSocietyAddress = $row['Address'];
					
				}
			}
		}
		else
		{
			echo "No Data Found from society database.";
		}
	}
	*/
	function GetSocietyDetails($ReqSocietyID)
	{
		$sqlFetch = "select * from society where society_id=".$ReqSocietyID."";		
		$res02 = $this->m_dbConn->select($sqlFetch); 
		if($res02 <> "")
		{
			foreach($res02 as $row => $v )
			{
				$this->objSocietyDetails->sSocietyName = $res02[$row]['society_name'];
				$this->objSocietyDetails->sSocietyRegNo = $res02[$row]['registration_no'];	
				$this->objSocietyDetails->sSocietyRegDate = $res02[$row]['registration_date'];	
				$this->objSocietyDetails->sSocietyAddress = $res02[$row]['society_add'];
				$this->objSocietyDetails->sSocietyCode = $res02[$row]['society_code'];
				$this->objSocietyDetails->sSocietyEmail = $res02[$row]['email'];
				$this->objSocietyDetails->sSocietyBillingCycle = $res02[$row]['bill_cycle'];
				$this->objSocietyDetails->sSocietyCC_Email = $res02[$row]['cc_email'];
				$this->objSocietyDetails->sSocietySendBillAsLink = $res02[$row]['bill_as_link'];
				$this->objSocietyDetails->sSocietyEmailContactNo = $res02[$row]['email_contactno'];
			}
		}
		else
		{
			//echo "No Data Found from society database test for society_id=<".$ReqSocietyID.">.";
		}
	}
	
	
	function GetMemberDetails($sUnitID,$Date = "")
	{
		if($Date <> "")
		{
			$sqlMember = 'select * from member_main where unit='.$sUnitID.' and  ownership_date <= "' .getDBFormatDate($Date). '"  ORDER BY ownership_date DESC LIMIT 1 ';
		}
		else
		{
			$sqlMember = 'select * from member_main where unit='.$sUnitID.' and  ownership_status = 1 ';	
		}
		
		$res02 = $this->m_dbConn->select($sqlMember);

		if($res02 <> "")
		{
			if($sqlMember)
			{
				foreach($res02 as $row => $v )
				//while ($rowMembers = mysql_fetch_array($Members, MYSQL_ASSOC))
				{
						$this->objMemeberDetails->sMemberName = $res02[$row]['owner_name'];
						$this->objMemeberDetails->sUnitNumber = $this->GetUnitNumber($sUnitID);	
						$this->objMemeberDetails->sParkingNumber = $res02[$row]['parking_no'];
						$this->objMemeberDetails->sGender = $res02[$row]["gender"];
						$this->objMemeberDetails->sEmail = $res02[$row]["email"];
						$this->objMemeberDetails->sMobile = $res02[$row]["mob"];
						
				}
			}
			else
			{
				//echo "No Data Found from Members database.";
			}
		}
	}
	public function GetUnitNumber($sUnitID)
	{
		$sqlMember = 'select unit_no from unit where unit_id='.$sUnitID.'';
		$res02 = $this->m_dbConn->select($sqlMember);
		$UnitNumber = "0";
		if($res02 <> "")
		{
			if($sqlMember)
			{
				foreach($res02 as $row => $v )
				//while ($rowMembers = mysql_fetch_array($Members, MYSQL_ASSOC))
				{
					$UnitNumber	= $this->objMemeberDetails->sUnitNumber = $res02[$row]['unit_no'];
				}
			}
			else
			{
				//echo "No Data Found from Members database.";
			}
		}
		//echo $UnitNumber;
		return $UnitNumber;
	}
	
	function GetWingID($sUnitID)
	{
		$sqlMember = 'select wing_id from unit where unit_id='.$sUnitID.'';
		$res02 = $this->m_dbConn->select($sqlMember);
		$UnitNumber = "0";
		if($res02 <> "")
		{
			if($sqlMember)
			{
				foreach($res02 as $row => $v )
				{
					$UnitNumber	= $this->objMemeberDetails->sUnitNumber = $res02[$row]['wing_id'];
				}
			}
			else
			{
				//echo "No WingID found for UnitID <" . $sUnitID. ">";
			}
		}
		return $UnitNumber;
	}
	
	function GetSocietyID($sUnitID)
	{
		$sqlMember = 'select society_id from unit where unit_id='.$sUnitID.'';
		$res02 = $this->m_dbConn->select($sqlMember);
		$SocietyID = "0";
		if($res02 <> "")
		{
			if($sqlMember)
			{
				foreach($res02 as $row => $v )
				//while ($rowMembers = mysql_fetch_array($Members, MYSQL_ASSOC))
				{
					$SocietyID = $res02[$row]['society_id'];
				}
			}
			else
			{
				//echo "No Data Found from Members database.";
			}
		}
		return $SocietyID;
	}
	
	function GetValuesFromBillRegister_Old($UnitID)
	{				
		$UnitID = 1;
		$sqlQuery = 'select * from billregister where UnitID="'.$UnitID.'"';
		$res02 = $this->m_dbConn->select($sqlQuery);
		
		$arr = array();
		if($res02 <> "")
		{
			//while ($UnitBillRow = mysql_fetch_array($UnitDetails, MYSQL_ASSOC))
			foreach($res02 as $row => $v )
			{
				$iIncrement = 1;
				for (; $iIncrement <= 20; $iIncrement++) 
				{
					$HeaderColumnName = "AccountHeadID" . $iIncrement;
					$HeaderAmountColumnName = $HeaderColumnName . "Amount";
					if(isset($UnitBillRow[$HeaderColumnName]))
					{
						$IsBillItem = $this->GetIsBillItemFromAccountHead($UnitBillRow[$HeaderColumnName]);
						$AccountHead1 = $UnitBillRow[$HeaderColumnName];
						$AccountHead1Amount = $UnitBillRow[$HeaderAmountColumnName];
						if($AccountHead1Amount != 0 && $IsBillItem == 1)
						{
							 $arr[$AccountHead1] = $AccountHead1Amount;
						}
					}
				}
			}
		}
		else
		{
			//echo "No Data Found from billregister table of BillGen database.";
		}
		return $arr;
	}
	
	function GetValuesFromBillDetails($UnitID, $PeriodID, $BillType)
	{					  
		$sqlQuery = 'SELECT * FROM `billdetails` WHERE UnitID = "' .$UnitID.'" and PeriodID = "'.$PeriodID .'" and BillType="'.$BillType .'" ';
		$result = $this->m_dbConn->select($sqlQuery);
		
		return $result;
	}
	
	function GetValuesFromBillRegister($UnitID, $PeriodID, $BillType)
	{		
		$arr = array();
		$sqlQuery = "select ID, BillRegisterID from billdetails where UnitID='".$UnitID."' and PeriodID= '". $PeriodID."' and BillType='".$BillType ."'";
		$res02 = $this->m_dbConn->select($sqlQuery);
		$billRegisterID = $res02[0]["ID"];
		
		$sqlQuery = "Select * from billregister where ID = '" . $res02[0]["BillRegisterID"] . "'";
		//echo $sqlQuery;
		$res02 = $this->m_dbConn->select($sqlQuery);
		//print_r($res02);
				
		if($res02 <> "")
		{			
			$iIncrement = 0;
			
			$sql1 = 'SELECT voucher_table.id as VcrId, voucher_table.To,voucher_table.Credit FROM `voucher` as `voucher_table` join `ledger` as `ledger_table` on voucher_table.To = ledger_table.id WHERE voucher_table.RefNo= '.$billRegisterID . ' and voucher_table.RefTableID = ' . TABLE_BILLREGISTER;
			//echo $sql1;
			$res = $this->m_dbConn->select($sql1);
			//print_r($res);
			foreach($res as $row => $v )
			{
				$objBillRegister = new CBillRegister($this->m_dbConn);
				//$objBillRegister->sBillNumber = $res02[0]["BillNumber"];
				$objBillRegister->sBillDate = $res02[0]["BillDate"];
				$objBillRegister->sDueDate = $res02[0]["DueDate"];				
				$objBillRegister->sNotes = $res02[0]["Notes"];				
				$billRegisterID = $res02[0]["ID"];
			
				$objBillRegister->sHeader =$v["To"];
				$objBillRegister->sHeaderAmount = $v["Credit"];
				$objBillRegister->sVoucherID = $v["VcrId"];
				$objData = array();
				$objData = array("key"=>$iIncrement,"value"=>$objBillRegister);
				array_push($arr, $objData);
				$iIncrement++;
			}
		}
		else
		{
			//echo "No Data Found from billregister table of BillGen database.";
			//echo "No Data Found.";
		}
		return $arr;
	}

	function GetHeadingFromAccountHead($AccountHeadID)
	{
		$sqlQuery = 'select ledger_name from ledger where id='.$AccountHeadID. '';
		$res02 = $this->m_dbConn->select($sqlQuery);
		$sRequiredHead = "";
		$iCounter = 1;
		if($res02 <> "")
		{
			foreach($res02 as $row => $v )
			{
				$sRequiredHead = $res02[$row]['ledger_name'];
				$iCounter++;
			}
		}
		else
		{
			//echo "No Data Found from account_head table of societies database for  AccountHeadID=".$AccountHeadID. '';
		}
		return $sRequiredHead;
	}
	
	public function getPreviousPeriodData($PeriodID)
	{
			$sqlPrevQuery = "Select Type, YearID, PrevPeriodID, Status from period where ID=" . $PeriodID;			
			$Prevresult = $this->m_dbConn->select($sqlPrevQuery);
			$PrevPeriodID = -1;
			if(!is_null($Prevresult))
			{
				$Type = $Prevresult[0]['Type'];			
				$YearID = $Prevresult[0]['YearID'];			
				$PrevPeriodID = $Prevresult[0]['PrevPeriodID'];			
			}
			return $PrevPeriodID;	
	}

	function getBeginEndDate($UnitID, $PeriodID)
	{
		$PrevPeriodID = $this->getPreviousPeriodData($PeriodID);
		$TotalAmountPaid = 0;
		$StartDate = 0;
		$EndDate = 0;		
		
		$sqlPrevQuery = "Select BeginingDate, EndingDate from period where ID=" . $PrevPeriodID;	
		$Prevresult = $this->m_dbConn->select($sqlPrevQuery);	
		return $Prevresult;						
	}
	
	function getBeginEndReceiptDate($UnitID, $PeriodID)
	{
		$currentDateSql = "Select BillDate from billregister where PeriodID = '" . $PeriodID . "' ORDER BY ID DESC LIMIT 1";
		$resultCurrentDate = $this->m_dbConn->select($currentDateSql);
		
		$EndDate = $resultCurrentDate[0]['BillDate'];
		
		if($EndDate <> '')
		{
			$EndDate = $this->GetDateByOffset($EndDate, -1);
		}
		
		$PrevPeriodID = $this->getPreviousPeriodData($PeriodID);
		
		$previousDateSql = "Select BillDate from billregister where PeriodID = '" . $PrevPeriodID . "' ORDER BY ID DESC LIMIT 1";
		$resultPreviousDate = $this->m_dbConn->select($previousDateSql);
		
		$StartDate = $resultPreviousDate[0]['BillDate'];
		
		$aryDate = array();
		$aryDate['BeginDate'] = $StartDate;
		$aryDate['EndDate'] = $EndDate;
		//print_r($aryDate);
		return $aryDate;
	}
	
	public function GetDateByOffset($myDate, $Offset)
	{
		//echo '<br/>myDate : ' . $myDate;
		//echo '<br/>Offset : ' . $Offset;
		$datetime1 = new DateTime($myDate);
		$newDate = $datetime1->modify($Offset . ' day');
		//echo '<br/>Offetdate : ' . $newDate->format('Y-m-d');

		return $newDate->format('Y-m-d');	
	}

	function getNextPeriodID($PeriodID)
	{
		$sqlQuery = "Select ID from period where PrevPeriodID=" . $PeriodID;	
		$result = $this->m_dbConn->select($sqlQuery);	
		return $result[0]['ID'];						
	}
	
	function getReceiptDetails($UnitID, $PeriodID, $show=false, $BillingCycle=0, $IsBill=false)
	{	
		if($_REQUEST["cycle"] <> "" && $_REQUEST["cycle"] <> 0 && $BillingCycle <> 0)
		{
			$PeriodID=$this->getNextPeriodID($PeriodID);
		}
		$Prevresult = $this->getBeginEndDate($UnitID, $PeriodID);
		
		if(!is_null($Prevresult))
		{
			$StartDate = $Prevresult[0]['BeginingDate'];
			$EndDate = $Prevresult[0]['EndingDate'];												
		}
		if($show== false)
		{			
			if($IsBill == true)
			{
		 		$sqlCheck = "select * from chequeentrydetails where voucherdate >= '". $StartDate . "' AND voucherdate <= '" . $EndDate . "' AND PaidBy = " . $UnitID . " AND chequeentrydetails.IsReturn = 0 ";
			}
			else
			{
				$sqlCheck = "select * from chequeentrydetails where voucherdate >= '". $StartDate . "' AND voucherdate <= '" . $EndDate . "' AND PaidBy = " . $UnitID ;
			}
		}
		else if($_REQUEST["cycle"] <> "")
		{
			//$voucherNo = $obj_display_bills->getVoucherNo($chequeDetailsExtra[$j]['ID']);
			//echo $sqlCheck = "select * from chequeentrydetails where voucherdate >= '". $EndDate . "'  AND PaidBy = " . $UnitID." ";
			$sqlCheck = "select chequeentrydetails.ID,periodtbl.ID as PeriodID,Amount,PayerBank,PayerChequeBranch,ChequeDate,ChequeNumber from chequeentrydetails JOIN `period` as periodtbl on   chequeentrydetails.voucherdate >= periodtbl.BeginingDate and  chequeentrydetails.voucherdate <= periodtbl.EndingDate where  voucherdate <= '". $EndDate . "'  AND  voucherdate >= '". $StartDate. "' AND PaidBy = " . $UnitID." AND periodtbl.Billing_Cycle = ".$BillingCycle." ";	
		}
		else
		{
			 $sqlCheck = "select chequeentrydetails.ID,periodtbl.ID as PeriodID,Amount,PayerBank,PayerChequeBranch,ChequeDate,ChequeNumber from chequeentrydetails JOIN `period` as periodtbl on   chequeentrydetails.voucherdate > periodtbl.BeginingDate and  chequeentrydetails.voucherdate <= periodtbl.EndingDate where  voucherdate >= '". $EndDate . "'  AND PaidBy = " . $UnitID." AND periodtbl.Billing_Cycle = ".$BillingCycle." ";	
		}
		
		
		//$sqlCheck = "select * from chequeentrydetails where  PaidBy = " . $UnitID;

		//echo '<br/>SqlCheck : ' . $sqlCheck;

		$resultCheck = $this->m_dbConn->select($sqlCheck);
		
		return $resultCheck;
	}
	
	function getReceiptDetailsEx($UnitID, $PeriodID, $show = false, $BillingCycle = 0, $IsBill = false)
	{	
		
		$StartDate;
		if($_REQUEST["cycle"] <> "" && $_REQUEST["cycle"] <> 0 && $BillingCycle <> 0)
		{
			$PeriodID=$this->getNextPeriodID($PeriodID);
		}
		$Prevresult = $this->getBeginEndReceiptDate($UnitID, $PeriodID);
		
		if(!is_null($Prevresult))
		{
			/*if($_SESSION['society_id'] == 99)
			{
				$StartDate = '2015-04-01';	
			}
			else
			{
				$StartDate = $Prevresult['BeginDate'];
			}*/
			$StartDate = $Prevresult['BeginDate'];
			$EndDate = $Prevresult['EndDate'];												
		}
		
		//if($StartDate == '')
		//	return;
		
		if($show== false)
		{			
			if($IsBill == true)
			{
		 		$sqlCheck = "select * from chequeentrydetails where voucherdate >= '". $StartDate . "'";
				if($EndDate <> '')
				{
					$sqlCheck .= " AND voucherdate <= '" . $EndDate . "'";
				}
				//$sqlCheck .= " AND PaidBy = " . $UnitID . " AND chequeentrydetails.IsReturn = 0";
				$sqlCheck .= " AND PaidBy = " . $UnitID;
			}
			else
			{
				$sqlCheck = "select * from chequeentrydetails where voucherdate >= '". $StartDate . "'";
				if($EndDate <> '')
				{
					$sqlCheck .= " AND voucherdate <= '" . $EndDate . "'";	
				}
				//$sqlCheck .= " AND PaidBy = '" . $UnitID . "'" ;
				$sqlCheck .= " AND PaidBy = '" . $UnitID . "' AND chequeentrydetails.IsReturn = 0";
			}			
		}
		else if($_REQUEST["cycle"] <> "")
		{
			$sqlCheck = "select chequeentrydetails.ID,periodtbl.ID as PeriodID,Amount,PayerBank,PayerChequeBranch,ChequeDate,ChequeNumber from chequeentrydetails JOIN `period` as periodtbl on chequeentrydetails.voucherdate >= periodtbl.BeginingDate and  chequeentrydetails.voucherdate <= periodtbl.EndingDate where ";
			if($EndDate <> '')
			{
			 	$sqlCheck .= "voucherdate <= '". $EndDate . "' AND ";
			}
			$sqlCheck .= "voucherdate >= '". $StartDate. "' AND PaidBy = " . $UnitID." AND periodtbl.Billing_Cycle = ".$BillingCycle." ";	
		}
		else
		{
			 //$sqlCheck = "select chequeentrydetails.ID,periodtbl.ID as PeriodID,Amount,PayerBank,PayerChequeBranch,ChequeDate,ChequeNumber from chequeentrydetails JOIN `period` as periodtbl on chequeentrydetails.voucherdate > " . $StartDate . "  and  chequeentrydetails.voucherdate <= " . $EndDate . " where voucherdate >= '" . $EndDate . "'  AND PaidBy = " . $UnitID." AND periodtbl.Billing_Cycle = ".$BillingCycle." ";	
			 $sqlCheck = "select chequeentrydetails.ID, Amount, PayerBank, PayerChequeBranch, ChequeDate, ChequeNumber from chequeentrydetails where voucherdate > '" . $EndDate . "'  AND PaidBy = '" . $UnitID."'" ;	
		}
				
		//$sqlCheck = "select * from chequeentrydetails where  PaidBy = " . $UnitID;

		//echo '<br/>SqlCheck : ' . $sqlCheck;

		$resultCheck = $this->m_dbConn->select($sqlCheck);
		
		//print_r($resultCheck);
		//echo '<br/>';
		return $resultCheck;
	}


function getReverseChargesDetails($UnitID, $Date)
	{		
		
		$ledgername_array=array();
		$sqlCheck = "select * from `reversal_credits` where Date >= ".$Date . " AND `UnitID` = '" . $UnitID."' ";
		$resultCheck = $this->m_dbConn->select($sqlCheck);
		$get_ledger_name="select id,ledger_name from `ledger`";
		$result02=$this->m_dbConn->select($get_ledger_name);
		
		//print_r($result02);
		for($i = 0; $i < sizeof($result02); $i++)
		{
		$ledgername_array[$result02[$i]['id']]=$result02[$i]['ledger_name'];
		
		}
		for($i = 0; $i < sizeof($resultCheck); $i++)
		{
			$resultCheck[$i]['To'] = $ledgername_array[$resultCheck[$i]['LedgerID']];
		}
		
		return $resultCheck;
	}	
	function getWing_AreaDetails($UnitID)
	{
		$detailsquery = 'SELECT unittable.area,wingtable.wing FROM `unit` as `unittable` join `wing` as `wingtable` on unittable.wing_id = wingtable.wing_id and `unit_id` = '.$UnitID;
		$result = $this->m_dbConn->select($detailsquery);
		return $result; 	
	}
	
	function GetIsBillItemFromAccountHead($AccountCategoryID)
	{
		$sqlQuery = 'select IsBillItem from account_head where AccountCategoryID='.$AccountCategoryID.'';
		$res02 = $this->m_dbConn->select($sqlQuery);
		$sRequiredHead = "";
		if($res02 <> "")
		{
			$iCounter = 1;
			foreach($res02 as $row => $v )
			{
				$sRequiredHead = $res02[$row]['IsBillItem'];
			}
		}
		else
		{
			//echo "FetchDeta:: No Data Found from account_head table of societies database.";
		}
		return $sRequiredHead;
	}
	function GetAllBankNamesFromBankMaster()
	{
		$sqlQuery = 'select * from bank_master';
		$res02 = $this->m_dbConn->select($sqlQuery);
		if($res02 <> "")
		{
			$iIncrement = 1;
			foreach($res02 as $row => $v )
			{
				$BankName = $res02[$row]["BankName"];
				if($BankName != "")
				{
					 $arr[$iIncrement] = $BankName;
				}
				$iIncrement++;
			}
		}
		else
		{
			//echo "No Data Found from BankMaster table of societies database.";
		}
		return $arr;
	}
	function GetBillFor($sPeriodID)
	{
		$sqlQuery = "SELECT periodtable.Type, yeartable.YearDescription FROM period AS periodtable JOIN year AS yeartable ON periodtable.YearID = yeartable.YearID WHERE periodtable.id =".$sPeriodID;
		$res02 = $this->m_dbConn->select($sqlQuery);
		$RetrunVal = "";
		if($res02 <> "")
		{
			foreach($res02 as $row => $v )
			{
					$RetrunVal = $res02[$row]["Type"];
					$RetrunVal .= " " . $res02[$row]["YearDescription"];
			}
		}
		else
		{
			//echo "FetchData::GetBillFor - No Data Found from Period table of societies database.";
		}
		return $RetrunVal;
	}
	
	function GetFieldsToShowInBill($UnitID)
	{
		$society_ID = $this->GetSocietyID($UnitID);
		$sql = 'SELECT `show_wing`,`show_parking`,`show_area`, `bill_method`, `show_share`, `bill_footer`,`bill_due_date` FROM `society` WHERE `society_id` = '.$society_ID;
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	function GetBillingCycle($SocietyID)
	{
			
	}
	
	function GetBillDate($SocietyID, $PeriodID, $BillType = 0)
	{
		$sql = "Select BillDate, DueDate from billregister where SocietyID = '"  . $SocietyID . "' and PeriodID = '" . $PeriodID . "' and BillType='". $BillType ."'";
		$result = $this->m_dbConn->select($sql);
		return $result;
	}
	
	function GetShareCertificateNo($UnitID)
	{
		$society_ID = $this->GetSocietyID($UnitID);
		$sql = "SELECT `share_certificate` FROM `unit` WHERE `unit_id` = '".$UnitID."' AND `society_id` = '".$society_ID."'";
		$result = $this->m_dbConn->select($sql);
		return $result[0]['share_certificate'];
	}
	
	function GetSMSDetails($society_ID)
	{		
		$sql = 'SELECT `sms_start_text`,`sms_end_text`,`send_reminder_sms` FROM `society` WHERE `society_id` = '.$society_ID;
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	function GetTotalBillPayable($period_id, $unitID)
	{
		//$sql = 'select `PrincipalArrears`,`AdjustmentCredit`, `BillNumber`, `InterestArrears`, `BillInterest`, `TotalBillPayable`, `PrevInterestArrears`, `BillSubTotal` from `billdetails` where `PeriodID` = "' . $period_id . '" and `unitID` = "'.$unitID.'"'; 	
		//$details = $this->m_dbConn->select($sql);
		//$totalBillPayable = $details[0]['BillSubTotal'] + $details[0]['AdjustmentCredit']  + $details[0]['BillInterest'] + $details[0]['InterestArrears'] +$details[0]['PrincipalArrears'];   		
		$sql = "SELECT SUM(`Debit`) - SUM(`Credit`) AS 'Total' FROM `assetregister` WHERE `LedgerID` = '".$unitID."'";	
		$details = $this->m_dbConn->select($sql);
		return $details[0]['Total'];		
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
		return $mailText;					
	}
	
	public function getUnitPresentation($UnitID)
	{
		$sql = "SELECT unittypetbl.description  as description FROM `unit` as unittbl JOIN `unit_type` as unittypetbl on unittbl.unit_presentation = unittypetbl.id where unittbl.unit_id = '".$UnitID."' ";	
		$details = $this->m_dbConn->select($sql);
		return $details[0]['description'];		
	}
	
	public function getLatestPeriodID($unitID)
	{
		$sql = "SELECT `PeriodID` FROM `billdetails` WHERE `UnitID` = '".$unitID."' ORDER BY `ID`";	
		$period = $this->m_dbConn->select($sql);	
		return $period[sizeof($period) - 1]['PeriodID'];
	}
}
?>