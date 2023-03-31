<?php if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");

class mem_other_family_new1 extends dbop
{
	public $actionPage = "../mem_other_family_new1.php?od&idd=1678";
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
		if($_REQUEST['insert']=='Add More' || $_REQUEST['insert']=='Add' && $errorExists==0)
		{
			if($_POST['other_name']<>"" && $_POST['relation']<>"" && $_POST['other_desg']<>"" && $_POST['child_bg']<>"" )
			{
				$sql = "select count(*)as cnt from mem_other_family where member_id='".$_SESSION['member_id']."' and status='Y'";
				$res = $this->m_dbConn->select($sql);
				
				$insert_query = "insert into mem_other_family (`member_id`,`other_name`,`relation`,`other_dob`,`other_desg`,`ssc`,`child_bg`) values ('".$_SESSION['member_id']."','".addslashes(trim(ucwords($_POST['other_name'])))."','".addslashes(trim(ucwords($_POST['relation'])))."','".$_POST['other_dob']."','".$_POST['other_desg']."','".addslashes(trim(ucwords($_POST['ssc'])))."','".$_POST['child_bg']."')";
				$data = $this->m_dbConn->insert($insert_query);
				
				return "Insert";
				
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
}
?>