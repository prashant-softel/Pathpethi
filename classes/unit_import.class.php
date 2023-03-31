<?php
//include_once("include/dbop.class.php");
include_once("dbconst.class.php");
include_once("utility.class.php");
include_once("register.class.php");
class unit_import 
{
	
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_utility;
        public $obj_register;
	
	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
		$this->obj_utility = new utility($this->m_dbConn);
                $this->obj_register = new regiser($this->m_dbConn);
	}
	
	public function CSVUnitImport($sid)
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
				$original_file_name='FaltID.csv';
				//echo $_FILES['file'] ['name'];
				if(($_FILES['file'] ['name']) != "$original_file_name") {
					  //exit("Does not match");
					  $result = '<p>File Name Does Not Match(only FlatID.csv file accepted)...</p>';
					  
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
						$result = '<p> Unit Data Uploading Process Started <' . $this->getDateTime() . '> </p>';
						
						$result .= $this->UploadData($tempName);
						
						$result .= '<p> Unit Data Uploading Process Complete <' . $this->getDateTime() . '> </p>';
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
	
	public function UploadData($fileName,$errorfile)
	{
		$file = fopen($fileName,"r");
		$SortOrderID=0;
		$errormsg="[Importing FlatID]";
		$isImportSuccess = false;
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		$sql00="select unit_flag from `import_history` where society_id='".$_SESSION['society_id']."'";
		$res01=$this->m_dbConn->select($sql00);
		if($res01[0]['unit_flag']==0)
		{	
			while (($row = fgetcsv($file)) !== FALSE)
			{
				if($row[0] <> '')
					{
						$rowCount++;
						if($rowCount == 1)
						{
									$BCode=array_search(BCode,$row,true);
									$WCode=array_search(WCode,$row,true);			
									$FCode=array_search(FCode,$row,true);
									$UnitType=array_search(UnitType,$row,true);
									$FloorNo=array_search(FloorNo,$row,true);
									$FlatArea=array_search(FlatArea,$row,true);
									$CarpetArea=array_search(CarpetArea,$row,true);
									$CommercialArea=array_search(CommercialArea,$row,true);
									$ResidentialArea=array_search(ResidentialArea,$row,true);
									$TerraceArea=array_search(TerraceArea,$row,true);
								   
								   
								if(!isset($BCode) || !isset($WCode)  || !isset($FCode) || !isset($UnitType) || !isset($FloorNo) || !isset($FlatArea) || !isset($CarpetArea) || !isset($CommercialArea) || !isset($ResidentialArea) || !isset($TerraceArea))
								{
									$result = '<p>Column Names Not Found Cant Proceed Further......</p>'.'Go Back';
									$errormsg="Column names  in file FlatId not match";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
									return $result;
									exit(0);
								}
							
						}
						else
					   {
							$society_code=$row[$BCode];
							$wing_code=$row[$WCode];
							$unit_no=$row[$FCode];
							$floor_no=$row[$FloorNo];
							$unit_type=$row[$UnitType];
							$area=$row[$FlatArea];
							$carpet=$row[$CarpetArea];
							$commercial=$row[$CommercialArea];
							$residential=$row[$ResidentialArea];
							$terrace=$row[$TerraceArea];
							
							$search_society_code="select society_id from society where society_code='".$society_code."'";
							$data2=$this->m_dbConn->select($search_society_code);
							if($data2=='')
							{
								$errormsg="society id  not found  for society code: &lt;".$society_code."&gt;";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"W");
							}
							
							$society_id=$data2[0]['society_id'];
							
							$serach_wing_code="select wing_id from wing where wing ='".$wing_code."' and society_id='".$society_id."'";
							$data3=$this->m_dbConn->select($serach_wing_code);
							
							if($data3=='')
							{
								$errormsg="wing id not found for wing code: &lt;".$wing_code."&gt; and society &lt;".$society_code."&gt; ";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"W");
							}
							$wing_id=$data3[0]['wing_id'];
							
							$LedgerName=$unit_no;
							//$chk_ledger_presence="select `id` from `ledger` where `ledger_name`='".$LedgerName."' and society_id='".$_SESSION['society_id']."'";
							//$ledger_exists=$this->m_dbConn->select($chk_ledger_presence);
							$chk_ledger_presence="select `unit_id` from `unit` where `unit_no`='".$LedgerName."' and society_id='".$_SESSION['society_id']."' and `wing_id`='" . $wing_id . "'";
							$ledger_exists=$this->m_dbConn->select($chk_ledger_presence);
							
							$chk_mapping_presence="select Count(*)  as cnt from `mapping` where `unit_id`='".$unit_id."' and society_id='".$_SESSION['society_id']."'";
							$mapping_exists=$this->m_dbConnRoot->select($chk_mapping_presence);
							
							$SortOrderID =$SortOrderID + 100;
							if($ledger_exists <>'' && ($society_id <> "" || $society_id <> 0) && ($wing_id <> "" || $wing_id <> 0))
							{
								$errormsg="Ledger name &lt;" .$LedgerName."&gt; already exists in ledger table with ledger_id as &lt;" .$ledger_exists[0]['id']."&gt;";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");	
								$unit_id=$ledger_exists[0]['id'];
								$insert_unit="insert into unit(unit_id,society_id,wing_id,unit_no,floor_no,unit_type,area,carpet,commercial,residential,terrace,sort_order) values('$unit_id','$society_id','$wing_id','$unit_no','$floor_no','$unit_type','$area','$carpet','$commercial','$residential','$terrace',".$SortOrderID.")";
								$data=$this->m_dbConn->insert($insert_unit);
								$isImportSuccess = true;
								
								if($mapping_exists[0]['cnt'] == 0)
								{
									$insert_mapping = "INSERT INTO `mapping`(`society_id`, `unit_id`, `desc`, `code`, `role`, `created_by`, `view`) VALUES ('" . $society_id . "', '" . $unit_id . "', '" . $unit_no . "', '" . getRandomUniqueCode() . "', '" . ROLE_MEMBER . "', '" . $_SESSION['login_id'] . "', 'MEMBER')";
									$result_mapping = $this->m_dbConnRoot->insert($insert_mapping);
								}
							}
							else if(($society_id <> "" || $society_id <> 0) && ($wing_id <> "" || $wing_id <> 0))
							{
								$unitLedgerName = $unit_no;
								$Date = $this->get_date($_POST['Period']);
								$sqlInsert = "INSERT INTO `ledger`(`society_id`,`categoryid`,`ledger_name`,`opening_type`,`opening_date`,`receipt`) VALUES ('$society_id',4, '" . $unitLedgerName . "','2','".getDBFormatDate($Date)."','1')";	
								$data4=$this->m_dbConn->insert($sqlInsert);
                                                                $insertAsset = $this->obj_register->SetAssetRegister(getDBFormatDate($Date),$data4, 0, 0, TRANSACTION_DEBIT, 0, 1);
								$isImportSuccess = true;
								$insert_unit="insert into unit(unit_id,society_id,wing_id,unit_no,floor_no,unit_type,area,carpet,commercial,residential,terrace,sort_order) values('".$data4."','$society_id','$wing_id','$unit_no','$floor_no','$unit_type','$area','$carpet','$commercial','$residential','$terrace',".$SortOrderID.")";
								$data=$this->m_dbConn->insert($insert_unit);
								
								if($mapping_exists[0]['cnt'] == 0)
								{
									$insert_mapping = "INSERT INTO `mapping`(`society_id`, `unit_id`, `desc`, `code`, `role`, `created_by`, `view`) VALUES ('" . $society_id . "', '" . $unit_id . "', '" . $unit_no . "', '" . getRandomUniqueCode() . "', '" . ROLE_MEMBER . "', '" . $_SESSION['login_id'] . "', 'MEMBER')";
									$result_mapping = $this->m_dbConnRoot->insert($insert_mapping);
								}
							}
							else
							{
								$errormsg="Unit &lt;".$unit_no."&gt; not imported check if society code &lt;".$society_code."&gt; match with BCode in BuildingID file or wing Code &lt;".$wing_code." &gt; match with WCode in WingID file";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");	
							}
						 }
				
						}
			
				}
			}
			
			if($isImportSuccess)
			{
				$update_import_history="update `import_history` set unit_flag=1 where society_id='".$_SESSION['society_id']."'";
				$res123=$this->m_dbConn->update($update_import_history);
			}
			else
			{
				$errormsg="unit details not imported";
				$this->obj_utility->logGenerator($errorfile,'Error',$errormsg,"E");	
			}	
			$errormsg="[End of  FlatID]";
			$this->obj_utility->logGenerator($errorfile,'End',$errormsg);
	}
	function getDateTime()
	{
		$dateTime = new DateTime();
		$dateTimeNow = $dateTime->format('Y-m-d H:i:s');
		return $dateTimeNow;
	}
	
	public function get_date($id)
	{
		$sql = "select `BeginingDate`- INTERVAL 1 DAY  as BeginingDate from `period` where  id=".$id." ";
		$data = $this->m_dbConn->select($sql);
		return $data[0]['BeginingDate'];
	}

}

?>