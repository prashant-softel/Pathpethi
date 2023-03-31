<?php if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once("dbconst.class.php");
include_once("include/dbop.class.php");
$dbconn = new dbop();

//include_once("adduser.class.php");
//include_once("initialize.class.php");
include_once("utility.class.php");


class mem_other_family extends dbop
{
	public $actionPage = "../mem_other_family_new.php";
	public $m_dbConn;
	//public $obj_addduser;
	//public $obj_initialize;
	public $m_dbconnRoot;
	public $obj_activation ;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbconnRoot = new dbop(true);
$this->display_pg=new display_table($this->m_dbConn);
		//$this->obj_addduser = new adduser($m_dbconnRoot,$this->m_dbConn);
		//$this->obj_initialize = new initialize($m_dbconnRoot);
		$this->obj_activation = new activation($this->m_dbConn, $this->m_dbconnRoot);
		//dbop::__construct();
	}
	public function startProcess()
	{
		$errorExists=0;
		if($_REQUEST['insert']=='Add More' || $_REQUEST['insert']=='Add' && $errorExists==0)
		{
			if($_POST['member_id']<>"" && $_POST['other_name']<>"" )
			{
				$sql = "select count(*)as cnt from mem_other_family where member_id='".$_POST['member_id']."' and status='Y'";
				$res = $this->m_dbConn->select($sql);
				//echo "trace";
				$role = ROLE_MEMBER;
				//echo "trace".$role;
				
				if($_POST['coowner'] == 1)
				{
					$_POST['other_send_commu_emails'] = 1;
				}
				
				$insert_query="insert into mem_other_family (`member_id`,`other_name`,`relation`,`other_dob`,`other_desg`,`ssc`,`child_bg`,`other_wed`,`other_profile`,`other_publish_contact`,`other_publish_profile`, `other_mobile`, `other_email`,`coowner`,`send_act_email`,`send_commu_emails`) values ('".$_POST['member_id']."','".addslashes(trim(ucwords($_POST['other_name'])))."','".addslashes(trim(ucwords($_POST['relation'])))."','".$_POST['other_dob']."','".$_POST['other_desg']."','".addslashes(trim(ucwords($_POST['ssc'])))."','".$_POST['child_bg']."','".$_POST['other_wed']."','".$_POST['other_profile']."','".$_POST['other_publish_contact']."','".$_POST['other_publish_profile']."','".$_POST['other_mobile']."','".$_POST['other_email']."','".$_POST['coowner']."','".$_POST['chkCreateLogin']."','".$_POST['other_send_commu_emails']."')";
				$data = $this->m_dbConn->insert($insert_query);

				if($_POST['coowner'] == 1 || $_POST['coowner'] == 2)
				{
					$UpdateOwnerSql = "Update `member_main` Set `owner_name` = concat(`owner_name`, concat(' & ', '" . addslashes(trim(ucwords($_POST['other_name']))) . "')) WHERE `member_id` = '" . $_POST['member_id'] . "'";
					$ResultUpdateOwner = $this->m_dbConn->update($UpdateOwnerSql);
				}

				if($_POST['chkCreateLogin'] == "1")
				{
					//echo "chk".$_POST['chkCreateLogin'];
					//die();
					$unit_id  = $_POST["txtUnitID"];
					$code  = $_POST["Code"];
					$society_id = $_SESSION['society_id'];
					$NewUserEmailID = $_REQUEST['other_email'];
					
					$ActivationStatus = $this->obj_activation->AddMappingAndSendActivationEmail($role, $unit_id, $society_id, $code, $NewUserEmailID, $_POST['other_name']);
					echo "status:".$ActivationStatus;
					//die();
					if($ActivationStatus != "Success")
					{				
						return "Unable to Send Activation Email.";
					}
				}

				/*if($_POST['coowner'] == 1)
				{
					$sqlOwner = "select owner_name from member_main where member_id='".$_POST['member_id']."'";
					$resOwner = $this->m_dbConn->select($sqlOwner);

					$sOwnerNames = $resOwner[0]['owner_name'] . ' & ' . addslashes(trim(ucwords($_POST['other_name'])));

					$sqlOwner = "Update `member_main` SET owner_name = '" . $sOwnerNames . "' WHERE `member_id` = '" . $_POST['member_id'] . "'";
					$resOwner = $this->m_dbConn->update($sqlOwner);
				}*/
				
				if(isset($_SESSION['role']) && $_SESSION['role']==ROLE_MEMBER)
				{
					?>
					<script>window.location.href = '../view_member_profile.php?prf&id=<?php echo $_POST['member_id'];?>';</script>
				<?php
                }
				else
				{
				  if($_POST['mkm']=='mkm')
					{
					?>
					<script>window.location.href = '../view_member_profile.php?scm&id=<?php echo $_POST['member_id'];?>&tikon=<?php echo time();?>';</script>
					<?php
					}
					else if($_POST['mrs']=='mrs')
					{
					?>
					<script>window.location.href = '../mem_rem_data.php?scm';</script>
					<?php	
					}
					else
					{
					return "Insert";
					}
				}
			}
			else
			{
				return "Some * field is missing";
			}	
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			if($_POST['member_id']<>"" && $_POST['other_name']<>"" && $_POST['relation']<>"" && $_POST['other_desg']<>"" && $_POST['child_bg']<>"" )
			{	
				$up_query="update mem_other_family set `member_id`='".$_POST['member_id']."',`other_name`='".addslashes(trim(ucwords($_POST['other_name'])))."',`relation`='".addslashes(trim(ucwords($_POST['relation'])))."',`other_dob`='".$_POST['other_dob']."',`other_desg`='".$_POST['other_desg']."',`ssc`='".addslashes(trim(ucwords($_POST['ssc'])))."',`child_bg`='".$_POST['child_bg']."',`send_act_email`='".$_POST['chkCreateLogin']."',`send_commu_emails`='".$_POST['other_send_commu_emails']."' where mem_other_family_id='".$_POST['id']."'";
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
			$thheader=array('Member Name','Other Family Member','Relation with owner','Date of Birth','Occupation','School/College/Company','Blood Group');
			$this->display_pg->edit="getmem_other_family";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="mem_other_family.php";
			$res=$this->display_pg->display_new($rsas);
			return $res;
	}
	public function pgnation()
	{
		$sql1 = "select mof.mem_other_family_id,mm.owner_name,mof.other_name,mof.relation,mof.other_dob,dsg.desg,mof.ssc,bg.bg 
				 from mem_other_family as mof, member_main as mm, bg as bg, desg as dsg
				 where mof.status='Y' and mm.status='Y'  and bg.status='Y' and dsg.status='Y' 
				 and mof.member_id=mm.member_id and mof.other_desg=dsg.desg_id and mof.child_bg=bg.bg_id
				 ";
		
		$cntr = "select count(*) as cnt 
				 from mem_other_family as mof, member_main as mm, bg as bg, desg as dsg
				 where mof.status='Y' and mm.status='Y'  and bg.status='Y' and dsg.status='Y' 
				 and mof.member_id=mm.member_id and mof.other_desg=dsg.desg_id and mof.child_bg=bg.bg_id
				 ";
		
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$this->display_pg->mainpg="mem_other_family.php";
		$limit="5";
		$page=$_REQUEST['page'];
		$extra="";
		$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
	}
	public function selecting()
	{
			$sql1="select mem_other_family_id,`member_id`,`other_name`,`relation`,`other_dob`,`other_desg`,`ssc`,`child_bg` from mem_other_family where mem_other_family_id='".$_REQUEST['mem_other_familyId']."'";
			$var=$this->m_dbConn->select($sql1);
			return $var;
	}
	public function deleting()
	{
			$sql1="update mem_other_family set status='N' where mem_other_family_id='".$_REQUEST['mem_other_familyId']."'";
			$this->m_dbConn->update($sql1);
	}
	
	public function owner_name($member_id)
	{
		$sql = "select * from member_main where member_id='".$member_id."' and status='Y'";
		$res = $this->m_dbConn->select($sql);	
		echo $res[0]['owner_name'];
		
		$_SESSION['owner_id'] = $member_id;
		$_SESSION['owner_name'] = $res[0]['owner_name'];
	}

	public function unit_details($member_id)
	{
		$result = array();
		$sql = "select u.unit_no, m.unit from unit as u JOIN member_main as m ON m.unit = u.unit_id where m.member_id='".$member_id."' and u.status='Y'";
		$res = $this->m_dbConn->select($sql);	
				
		if($res != '')
		{
			$result['unit_no'] = $res[0]['unit_no'];
			$result['unit_id'] = $res[0]['unit'];
		}

		return $result;
	}
	
	public function get_society_details($society_id)
	{
		$sql01 = "select society_name, society_add from society where society_id = ".$society_id;
		$sql11 = $this->m_dbConn->select($sql01);
		
		return $sql11;
	}
}
?>