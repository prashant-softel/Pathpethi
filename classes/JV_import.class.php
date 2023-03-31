<?php
include_once("defaults.class.php");
include_once("utility.class.php");
include_once("createvoucher.class.php");
set_time_limit(0);
ignore_user_abort(1);
class JV_import 
{
	public $m_dbConn;
	public $m_dbConnRoot;
	private $obj_default;
	private $obj_utility;
	public $obj_createvoucher;
	public $actionPage = "../import_JV.php";
	public $errorLog;
	public $errorfile_name;	
		
	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
		$this->obj_default = new defaults($this->m_dbConn);
		$this->obj_utility = new utility($this->m_dbConn);
		$this->obj_createvoucher = new createVoucher($this->m_dbConn);		
	}	
	
	public function CSVJVImport()
	{
		date_default_timezone_set('Asia/Kolkata');		
		$this->errorfile_name = 'JV_import_errorlog_'.date("d.m.Y").'.html';
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
					$result = '<p>Invalid file format selected. Expected csv file format.</p>';					
				}
				else
				{
					if (isset($_FILES['upload_files']['error'][0]) || is_array($_FILES['upload_files']['error'][0]))
					{  
						$result = '<p> Data Uploading Process Started <' . $this->getDateTime() . '> </p>';
						
						$result .= $this->UploadData_JV($tempName,$errorfile);
						
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
	
	public function UploadData_JV($fileName,$errorfile)
	{
		//echo "Inside UploadData_JV";
		$file = fopen($fileName,"r");		
		$errormsg="[Importing JV]";
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		$errormsg = "Importing from file: ".$_FILES['upload_files']['name'][0].", at date and time: ".date("d/m/y h:i:s").".";
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		//pending done call file name, timestamp
		
		$arData = array();
		$iSubCount = 0;
		$arChildData = array();			
		
		while (($row = fgetcsv($file)) !== FALSE)
		{							
			if($row[0] <> '')
			{  
				$rowCount++;	
				$errormsg = "Read line no. ".$rowCount;	
				$this->obj_utility->logGenerator($errorfile,"-",$errormsg,"I");			
				if($rowCount == 1)
				{
					$expectedCols = array('VoucherNo','Type','AccountName','DrAmount','CrAmount','VoucherDate','Remark');
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
					if($count == 7)
					{
						$VoucherNumber = array_search(VoucherNo,$row,true);
						$Type = array_search(Type,$row,true);
						$AccName = array_search(AccountName,$row,true);
						$Debit = array_search(DrAmount,$row,true);
						$Credit = array_search(CrAmount,$row,true);
						$VoucherDate = array_search(VoucherDate,$row,true);				
						$Note = array_search(Remark,$row,true);
					}
					else
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
				}
				else
				{	
					$VoucherNo = $row[$VoucherNumber];
					
					$ByTo = $row[$Type];
					$LedgerName = $row[$AccName];
					$DrAmt = $row[$Debit];
					$CrAmt = $row[$Credit];
					$VoucherDt = $row[$VoucherDate];
					$Remark = $row[$Note];	
							
					$arSubData = array();
					$arSubData['VNo'] = $VoucherNo;
					$arSubData['VDate'] = $VoucherDt;
					$arSubData['Note'] = $Remark;
					
					$arSubData['byto'] = $ByTo;
					

					$query1 = "select id from ledger where ledger_name = '".$LedgerName."'";
					$query11 = $this->m_dbConn->select($query1);

					if($query11[0]['id'] <> '')
					{
						$arSubData['To'] = $query11[0]['id'];
					}
					else
					{
						$arSubData['To'] = 0; //pending done
						$errormsg="Ledger details for ledger &lt;".$LedgerName."&gt; not found.";
						$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");	
					}
									
					$arSubData['Debit'] = $DrAmt;
					$arSubData['Credit'] = $CrAmt;		
					//pending done change $prev to $prevLineVoucherNo		
					if($VoucherNo == $prevLineVoucherNo)
					{						
						$arChildData[$iSubCount] = $arSubData;  
						$arData[$VoucherNo] = $arChildData;
						$iSubCount++;
						$prevLineVoucherNo = $VoucherNo;
					}
					else
					{
						$iSubCount = 0;
						$arChildData = array();	
						$arChildData[$iSubCount] = $arSubData;  
						$arData[$VoucherNo] = $arChildData;
						$iSubCount++;
						$prevLineVoucherNo = $VoucherNo;
					}
				}
			}
		}
		
		/*echo "<pre>";
		print_r($arData);
		echo "</pre>";*/
		//die();
		
		foreach($arData as $key => $value)
		{	
			//echo "something";	
			/*echo "<pre>";
			print_r($value);
			echo "</pre>";*/
			
			$topass = array();
			$DataMissingFlag = false;
			$isDateInRangeFlag = true;
			for($i=0;$i<sizeof($value);$i++)
			{		
				$VDate = $value[$i]['VDate'];
				if($_SESSION['default_year_start_date'] <> "" && $_SESSION['default_year_end_date'] <> "")
				{
					$correct_date = $this->obj_utility->getIsDateInRange($VDate,$_SESSION['default_year_start_date'],$_SESSION['default_year_end_date']);
				}
				else
				{
					$sql07 = "select * from year where YearID = '".$_SESSION['default_year']."'";
					$sql77 = $this->m_dbConn->select($sql07);
					if($sql77 <> "")
					{
						$correct_date = $this->obj_utility->getIsDateInRange($voucherDate,$sql77[0]['BeginingDate'],$sql77[0]['EndingDate']);
					}
				}
				if($correct_date != 1)
				{
					$isDateInRangeFlag = false;
				}
				//echo "Date: ".$VDate;
				$Note = $value[$i]['Note'];
				//echo "Note: ".$Note;
				if ($value[$i]['To'] == 0)
				{		
					$DataMissingFlag = true;
				}
				$topass[] = $value[$i];				
			}
			//die();
				
			$ShowDebugTrace = 0;
			if($DataMissingFlag == false && $isDateInRangeFlag == true)
			{
				/*echo "topass array<br>";
				echo "<pre>";
				print_r($topass);
				echo "</pre>";*/
				//die();
				$jvcount = 0;
				if($ShowDebugTrace ==1)
						{
							echo "<BR>topass : ";
							print_r($topass);	
						}
				for($m=0;$m<sizeof($topass);$m++)
				{
					if(strtolower($topass[$m]['byto']) == 'by')
					{
						$sql01 = "select * from `voucher` where `By`='".$topass[$m]['To']."' and `Debit`='".$topass[$m]['Debit']."' and `Date`='".getDBFormatDate($topass[$m]['VDate'])."'";
						$sql11 = $this->m_dbConn->select($sql01);
						if($ShowDebugTrace ==1)
						{
							echo "<BR>SQL : ". $sql01 ;
							print_r($sql11);	
						}
						if($sql11 <> '')
						{
							$VNo = $sql11[0]['VoucherNo'];
							$jvcount++;
						}
					}
					else if(strtolower($topass[$m]['byto']) == 'to')
					{
						$sql02 = "select * from `voucher` where `To`='".$topass[$m]['To']."' and `Credit`='".$topass[$m]['Credit']."' and `Date`='".getDBFormatDate($topass[$m]['VDate'])."' and `VoucherNo`='".$VNo."'";
						$sql22 = $this->m_dbConn->select($sql02);
						if($ShowDebugTrace ==1)
						{
							echo "<BR>SQL : ". $sql02 ;
							print_r($sql22);	
						}
						
						if($sql22 <> '')
						{
							$jvcount++;
						}
					}
				}
				
				if($ShowDebugTrace ==1)
				{
					echo "<BR>jvcount : ". $jvcount;
					echo "<BR>topass : ". $topass;
					print_r($sql22);	
				}
				$entryinfo = '';
								
				//echo "Implode check: ".$entryinfo;
				//die();
				
				$Result = '';
				if($jvcount == 0)
				{										
					$Result = $this->obj_createvoucher->createNewVoucher("","false",0,"true",$VDate,$topass,"false",0,0,0,0,0,0,$Note);
					if($Result == 'Update')
					{
						$errormsg = "Voucher No.: &lt;".$key."&gt; inserted successfully.<br>";
						$this->obj_utility->logGenerator($errorfile,"-",$errormsg,"I");
					}
					else
					{
						$errormsg = "Voucher No.: &lt;".$key."&gt; not inserted successfully.<br>";
						$this->obj_utility->logGenerator($errorfile,"-",$errormsg,"E");
					}
				}
				else
				{
					$Result = $this->obj_createvoucher->createNewVoucher("","false",0,"true",$VDate,$topass,"false",0,0,0,0,0,0,$Note);
					if($Result == 'Update')
					{
						$errormsg = "Voucher No.: &lt;".$key."&gt; inserted successfully, but this entry might be duplicate. Please check.<br>";
						for($m=0;$m<sizeof($topass);$m++)
						{
							$sql_ledgername = "select ledger_name from ledger where id = '".$topass[$m]['To']."'";
							$found_ledgername = $this->m_dbConn->select($sql_ledgername);
							$topass[$m]['To'] = $found_ledgername[0]['ledger_name'];
							$entryinfo = $entryinfo."<br>".implode(" | ",$topass[$m]);									
						}
						$errormsg = $errormsg."<br>".$entryinfo;
						$this->obj_utility->logGenerator($errorfile,"-",$errormsg,"E");
					}
					else
					{
						$errormsg = "Voucher No.: &lt;".$key."&gt; not inserted successfully.<br>";
						$this->obj_utility->logGenerator($errorfile,"-",$errormsg,"E");
					}
				}
			}
			else if($DataMissingFlag == true || $isDateInRangeFlag == false)
			{
				$errormsg = "Ledger details are missing or date not in range, for Voucher No.: &lt;".$key."&gt; so voucher not imported.<br>";
				$this->obj_utility->logGenerator($errorfile,"-",$errormsg,"E");
			}
		}
		
		$errormsg = "<br>[End of JV]";
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