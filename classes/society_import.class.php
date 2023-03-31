<?php
include_once("defaults.class.php");
include_once("utility.class.php");
include_once("dbconst.class.php");

class society_import
{
	
	public $m_dbConn;
	public $m_dbConnRoot;
	private $obj_default;
	public $obj_utility;
	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
		$this->obj_default = new defaults($this->m_dbConn);
		$this->obj_utility= new utility($this->m_dbConn);
	}
	
	public function CSVSocietyImport()
	{
		if(isset($_POST["Import"]))
		{
			if(isset($_FILES['file']) && $_FILES['file']['error'] == 0)
			{
				
				$result = "0";
				  $ext = pathinfo($_FILES['file'] ['name'], PATHINFO_EXTENSION);
				$tempName = $_FILES['file'] ['tmp_name'];
				
				if($ext <> '' && $ext <> 'csv')
				{	
					$result = '<p>Invalid file format selected. Expected csv file format</p>';
				}
				else
				{
					
					if (isset($_FILES['file']['error']) || is_array($_FILES['file']['error']))
					{  
						$result = '<p> Society Data Uploading Process Started <' . $this->getDateTime() . '> </p>';
						
						$result .= $this->UploadData($tempName);
						
						$result .= '<p> Society Data Uploading Process Complete <' . $this->getDateTime() . '> </p>';
					}
					else
					{ 
						echo $_FILES['file'] ['error'];
						switch ($_FILES['file'] ['error'])
						{
							case 1:
								   echo '<p> The file is bigger than this PHP installation allows</p>';
								   $result = '<p> The file is bigger than this PHP installation allows</p>';
								   break;
							case 2:
								   echo '<p> The file is bigger than this form allows</p>';
								   $result = '<p> The file is bigger than this form allows</p>';
								   break;
							case 3:
								   echo '<p> Only part of the file was uploaded</p>';
								   $result = '<p> Only part of the file was uploaded</p>';
								   break;
							case 4:
								   echo '<p> No file was uploaded</p>';
								   $result = '<p> No file was uploaded</p>';
								   break;
						}
					} 
				}
			}
			else if(isset($_FILES['file']) && $_FILES['file']['error'] <> 0)
			{
				
				//echo '2';
				$errorCode = $_FILES['file']['error']; 
				switch ($errorCode)
				{
					case 1:
						   //echo '<p> The file is bigger than this PHP installation allows</p>';
						   $result = '<p> The file is bigger than this PHP installation allows</p>';
						   break;
					case 2:
						   //echo '<p> The file is bigger than this form allows</p>';
						   $result = '<p> The file is bigger than this form allows</p>';
						   break;
					case 3:
						   //echo '<p> Only part of the file was uploaded</p>';
						   $result = '<p> Only part of the file was uploaded</p>';
						   break;
					case 4:
						   //echo '<p> No file was uploaded</p>';
						   $result = '<p> No file was uploaded</p>';
						   break;
				}
			}
			
			return $result;
			
		}
		
		else
		{
			
		}
	}
	
	public function UploadData($fileName,$errorfile)
	{
		$file = fopen($fileName,"r");
		$isImportSuccess = false;
		$errormsg="[Importing BuildingID]";
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		while (($row = fgetcsv($file)) !== FALSE)
		{
			if($row[0] <> '')
				{
					$rowCount++;
					if($rowCount == 1)
					{
						//echo '1';
						$BCode=array_search(BCode,$row,true);
						$BName=array_search(BName,$row,true);
						if(!isset($BCode) || !isset($BName))
							{
								$errormsg=" Column names BCode or BName in file  BuildingID not match";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								exit(0);
								
							}
					}
					else
				   {
					  $society_code=$row[$BCode];
						$society_name=$row[$BName];
						$society_add=$row[$BAddress];
						$com_id=0;
						$authority="Super Admin";
						$name="Admin";
						$societyExist = false;
						if($society_name =="-" || $society_name=="")
						{
							$errormsg=	"Society name not provided or blank in BuildingID file";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							return "Society name not provided or blank in BuildingID file";	
							break;
						}
						else
						{
							$errormsg=	" <table  border='1px solid black'><tr><td>Society Name</td><td>".$society_name."</td></tr>";
							$this->obj_utility->logGenerator($errorfile,'',$errormsg);
							$errormsg2=	" <tr><td>Society Code</td><td>".$society_code."</td></tr></table><br>";
							$this->obj_utility->logGenerator($errorfile,'',$errormsg2);	
						}
						
						if($society_code=="")
						{
							$errormsg=	" Society code not provided in BuildingID file";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							return "Society code not provided in BuildingID file";
							break;
						}
							
						$search_exists="select count(*) as cnt from `society` where society_name='".$society_name."' ";
						$res00=$this->m_dbConnRoot->select($search_exists);
						if($res00[0]['cnt'] > 0)
						{ 
							$errormsg="Society &lt;".$society_name."&gt;  already exist";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							return "Exist";
							break;
						}
						else if($res00[0]['cnt']==0 && $society_code <> "")
						{
							
							$insert_society_root = "INSERT INTO `society`(`society_code`, `society_name`, `dbname`,`client_id`) VALUES ('$society_code','$society_name','" . $_SESSION['dbname'] . "','".$_SESSION['client_id']."')";
							
							$result_society_id = $this->m_dbConnRoot->insert($insert_society_root);
							$isImportSuccess = true;
							$update_dbname = "UPDATE dbname SET society_id = '" . $result_society_id . "' WHERE dbname = '" . $_SESSION['dbname'] . "'";
							$result_dbname = $this->m_dbConnRoot->update($update_dbname);
							
							$insert_mapping = "INSERT INTO `mapping`(`login_id`, `society_id`, `desc`, `role`, `profile`, `created_by`, `status`, `view`) VALUES ('" . $_SESSION['login_id'] . "', '" . $result_society_id . "', '" . ROLE_SUPER_ADMIN . "', '" . ROLE_SUPER_ADMIN . "', '" . PROFILE_SUPER_ADMIN_ID . "', '" . $_SESSION['login_id'] . "', 2, 'ADMIN')";
							
							$result_mapping = $this->m_dbConnRoot->insert($insert_mapping);
							
							$sqlUpdate = "UPDATE `login` SET `current_mapping`='" . $result_mapping . "' WHERE login_id = '" . $_SESSION['login_id'] . "'";
							$resultUpdate = $this->m_dbConnRoot->update($sqlUpdate);						
							
							$_SESSION['current_mapping'] = $result_mapping;
								
							if($_SESSION['client_id'] > 0)
							{
								$sqlSelectSadmin = "select login_id from login where client_id = '" . $_SESSION['client_id'] . "' and authority = 'self'";
								$resultSelectSadmin = $this->m_dbConnRoot->select($sqlSelectSadmin);
								
								for($sadminCnt = 0 ; $sadminCnt < sizeof($resultSelectSadmin) ; $sadminCnt++)
								{
									if($resultSelectSadmin[$sadminCnt]['login_id'] <> $_SESSION['login_id'])
									{
										$insert_mapping_sadmin = "INSERT INTO `mapping`(`login_id`, `society_id`, `desc`, `role`, `profile`, `created_by`, `status`, `view`) VALUES ('" . $resultSelectSadmin[$sadminCnt]['login_id'] . "', '" . $result_society_id . "', '" . ROLE_SUPER_ADMIN . "', '" . ROLE_SUPER_ADMIN . "', '" . PROFILE_SUPER_ADMIN_ID . "', '" . $_SESSION['login_id'] . "', 2, 'ADMIN')";
							
										$result_mapping_sadmin = $this->m_dbConnRoot->insert($insert_mapping_sadmin);
									}
								}
							}
								
							$insert_mapping = "INSERT INTO `mapping`(`society_id`, `desc`, `code`, `role`, `profile`, `created_by`, `view`) VALUES ('" . $result_society_id . "', '" . ROLE_ADMIN . "', '" . getRandomUniqueCode() . "', '" . ROLE_ADMIN. "', '" . PROFILE_ADMIN_ID . "', '" . $_SESSION['login_id'] . "', 'ADMIN')";
							
							$result_mapping = $this->m_dbConnRoot->insert($insert_mapping);
							
							$prevPeriod = $this->getPrevPeriod($_POST['Period']);
							
							$insert_society="insert into society(society_id, society_code,society_name,society_add,bill_cycle,int_rate,int_method,rebate_method,rebate,chq_bounce_charge,bill_method,M_PeriodID,society_creation_yearid) values('" . $result_society_id . "', '$society_code', '$society_name', '$society_add', '".$_POST['Cycle']."','".$_POST['int_rate']."','".$_POST['int_method']."','".$_POST['rebate_method']."','".$_POST['rebate']."','".$_POST['chq_bounce_charge']."','".BILL_FORMAT_WITH_RECEIPT."','". $prevPeriod."','".$_POST['Year']."')";
							$data=$this->m_dbConn->insert($insert_society);
	
							
							$_SESSION['society_id']	= $data;
							
							$sqlDefault = "INSERT INTO `appdefault`(`APP_DEFAULT_SOCIETY`, `changed_by`) VALUES ('" . $data . "', '" . $_SESSION['login_id'] . "')";
							$resultDefault = $this->m_dbConn->insert($sqlDefault);
							 
							$sqlDefault = "INSERT INTO `counter`(`society_id`) VALUES ('" . $data . "')";
							$resultDefault = $this->m_dbConn->insert($sqlDefault);
							
							break;
						
						}//else if
				   	
				   }//else
			
		}//if
		
	}//while
	
		if($isImportSuccess)
		{
			$sql="insert into `import_history`(society_id,society_flag) values('".$data."',1)";
			$res=$this->m_dbConn->insert($sql);
		}
		else
		{
			$errormsg="society not imported";
			$this->obj_utility->logGenerator($errorfile,'Error',$errormsg,"E");	
		}
	
	$errormsg="[End of  BuildingID]";
	$this->obj_utility->logGenerator($errorfile,'End',$errormsg);
	}
	function getDateTime()
	{
		$dateTime = new DateTime();
		$dateTimeNow = $dateTime->format('Y-m-d H:i:s');
		return $dateTimeNow;
	}
	
	function getPrevPeriod($curPeriod)
	{
		$sql = "Select PrevPeriodID from period where ID = '" . $curPeriod . "'";
		$result = $this->m_dbConn->select($sql);
		
		return $result[0]['PrevPeriodID'];
	}
}

?>