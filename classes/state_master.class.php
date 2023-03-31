<?php
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");

class state_master extends dbop
{
	public $actionPage = "../state_master.php";
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
			if($_POST['state']<>"")
			{
				$sql = "select count(*)as cnt from state_master where state='".addslashes(trim(ucwords($_POST['state'])))."' and status='Y'";
				$res = $this->m_dbConn->select($sql);
				
				if($res[0]['cnt']==0)
				{
					$insert_query="insert into state_master (`country_id`,`state`) values ('102','".addslashes(trim(ucwords($_POST['state'])))."')";
					$data=$this->m_dbConn->insert($insert_query);
					return "Insert";
				}
				else
				{
					return "Already exist this state";
				}
			}
			else
			{
				return "State should not be blank.";
			}
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			if($_POST['state']<>"")
			{
				$up_query="update state_master set `state`='".addslashes(trim(ucwords($_POST['state'])))."' where state_id='".$_POST['id']."'";
				$data=$this->m_dbConn->update($up_query);
				return "Update";
			}
			else
			{
				return "State should not be blank.";
			}
		}
		else
		{
			return $errString;
		}
	}
	public function display1($rsas)
	{
		$thheader=array('State');
		$this->display_pg->edit="getstate_master";
		$this->display_pg->th=$thheader;
		$this->display_pg->mainpg="state_master.php";
		$res=$this->display_pg->display_new($rsas);
		return $res;
	}
	public function pgnation()
	{
		$sql1="select state_id,`state` from state_master where status='Y' group by state order by state";
		$cntr="select count(*) as cnt from state_master where status='Y' group by state order by state";
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$this->display_pg->mainpg="state_master.php";
		$limit="10";
		$page=$_REQUEST['page'];
		$extra="";
		$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
	}
	public function selecting()
	{
		$sql1="select state_id,`state` from state_master where state_id='".$_REQUEST['state_masterId']."'";
		$var=$this->m_dbConn->select($sql1);
		return $var;
	}
	public function deleting()
	{
		$sql1="update state_master set status='N' where state_id='".$_REQUEST['state_masterId']."'";
		$this->m_dbConn->update($sql1);
	}
}
?>