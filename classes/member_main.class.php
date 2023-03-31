<?php if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");

class member_main extends dbop
{
	public $actionPage = "../member_main_new.php?scm";
	public $m_dbConn;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		//dbop::__construct();
	}
	public function startProcess()
	{
		$errorExists=0;
		if($_REQUEST['insert']=='Insert' && $errorExists==0)
		{
			if($_POST['wing_id']<>"" && $_POST['unit']<>"" && $_POST['owner_name']<>"")
			{
				$sql = "select count(*)as cnt from member_main where wing_id='".$_POST['wing_id']."' and unit='".$_POST['unit']."' and status='Y'";
				$res = $this->m_dbConn->select($sql);
				
				if($res[0]['cnt']==0)
				{
					$insert_query = "insert into member_main (`society_id`,`wing_id`,`unit`,`owner_name`,`resd_no`,`mob`,`alt_mob`,`off_no`,`off_add`,`desg`,`email`,`alt_email`,`dob`,`wed_any`,`blood_group`,`eme_rel_name`,`eme_contact_1`,`eme_contact_2`) values ('".$_SESSION['society_id']."','".$_POST['wing_id']."','".$_POST['unit']."','".addslashes(trim(ucwords($_POST['owner_name'])))."','".$_POST['resd_no']."','".$_POST['mob']."','".$_POST['alt_mob']."','".$_POST['off_no']."','".addslashes(trim(ucwords($_POST['off_add'])))."','".$_POST['desg']."','".addslashes(trim($_POST['email']))."','".addslashes(trim($_POST['alt_email']))."','".$_POST['dob']."','".$_POST['wed_any']."','".$_POST['bg']."','".addslashes(trim(ucwords($_POST['eme_rel_name'])))."','".$_POST['eme_contact_1']."','".$_POST['eme_contact_2']."')";
					$data = $this->m_dbConn->insert($insert_query);
					
					//$sql = "insert into login(`society_id`,`com_id`,`member_id`,`password`,`authority`,`name`)values('".$_SESSION['society_id']."','".$data."','".$_POST['mem_user']."','".$_POST['mem_pass']."','".$_SESSION['login_id']."','".addslashes(trim(ucwords($_POST['owner_name'])))."')";
					//$res = $this->m_dbConn->insert($sql);
					
					###################################################
					$_SESSION['owner_name'] = ucwords($_POST['owner_name']);
					$_SESSION['owner_id']   = $data;
					###################################################
					
					?>
                    <script>window.location.href = '../mem_spouse_details_new.php?scm&tik_id=<?php echo time();?>&nok';</script>
                    <?php
					
					//return "Insert";
				}
				else
				{
					return "Already exist";
				}
			}
			else
			{
				//return "Some * field is missing";
				return "All * Field Required";	
			}
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			if($_POST['unit']<>"")
			{
				$up_query="update member_main set `unit`='".$_POST['unit']."',`owner_name`='".addslashes(trim(ucwords($_POST['owner_name'])))."',`resd_no`='".$_POST['resd_no']."',`mob`='".$_POST['mob']."',`alt_mob`='".$_POST['alt_mob']."',`off_no`='".$_POST['off_no']."',`off_add`='".addslashes(trim(ucwords($_POST['off_add'])))."',`desg`='".$_POST['desg']."',`email`='".addslashes(trim($_POST['email']))."',`alt_email`='".addslashes(trim($_POST['alt_email']))."',`dob`='".$_POST['dob']."',`wed_any`='".$_POST['wed_any']."',`blood_group`='".$_POST['bg']."', `eme_rel_name`='".addslashes(trim(ucwords($_POST['eme_rel_name'])))."', `eme_contact_1`='".$_POST['eme_contact_1']."', `eme_contact_2`='".$_POST['eme_contact_2']."' where member_id='".$_POST['id']."'";
				$data=$this->m_dbConn->update($up_query);
				return "Update";
			}
			else
			{
				return "Some * field is missing";
			}
		}
		else
		{
			return $errString;
		}
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
	public function display1($rsas)
	{
			$thheader=array('Unit No.','Owner Name','Resd Phone No.','Mobile','Office No','Office Address','Designation','Email-id','Date of Birth','Wedd Anny Date','Blood Group','Close Relative','Emergency Contact 1','Emergency Contact 2');
			
			$thheader=array('Unit No.','Owner Name','Resd Phone No.','Mobile','Office No','Office Address','Designation','Email-id','Date of Birth','Wedd Anny Date','Close Relative','Emergency Contact 1','Emergency Contact 2');
			
			$this->display_pg->edit="getmember_main";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="member_main.php";
			$res=$this->display_pg->display_new($rsas);
			return $res;
	}
	public function pgnation1()
	{
		/*
		$sql1 = "select mm.member_id,un.unit_no,mm.owner_name,mm.resd_no,Concat_ws('\n',mm.mob,mm.alt_mob),mm.off_no,mm.off_add,dsg.desg,Concat_ws('\n',mm.email,
				 mm.alt_email),mm.dob,mm.wed_any,bg.bg,mm.eme_rel_name,mm.eme_contact_1,mm.eme_contact_2 from 
				 member_main as mm, unit as un, desg as dsg, bg as bg where 
				 mm.status='Y' and un.status='Y' and dsg.status='Y' and bg.status='Y' and 
				 mm.unit=un.unit_id and mm.desg=dsg.desg_id and mm.blood_group=bg.bg_id
				 ";	
				 */
		/*		 
		$sql1 = "select mm.member_id,un.unit_no,mm.owner_name,mm.resd_no,mm.mob,mm.off_no,mm.off_add,dsg.desg,mm.email,mm.dob,mm.wed_any,bgg.bg,mm.eme_rel_name,mm.eme_contact_1,mm.eme_contact_2 from member_main as mm, unit as un, desg as dsg, bg as bgg where mm.status='Y' and un.status='Y' and dsg.status='Y' and bgg.status='Y' and mm.unit=un.unit_id and mm.desg=dsg.desg_id and mm.blood_group=bgg.bg_id";			 	 
		
		$cntr = "select count(*) as cnt from member_main as mm, unit as un, desg as dsg, bg as bgg where mm.status='Y' and un.status='Y' and dsg.status='Y' and bgg.status='Y' and mm.unit=un.unit_id and mm.desg=dsg.desg_id and mm.blood_group=bgg.bg_id";
		*/
		$sql1 = "select mm.member_id,un.unit_no,mm.owner_name,mm.resd_no,mm.mob,mm.off_no,mm.off_add,dsg.desg,mm.email,mm.dob,mm.wed_any,mm.eme_rel_name,mm.eme_contact_1,mm.eme_contact_2 from member_main as mm, unit as un, desg as dsg where mm.status='Y' and un.status='Y' and dsg.status='Y' and mm.unit=un.unit_id and mm.desg=dsg.desg_id";			 	 
		
		$cntr = "select count(*) as cnt from member_main as mm, unit as un, desg as dsg where mm.status='Y' and un.status='Y' and dsg.status='Y' and mm.unit=un.unit_id and mm.desg=dsg.desg_id";
		
		
		
		$this->display_pg->sql1		=	$sql1;
		$this->display_pg->cntr1	=	$cntr;
		$this->display_pg->mainpg	=	"member_main.php";
		
		$limit 	= "10";
		$page 	= $_REQUEST['page'];
		$extra 	= "";
		
		$res 	= $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
	}
	
	public function selecting()
	{
		$sql1 = "select member_id,`unit`,`owner_name`,`resd_no`,`mob`,`alt_mob`,`off_no`,`off_add`,`desg`,`email`,`alt_email`,`dob`,`wed_any`,`blood_group`,eme_rel_name,eme_contact_1,eme_contact_2 from member_main where member_id='".$_REQUEST['member_mainId']."'";
		$var=$this->m_dbConn->select($sql1);
		return $var;
	}
	public function deleting()
	{
		$sql0 = "select count(*)as cnt from mem_spouse_details where member_id='".$_REQUEST['member_mainId']."' and status='Y'";
		$res0 = $this->m_dbConn->select($sql0);
		
		$sql00 = "select count(*)as cnt from mem_child_details where member_id='".$_REQUEST['member_mainId']."' and status='Y'";
		$res00 = $this->m_dbConn->select($sql00);
		
		$sql000 = "select count(*)as cnt from mem_other_family where member_id='".$_REQUEST['member_mainId']."' and status='Y'";
		$res000 = $this->m_dbConn->select($sql000);
		
		$sql0000 = "select count(*)as cnt from mem_bike_parking where member_id='".$_REQUEST['member_mainId']."' and status='Y'";
		$res0000 = $this->m_dbConn->select($sql0000);
		
		$sql0000 = "select count(*)as cnt from mem_car_parking where member_id='".$_REQUEST['member_mainId']."' and status='Y'";
		$res0000 = $this->m_dbConn->select($sql0000);
		
		
		if($res0[0]['cnt']==0 && $res00[0]['cnt']==0 && $res000[0]['cnt']==0 && $res0000[0]['cnt']==0)
		{
			$sql1="update member_main set status='N' where member_id='".$_REQUEST['member_mainId']."'";
			$this->m_dbConn->update($sql1);
			
			echo "msg1";
		}
		else
		{
			echo "msg";	
		}		
	}
	public function check_record()
	{
		$sql = "select count(*)as cnt from member_main where unit='".$_REQUEST['unit_id']."' and status='Y'";
		//echo $sql;
		$res = $this->m_dbConn->select($sql);	
		echo $res[0]['cnt'];
	}
	
	public function get_unit()
	{
		//if($_REQUEST['wing'])
		//{
		$sql = "select * from unit where status='Y' and wing_id='".$_REQUEST['wing']."' ORDER BY `sort_order`";
		$res = $this->m_dbConn->select($sql);	
			
			if($res<>"")
			{
				$i=0;
				foreach($res as $k => $v)
				{
				 echo $res[$k]['unit_id']."#".$res[$k]['unit_no']."###";
				 $i++;
				}
				echo "****".$i;
			}
			else
			{
				echo ""."#"."0";
				echo "****"."0";
			}
		//}	
	}
	
	public function get_unit_new()
	{
		$sql = '';
		if($_REQUEST['wing_id'] == 0)
		{
			$sql = "select unittbl.unit_id, unittbl.unit_no, unittbl.area, wingtbl.wing from unit as unittbl JOIN wing as wingtbl ON unittbl.wing_id = wingtbl.wing_id where unittbl.status='Y' and unittbl.society_id='".$_SESSION['society_id']."' ORDER BY unittbl.sort_order";			
		}
		else
		{
			$sql = "select unittbl.unit_id, unittbl.unit_no, unittbl.area, wingtbl.wing from unit as unittbl JOIN wing as wingtbl ON unittbl.wing_id = wingtbl.wing_id where unittbl.status='Y' and unittbl.wing_id='".$_REQUEST['wing_id']."' ORDER BY unittbl.sort_order";
		}
		//echo $sql;
		$res = $this->m_dbConn->select($sql);	
		
		$ownerNameArr = array();
		$sqlQuery = "SELECT `unit`, `owner_name` FROM `member_main`";
		$ownerName = $this->m_dbConn->select($sqlQuery);
		for($i = 0; $i < sizeof($ownerName); $i++)
		{
			$ownerNameArr[$ownerName[$i]['unit']] = $ownerName[$i]['owner_name'];	
		}
		
		if($res<>"")
		{
			$aryResult = array();
			array_push($aryResult,array('success'=>'0'));
			foreach($res as $k => $v)
			{
			 	$show_dtl = array("id"=>$res[$k]['unit_id'], "unit"=>$res[$k]['unit_no']." ".$ownerNameArr[$res[$k]['unit_id']], "wing"=>$res[$k]['wing'], "area"=>$res[$k]['area']);
				array_push($aryResult,$show_dtl);
			}
			echo json_encode($aryResult);
		}
		else
		{
			echo json_encode(array(array("success"=>1), array("message"=>'No Data To Display')));
		}
	}
	
	public function check_username()
	{
		$sql = "select count(*)as cnt from login where status='Y' and member_id='".$_REQUEST['email']."'";
		$res = $this->m_dbConn->select($sql);		
		
		echo $res[0]['cnt'];
	}
	
	public function get_year()
	{
		$sql = "select * from year where status='Y' ORDER BY YearID DESC";
		$res = $this->m_dbConn->select($sql);	
		
		if($res<>"")
		{
			$aryResult = array();
			foreach($res as $k => $v)
			{
			 	$show_dtl = array("id"=>$res[$k]['YearID'], "year"=>$res[$k]['YearDescription']);
				array_push($aryResult,$show_dtl);
			}
			echo json_encode($aryResult);
		}
		else
		{
			echo json_encode(array(array("success"=>1), array("message"=>'No Data To Display')));
		}
	}
	
	public function get_period()
	{
		$sql = "select * from period where status='Y' and YearID='" . $_REQUEST['year'] . "'";
		$res = $this->m_dbConn->select($sql);	
		
		if($res<>"")
		{
			$aryResult = array();
			array_push($aryResult,array('success'=>'0'));
			foreach($res as $k => $v)
			{
			 	$show_dtl = array("id"=>$res[$k]['ID'], "period"=>$res[$k]['Type']);
				array_push($aryResult,$show_dtl);
			}
			echo json_encode($aryResult);
		}
		else
		{
			echo json_encode(array(array("success"=>1), array("message"=>'No Data To Display')));
		}
	}
}
?>