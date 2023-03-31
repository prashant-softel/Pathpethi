<?php
include_once("include/display_table.class.php");
include_once("dbconst.class.php");
include_once("adduser.class.php");

class client
{
	public $actionPage = "../client.php";
	public $m_dbConnRoot;
	private $obj_register;
	private $obj_changelog;
	private $display_pg;
	public $obj_addduser;
	
	function __construct($dbConnRoot)
	{
		$this->m_dbConnRoot = $dbConnRoot;
		$this->display_pg = new display_table($this->m_dbConnRoot);

		/*//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
		//$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);*/
		$this->obj_addduser = new adduser($this->m_dbConnRoot);

		
		//$this->obj_changelog = new changeLog($this->m_dbConn);
	}

	public function startProcess()
	{
		$errorExists = 0;

		/*//$curdate 		=  $this->curdate;
		//$curdate_show	=  $this->curdate_show;
		//$curdate_time	=  $this->curdate_time;
		//$ip_location	=  $this->ip_location;*/
	}
	
	public function InsertData()
	{
		$sqlInsert = "INSERT INTO `client`(`client_name`, `mobile`, `landline`, `email`, `address`, `details`) VALUES ('" . $this->m_dbConnRoot->escapeString($_REQUEST['client_name']) . "', '" . $this->m_dbConnRoot->escapeString($_REQUEST['mobile']) . "', '" . $this->m_dbConnRoot->escapeString($_REQUEST['landline']) . "', '" . $this->m_dbConnRoot->escapeString($_REQUEST['email']) . "', '" . $this->m_dbConnRoot->escapeString($_REQUEST['address']) . "', '" . $this->m_dbConnRoot->escapeString($_REQUEST['details']) . "')";
		
		$result = $this->m_dbConnRoot->insert($sqlInsert);
		
		return $result;
	}
	
	public function UpdateData($ID, $PaidBy, $PaidTo, $PayerBank, $PayerBranch, $Amount, $Date, $AccNumber, $TransactionNo, $Comments)
	{
		$result = '';
		return $result;
	}
	
	public function combobox($query, $id, $defaultOption = '', $defaultValue = '')
	{
		$str = '';
		
		if($defaultOption)
		{
			$str.="<option value='" . $defaultValue . "'>" . $defaultOption . "</option>";
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
	
	public function display1($rsas)
	{
		$thheader = array('Client Name','Mobile','Landline', 'Address','Details', 'Status', 'View');
		$editFunction		= "getnewclient";
		$this->display_pg->th		= $thheader;
		$mainpg	= "newclient.php";

		for($iCnt = 0; $iCnt < sizeof($rsas); $iCnt++)
		{
			$rsas[$iCnt]['View'] = '<a href="client_details.php?client=' . $rsas[$iCnt]['id'] . '">Details</a>';
		}
		
		$res = $this->display_pg->display_datatable($rsas, false, false);
		return $res;
	}
	
	public function pgnation()
	{
		$sql1 = "select id, client_name, mobile, landline, address, details, status from client";
			
		$result = $this->m_dbConnRoot->select($sql1);
		
		$data=$this->display1($result);
		return $data;
	}
	
	public function selecting($NeftID)
	{
		$sql = "SELECT `ID`, `society_id`, `paid_by`, `paid_to`, `payer_bank`, `payer_branch`, `amount`, `date`, `acc_no`, `transaction_no`,  `approved`, `comments` FROM `neft` where ID='" . $NeftID . "'";
		
		$res = $this->m_dbConn->select($sql);
		
		return $res;
	}
	
	public function deleting()
	{
		$sql = "update bank_master set status='N' where BankID='".$_REQUEST['BankDetailsId']."'";
		$res = $this->m_dbConn->update($sql);
	}
	
	public function getClientDetails($clientID)
	{
		$sql = "Select * from `client` where `id` = '" . $clientID . "'";
		$result = $this->m_dbConnRoot->select($sql);
		return $result;
	}
	
	public function getSocietyList()
	{
		$sql = "Select `society_id`, `society_name` from `society` where `client_id` = '" . $_REQUEST['client'] . "'";
		$result = $this->m_dbConnRoot->select($sql);
		
		$thheader = array('Society Name', 'Super Admin', 'Admin', 'Admin Member', 'Member', 'Details');
		$editFunction		= "client_details";
		$this->display_pg->th		= $thheader;
		$mainpg	= "client_details.php";

		for($iCnt = 0; $iCnt < sizeof($result); $iCnt++)
		{
			$result[$iCnt]['Super Admin'] = 0;
			$result[$iCnt]['Admin'] = 0;
			$result[$iCnt]['Admin Member'] = 0;
			$result[$iCnt]['Member'] = 0;
			
			$sqlCnt = "SELECT role, COUNT(*) AS times FROM `mapping` where society_id = '" . $result[$iCnt]['society_id'] . "' GROUP BY role ";
			$cntResult = $this->m_dbConnRoot->select($sqlCnt);
			
			for($iCntRole = 0; $iCntRole < sizeof($cntResult); $iCntRole++)
			{
				$result[$iCnt][$cntResult[$iCntRole]['role']] =  $cntResult[$iCntRole]['times'];
			}
			
			$result[$iCnt]['View'] = '<a href="#" onclick="fetchUserDetails(' . $result[$iCnt]['society_id'] . ');">View</a>';
		}
		
		$res = $this->display_pg->display_datatable($result, false, false);
				
		return $res;
	}
	
	public function getUserList()
	{
		$thheader = array('ID','Name', 'Login ID', 'Desc', 'Role', 'Status', 'Last Login', 'Details');
		$editFunction		= "client_details";
		$this->display_pg->th		= $thheader;
		$mainpg	= "client_details.php";

		$sql = "Select map.`id`, log.login_id, log.`name`, log.`member_id`, map.`desc`, map.`role`, IF(map.`status` = 2, '<font style=\'color:#009900;\'>Active</font>', IF(map.`status` = 1, '<font style=\'color:#FF0000;\'>Inactive</font>', '<font style=\'color:#0000FF;\'>Disabled</font>')) from `mapping` as map LEFT JOIN `login` as log on map.login_id = log.login_id where map.`society_id` = '" . $_REQUEST['society'] . "' and map.`role` = '" . $_REQUEST['usertype'] . "' ORDER BY FIELD( map.`status`, '2','3','1')";		
		$result = $this->m_dbConnRoot->select($sql);	
		
		$sqlLogin = "SELECT `ID`,`UserID`, MAX(Timestamp) As 'Last_Login', `IP`, `City`, `Region`, `Country` FROM `LoginTrackingLog` GROUP BY `UserId`";
		$res = $this->m_dbConnRoot->select($sqlLogin);
		
		if(!$result)
		{
			return "<font style='color:#0000FF;'>No records to display for User Type [" . $_REQUEST['usertype'] . "]</font>";
		}
		else
		{
			for($iCnt = 0; $iCnt < sizeof($result); $iCnt++)
			{
				$result[$iCnt]['Last Login'] = "-";
				//$result[$iCnt]['IP'] = "-";									
				//$result[$iCnt]['Region'] = "-";
				//$result[$iCnt]['Country'] = "-";
				$result[$iCnt]['View'] = "-";
						
				for($j = 0; $j < sizeof($res); $j++)
				{					
					if($result[$iCnt]['login_id'] == $res[$j]['UserID'])
					{												
						$result[$iCnt]['Last Login'] = $res[$j]['Last_Login'];
						if($res[$j]['Last_Login'] <> "")
						{
							//$result[$iCnt]['IP'] = $res[$j]['IP'];
						}
						if($res[$j]['Region'] <> "" || $res[$j]['City'] <> "")
						{											
							//$result[$iCnt]['Region'] = $res[$j]['Region']. "[" .  $res[$j]['City'] . "]";
						}
						if($res[$j]['Country'] <> "")
						{
							//$result[$iCnt]['Country'] = $res[$j]['Country'];
						}						
						$result[$iCnt]['View'] = '<a href="#" onclick="fetchLoginDetails(' . $res[$j]['UserID'] . ');">View</a>';
						break;	
					}																
				}
				//$result[$iCnt]['Last Login'] = '2015/12/10';
			}
			
			$res = $this->display_pg->display_datatable($result, false, false);
			return $res;
		}
	}
	
	public function getSocietyName()
	{
		$sql = "Select society_name from society where society_id = '" . $_REQUEST['society'] . "'";
		$result = $this->m_dbConnRoot->select($sql);
		return $result[0]['society_name'];
	}
	
	public function getLoginDetails()
	{
		$thheader = array('ID', 'Society', 'Role', 'IP', 'Hostname', 'City', 'Region', 'Country', 'Location', 'ISP', 'Postal Code', 'Timestamp', 'Edit');
		$editFunction		= "client_details";
		$this->display_pg->th		= $thheader;
		$mainpg	= "client_details.php";
		//$sql = "SELECT * FROM `logintrackinglog` WHERE `UserID` = '".$_REQUEST['userID']."'";
		$sql = "SELECT trLog.MappingID,trLog.UserId,s.society_name,mp.role,trLog.IP,trLog.Hostname,trLog.City,trLog.Region,trLog.Country,trLog.Location,trLog.ISP,trLog.Postal_Code,trLog.Timestamp 
				FROM `LoginTrackingLog` AS trLog JOIN `mapping` AS mp ON trLog.MappingID = mp.id JOIN `society` AS s ON mp.society_id = s.society_id WHERE trLog.UserID = '".$_REQUEST['userID']."' ORDER BY `Timestamp` DESC";
		$result = $this->m_dbConnRoot->select($sql);
		for($i = 0; $i < sizeof($result); $i++)
		{
			if($result[$i]['role'] == ROLE_SUPER_ADMIN)
			{
				$result[$i]['edit'] = '-';
			}
			else
			{
				$result[$i]['edit'] = '<a href="updateuser.php?id='.$result[$i]['MappingID'].'"><img src="images/edit.gif" /></a>';
			}
		}
		
		$res = $this->display_pg->display_datatable($result, false, false);
		return $res;
	}
	
	public function getAssignedSocieties()
	{
		$thheader = array('Society', 'Desciption', 'Role', 'Status');
		$editFunction = "client_details";
		$this->display_pg->th = $thheader;
		$mainpg	= "client_details.php";
		$sql = "SELECT mp.id, s.society_name, mp.desc, mp.role, IF(mp.status = 2, '<font style=\'color:#009900;\'>Active</font>', IF(mp.status = 1, '<font style=\'color:#FF0000;\'>Inactive</font>', '<font style=\'color:#0000FF;\'>Disabled</font>'))
			 	FROM `mapping` AS mp JOIN `society` AS s ON mp.society_id = s.society_id WHERE mp.login_id = '".$_REQUEST['LoginID']."'";
		$result = $this->m_dbConnRoot->select($sql);
		$res = $this->display_pg->display_datatable($result, false, false);
		return $res;
	}
	
	public function getSocieties()
	{
		$aryResult = array();
		$sql = 'SELECT * FROM `society` WHERE `society_id` NOT IN ( SELECT DISTINCT s.society_id FROM `society` AS s INNER JOIN `mapping` AS m ON s.society_id = m.society_id WHERE m.login_id = "'.$_REQUEST['LoginID'].'")';	
		$result = $this->m_dbConnRoot->select($sql);
		
		$show_dtl = array("id"=>'0', "society_name"=>'Please Select');
		array_push($aryResult,$show_dtl);
		
		foreach($result as $k => $v)
		{
			$show_dtl = array("id"=>$result[$k]['society_id'], "society_name"=>$result[$k]['society_name']);
			array_push($aryResult,$show_dtl);
		}
		echo json_encode($aryResult);		
	}
	
	public function getUnits()
	{
		$aryResult = array();
		$sql = "select `unit_id`, `desc` from mapping where unit_id != 0 and society_id = '" . $_REQUEST['SocietyID'] . "'";
		$result = $this->m_dbConnRoot->select($sql);
		
		$show_dtl = array("id"=>'0', "unit"=>'Please Select');
		array_push($aryResult,$show_dtl);
		
		foreach($result as $k => $v)
		{
			$show_dtl = array("id"=>$result[$k]['unit_id'], "unit"=>$result[$k]['desc']);
			array_push($aryResult,$show_dtl);
		}
		echo json_encode($aryResult);
	}
	
	public function addUser()
	{
		$code = getRandomUniqueCode();						
		$result = $this->obj_addduser->addUser($_REQUEST['userRole'], $_REQUEST['unitID'], $_REQUEST['societyID'], $code, $_REQUEST['LoginID']);
		if($result > 0)
		{
			$msg = $_REQUEST['userRole'] . ' Added Successfully.<br>Account Access Code : ' . $code;
		}
		return $code;					
	}
}
?>