<?php
include_once("dbconst.class.php");
include_once("utility.class.php");

class report extends dbop
{
	public $m_dbConn;
	public $obj_utility;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->obj_utility = new utility($this->m_dbConn);
	}
	
	
	public function show_society_name($name)
	{
	  
	  $sql = "select society_name from society where society_id='".$name."'";
	  $data = $this->m_dbConn->select($sql);
	  return $data[0]['society_name'];
	
	}
	
	public function show_mem_due_details($from, $to, $wing, $BillType)
	{
		$memberIDS = $this->obj_utility->getMemberIDs(getDBFormatDate($to));	
		if($from == '')
		{
			$getPerod = "select yeartbl.BeginingDate from `period` as periodtbl JOIN `society` as societytbl on periodtbl.Billing_cycle = societytbl.bill_cycle  JOIN `year` as yeartbl on yeartbl.YearID=periodtbl.YearID where societytbl.society_id =".$_REQUEST["sid"]." and  yeartbl.YearID= ".$_SESSION['default_year']." ";
			//$period = $this->m_dbConn->select($getPerod);
			//$from =	$period[0]['BeginingDate'];
			
		}
		//$sql ="select wingtbl.wing as wing,wingtbl.wing_id as wing_id,membertbl.owner_name as member,membertbl.member_id as member_id, unittbl.unit_no as unit,sum(Debit)-sum(Credit) as amount, unittbl.unit_id as unit_id from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id JOIN society as societytbl on unittbl.society_id=societytbl.society_id where societytbl.society_id=".$_REQUEST["sid"]." and assettbl.Date Between '".getDBFormatDate($from)."' and '".getDBFormatDate($to)."'";
		
		$strBillType = "";
		$strChequeType = "";

		//echo "BT:".$BillType;
		if($BillType ==  0 || $BillType ==  1 )  // Maintenance bill OR Supplementary bill
		{ 
			$strBillType = " and billdet.BillType=". $BillType;
			$strChequeType = " and chqdet.BillType=". $BillType ."";

		}
		$strJVFilter = "";
		if($BillType ==  0 || $BillType ==  2 ) // Maintenance bill OR Combined
		{
			$strJVFilter = " OR assettbl.VoucherTypeID='5' OR assettbl.VoucherTypeID='2' ";
		}
		/*$sql = "select wingtbl.wing as wing,wingtbl.wing_id as wing_id,membertbl.owner_name as member,membertbl.member_id as member_id, unittbl.unit_no as unit,sum(assettbl.Debit)-sum(assettbl.Credit) as amount, unittbl.unit_id as unit_id from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id LEFT JOIN `billdetails` as billdet on vchrtbl.RefNo=billdet.ID LEFT JOIN `chequeentrydetails` as chqdet on (vchrtbl.RefNo=chqdet.ID AND chqdet.IsReturn=0) JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id JOIN `society` as societytbl on unittbl.society_id=societytbl.society_id where societytbl.society_id=".$_REQUEST["sid"]." " . $strBillType . "and assettbl.Date <= '".getDBFormatDate($to)."'  and (". $strJVFilter ."vchrtbl.RefTableID='2' OR vchrtbl.RefTableID='1')and membertbl.member_id IN (".$memberIDS.")   ";*/

		//$sql = "select wingtbl.wing as wing,wingtbl.wing_id as wing_id,membertbl.owner_name as member,membertbl.member_id as member_id, unittbl.unit_no as unit,sum(Debit)-sum(Credit) as amount, unittbl.unit_id as unit_id from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id JOIN society as societytbl on unittbl.society_id=societytbl.society_id where societytbl.society_id=".$_REQUEST["sid"]." and assettbl.Date <= '".getDBFormatDate($to)."'  and membertbl.member_id IN (".$memberIDS.")   ";

		$sql = "SELECT * FROM(select membertbl.owner_name as member,membertbl.member_id as member_id, unittbl.unit_no as unit,(assettbl.Debit),(assettbl.Credit), assettbl.LedgerID as LedgerID, societytbl.society_id, assettbl.Date, assettbl.VoucherTypeID, unittbl.sort_order,unittbl.unit_id, wingtbl.wing from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN `billdetails` as billdet on vchrtbl.RefNo=billdet.ID JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id JOIN `society` as societytbl on unittbl.society_id=societytbl.society_id where vchrtbl.RefTableID='1'" . $strBillType; 
		
		$sql .= " UNION ALL select membertbl.owner_name as member,membertbl.member_id as member_id, unittbl.unit_no as unit,(assettbl.Debit),(assettbl.Credit), assettbl.LedgerID as LedgerID, societytbl.society_id, assettbl.Date, assettbl.VoucherTypeID, unittbl.sort_order,unittbl.unit_id, wingtbl.wing from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN `chequeentrydetails` as chqdet on (vchrtbl.RefNo=chqdet.ID) JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id JOIN `society` as societytbl on unittbl.society_id=societytbl.society_id where vchrtbl.RefTableID='2'" . $strChequeType ;

		if($BillType ==  0 || $BillType ==  2 )
		{
			$sql .= " UNION ALL select membertbl.owner_name as member,membertbl.member_id as member_id, unittbl.unit_no as unit,(assettbl.Debit),(assettbl.Credit), assettbl.LedgerID as LedgerID, societytbl.society_id, assettbl.Date, assettbl.VoucherTypeID, unittbl.sort_order,unittbl.unit_id, wingtbl.wing from `assetregister` as assettbl JOIN `unit` as unittbl on assettbl.LedgerID=unittbl.unit_id JOIN `member_main` as membertbl on membertbl.unit=unittbl.unit_id JOIN `voucher` as vchrtbl on assettbl.VoucherID=vchrtbl.id JOIN `wing` as wingtbl on unittbl.wing_id=wingtbl.wing_id JOIN `society` as societytbl on unittbl.society_id=societytbl.society_id where assettbl.VoucherTypeID ='2' || assettbl.VoucherTypeID ='5'";
		}

		$sql .= ") A where A.member_id IN (" . $memberIDS . ") and A.society_id='".$_REQUEST["sid"]."' and A.Date <= '".getDBFormatDate($to)."'";

		if($wing <> "")
		{
			$sql .= " and A.wing = '".$wing."'";
		}
		$sql .= " ORDER BY A.sort_order, A.Date";
		
		//echo $sql;
		$res = $this->m_dbConn->select($sql);
		
		//print_r($res);
		$finalArray = array();

		for($iCount = 0; $iCount < sizeof($res); $iCount++)
		{
			$amount = 0;
			$amount = $finalArray[$res[$iCount]['LedgerID']]['amount'] + $res[$iCount]['Debit'] - $res[$iCount]['Credit'];
			
			$finalArray[$res[$iCount]['LedgerID']] = $res[$iCount];
			$finalArray[$res[$iCount]['LedgerID']]['amount'] = $amount;

			//echo '<br>' . ($iCount + 1) . ' ' . $res[$iCount]['LedgerID'] . ' VoucherType : ' . $res[$iCount]['VoucherTypeID'] . ' : Debit : ' . $res[$iCount]['Debit'] . ' Credit : ' . $res[$iCount]['Credit'] . ' Total : ' . $finalArray[$res[$iCount]['LedgerID']]['amount'];
		}

		//print_r($finalArray);

		$res = array();
		foreach($finalArray as $k => $v)
		{
			array_push($res, $v);
		}
		//print_r($res);
		if(sizeof($res) > 0 && ($BillType ==  0 || $BillType ==  2 ))
		{
			$YearDetails =$this->obj_utility->getBeginningAndEndingDate($_SESSION["society_creation_yearid"]);
			//echo $YearDetails['BeginingDate'];
			//echo $YearDetails['EndingDate'];
			for($i = 0;$i <= sizeof($res)-1;$i++)
			{
				 $temp = $this->obj_utility->getOpeningBalance($res[$i]['unit_id'],getDisplayFormatDate($YearDetails['BeginingDate']));
				 //$temp = $this->obj_utility->getOpeningBalance($res[$i]['unit_id'],"1-04-2015");
				 //print_r($temp);
				 if(sizeof($temp) > 0)
				 {
					 //var_dump($temp);
					 if($temp['OpeningType'] == TRANSACTION_CREDIT)
					 {
					 	$res[$i]['amount'] = $res[$i]['amount'] - $temp['Total'] ;
					 }
					 else
					 {
						 $res[$i]['amount'] = $res[$i]['amount'] + $temp['Total'] ;
					 }
				 }
			}
		}
		return $res;
	}
	
	public function get_login_name($login_id)
	{
	 	$sql = "select member_id from `login` where login_id=".$login_id."";
	 	$res = $this->m_dbConn->select($sql);
		return $res[0]['member_id'];
		
	}
	
	
	
}
?>