<?php
//include_once("include/dbop.class.php");
include_once("dbconst.class.php");

class member_dues_import extends dbop
{
	
	public $m_dbConn;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
	}
	
	public function CSVMemberDuesImport()
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
				
				if($ext <> '' && $ext <> 'csv')
				{	
					$result = '<p>Invalid file format selected. Expected csv file format</p>';
				}
				else
				{
					//if ( move_uploaded_file ($_FILES['file'] ['tmp_name'], $fileName)  )
					if (isset($_FILES['file']['error']) || is_array($_FILES['file']['error']))
					{  
						$result = '<p> Member Dues Data Uploading Process Started <' . $this->getDateTime() . '> </p>';
						
						$result .= $this->UploadData($tempName);
						
						$result .= '<p> Member Dues Data Uploading Process Complete <' . $this->getDateTime() . '> </p>';
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
		//echo '3';
		//echo 'Inside Upload Data';
		$file = fopen($fileName,"r");
		
		$sql00="select memberdue_flag from `import_history` where society_id='".$_SESSION['society_id']."'";
		$res01=$this->m_dbConn->select($sql00);
		//echo '6';
		//print_r($res01);
		if($res01[0]['memberdue_flag']== 0)
		{
			//echo '66';
			//echo $row= fgetcsv($file);
		while(($row = fgetcsv($file)) !== FALSE)
		{
			
			//echo '77';
			if($row[0] <> '')
				{
					$rowCount++;
					if($rowCount == 1)
					{
						$WCode=array_search(WCode,$row,true);
						$FCode=array_search(FCode,$row,true);
						$CrBalance=array_search(CrBalance,$row,true);
						$DrBalance=array_search(DrBalance,$row,true);
					}
			//print_r($row);
			       else
				   {
					   //echo '4';
						$society_code=$row[1];
						//echo $society_code;
						$wing=$row[$WCode];
						$unit_no=$row[$FCode];
						$Debit=$row[$DrBalance];
						$Credit=$row[$CrBalance];
						//echo $Credit;
						$Credit=abs($Credit);
						//echo $Credit;
						$CategoryID=2;
						$SubCategoryID=3;
						$VoucherID=0;
						$VoucherTypeID=0;
						//$Date=$_POST['date'];
						
						$get_unit_id="select unit_id from unit where unit_no='".$unit_no."'";
						$result=$this->m_dbConn->select($get_unit_id);
						$unit_id=$result[0]['unit_id'];
						
						$insert_assetregister="insert into assetregister(Date,CategoryID,SubCategoryID,LedgerID,VoucherID,VoucherTypeID,Debit,Credit) 
						values('".$_POST['date']."','$CategoryID','$SubCategoryID','$unit_id','$VoucherID','$VoucherTypeID','$Debit','$Credit')";
						//echo '5';
						$data=$this->m_dbConn->insert($insert_assetregister);
				   		
						if($data <> '')
						{
							//echo "b4 import";
						$update_import_history="update `import_history` set memberdue_flag=1 where society_id='".$_SESSION['society_id']."'";							
						$data23=$this->m_dbConn->update($update_import_history);
						//echo "after import";
						}
				   
				   
				   }
					
			
		}
		
		
				
	}
						
	
		}
		
		
		//echo "file imported successfully..";
	}
	function getDateTime()
	{
		$dateTime = new DateTime();
		$dateTimeNow = $dateTime->format('Y-m-d H:i:s');
		return $dateTimeNow;
	}
}

?>