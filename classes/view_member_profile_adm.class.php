<?php
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");

class view_member_profile_adm extends dbop
{
	public $m_dbConn;
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
$this->display_pg=new display_table($this->m_dbConn);
		//dbop::__construct();
	}
	
	public function show_member_main()
	{
		//$sql = "SELECT * FROM member_main as mm,bg as bgg,unit as u,wing as w,desg as dsg where mm.blood_group=bgg.bg_id and mm.unit=u.unit_id and u.wing_id=w.wing_id and mm.desg=dsg.desg_id and mm.status='Y' and bgg.status='Y' and u.status='Y' and w.status='Y' and dsg.status='Y' and mm.member_id='".$_GET['id']."'";
		$sql = "SELECT wing,unit_no,mm.owner_name,mob,resd_no,off_no,dsg.desg,email,alt_email,dob,wed_any,bgg.bg,member_id,eme_rel_name,eme_contact_1,eme_contact_2,off_add,alt_mob,u.intercom_no,mm.parking_no,u.area FROM member_main as mm,bg as bgg,unit as u,wing as w,desg as dsg where mm.blood_group=bgg.bg_id and mm.unit=u.unit_id and u.wing_id=w.wing_id and mm.desg=dsg.desg_id and mm.status='Y' and bgg.status='Y' and u.status='Y' and w.status='Y' and dsg.status='Y' and mm.member_id='".$_GET['id']."' ";				
		$res = $this->m_dbConn->select($sql);
		//echo $sql;
		return $res;
		
	}
	
	public function show_mem_spouse_details()
	{
		//$sql = "select * from mem_spouse_details as msd,bg as bgg,desg as dsg where msd.member_id='".$_GET['id']."' and msd.spouse_bg=bgg.bg_id and msd.spouse_desg=dsg.desg_id and msd.status='Y' and bgg.status='Y' and dsg.status='Y'";
		$sql = "select spouse_name,msd.member_id,msd.spouse_desg,spouse_dob,spouse_off_add,spouse_off_no,bgg.bg,dsg.desg from mem_spouse_details as msd,bg as bgg,desg as dsg,member_main as membertbl where  membertbl.member_id='".$_GET['id']."' and membertbl.member_id=msd.member_id and msd.spouse_bg=bgg.bg_id and msd.spouse_desg=dsg.desg_id and msd.status='Y' and bgg.status='Y' and dsg.status='Y'";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	public function show_mem_child_details()
	{
		
		
		//$sql = "select * from mem_child_details as msd,bg as bgg,desg as dsg where msd.member_id='".$_GET['id']."' and msd.child_bg=bgg.bg_id and msd.child_desg=dsg.desg_id and msd.status='Y' and bgg.status='Y' and dsg.status='Y'";
		$sql = "select child_name,child_dob,scc,sdd,bg,dsg.desg from mem_child_details as msd,bg as bgg,desg as dsg,member_main as membertbl where membertbl.member_id='".$_GET['id']."' and membertbl.member_id=msd.member_id and msd.child_bg=bgg.bg_id and msd.child_desg=dsg.desg_id and msd.status='Y' and bgg.status='Y' and dsg.status='Y'";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	public function show_mem_other_family()
	{
		
		
		//$sql = "select * from mem_other_family as msd,bg as bgg,desg as dsg where msd.member_id='".$_GET['id']."' and msd.child_bg=bgg.bg_id and msd.other_desg=dsg.desg_id and msd.status='Y' and bgg.status='Y' and dsg.status='Y'";
		$sql = "select other_name,relation,other_dob,dsg.desg,ssc,bg from mem_other_family as msd,bg as bgg,desg as dsg,member_main as membertbl where membertbl.member_id='".$_GET['id']."' and membertbl.member_id=msd.member_id and msd.child_bg=bgg.bg_id and msd.other_desg=dsg.desg_id and msd.status='Y' and bgg.status='Y' and dsg.status='Y'";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	public function show_mem_car_parking()
	{
		//$sql = "select * from mem_car_parking where member_id='".$_GET['id']."' and status='Y'";
		$sql = "select * from mem_car_parking as mcp,member_main as membertbl where membertbl.member_id='".$_GET['id']."' and membertbl.member_id=mcp.member_id and mcp.status='Y' ";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	public function show_mem_bike_parking()
	{
		//$sql = "select * from mem_bike_parking where member_id='".$_GET['id']."' and status='Y'";
		$sql = "select * from mem_bike_parking as mbp,member_main as membertbl where membertbl.member_id='".$_GET['id']."' and membertbl.member_id=mbp.member_id and mbp.status='Y' ";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	public function show_share_certificate_details()
	{
		$sql = "SELECT `unit` FROM `member_main` WHERE `member_id` = '".$_GET['id']."'";
		$unit = $this->m_dbConn->select($sql);
		$sql = "SELECT `share_certificate`, `share_certificate_from`, `share_certificate_to` FROM `unit` WHERE `unit_id` = '".$unit[0]['unit']."'";
		$result = $this->m_dbConn->select($sql);
		return $result;
	}
	
	public function show_share_certificate()
	{
		$sql = 'SELECT `show_share` FROM `society` WHERE `society_id` = "'.$_SESSION['society_id'].'"';
		$result = $this->m_dbConn->select($sql);	
		return $result[0]['show_share'];
	}
}
?>