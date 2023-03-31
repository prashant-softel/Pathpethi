<?php

include_once("include/display_table.class.php");
include_once("dbconst.class.php");
include_once("include/dbop.class.php");

class depositgroup extends dbop
{
	public $actionPage = "../ChequeDetails.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = new dbop(true);
		$this->display_pg=new display_table($this->m_dbConn);

		/*//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
		//$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);*/

		//dbop::__construct();
	}

	public function startProcess()
	{
		$errorExists = 0;
		$errString = "";

		/*//$curdate 		=  $this->curdate;
		//$curdate_show	=  $this->curdate_show;
		//$curdate_time	=  $this->curdate_time;
		//$ip_location	=  $this->ip_location;
		*/
		

		if($_REQUEST['insert']=='Create' && $errorExists==0)
		{
			//echo "inside";
			$insert_query="insert into depositgroup (`bankid`,`createby`,`depositedby`,`status`,`desc`,`DepositSlipCreatedYearID`) values ('".$_POST['bankid']."','".$_SESSION['login_id']."','".$_POST['depositedby']."','".$_POST['status']."','".$_POST['desc']."','".$_SESSION['default_year']."')";
			//echo $insert_query;
			$data = $this->m_dbConn->insert($insert_query);
			$this->actionPage = "../ChequeDetails.php?&depositid=".$data."&bankid=".$_POST['bankid'];
			//echo $this->actionPage;
			return "Insert";
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			$up_query="update depositgroup set `bankid`='".$_POST['bankid']."',`depositedby`='".$_POST['depositedby']."',`status`='".$_POST['status']."',`desc`='".$_POST['desc']."' where id='".$_POST['id']."'";
			$data = $this->m_dbConn->update($up_query);
			$this->actionPage = "../depositgroup.php?&bankid=".$_POST['bankid'];
			return "Update";
		}
		else
		{
			$errString ="error";
			return $errString;
		}
	}
	public function combobox($query)
	{
		$id=0;
		//echo "<script>alert($query)<//script>";
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
		$thheader = array('Deposit slip description', 'Number of cheques', 'Deposited Amount', 'Created By','Deposited By','Status');
		$this->display_pg->edit		= "getdepositgroup";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "depositgroup.php";

		//$res = $this->display_pg->display_new($rsas);
		$res = $this->display_pg->display_datatable($rsas, true, false);
		return $res;
	}
	
	public function combobox1($query, $id, $bShowAll = false)
	{
		if($bShowAll == true)
		{
			$str.="<option value=''>All</option>";
		}
		else
		{
			$str.="<option value=''>Please Select</option>";
		}
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
	
	public function comboboxEx($query)
	{
		$id=0;
		//echo "<script>alert('test')<//script>";
		$str.="<option value=''>Please Select</option>";
	$data = $this->m_dbConn->select($query);
	//echo "<script>alert('test2')<//script>";
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
					//echo "<script>alert('".$str."')<//script>";
					$i++;
				}
			}
		}
		//return $str;
		//print_r( $str);
		//echo "<script>alert('test')<//script>";
		return $str;
	}
	public function pgnation()
	{
		//for displaying records at bottom of screen, just before footer
		
		$slipNumbers = $this->getSlipNumbers();
		//if($_REQUEST['depositgroupId'] == "")
		//{
			$sql1 = "select id,`desc` ,`createby`,`depositedby`, if(`status` = 0, 'Open', 'Close') as status from depositgroup where bankid='".$_REQUEST['bankid']."'  and  (id IN(".$slipNumbers.") OR `DepositSlipCreatedYearID` =  '".$_SESSION['default_year']."') ";

$sql1 = "select d.id, d.desc, COUNT(c.DepositID), SUM(c.AMOUNT), d.createby, d.depositedby, if(d.status = 0, 'Open', 'Close') as status from depositgroup as d LEFT JOIN chequeentrydetails as c ON d.id= c.depositid where d.bankid= '".$_REQUEST['bankid']."' and  (d.id IN(".$slipNumbers.") OR d.`DepositSlipCreatedYearID` =  '".$_SESSION['default_year']."') GROUP BY d.id";

		//}
		//else
		//{
			//$sql1 = "select id,`bankid`,`createby`,`depositedby`,`status`,`desc` from depositgroup where id='".$_REQUEST['depositgroupId']."'";
		//}
		/*$cntr = "select count(status) as cnt from depositgroup where status='Y'";

		$this->display_pg->sql1		= $sql1;
		$this->display_pg->cntr1	= $cntr;
		$this->display_pg->mainpg	= "depositgroup.php";

		$limit	= "50";
		$page	= $_REQUEST['page'];
		$extra	= "";

		$res	= $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;*/
		$result = $this->m_dbConn->select($sql1);
		$arMermberIdNandName = array();
		for($iCnt = 0; $iCnt < sizeof($result) ; $iCnt++)
		{
			$CreateBy = $result[$iCnt]['createby'];
			
			if (!array_key_exists($CreateBy, $arMermberIdNandName)) 
			{
				$sqlMemberLogin = "select `name` from login where `login_id` = '".$CreateBy."'";
				$MemberNames =$this->m_dbConnRoot->select($sqlMemberLogin);
				
				$arMermberIdNandName[$CreateBy]  = $MemberNames[0]['name'];
			}

			$result[$iCnt]['createby']  =  $arMermberIdNandName[$CreateBy];

		}
		$this->display1($result);
	}
	public function selecting()
	{
		//echo "depd";
		//echo $_REQUEST['depositgroupId'];
		$sql = "select id,`bankid`,`createby`,`depositedby`,`status`,`desc` from depositgroup where id='".$_REQUEST['depositgroupId']."'";
		
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	public function deleting()
	{
		$sql = "update depositgroup set status='N' where id='".$_REQUEST['depositgroupId']."'";
		$res = $this->m_dbConn->update($sql);
	}
	
	public function getSlipNumbers()
	{
			$slipNumberArray = array();
			$sql = "SELECT DISTINCT DepositID FROM `chequeentrydetails` where `VoucherDate` between '".$_SESSION['default_year_start_date']."' and  '".$_SESSION['default_year_end_date']."' and  DepositID > 0 and  BankID='".$_REQUEST['bankid']."' ";
			$res = $this->m_dbConn->select($sql);
			array_push($slipNumberArray,'0');
			if(sizeof($res) > 0)
			{
				for($i = 0;$i < sizeof($res); $i++)
				{
					array_push($slipNumberArray,$res[$i]['DepositID']);	
				}
			}
			$slipNumbers = implode(',', $slipNumberArray);
			return $slipNumbers;
	}
}
?>