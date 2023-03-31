<?php
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");

class view_member_profile_adm_edit extends dbop
{
	public $m_dbConn;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
$this->display_pg=new display_table($this->m_dbConn);
		//dbop::__construct();
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
	
	public function show_member_main()
	{
		//$sql = "SELECT * FROM member_main as mm,bg as bgg,unit as u,wing as w,desg as dsg where mm.blood_group=bgg.bg_id and mm.unit=u.unit_id and u.wing_id=w.wing_id and mm.desg=dsg.desg_id and mm.status='Y' and bgg.status='Y' and u.status='Y' and w.status='Y' and dsg.status='Y' and mm.unit=".$_SESSION['unit_id']." ";
		$sql = "SELECT wing,unit_no,mm.owner_name,mob,resd_no,off_no,dsg.desg,email,alt_email,dob,wed_any,bgg.bg,eme_rel_name,eme_contact_1,eme_contact_2,off_add,alt_mob,u.intercom_no,mm.parking_no,u.area FROM member_main as mm,bg as bgg,unit as u,wing as w,desg as dsg where mm.blood_group=bgg.bg_id and mm.unit=u.unit_id and u.wing_id=w.wing_id and mm.desg=dsg.desg_id and mm.status='Y' and bgg.status='Y' and u.status='Y' and w.status='Y' and dsg.status='Y' and mm.member_id='".$_GET['id']."' ";
		//echo $sql;
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	public function show_mem_spouse_details()
	{
		//$sql = "select * from mem_spouse_details as msd,bg as bgg,desg as dsg,member_main as membertbl where membertbl.unit=".$_SESSION['unit_id']." and msd.spouse_bg=bgg.bg_id and msd.spouse_desg=dsg.desg_id and msd.status='Y' and bgg.status='Y' and dsg.status='Y'";
		$sql = "select spouse_name,msd.member_id,msd.spouse_desg,spouse_dob,spouse_off_add,spouse_off_no,bgg.bg,bgg.bg_id,dsg.desg from mem_spouse_details as msd,bg as bgg,desg as dsg,member_main as membertbl where  membertbl.member_id='".$_GET['id']."' and membertbl.member_id=msd.member_id and msd.spouse_bg=bgg.bg_id and msd.spouse_desg=dsg.desg_id and msd.status='Y' and bgg.status='Y' and dsg.status='Y'";
		//echo $sql;
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	public function show_mem_child_details()
	{
		
		
		//$sql = "select * from mem_child_details as msd,bg as bgg,desg as dsg where msd.member_id='".$_GET['id']."' and msd.child_bg=bgg.bg_id and msd.child_desg=dsg.desg_id and msd.status='Y' and bgg.status='Y' and dsg.status='Y'";
		$sql = "select child_name,child_dob,scc,sdd,bg,dsg.desg,desg_id,bgg.bg_id,msd.mem_child_details_id from mem_child_details as msd,bg as bgg,desg as dsg,member_main as membertbl where membertbl.member_id='".$_GET['id']."' and membertbl.member_id=msd.member_id and msd.child_bg=bgg.bg_id and msd.child_desg=dsg.desg_id and msd.status='Y' and bgg.status='Y' and dsg.status='Y'";
		//echo $sql;
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	public function show_mem_other_family()
	{
		
		
		//$sql = "select * from mem_other_family as msd,bg as bgg,desg as dsg where msd.member_id='".$_GET['id']."' and msd.child_bg=bgg.bg_id and msd.other_desg=dsg.desg_id and msd.status='Y' and bgg.status='Y' and dsg.status='Y'";
		$sql = "select other_name,relation,other_dob,dsg.desg,desg_id,ssc,bg,bgg.bg_id,msd.mem_other_family_id from mem_other_family as msd,bg as bgg,desg as dsg,member_main as membertbl where membertbl.member_id='".$_GET['id']."' and membertbl.member_id=msd.member_id and msd.child_bg=bgg.bg_id and msd.other_desg=dsg.desg_id and msd.status='Y' and bgg.status='Y' and dsg.status='Y'";
		//echo $sql;
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	public function show_mem_car_parking()
	{
		//$sql = "select * from mem_car_parking as mcp,member_main as membertbl where membertbl.unit=".$_SESSION['unit_id']." and mcp.status='Y'";
		$sql = "select * from mem_car_parking as mcp,member_main as membertbl where membertbl.member_id='".$_GET['id']."' and membertbl.member_id=mcp.member_id and mcp.status='Y' ";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	public function show_mem_bike_parking()
	{
		//$sql = "select * from mem_bike_parking as mbp,member_main as membertbl where membertbl.unit=".$_SESSION['unit_id']."  and mbp.status='Y'";
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
	
	public function update_member_profile()
	{		
		################################################################## Member Main Update ##################################################################
		$sql = "update member_main set owner_name='".addslashes(trim($_POST['owner_name']))."', resd_no='".addslashes(trim($_POST['resd_no']))."', mob='".addslashes(trim($_POST['mob']))."', alt_mob='".addslashes(trim($_POST['alt_mob']))."', off_no='".addslashes(trim($_POST['off_no']))."', off_add='".addslashes(trim($_POST['off_add']))."', desg='".addslashes(trim($_POST['desg']))."', email='".addslashes(trim($_POST['email']))."', alt_email='".addslashes(trim($_POST['alt_email']))."', dob='".addslashes(trim($_POST['dob']))."', wed_any='".addslashes(trim($_POST['wed_any']))."', blood_group='".addslashes(trim($_POST['bg']))."', eme_rel_name='".addslashes(trim($_POST['eme_rel_name']))."', eme_contact_1='".addslashes(trim($_POST['eme_contact_1']))."', eme_contact_2='".addslashes(trim($_POST['eme_contact_2']))."', parking_no='".$this->m_dbConn->escapeString($_POST['parkingNo'])."' where member_id='".$_POST['id']."'";		
		$res = $this->m_dbConn->update($sql);
		//echo $sql;
		################################################################## Member Main Update ##################################################################
		
		$sql = "UPDATE `unit` SET `intercom_no`='".$_POST['intercom_no']."' WHERE `unit_no` = '".$_POST['flat_no']."'";
		$result = $this->m_dbConn->update($sql);
		
		################################################################## Member Spouse Update ##################################################################
		$sql1 = "update mem_spouse_details set spouse_name='".addslashes(trim($_POST['spouse_name']))."', spouse_desg='".addslashes(trim($_POST['spouse_desg']))."', spouse_dob='".addslashes(trim($_POST['spouse_dob']))."', spouse_off_add='".addslashes(trim($_POST['spouse_off_add']))."', spouse_off_no='".addslashes(trim($_POST['spouse_off_no']))."', spouse_bg='".addslashes(trim($_POST['spouse_bg']))."' where member_id='".$_POST['id']."'";
		$res1 = $this->m_dbConn->update($sql1);
		//echo $sql1;
		################################################################## Member Spouse Update ##################################################################
		
		
		
		################################################################## Member Child Update ##################################################################
		
		//echo 'tot_child'.$_POST['tot_child'];
		for($i=1;$i<=$_POST['tot_child'];$i++)
		{
		$sql2 = "update mem_child_details set child_name='".addslashes(trim($_POST['child_name'.$i]))."', child_desg='".addslashes(trim($_POST['child_desg'.$i]))."', child_dob='".addslashes(trim($_POST['child_dob'.$i]))."', scc='".addslashes(trim($_POST['scc'.$i]))."', sdd='".addslashes(trim($_POST['sdd'.$i]))."', child_bg='".addslashes(trim($_POST['child_bg'.$i]))."' where mem_child_details_id='".$_POST['mem_child_details_id'.$i]."' and member_id='".$_POST['id']."'";
		$res2 = $this->m_dbConn->update($sql2);
		//echo '<br>';
		//echo $sql2;
		}
		################################################################## Member Child Update ##################################################################
		
		
		
		################################################################## Member Other Update ##################################################################
		for($i1=1;$i1<=$_POST['tot_other'];$i1++)
		{
		$sql3 = "update mem_other_family set other_name='".addslashes(trim($_POST['other_name'.$i1]))."', relation='".addslashes(trim($_POST['relation'.$i1]))."', other_dob='".addslashes(trim($_POST['other_dob'.$i1]))."', other_desg='".addslashes(trim($_POST['other_desg'.$i1]))."', ssc='".addslashes(trim($_POST['ssc_other'.$i1]))."', child_bg='".addslashes(trim($_POST['other_bg'.$i1]))."' where mem_other_family_id ='".$_POST['mem_other_family_id'.$i1]."' and member_id='".$_POST['id']."'";
		$res3 = $this->m_dbConn->update($sql3);
		//echo '<br>';
		//echo $sql3;
		}
		################################################################## Member Other Update ##################################################################
		
		
		
		################################################################## Member Car Update ##################################################################
		for($i2=1;$i2<=$_POST['tot_car'];$i2++)
		{
		$sql4 = "update mem_car_parking set parking_slot='".addslashes(trim($_POST['parking_slot'.$i2]))."', car_reg_no='".addslashes(trim($_POST['car_reg_no'.$i2]))."', car_owner='".addslashes(trim($_POST['car_owner'.$i2]))."', car_model='".addslashes(trim($_POST['car_model'.$i2]))."', car_make='".addslashes(trim($_POST['car_make'.$i2]))."',  car_color='".addslashes(trim($_POST['car_color'.$i2]))."' where mem_car_parking_id='".$_POST['mem_car_parking_id'.$i2]."' and  member_id='".$_POST['id']."'";
		$res4 = $this->m_dbConn->update($sql4);
		//echo '<br>';
		//echo $sql4;
		}
		################################################################## Member Car Update ##################################################################
		
		
		
		################################################################## Member Bike Update ##################################################################
		for($i3=1;$i3<=$_POST['tot_bike'];$i3++)
		{
		$sql5 = "update mem_bike_parking set parking_slot='".addslashes(trim($_POST['bike_parking_slot'.$i3]))."', bike_reg_no='".addslashes(trim($_POST['bike_reg_no'.$i3]))."', bike_owner='".addslashes(trim($_POST['bike_owner'.$i3]))."', bike_model='".addslashes(trim($_POST['bike_model'.$i3]))."', bike_make='".addslashes(trim($_POST['bike_make'.$i3]))."', bike_color='".addslashes(trim($_POST['bike_color'.$i3]))."' where mem_bike_parking_id='".$_POST['mem_bike_parking_id'.$i3]."' and member_id='".$_POST['id']."'";
		$res5 = $this->m_dbConn->update($sql5);
		//echo '<br>';
		//echo $sql5;
		}
		################################################################## Member Bike Update ##################################################################
		
		$sql6 = "UPDATE `unit` SET `share_certificate`='".$_POST['share_certificate']."',`share_certificate_from`='".$_POST['share_certificate_from']."',`share_certificate_to`='".$_POST['share_certificate_to']."' WHERE `unit_no` = '".$_POST['flat_no']."'";
		$res6 = $this->m_dbConn->update($sql6);		
	}
}
?>