<?php
include_once("defaults.class.php");
include_once("utility.class.php");
set_time_limit(0);
ignore_user_abort(1);
class import_reverse_charges 
{
	public $m_dbConn;
	public $m_dbConnRoot;
	private $obj_default;
	private $obj_utility;
	public $actionPage = "../import_reverse_charge.php";
	public $errorLog;
	public $errorfile_name;	
	
	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
		$this->obj_default = new defaults($this->m_dbConn);
		$this->obj_utility = new utility($this->m_dbConn);
	}
	
	public function CSV_RC_Import()
	{		
		date_default_timezone_set('Asia/Kolkata');		
		$this->errorfile_name = 'rcf_import_errorlog_'.$_SESSION['client_id'].'.html';
		$this->errorLog = $this->errorfile_name;
		
		$errorfile = fopen($this->errorfile_name, "a");
		if(isset($_POST["Import"]))
		{
			if(isset($_FILES))
			{
				$result = "0";				
				$ext = pathinfo($_FILES['upload_files']['name'][0], PATHINFO_EXTENSION);
				$tempName = $_FILES['upload_files']['tmp_name'][0];
				if($ext <> '' && $ext <> 'csv')
				{	
					$result = '<p>Invalid file format selected. Expected csv file format</p>';					
				}
				else
				{					
					if (isset($_FILES['upload_files']['error'][0]) || is_array($_FILES['upload_files']['error'][0]))
					{  
						$result = '<p> Data Uploading Process Started <' . $this->getDateTime() . '> </p>';
						
						$result .= $this->UploadData_RC_F($tempName,$errorfile);
						
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
						   $result = '<p> The file is bigger than this PHP installation allows</p>';
						   break;
					case 2:
						   $result = '<p> The file is bigger than this form allows</p>';
						   break;
					case 3:
						   $result = '<p> Only part of the file was uploaded</p>';
						   break;
					case 4:
						   $result = '<p> No file was uploaded</p>';
						   break;
				}
			}
			return $result;			
		}
	}
	
	public function UploadData_RC_F($fileName,$errorfile)
	{
		$file = fopen($fileName,"r");	//open the file for reading
		$errormsg="[Importing Reverse Charge/Fine]<br>";
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		$errormsg = "Importing from file: ".$_FILES['upload_files']['name'][0].", at date and time: ".date("d/m/y h:i:s").".";	//msg to show data is being imported from where, at what time and date
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		try
		{
			while (($row = fgetcsv($file)) !== FALSE)
			{
				if($row[0] <> '')
				{
					$rowCount++;					
					if($rowCount == 1)	//read 1st row
					{
						$expectedCols = array('UnitNo','LedgerName','Amount','ChargeType','Comments');	//to check whether column heads in csv file are correct or not
						$count = 0;
						$errorCols = array();
						for($i=0;$i<sizeof($row);$i++)
						{
							if(in_array($row[$i], $expectedCols))
							{
								//echo "Found:".$row[$i];
								$count++;
							}
							else
							{
								$errorCols[] = $row[$i];
							}
						}
						if($count == 5) 	//if all 5 columns match, then only read
						{
							$UnitNo = array_search(UnitNo,$row,true);
							$LedgerName = array_search(LedgerName,$row,true);
							$Amount = array_search(Amount,$row,true);
							$ChargeType = array_search(ChargeType,$row,true);
							$Comments = array_search(Comments,$row,true);
						}
						else 	//else show error and exit
						{
							$result = 'Required Column Names Not Found in the file being imported. Cannot Proceed Further!';
							for($z=0;$z<sizeof($errorCols);$z++)
							{
								$errormsg = "Erroneous column name is:".$errorCols[$z];
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							}
							$errormsg = "Required Column Names Not Found in the file being imported.";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							return $result;
							exit(0);
						}						
						/*$UnitNo = array_search(UnitNo,$row,true);
							$LedgerName = array_search(LedgerName,$row,true);
							$Amount = array_search(Amount,$row,true);
							$ChargeType = array_search(ChargeType,$row,true);
							$Comments = array_search(Comments,$row,true);											
							
							if(!isset($UnitNo) || !isset($LedgerName) || !isset($Amount) || !isset($ChargeType) || !isset($Comments))
							{
								$result = "Column Names Not Found, Cannot Proceed Further.";
								$errormsg = "Column Names in file to be imported does not match the column names in Sample file.";
								$this->obj_utility->logGenerator($errorfile,"-",$errormsg,"E");
								return $result;
								exit(0);
							}*/
					}
					else 	//read from 2nd row onwards
					{							
						$UnitNo1 = $row[$UnitNo];	
						$LedgerName1 = $row[$LedgerName];
						$Amount1 = $row[$Amount];
						$ChargeType1 = $row[$ChargeType];
						$Comments1 = $row[$Comments];

						$prohibited = array("&","#","$","%","^","*","(",")","!","@","_","+",",");
						$prohibited_count = 0;
						for($i=0;$i<strlen($Comments1);$i++)
						{
							if(in_array($Comments1[$i],$prohibited))
							{
								$prohibited_count++;
							}
						}
						
						if($prohibited_count > 0)
						{
							$errormsg = "Comment on line no.: &lt;".$rowCount."&gt contains special character.";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
						}
						
						$Bill_Type = $_POST['bill_type'];
						$Bill_for = $_POST['bill_for'];
						$periodid = $Bill_for - 1;	//to get previous period id
						
						$is_numeric = false;
						$wrong_spelling = false;
						$is_neg_num = false;
						if(is_numeric($Amount1))	//to check amount is numeric
						{
							if($Amount1 > 0)
							{
								if(strtolower($ChargeType1) == "reverse charges") 	//if reverse charges, amount to be in negative
								{
									$Amount1 = -($Amount1);
									$to_insert_charge_type = 1;
								}
								else if(strtolower($ChargeType1) == "fine")
								{
									$to_insert_charge_type = 2;
								}
								else	//to check spelling of reverse charge and fine
								{
									$wrong_spelling = true;
									$errormsg = "Please check spelling of Reverse Charge and Fine on line no.: &lt;".$rowCount."&gt;.";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								}
								$is_numeric = true;
							}
							else
							{
								$errormsg = "Please enter positive amounts only.";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								$is_neg_num = true;
							}
						}
						else	//if Amount is not numeric
						{
							$errormsg = "Invalid Amount on line no.: &lt;".$rowCount."&gt;. Please correct the amount.";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E"); 
							$is_numeric = false;
						}
							
						$get_bill_date="select DISTINCT BillDate from `billregister` where PeriodID=".$periodid." and SocietyID=".$_SESSION['society_id']." ";
						$data = $this->m_dbConn->select($get_bill_date);
						
						$unitid = "";
						$sql01 = "select id from `ledger` where ledger_name = '".$UnitNo1."'";
						$sql11 = $this->m_dbConn->select($sql01);
						if($sql11 == "")
						{
							$errormsg = "Unit No.: &lt;".$UnitNo1."&gt; not found.<br>";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
						}
						else
						{
							$unitid = $sql11[0]['id'];
						}
							
						$ledgerid = "";
						$sql02 = "select id from `ledger` where ledger_name = '".$LedgerName1."'";
						$sql22 = $this->m_dbConn->select($sql02);
						if($sql22 == "")
						{
							$errormsg = "Ledger Name: &lt;".$LedgerName1."&gt; not found.<br>";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
						}
						else
						{
							$sql03 = "select l.id, l.categoryid, l.show_in_bill, ac.category_id, ac.group_id FROM `ledger` l, `account_category` ac where ledger_name = '".$LedgerName1."' and l.categoryid = ac.category_id";
							$sql33 = $this->m_dbConn->select($sql03);
							if($sql33[0]['group_id'] == 3)
							{
								if($sql33[0]['show_in_bill'] == 0)
								{
									$errormsg = "Show in bill property of the ledger &lt;".$LedgerName1."&gt; is not checked. You may check it if you want it to be shown in the bill.";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"W");
								}
								$ledgerid = $sql22[0]['id'];								
							}
							else
							{
								$errormsg = "The group of the ledger name &lt;".$LedgerName1."&gt; is not Income. Connot be imported.";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							}
							
						}
						
						/*echo "BillDate: ".$data[0]['BillDate']."<br>";
						echo "unitid: ".$unitid."<br>";
						echo "Amount: ".$Amount1."<br>";
						echo "ledgerid: ".$ledgerid."<br>";
						echo "comments: ".$Comments1."<br>";
						echo "bill type: ".$Bill_Type."<br>";
						echo "charge type: ".$to_insert_charge_type."<br>";*/
							
						if($unitid <> "" && $ledgerid <> "" && $is_numeric == true && $wrong_spelling == false && $is_neg_num == false && $prohibited_count == 0)		//insert
						{
							$sql_insert = "insert into `reversal_credits`(`Date`,`UnitID`,`Amount`,`LedgerID`,`Comments`,`BillType`,`ChargeType`,`PeriodID`,`ReportedBy`) values ('".$data[0]['BillDate']."','".$unitid."','".$Amount1."','".$ledgerid."','".$Comments1."','".$Bill_Type."','".$to_insert_charge_type."','".$Bill_for."','".$_SESSION['name']."')";
							$sql_insert_done = $this->m_dbConn->insert($sql_insert);
							if($sql_insert_done <> "")
							{
								$errormsg = "Reverse Charge/Fine inserted successfully for Unit No.: &lt;".$UnitNo1."&gt; for Ledger Name: &lt;".$LedgerName1."&gt;.";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");
							}
						}
						else
						{
							$errormsg = "Record on line no.: &lt;".$rowCount."&gt; not inserted, please check Unit No, Amount, Spelling of Reverse Charge/Fine and Ledger Name.";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
						}
					}
				}
			}
		}
		catch(Exception $exp)
		{
			echo $exp->getMessage();
		}
		$errormsg="[End of Reverse Charge/Fine]";
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
		$data = $this->m_dbConn->select($query);
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