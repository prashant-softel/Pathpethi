<?php
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");

class bg extends dbop
{
	public $actionPage = "../bg.php";
	public $m_dbConn;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		////dbop::__construct();
	}
	public function startProcess()
	{
		$errorExists=0;
		if($_REQUEST['insert']=='Insert' && $errorExists==0)
		{
			if($_POST['bg']<>"")
			{
				$sql = "select count(*)as cnt from bg where bg='".addslashes(trim(ucwords($_POST['bg'])))."' and status='Y'";
				$res = $this->m_dbConn->select($sql);
				
				if($res[0]['cnt']==0)
				{
					$insert_query="insert into bg (`bg`) values ('".addslashes(trim(ucwords($_POST['bg'])))."')";
					$data=$this->m_dbConn->insert($insert_query);
					return "Insert";
				}
				else
				{
					return "Already Exist";
				}
			}
			else
			{
				return "* Field should not be blank.";
			}
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			if($_POST['bg']<>"")
			{
				$up_query="update bg set `bg`='".addslashes(trim(ucwords($_POST['bg'])))."' where bg_id='".$_POST['id']."'";
				$data=$this->m_dbConn->update($up_query);
				return "Update";
			}
			else
			{
				return "* Field should not be blank.";
			}
		}
		else
		{
			return $errString;
		}
	}
	public function combobox($query)
	{
	}
	public function display1($rsas)
	{
			$thheader=array('Blood Group');
			$this->display_pg->edit="getbg";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="bg.php";
			$res=$this->display_pg->display_new($rsas);
			return $res;
	}
	public function pgnation()
	{
			$sql1="select bg_id,`bg` from bg where status='Y'";
			$cntr="select count(*) as cnt from bg where status='Y'";
			$this->display_pg->sql1=$sql1;
			$this->display_pg->cntr1=$cntr;
			$this->display_pg->mainpg="bg.php";
			$limit="10";
			$page=$_REQUEST['page'];
			$extra="";
			$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
			return $res;
	}
	public function selecting()
	{
			$sql1="select bg_id,`bg` from bg where bg_id='".$_REQUEST['bgId']."'";
			$var=$this->m_dbConn->select($sql1);
			return $var;
	}
	public function deleting()
	{
		$sql0 = "select count(*)as cnt from member_main where blood_group='".$_REQUEST['bgId']."' and status='Y'";
		$res0 = $this->m_dbConn->select($sql0);
		
		$sql00 = "select count(*)as cnt from mem_child_details where child_bg='".$_REQUEST['bgId']."' and status='Y'";
		$res00 = $this->m_dbConn->select($sql00);
		
		$sql000 = "select count(*)as cnt from mem_other_family where child_bg='".$_REQUEST['bgId']."' and status='Y'";
		$res000 = $this->m_dbConn->select($sql000);
		
		$sql0000 = "select count(*)as cnt from mem_spouse_details where spouse_bg='".$_REQUEST['bgId']."' and status='Y'";
		$res0000 = $this->m_dbConn->select($sql0000);
		
		if($res0[0]['cnt']==0 && $res00[0]['cnt']==0 && $res000[0]['cnt']==0 && $res0000[0]['cnt']==0)
		{
			$sql1="update bg set status='N' where bg_id='".$_REQUEST['bgId']."'";
			$this->m_dbConn->update($sql1);
			
			echo "msg1";
		}
		else
		{
			echo "msg";	
		}
	}
}
?>