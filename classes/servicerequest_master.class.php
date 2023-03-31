<?php
include_once("include/display_table.class.php");

class serviceRequest_Category extends dbop
{
	public $actionPage = "../servicerequest_master.php";
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);

		//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
		//$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);		
	}

	public function startProcess()
	{
		$errorExists = 0;

		//$curdate 		=  $this->curdate;
		//$curdate_show	=  $this->curdate_show;
		//$curdate_time	=  $this->curdate_time;
		//$ip_location	=  $this->ip_location;
		
		if($_REQUEST['insert']=='Insert' && $errorExists==0)
		{
			if($_POST['check'] == 'on')
			{
				$is_visible = 1;
			}
			else
			{
				$is_visible = 0;
			}
			$mem_other_family_id = $_POST['member'];
			$sql_get_unit_id = "SELECT mof.mem_other_family_id, mof.member_id, mm.member_id, mm.unit FROM `member_main` mm, `mem_other_family` mof where mof.mem_other_family_id = '".$mem_other_family_id."' and mof.member_id = mm.member_id";
			$sql_get_unit_id_res = $this->m_dbConn->select($sql_get_unit_id);
			$unit_id = $sql_get_unit_id_res[0]['unit'];
			
			$co_mem_other_family_id = $_POST['co-member'];
			$sql_get_co_unit_id = "SELECT mof.mem_other_family_id, mof.member_id, mm.member_id, mm.unit FROM `member_main` mm, `mem_other_family` mof where mof.mem_other_family_id = '".$co_mem_other_family_id."' and mof.member_id = mm.member_id";
			$sql_get_co_unit_id_res = $this->m_dbConn->select($sql_get_co_unit_id);
			$co_unit_id = $sql_get_co_unit_id_res[0]['unit'];
			
			$insert_query= "INSERT INTO `servicerequest_category`(`category`, `email`,`email_cc`, `member_id`, `unitID`, `co_member_id`, `co_unitID`, `be_visible`) VALUES ('".$_POST['category']."','".$_POST['email']."','".$_POST['email_cc']."','".$_POST['member']."','".$unit_id."','".$_POST['co-member']."','".$co_unit_id."','".$is_visible."')";
			//echo $insert_query;
			$data = $this->m_dbConn->insert($insert_query);
			return "Insert";
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			if($_POST['check'] == 'on')
			{
				$is_visible = 1;
			}
			else
			{
				$is_visible = 0;
			}
			
			$mem_other_family_id = $_POST['member'];
			$sql_get_unit_id = "SELECT mof.mem_other_family_id, mof.member_id, mm.member_id, mm.unit FROM `member_main` mm, `mem_other_family` mof where mof.mem_other_family_id = '".$mem_other_family_id."' and mof.member_id = mm.member_id";
			$sql_get_unit_id_res = $this->m_dbConn->select($sql_get_unit_id);
			$unit_id = $sql_get_unit_id_res[0]['unit'];
			
			$co_mem_other_family_id = $_POST['co-member'];
			$sql_get_co_unit_id = "SELECT mof.mem_other_family_id, mof.member_id, mm.member_id, mm.unit FROM `member_main` mm, `mem_other_family` mof where mof.mem_other_family_id = '".$co_mem_other_family_id."' and mof.member_id = mm.member_id";
			$sql_get_co_unit_id_res = $this->m_dbConn->select($sql_get_co_unit_id);
			$co_unit_id = $sql_get_co_unit_id_res[0]['unit'];
			
			$up_query="update `servicerequest_category` set `category`='".$_POST['category']."',`email`='".$_POST['email']."',`email_cc`='".$_POST['email_cc']."', `member_id`='".$_POST['member']."', `unitID`='".$unit_id."', `co_member_id`='".$_POST['co-member']."', `co_unitID`='".$co_unit_id."', `be_visible`='".$is_visible."' where id='".$_POST['id']."'";
			$data = $this->m_dbConn->update($up_query);
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
	
	public function display1($rsas)
	{
		$thheader = array('Category Name','Assigned Member Name', 'Email','Email (CC)','Co Assigned Member Name');
		$this->display_pg->edit		= "getcategory";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "servicerequest_master.php";

		$res = $this->display_pg->display_datatable($rsas);
		return $res;
	}
	public function pgnation()
	{		
		//$sql1 = "SELECT ser.id, ser.category, CONCAT(CONCAT(mem_other_family.other_name,' - '), CONCAT(u.unit_no, IF(mem_other_family.coowner = 1, ' (Owner)', ' (Co-Owner)'))), ser.email FROM `servicerequest_category` AS ser JOIN `mem_other_family` ON ser.member_id = mem_other_family.mem_other_family_id JOIN member_main ON member_main.member_id = mem_other_family.member_id JOIN unit as u ON member_main.unit = u.unit_id WHERE ser.status = 'Y' AND member_main.ownership_status =1";		
		$sql1 = "SELECT ser.id, ser.category, CONCAT(CONCAT(mem_other_family.other_name,' ('), CONCAT(u.unit_no, ')')) as mem_name, ser.email,ser.email_cc FROM `servicerequest_category` AS ser JOIN `mem_other_family` ON ser.member_id = mem_other_family.mem_other_family_id JOIN member_main ON member_main.member_id = mem_other_family.member_id JOIN unit as u ON member_main.unit = u.unit_id WHERE ser.status = 'Y' AND member_main.ownership_status =1";
		$result = $this->m_dbConn->select($sql1);
		
		$sql3 = "SELECT ser.id, ser.category, CONCAT(CONCAT(mem_other_family.other_name,' ('), CONCAT(u.unit_no, ')')) as co_mem_name, ser.email,ser.email_cc FROM `servicerequest_category` AS ser JOIN `mem_other_family` ON ser.co_member_id = mem_other_family.mem_other_family_id JOIN member_main ON member_main.member_id = mem_other_family.member_id JOIN unit as u ON member_main.unit = u.unit_id WHERE ser.status = 'Y' AND member_main.ownership_status =1";
		$sql3_res = $this->m_dbConn->select($sql3);
		
		for($i = 0; $i < sizeof($result); $i++)
		{
			$found = 0;
			for($j = 0; $j < sizeof($sql3_res); $j++)
			{
				if($result[$i]['id'] == $sql3_res[$j]['id'])
				{
					$result[$i]['co_mem_name'] = $sql3_res[$j]['co_mem_name'];
					$found = 1;
				}
			}
			if($found == 0)
			{
				$sql3_res1 = array("co_mem_name" => '0');
				$result[$i]['co_mem_name'] = $sql3_res1['co_mem_name'];
			}
		}
		
		/*echo "Result:";
		echo "<pre>";
		print_r($result);
		echo "</pre>";*/
		
		$sql2 = "SELECT id, category, unitID as mem_name, email, email_cc, co_member_id as co_mem_name FROM `servicerequest_category` where unitID = 0 and member_id = 0";
		$sql02 = $this->m_dbConn->select($sql2);
		if($result == "")
		{
			$result = array();
		}

		foreach($sql02 as $key)
		{
			/*echo "<pre>";
			print_r($key);
			echo "</pre>";*/
			array_push($result,$key);
		}
		
		/*echo "Result:";
		echo "<pre>";
		print_r($result);
		echo "</pre>";*/

		$data=$this->display1($result);
		return $data;
	}
	public function selecting()
	{
		$sql = "SELECT id, category, unitID, email, email_cc, member_id, co_member_id, be_visible FROM `servicerequest_category` WHERE id='".$_REQUEST['categoryId']."'";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	public function deleting()
	{
		$sql = "update `servicerequest_category` set status='N' where id='".$_REQUEST['categoryId']."'";
		$res = $this->m_dbConn->update($sql);		
	}
	
	public function getEmailOfMember()
	{
		$sql = "SELECT `other_email` FROM `mem_other_family` WHERE `mem_other_family_id` = '".$_REQUEST['member_id']."'";			
		$result = $this->m_dbConn->select($sql);
		return $result[0]['other_email'];
	}
}
?>