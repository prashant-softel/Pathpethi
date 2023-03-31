
<?php
//include_once("include/dbop.class.php");

include_once("defaults.class.php");
set_time_limit(0);
ignore_user_abort(1);

class tarrif_import extends dbop
{
	
	public $m_dbConn;
	private $obj_default;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->obj_default = new defaults($this->m_dbConn);
	}
	
	public function CSVTarrifImport()
	{
		if(isset($_POST["Import"]))
		{
			//echo 'Inside CSVUnitImport';
			if(isset($_FILES['file']) && $_FILES['file']['error'] == 0)
			{
				$result = "0";
				 $ext = pathinfo($_FILES['file'] ['name'], PATHINFO_EXTENSION);
				//$fileName = "files/" . $dateTimeNow. ".csv";
				 $tempName = $_FILES['file'] ['tmp_name'];
				/*
				$original_file_name='BuildingID.csv';
				//echo $_FILES['file'] ['name'];
				if(($_FILES['file'] ['name']) != "$original_file_name") {
					  //exit("Does not match");
					  $result = '<p>File Name Does Not Match(only BuildingID.csv file accepted)...</p>';
					  
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
						$result = '<p> Data Uploading Process Started <' . $this->getDateTime() . '> </p>';
						
						$result .= $this->UploadData($tempName);
						
						$result .= '<p> Data Uploading Process Complete <' . $this->getDateTime() . '> </p>';
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
	
	private function UploadData($fileName)
	{
		//echo 'Inside Upload Data';
		$file = fopen($fileName,"r");
		
		$sql00="select tarrif_flag from `import_history` where society_id='".$_SESSION['society_id']."'";
		$res01=$this->m_dbConn->select($sql00);
		if($res01[0]['tarrif_flag']==0)
		{
		while (($row = fgetcsv($file)) !== FALSE)
		{
			//echo 'Inside Upload Data';
			//echo '<br/>';
			if($row[0] <> '')
				{
					
					//echo '1';
					$rowCount++;
					if($rowCount == 1)
					{
					$WCode=array_search(WCode,$row,true);
					$BCode=array_search(BCode,$row,true);
					$FCode=array_search(FCode,$row,true);
					$Particulars=array_search(Particulars,$row,true);
					$AccountName=array_search(AccountName,$row,true);
					$Rate=array_search(Rate,$row,true);
					
						 
						if(!isset($WCode) || !isset($FCode) || !isset($BCode) || !isset($Particulars) || !isset($AccountName) || !isset($Rate))
						{
							$result = '<p>Column Names Not Found Cant Proceed Further......</p>'.'Go Back';
								//$result.'<p>Cant Proceed Further...</p>';
								return $result;
								exit(0);
								//break;
								//return ;
						}
					
					}
			
			       else
				   {	
				   		//echo '2';
						$UnitNo=$row[$FCode];
						$society_code=$row[$BCode];
						$wing=$row[$WCode];
						$ledger_name=$row[$Particulars];
						$account_category=$row[$AccountName];
						$AccountHeadAmount=$row[$Rate];
						$changeLog = new changeLog($this->m_dbConn);
						$desc = 'Imported Data';
						$LatestChangeID = $changeLog->setLog($desc, $_SESSION['login_id'], 'unitbillmaster', '--');
						//echo '3';
						
						
						$sql00="select society_id from `society` where society_code='".$society_code."'";
						$data00=$this->m_dbConn->select($sql00);
						$society_id=$data00[0]['society_id'];
						//echo $sql00;
						
					/*	$sql02="select wing_id from `wing` where wing='".$wing."'";
						$data02=$this->m_dbConn->select($sql02);
						$wing_id=$data02[0]['wing_id'];
					*/	
						$sql="select unit_id from `unit` where unit_no='".$UnitNo."' and society_id='".$society_id."' ";
						$data=$this->m_dbConn->select($sql);
						$UnitID=$data[0]['unit_id'];
						//echo $sql;
					
					
						$search_account_head="select id from `ledger` where ledger_name='".$ledger_name."' ";
						$AccountHead=$this->m_dbConn->select($search_account_head);
						$HeadID=$AccountHead[0]['id'];
					
							//echo $search_account_head;						
						if($AccountHeadAmount <> 0)
						{
						$insert_unitbillmaster="insert into `unitbillmaster`(UnitID,CreatedBy,LatestChangeID,AccountHeadID,AccountHeadAmount) values('$UnitID','" . $_SESSION['login_id'] . "','$LatestChangeID','$HeadID','$AccountHeadAmount')";
						$data1=$this->m_dbConn->insert($insert_unitbillmaster);
						//echo $insert_unitbillmaster;
						}
				   
				   }
			
			if($data1<> '')
						{
						$update_import_history="update `import_history` set tarrif_flag=1 where society_id='".$_SESSION['society_id']."'";
						$data23=$this->m_dbConn->update($update_import_history);						
						}
		}
				
		
	}
	
	//echo "file imported successfully..";
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