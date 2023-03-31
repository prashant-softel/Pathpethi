<?php if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");

class cp
{
	public $m_dbConn;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
$this->display_pg=new display_table($this->m_dbConn);
		//dbop::__construct();
	}
	
	public function cp()
	{		
		if(trim($_POST['code'])<>"" && trim($_POST['op'])<>"" && trim($_POST['np'])<>"" && trim($_POST['cnp'])<>"")
		{	
			$s = "select * from login where login_id='".$_SESSION['login_id']."' and status='Y'";
			$r = $this->m_dbConn->select($s);
			
			if($r[0]['password']==$_POST['op'])
			{
				$sql = "select count(*)as cnt from login where password='".$this->m_dbConn->escapeString($_POST['op'])."' and status='Y' and login_id ='".$_SESSION['login_id']."'";
				$res = $this->m_dbConn->select($sql);
				
				if($res[0]['cnt']==1)
				{				
					$sql1 = "update login set password='".$this->m_dbConn->escapeString($_POST['np'])."' where status='Y' and login_id ='".$_SESSION['login_id']."'";
					$res1 = $this->m_dbConn->update($sql1);
					
					$sql2 = "update login set security_no='".$this->m_dbConn->escapeString($_POST['code'])."' where status='Y' and login_id ='".$_SESSION['login_id']."'";
					$res2 = $this->m_dbConn->update($sql2);
					
					return "Password updated successfully";	
				}
				else
				{
					return "Old password is incorrect";	
				}	
			}
			else
			{
				return "Old password is incorrect";	
			}
		}
		else
		{
			return "All fields marked as * are compulsory";	
		}		
	}
	
	public function code()
	{
		$sql = "select `security_no`,`member_id`,`name` from login where login_id='".$_SESSION['login_id']."' and status='Y'";
		$res = $this->m_dbConn->select($sql);	
		
		return $res[0];
	}
	public function ChangeUserName()
	{
		$txtUsername = $_POST['txtNewUserName'];
		$txtDisplayName = $_POST['txtDisplayName'];
		$sql = "update `login` set `member_id` ='".$txtUsername ."',`name`='".$txtDisplayName ."' where `login_id`='".$_SESSION['login_id']."' and `status`='Y'";
		//echo $sql;
		$res = $this->m_dbConn->update($sql);	
		//print_r($res);
		return $res;
	}
}

?>