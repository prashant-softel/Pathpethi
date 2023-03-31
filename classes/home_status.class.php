<?php
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");

class home_status extends dbop
{
	public $actionPage = "../home_status.php";
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
			if($_POST['status']<>"")
			{
				$sql = "select count(*)as cnt from home_status where hm_status='".addslashes(trim(ucwords($_POST['status'])))."' and status='Y'";
				$res = $this->m_dbConn->select($sql);
				
				if($res[0]['cnt']==0)
				{
					$insert_query="insert into home_status (`hm_status`) values ('".addslashes(trim(ucwords($_POST['status'])))."')";
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
			if($_POST['status']<>"")
			{
				$up_query="update home_status set `hm_status`='".$_POST['status']."' where status_id='".$_POST['id']."'";
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
			$thheader=array('status');
			$this->display_pg->edit="gethome_status";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="home_status.php";
			$res=$this->display_pg->display_new($rsas);
			return $res;
	}
	public function pgnation()
	{
			$sql1="select status_id,`hm_status` from home_status where status='Y'";
			$cntr="select count(*) as cnt from home_status where status='Y'";
			$this->display_pg->sql1=$sql1;
			$this->display_pg->cntr1=$cntr;
			$this->display_pg->mainpg="home_status.php";
			$limit="10";
			$page=$_REQUEST['page'];
			$extra="";
			$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
			return $res;
	}
	public function selecting()
	{
			$sql1="select status_id,`hm_status` from home_status where status_id='".$_REQUEST['home_statusId']."'";
			$var=$this->m_dbConn->select($sql1);
			return $var;
	}
	public function deleting()
	{
		$sql0 = "select count(*)as cnt from unit where status_id='".$_REQUEST['home_statusId']."' and status='Y'";
		$res0 = $this->m_dbConn->select($sql0);
		
		if($res0[0]['cnt']==0)
		{
			$sql1="update home_status set status='N' where status_id='".$_REQUEST['home_statusId']."'";
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