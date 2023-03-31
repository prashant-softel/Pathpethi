<?php
include_once("include/display_table.class.php");

class commitee extends dbop
{
	public $actionPage = "../commitee.php";
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);			
	}

	public function startProcess()
	{
		$errorExists = 0;
		

		if($_REQUEST['insert']=='Create' && $errorExists==0)
		{						
			$insert_query1 = "INSERT INTO `commitee`(`position`, `member_id`) VALUES ('Secretary','".$_POST['secretory']."')";			
			$data = $this->m_dbConn->insert($insert_query1);
			
			$insert_query2 = "INSERT INTO `commitee`(`position`, `member_id`) VALUES ('Joint Secretary','".$_POST['join_secretory']."')";			
			$data = $this->m_dbConn->insert($insert_query2);
			
			$insert_query3 = "INSERT INTO `commitee`(`position`, `member_id`) VALUES ('Treasurer','".$_POST['treasurer']."')";			
			$data = $this->m_dbConn->insert($insert_query3);
			
			$insert_query4 = "INSERT INTO `commitee`(`position`, `member_id`) VALUES ('Chairman','".$_POST['chairman']."')";			
			$data = $this->m_dbConn->insert($insert_query4);
			
			for($i = 1; $i <= $_POST['no_of_commitee_members']; $i++)
			{
				$insert_query = "INSERT INTO `commitee`(`position`, `member_id`) VALUES ('Committee Member','".$_POST['commitee_member'.$i]."')";			
				$data = $this->m_dbConn->insert($insert_query);
			}
			return "Insert";
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			$up_query1="update `commitee` set `member_id`='".$_POST['secretory']."' where `position` ='Secretary'";
			$data = $this->m_dbConn->update($up_query1);
			
			$up_query2="update `commitee` set `member_id`='".$_POST['join_secretory']."' where `position` ='Joint Secretary'";
			$data = $this->m_dbConn->update($up_query2);
			
			$up_query3="update `commitee` set `member_id`='".$_POST['treasurer']."' where `position` ='Treasurer'";
			$data = $this->m_dbConn->update($up_query3);
			
			$up_query4="update `commitee` set `member_id`='".$_POST['chairman']."' where `position` ='Chairman'";
			$data = $this->m_dbConn->update($up_query4);
			
			$del_query = "DELETE FROM `commitee` WHERE `position` = 'Commitee Member'";
			$result = $this->m_dbConn->delete($del_query);
			
			for($i = 1; $i <= $_POST['no_of_commitee_members']; $i++)
			{
				$insert_query = "INSERT INTO `commitee`(`position`, `member_id`) VALUES ('Commitee Member','".$_POST['commitee_member'.$i]."')";			
				$data = $this->m_dbConn->insert($insert_query);
			}
			return "Update";
		}
		else
		{
			return $errString;
		}
	}
	public function combobox($query,$id, $defaultText = 'Please Select', $defaultValue = '')
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
	
	function getMemberDetails()
	{
		$finalarray = array();
		
		$selectQuery = "SELECT `unit`, `owner_name` FROM `member_main`";
		$result = $this->m_dbConn->select($selectQuery);
		
		for($i = 0; $i < sizeof($result); $i++)
		{			
			$finalarray[$result[$i]['unit']] = $result[$i]['owner_name'];				
		}
		
		return $finalarray;
	}
	
	function getMemberDetailsEx()
	{
		$finalarray = array();
		
		//$selectQuery = "SELECT `mem_other_family_id`, `other_name` FROM `mem_other_family`";
		$selectQuery = "select mo.mem_other_family_id, CONCAT(CONCAT(mo.other_name,' - '), CONCAT(u.unit_no, IF(mo.coowner = 1, ' (Owner)', ' (Co-Owner)'))) AS 'other_name' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit JOIN mem_other_family as mo ON mm.member_id = mo.member_id where u.society_id = '" . DEFAULT_SOCIETY . "' and mm.ownership_status = 1 and mo.status = 'Y' and mo.coowner > 0 ORDER BY u.sort_order, mo.coowner";

		$result = $this->m_dbConn->select($selectQuery);
		
		for($i = 0; $i < sizeof($result); $i++)
		{			
			$finalarray[$result[$i]['mem_other_family_id']] = $result[$i]['other_name'];				
		}
		
		return $finalarray;
	}
	
	function getCommiteeDetails()
	{
		
		//$commiteeMembers = array();
		
		
					
		$sql = "SELECT * FROM `commitee` where member_id !='0'";
		$res = $this->m_dbConn->select($sql);			
		/*if($res <> "")
		{
				
			$commiteeMembers[0]['secretory'] = $finalarray[$res[0]['secretory']];
			$commiteeMembers[0]['join_secretory'] = $finalarray[$res[0]['join_secretory']];
			$commiteeMembers[0]['treasurer'] = $finalarray[$res[0]['treasurer']];
			$commiteeMembers[0]['general_manager'] = $finalarray[$res[0]['general_manager']];
			$commiteeMembers[0]['commitee_member'] = $finalarray[$res[0]['commitee_member']];
		}*/
		//return $commiteeMembers;
		
		return $res;		
	}
	public function getCommiteeCategory()
	{
		$finalarray=array();
		//$sql = "SELECT * FROM `commitee` as c   join `servicerequest_category` as sc on c.member_id=sc.member_id ";
		$sql ="SELECT * FROM  `servicerequest_category`";
		$res = $this->m_dbConn->select($sql);
		
		for($i = 0; $i < sizeof($res); $i++)
		{			
		  $finalarray[$res[$i]['member_id']]  = (isset($finalarray[$res[$i]['member_id']] )) ? $finalarray[$res[$i]['member_id']]  . ', ' .  $res[$i]['category'] : $res[$i]['category'];
			//$finalarray[$res[$i]['member_id']] = $res[$i]['category'];	
		}
		return $finalarray;
	}
	
	function selecting()
	{
		$sql = "SELECT * FROM `commitee`";		
		$res = $this->m_dbConn->select($sql);	
		return $res;	
	}	
	
	function deleting()
	{		
		$sql = "DELETE FROM `commitee` WHERE `id` = ".$_REQUEST['commiteememberId'];		
		$this->m_dbConn->delete($sql);
	}
}
?>