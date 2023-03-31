<?php

include_once("dbconst.class.php");
include_once("changelog.class.php");
include_once("society_import.class.php");
include_once("wing_import.class.php");
include_once("ledger_import.class.php");
include_once("unit_import.class.php");
include_once("member_import.class.php");
include_once("tarrif_import.class.php");
include_once("billdetails_import.class.php");
include_once("include/display_table.class.php");
include_once("utility.class.php");
include_once("bill_period.class.php");


class import 
{
	public $ErrorLogFile;
	public $m_dbConnRoot;
	public $m_dbConn;
	private $obj_default;
	public $obj_utility;
	public $actionPage = "../import_report.php";
	public $obj_billperiod;
	
	
	function __construct($m_dbConnRoot, $dbConn)
	{
		$this->m_dbConnRoot = $m_dbConnRoot;
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		$this->obj_default = new defaults($this->m_dbConn);
		$this->obj_utility= new utility($this->m_dbConn);
		$this->obj_billperiod = new bill_period($this->m_dbConn);
	}
	
	public function ImportData()
	{
		
		$errofile_name='import_errorlog_'.date("d.m.Y").'_'.rand().'.html';
		$this->ErrorLogFile=$errofile_name;
		$errorfile=fopen($errofile_name, "a");
		$tmp_array=array();	
		if(isset($_POST["Import"]))
		{
			
			if(isset($_POST['Cycle']) && isset($_POST['eperiod']) && isset($_POST['Year']) )
			{
					$PeriodName = '';		
					$IsPeriodsAdded = 'No';	
					$PeriodStatus = '';
					
					$FetchPeriod = $this->m_dbConn->select("select count(YearID) as count from `period` where `Billing_cycle`='".$_POST['Cycle']."' and `YearID`= '".$_POST['Year']."'");
				
											
					if($FetchPeriod[0]['count'] == 0)
					{ 
					
						$months = getMonths($_POST['Cycle']);
						
						$PrevYear =  $_POST['Year'] - 1;
						$sqlFetchData = $this->m_dbConn->select("SELECT * FROM `year`  where  `YearID`= '".$PrevYear."'");
						
						$begin_date = $this->obj_billperiod->getBeginDate(end($months),$sqlFetchData[0]['YearDescription']);
						$end_date = $this->obj_billperiod->getEndDate(end($months),$sqlFetchData[0]['YearDescription']); 
												
						$insert_query="insert into period(`Billing_cycle`,`Type`,`YearID`,`PrevPeriodID`,`IsYearEnd`,`BeginingDate`,`EndingDate` )
												 values(".$_POST['Cycle'].",'".end($months)."',".$PrevYear.",'0', '1','".$begin_date ."','".$end_date."')";
						$prevPeriod = $this->m_dbConn->insert($insert_query);	
						
						$this->obj_billperiod->setPeriod($months ,$_POST['Cycle'],$_POST['Year']);
						$IsPeriodsAdded = 'Yes';
					}
					else
					{
						$IsPeriodsAdded = 'No';	
						$PeriodStatus =  'Unable to generate  periods for  selected year because period already exists';
					}		
					
					if($IsPeriodsAdded == 'No')
					{
						$this->actionPage="../import.php";
						return $PeriodStatus;		
					}
					else
					{
						$sqlFetchData = $this->m_dbConn->select("select *  from `period` where `Billing_cycle`='".$_POST['Cycle']."' and `YearID`= '".$_POST['Year']."'");		
						$_POST['Period'] = $sqlFetchData[0]['ID'];
						if($_POST['Period']  == "")
						{
							$this->actionPage="../import.php";
							return $PeriodStatus;		
						}
					}
					
					$billingcycle="select `Description` from `billing_cycle_master` where `ID`='".$_POST['Cycle']."'";
					$resCycle=$this->m_dbConn->select($billingcycle);
					$getyear="SELECT `YearDescription` FROM `year` where `YearID`='".$_POST['Year']."' ";
					$resgetyear=$this->m_dbConn->select($getyear);
					$getperiod="SELECT `Type` FROM `period` where `ID`='".$_POST['Period']."'";
					$resgetperiod=$this->m_dbConn->select($getperiod);
					$int_method="";
					$rebate_method="";
					if($_POST['int_method']==1)
					{
						$int_method="INTEREST_METHOD_DELAY_DUE";
					}
					else
					{
						$int_method="INTEREST_METHOD_FULL_MONTH";
					}
					
					
					if($_POST['rebate_method']==1)
					{
						$rebate_method="REBATE_METHOD_NONE";
					}
					else
					{
						$rebate_method="REBATE_METHOD_FLAT";
					}
					
					$errormsg1='<html><table border=1px solid black><tr>';
					$errormsg1.='<td colspan="2">Import Society Data Form Fields:'.'</td></tr>';
					$errormsg1.='<tr><td>Billing Cycle</td><td>'.$resCycle[0]['Description'].'</td></tr>';
					$errormsg1 .='<tr><td>E Society Period</td><td>'.$_POST['eperiod'].'</td></tr>';
					$errormsg1 .='<tr><td>Data Import Into Year</td><td>'.$resgetyear[0]['YearDescription'].'</td></tr>';
					$errormsg1 .='<tr><td>Data Import Into Period</td><td>'.$resgetperiod[0]['Type'].'</td></tr>';
					$errormsg1 .='<tr><td>Bill Interest Rate</td><td>'.$_POST['int_rate'].'</td></tr>';
					$errormsg1 .='<tr><td>Bill Interest Method</td><td>'.$int_method.'</td></tr>';
					$errormsg1 .='<tr><td>Bill Rebate Method</td><td>'.$rebate_method.'</td></tr>';
					$errormsg1 .='<tr><td>Bill Rebate Amount</td><td>'.$_POST['rebate'].'</td></tr>';
					$errormsg1 .='<tr><td>Cheque Bounce Charges</td><td>'.$_POST['chq_bounce_charge'].'</td></tr>';
					$errormsg1 .='<tr><td>Periods Added For  '.$resgetyear[0]['YearDescription'].' </td><td>'.$IsPeriodsAdded.'</td></tr>';
					$errormsg1.='</table><br><br></html>';
					$errormsg=$errormsg1;
					$this->obj_utility->logGenerator($errorfile,'',$errormsg);
					
					$bUserExist = false;
					if($bUserExist == true)
					{
						
						return "<p> Already exist this username:  " . $_POST['admin_user']."</p>";	
					}
					else
					{	
											
						$valid_files=array('BuildingID','WingID','AccountMaster','FlatId','OwnerId','Tariff','BillMainPrevYear');
								$limit=count($_FILES['upload_files']['name']);
								$success=0;
								 
								 for($m=0;$m<$limit;$m++)
								 {
									 $filename=$_FILES['upload_files']['name'][$m];
									 $tmp_filename=$_FILES['upload_files']['tmp_name'][$m];
									 $bFileExists=false;
									for($i=0;$i<sizeof($valid_files);$i++)
									{
											
										$pos=strpos($filename,$valid_files[$i]);
										if($pos == false)
											{
												
											$message = $filename." is not a valid file";
											
											}
											else
											{
												$ext = pathinfo($filename, PATHINFO_EXTENSION);
												if($ext <> '' && $ext <> 'txt' && $ext <> 'csv')
												{	
														$this->actionPage="../import.php";
														$errormsg = $filename.'  Invalid file format selected. Expected *.txt or *.csv file format';
														$this->obj_utility->logGenerator($errorfile,'',$errormsg);
														return $filename.'  Invalid file format selected. Expected *.txt or *.csv file format';
												}
												else
												{
														$bFileExists=true;
														$success++;
														$tmp_array[$i]=$_FILES['upload_files']['tmp_name'][$m];
														break;
												}
												break;
											}
									}
									if($bFileExists==false)
									{
										$this->actionPage="../import.php";
										$message = $filename." is not a valid file";
										$this->obj_utility->logGenerator($errorfile,'',$message);
										return $message;	
									}
									
								 }
								 if($success == 7)
								 {
									 $logfile="";
									 for($n=0;$n<sizeof($tmp_array);$n++)
									 {
										$result=$this->startprocess($tmp_array[$n],$n,$errorfile);
									 	if($result <> '')
										 {
											 $this->actionPage="../import.php";
											 return $result;
											 
											}
					
									 }
								 }
								 else
								 {
									 if(sizeof($valid_files) > sizeof($tmp_array))
									 {
										 
											 $result=array_diff_key($valid_files,$tmp_array);
												$filesmissed='';
												foreach($result as $getkey=>$getval)
												{
													
													$filesmissed .= $result[$getkey].'  File is missing.';
													$this->obj_utility->logGenerator($errorfile,'',$filesmissed);
												}
												$this->actionPage="../import.php";
												return $filesmissed;
									}
									
								}
				
					}
					
					return 'Data Imported Succesfully..';
			}
			
			else
			{
				
			return "All Fields Are Required.."; 	
				
				
			}
			
			
		}
		
	}
	
	function startprocess($filename,$pos,$errorfile)
	{
		
		
		if($pos==0)
			 {
					$obj_society_import= new society_import($this->m_dbConnRoot, $this->m_dbConn);
					$import_result=$obj_society_import->UploadData($filename,$errorfile);
					if($import_result <> '')
					{
					return $import_result;
					}
			}
			 
			 
			 else if($pos==1  || $import_result=="Society already exist")
			 {
				 $obj_wing_import=new wing_import($this->m_dbConnRoot, $this->m_dbConn);
				 $import_result=$obj_wing_import->UploadData($filename,$errorfile);
			}
			 
			 else if($pos==2)
			 {
				 $obj_ledger_import=new ledger_import($this->m_dbConnRoot, $this->m_dbConn);
					 $import_result=$obj_ledger_import->UploadData($filename,$errorfile);
			 }
			else if($pos==3)
			 {
				 $obj_unit_import=new unit_import($this->m_dbConnRoot, $this->m_dbConn);
				 $import_result=$obj_unit_import->UploadData($filename,$errorfile);
			 	
			 }
			 
			 
			 else if($pos==4)
			 {
				 $obj_member_import=new member_import($this->m_dbConnRoot, $this->m_dbConn);
					 $import_result=$obj_member_import->UploadData($filename,$errorfile);
			 }
			 
		
			  else if($pos==5)
			 {
				 
				 $obj_tarrif_import=new tarrif_import($this->m_dbConnRoot, $this->m_dbConn);
					 $import_result=$obj_tarrif_import->UploadData_Custom($filename,$errorfile);
			 	
			 }
			 
			 else if($pos==6)
			 {
					$obj_billdetails_import=new billdetails_import($this->m_dbConnRoot, $this->m_dbConn);
					 $import_result=$obj_billdetails_import->UploadData($filename,$errorfile);
			 }
			 
			 else
			 {
				 
				return 'All Data Imported Successfully...'; 
				 
			}
			 
		
		
	}
	
	
	
	
	

}
?>