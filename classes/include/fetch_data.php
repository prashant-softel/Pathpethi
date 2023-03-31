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
	public $sFont;
	public $sVoucherID;
	public $Taxable;
	public $BillFor_Msg;
	public $BillAmount;
	
	public function __construct($dbConn)
	{
		$this->sBillNumber = "";
		$this->sBillDate = "";
		$this->sBillDueDate = "";
		$this->sHeader = "";
		$this->sHeaderAmount = "";
		$this->sNotes = "";
		$this->sFont = "";
		$this->sVoucherID = "";
		$this->Taxable = 0;
		$this->GetBillFor_Msg = "";
		$this->BillAmount = "";
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
	public $bSocietyAddressInEmail;
	public $sSocietyGSTINNo;
	public $sSocietyApplyTax;
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
		$this->bSocietyAddressInEmail=0;
		$this->sSocietyGSTINNo="";
		$this->sSocietyApplyTax=0;
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
	public $arListOfMembers;
	public $arListOfTenants;
	public $sMemberGstinNo;
	
	public function __construct($dbConn)
	{
			$this->iMemberID = "";
			$this->sMemberName = "";
			$this->sUnitNumber = "";	
			$this->sParkingNumber = "";
			$this->sGender = "";
			$this->sEmail = "";
			$this->sMobile = "";
			$this->arListOfMembers = array();
			$this->arListOfTenants = array();
			$this->sMemberGstinNo = "";
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
	function GetNotes()
	{
		//***Fetching Note present in Bill register data 
		
		$LastID = "SELECT MAX(id) from billregister ";
		$ResultLastID = $this->m_dbConn->select($LastID);
		$RegisterID = $ResultLastID[0]['MAX(id)'];
		$GetNotes = "SELECT Notes FROM billregister where ID = '".$RegisterID."'";
		$ResultGetNotes = $this->m_dbConn->select($GetNotes);
		if($ResultGetNotes[0]['Notes'] == '' )
		{
			//*** IF Note not available  then check it previous entry
			for($i = $RegisterID ; $i > 0 ; $i--)
			{
				$PreID = $i - 1 ;
				$CheckPreviousEntry = "SELECT Notes FROM billregister where ID = '".$PreID."' ";
				$ResultPreviousEntry = $this->m_dbConn->select($CheckPreviousEntry);
				
				if($ResultPreviousEntry[0]['Notes'] <> '' )
				{
					return $ResultPreviousEntry;
				}
				else
				{
					return $ResultPreviousEntry  = "";
				}
			}
		}
		else
		{
			return $ResultGetNotes;
		}
	}
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
				$this->objSocietyDetails->bSocietyAddressInEmail = $res02[$row]['show_address_in_email'];
				$this->objSocietyDetails->sSocietyGSTINNo = $res02[$row]['gstin_no'];
				$this->objSocietyDetails->sSocietyApplyTax = $res02[$row]['apply_service_tax'];
				$this->objSocietyDetails->sSocietyservice_tax_threshold = $res02[$row]['service_tax_threshold'];
				
			}
		}
		else
		{
			//echo "No Data Found from society database test for society_id=<".$ReqSocietyID.">.";
		}
		
	}
	function GetSocietyDetails1($ReqSocietyID)
	{
		$sqlFetch = "select * from society where society_id=".$ReqSocietyID."";		
		$res02 = $this->m_dbConn->select($sqlFetch); 
		return $res02;	
	}
	
	
	function GetMemberDetails($sUnitID,$Date = "")
	{
		$CheckCategory = "SELECT categoryid FROM ledger WHERE id = '".$sUnitID."'";
		$ResultCheckCategory = $this->m_dbConn->select($CheckCategory);
		if($ResultCheckCategory[0]['categoryid'] <> DUE_FROM_MEMBERS)
		{
			$LedgerName = $this->LedgerDetails($sUnitID);
		}
		if($Date <> "")
		{
			$sqlMember = 'select * from member_main where unit='.$sUnitID.' and  ownership_date <= "' .getDBFormatDate($Date). '"  ORDER BY ownership_date 

DESC LIMIT 1 ';
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
				$sql_other_family  = "select * from mem_other_family where member_id='".$res02[0]['member_id'] ."' AND send_commu_emails=1 AND status = 

'Y'";
				$res_other_family = $this->m_dbConn->select($sql_other_family);
				
				$arListOfOtherMember = array();
				foreach($res_other_family as $member )
				{
					$arMember = array($member['other_name'], $member['other_email'], $member['mem_other_family_id']);
					if($member['other_email'] != "")
					{
						array_push($arListOfOtherMember,$arMember);
					}
				}

				$sql_other_family  = "select tmem.tmember_id, tmem.mem_name, tmem.email from tenant_member as tmem JOIN tenant_module as tmod ON 

tmem.tenant_id = tmod.tenant_id where tmod.unit_id = '" . $sUnitID . "' AND tmod.end_date > NOW() AND tmem.send_commu_emails=1";

				$res_other_family = $this->m_dbConn->select($sql_other_family);
				
				$arListOfActiveTenants = array();
				foreach($res_other_family as $member )
				{
					$arMember = array($member['mem_name'], $member['email'], $member['tmember_id']);
					if($member['email'] != "")
					{
						array_push($arListOfActiveTenants,$arMember);
					}
				}


				foreach($res02 as $row => $v )
				{
					$this->objMemeberDetails->sMemberName = $res02[$row]['owner_name'];
					$this->objMemeberDetails->sUnitNumber = $this->GetUnitNumber($sUnitID);	
					$this->objMemeberDetails->sParkingNumber = $res02[$row]['parking_no'];
					$this->objMemeberDetails->sGender = $res02[$row]["gender"];
					$this->objMemeberDetails->sEmail = $res02[$row]["email"];
					$this->objMemeberDetails->sMobile = $res02[$row]["mob"];
					$this->objMemeberDetails->arListOfMembers = $arListOfOtherMember;
					$this->objMemeberDetails->arListOfTenants = $arListOfActiveTenants;
					$this->objMemeberDetails->sMemberGstinNo = $res02[$row]["owner_gstin_no"];
				}
			}
			else
			{
				//echo "No Data Found from Members database.";
			}
		}
	}
	
	public function getMobileNumber($unitID)
	{
		$Mobile = $this->m_dbConn->select("SELECT mob from member_main where unit = '".$unitID."' And society_id = '".$_SESSION['society_id']."' and 

ownership_status = 1");
		return $Mobile;	
	}
	
	public function LedgerDetails($ledgerID)
	{
		$Sql = "Select l.id,l.ledger_name, ld.GSTIN_No from ledger as l JOIN ledger_details as ld ON l.id = ld.LedgerID where id = '".$ledgerID."'";
		$Result = $this->m_dbConn->select($Sql);
		if(!empty($Result))
		{
			$this->objMemeberDetails->sMemberName = $Result[0]['ledger_name'];
			$this->objMemeberDetails->sMemberGstinNo = $Result[0]["GSTIN_No"];
			$this->objMemeberDetails->sUnitNumber = $Result[0]['id'];
			return $Result;	
		}
		else
		{
			$Sql2 = "Select id,ledger_name from ledger where id = '".$ledgerID."'";
			$Result2 = $this->m_dbConn->select($Sql2);
			$this->objMemeberDetails->sMemberName = $Result2[0]['ledger_name'];
			$this->objMemeberDetails->sUnitNumber = $Result2[0]['id'];
			return $Result2;
		}
		
	}
	
	public function GetEmailIDToSendNotification($UnitID,$GetTenant = true)
	{
		$result = array();

		if($UnitID == 0)
		{
			$sql = "SELECT  mem_other_family.mem_other_family_id as id, mem_other_family.other_email as to_email, mem_other_family.other_name as to_name, IF(mem_other_family.mem_other_family_id > 0, mem_other_family.coowner, 0) as type, unit.unit_id as unit FROM `mem_other_family` JOIN  `member_main` on mem_other_family.member_id = member_main.member_id JOIN `unit` on unit.unit_id = member_main.unit where mem_other_family.send_commu_emails = 1 and member_main.member_id IN (SELECT  member_main.`member_id` FROM (select  `member_id` from `member_main` where `ownership_date` <= NOW()  ORDER BY `ownership_date` desc) as member_id Group BY unit)";
			
			if($GetTenant == true)
			{
				$sql .= " UNION ";
				$sql .= "select tmem.tmember_id as id, tmem.email as to_email, tmem.mem_name as to_name, IF(tmem.tmember_id > 0, 10, 0) as type, tmod.unit_id as unit from tenant_member as tmem JOIN tenant_module as tmod ON tmem.tenant_id = tmod.tenant_id where tmod.end_date > NOW() AND tmem.send_commu_emails=1";
			}
		}
		else
		{
			$sql =  "SELECT  mem_other_family.mem_other_family_id as id, mem_other_family.other_email as to_email, mem_other_family.other_name as to_name, IF(mem_other_family.mem_other_family_id > 0, mem_other_family.coowner, 0) as type, unit.unit_id as unit FROM `mem_other_family` JOIN  `member_main` on mem_other_family.member_id = member_main.member_id JOIN `unit` on unit.unit_id = member_main.unit where mem_other_family.send_commu_emails = 1 and `unit`.unit_id = '" . $UnitID  . "' and member_main.member_id IN (SELECT  member_main.`member_id` FROM (select  `member_id` from `member_main` where `ownership_date` <= NOW()  ORDER BY `ownership_date` desc) as member_id Group BY unit)";
			if($GetTenant == true)
			{
				$sql .= " UNION ";
				$sql .= "select tmem.tmember_id as id, tmem.email as to_email, tmem.mem_name as to_name, IF(tmem.tmember_id > 0, 10, 0) as type, tmod.unit_id as unit from tenant_member as tmem JOIN tenant_module as tmod ON tmem.tenant_id = tmod.tenant_id where tmod.unit_id = $UnitID AND tmod.end_date > NOW() AND tmem.send_commu_emails=1";
			}
		}

		//echo $sql;

		return $this->m_dbConn->select($sql);
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
	
	function GetValuesFromBillRegister($UnitID, $PeriodID, $BillType, $SkipLedger = "")
	{		
		$arr = array();
		$sqlQuery = "select ID, BillRegisterID,TotalBillPayable,BillNumber from billdetails where UnitID='".$UnitID."' and PeriodID= '". $PeriodID."' and 

BillType='".$BillType ."'";
		//echo $sqlQuery;
		$res01 = $this->m_dbConn->select($sqlQuery);
		//print_r($res02);
		if($res01 <> "")
		{
			$BillNumber = $res01[0]["BillNumber"];
			$Billpayable = $res01[0]["TotalBillPayable"];
			$billRegisterID = $res01[0]["ID"];
			
			$sqlQuery = "Select * from billregister where ID = '" . $res01[0]["BillRegisterID"] . "'";
			//echo $sqlQuery;
			$res02 = $this->m_dbConn->select($sqlQuery);
			//print_r($res02);
					
			if($res02 <> "")
			{			
				$iIncrement = 0;
				
				$sql1 = 'SELECT voucher_table.id as VcrId, voucher_table.To, voucher_table.Credit, ledger_table.taxable, 

ledger_table.taxable_no_threshold FROM `voucher` as `voucher_table` join `ledger` as `ledger_table` on voucher_table.To = ledger_table.id WHERE voucher_table.RefNo= 

'.$billRegisterID . ' and voucher_table.RefTableID = ' . TABLE_BILLREGISTER;
				//echo "size:".sizeof($SkipLedger);
				//print_r($SkipLedger);
				if(isset($SkipLedger) && sizeof($SkipLedger) > 0)
				{
					$sLedger = implode($SkipLedger, ",");
					//echo "ledger:".$sLedger;
					if($sLedger != "")
					{
						$sql1 .= " and voucher_table.To NOT IN(".$sLedger .")";	
					}
				}
				$sql1 .= '  ORDER BY `ledger_table`.taxable ASC, voucher_table.id ASC';

				//echo $sql1;
				$res = $this->m_dbConn->select($sql1);
				//print_r($res);
				foreach($res as $row => $v )
				{
					$objBillRegister = new CBillRegister($this->m_dbConn);
					//$objBillRegister->sBillNumber = $res02[0]["BillNumber"];
					$objBillRegister->sBillDate = $res02[0]["BillDate"];
					$objBillRegister->sDueDate = $res02[0]["DueDate"];				
					$objBillRegister->sNotes = $res02[0]["Notes"]; 		//pending : remove from here for optimization
					$objBillRegister->sFont = $res02[0]["font_size"];
					$objBillRegister->BillFor_Msg = $res02[0]["BillFor_Message"];				
					$objBillRegister->BillAmount = $Billpayable;
					$objBillRegister->BillNumber = $BillNumber;				
					$billRegisterID = $res02[0]["ID"];
				
					$objBillRegister->sHeader =$v["To"];
					$objBillRegister->sHeaderAmount = $v["Credit"];
					$objBillRegister->Taxable = $v["taxable"];
					$objBillRegister->Taxable_no_threshold = $v["taxable_no_threshold"];

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
		}
		else
		{
			//echo "No Data Found from billregister table of BillGen database.";
			//echo "No Data Found.";
		}
		return $arr;
	}
	
	
	function GetValuesFromVoucherofDebitCredit($CreditNoteID,$Note_Type)
	{			
		//***Fetch details description about Invoice bill from voucher
		$ToORBy = 'To';
		$DebitOrCredit = 'Credit';
		if($Note_Type == CREDIT_NOTE)
		{
			$ToORBy = 'By';
			$DebitOrCredit = 'Debit';
		}
		$sql1 = 'SELECT voucher_table.id as VcrId, voucher_table.'.$ToORBy.' as key1, voucher_table.'.$DebitOrCredit.' As Credit, ledger_table.ledger_name FROM 

`voucher` as `voucher_table` join `ledger` as `ledger_table` on voucher_table.'.$ToORBy.' = ledger_table.id WHERE voucher_table.RefNo= '.$CreditNoteID.' and 

voucher_table.RefTableID = ' .TABLE_CREDIT_DEBIT_NOTE. ' order by voucher_table.SrNo';
		$res = $this->m_dbConn->select($sql1);
		return $res;
	}
	
	function GetValuesFromSaleInvoice($Inv_ID)
	{			
			//***Fetch details description about Invoice bill from voucher
	
			 $sql1 = 'SELECT voucher_table.id as VcrId, voucher_table.To as key1, voucher_table.Credit, ledger_table.ledger_name, ledger_table.ledger_name  

FROM `voucher` as `voucher_table` join `ledger` as `ledger_table` on voucher_table.To = ledger_table.id WHERE voucher_table.RefNo= '.$Inv_ID.' and 

voucher_table.RefTableID = ' .TABLE_SALESINVOICE . ' order by voucher_table.SrNo';
		
			$res = $this->m_dbConn->select($sql1);
			
		
		return $res;
		
	}
	
	function getInvoiceBillDate($unitID,$invNumber)
	{
		//***Fetch Date 
		
		$getInvoiceBill="select Inv_Date from sale_invoice where UnitID='".$unitID."' and Inv_Number='".$invNumber."'";
		$getInvoiceBillResult = $this->m_dbConn->select($getInvoiceBill);
		return $getInvoiceBillResult;
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
	
	public function getPreviousPeriodData($PeriodID,$IsrequestfromSetGSTNoThreshold = false)
	{
			$sqlPrevQuery = "Select Type, YearID, PrevPeriodID,BeginingDate,EndingDate,Status from period where ID= '".$PeriodID."'";			
			$Prevresult = $this->m_dbConn->select($sqlPrevQuery);
			$PrevPeriodID = -1;
			if(sizeof($Prevresult))
			{
				$Type = $Prevresult[0]['Type'];			
				$YearID = $Prevresult[0]['YearID'];			
				$PrevPeriodID = $Prevresult[0]['PrevPeriodID'];			
			}
			if($IsrequestfromSetGSTNoThreshold == true)
			{
				return $Prevresult;
			}
			else
			{
				return $PrevPeriodID;	
			}
				
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
	
	function getBeginEndReceiptDate($UnitID, $PeriodID, $BillType = 0)
	{
		$currentDateSql = "Select BillDate from billregister where PeriodID = '" . $PeriodID . "' AND BillType = '" . $BillType . "' ORDER BY ID DESC LIMIT 1";
		$resultCurrentDate = $this->m_dbConn->select($currentDateSql);
		
		$EndDate = $resultCurrentDate[0]['BillDate'];
		
		if($EndDate <> '')
		{
			$EndDate = $this->GetDateByOffset($EndDate, -1);
		}
		
		$PrevPeriodID = $this->getPreviousPeriodData($PeriodID);
		$previousDateSql = "Select BillDate from billregister where PeriodID = '" . $PrevPeriodID . "' AND BillType = '" . $BillType . "' ORDER BY ID DESC 

LIMIT 1";
		$resultPreviousDate = $this->m_dbConn->select($previousDateSql);
		
		$StartDate = $resultPreviousDate[0]['BillDate'];
		
		$aryDate = array();
		$aryDate['BeginDate'] = $StartDate;
		$aryDate['EndDate'] = $EndDate;
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
		 		$sqlCheck = "select * from chequeentrydetails where voucherdate >= '". $StartDate . "' AND voucherdate <= '" . $EndDate . "' AND PaidBy 

= " . $UnitID . " AND chequeentrydetails.IsReturn = 0 ";
			}
			else
			{
				$sqlCheck = "select * from chequeentrydetails where voucherdate >= '". $StartDate . "' AND voucherdate <= '" . $EndDate . "' AND PaidBy 

= " . $UnitID ;
			}
		}
		else if($_REQUEST["cycle"] <> "")
		{
			//$voucherNo = $obj_display_bills->getVoucherNo($chequeDetailsExtra[$j]['ID']);
			//echo $sqlCheck = "select * from chequeentrydetails where voucherdate >= '". $EndDate . "'  AND PaidBy = " . $UnitID." ";
			$sqlCheck = "select chequeentrydetails.ID,periodtbl.ID as PeriodID,Amount,PayerBank,PayerChequeBranch,ChequeDate,ChequeNumber from 

chequeentrydetails JOIN `period` as periodtbl on   chequeentrydetails.voucherdate >= periodtbl.BeginingDate and  chequeentrydetails.voucherdate <= periodtbl.EndingDate 

where  voucherdate <= '". $EndDate . "'  AND  voucherdate >= '". $StartDate. "' AND PaidBy = " . $UnitID." AND periodtbl.Billing_Cycle = ".$BillingCycle." ";	
		}
		else
		{
			 $sqlCheck = "select chequeentrydetails.ID,periodtbl.ID as PeriodID,Amount,PayerBank,PayerChequeBranch,ChequeDate,ChequeNumber from 

chequeentrydetails JOIN `period` as periodtbl on   chequeentrydetails.voucherdate > periodtbl.BeginingDate and  chequeentrydetails.voucherdate <= periodtbl.EndingDate 

where  voucherdate >= '". $EndDate . "'  AND PaidBy = " . $UnitID." AND periodtbl.Billing_Cycle = ".$BillingCycle." ";	
		}
		
		
		//$sqlCheck = "select * from chequeentrydetails where  PaidBy = " . $UnitID;

		//echo '<br/>SqlCheck : ' . $sqlCheck;

		$resultCheck = $this->m_dbConn->select($sqlCheck);
		
		return $resultCheck;
	}
	
	function getReceiptDetailsEx($UnitID, $PeriodID, $show = false, $BillingCycle = 0, $IsBill = false,$BillType = 0)
	{	
		
		$StartDate;
		if($_REQUEST["cycle"] <> "" && $_REQUEST["cycle"] <> 0 && $BillingCycle <> 0)
		{
			$PeriodID=$this->getNextPeriodID($PeriodID);
		}
		$Prevresult = $this->getBeginEndReceiptDate($UnitID, $PeriodID);
		
		$ledgername_array = array();
		$get_ledger_name ="select id,ledger_name from `ledger`";
		$result02 = $this->m_dbConn->select($get_ledger_name);
		
		for($i = 0; $i < sizeof($result02); $i++)
		{
			$ledgername_array[$result02[$i]['id']] = $result02[$i]['ledger_name'];	
			
		}
		
		if(!is_null($Prevresult))
		{
			if($_SESSION['society_id'] == 136)  // GARDENIA VASANT VALLEY RAGHUKUL CO-OP HOUSING SOCIETY LTD.
			{
				//$StartDate = '2016-04-01';
				$StartDate = '2018-04-01';	
			}
			else
			{
				$StartDate = $Prevresult['BeginDate'];
			}
			
			//$StartDate = $Prevresult['BeginDate'];
			$EndDate = $Prevresult['EndDate'];												
		}
		
		//if($StartDate == '')
		//	return;
		
		if($show== false)
		{			
			if($IsBill == true)
			{
		 		$sqlCheck = "select ChequeDate as Date,VoucherDate, PayerBank, PayerChequeBranch,ChequeNumber,BankID,Amount,BillType, IsReturn from chequeentrydetails where voucherdate >= '". $StartDate . "'";
				if($EndDate <> '')
				{
					$sqlCheck .= " AND voucherdate <= '" . $EndDate . "'";
				}
				//$sqlCheck .= " AND PaidBy = " . $UnitID . " AND chequeentrydetails.IsReturn = 0";
				$sqlCheck .= " AND PaidBy = " . $UnitID;
			}
			else
			{
				$sqlCheck = "select ChequeDate as Date,VoucherDate, PayerBank, PayerChequeBranch,ChequeNumber,BankID,Amount,BillType, IsReturn from chequeentrydetails where voucherdate >= '". $StartDate . "'";
				if($EndDate <> '')
				{
					$sqlCheck .= " AND voucherdate <= '" . $EndDate . "'";	
				}
				//$sqlCheck .= " AND PaidBy = '" . $UnitID . "'" ;
				$sqlCheck .= " AND PaidBy = '" . $UnitID . "'  AND BillType = '" . $BillType."' AND chequeentrydetails.IsReturn = 0";
			}			
		}
		else if($_REQUEST["cycle"] <> "")
		{
			$sqlCheck = "select chequeentrydetails.ID,periodtbl.ID as PeriodID,Amount,PayerBank,PayerChequeBranch,ChequeDate as Date,ChequeNumber,BankID,BillType,chequeentrydetails.VoucherDate, IsReturn from chequeentrydetails JOIN `period` as periodtbl on chequeentrydetails.voucherdate >= periodtbl.BeginingDate and  chequeentrydetails.voucherdate <= periodtbl.EndingDate where ";
			if($EndDate <> '')
			{
			 	$sqlCheck .= "voucherdate <= '". $EndDate . "' AND ";
			}
			$sqlCheck .= "voucherdate >= '". $StartDate. "' AND PaidBy = " . $UnitID." AND periodtbl.Billing_Cycle = ".$BillingCycle." ";	
		}
		else
		{
			 $sqlCheck = "select chequeentrydetails.ID,periodtbl.ID as PeriodID,Amount,PayerBank,PayerChequeBranch,ChequeDate,ChequeNumber,BillType from 

chequeentrydetails JOIN `period` as periodtbl on chequeentrydetails.voucherdate > " . $StartDate . "  and  chequeentrydetails.voucherdate <= " . $EndDate . " where 

voucherdate >= '" . $EndDate . "'  AND PaidBy = " . $UnitID." AND periodtbl.Billing_Cycle = ".$BillingCycle." ";	
			$sqlCheck = "select chequeentrydetails.ID, Amount, PayerBank, PayerChequeBranch, ChequeDate as Date, ChequeNumber,BankID,VoucherDate,IsReturn from 

chequeentrydetails where voucherdate > '" . $EndDate . "'  AND PaidBy = '" . $UnitID."' AND BillType = '" . $BillType."' " ;	
		}
			
		$SelectCreditDebitNote = "SELECT  TotalPayable as Amount, Note_No, Note_Type as BillType, Date as Date,Date as VoucherDate FROM  credit_debit_note 

WHERE UNITID = '".$UnitID."' and BillType = '".$BillType."' and Note_Type = '".CREDIT_NOTE."' and `Date` between '".$StartDate."' and '".$EndDate."'";
		$CreditDebitResult = $this->m_dbConn->select($SelectCreditDebitNote);
		
		for($i = 0 ; $i < count($CreditDebitResult); $i++)
		{
			$CreditDebitResult[$i]['ChequeNumber'] =  '-';
			$CreditDebitResult[$i]['PayerBank'] =  '-';
			$CreditDebitResult[$i]['PayerChequeBranch'] =  '-';
			$CreditDebitResult[$i]['IsReturn'] = 0; 
		}		
		//$sqlCheck = "select * from chequeentrydetails where  PaidBy = " . $UnitID;

		//echo '<br/>SqlCheck : ' . $sqlCheck;

		$resultCheck = $this->m_dbConn->select($sqlCheck);
		
		if(sizeof($resultCheck) > 0 )
		{
			for($i = 0; $i < sizeof($resultCheck); $i++)
			{
				$resultCheck[$i]['BankID'] = $ledgername_array[$resultCheck[$i]['BankID']];
				
			}
		}
		
		//This is to sort the merge array with date
		function cmpdate($a, $b)
		{
			$ad = strtotime($a['Date']);
			$bd = strtotime($b['Date']);
			return ($bd-$ad);
		}
		
		if(!empty($CreditDebitResult))
		{
			if(!empty($resultCheck))
			{
				//Merging CreditDebit and Receipt array
				$resultCheck = array_merge($resultCheck, $CreditDebitResult);
			}
			else
			{
				$resultCheck = $CreditDebitResult;
			}

			usort($resultCheck, 'cmpdate');		
		}
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
		$detailsquery = 'SELECT unittable.area,unittable.floor_no,wingtable.wing, unittable.taxable_no_threshold FROM `unit` as `unittable` join `wing` as 

`wingtable` on unittable.wing_id = wingtable.wing_id and `unit_id` = '.$UnitID;
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

	function GetBillFor_2($sPeriodID, $BillType)
	{
		$RetrunVal = "";
		//check if Billfor_Message
		$sqlBillRegister = "SELECT `BillFor_Message` FROM `billregister` where `PeriodID`='".$sPeriodID."' AND `BillType`='".$BillType . "' ";
		//echo $sqlBillRegister;
		$BillRegResult = $this->m_dbConn->select($sqlBillRegister);
		//print_r($BillRegResult);
		//echo "Msg:".$BillRegResult[0]["BillFor_Message"];
		$BillMsg =$BillRegResult[0]["BillFor_Message"];
		if($BillMsg != "")
		{
			$RetrunVal =  $BillMsg;
		}
		else
		{
			$RetrunVal = $this->GetBillFor($sPeriodID);			
		}
		return $RetrunVal;
	}


	function GetBillFor($sPeriodID)
	{
		$RetrunVal = "";
		//check if Billfor_Message
		$sqlBillRegister = "SELECT `BillFor_Message` FROM `billregister` where `PeriodID`='".$sPeriodID."' AND `BillType`='".$res[0]['BillType']."' ";
		//echo $sqlBillRegister;
		$BillRegResult = $this->m_dbConn->select($sqlBillRegister);
		//print_r($BillRegResult);
		//echo "Msg:".$BillRegResult[0]["BillFor_Message"];
		$BillMsg =$BillRegResult[0]["BillFor_Message"];
		if($BillMsg != "")
		{
			$RetrunVal =  $BillMsg;
		}
		
		$sqlQuery = "SELECT periodtable.Type, periodtable.EndingDate, yeartable.YearDescription FROM period AS periodtable JOIN year AS yeartable ON 

periodtable.YearID = yeartable.YearID WHERE periodtable.id =".$sPeriodID;
		$res02 = $this->m_dbConn->select($sqlQuery); 
		$RetrunVal = "";
		if($res02 <> "")
		{
			$startyear = array();
			$EndDate = array();
			foreach($res02 as $row => $v )
			{
					$startyear = explode('-',$res02[$row]["YearDescription"]);
					$EndDate = explode('-',$res02[$row]["EndingDate"]);
					$RetrunVal = $res02[$row]["Type"];
					if($startyear[0] >= $EndDate[0])
						{
							$RetrunVal .= " " . $startyear[0];
						}
					else if($startyear[0] <= $EndDate[0])
						{
							$RetrunVal .= " " . $EndDate[0];
						}
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
		$sql = 'SELECT `show_wing`,`show_parking`,`show_area`, `bill_method`, `show_share`, `bill_footer`,`bill_due_date`,`show_floor` FROM `society` WHERE 

`society_id` = '.$society_ID;
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	function GetBillingCycle($SocietyID)
	{
			
	}
	
	function GetBillDate($SocietyID, $PeriodID, $BillType = 0)
	{
		$sql = "Select BillDate, DueDate from billregister where SocietyID = '"  . $SocietyID . "' and PeriodID = '" . $PeriodID . "' and BillType='". 

$BillType ."'";
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
	
	/*
	BillType = 0 (Payable Regular Bill)
	BillType = 1 (Payable Supplementary Bill)
	BillType = 2 (Payable Final Dues)
	*/
	function GetTotalBillPayable($period_id, $unitID, $billType = 2)
	{
		$TotalPayable = 0;
		if($billType == 2)
		{
			$sql = "SELECT SUM(`Debit`) - SUM(`Credit`) AS 'Total' FROM `assetregister` WHERE `LedgerID` = '".$unitID."'";	
			$details = $this->m_dbConn->select($sql);
			$TotalPayable = $details[0]['Total'];
		}
		else
		{
			$sql = 'select `PrincipalArrears`,`AdjustmentCredit`, `BillNumber`, `InterestArrears`, `BillInterest`, `TotalBillPayable`, 

`PrevInterestArrears`, `BillSubTotal`, `TotalBillPayable` from `billdetails` where `PeriodID` = "' . $period_id . '" and `unitID` = "'.$unitID.'" and `BillType` = "' . 

$billType . '"'; 	
			$details = $this->m_dbConn->select($sql);
			//$TotalPayable = $details[0]['BillSubTotal'] + $details[0]['AdjustmentCredit']  + $details[0]['BillInterest'] + $details[0]['InterestArrears'] 

+$details[0]['PrincipalArrears'];   
			$TotalPayable = $details[0]['TotalBillPayable'];
		}		
		
		return $TotalPayable;		
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
						<table align="center" border="1" bordercolor="#CCCCCC" cellpadding="0" cellspacing="0" width="600" style="border-

collapse: collapse;">
						   <tr>
							 <td align="center" bgcolor="#D9EDF7" style="padding: 30px 0 20px 0;border-bottom:none;">
							  <img src="http://way2society.com/images/logo.png" alt="Way2Society.com"  style="display: block;" />
							  <br />
							  <i><font color="#43729F" size="4"><b> Way2Society.com - Housing Society Social & Accounting Software 

</b></font></i>
							 </td>
						   </tr>
						   <tr>
							 <td bgcolor="#ffffff" style="padding-top:20px; padding-bottom:20px; padding-left:10px; padding-

right:10px;border-top:none;border-bottom:none;" >
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
										<a href="https://twitter.com/way2society" target="_blank"><img 

src="http://way2society.com/images/icon2.jpg" alt=""></a>                  
									</td>
									<td style="font-size: 0; line-height: 0;" width="20">&nbsp;&nbsp;</td>
									<td>
										<a href="https://www.facebook.com/way2soc" target="_blank"><img 

src="http://way2society.com/images/icon1.jpg" alt=""></a>                 
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
		$sql = "SELECT unittypetbl.description  as description FROM `unit` as unittbl JOIN `unit_type` as unittypetbl on unittbl.unit_presentation = 

unittypetbl.id where unittbl.unit_id = '".$UnitID."' ";	
		$details = $this->m_dbConn->select($sql);
		return $details[0]['description'];		
	}
	
	public function getLatestPeriodID($unitID)
	{
		$sql = "SELECT `PeriodID` FROM `billdetails` WHERE `UnitID` = '".$unitID."' ORDER BY `ID`";	
		$period = $this->m_dbConn->select($sql);	
		return $period[sizeof($period) - 1]['PeriodID'];
	}
	
	public function getlatestbillstartdate()
	{
		$Lastest_Bill_Date = "SELECT BeginingDate FROM period as p JOIN society as s ON p.ID = s. M_PeriodID";
		$Result = $this->m_dbConn->select($Lastest_Bill_Date);
		return getDisplayFormatDate($Result[0]['BeginingDate']);
	}
	
	public function getDataForI_register($iID)
	{
		$sql01 = "SELECT u.`nominee_name`, mm.`owner_name`, mm.`alt_address`, mm.`ownership_date`, mm.`member_id`, mm.`unit`, mm.`iid`,u.`share_certificate`, 

u.`unit_no`, (((u.`share_certificate_to`) - (u.`share_certificate_from`)) + 1) as `no_of_shares`, s.`amt_per_share`, mm.`ownership_status`, mm.`transfer_reason` FROM 

`unit` u, `member_main` mm, `society` s WHERE u.`unit_id` = mm.`unit` AND s.`society_id` = '".$_SESSION['society_id']."' AND mm.`iid` = '".$iID."'";
		$sql11 = $this->m_dbConn->select($sql01);
		return $sql11;
	}
	
	public function getDataForJ_register()
	{
		$sql01 = "SELECT mm.`owner_name`, mm.`alt_address`, u.`unit_no`, mm.`iid` FROM `member_main` mm, `unit` u WHERE mm.`unit` = u.`unit_id`";
		$sql11 = $this->m_dbConn->select($sql01);
		
		$sql2 = "SELECT * FROM `society` WHERE `society_id` = '".$_SESSION['society_id']."'";
		$sql2_res = $this->m_dbConn->select($sql2);
		
		$table = "<table style='width:100%' id='j_table'><tr><th width='5%'>Serial No.</th><th width='40%'>Full Name of the Member</th><th 

width='40%'>Address</th><th width='15%'>Class of Member</th></tr>";
		
		for($i = 0; $i < sizeof($sql11); $i++)
		{
			$table .= "<tr><td>".$sql11[$i]['iid']."</td><td>".$sql11[$i]['owner_name']."</td>";
			
			if($sql11[$i]['alt_address'] == "")
			{
				$sql11[$i]['alt_address'] = $sql11[$i]['unit_no'].", ".$sql2_res[0]['society_name'].", ".$sql2_res[0]['society_add'];
			}
			
			$table .= "<td>".$sql11[$i]['alt_address']."</td><td></td></tr>";
		}
		
		$table .= "</table>";

		return $table;
	}
	
	public function getDataForShare_register()
	{
		$sql01 = "SELECT mm.`iid`, mm.`unit`, u.`unit_id`, u.`share_certificate`, mm.`owner_name`, mm.`ownership_date`, u.`share_certificate_from`, 

u.`share_certificate_to`, s.`amt_per_share`, mm.`ownership_status`, mm.`member_id` FROM `member_main` mm, `unit` u, `society` s WHERE mm.unit = u.unit_id AND 

s.`society_id` = '".$_SESSION['society_id']."' ORDER BY mm.`iid`";
		$sql11 = $this->m_dbConn->select($sql01);
		
		for($i = 0; $i < sizeof($sql11); $i++)
		{
			if($sql11[$i]['ownership_status'] == 0)
			{
				for($j = ($i+1); $j < sizeof($sql11); $j++)
				{
					if($sql11[$i]['unit_id'] == $sql11[$j]['unit_id'])
					{	
						$sql11[$i]['second_owner'] = $sql11[$j]['owner_name'];
						$sql11[$i]['second_ownership_date'] = $sql11[$j]['ownership_date'];
						break;
					}
				}
			}
		}
		
		return $sql11;
	}
	
	/*public function getDataForNomination_register($unitID)
	{
		$sql01 = "select mm.unit, mm.primary_owner_name, u.unit_id, u.nomination, u.nominee_name from unit u, member_main mm where u.nomination != 0 and 

mm.ownership_status = 1 and u.unit_id = mm.unit and u.unit_id = '".$unitID."'";
		$sql11 = $this->m_dbConn->select($sql01);
		return $sql11;
	}*/
	
	public function getDataForNomination_register()
	{
		$sql01 = "SELECT nf.`nomination_id`, nf.`member_id`, nf.`timestamp`, mm.`owner_name` FROM `nomination_form` nf, `member_main` mm WHERE nf.`status` = 

'Y' AND nf.`member_id` = mm.`member_id` AND nf.`nomination_status` = '3'";
		$sql11 = $this->m_dbConn->select($sql01);
		
		$table = '<table style="width:100%" border="1">
        			<tr>
		            	<th style="width:5%"><center>Serial No.</center></th>
        		        <th style="width:25%"><center>Name of the Member making Nomination</center></th>
                		<th style="width:8%"><center>Date of Nomination</center></th>
		                <th style="width:25%"><center>Name/s of Nominee/s &amp; Address/es of the Nominee/s</center></th>
        		        <th style="width:8%"><center>Date of the Managing Committee Meeting in which the Nomination was recorded</center></th>
                		<th style="width:9%"><center>*Date of any subsequent revocation of Nomination</center></th>
		                <th style="width:20%"><center>Remarks</center></th>
        		    </tr>
					<tr>';	
		
		for($i=1;$i<=7;$i++)
		{
           	$table .= '<th><center>'.$i.'</center></th>';
		}
		
		$table .= '</tr>';
		
		for($i = 0; $i < sizeof($sql11); $i++)
		{
			$sql02 = "SELECT * FROM `nomination_details` WHERE `nomination_id` = '".$sql11[$i]['nomination_id']."'";
			$sql22 = $this->m_dbConn->select($sql02);

			$table .= '<tr>
					   <td rowspan="'.(sizeof($sql22)).'">'.($i + 1).'</td>
					   <td rowspan="'.(sizeof($sql22)).'">'.$sql11[$i]['owner_name'].'</td>
					   <td rowspan="'.(sizeof($sql22)).'">'.getDisplayFormatDate($sql11[$i]['timestamp']).'</td>
					   <td>'.$sql22[0]['nominee_name'].'<br>'.$sql22[0]['nominee_address'].'</td>
					   <td rowspan="'.(sizeof($sql22)).'"></td>
					   <td rowspan="'.(sizeof($sql22)).'"></td>
					   <td rowspan="'.(sizeof($sql22)).'"></td>';
			$table .= '</tr>';
					   
			for($j = 1; $j < sizeof($sql22); $j++)
			{
				$table .= '<tr><td>'.$sql22[$j]['nominee_name'].'<br>'.$sql22[$j]['nominee_address'].'</td></tr>';
			}				   
			
		}
		
		$table .= '</table>';
		
		return $table;
	}
}
?>