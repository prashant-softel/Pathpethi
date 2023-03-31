<?php if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");

class member_main_new1 extends dbop
{
	public $actionPage = "../member_main_new1.php?scm";
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
		if($_REQUEST['insert']=='Next' && $errorExists==0)
		{
			if($_POST['code']<>"")
			{
				$sql = "select count(*)as cnt from member_main where code='".$_POST['code']."' and status='Y'";
				$res = $this->m_dbConn->select($sql);
				
				if($res[0]['cnt']==0)
				{
					$sql1 = "select * from unit where rand_no='".$_POST['code']."' and status='Y'";
					$res1 = $this->m_dbConn->select($sql1);
					
					$society_id = $res1[0]['society_id'];
					$wing_id = $res1[0]['wing_id'];
					$unit_id = $res1[0]['unit_id'];
					
					$insert_query = "insert into member_main (`society_id`,`wing_id`,`unit`,`code`,`fbid`,`owner_name`,`resd_no`,`mob`,`alt_mob`,`off_no`,`off_add`,`desg`,`email`,`alt_email`,`dob`,`wed_any`,`blood_group`,`eme_rel_name`,`eme_contact_1`,`eme_contact_2`) values ('".$society_id."','".$wing_id."','".$unit_id."','".$_POST['code']."','".$_SESSION['fbid']."','".$this->m_dbConn->escapeString(trim(ucwords($_POST['owner_name'])))."','".$_POST['resd_no']."','".$_POST['mob']."','".$_POST['alt_mob']."','".$_POST['off_no']."','".$this->m_dbConn->escapeString(trim(ucwords($_POST['off_add'])))."','".$_POST['desg']."','".$this->m_dbConn->escapeString(trim($_POST['email']))."','".$this->m_dbConn->escapeString(trim($_POST['alt_email']))."','".$_POST['dob']."','".$_POST['wed_any']."','".$_POST['bg']."','".$this->m_dbConn->escapeString(trim(ucwords($_POST['eme_rel_name'])))."','".$_POST['eme_contact_1']."','".$_POST['eme_contact_2']."')";
					$data = $this->m_dbConn->insert($insert_query);
					
					$sql = "insert into login(`society_id`,`com_id`,`member_id`,`password`,`authority`,`name`)values('".$society_id."','".$data."','".$_POST['email']."','".$this->m_dbConn->escapeString($_POST['user_pass'])."','Self Member','".$this->m_dbConn->escapeString(trim(ucwords($_POST['owner_name'])))."')";
					$res = $this->m_dbConn->insert($sql);
					
					###################################################
					$_SESSION['owner_name'] = ucwords($_POST['owner_name']);
					$_SESSION['owner_id']   = $data;
					$_SESSION['member_id']  = $data;
					$_SESSION['com_id']     = $data;//$res; 
					$_SESSION['login_id']	= $res;
					###################################################
					$_SESSION['member_name'] = ucwords($_POST['owner_name']);
					$_SESSION['society_id'] = $society_id;
				
					$sql2 = "select * from society where society_id='".$_SESSION['society_id']."' and status='Y'";
					$res2 = $this->m_dbConn->select($sql2);
						
					$_SESSION['society_name'] = $res2[0]['society_name'];
					###################################################
					
					$_SESSION['set'] = 'set';
					
					?>
                    	<script>window.location.href = '../mem_spouse_details_new1.php?scm&tik_id=<?php echo time();?>&nok&sd';</script>
                    <?php
				}
				else
				{
					return "Already exist this code";
				}
			}
			else
			{
				return "Some * field is missing";
			}
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
		$res = $this->m_dbConn->select($sql);	
		echo $res[0]['cnt'];
	}
	
	function check_code()
	{
		$sql = "select count(*)as cnt from unit where rand_no = '".$_REQUEST['code']."' and status='Y'";
		$res = $this->m_dbConn->select($sql);	
		
		echo $res[0]['cnt'];
	}
	
	function check_code_exist()
	{
		$sql = "select count(*)as cnt from member_main where code='".$_REQUEST['code']."' and status='Y'";
		$res = $this->m_dbConn->select($sql);	
		
		echo $res[0]['cnt'];
	}
}
?>