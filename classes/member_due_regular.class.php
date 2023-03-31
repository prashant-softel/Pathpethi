<?php
include_once("dbconst.class.php");
include_once("utility.class.php");

class memberDuesRegular extends dbop
{
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_utility;
	
	function __construct($dbConn,$dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot= $dbConnRoot;
		$this->obj_utility = new utility($this->m_dbConn);

	}
	
	public function getMemberDuesRegular($dues, $wing,$to)
	{
		$memberIDS = $this->obj_utility->getMemberIDs(getDBFormatDate($to));	
		$wingsCollection = array();
		$result = array();
		//$max_period = "select M_PeriodID from `society` where society_id=".$_SESSION['society_id']." ";
		$max_period = "select `ID` as 'M_PeriodID' from `period` WHERE ('" . getDBFormatDate($to) . "' BETWEEN `BeginingDate` and `EndingDate`)";
		$data=$this->m_dbConn->select($max_period);	
		
		$getPeriod ="select yeartbl.BeginingDate from `period` as periodtbl JOIN `society` as societytbl on periodtbl.Billing_cycle = societytbl.bill_cycle  JOIN `year` as yeartbl on yeartbl.YearID=periodtbl.YearID where societytbl.society_id =".$_REQUEST["sid"]." and  yeartbl.YearID= ".$_SESSION['default_year']." ";
		$period = $this->m_dbConn->select($getPeriod);
		$from = $period[0]['BeginingDate'];
		
		//$sql = "SELECT billregistertbl.BillDate,billtbl.UnitID,unittbl.unit_no,( billtbl.BillSubTotal + billtbl.PrincipalArrears ) as Principal,( billtbl.BillInterest + billtbl.InterestArrears ) as Interest,membertbl.owner_name FROM `billdetails` as billtbl JOIN `billregister` as billregistertbl on billregistertbl.id=billtbl.BillRegisterID JOIN `unit` as unittbl on billtbl.UnitID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id  where unittbl.society_id=".$_SESSION['society_id']." and billtbl.PeriodID=".$data[0]['M_PeriodID']."  and billregistertbl.BillDate BETWEEN '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."'   and membertbl.member_id IN (SELECT MAX(member_id) FROM `member_main` where ownership_date <='".getDBFormatDate($from)."' GROUP BY unit)";		
		 $sql = "SELECT billregistertbl.BillDate,billtbl.UnitID,unittbl.unit_no,( billtbl.BillSubTotal + billtbl.PrincipalArrears ) as Principal,( billtbl.BillInterest + billtbl.InterestArrears ) as Interest,membertbl.owner_name,membertbl.member_id FROM `billdetails` as billtbl JOIN `billregister` as billregistertbl on billregistertbl.id=billtbl.BillRegisterID JOIN `unit` as unittbl on billtbl.UnitID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id  where unittbl.society_id=".$_SESSION['society_id']." and billtbl.PeriodID=".$data[0]['M_PeriodID']."  and billregistertbl.BillDate BETWEEN '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."'   and membertbl.member_id IN (".$memberIDS.")";	
		if($dues <> "")
		{
			$sql .= " HAVING Principal > ".$dues." ORDER BY unittbl.sort_order";	
		}								
		$res = $this->m_dbConn->select($sql); 
		
		$sqlWing = 'SELECT unittbl.unit_id, wingtbl.wing from `unit` as unittbl JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id';
		if($wing <> "")
		{
			$sqlWing .= ' WHERE wingtbl.wing = "'.$wing.'"';	
		}		
		$wing_details = $this->m_dbConn->select($sqlWing);
		for($i = 0; $i < sizeof($wing_details); $i++)
		{			
			$wingsCollection[$wing_details[$i]['unit_id']] = $wing_details[$i]['wing'];
		}
		
		for($i = 0; $i < sizeof($res); $i++)
		{
			if($wingsCollection[$res[$i]['UnitID']] <> "")
			{
				$final = array();
				$final['BillDate'] = $res[$i]['BillDate'];
				$final['UnitID'] = $res[$i]['UnitID'];
				$final['UnitNo'] = $res[$i]['unit_no'];
				$final['Principal'] = $res[$i]['Principal'];
				$final['Interest'] = $res[$i]['Interest'];
				$final['owner_name'] = $res[$i]['owner_name'];
				$final['member_id'] = $res[$i]['member_id'];
				$final['Wing'] = $wingsCollection[$res[$i]['UnitID']];
				array_push($result, $final);				
			}
		}		 
		return $result;				
	}
				
	public function getAllPaymentDetails($uid,$billdate,$principal,$interest,$to)
	{
		$todate = DateTime::createFromFormat('d-m-Y', $to);
		$dateTo =$todate->format('Y-m-d');
		$today = date("Y-m-d");
		$calulatedAmount = array();
		$calulatedTotalAmount = array();
		 $sql = "SELECT VoucherDate,ChequeDate,ChequeNumber,sum(Amount) as 'Amount',PaidBy,IsReturn from `chequeentrydetails` where  PaidBy=".$uid." and IsReturn=0 and ChequeDate  between '".$billdate."' and '".$today."' ";
		$res = $this->m_dbConn->select($sql); 
		$Amount = $res[0]['Amount'];
		
		
	   $sql2="Select MAX(ChequeDate) as ChequeDate, MAX(VoucherDate) as VoucherDate from chequeentrydetails where PaidBy='".$uid."' and VoucherDate <= '".$dateTo."'";
		$res2 = $this->m_dbConn->select($sql2);
	 
		 $date=$res2[0]['ChequeDate'];
		 $Vdate=$res2[0]['VoucherDate'];
		//echo $Vdate;
		//echo $date;
		if($Vdate <> '')
		{
		 $days = $this->obj_utility->getDateDiff($dateTo, $Vdate);
		}
		else
		{
			$sql3="Select left(`locked`,10) as dt from dbname  where society_id='".$_SESSION['society_id']."'";
			$res3 = $this->m_dbConnRoot->select($sql3); 
			//echo $res3;
			$lockeDate=	$res3[0]['dt'];
			$date = DateTime::createFromFormat('m-d-Y', $lockeDate);
			$date = $date->format('Y-m-d');
			//echo $date;
			$days = $this->obj_utility->getDateDiff($dateTo, $date);
			//echo $days;
			
			
		}
		if($Amount > 0)
		{
			if($interest > 0)
			{
				$Amount = $Amount-$interest;
				if($Amount > 0 )
				{
					$interest = 0;	
					$principal = $principal-$Amount;	
					$calulatedAmount["interest"] = $interest;
					$calulatedAmount["principal"] = $principal;
					$calulatedAmount["ChequeDate"] = $date;
					$calulatedAmount["DiffDate"] = $days;
				}
				array_push($calulatedTotalAmount,$calulatedAmount);
			}
			else
			{
				$principal = $principal-$Amount;
				$calulatedAmount["interest"] = $interest;
				$calulatedAmount["principal"] = $principal;
				$calulatedAmount["ChequeDate"] = $date;
				$calulatedAmount["DiffDate"] = $days;
				array_push($calulatedTotalAmount,$calulatedAmount);
			}
				
		}
		else
		{
			$calulatedAmount["interest"] = $interest;
			$calulatedAmount["principal"] = $principal;
			$calulatedAmount["ChequeDate"] = $date;
			$calulatedAmount["DiffDate"] = $days;
			array_push($calulatedTotalAmount,$calulatedAmount);
		}
	
	return $calulatedTotalAmount;
		
		
	}


	public function getWing($uid)
	{
		
		$sql = "select wingtbl.wing from `unit` as unittbl JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id where unittbl.unit_id=".$uid."  ";
		$data = $this->m_dbConn->select($sql);
		return $data[0]['wing'];	
		
	}

}



?>