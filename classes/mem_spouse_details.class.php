<?php if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once("dbconst.class.php");

class mem_spouse_details extends dbop
{
	public $actionPage = "../mem_spouse_details_new.php";
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
		if($_REQUEST['insert']=='Add' && $errorExists==0)
		{
			if($_POST['member_id']<>"" && $_POST['spouse_name']<>"" && $_POST['spouse_desg']> '0' && $_POST['spouse_bg'] > '0')
			{
				$sql = "select count(*)as cnt from mem_spouse_details where member_id='".$_POST['member_id']."' and status='Y'";
				$res = $this->m_dbConn->select($sql);
				
				if($res[0]['cnt']==0)
				{
					$insert_query="insert into mem_spouse_details (`member_id`,`spouse_name`,`spouse_desg`,`spouse_dob`,`spouse_off_add`,`spouse_off_no`,`spouse_bg`) values ('".$_POST['member_id']."','".addslashes(trim(ucwords($_POST['spouse_name'])))."','".$_POST['spouse_desg']."','".$_POST['spouse_dob']."','".addslashes(trim(ucwords($_POST['spouse_off_add'])))."','".$_POST['spouse_off_no']."','".$_POST['spouse_bg']."')";
					$data = $this->m_dbConn->insert($insert_query);
					
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
					<script>window.location.href = '../view_member_profile_adm.php?scm&id=<?php echo $_POST['member_id'];?>&tikon=<?php echo time();?>';</script>
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
					return "You cant add more than one.";
				}
			}
			else
			{
				return "Some * field is missing";
			}
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			if($_POST['member_id']<>"" && $_POST['spouse_name']<>"" && $_POST['spouse_desg']<>"" && $_POST['spouse_bg']<>"")
			{
				$up_query="update mem_spouse_details set `member_id`='".$_POST['member_id']."',`spouse_name`='".addslashes(trim(ucwords($_POST['spouse_name'])))."',`spouse_desg`='".$_POST['spouse_desg']."',`spouse_dob`='".$_POST['spouse_dob']."',`spouse_off_add`='".addslashes(trim(ucwords($_POST['spouse_off_add'])))."',`spouse_off_no`='".$_POST['spouse_off_no']."',`spouse_bg`='".$_POST['spouse_bg']."' where mem_spouse_details_id='".$_POST['id']."'";
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
			$str.="<option value='0'>Please Select</option>";
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
			$thheader=array('Main Member Name','Member Spouse Name','Occupation','Date of Birth','Office Address','Office Phone No.','Bloos Group');
			$this->display_pg->edit="getmem_spouse_details";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="mem_spouse_details.php";
			$res=$this->display_pg->display_new($rsas);
			return $res;
	}
	public function pgnation()
	{
		$sql1 = "select msd.mem_spouse_details_id,mm.owner_name,msd.spouse_name,dsg.desg,msd.spouse_dob,msd.spouse_off_add,msd.spouse_off_no,bg.bg 
				 from mem_spouse_details as msd, member_main as mm, bg as bg, desg as dsg
				 where msd.status='Y' and mm.status='Y'  and bg.status='Y' and dsg.status='Y' 
				 and msd.member_id=mm.member_id and msd.spouse_desg=dsg.desg_id and msd.spouse_bg=bg.bg_id
				 ";
		
		$cntr = "select count(*) as cnt 
				 from mem_spouse_details as msd, member_main as mm, bg as bg, desg as dsg
				 where msd.status='Y' and mm.status='Y'  and bg.status='Y' and dsg.status='Y' 
				 and msd.member_id=mm.member_id and msd.spouse_desg=dsg.desg_id and msd.spouse_bg=bg.bg_id
				 ";
		
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$this->display_pg->mainpg="mem_spouse_details.php";
		$limit="5";
		$page=$_REQUEST['page'];
		$extra="";
		$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
	}
	public function selecting()
	{
		$sql1="select mem_spouse_details_id,`member_id`,`spouse_name`,`spouse_desg`,`spouse_dob`,`spouse_off_add`,`spouse_off_no`,`spouse_bg` from mem_spouse_details where mem_spouse_details_id='".$_REQUEST['mem_spouse_detailsId']."'";
		$var=$this->m_dbConn->select($sql1);
		return $var;
	}
	public function deleting()
	{
		$sql1="update mem_spouse_details set status='N' where mem_spouse_details_id='".$_REQUEST['mem_spouse_detailsId']."'";
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
}
?>