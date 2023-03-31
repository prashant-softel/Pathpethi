<?php
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");

class desg extends dbop
{
	public $actionPage = "../desg.php";
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
			if($_POST['desg']<>"")
			{
				$sql = "select count(*)as cnt from desg where desg='".addslashes(trim(ucwords($_POST['desg'])))."' and status='Y'";
				$res = $this->m_dbConn->select($sql);
				
				if($res[0]['cnt']==0)
				{	
					$insert_query="insert into desg (`desg`) values ('".addslashes(trim(ucwords($_POST['desg'])))."')";
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
			if($_POST['desg']<>"")
			{	
				$up_query="update desg set `desg`='".addslashes(trim(ucwords($_POST['desg'])))."' where desg_id='".$_POST['id']."'";
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
			$thheader=array('Designation');
			$this->display_pg->edit="getdesg";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="desg.php";
			$res=$this->display_pg->display_new($rsas);
			return $res;
	}
	public function pgnation()
	{
			$sql1="select desg_id,`desg` from desg where status='Y' order by desg";
			$cntr="select count(*) as cnt from desg where status='Y'";
			/*$this->display_pg->sql1=$sql1;
			$this->display_pg->cntr1=$cntr;
			$this->display_pg->mainpg="desg.php";
			$limit="10";
			$page=$_REQUEST['page'];
			$extra="";
			$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
			return $res;*/
			$result = $this->m_dbConn->select($sql1);
			$thheader=array('Designation');
			$this->display_pg->edit="getdesg";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="desg.php";
			$res = $this->display_pg->display_datatable($result);
	}
	public function selecting()
	{
			$sql1="select desg_id,`desg` from desg where desg_id='".$_REQUEST['desgId']."'";
			$var=$this->m_dbConn->select($sql1);
			return $var;
	}
	public function deleting()
	{
		$sql0 = "select count(*)as cnt from member_main where desg='".$_REQUEST['desgId']."' and status='Y'";
		$res0 = $this->m_dbConn->select($sql0);
		
		$sql00 = "select count(*)as cnt from mem_child_details where child_desg='".$_REQUEST['desgId']."' and status='Y'";
		$res00 = $this->m_dbConn->select($sql00);
		
		$sql000 = "select count(*)as cnt from mem_other_family where other_desg='".$_REQUEST['desgId']."' and status='Y'";
		$res000 = $this->m_dbConn->select($sql000);
		
		$sql0000 = "select count(*)as cnt from mem_spouse_details where spouse_desg='".$_REQUEST['desgId']."' and status='Y'";
		$res0000 = $this->m_dbConn->select($sql0000);
		
		if($res0[0]['cnt']==0 && $res00[0]['cnt']==0 && $res000[0]['cnt']==0 && $res0000[0]['cnt']==0)
		{
			$sql1="update desg set status='N' where desg_id='".$_REQUEST['desgId']."'";
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