<?php if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");

class cp_m extends dbop
{
	public $m_dbConn;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
$this->display_pg=new display_table($this->m_dbConn);
		//dbop::__construct();
	}
	
	public function cp_m()
	{		
		if(trim($_POST['np'])<>"" && trim($_POST['cnp'])<>"")
		{				
			$sql1 = "update login set password='".$this->m_dbConn->escapeString($_POST['np'])."' where status='Y' and login_id ='".$_SESSION['login_id']."'";
			$res1 = $this->m_dbConn->update($sql1);
			
			return "Password changed successfully";			
		}
		else
		{
			return "All fields marked as * are compulsory";	
		}		
	}
}
?>