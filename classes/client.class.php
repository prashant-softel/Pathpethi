<?php
include_once("include/display_table.class.php");
include_once("dbconst.class.php");
include_once("adduser.class.php");
include_once("changelog.class.php");

class client
{
	public $actionPage = "../client.php";
	public $m_dbConnRoot;
	private $obj_register;
	private $obj_changelog;
	private $display_pg;
	public $obj_addduser;
	public $m_clientID;
	
	function __construct($dbConnRoot, $CID = '')
	{
		$this->m_dbConnRoot = $dbConnRoot;
		$this->display_pg = new display_table($this->m_dbConnRoot);
		$this->m_objLog = new changeLog($dbConnRoot);
		/*//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
		//$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);*/
		$this->obj_addduser = new adduser($this->m_dbConnRoot);
		$this->m_clientID = $CID;				
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
		$sqlInsert = "INSERT INTO `client`(`client_name`, `mobile`, `landline`, `email`, `address`,`email_header`,`email_footer`,`sms_userid`,`sms_key`,`details`, `bill_footer`,`sms_domain`,`sms_senderid`) VALUES ('" . $this->m_dbConnRoot->escapeString($_REQUEST['client_name']) . "', '" . $this->m_dbConnRoot->escapeString($_REQUEST['mobile']) . "', '" . $this->m_dbConnRoot->escapeString($_REQUEST['landline']) . "', '" . $this->m_dbConnRoot->escapeString($_REQUEST['email']) . "', '" . $this->m_dbConnRoot->escapeString($_REQUEST['address']) . "', '" . $this->m_dbConnRoot->escapeString($_REQUEST['email_header']) . "', '" . $this->m_dbConnRoot->escapeString($_REQUEST['email_footer']) . "','" . $this->m_dbConnRoot->escapeString($_REQUEST['sms_userid']) . "','" . $this->m_dbConnRoot->escapeString($_REQUEST['sms_key']) . "','" . $this->m_dbConnRoot->escapeString($_REQUEST['details']) . "','" . $this->m_dbConnRoot->escapeString($_REQUEST['bill_footer']) . "','" . $this->m_dbConnRoot->escapeString($_REQUEST['sms_domain']) . "','" . $this->m_dbConnRoot->escapeString($_REQUEST['sms_senderid']) . "')";
		
		$result = $this->m_dbConnRoot->insert($sqlInsert);
		$insertlog = "client_name|mobile|landline|email|address|email_header|email_footer|sms_userid|sms_key|sms_domain|sms_sender_id|details|bill_footer<br/>";
		$insertlog .= "<".$_REQUEST['client_name'] . "'|'" . $this->m_dbConnRoot->escapeString($_REQUEST['mobile']) . "'|'" . $this->m_dbConnRoot->escapeString($_REQUEST['landline']) . "'|'" . $this->m_dbConnRoot->escapeString($_REQUEST['email']) . "'|'" . $this->m_dbConnRoot->escapeString($_REQUEST['address']) . "'|'" . $this->m_dbConnRoot->escapeString($_REQUEST['email_header']) . "'|'" . $this->m_dbConnRoot->escapeString($_REQUEST['email_footer']) . "'|'" . $this->m_dbConnRoot->escapeString($_REQUEST['sms_userid']) . "'|'".$this->m_dbConnRoot->escapeString($_REQUEST['sms_key'])."'|'" .$this->m_dbConnRoot->escapeString($_REQUEST['sms_domain'])."'|'". $this->m_dbConnRoot->escapeString($_REQUEST['sms_senderid']) ."'|'".$this->m_dbConnRoot->escapeString($_REQUEST['details']). "'|'" . $this->m_dbConnRoot->escapeString($_REQUEST['bill_footer']);
		
		$this->m_objLog->setLog($insertlog ,$_SESSION['login_id'], 'client', $result);
		return $result;
	}
	
	public function UpdateData()
	{
		$selectQuery = $this->m_dbConnRoot->select("select * from `client` where `id`='".$_POST['id']."'");
		$sqlUpdate = "UPDATE `client` SET `client_name`='" . $this->m_dbConnRoot->escapeString($_REQUEST['client_name']) . "',`mobile`='" . $this->m_dbConnRoot->escapeString($_REQUEST['mobile']) . "',`landline`='" . $this->m_dbConnRoot->escapeString($_REQUEST['landline']) . "',`email`='" . $this->m_dbConnRoot->escapeString($_REQUEST['email']) . "',`address`='" . $this->m_dbConnRoot->escapeString($_REQUEST['address']) . "',`email_header`='" . $this->m_dbConnRoot->escapeString($_REQUEST['email_header']) . "',`email_footer`='" . $this->m_dbConnRoot->escapeString($_REQUEST['email_footer']) . "',`sms_userid`='" . $this->m_dbConnRoot->escapeString($_REQUEST['sms_userid']) . "',`sms_key`='" . $this->m_dbConnRoot->escapeString($_REQUEST['sms_key']) . "',`sms_domain`='" . $this->m_dbConnRoot->escapeString($_REQUEST['sms_domain']) . "',`sms_senderid`='" . $this->m_dbConnRoot->escapeString($_REQUEST['sms_senderid']) . "',`details`='" . $this->m_dbConnRoot->escapeString($_REQUEST['details']) . "',`bill_footer`='" . $this->m_dbConnRoot->escapeString($_REQUEST['bill_footer']) . "' WHERE `id` = '".$_POST['id']."'";
		$result = $this->m_dbConnRoot->update($sqlUpdate);
		
		$updatelog = "id|client_name|mobile|landline|email|address|email_header|email_footer|sms_userid|sms_key|sms_domain|sms_senderid|details|bill_footer<br/>";
		$updatelog .= $_POST['id']."|".$selectQuery[0]['client_name'] . "'|'" . $this->m_dbConnRoot->escapeString($selectQuery[0]['mobile']) . "'|'" . $this->m_dbConnRoot->escapeString($selectQuery[0]['landline']) . "'|'" . $this->m_dbConnRoot->escapeString($selectQuery[0]['email']) . "'|'" . $this->m_dbConnRoot->escapeString($selectQuery[0]['address']) . "'|'" . $this->m_dbConnRoot->escapeString($selectQuery[0]['email_header']) . "'|'" . $this->m_dbConnRoot->escapeString($selectQuery[0]['email_footer']) . "'|'" . $this->m_dbConnRoot->escapeString($_REQUEST['sms_userid']) . "'|'".$this->m_dbConnRoot->escapeString($_REQUEST['sms_key'])."'|'" .$this->m_dbConnRoot->escapeString($_REQUEST['sms_domain'])."'|'". $this->m_dbConnRoot->escapeString($_REQUEST['sms_senderid']) ."'|'". $this->m_dbConnRoot->escapeString($selectQuery[0]['details']). "'|'" . $this->m_dbConnRoot->escapeString($selectQuery[0]['bill_footer']);
		
		$this->m_objLog->setLog($updatelog ,$_SESSION['login_id'], 'client', $_POST['id']);
		
		return 1;
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
		$thheader = array('Client Name','Mobile','Landline', 'Address', 'email_header', 'email_footer','sms_userid','sms_key','sms_limit','sms_counter','sms_domain','sms_senderid','Details','bill_footer', 'Status', 'View');
		$editFunction		= "getnewclient";
		$this->display_pg->edit		= "getClient";
		$this->display_pg->th		= $thheader;
		$mainpg	= "newclient.php";

		for($iCnt = 0; $iCnt < sizeof($rsas); $iCnt++)
		{
			$clientID = base64_encode($rsas[$iCnt]['id']);
			$rsas[$iCnt]['View'] = '<a href="client_details.php?client=' . $clientID . '">Details</a>';
		}
		
		$res = $this->display_pg->display_datatable($rsas, true, false);
		return $res;
	}
	
	public function pgnation()
	{
		if($_SESSION['role'] == ROLE_MASTER_ADMIN)
		{
			$sql1 = "select id, client_name, mobile, landline, address, email_header, email_footer,sms_userid,sms_key,sms_limit,sms_counter,sms_domain,sms_senderid,details,bill_footer, status from client";
		}
		else
		{
			$sql1 = "select id, client_name, mobile, landline, address, email_header, email_footer,sms_userid,sms_key,sms_limit,sms_counter, sms_domain,sms_senderid,details,bill_footer, status from client WHERE `id` = '" . $_SESSION['society_client_id'] . "'";			
		}
		
		$result = $this->m_dbConnRoot->select($sql1);		
		$data=$this->display1($result);
		return $data;
	}
	
	public function selecting()
	{
		$sql = "SELECT `id`, `client_name`, `mobile`, `landline`, `email`, `address`, `email_header`, `email_footer`,`sms_userid`,`sms_key`,`sms_domain`,`sms_senderid`,`details`,`bill_footer` FROM `client` WHERE `id` = '" . $_REQUEST['cID'] . "'";		
		$res = $this->m_dbConnRoot->select($sql);		
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
		$sql = "Select `society_id`, `society_name` from `society` where `client_id` = '" . $_REQUEST['client'] . "' AND `status` = 'Y'";
		$result = $this->m_dbConnRoot->select($sql);
		
		$totalMember = "SELECT count(*) AS 'totalMember' FROM `mapping` JOIN `society` ON society.society_id = mapping.society_id where society.client_id = '" . $_REQUEST['client'] . "' AND society.`status` = 'Y' AND mapping.`role` = 'Member'";
		$t1 = $this->m_dbConnRoot->select($totalMember);
		
		$totalActiveMember = "SELECT count(*) AS 'totalActive' FROM `mapping` JOIN `society` ON society.society_id = mapping.society_id where society.client_id = '" . $_REQUEST['client'] . "' AND society.`status` = 'Y' AND mapping.`role` = 'Member' AND mapping.status = 2";
		$t2 = $this->m_dbConnRoot->select($totalActiveMember);		
		$thheader = array('Society Name', 'Super Admin', 'Admin', 'Admin Member', "Member[<font style='color:#000FFF;'>".$t1[0]['totalMember']."</font>]", "Registered Member[<font style='color:#009900;'>".$t2[0]['totalActive']."</font>]", 'Active Member<br />[30days]', 'Active Member<br />[60days]', 'Details');
		$editFunction		= "client_details";
		$this->display_pg->th		= $thheader;
		$encrClientID = base64_encode($_REQUEST['client']);
		$mainpg	= "client_details.php?client=" . $encrClientID ;

		for($iCnt = 0; $iCnt < sizeof($result); $iCnt++)
		{
			$result[$iCnt]['Super Admin'] = 0;
			$result[$iCnt]['Admin'] = 0;
			$result[$iCnt]['Admin Member'] = 0;
			$result[$iCnt]['Member'] = 0;
			
			$sqlCnt = "SELECT role, COUNT(*) AS times FROM `mapping` where society_id = '" . $result[$iCnt]['society_id'] . "' GROUP BY role ";
			$cntResult = $this->m_dbConnRoot->select($sqlCnt);			
			
			$activeIn30days = $this->fetchActiveMembers(30, $result[$iCnt]['society_id']);	
			$activeIn60days = $this->fetchActiveMembers(60, $result[$iCnt]['society_id']);
			
			$sqlActiveMember = "SELECT count(*) AS 'Total' FROM `mapping` where society_id = '" . $result[$iCnt]['society_id'] . "' AND `role` = 'Member' AND `status` = 2";
			$totalCount = $this->m_dbConnRoot->select($sqlActiveMember);
			for($iCntRole = 0; $iCntRole < sizeof($cntResult); $iCntRole++)
			{				
				$result[$iCnt][$cntResult[$iCntRole]['role']] =  $cntResult[$iCntRole]['times'];				
			}
			$result[$iCnt]['ActiveMember'] = $totalCount[0]['Total'];
			$result[$iCnt]['ActiveIn30Days'] = $activeIn30days;
			$result[$iCnt]['ActiveIn60Days'] = $activeIn60days - $activeIn30days;
			$result[$iCnt]['View'] = '<a href="#" onclick="fetchUserDetails(' . $result[$iCnt]['society_id'] . ');">View</a>';
		}		
		$res = $this->display_pg->display_datatable($result, false, false);
				
		return $res;
	}
	
	public function getUserList()
	{
		$thheader = array('Login Name', 'Login ID', 'Desc', 'Role', 'Status', 'Last Login', 'Details');
		$editFunction		= "client_details";
		$this->display_pg->th		= $thheader;
		$encryptedCID = base64_encode($this->m_clientID);
		$mainpg	= "client_details.php?client=".$encryptedCID;

		if($_REQUEST['usertype'] == 'Member')
		{		 
		 	$sql = "Select map.`id`, log.login_id, log.`name`, log.`member_id`, map.`desc`, map.`role`, map.`status`, IF(map.`status` = 2, '<font style=\'color:#009900;\'>Active</font>', IF(map.`status` = 1, '<font style=\'color:#FF0000;\'>Inactive</font>', '<font style=\'color:#0000FF;\'>Disabled</font>')) from `mapping` as map LEFT JOIN `login` as log on map.login_id = log.login_id where map.`society_id` = '" . $_REQUEST['society'] . "' and (map.`role` = '" . $_REQUEST['usertype'] . "' OR map.`role` = 'Admin Member') ORDER BY FIELD( map.`status`, '2','3','1')";
		}
		else
		{
			$sql = "Select map.`id`, log.login_id, log.`name`, log.`member_id`, map.`desc`, map.`role`, map.`status`, IF(map.`status` = 2, '<font style=\'color:#009900;\'>Active</font>', IF(map.`status` = 1, '<font style=\'color:#FF0000;\'>Inactive</font>', '<font style=\'color:#0000FF;\'>Disabled</font>')) from `mapping` as map LEFT JOIN `login` as log on map.login_id = log.login_id where map.`society_id` = '" . $_REQUEST['society'] . "' and map.`role` = '" . $_REQUEST['usertype'] . "' ORDER BY FIELD( map.`status`, '2','3','1')";
		}
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
				
				if($result[$iCnt]['status'] == 1)
				{					 					
					$result[$iCnt]['View'] = '<a onclick="myFunction(' . $this->m_clientID . ', '.$result[$iCnt]['id'].')"  title="Invite user through email.">Invite User</a>'; ?>					
                <?php
				}
				else
				{
					$result[$iCnt]['View'] = '<a href="#" onclick="fetchLoginDetails(' . $result[$iCnt]['login_id'] . ');">View</a>';						
				}
				unset($result[$iCnt]['status']);
						
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
						break;	
					}																					
				}
				unset($result[$iCnt]['login_id']);
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
		$thheader = array('Society', 'Role', 'Desc', 'IP', 'Address', 'Location', 'Postal Code', 'Timestamp', 'Edit');
		$editFunction		= "client_details";
		$this->display_pg->th		= $thheader;
		$encryptedCID = base64_encode($this->m_clientID);
		$mainpg	= "client_details.php?client=".$encryptedCID;
		//$sql = "SELECT * FROM `logintrackinglog` WHERE `UserID` = '".$_REQUEST['userID']."'";
		$sql = "SELECT trLog.MappingID,trLog.UserId,s.society_name,mp.role,mp.desc,trLog.IP,CONCAT(trLog.City,' ',trLog.Region,' ',trLog.Country),trLog.Location,trLog.Postal_Code,trLog.Timestamp 
				FROM `LoginTrackingLog` AS trLog JOIN `mapping` AS mp ON trLog.MappingID = mp.id JOIN `society` AS s ON mp.society_id = s.society_id WHERE trLog.UserID = '".$_REQUEST['userID']."' ORDER BY `Timestamp` DESC";								
		$result = $this->m_dbConnRoot->select($sql);
		if(!$result)
		{
			return "<font style='color:#0000FF;'>No records to display. </font>";
		}
		for($i = 0; $i < sizeof($result); $i++) 
		{			
			if($result[$i]['role'] == ROLE_SUPER_ADMIN)
			{
				$result[$i]['edit'] = '-';
			}
			else
			{
				$encryptedCID = base64_encode($this->m_clientID);
				$result[$i]['edit'] = '<a href="updateuser.php?id='.$result[$i]['MappingID'].'&cltID='.$encryptedCID.'"><img src="images/edit.gif" /></a>';
			}
			unset($result[$i]['MappingID']);
		}
				
		$res = $this->display_pg->display_datatable($result, false, false);
		$sql1 = 'SELECT `name` FROM `login` WHERE `login_id` = "'.$_REQUEST['userID'].'"';
		$name = $this->m_dbConnRoot->select($sql1);	
		$res .= '<input type="hidden" id="loginName" value="'.$name[0]['name'].'" />';
		return $res;
	}
	
	public function getAssignedSocieties()
	{
		$thheader = array('Society', 'Desciption', 'Role', 'Status');
		$editFunction = "client_details";
		$this->display_pg->th = $thheader;
		$encryptedCID = base64_encode($this->m_clientID);
		$mainpg	= "client_details.php?client=".$encryptedCID;
		$sql = "SELECT mp.id, s.society_name, mp.desc, mp.role, IF(mp.status = 2, '<font style=\'color:#009900;\'>Active</font>', IF(mp.status = 1, '<font style=\'color:#FF0000;\'>Inactive</font>', '<font style=\'color:#0000FF;\'>Disabled</font>'))
			 	FROM `mapping` AS mp JOIN `society` AS s ON mp.society_id = s.society_id WHERE mp.login_id = '".$_REQUEST['LoginID']."'";				
		$result = $this->m_dbConnRoot->select($sql);
		$res = $this->display_pg->display_datatable($result, false, false);
		$res.= '<input type="hidden" id="encryptedLoginID" value="'.base64_encode($_REQUEST['LoginID']).'" />';
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
	
	public function fetchActiveMembers($days, $societyID)
	{		
		date_default_timezone_set('Asia/Kolkata');	
		$daysBefore = time() - ($days * 24 * 60 * 60);
		$current_DateTime = date('Y-m-d h:i:s');
		$time = date('Y-m-d h:i:s', $daysBefore); 		
		
		$sqlActive = "SELECT MAX(LoginTrackingLog.Timestamp) FROM `mapping` JOIN `LoginTrackingLog` ON mapping.id = LoginTrackingLog.MappingID where mapping.society_id = '" . $societyID . "'  AND mapping.role = 'MEMBER' GROUP BY mapping.`unit_id` HAVING MAX(LoginTrackingLog.Timestamp) >= '".$time."'";				
		$activeMember = $this->m_dbConnRoot->select($sqlActive);
		return sizeof($activeMember);
	}
}
?>