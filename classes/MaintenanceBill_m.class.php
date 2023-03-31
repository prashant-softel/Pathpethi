<?php
include_once("include/display_table.class.php");
include_once("include/dbop.class.php");

class Display_Maintenance_Bill extends dbop
{
	public $m_dbConn;
	public $objFetchData;
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->objFetchData = new FetchData($dbConn);
	}
	
	function fetchBillDetails($unitID)
	{
		$sql = 'SELECT * FROM `billdetails` Where UnitID = "' . $unitID . '" ORDER BY PeriodID ASC';
		$result = $this->m_dbConn->select($sql);
		
		for($i = 0; $i < sizeof($result); $i++)
		{
			if($i == 0 && $this->getIsOpeningBill($result[$i]['PeriodID']) == true)
			{
				$result[$i]['BillNumber'] = '';
				$result[$i]['BillDate'] = '';
				$result[$i]['DueDate'] = '';
				$result[$i]['BillFor'] = 'Opening Balance';
			}
			else
			{
				$sql = 'Select * from billregister where ID ='.$result[$i]['BillRegisterID'];
				$res = $this->m_dbConn->select($sql);
				
				$result[$i]['BillDate'] = $res[0]['BillDate'];
				$result[$i]['DueDate'] = $res[0]['DueDate'];
										 
				$result[$i]['BillFor'] = $this->objFetchData->GetBillFor($result[$i]['PeriodID']);
			}
		}				
		return $result;
	}
	
	function getBillPDFLink($unitID, $billFor, $periodID, $BillType)
	{
		$societyDetails = $this->objFetchData->GetSocietyDetails($this->objFetchData->GetSocietyID($unitID));
		$societyCode = $this->objFetchData->objSocietyDetails->sSocietyCode;
		
		$memberDetails = $this->objFetchData->GetMemberDetails($unitID);
		$unitNumber = $this->objFetchData->objMemeberDetails->sUnitNumber;
		
		$billLink = 'maintenance_bills/' . $societyCode . '/' . $billFor . '/bill-' . $societyCode . '-' . $unitNumber . '-' . $billFor .'-'.$BillType.'.pdf';
		
		$billLink = 'Maintenance_bill.php?UnitID=' . $unitID . "&PeriodID=" . $periodID ."&BT=".$BillType;
		
		return $billLink;
	}	
	
	function getVoucherNo($refno)
	{
		$sql = 'SELECT `VoucherNo` FROM `voucher` WHERE `RefNo` = "'.$refno.'" AND `RefTableID` = 2';
		$result = $this->m_dbConn->select($sql);
		return $result[0]['VoucherNo'];
	}
	
	function getIsOpeningBill($periodID)
	{
		$sql = 'Select YearID, IsYearEnd from period where ID = "' . $periodID . '"';
		$result = $this->m_dbConn->select($sql);
		
		if($result[0]['IsYearEnd'] == 1 && $result[0]['YearID'] == ($_SESSION['society_creation_yearid'] -1) )
		{
			return true;
		}
		return false;
	}
}
?>