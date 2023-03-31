<?php if(!isset($_SESSION)){ session_start(); }

	//include_once("include/dbop.class.php");
 	include_once("include/display_table.class.php");
 	include_once("include/validation.class.php");
	 	
	class login_m extends dbop
	{
	public $actionPage="../login.php";
	public $validate;
	public $m_dbConn;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
$this->display_pg=new display_table($this->m_dbConn);
		$this->validate=new validation();
		//dbop::__construct();
	}
	
	public function chk_log_m()
	{
		if(isset($_REQUEST['login_m']))
		{		
			$s = "select * from login where member_id='".$this->m_dbConn->escapeString(strtolower($_POST['user']))."' and status='Y'";
			$r = $this->m_dbConn->select($s);
			
			if($r[0]['password']==$_POST['pass'])
			{								
				$sql = "select count(*)as cnt from login where member_id='".$this->m_dbConn->escapeString(strtolower($_POST['user']))."' and password='".$this->m_dbConn->escapeString($_POST['pass'])."' and status='Y'";
				$res = $this->m_dbConn->select($sql);
			
				if($res[0]['cnt']==1)
				{
					$sql1 = "select * from login where member_id='".$this->m_dbConn->escapeString(strtolower($_POST['user']))."' and password='".$this->m_dbConn->escapeString($_POST['pass'])."' and status='Y'";
					$res1 = $this->m_dbConn->select($sql1);
					
					
					$_SESSION['member_id'] = $res1[0]['member_id'];
					$_SESSION['member_name'] = $res1[0]['name'];
					$_SESSION['com_id'] = $res1[0]['com_id'];
					$_SESSION['login_id'] = $res1[0]['login_id'];
					
					$_SESSION['society_id'] = $res1[0]['society_id'];
					
					$sql2 = "select * from society where society_id='".$_SESSION['society_id']."' and status='Y'";
					$res2 = $this->m_dbConn->select($sql2);
						
					$_SESSION['society_name'] = $res2[0]['society_name'];
					
					?>
					<script language="javascript" type="text/javascript">
						window.location.href = "view_member_profile.php?prf";   
					</script>
					<?php
				}
				else
				{
					return "Invalid email id or password";
				}		
			}
			else
			{
				return "Invalid email id or password";
			}
		}
	}
	
	public function check_fbid()
	{
		$sql =  "select count(*)as cnt from member_main where fbid='".$_SESSION['fbid']."' and status='Y'";
		$res = $this->m_dbConn->select($sql);	
		
		if($res[0]['cnt']==1)
		{
			$sql1 =  "select * from member_main where fbid='".$_SESSION['fbid']."' and status='Y'";
			$res1 = $this->m_dbConn->select($sql1);	
			
			$_SESSION['member_id'] = $res1[0]['member_id'];
			$_SESSION['member_name'] = $res1[0]['owner_name'];
			
			$sql11 =  "select * from login where com_id='".$_SESSION['member_id']."' and status='Y'";
			$res11 = $this->m_dbConn->select($sql11);	
			
			$_SESSION['com_id'] = $res11[0]['com_id'];
			$_SESSION['login_id'] = $res11[0]['login_id'];
			
			$_SESSION['society_id'] = $res1[0]['society_id'];
			
			$sql2 = "select * from society where society_id='".$_SESSION['society_id']."' and status='Y'";
			$res2 = $this->m_dbConn->select($sql2);
				
			$_SESSION['society_name'] = $res2[0]['society_name'];
			
			?>
			<script language="javascript" type="text/javascript">
                window.location.href = "view_member_profile.php?prf";   
            </script>
            <?php
		}
		else
		{
			?>
            <script>window.location.href = "member_main_new1.php?md&idd<?php echo time();?>";</script>
            <?php	
		}
	}
}
?>
