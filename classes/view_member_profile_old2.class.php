<?php if(!isset($_SESSION)){ session_start(); }
include_once("include/dbop.class.php");
include_once("include/display_table.class.php");

include_once("activate_user_email.class.php");

class view_member_profile extends dbop
{
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_activation ;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = new dbop(true);
$this->display_pg=new display_table($this->m_dbConn);
		
		$this->obj_activation = new activation_email($this->m_dbConn, $this->m_dbConnRoot);
		//dbop::__construct();
	}
	
	public function show_member_main()
	{
		$sql = "SELECT wing,unit_no,mm.unit,mm.owner_name,mm.primary_owner_name,mob,resd_no,off_no,dsg.desg_id,dsg.desg,email,alt_email,dob,wed_any,bgg.bg_id,bgg.bg,eme_rel_name,eme_contact_1,eme_contact_2,off_add,alt_mob,mm.parking_no,u.area,mm.profile,mm.publish_contact,mm.publish_profile,intercom_no,mm.alt_address,mm.owner_gstin_no FROM member_main as mm,bg as bgg,unit as u,wing as w,desg as dsg where mm.blood_group=bgg.bg_id and mm.unit=u.unit_id and u.wing_id=w.wing_id and mm.desg=dsg.desg_id and mm.status='Y' and bgg.status='Y' and u.status='Y' and w.status='Y' and dsg.status='Y' and mm.member_id='".$_GET['id']."' ";
		
		
		$res = $this->m_dbConn->select($sql);
	
		return $res;
	}
	public function show_member_main_by_OwnerID()
	{
		$sql = "SELECT `wing`,unit_no,mm.unit,mm.owner_name,mm.primary_owner_name,mob,resd_no,off_no,dsg.desg_id,dsg.desg,email,alt_email,dob,wed_any,bgg.bg_id,bgg.bg,eme_rel_name,eme_contact_1,eme_contact_2,off_add,alt_mob,mm.parking_no,u.area,mm.profile,mm.publish_contact,mm.publish_profile,intercom_no,mm.alt_address,mm.owner_gstin_no FROM member_main as mm,bg as bgg,unit as u,wing as w,desg as dsg where mm.blood_group=bgg.bg_id and mm.unit=u.unit_id and u.wing_id=w.wing_id and mm.desg=dsg.desg_id and mm.status='Y' and bgg.status='Y' and u.status='Y' and w.status='Y' and dsg.status='Y' and mm.member_id='".$_SESSION['owner_id']."' ";
		//echo "string".$sql;
		
		$res = $this->m_dbConn->select($sql);
	
		return $res;
	}
	public function show_mem_other_family()
	{
		
		
		//$sql = "select * from mem_other_family as msd,bg as bgg,desg as dsg where msd.member_id='".$_GET['id']."' and msd.child_bg=bgg.bg_id and msd.other_desg=dsg.desg_id and msd.status='Y' and bgg.status='Y' and dsg.status='Y'";
		$sql = "select mem_other_family_id,other_name,relation,other_dob,dsg.desg_id,dsg.desg,ssc,bgg.bg_id,bg,msd.coowner, msd.other_profile, msd.other_mobile, msd.other_email,msd.other_publish_profile,msd.coowner,msd.other_wed,msd.other_publish_contact,msd.send_commu_emails from mem_other_family as msd,bg as bgg,desg as dsg,member_main as membertbl where membertbl.member_id='".$_GET['id']."' and membertbl.member_id=msd.member_id and msd.child_bg=bgg.bg_id and msd.other_desg=dsg.desg_id and msd.status='Y' and bgg.status='Y' and dsg.status='Y'";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	public function show_mem_car_parking()
	{
		
		$sql = "select * from mem_car_parking as mcp,member_main as membertbl where membertbl.member_id='".$_GET['id']."' and membertbl.member_id=mcp.member_id and mcp.status='Y' ";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	public function show_mem_bike_parking()
	{
		$sql = "select * from mem_bike_parking as mbp,member_main as membertbl where membertbl.member_id='".$_GET['id']."' and membertbl.member_id=mbp.member_id and mbp.status='Y' ";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	public function show_share_certificate_details()
	{
		$sql = "SELECT `unit` FROM `member_main` WHERE `member_id` = '".$_GET['id']."'";
		$unit = $this->m_dbConn->select($sql);
		$sql = "SELECT `share_certificate`, `share_certificate_from`, `share_certificate_to`, `nomination`,nominee_name FROM `unit` WHERE `unit_id` = '".$unit[0]['unit']."'";
		$result = $this->m_dbConn->select($sql);
		return $result;
	}
	
	public function show_share_certificate()
	{
		$sql = 'SELECT `show_share` FROM `society` WHERE `society_id` = "'.$_SESSION['society_id'].'"';
		$result = $this->m_dbConn->select($sql);	
		return $result[0]['show_share'];
	}

	public function combobox11($query,$id)
	{
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
	public function ComboboxWithDefaultSelect($query,$id)
		{
		//$str.="<option value=''>All</option>";
		$str.="<option value='0'>Undefine</option>";
		$data = $this->m_dbConn->select($query);
		//print_r($data);
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
}
?>