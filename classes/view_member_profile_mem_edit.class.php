<?php if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once("dbconst.class.php");
include_once("changelog.class.php");
class view_member_profile_mem_edit extends dbop
{
	public $m_dbConn;
	
	private $m_sOwnerNames;
	
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		$m_sOwnerNames = "";
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
		$sql = "SELECT wing,unit_no,mm.owner_name,mm.primary_owner_name,mob,resd_no,off_no,dsg.desg,email,alt_email,dob,wed_any,bgg.bg,eme_rel_name,eme_contact_1,eme_contact_2,off_add,alt_mob,mm.parking_no,u.area,mm.profile,mm.publish_contact,mm.publish_profile FROM member_main as mm,bg as bgg,unit as u,wing as w,desg as dsg where mm.blood_group=bgg.bg_id and mm.unit=u.unit_id and u.wing_id=w.wing_id and mm.desg=dsg.desg_id and mm.status='Y' and bgg.status='Y' and u.status='Y' and w.status='Y' and dsg.status='Y' and mm.member_id='".$_GET['id']."' ";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	
	public function show_mem_other_family()
	{
		$sql = "select other_name,relation,other_dob,other_wed,coowner,dsg.desg,desg_id,ssc,bg,bgg.bg_id,msd.mem_other_family_id, msd.coowner, msd.other_profile, msd.other_mobile, msd.other_email,msd.other_publish_profile from mem_other_family as msd,bg as bgg,desg as dsg,member_main as membertbl where membertbl.member_id='".$_GET['id']."' and membertbl.member_id=msd.member_id and msd.child_bg=bgg.bg_id and msd.other_desg=dsg.desg_id and msd.status='Y' and bgg.status='Y' and dsg.status='Y'";
		//echo $sql;
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
	
	public function update_member_profile()
	{
		//echo '1';
		################################################################## Member Main Update ##################################################################

		$changeLogArray = array();

		$logSelect = "SELECT * from `member_main` WHERE member_id = '" . $_POST['id'] . "'";
		$resultLog = $this->m_dbConn->select($logSelect);
		//print_r($resultLog);
		$changeLogText['OLD_RECORD']['OWNER'] = explode('/', json_encode($resultLog));

		$logSelect = "SELECT * from `mem_other_family` WHERE member_id = '" . $_POST['id'] . "'";
		$resultLog = $this->m_dbConn->select($logSelect);
		$changeLogText['OLD_RECORD']['OTHER'] = explode('/', json_encode($resultLog));

		$logSelect = "SELECT * from `mem_car_parking` WHERE member_id = '" . $_POST['id'] . "'";
		$resultLog = $this->m_dbConn->select($logSelect);
		$changeLogText['OLD_RECORD']['CAR'] = explode('/', json_encode($resultLog));

		$logSelect = "SELECT * from `mem_bike_parking` WHERE member_id = '" . $_POST['id'] . "'";
		$resultLog = $this->m_dbConn->select($logSelect);
		$changeLogText['OLD_RECORD']['BIKE'] = explode('/', json_encode($resultLog));

		$changeLogText = str_replace('\"', '', json_encode($changeLogText));

		$objLog = new changeLog($this->m_dbConn);
		$iLatestChangeID = $objLog->setLog($changeLogText, $_SESSION['login_id'], 'member', $_POST['id']);

		//$m_sOwnerNames .= addslashes(trim($_POST['primary_owner_name']));

		//$sql = "update member_main set primary_owner_name='".addslashes(trim($_POST['primary_owner_name']))."', resd_no='".addslashes(trim($_POST['resd_no']))."', mob='".addslashes(trim($_POST['mob']))."', alt_mob='".addslashes(trim($_POST['alt_mob']))."', off_no='".addslashes(trim($_POST['off_no']))."', off_add='".addslashes(trim($_POST['off_add']))."', desg='".addslashes(trim($_POST['desg']))."', email='".addslashes(trim($_POST['email']))."', alt_email='".addslashes(trim($_POST['alt_email']))."', dob='".addslashes(trim($_POST['dob']))."', wed_any='".addslashes(trim($_POST['wed_any']))."', blood_group='".addslashes(trim($_POST['bg']))."', eme_rel_name='".addslashes(trim($_POST['eme_rel_name']))."', eme_contact_1='".addslashes(trim($_POST['eme_contact_1']))."', eme_contact_2='".addslashes(trim($_POST['eme_contact_2']))."', profile='".addslashes(trim($_POST['profile']))."', publish_contact='".addslashes(trim($_POST['publish_contact']))."', publish_profile='".addslashes(trim($_POST['publish_profile']))."' where member_id='".$_POST['id']."' ";

		if($_SESSION['role'] == ROLE_SUPER_ADMIN)
		{
			$sql = "update member_main set owner_name='".addslashes(trim($_POST['owner_name']))."', resd_no='".addslashes(trim($_POST['resd_no']))."', mob='".addslashes(trim($_POST['mob']))."', alt_mob='".addslashes(trim($_POST['alt_mob']))."', email='".addslashes(trim($_POST['email']))."', alt_email='".addslashes(trim($_POST['alt_email']))."', eme_rel_name='".addslashes(trim($_POST['eme_rel_name']))."', eme_contact_1='".addslashes(trim($_POST['eme_contact_1']))."', eme_contact_2='".addslashes(trim($_POST['eme_contact_2']))."', owner_gstin_no='".addslashes(trim($_POST['owner_gstin_no']))."' where member_id='".$_POST['id']."' ";
		}
		else
		{
			$sql = "update member_main set owner_name='".addslashes(trim($_POST['owner_name']))."', resd_no='".addslashes(trim($_POST['resd_no']))."', mob='".addslashes(trim($_POST['mob']))."', alt_mob='".addslashes(trim($_POST['alt_mob']))."', email='".addslashes(trim($_POST['email']))."', alt_email='".addslashes(trim($_POST['alt_email']))."', eme_rel_name='".addslashes(trim($_POST['eme_rel_name']))."', eme_contact_1='".addslashes(trim($_POST['eme_contact_1']))."', eme_contact_2='".addslashes(trim($_POST['eme_contact_2']))."', owner_gstin_no='".addslashes(trim($_POST['owner_gstin_no']))."' where member_id='".$_POST['id']."' ";
			//$sql = "update member_main set resd_no='".addslashes(trim($_POST['resd_no']))."', mob='".addslashes(trim($_POST['mob']))."', alt_mob='".addslashes(trim($_POST['alt_mob']))."', email='".addslashes(trim($_POST['email']))."', alt_email='".addslashes(trim($_POST['alt_email']))."', eme_rel_name='".addslashes(trim($_POST['eme_rel_name']))."', eme_contact_1='".addslashes(trim($_POST['eme_contact_1']))."', eme_contact_2='".addslashes(trim($_POST['eme_contact_2']))."' where member_id='".$_POST['id']."' ";	
		}
		
		//echo $sql;
		
		$res = $this->m_dbConn->update($sql);
		
		
		################################################################## Member Main Update ##################################################################
				
		################################################################## Member Other Update ##################################################################
		for($i1=1;$i1<=$_POST['tot_other'];$i1++)
		{
			if($_POST['delete'.$i1] == 1)
			{
				$sql3 = "Update `mem_other_family` SET `status` = 'N' WHERE mem_other_family_id ='".$_POST['mem_other_family_id'.$i1]."' and member_id='".$_POST['id']."'";
				$res3 = $this->m_dbConn->update($sql3);
			}
			else
			{
				$sql3 = "update mem_other_family set other_name='".addslashes(trim($_POST['other_name'.$i1]))."', relation='".addslashes(trim($_POST['relation'.$i1]))."', other_dob='".getDBFormatDate(addslashes(trim($_POST['other_dob'.$i1])))."', other_wed='".getDBFormatDate(addslashes(trim($_POST['other_wed'.$i1])))."', other_desg='".addslashes(trim($_POST['other_desg'.$i1]))."', ssc='".addslashes(trim($_POST['ssc_other'.$i1]))."', child_bg='".addslashes(trim($_POST['other_bg'.$i1]))."', other_mobile='".addslashes(trim($_POST['other_mobile'.$i1]))."', other_email='".addslashes(trim($_POST['other_email'.$i1]))."', other_profile='".addslashes(trim($_POST['other_profile'.$i1]))."', other_publish_contact='".addslashes(trim($_POST['other_publish_contact'.$i1]))."', other_publish_profile='".addslashes(trim($_POST['other_publish_profile'.$i1]))."', coowner='".addslashes(trim($_POST['coowner'.$i1]))."', send_commu_emails='".addslashes(trim($_POST['Send_commu_emails'.$i1]))."' where mem_other_family_id ='".$_POST['mem_other_family_id'.$i1]."' and member_id='".$_POST['id']."'";
				$res3 = $this->m_dbConn->update($sql3);
				
				$sql_mem  = "select publish_contact from `member_main` where `member_id` ='".$_POST['id']."'";
				$res_mem = $this->m_dbConn->select($sql_mem);
				
				if($res_mem[0]['publish_contact'] == "1")
				{
					$sql_upd = "update mem_other_family as other_family set other_family.other_publish_contact='1' where  other_family.member_id='".$_POST['id']."'";
				//echo $sql_upd;
					$res_mem_update = $this->m_dbConn->update($sql_upd);
				}
				if($_POST['coowner'.$i1] == 1)
				{
					$update_mem_main_qry  = "update `member_main` set `email` = '".addslashes(trim($_POST['other_email'.$i1])) ."',mob ='".addslashes(trim($_POST['other_mobile'.$i1]))."' where  member_id='".$_POST['id']."'";
					//echo $update_mem_main_qry;
					
					$res3 = $this->m_dbConn->update($update_mem_main_qry);
				}

				if($_POST['coowner'.$i1] == 1 || $_POST['coowner'.$i1] == 2)
				{
					if($m_sOwnerNames == '')
					{
						$m_sOwnerNames = addslashes(trim($_POST['other_name'.$i1]));
					}
					else
					{
						$m_sOwnerNames .= ' & ' . addslashes(trim($_POST['other_name'.$i1]));	
					}
				}
			}
		}

		################################################################## Member Other Update ##################################################################
		
		//$sqlOwnerUpdate = "update `member_main` SET owner_name = '" . $m_sOwnerNames . "' WHERE member_id='".$_POST['id']."'";
		//$resOwners = $this->m_dbConn->update($sqlOwnerUpdate);

		################################################################## Member Car Update ##################################################################
		for($i2=1;$i2<=$_POST['tot_car'];$i2++)
		{
			if($_POST['car_delete'.$i2] == 1)
			{
				$sql4 = "UPDATE `mem_car_parking` SET `status` = 'N' where mem_car_parking_id='".$_POST['mem_car_parking_id'.$i2]."' and  member_id='".$_POST['id']."'";
				$res4 = $this->m_dbConn->update($sql4);
			}
			else
			{
				$sql4 = "update mem_car_parking set parking_slot='".addslashes(trim($_POST['parking_slot'.$i2]))."', car_reg_no='".addslashes(trim($_POST['car_reg_no'.$i2]))."', car_owner='".addslashes(trim($_POST['car_owner'.$i2]))."', car_model='".addslashes(trim($_POST['car_model'.$i2]))."', car_make='".addslashes(trim($_POST['car_make'.$i2]))."',  car_color='".addslashes(trim($_POST['car_color'.$i2]))."',  parking_sticker='".addslashes(trim($_POST['parking_sticker'.$i2]))."' where mem_car_parking_id='".$_POST['mem_car_parking_id'.$i2]."' and  member_id='".$_POST['id']."'";
				$res4 = $this->m_dbConn->update($sql4);
			}
		//echo '<br>';
		//echo '6';
		//echo  $sql4;
		}
		################################################################## Member Car Update ##################################################################
		
		
		
		################################################################## Member Bike Update ##################################################################
		for($i3=1;$i3<=$_POST['tot_bike'];$i3++)
		{
			if($_POST['bike_delete'.$i3] == 1)
			{
				$sql5 = "UPDATE `mem_bike_parking` SET `status` = 'N' where mem_bike_parking_id='".$_POST['mem_bike_parking_id'.$i3]."' and  member_id='".$_POST['id']."'";
				$res5 = $this->m_dbConn->update($sql5);
			}
			else
			{
				$sql5 = "update mem_bike_parking set parking_slot='".addslashes(trim($_POST['bike_parking_slot'.$i3]))."', bike_reg_no='".addslashes(trim($_POST['bike_reg_no'.$i3]))."', bike_owner='".addslashes(trim($_POST['bike_owner'.$i3]))."', bike_model='".addslashes(trim($_POST['bike_model'.$i3]))."', bike_make='".addslashes(trim($_POST['bike_make'.$i3]))."', bike_color='".addslashes(trim($_POST['bike_color'.$i3]))."', parking_sticker='".addslashes(trim($_POST['bike_parking_sticker'.$i3]))."' where mem_bike_parking_id='".$_POST['mem_bike_parking_id'.$i3]."' and member_id='".$_POST['id']."'";
				$res5 = $this->m_dbConn->update($sql5);
			}
		//echo '<br>';
		//echo '7';
		//echo $sql5;
		}
		################################################################## Member Bike Update ##################################################################
		
	}
}
?>