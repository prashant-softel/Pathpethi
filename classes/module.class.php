<?php

include_once("dbconst.class.php");

class adduser
{
	public $m_dbConnRoot;
	
	function __construct($dbConnRoot)
	{
		$this->m_dbConnRoot = $dbConnRoot;
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
	
	public function addUser($role, $unit_id, $society_id, $code)
	{
		$desc = $role;
		if($role == ROLE_MEMBER)
		{
			$desc = $this->geUnitDesc($unit_id, $society_id);
		}
		$profile = 0;
		if($role == ROLE_ADMIN)
		{
			$profile = 1;
		}
		
		$insert_mapping = "INSERT INTO `mapping`(`society_id`, `unit_id`, `desc`, `role`, `created_by`, `status`, `view`, `code`, `profile`) VALUES ('" . $society_id . "', '" . $unit_id . "', '" . $desc . "', '" . $role . "', '" . $_SESSION['login_id'] . "', 1, '" . strtoupper($role) . "', '" . $code . "', '" . $profile . "')";
						
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
		$result = $this->m_dbConnRoot->select($sql);
		
		return $result[0]['desc'];
	}
	
}
?>