<?php
include_once("include/display_table.class.php");
include_once("bill_period.class.php");
include_once("changelog.class.php");

class bill_year
{
	public $actionPage = "../bill_year.php";
	public $m_dbConn;
	public $m_bill_period;
	public $m_objLog;
	public $sEntryTracker;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);

		/*//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
		//$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);*/
		$this->m_bill_period = new bill_period($dbConn);
		$this->m_objLog = new changeLog($this->m_dbConn);
		//dbop::__construct();
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
			if($_POST['YearDescription'] <> '')
			{
				$begin_date = $this->m_bill_period->getBeginDate('April-May-June-July-August-September-October-November-December-January-February-March',$_POST['YearDescription']);
				$end_date = $this->m_bill_period->getEndDate('April-May-June-July-August-September-October-November-December-January-February-March',$_POST['YearDescription']);
				$insert_query="insert into year (`YearDescription`,`PrevYearID`,`BeginingDate`,`EndingDate`,`is_year_freeze`) values ('".$_POST['YearDescription']."','".$_POST['PrevYearID']."','".$begin_date."','".$end_date."','".$_POST['freeze_year']."' )";
				$data = $this->m_dbConn->insert($insert_query);
				
				if($_POST['freeze_year'] <> $_POST['prev_year_status'])
				{
					if($_POST['freeze_year'] == 1)
					{
						$this->sEntryTracker = "year <".$_POST['YearDescription']."> locked";
					}
					else
					{
						$this->sEntryTracker = "year <".$_POST['YearDescription']."> unlocked";
					}
					$this->m_objLog->setLog($this->sEntryTracker, $_SESSION['login_id'], 'year', $data);
				}
			
				return "Insert";
			}
			else
			{
				return "Please enter Year Description";
			}
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			$begin_date = $this->m_bill_period->getBeginDate('April-May-June-July-August-September-October-November-December-January-February-March',$_POST['YearDescription']);
			$end_date = $this->m_bill_period->getEndDate('April-May-June-July-August-September-October-November-December-January-February-March',$_POST['YearDescription']);
			$up_query="update year set `YearDescription`='".$_POST['YearDescription']."',`PrevYearID`='".$_POST['PrevYearID']."',`BeginingDate`='".$begin_date."',`EndingDate`='".$end_date."' ,`is_year_freeze` ='".$_POST['freeze_year']."'  where YearID='".$_POST['id']."'";
			$data = $this->m_dbConn->update($up_query);
			
			if($_POST['freeze_year'] <> $_POST['prev_year_status'])
			{
				if($_POST['freeze_year'] == 1)
				{
					$this->sEntryTracker = "year <".$_POST['YearDescription']."> locked";
				}
				else
				{
					$this->sEntryTracker = "year <".$_POST['YearDescription']."> unlocked";
				}
				$this->m_objLog->setLog($this->sEntryTracker, $_SESSION['login_id'], 'year', $_POST['id']);
			}
			
			
			
			if($_POST['freeze_year'] == 1 && $_POST['id'] == $_SESSION['default_year'])
			{		
				$_SESSION['is_year_freeze'] = 1;
			}
			else
			{
					$_SESSION['is_year_freeze'] = 0;
			}
			return "Update";
		}
		else
		{
			return $errString;
		}
	}
	//public function combobox($query)
	//{
	//}
	public function combobox($query, $id)
	{
		//$str.="<option value=''>Please Select</option>";
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
						if($id==$v)
						{
							$sel = 'selected';	
						}
						else
						{
							$sel = '';
						}
						
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
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
		$thheader = array('YearDescription','PrevYearDescription','FreezeYear');
		$this->display_pg->edit		= "getbill_year";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "bill_year.php";

		$res = $this->display_pg->display_new($rsas);
		return $res;
	}
	public function pgnation()
	{
		$sql1 = "select yeartable.YearID, yeartable.YearDescription, prevyeartable.YearDescription as `PrevYearDesc`,IF(yeartable.is_year_freeze = 1,'<i class=\'fa  fa-check\'  style=\'font-size:10px;font-size:1.75vw;color:#6698FF;\'></i>','No') as is_year_freeze from `year` as `yeartable` left join `year` as `prevyeartable` on yeartable.PrevYearID = prevyeartable.YearID where yeartable.status='Y' ORDER BY yeartable.YearID DESC ";
		$cntr = "select count(status) as cnt from year where status='Y'";

		/*$this->display_pg->sql1		= $sql1;
		$this->display_pg->cntr1	= $cntr;
		$this->display_pg->mainpg	= "bill_year.php";

		$limit	= "50";
		$page	= $_REQUEST['page'];
		$extra	= "";

		$res	= $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;*/
		
		$result = $this->m_dbConn->select($sql1);
		$thheader = array('YearDescription','PrevYearDescription','FreezeYear');
		$this->display_pg->edit		= "getbill_year";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "bill_year.php";

		$res = $this->display_pg->display_datatable($result);
	}
	
	public function selecting()
	{
		$sql = "select YearID,`YearDescription`,`PrevYearID`,`is_year_freeze` from year where YearID='".$_REQUEST['bill_yearId']."'";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	public function deleting()
	{
		$sql = "update year set status='N' where YearID='".$_REQUEST['bill_yearId']."'";
		$res = $this->m_dbConn->update($sql);
	}
}
?>