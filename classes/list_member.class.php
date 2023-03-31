<?php if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once("dbconst.class.php");
include_once("utility.class.php");

class list_member extends dbop
{
	public $actionPage = "../list_member.php";
	public $m_dbConn;
	public $obj_utility;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$dbopRoot = new dbop(true);
		$this->obj_utility = new utility($this->m_dbConn, $dbopRoot);
		$this->display_pg = new display_table($this->m_dbConn);
		//dbop::__construct();
	}
	
	public function combobox($query)
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
						$str.="<OPTION VALUE=".$v.">";
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
	public function combobox07($query,$id)
	{
	$str.="<option value=''>All</option>";
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
			$thheader=array('Member Name','Parking Slot','Bike Reg No.','Bike Owner','Bike Model','Bike Make','Bike Color');
			$this->display_pg->edit="list_member";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="list_member.php";
			
			//$res = $this->display_pg->display_new($rsas);
			$res = $this->list_member_show($rsas);
			
			return $res;
	}
	public function getAllMemberDetails()
	{
		$sql1 = "SELECT * FROM member_main WHERE status = 'Y'";
		return $this->m_dbConn->select($sql1);
	}
	public function getAllUnits()
	{
		$sql="select `unit_id` from `unit` where `society_id` = ".$_SESSION['society_id']." and `status` = 'Y' order by sort_order asc";
		$res=$this->m_dbConn->select($sql);
		$flatten = array();
    	foreach($res as $key)
		{
			$flatten[] = $key['unit_id'];
		}

    	return $flatten;
	}

	



	public function list_member_show($res)
	{
		
	}
	public function chk_delete_perm_admin()
	{
		$sql = "select * from del_control_admin where status='Y' and login_id='".$_SESSION['login_id']."'";
		$res = $this->m_dbConn->select($sql);
		return $res[0]['del_control_admin'];
	}
	public function selecting()
	{
		$sql1="select mem_bike_parking_id,`member_id`,`parking_slot`,`bike_reg_no`,`bike_owner`,`bike_model`,`bike_make`,`bike_color` from mem_bike_parking where mem_bike_parking_id='".$_REQUEST['mem_bike_parkingId']."'";
		$var=$this->m_dbConn->select($sql1);
		return $var;
	}
	public function del_member()
	{
		$kk = 2;
		$pp = 0;
		
		if($kk==1)
		{
			$sql1 = "update member_main set status='N' where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql2 = "update mem_spouse_details set status='N' where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql3 = "update mem_child_details set status='N' where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql4 = "update mem_other_family set status='N' where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql5 = "update mem_bike_parking set status='N' where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql6 = "update mem_car_parking set status='N' where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql7 = "update login set status='N' where com_id='".$_REQUEST['member_id']."' and status='Y'";
			
			if($pp==1)
			{
			$res1 = $this->m_dbConn->update($sql1);
			$res2 = $this->m_dbConn->update($sql2);
			$res3 = $this->m_dbConn->update($sql3);
			$res4 = $this->m_dbConn->update($sql4);
			$res5 = $this->m_dbConn->update($sql5);
			$res6 = $this->m_dbConn->update($sql6);
			$res7 = $this->m_dbConn->update($sql7);
			}
		}
		else
		{
			$sql1 = "delete from member_main where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql2 = "delete from mem_spouse_details where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql3 = "delete from mem_child_details where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql4 = "delete from mem_other_family where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql5 = "delete from mem_bike_parking where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql6 = "delete from mem_car_parking where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql7 = "delete from login where com_id='".$_REQUEST['member_id']."' and status='Y'";
			
			
			$res1 = $this->m_dbConn->delete($sql1);
			$res2 = $this->m_dbConn->delete($sql2);
			$res3 = $this->m_dbConn->delete($sql3);
			$res4 = $this->m_dbConn->delete($sql4);
			$res5 = $this->m_dbConn->delete($sql5);
			$res6 = $this->m_dbConn->delete($sql6);
			$res7 = $this->m_dbConn->delete($sql7);
		}
	}
	
	public function soc_name($society_id)
	{
		$sql = "select * from society where society_id='".$society_id."' and status='Y'";
		$res = $this->m_dbConn->select($sql);
		echo $res[0]['society_name'];
	}
	
	public function get_wing()
	{
		if($_REQUEST['society_id']<>"")
		{	
			$sql = "select * from wing where status='Y' and society_id='".$_REQUEST['society_id']."'";
		}
		else
		{
			$sql = "select * from wing where status='Y'";	
		}
		$res = $this->m_dbConn->select($sql);	
			
		if($res<>"")
		{
			$i=0;
			foreach($res as $k => $v)
			{
			 echo $res[$k]['wing_id']."#".$res[$k]['wing']."###";
			 $i++;
			}
			echo "****".$i;
		}
		else
		{
			echo ""."#"."0";
			echo "****"."0";
		}
	}
	
	public function get_wing_new()
	{
		if($_REQUEST['society_id']<>"")
		{	
			$sql = "select * from wing where status='Y' and society_id='".$_REQUEST['society_id']."'";
		}
		else
		{
			$sql = "select * from wing where status='Y'";	
		}
		$res = $this->m_dbConn->select($sql);	
			
		if($res<>"")
		{
			$aryResult = array();
			foreach($res as $k => $v)
			{
			 	$show_dtl = array("id"=>$res[$k]['wing_id'], "wing"=>$res[$k]['wing']);
				array_push($aryResult,$show_dtl);
			}
			echo json_encode($aryResult);
		}
		else
		{
			echo json_encode(array(array("success"=>1), array("message"=>'No Data To Display')));
		}
	}
	
	public function get_society_new()
	{
		$sql = "select * from society where status='Y'";	
		
		$res = $this->m_dbConn->select($sql);	
			
		if($res<>"")
		{
			$aryResult = array();
			foreach($res as $k => $v)
			{
			 	$show_dtl = array("id"=>$res[$k]['society_id'], "society"=>$res[$k]['society_name']);
				array_push($aryResult,$show_dtl);
			}
			echo json_encode($aryResult);
		}
		else
		{
			echo json_encode(array(array("success"=>1), array("message"=>'No Data To Display')));
		}
	}
	
	
	public function display_society_name($society_id)
	{
		$sql="select society_name from society where society_id=".$society_id." ";
		$data=$this->m_dbConn->select($sql);
		return $data[0]['society_name'];
	}

	public function getMemberDetails($member_id){

		if(!empty($member_id)){
			$query = "SELECT member_id, owner_name, gender, mob, email, dob, member_aadhar_number, member_pan_number, member_occupation, member_area, member_city, member_state, member_category, alt_address 
				  FROM member_main WHERE member_id = '$member_id'";
			return $this->m_dbConn->select($query);
		}
		
	}
}
?>