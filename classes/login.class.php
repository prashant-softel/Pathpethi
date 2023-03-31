<?php if(!isset($_SESSION)){ session_start(); }
	include_once("include/display_table.class.php");
	include_once("defaults.class.php");
	include_once("initialize.class.php");	
	include_once("utility.class.php");	
class login
{
	public $actionPage = "../login.php";
	public $m_dbConn;
	private $obj_defaults;
	public $obj_Utility;
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
	
		$this->obj_defaults = new defaults($this->m_dbConn);
		$this->obj_Utility = new utility($this->m_dbConn);
	}

	public function chk_log($mapCode = '', $email = '')
	{
		if(isset($_REQUEST['login']))
		{
			$s = "select * from login where member_id='".$this->m_dbConn->escapeString(strtolower($_POST['user']))."' and status='Y'";
			$r = $this->m_dbConn->select($s);
					
			if($r[0]['password']==$_POST['pass'])
			{
				$sql = "select count(*)as cnt from login where member_id='".$this->m_dbConn->escapeString(strtolower($_POST['user']))."' and password='".$this->m_dbConn->escapeString($_POST['pass'])."' and status='Y'";
				//echo $sql;
				$res = $this->m_dbConn->select($sql);
		
				if($res[0]['cnt']==1)
				{
					$sql1 = "select * from login where member_id='".$this->m_dbConn->escapeString(strtolower($_POST['user']))."' and password='".$this->m_dbConn->escapeString($_POST['pass'])."' and status='Y'";
					$res1 = $this->m_dbConn->select($sql1);
					
					if($email <> '' && $mapCode <> '')
					{
						$emailID = $this->obj_Utility->decryptData($email);
						if($this->m_dbConn->escapeString(strtolower($emailID)) == $this->m_dbConn->escapeString(strtolower($_POST['user'])))
						{
							$obj_initialize = new initialize($this->m_dbConn);
							$result = $obj_initialize->verifyCode($mapCode);
							if($result == '')
							{
								$msg = 'Invalid Code.';
							}
							else if($result[0]['status'] == 2 || $result[0]['status'] == 3)
							{
								$msg = 'Code Already In Use.';
							}
							else
							{
								$_SESSION['login_id'] = $res1[0]['login_id'];
								$obj_initialize->setLoginIDToMap($result[0]['id'], 2);
							}
						}						
					}
					
					$this->setSessionVariables($res1);
				}
				else
				{
					return "Invalid Username or Password";
				}	
			}
			else
			{
				return "Invalid Username or Password";	
			}
		}
	}
	
	public function chk_log_fb($user, $fbid, $mapCode = "",$name = "")
	{
		$bAccountExist = true;
		
		//$obj_initialize = new initialize($this->m_dbConn);
		
		//$s = "select * from login where member_id='".$this->m_dbConn->escapeString(strtolower($user))."' and status='Y'";
		$s = "select * from login where fbcode='".$this->m_dbConn->escapeString($fbid)."' and status='Y'";
		$r = $this->m_dbConn->select($s);
					
		if($r <> '')
		{
			if($r[0]['fbcode'] == $fbid)
			{
				//$sql = "select count(*)as cnt from login where member_id='".$this->m_dbConn->escapeString(strtolower($user))."' and fbcode='".$this->m_dbConn->escapeString($fbid)."' and status='Y'";
				$sql = "select count(*)as cnt from login where  fbcode='".$this->m_dbConn->escapeString($fbid)."'  and fbcode='".$this->m_dbConn->escapeString($fbid)."' and status='Y'";
				$res = $this->m_dbConn->select($sql);
		
				if($res[0]['cnt']==1)
				{
					//$sql1 = "select * from login where member_id='".$this->m_dbConn->escapeString(strtolower($user))."' and fbcode='".$this->m_dbConn->escapeString($fbid)."' and status='Y'";
					$sql1 = "select * from login where   fbcode='".$this->m_dbConn->escapeString($fbid)."'  and fbcode='".$this->m_dbConn->escapeString($fbid)."' and status='Y'";
					$res1 = $this->m_dbConn->select($sql1);
					
					if($mapCode <> '')
					{
						$obj_initialize = new initialize($this->m_dbConn);
						$result = $obj_initialize->verifyCode($mapCode);
						
						if($result == '')
						{
							$msg = 'Invalid Code.';
						}
						else if($result[0]['status'] == 2 || $result[0]['status'] == 3)
						{
							$msg = 'Code Already In Use.';
						}
						else
						{
							$_SESSION['login_id'] = $res1[0]['login_id'];
							$obj_initialize->setLoginIDToMap($result[0]['id'], 2);
						}
												
					}
					
					$this->setSessionVariables($res1);
				}
				else
				{
					$bAccountExist = false;
					return "Account Is Not Registered.";
				}
			}
			else
			{
				if(sizeof($r) == 1)
				{
					$sql1 = "Update login SET fbcode = '" . $fbid . "' where member_id='".$this->m_dbConn->escapeString(strtolower($user))."'  and status='Y'";
					$res1 = $this->m_dbConn->update($sql1);
					
					if($mapCode <> '')
					{
						$obj_initialize = new initialize($this->m_dbConn);
						$result = $obj_initialize->verifyCode($mapCode);
						
						if($result == '')
						{
							$msg = 'Invalid Code.';
						}
						else if($result[0]['status'] == 2 || $result[0]['status'] == 3)
						{
							$msg = 'Code Already In Use.';
						}
						else
						{
							$_SESSION['login_id'] = $res1[0]['login_id'];
							$obj_initialize->setLoginIDToMap($result[0]['id'], 2);
						}
												
					}
					
					$this->setSessionVariables($r);
				}
			}
		}
		else
		{
			$bAccountExist = false;
			return "Account Is Not Registered.";	
		}
		
		/*if($bAccountExist == false)
		{
			$result = $obj_initialize->addUser($name, $user, '', $fbid, true);
			if($result > 0)
			{
				?>
                	<script>window.location.href = "initialize.php?imp"</script>
                <?php
			}
			else if($result == -1)
			{
				$msg = 'E-Mail ID [' . $user. '] Is Already Registered.';
				return $msg;
			}
		}*/
	}
	
	function getEmptyDBName()
	{
		$sql = "Select * from `dbname` where locked =  0 and status = 0 LIMIT 0,1";	
		$result = $this->m_dbConn->select($sql);
		if($result <> '')
		{
			return $result[0]['dbname'];
		}
		else 
		{
			return '';
		}
	}
	
	private function setSessionVariables($result)
	{
		$_SESSION['login_id'] = $result[0]['login_id'];
		$_SESSION['current_mapping'] = $result[0]['current_mapping'];
		$_SESSION['society_id'] = 0;
		$_SESSION['authority'] = $result[0]['authority'];
		$_SESSION['name'] = $result[0]['name'];
		$_SESSION["View"] = "";
		$_SESSION["unit_id"] = 0;
		$_SESSION['desc'] = '';
		$_SESSION['client_id'] = $result[0]['client_id'];

		$this->obj_defaults->resetDefaults();
		
		//print_r($_SESSION);
												
		//$_SESSION["sadmin_details1"] = $res1;		
		
		if($_SESSION['current_mapping'] <> 0)
		{
			$obj_initialize = new initialize($this->m_dbConn);
			$mapDetails = $obj_initialize->getMapDetails($_SESSION['current_mapping']);

			
			if($mapDetails <> '')
			{
				$dbName = $mapDetails[0]['dbname'];
				$_SESSION['dbname'] = $dbName;
				
				$society_id = $mapDetails[0]['society_id'];
				$_SESSION['society_id'] = $society_id;
				
				$role = $mapDetails[0]['role'];
				$_SESSION['role'] = $role;
				
				$view = $mapDetails[0]['view'];
				$_SESSION["View"] = strtoupper($view);
				
				$unit_id = $mapDetails[0]['unit_id'];
				$_SESSION["unit_id"] = $unit_id;
				
				$_SESSION['desc'] = $mapDetails[0]['desc'];
				
				$obj_initialize->setProfile($mapDetails[0]['profile']);
				
				$obj_initialize->getModuleAccess();
				if(isset($_REQUEST["url"]))
				{
					?>
					<script>
						localStorage.setItem('login', "<?php echo $_SESSION['login_id']; ?>");
						window.location.href = "initialize.php?set&url=<?php echo $_REQUEST["url"]?> ";
					</script>
					<?php
				}
				else
				{
				?>
					<script language="javascript" type="text/javascript">
						localStorage.setItem('login', "<?php echo $_SESSION['login_id']; ?>");
						window.location.href = "initialize.php?imp";
					</script>
				<?php
				}
			}
		}
		else
		{
			?>
			<script language="javascript" type="text/javascript">
				localStorage.setItem('login', "<?php echo $_SESSION['login_id']; ?>");
				<?php 
				if($_SESSION['authority'] == 'master')
				{
				$_SESSION['role'] = 'Master Admin';
				?>
				window.location.href = "client.php";  
				<?php
				}
				else if(isset($_REQUEST["url"]))
				{
				?>
				window.location.href = "initialize.php?imp&url=<?php echo $_REQUEST["url"]?>"; 
				<?php
				}
				else
				{
				?>
				window.location.href = "initialize.php?imp"; 
				<?php
				}
				?>
			</script>
			<?php
		}
	}		
}
?>
 