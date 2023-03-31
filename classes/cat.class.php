<?php
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");

class cat extends dbop
{
	public $actionPage = "../cat.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	
	function __construct($dbConn, $dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		$this->m_dbConnRoot = $dbConnRoot;
		//dbop::__construct();
	}
	public function startProcess()
	{
		$errorExists=0;
		if($_REQUEST['insert']=='Insert' && $errorExists==0)
		{
			if($_POST['cat']<>"")
			{
				$sql = "select count(*)as cnt from cat where cat='".addslashes(trim(ucwords($_POST['cat'])))."' and status='Y'";
				$res = $this->m_dbConnRoot->select($sql);
				
				if($res[0]['cnt']==0)
				{
					$insert_query="insert into cat (`cat`) values ('".addslashes(trim(ucwords($_POST['cat'])))."')";
					$data=$this->m_dbConnRoot->insert($insert_query);
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
			if($_POST['cat']<>"")
			{	
				$up_query="update cat set `cat`='".addslashes(trim(ucwords($_POST['cat'])))."' where cat_id='".$_POST['id']."'";
				$data=$this->m_dbConnRoot->update($up_query);
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
			$thheader=array('Category');
			$this->display_pg->edit="getcat";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="cat.php";
			$res=$this->display_pg->display_new($rsas);
			return $res;
	}
	public function pgnation()
	{
			$sql1="select cat_id,`cat` from cat where status='Y'";
			$cntr="select count(*) as cnt from cat where status='Y'";
			/*$this->display_pg->sql1=$sql1;
			$this->display_pg->cntr1=$cntr;
			$this->display_pg->mainpg="cat.php";
			$limit="20";
			$page=$_REQUEST['page'];
			$extra="";
			$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
			return $res;*/
			$result = $this->m_dbConnRoot->select($sql1);
			$thheader=array('Category');
			$this->display_pg->edit="getcat";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="cat.php";
			$res = $this->display_pg->display_datatable($result);
	}
	public function selecting()
	{
			$sql1="select cat_id,`cat` from cat where cat_id='".$_REQUEST['catId']."'";
			$var=$this->m_dbConnRoot->select($sql1);
			return $var;
	}
	public function deleting()
	{
		$sql0 = "select count(*)as cnt from spr_cat where cat_id='".$_REQUEST['catId']."' and status='Y'";
		$res0 = $this->m_dbConnRoot->select($sql0);
		
		if($res0[0]['cnt']==0)
		{
			$sql1="update cat set status='N' where cat_id='".$_REQUEST['catId']."'";
			$this->m_dbConnRoot->update($sql1);
			
			echo "msg1";
		}
		else
		{
			echo "msg";	
		}
	}
}
?>