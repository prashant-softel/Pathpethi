<?php
include_once("defaults.class.php");
include_once("utility.class.php");
include_once("billmaster.class.php");
set_time_limit(0);
ignore_user_abort(1);
class tarrif_import 
{
	public $m_dbConn;
	public $m_dbConnRoot;
	private $obj_default;
	private $obj_utility;
	private $obj_billmaster;
	public $actionPage = "../import_tariff.php";
	public $errorLog;
	public $errorfile_name;	
	
	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
		$this->obj_default = new defaults($this->m_dbConn);
		$this->obj_utility = new utility($this->m_dbConn);
		$this->obj_billmaster = new billmaster($this->m_dbConn);		
	}
	
	public function DownloadCSV()
	{
		$get_all = "SELECT s.society_code, w.wing, u.unit_no FROM `wing` w, `unit` u, `society` s where s.society_id='".$_SESSION['society_id']."' and u.wing_id = w.wing_id";		
		$get_all1 = $this->m_dbConn->select($get_all);		
		$getledgers="SELECT a.`category_id`, l.`ledger_name` FROM `ledger` l, `account_category` a where a.`category_name`='Contributions from members' and a.category_id=l.categoryid";
		$getledgers1=$this->m_dbConn->select($getledgers);
		$ledger=array('BCode','WCode','FCode');
		$j=3;
		for($i=0;$i<sizeof($getledgers1);$i++)
		{
			$ledger[$j]=$getledgers1[$i]['ledger_name'];
			$j++;
		}
		header('Content-Type: text/csv; charset=utf-8');
    	header('Content-Disposition: attachment; filename=Tariff.csv');
		ob_end_clean();
		$output = fopen("php://output","w");		
    	fputcsv($output, $ledger); 
		
		foreach($get_all1 as $value)
		{
			fputcsv($output, $value);
		}
		fclose($output);
		exit();		
		//$result="Done.";
		//return $result;
	}
	
	public function CSVTarrifImport()
	{
		date_default_timezone_set('Asia/Kolkata');		
		$this->errorfile_name = 'tariff_import_errorlog_'.date("d.m.Y").'_'.rand().'.html';
		$this->errorLog = $this->errorfile_name;
		
		$errorfile = fopen($this->errorfile_name, "a");
		//echo "inside tariff import";
		//print_r($_SESSION);
		if(isset($_POST["Import"]))
		{
			//echo 'Inside CSVTariffImport';						
			if(isset($_FILES)) //&& $_FILES['upload_files']['error'] == 0)
			{
				 $result = "0";				
				 $ext = pathinfo($_FILES['upload_files']['name'][0], PATHINFO_EXTENSION);
				 //$fileName = "files/" . $dateTimeNow. ".csv";
				 $tempName = $_FILES['upload_files']['tmp_name'][0];
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
					
					if (isset($_FILES['upload_files']['error'][0]) || is_array($_FILES['upload_files']['error'][0]))
					{  
						$result = '<p> Data Uploading Process Started <' . $this->getDateTime() . '> </p>';
						
						$method = $_POST['methodofimport'];
						//$lifetime = $_POST['lifetime'];
						//echo "Lifetime: ".$lifetime;												
						//echo "Check: " .PHP_MAX_DATE;
						//die();
						if($method=='Way2Society')
						{
							$result .= $this->UploadData_W2S($tempName,$errorfile);
						}
						else if($method=='Custom')
						{
							$result .= $this->UploadData_Custom($tempName,$errorfile);
						}						
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
	
	public function UploadData_W2S($fileName,$errorfile)
	{
		$file = fopen($fileName,"r");		
		$errormsg="[Importing Tarrif]";
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		$isImportSuccess = false;
		//$sql00="select tarrif_flag from `import_history` where society_id='".$_SESSION['society_id']."'";
		//$res01=$this->m_dbConn->select($sql00);		
		$res01=0;
		$exists = array();
		$counterE = 0;
		$counterI = 0;
		$counterU = 0;
		try
		{
			if($res01==0)
			{			
				while (($row = fgetcsv($file)) !== FALSE)
				{
					if($row[0] <> '')
					{
						$rowCount++;					
						if($rowCount == 1)
						{						
							$legder = array();
							$WCode=array_search(WCode,$row,true);
							$BCode=array_search(BCode,$row,true);
							$FCode=array_search(FCode,$row,true);						
							$j = 0;
							for($i=4;$i<sizeof($row);$i++)
							{							
								$legder[$j] = $row[$i];													
								$legder_no[$j]=array_search($legder[$j],$row,true);							
								$j++;
							}						
						//$EffectiveDate = array_search(EffectiveDate,$row,true);
						 //die();
						/*if(!isset($WCode) || !isset($FCode) || !isset($BCode) || !isset($Particulars) || !isset($AccountName) || !isset($Rate) || !isset($EffectiveDate))
						{
							$result = '<p>Column Names Not Found Cant Proceed Further......</p>'.'Go Back';
							$errormsg=" Column names  in file Tarrif not match";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							return $result;
							exit(0);
						}*/
						}
						else
						{							
							$UnitNo=$row[$FCode];	
							$t = in_array($UnitNo,$exists);
							if($t == FALSE)
							{
								$exists[] = $UnitNo;
								$society_code=$row[$BCode];
								$wing=$row[$WCode];						
								$legder_rate = array();						
								for($i=0;$i<sizeof($legder);$i++)
								{
									$legder_rate[$i] = $row[$legder_no[$i]];							
								}
								/*for($z=0;$z<sizeof($legder);$z++)
								{
									echo "Legder name: ".$legder[$z]."<br>";
									echo "Legder no: ".$legder_no[$z]."<br>";
									echo "Legder rate: ".$legder_rate[$z]."<br>";
								}
								die();*/
								$sql00="select society_id from `society` where society_code='".$society_code."'";
								$data00=$this->m_dbConn->select($sql00);
								$society_id=$data00[0]['society_id'];						
					
								$sql="select unit_id from `unit` where unit_no='".$UnitNo."' and society_id='".$society_id."' ";
								$data=$this->m_dbConn->select($sql);
								$UnitID=$data[0]['unit_id'];
								if($UnitID <> '')
								{
									for($k=0;$k<sizeof($legder);$k++)
									{
										$search_account_head="select id from `ledger` where ledger_name='".$legder[$k]."' and categoryid = 2";
										$AccountHead=$this->m_dbConn->select($search_account_head);
									
										if($AccountHead == 0)
										{
											$create = $_POST['create_l'];
											if($create == 'on')
											{														
												$get_category_id="select category_id from `account_category` where category_name='Contributions from Members'";
												$category_id_array=$this->m_dbConn->select($get_category_id);
									
												if($category_id_array == 0)
												{
													$errormsg = "Category &lt; Contributions from Members &gt; not found. Please add the category first.";
													$counterE++;
													$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
												}
												else
												{
													$category_id=$category_id_array[0]['category_id'];
										
													$insert_if_not_found="insert into `ledger`(society_id,categoryid,ledger_name,show_in_bill,taxable,sale,purchase,income,expense,payment,receipt,opening_type,opening_balance,opening_date) values('".$_SESSION['society_id']."','".$category_id."','".$legder[$k]."',1,0,1,0,1,0,0,1,0,0.00,'".$_SESSION['default_year_start_date']."')";
													$legder_insert=$this->m_dbConn->insert($insert_if_not_found);
											
													$errormsg = "Created new legder &lt; ".$legder[$k]." &gt;";
													$counterI++;
													$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"C");
									
													$search_account_head="select id from `ledger` where ledger_name='".$legder[$k]."' ";
													$AccountHead=$this->m_dbConn->select($search_account_head); 
												}
											}
											else
											{
												$errormsg = "Ledger name &lt;".$legder[$k]."&gt; not found. Please tick the 'Create new ledger' checkbox to create ledgers that were not found.";
												$counterE++;
												$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
											}
										}								
										$HeadID=$AccountHead[0]['id'];
										if($HeadID != 0)
										{
											$bf=$_POST['bill_for'];
											$period = $bf;
											$start_period = $bf;
											$end_period = $bf;
											
											$lifetime = $_POST['period'];											
											if($lifetime == 'Lifetime')
											{
												$end_period = 0;
											}
											/*echo "Unit id: ".$UnitID."<br>";
											echo "AccHead: ".$HeadID."<br>";
											echo "Amount: ".$legder_rate[$k]."<br>";
											echo "Period: ".$period."<br>";
											echo "Start period: ".$start_period."<br>";
											echo "End period: ".$end_period."<br>";
											die();*/	
											$result = $this->obj_billmaster->update_billmaster($UnitID, $HeadID, $legder_rate[$k], $period, $start_period, $end_period, 0);																								
										}								
									}
									$isImportSuccess = true;
									$bSuccess = true;									
						/*$bUpdateAmount = true;
						if(!array_key_exists($UnitID, $aryMain))
						{
							$aryMain[$UnitID] = array("date"=>$EffDate, "data"=>array());
						}
						else
						{
							$date1 = date("Y-m-d", strtotime($aryMain[$UnitID]['date'])) . ' ';
							$date2 = date("Y-m-d", strtotime($EffDate)) . ' ';
							$dateDiff = $this->obj_utility->getDateDiff($date1, $date2);
							if($dateDiff <= 0)
							{
								$aryMain[$UnitID]['date'] = $EffDate;
								if($dateDiff <> 0)
								{
									unset($aryMain[$UnitID]['data']);
									$aryMain[$UnitID]['data'] = array();
								}
							}
							else
							{
								$bUpdateAmount = false;
							}
						}*/
						
						/*if($bUpdateAmount)
						{
							if($AccountHeadAmount <> 0)
							{
								if(!array_key_exists($aryMain[$UnitID]['data'][$HeadID], $aryMain[$UnitID]['data']))
								{
									$aryMain[$UnitID]['data'][$HeadID] = $AccountHeadAmount;
								}
								else
								{
									$aryMain[$UnitID]['data'][$HeadID] = $AccountHeadAmount;
								}
							}
						}*/						
								}					
							}
							else
							{
								$errormsg = "Tariff already imported for Unit No. &lt;".$UnitNo."&gt; from the current file.";
								$counterE++;
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							}
						}					
					}
				}			
			/*$bSuccess = false;
			foreach($aryMain as $k=>$v)
			{
				$bHasValues = false;
				$insertStatement = '';
				$iCounter = 1;
				if(sizeof($v['data']) > 0)
				{
					foreach($v['data'] as $head=>$amount)
					{
						$bHasValues = true;
						if($iCounter == 1)
						{
							$insertStatement = ' ("' . $k . '", "' . $_SESSION['login_id'] . '", 0, "' . $head . '", "' . $amount . '")';
						}
						else
						{
							$insertStatement .= ',("' . $k . '", "' . $_SESSION['login_id'] . '", 0, "' . $head . '", "' . $amount . '")';
						}
						$iCounter++;
						
					}
					$insert_unitbillmaster="insert into `unitbillmaster`(UnitID,CreatedBy,LatestChangeID,AccountHeadID,AccountHeadAmount) values " . $insertStatement ;
					
					//$this->obj_utility->logGenerator($errorfile,$rowCount,$insert_unitbillmaster,"W");  
					
					$data1=$this->m_dbConn->insert($insert_unitbillmaster);
					$isImportSuccess = true;
					$bSuccess = true;
				}
		
			
			}*/			
				if($bSuccess)
				{
					$update_import_history="update `import_history` set tarrif_flag=1 where society_id='".$_SESSION['society_id']."'";
					$data23=$this->m_dbConn->update($update_import_history);						
				}
				else
				{
					$errormsg="Tarrif details not imported.";
					$counterE++;
					$this->obj_utility->logGenerator($errorfile,'Error',$errormsg,"E");	
				}			
			}
		}
		catch(Exception $exp)
		{
			echo $exp->getMessage();
		}
		/*$errormsg = "Number of errors: <font color='#FF0000'>".$counterE."</font><br>";
		$this->obj_utility->logGenerator($errorfile,'End',$errormsg);
		$errormsg = "Number of inserts: <font color='#006600'>".$counterI."</font><br>";
		$this->obj_utility->logGenerator($errorfile,'End',$errormsg);
		$errormsg = "Number of updates: <font color='#FFCC00'>".$counterU."</font><br>";
		$this->obj_utility->logGenerator($errorfile,'End',$errormsg);*/
		$errormsg="[End of Tarrif]";
		$this->obj_utility->logGenerator($errorfile,'End',$errormsg);	
	}
	
	public function UploadData_Custom($fileName,$errorfile)
	{
		$file = fopen($fileName,"r");
		$errormsg="[Importing Tarrif]";
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		$isImportSuccess = false;
		$sql00="select tarrif_flag from `import_history` where society_id='".$_SESSION['society_id']."'";
		$res01=$this->m_dbConn->select($sql00);
		if($res01[0]['tarrif_flag']==0)
		{
			$aryMain = array();
			
			while (($row = fgetcsv($file)) !== FALSE)
			{
				if($row[0] <> '')
				{
					$rowCount++;
					if($rowCount == 1)
					{
						$WCode=array_search(WCode,$row,true);
						$BCode=array_search(BCode,$row,true);
						$FCode=array_search(FCode,$row,true);
						$Particulars=array_search(Particulars,$row,true);
						$AccountName=array_search(AccountName,$row,true);
						$Rate=array_search(Rate,$row,true);
						$EffectiveDate = array_search(EffectiveDate,$row,true);
						 
						if(!isset($WCode) || !isset($FCode) || !isset($BCode) || !isset($Particulars) || !isset($AccountName) || !isset($Rate) || !isset($EffectiveDate))
						{
							$result = '<p>Column Names Not Found Cant Proceed Further......</p>'.'Go Back';
							$errormsg=" Column names  in file Tarrif not match";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							return $result;
							exit(0);
						}
					}
					else
					{	
						$UnitNo=$row[$FCode];
						$society_code=$row[$BCode];
						$wing=$row[$WCode];
						$ledger_name=$row[$Particulars];
						$account_category=$row[$AccountName];
						$AccountHeadAmount=$row[$Rate];
						$EffDate = $row[$EffectiveDate];
						$sql00="select society_id from `society` where society_code='".$society_code."'";
						$data00=$this->m_dbConn->select($sql00);
						$society_id=$data00[0]['society_id'];
					
						$sql="select unit_id from `unit` where unit_no='".$UnitNo."' and society_id='".$society_id."' ";
						$data=$this->m_dbConn->select($sql);
						$UnitID=$data[0]['unit_id'];
						if($UnitID <> '')
						{
				
						$search_account_head="select id from `ledger` where ledger_name='".$ledger_name."' ";
						$AccountHead=$this->m_dbConn->select($search_account_head);
						if($AccountHead == '')
						{
							$errormsg = "Ledger id not found for ledger  name &lt;" .$ledger_name."&gt  please check if  account name match with ledger name in tarrif  file " ;
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");  		
						} 
						
						$HeadID=$AccountHead[0]['id'];
						$bUpdateAmount = true;
						if(!array_key_exists($UnitID, $aryMain))
						{
							$aryMain[$UnitID] = array("date"=>$EffDate, "data"=>array());
						}
						else
						{
							$date1 = date("Y-m-d", strtotime($aryMain[$UnitID]['date'])) . ' ';
							$date2 = date("Y-m-d", strtotime($EffDate)) . ' ';
							$dateDiff = $this->obj_utility->getDateDiff($date1, $date2);
							if($dateDiff <= 0)
							{
								$aryMain[$UnitID]['date'] = $EffDate;
								if($dateDiff <> 0)
								{
									unset($aryMain[$UnitID]['data']);
									$aryMain[$UnitID]['data'] = array();
								}
							}
							else
							{
								$bUpdateAmount = false;
							}
						}
						
						if($bUpdateAmount)
						{
							if($AccountHeadAmount <> 0)
							{
								if(!array_key_exists($aryMain[$UnitID]['data'][$HeadID], $aryMain[$UnitID]['data']))
								{
									$aryMain[$UnitID]['data'][$HeadID] = $AccountHeadAmount;
								}
								else
								{
									$aryMain[$UnitID]['data'][$HeadID] = $AccountHeadAmount;
								}
							}
						}
						
						}
					}
			
					
				}
			}
			
			$bSuccess = false;
			foreach($aryMain as $k=>$v)
			{
				$bHasValues = false;
				$insertStatement = '';
				$iCounter = 1;
				if(sizeof($v['data']) > 0)
				{
					foreach($v['data'] as $head=>$amount)
					{
						$bHasValues = true;
						if($iCounter == 1)
						{
							$insertStatement = ' ("' . $k . '", "' . $_SESSION['login_id'] . '", 0, "' . $head . '", "' . $amount . '")';
						}
						else
						{
							$insertStatement .= ',("' . $k . '", "' . $_SESSION['login_id'] . '", 0, "' . $head . '", "' . $amount . '")';
						}
						$iCounter++;
						
					}
					$insert_unitbillmaster="insert into `unitbillmaster`(UnitID,CreatedBy,LatestChangeID,AccountHeadID,AccountHeadAmount) values " . $insertStatement ;
					
					//$this->obj_utility->logGenerator($errorfile,$rowCount,$insert_unitbillmaster,"W");  
					
					$data1=$this->m_dbConn->insert($insert_unitbillmaster);
					$isImportSuccess = true;
					$bSuccess = true;
				}
		
			
			}
			
			if($bSuccess)
			{
				$update_import_history="update `import_history` set tarrif_flag=1 where society_id='".$_SESSION['society_id']."'";
				$data23=$this->m_dbConn->update($update_import_history);						
			}
			else
			{
				$errormsg="tarrif details not imported";
				$this->obj_utility->logGenerator($errorfile,'Error',$errormsg,"E");	
			}	
			
		}
		
		$errormsg="[End of  Tarrif]";
		$this->obj_utility->logGenerator($errorfile,'End',$errormsg);
	}
	
	function getDateTime()
	{
		$dateTime = new DateTime();
		$dateTimeNow = $dateTime->format('Y-m-d H:i:s');
		return $dateTimeNow;
	}
	
	public function combobox($query, $id, $defaultText = 'Please Select', $defaultValue = '')
	{
		$str = '';
		
		/*if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "'>" . $defaultText . "</option>";
		}*/
		//echo "$query";
		$data = $this->m_dbConn->select($query);
		//print_r($data);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($id==$v)
						{
							$sel = 'selected';
						}
						else
						{
							$sel = '';
						}
						
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
					}
					else
					{
						$str.=$v."</OPTION>";
					}
					$i++;
				}
			}
		}
		return $str;
	}
}

?>