<?php if(!isset($_SESSION)){ session_start(); }
 //include_once("include/dbop.class.php");
 include_once("include/display_table.class.php");
 include_once("include/validation.class.php");

	class login_s extends dbop
	{
	public $actionPage = "../login_s.php";
	public $validate;
	public $m_dbConn;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
$this->display_pg=new display_table($this->m_dbConn);
		$this->validate=new validation();
		//dbop::__construct();
	}
	
	public function chk_log_s()
	{
		if(isset($_REQUEST['login_s']))
		{
			if($_POST['sqr']=="123")
			{
				$sql = "select count(*)as cnt from login where member_id='".$this->m_dbConn->escapeString($_POST['user'])."' and password='".$this->m_dbConn->escapeString($_POST['pass'])."' and status='Y'";
				$res = $this->m_dbConn->select($sql);
			
				if($res[0]['cnt']==1)
				{
					$sql1 = "select * from login where member_id='".$_POST['user']."' and password='".$_POST['pass']."' and status='Y'";
					$res1 = $this->m_dbConn->select($sql1);
					
					//$_SESSION['admin'] = '';
					$_SESSION['sadmin'] = 'Super Admin';
					$_SESSION['com_id'] = 0;
					$_SESSION['login_id'] = $res1[0]['login_id'];
					
					?>
					<script language="javascript" type="text/javascript">
						window.location.href = "../society_view.php?imp";  
					</script>
					<?php
				}
				else
				{
					return "Invalid Username or Password";
				}		
			}
			else
			{
				return 'Enter correct security no.';
			}
		}
	}
}
?>
