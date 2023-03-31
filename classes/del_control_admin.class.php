<?php if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");

class del_control_admin extends dbop
{
	public $m_dbConn;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		//dbop::__construct();
	}
	
	public function set_delete_perm_admin()
	{
		if($_REQUEST['del_control']=='true')
		{
			$sql = "update del_control_admin set del_control_admin='1' where status='Y' and login_id='".$_SESSION['login_id']."'";
			$res = $this->m_dbConn->update($sql);	
			echo 1;
		}
		else
		{
			$sql = "update del_control_admin set del_control_admin='0' where status='Y' and login_id='".$_SESSION['login_id']."'";
			$res = $this->m_dbConn->update($sql);	
			echo 0;
		}
	}
	
	public function chk_delete_perm_admin()
	{
		$sql = "select * from del_control_admin where status='Y' and login_id='".$_SESSION['login_id']."'";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
}
?>