<?php

include_once("dbconst.class.php");
//echo "inc";
//include_once("utility.class.php");
//echo "inc2";
include_once("../classes/include/dbop.class.php");
   	  
	  
class adduser
{
	public $m_dbConnRoot;
	public $m_dbConn;
	//public $m_objUtility;
	
	function __construct($dbConnRoot,$dbConn="")
	{
		//echo "ctor";
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
		//echo "ctor";
		//$this->m_objUtility = new utility($this->m_dbConn,$this->m_dbConnRoot);
	}

	public function combobox($query, $id, $defaultText = 'Please Select', $defaultValue = '0')
	{
		$str = '';
		
		if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "'>" . $defaultText . "</option>";
		}
		$data = $this->m_dbConnRoot->select($query);
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

	public function comboboxEx($query, $id, $defaultText = 'Please Select', $defaultValue = '0')
	{
		$str = '';
		
		if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "'>" . $defaultText . "</option>";
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
	public function getParentOfLedger($ledgerID)
	{
		$sqlSelect = "select categorytbl.group_id, categorytbl.category_name, ledgertbl.categoryid,ledgertbl.ledger_name from ledger As ledgertbl JOIN account_category As categorytbl ON ledgertbl.categoryid = categorytbl.category_id where ledgertbl.id = '" . $ledgerID . "'";
		$result = $this->m_dbConn->select($sqlSelect);
		$sqlGroup="select `groupname` from `group` where `id`='".$result[0]['group_id']."' ";
		$resultGroupName = $this->m_dbConn->select($sqlGroup);
		$aryParent = array();
		$aryParent['group'] = $result[0]['group_id'];
		$aryParent['group_name'] = $resultGroupName[0]['groupname'];
		$aryParent['category'] = $result[0]['categoryid'];
		$aryParent['category_name'] = $result[0]['category_name'];
		$aryParent['ledger_name'] = $result[0]['ledger_name'];
					
		return $aryParent;
	}	
	public function addUser($role, $unit_id, $society_id, $code, $userEmail, $login_id = 0 )
	{
		$desc = $role;
		//echo "add".$role;
		if($role == ROLE_MEMBER)
		{
			//echo "add".$role;
			$desc = $this->geUnitDesc($unit_id, $society_id);
			//print_r($desc);
			if($desc == "")
			{
				$arParentDetails = $this->getParentOfLedger($unit_id);
				if(!(empty($arParentDetails)))
				{
					$desc = $arParentDetails['ledger_name'];
				}
			}
			//echo "add2";
		}
		$profile = 0;
		if($role == ROLE_ADMIN)
		{
			$profile = 1;
		}
		$status = 1;
		if($login_id <> 0)
		{
			$status = 2;	
		}
		if($userEmail <> '')
			{	
				
				$sCode=substr(($code),0, 4);
				$sActivCode=$sCode;
				$sAccountActivationCode=$userEmail . $sActivCode;									
				$codeType=1;
				}
				else
				{
					$sAccountActivationCode=$code;
					$codeType=1;
				}	
		$insert_mapping = "INSERT INTO `mapping`(`society_id`, `unit_id`, `desc`, `role`, `created_by`, `status`, `view`, `code`,`code_type`, `profile`, `login_id`) VALUES ('" . $society_id . "', '" . $unit_id . "', '" . $desc . "', '" . $role . "', '" . $_SESSION['login_id'] . "', '".$status."', '" . strtoupper($role) . "', '" . $sAccountActivationCode . "','".$codeType."', '" . $profile . "', '".$login_id."')";					
		//echo $insert_mapping;		
		$result_mapping = $this->m_dbConnRoot->insert($insert_mapping);
		
		return $result_mapping;
	}
	
	public function updateUserRole($mapID, $role, $status)
	{
		$userview = 'MEMBER';
		if($role == ROLE_SUPER_ADMIN || $role == ROLE_ADMIN)
		{
			$userview = 'ADMIN';
		}
		
		if($status == '1')
		{
			$update_role = "Update mapping SET role = '" . $role . "', view = '" . $userview . "' where id = '" . $mapID . "'";
		}
		else 
		{
			if($status == '2')
			{
				$query = 'Select login_id from mapping where id = "' . $mapID . '"';
				$loginID = $this->m_dbConnRoot->update($query);

				if($loginID[0]['login_id'] == '0')
				{
					$status = "1";
				}
			}
			
			$update_role = "Update mapping SET role = '" . $role . "', view = '" . $userview . "', status = '" . $status . "' where id = '" . $mapID . "'";
		}
		
		$result_role = $this->m_dbConnRoot->update($update_role);
		
		return $result_role;
	}
	
	public function updateUserProfile($mapID, $profile)
	{
		//Check if the profile already exist in the profile table
		//Create where clause
		$whereClause = '';
		$bFirst = true;
		foreach($profile as $k => $v)
		{
			if($bFirst)
			{
				$whereClause .= " `" . $k . "` = '" . $v . "'";
				$bFirst = false;
			}
			else
			{
				$whereClause .= " and `" . $k . "` = '" . $v . "'";
			}
		}
		
		$sql = "Select `id` from profile where " . $whereClause;
		
		$result = $this->m_dbConnRoot->select($sql);
		
		if($result <> '')
		{
			$update_profile = "Update mapping SET profile = '" . $result[0]['id'] . "' where id = '" . $mapID . "'";
			$result_profile = $this->m_dbConnRoot->update($update_profile);
		}
		else
		{
			//create new profile
			$keys = '';
			$values = '';
			$bFirst = true;
			foreach($profile as $k => $v)
			{
				if($bFirst)
				{
					$keys .= "`" . $k . "`";
					$values .= "'" . $v . "'";
					$bFirst = false;
				}
				else
				{
					$keys .= ", `" . $k . "`";
					$values .= ", '" . $v . "'";
				}
			}
			
			$sqlInsert = "Insert into `profile` (" . $keys . ") VALUES (" . $values . ")";
			$resultInsert = $this->m_dbConnRoot->insert($sqlInsert);
			
			$update_profile = "Update mapping SET profile = '" . $resultInsert . "' where id = '" . $mapID . "'";
			$result_profile = $this->m_dbConnRoot->update($update_profile);
		}
	}
	
	private function geUnitDesc($unit_id, $society_id)
	{
		$desc = '';
		
		$sql = "Select `desc` from mapping where unit_id = '" . $unit_id . "' and society_id = '" . $society_id . "'";
		//echo "qe".$sql;
		$result = $this->m_dbConnRoot->select($sql);
		//print_r($result);
		return $result[0]['desc'];
	}
	
}
?>