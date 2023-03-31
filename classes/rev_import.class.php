<?php
include_once("../classes/include/dbop.class.php");
class rev_import extends dbop
{
	//public $actionPage = "../society.php";
	public $m_dbConn;
	public $actionPage = "../rev_import.php";
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
//$this->display_pg=new display_table($this->m_dbConn);
		//dbop::__construct();
	}

public function ReverseImport()
{
	
	if($_REQUEST['insert']=='Submit')
	{
		if(isset($_POST['society_name']) && isset($_POST['wing']))
		{
			$delete_wing="delete from `wing` where society_id='".$_POST['society_name']."'";
			$res00 = $this->m_dbConn->delete($delete_wing);
		//print_r($res00);
			$update_import_history="update `import_history` set wing_flag=0 where society_id='".$_POST['society_name']."'";							
			$res123=$this->m_dbConn->update($update_import_history);
			
		 return "Wing Deleted Successfully";
		}
		
		if(isset($_POST['society_name']) && isset($_POST['unit']))
		{
			$delete_unit="delete from `unit` where society_id='".$_POST['society_name']."'";
			$res00 = $this->m_dbConn->delete($delete_unit);
			$update_import_history="update `import_history` set unit_flag=0 where society_id='".$_POST['society_name']."'";							
			$res123=$this->m_dbConn->update($update_import_history);
			return "Unit Deleted Successfully";
		}
		
		if(isset($_POST['society_name']) && isset($_POST['member']))
		{
			$delete_member="delete from `member_main` where society_id='".$_POST['society_name']."'";
			$res00 = $this->m_dbConn->delete($delete_member);
			$update_import_history="update `import_history` set member_flag=0 where society_id='".$_POST['society_name']."'";							
			$res123=$this->m_dbConn->update($update_import_history);
			return "Members Deleted Successfully";
		}
		
		if(isset($_POST['society_name']) && isset($_POST['ledger']))
		{
			$delete_ledger="delete from `ledger` where society_id='".$_POST['society_name']."'";
			$res00 = $this->m_dbConn->delete($delete_ledger);
			$update_import_history="update `import_history` set ledger_flag=0 where society_id='".$_POST['society_name']."'";							
			$res123=$this->m_dbConn->update($update_import_history);
			return "Ledgers Deleted Successfully";
		}
		
		
		if(isset($_POST['society_name']) && isset($_POST['tarrif']))
		{
			$delete_ledger="delete from `unitbillmaster` where society_id='".$_POST['society_name']."'";
			$res00 = $this->m_dbConn->delete($delete_ledger);
			$update_import_history="update `import_history` set tarrif_flag=0 where society_id='".$_POST['society_name']."'";							
			$res123=$this->m_dbConn->update($update_import_history);
			return "Tarrif Details Deleted Successfully";
		}
		
	}
	//return $result;
	
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
		
}
?>	