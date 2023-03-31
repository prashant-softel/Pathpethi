<?php
//include_once("include/dbop.class.php");

class del_control_sadmin extends dbop
{
	public $m_dbConn;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		//dbop::__construct();
	}
	
	public function set_delete_perm_sadmin()
	{
		if($_REQUEST['del_control']=='true')
		{
			$sql = "update del_control_sadmin set del_control_sadmin='1' where status='Y'";
			$res = $this->m_dbConn->update($sql);	
			echo 1;
		}
		else
		{
			$sql = "update del_control_sadmin set del_control_sadmin='0' where status='Y'";
			$res = $this->m_dbConn->update($sql);	
			echo 0;
		}
	}
	
	public function chk_delete_perm_sadmin()
	{
		$sql = "select * from del_control_sadmin where status='Y'";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
}
?>