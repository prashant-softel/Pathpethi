<?php
	//error_reporting(7);
	//echo "ctor";
	include_once "dbconst.class.php";	
	//echo "inc dbcon";
	include_once "adduser.class.php";
	//echo "inc adduser";
	include_once "initialize.class.php";
	//echo "inc ini";
	
	class activation_email
	{
		private $m_dbConn;
		public $m_dbConnRoot;
		public $obj_addduser;
		public $obj_initialize;
		
		function __construct($dbConn, $dbConnRoot = "")
		{
			//echo "ctor act";
			$this->m_dbConn = $dbConn;
			$this->m_dbConnRoot = $dbConnRoot;
			$this->obj_addduser = new adduser($this->m_dbConnRoot,$this->m_dbConn);
			$this->obj_initialize = new initialize($this->m_dbConnRoot);
			//echo "ctor act end";
			//$this->obj_utility = new utility($this->m_dbConn, $this->m_dbConnRoot);
		}
		
		function AddMappingAndSendActivationEmail($role, $unit_id, $society_id, $code, $NewUserEmailID, $name)
		{
			//echo "trace:";
			$result = $this->obj_addduser->addUser($role, $unit_id, $society_id, $code,$NewUserEmailID);
						
				//		echo "trace2".$result ;
			if($result > 0)
			{
				$ActivationStatus = $this->obj_initialize->sendNewUserActivationEmail( $NewUserEmailID, $name,$code, "1", $society_id);
				//echo "ActivationStatus:".$ActivationStatus;
					return $ActivationStatus;
				
			}
		}
		function CheckIfMappingAlreadyExist($EmailID, $SocID, $UnitID)
		{
			$sqlQry = "select * from login where `member_id`='".$EmailID."'";
			$res = $this->m_dbConnRoot->select($sqlQry);
			if(isset($res))
			{
				$loginId = $res[0]['login_id'];
				//echo "UnitID -".$UnitID ." LoginID -".$loginId .".";
				$sqlMapp = "select * from mapping where `login_id`='".$loginId ."'";
				$mapRes = $this->m_dbConnRoot->select($sqlMapp);
				$bMappingFound = false;
				if(isset($mapRes))
				{
					foreach($mapRes as $mapping)
					{
						//echo "loginid:".$mapping['login_id'] ."socid:".$mapping['society_id']."unitid:".$mapping['unit_id'];
						if(($mapping['login_id'] == $loginId) && ($mapping['society_id'] == $SocID) && ($mapping['unit_id'] == $UnitID))
						{
							$bMappingFound = true;
							return ACCOUNT_EXIST_ACTIVE;
						}
						//echo sizeof($mapRes);
					}
					
				}
				if($bMappingFound == false)
				{
					return ACCOUNT_EXIST_MAPPING_NOT_FOUND;
						
				}
			}
			else
			{
				return NO_ACCOUNT;
			}
		}
	}