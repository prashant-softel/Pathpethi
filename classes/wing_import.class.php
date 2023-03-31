<?php
//include_once("include/dbop.class.php");
include_once("utility.class.php");

class wing_import
{
	
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_utility;
	
	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
		$this->obj_utility= new utility($this->m_dbConn);
		
	}
	
	public function CSVWingImport()
	{
		if(isset($_POST["Import"]))
		{
			//echo '1';
			if(isset($_FILES['file']) && $_FILES['file']['error'] == 0)
			{
				$result = "0";
				 $ext = pathinfo($_FILES['file'] ['name'], PATHINFO_EXTENSION);
				//$fileName = "files/" . $dateTimeNow. ".csv";
				 $tempName = $_FILES['file'] ['tmp_name'];
				/*	
				$original_file_name='WingID.csv';
				//echo $_FILES['file'] ['name'];
				if(($_FILES['file'] ['name']) != "$original_file_name") {
					  //exit("Does not match");
					  $result = '<p>File Name Does Not Match(only WingID.csv file accepted)...</p>';
					  
					}
				else 
				*/
				if($ext <> '' && $ext <> 'csv')
				{	
					$result = '<p>Invalid file format selected. Expected csv file format</p>';
				}
				else
				{
					//if ( move_uploaded_file ($_FILES['file'] ['tmp_name'], $fileName)  )
					if (isset($_FILES['file']['error']) || is_array($_FILES['file']['error']))
					{  
						$result = '<p> Wing Data Uploading Process Started <' . $this->getDateTime() . '> </p>';
						
						$result .= $this->UploadData($tempName);
						
						$result .= '<p> Wing Data Uploading Process Complete <' . $this->getDateTime() . '> </p>';
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
			//echo '<body onload="parent.doneloading(\''.$result.'\')"></body>'; 
			return $result;
			
		}
	}
	
	public function UploadData($fileName,$errorfile)
	{
		$file = fopen($fileName,"r");
		$errormsg="[Importing WingID]";
		$isImportSuccess = false;
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		
		$sql00="select wing_flag from `import_history` where society_id='".$_SESSION['society_id']."'";
		$res01=$this->m_dbConn->select($sql00);
		if($res01[0]['wing_flag']==0)
		{
			while (($row = fgetcsv($file)) !== FALSE)
			{
				if($row[0] <> '')
					{
						$rowCount++;
						if($rowCount == 1)
						{
							$WCode=array_search(WCode,$row,true);
							$WName=array_search(WName,$row,true);
							
							if(!isset($WCode) || !isset($WName))
								{
									$result = '<p>Column Names Not Found Cant Proceed Further......</p>'.'Go Back';
									$errormsg=" Column names WCode and WName  in file WingID not match";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
									return $result;
									exit(0);
								}
							
							
							
							
						}
					 else
					   {
						   $wingExist=false;
							$wing=$row[$WCode];
							$wing_name = $row[$WName];
							if($wing=='' || $wing=='-')
							{
								
									$errormsg="WCode is &lt; - &gt;' or not provided in WingId file";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"W");
								
							}
							
							$search_exists="select count(*) as cnt from `wing` where wing='".$wing_name."' and society_id='".$_SESSION['society_id']."' ";
							$res00=$this->m_dbConn->select($search_exists);
							if($res00[0]['cnt'] > 0)
							{
								 $wingExist = true;
								$errormsg="Already Exists &lt;" .$wing_name."&gt; Wing for this society";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"W");
								$update_import_history="update `import_history` set wing_flag=1 where society_id='".$_SESSION['society_id']."'";
								$res123=$this->m_dbConn->update($update_import_history);
							}
							
							else
							{
								$insert_society="insert into wing(society_id,wing) values('".$_SESSION['society_id']."','$wing_name')";
								$data=$this->m_dbConn->insert($insert_society);
								$isImportSuccess = true;
							}
							
					   }
				
					}
			
				}
				if($isImportSuccess)
				{
					$update_import_history="update `import_history` set wing_flag=1 where society_id='".$_SESSION['society_id']."'";							
					$res123=$this->m_dbConn->update($update_import_history);
				}
				else
				{
					$errormsg="wing details not imported";
					$this->obj_utility->logGenerator($errorfile,'Error',$errormsg,"E");	
				}
				$errormsg="[End of  WingID]";
				$this->obj_utility->logGenerator($errorfile,'End',$errormsg);
	
		}
	}
	function getDateTime()
	{
		$dateTime = new DateTime();
		$dateTimeNow = $dateTime->format('Y-m-d H:i:s');
		return $dateTimeNow;
	}
}

?>