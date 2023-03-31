<?php
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");

class occupation extends dbop
{
	public $actionPage = "../occupation.php";
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
			if($_POST['occupation']<>"")
			{
				$sql = "select count(*)as cnt from occupation where occupation='".addslashes(trim(ucwords($_POST['occupation'])))."' and status='Y'";
				$res = $this->m_dbConn->select($sql);
				
				if($res[0]['cnt']==0)
				{
					$insert_query="insert into occupation (`occupation`) values ('".addslashes(trim(ucwords($_POST['occupation'])))."')";
					$data=$this->m_dbConn->insert($insert_query);
					return "Insert";
				}
				else
				{
					return "Already Exist this Occupation";
				}
			}
			else
			{
				return "Occupation Should not be blank.";
			}	
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			if($_POST['occupation']<>"")
			{
				$up_query="update occupation set `occupation`='".addslashes(trim(ucwords($_POST['occupation'])))."' where occupation_id='".$_POST['id']."'";
				$data=$this->m_dbConn->update($up_query);
				return "Update";
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
			$thheader=array('Occupation');
			$this->display_pg->edit="getoccupation";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="occupation.php";
			$res=$this->display_pg->display_new($rsas);
			return $res;
	}
	public function pgnation()
	{
			$sql1="select occupation_id,`occupation` from occupation where status='Y'";
			$cntr="select count(*) as cnt from occupation where status='Y'";
			$this->display_pg->sql1=$sql1;
			$this->display_pg->cntr1=$cntr;
			$this->display_pg->mainpg="occupation.php";
			$limit="5";
			$page=$_REQUEST['page'];
			$extra="";
			$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
			return $res;
	}
	public function selecting()
	{
			$sql1="select occupation_id,`occupation` from occupation where occupation_id='".$_REQUEST['occupationId']."'";
			$var=$this->m_dbConn->select($sql1);
			return $var;
	}
	public function deleting()
	{
			$sql1="update occupation set status='N' where occupation_id='".$_REQUEST['occupationId']."'";
			$this->m_dbConn->update($sql1);
	}
}
?>