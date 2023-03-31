<?php
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");

class group extends dbop
{
	public $actionPage = "../group.php";
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
$this->display_pg=new display_table($this->m_dbConn);

		//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
		//$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);

		//dbop::__construct();
	}

	public function startProcess()
	{
		$errorExists = 0;

		//$curdate 		=  $this->curdate;
		//$curdate_show	=  $this->curdate_show;
		//$curdate_time	=  $this->curdate_time;
		//$ip_location	=  $this->ip_location;

		if($_REQUEST['insert']=='Insert' && $errorExists==0)
		{
			//$insert_query="insert into group (`srno`,`groupname`) values ('".$_POST['srno']."','".$_POST['groupname']."')";
			$insert_query="insert into `group` (`srno`,`groupname`) values ('".$_POST['srno']."','".$_POST['groupname']."')";
			echo $insert_query;
			$data = $this->m_dbConn->insert($insert_query);
			return "Insert";
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			$up_query="update `group` set `srno`='".$_POST['srno']."',`groupname`='".$_POST['groupname']."' where id='".$_POST['id']."'";
			$data = $this->m_dbConn->update($up_query);
			return "Update";
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
		$thheader = array('srno','groupname');
		$this->display_pg->edit		= "getgroup";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "group.php";

		$res = $this->display_pg->display_datatable($rsas);
		return $res;
	}
	public function pgnation()
	{
		$sql1 = "select `id`,`srno`,`groupname` from `group` where status='Y'";
		/*$cntr = "select count(status) as cnt from `group` where status='Y'";

		$this->display_pg->sql1		= $sql1;
		$this->display_pg->cntr1	= $cntr;
		$this->display_pg->mainpg	= "group.php";

		$limit	= "50";
		$page	= $_REQUEST['page'];
		$extra	= "";

		$res	= $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;*/
		
		$result = $this->m_dbConn->select($sql1);
		
		$data=$this->display1($result);
		return $data;
	}
	public function selecting()
	{
		$sql = "select id,`srno`,`groupname` from `group` where id='".$_REQUEST['groupId']."'";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	public function deleting()
	{
		$sql = "update `group` set status='N' where id='".$_REQUEST['groupId']."'";
		$res = $this->m_dbConn->update($sql);
	}
}
?>