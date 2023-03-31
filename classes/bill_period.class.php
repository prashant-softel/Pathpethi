<?php
include_once("dbconst.class.php");
include_once("include/display_table.class.php");

class bill_period
{
	
	public $actionPage = "../bill_period.php";
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		/*
		//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
		//$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);
		//dbop::__construct();
		*/	
	}

	public function startProcess()
	{
		$errorExists = 0;

		/*//$curdate 		=  $this->curdate;
		//$curdate_show	=  $this->curdate_show;
		//$curdate_time	=  $this->curdate_time;
		//$ip_location	=  $this->ip_location;*/

		if($_REQUEST['insert']=='Insert' && $errorExists==0)
		{														
			if($_POST['Cycle']<>'' && $_POST['YearID']<>'')
			{									
				$sql = "select count(YearID) as count from `period` where `Billing_cycle`='".$_POST['Cycle']."' and `YearID`= '".$_POST['YearID']."'";
				$res = $this->m_dbConn->select($sql);
				
				if($res[0]['count'] <= 0)
				{ 
					$months = getMonths($_POST['Cycle']);
					$this->setPeriod($months ,$_POST['Cycle'],$_POST['YearID']);																																				
									
					return "Insert";
				}
				else
				{
					return "Data already exist";		
				}												
			}								
			else
			{
				$show="Please select cycle and year";				  
				return $show;
			}
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			$up_query="update period set `Type`='".$_POST['Type']."',`YearID`='".$_POST['YearID']."' where id='".$_POST['id']."'";
			$data = $this->m_dbConn->update($up_query);			
			return "Update";
		}
		else
		{
			return $errString;
		}
	}		
	
	public function setPeriod($months, $billingcycle, $year)
	{		
		$prevPeriod = $this->getPreviousPeriodID($year,$billingcycle);				
		
		for( $i = 0; $i < sizeof($months); $i++)
		{
			$isLast = 0;
			if($i == sizeof($months)-1)
			{
				$isLast = 1;	
			}
			
			$year_desc_query = 'SELECT `YearDescription` FROM `year` where `YearID` =' .$year;
			$desc = $this->m_dbConn->select($year_desc_query); 
			$begin_date = $this->getBeginDate($months[$i],$desc[0]['YearDescription']);
			$end_date = $this->getEndDate($months[$i],$desc[0]['YearDescription']);
			//echo "date";
			//echo $begin_date;
			//echo $end_date;
			$insert_query="insert into period(`Billing_cycle`,`Type`,`YearID`,`PrevPeriodID`,`IsYearEnd`,`BeginingDate`,`EndingDate` ) values(".$billingcycle.",'".$months[$i]."',".$year.",".$prevPeriod.", ".$isLast.",'".$begin_date."','".$end_date."')";
			//echo $insert_query;
			$prevPeriod = $this->m_dbConn->insert($insert_query);										
		}
	}
	
	public function getPreviousPeriodID($year,$billingcycle)
	{
		$prevPeriod = 0;
		$sql = 'SELECT `PrevYearID` FROM `year` where YearID = ' .$year; 
		$prevYear = $this->m_dbConn->select($sql);
		if($prevYear[0]['PrevYearID'] <> 0)
		{
			$sql1 = 'select `ID` from `period` where `YearID` =' .$prevYear[0]['PrevYearID']. '  and Billing_cycle = ' .$billingcycle. ' and `IsYearEnd` = 1';
						//echo $sql1;
 			$Period = $this->m_dbConn->select($sql1);
			
			if($Period <> "")
			{
				$prevPeriod = $Period[0]['ID'];
			}
			//echo $Period[0]['ID'];		
		}
		return $prevPeriod;	
	}
	
	public function getBeginDate($months, $year_desc)
	{		
		//echo "in begin date";					
		$res = explode("-",$months);
		$month = $res[0];
		//echo sizeof($res) && sizeof($res) > 1;	
		$result = explode("-",$year_desc);
		$begin_year = $result[0];
		//echo $res[0];
		if(($month == 'January')  || ($month == 'February') || ($month == 'March'))
		{			
			$begin_year = $result[1];				
		}
		//echo $month.$begin_year;				
		//$begin_date = date('01-m-Y', strtotime(''.$res[0].' 1, '.$begin_year.''));
		$begin_date = date('Y-m-01', strtotime(''.$res[0].' 1, '.$begin_year.''));
		return $begin_date;		
	}
	
	public function getEndDate($months,$year_desc)
	{
		$res = explode("-",$months);
		$result = explode("-",$year_desc);
		$month = $res[sizeof($res) - 1];
		$end_year = $result[0];
		//echo $res[0];
		if(($month == 'January')  || ($month == 'February') || ($month == 'March'))
		{
			$end_year = $result[1];				
		}

		//$end_date = date('t-m-Y', strtotime(''.$month.' 1, '.$end_year.''));
		$end_date = date('Y-m-t', strtotime(''.$month.' 1, '.$end_year.''));			
		return $end_date;
	}
	
	public function combobox($query, $id)
	{		
		$str.="<option value=''>Please Select</option>";
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($v==$id)
						{
							$sel = "selected";
						}
						else
						{
							$sel = "";	
						}
						$str.="<OPTION VALUE=".$v." ".$sel.">";
					}
					else
					{
						$str.=$v."</OPTION>";
					}
					$i++;
				}
			}
		}
			return $str;
			
	}
	
	public function display1($rsas)
	{				
		for($i = 0; $i < sizeof($rsas); $i++)
		{
			$sql = 'SELECT `PrevPeriodID` FROM `period` where `ID` = '. $rsas[$i]['ID'];			
			$res = $this->m_dbConn->select($sql);
			$periodquery = 'SELECT CONCAT(periodTable.Type,"/",yeartable.YearDescription) as "PrevPeriod" FROM `period` as `periodTable`,`period` as `periodTable1`, `year` as `yeartable` where periodTable.ID = periodTable1.PrevPeriodID and periodTable.YearID = yeartable.YearID and periodTable.ID = '. $res[0]['PrevPeriodID'];
			$prevPeriod = $this->m_dbConn->select($periodquery);
			
			if($prevPeriod <> "")
			{			
				$rsas[$i]['PrevPeriod'] = $prevPeriod[0]['PrevPeriod'];
			}
			else
			{
				$rsas[$i]['PrevPeriod'] = "--";
			}
		}
		
		$thheader = array('Billing_cycle','Type','YearID','IsYearEnd','PrevPeriod');
		$this->display_pg->edit		= "getbill_period";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "bill_period.php";

		$res = $this->display_pg->display_new($rsas, false);
		return $res;
	}
	public function pgnation()
	{		
		//$sql1 = "select periodtable.id, periodtable.Type, yeartable.YearDescription from period as periodtable JOIN year AS yeartable on periodtable.YearID = yeartable.YearID  where periodtable.status = 'Y'";
		$sql1 = "select periodtable.ID,cycletable.Description, periodtable.Type, yeartable.YearDescription,periodtable.IsYearEnd from period as periodtable, year AS yeartable,billing_cycle_master as cycletable where periodtable.YearID = yeartable.YearID and cycletable.ID = periodtable.Billing_cycle ";
		$cntr = "select count(status) as cnt from period where status='Y'";

		/*$this->display_pg->sql1		= $sql1;
		$this->display_pg->cntr1	= $cntr;
		$this->display_pg->mainpg	= "bill_period.php";

		$limit	= "50";
		$page	= $_REQUEST['page'];
		$extra	= "";

		$res	= $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;*/
		$rsas = $this->m_dbConn->select($sql1);
		for($i = 0; $i < sizeof($rsas); $i++)
		{
			$sql = 'SELECT `PrevPeriodID` FROM `period` where `ID` = '. $rsas[$i]['ID'];			
			$res = $this->m_dbConn->select($sql);
			$periodquery = 'SELECT CONCAT(periodTable.Type,"/",yeartable.YearDescription) as "PrevPeriod" FROM `period` as `periodTable`,`period` as `periodTable1`, `year` as `yeartable` where periodTable.ID = periodTable1.PrevPeriodID and periodTable.YearID = yeartable.YearID and periodTable.ID = '. $res[0]['PrevPeriodID'];
			$prevPeriod = $this->m_dbConn->select($periodquery);
			
			if($prevPeriod <> "")
			{			
				$rsas[$i]['PrevPeriod'] = $prevPeriod[0]['PrevPeriod'];
			}
			else
			{
				$rsas[$i]['PrevPeriod'] = "--";
			}
		}
		
		$thheader = array('Billing_cycle','Type','YearID','IsYearEnd','PrevPeriod');
		$this->display_pg->edit		= "getbill_period";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "bill_period.php";
		$res = $this->display_pg->display_datatable($rsas);

	}
	public function selecting()
	{
		$sql = "select id,`Billing_cycle`,`Type`,`YearID` from period where id='".$_REQUEST['bill_periodId']."'";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	public function deleting()
	{
		$sql = "update period set status='N' where id='".$_REQUEST['bill_periodId']."'";
		$res = $this->m_dbConn->update($sql);
	}
	
	public function getPeriodStartAndEndDate($periodID)
	{
		$sql = "select BeginingDate, EndingDate	from period where ID = '" . $periodID . "'";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	public function get_period($cycleID = 0)
	{
		/*if($cycleID==0)
		{
			$sql = "select * from period as periodtbl JOIN society as societytbl ON periodtbl.Billing_cycle = societytbl.bill_cycle where periodtbl.status='Y' and periodtbl.YearID='" . $_REQUEST['year'] . "' and societytbl.society_id='" . $_SESSION['society_id'] . "'";
		}
		else
		{
			$sql = "select * from period where Billing_cycle =".$_REQUEST['cycleID']." and status='Y' and YearID='" . $_REQUEST['year'] . "'";	
		}*/
		
		$sql = "select * from period where YearID = '" . $_REQUEST['year'] . "' ";
		$res = $this->m_dbConn->select($sql);	
		
		if($res<>"")
		{
			$aryResult = array();
			array_push($aryResult,array('success'=>'0'));
			foreach($res as $k => $v)
			{
				$sqlCnt = "Select count(ID) as cnt from billregister where PeriodID = '" . $res[$k]['ID'] . "' and SocietyID = '" . $_SESSION['society_id'] . "'";
				$result = $this->m_dbConn->select($sqlCnt);
				
				if($result[0]['cnt'] > 0)
				{
					$show_dtl = array("id"=>$res[$k]['ID'], "period"=>('<font color="#FF0000">' . $res[$k]['Type'] . '**</font>'));
				}
				else
				{	
			 		$show_dtl = array("id"=>$res[$k]['ID'], "period"=>$res[$k]['Type']);
				}
				array_push($aryResult,$show_dtl);
			}
			echo json_encode($aryResult);
		}
		else
		{
			echo json_encode(array(array("success"=>1), array("message"=>'No Data To Display')));
		}
	}
}
?>