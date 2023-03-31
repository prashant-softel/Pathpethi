<?php
//include_once("include/dbop.class.php");
include_once("register.class.php");
include_once("utility.class.php");
include_once("dbconst.class.php");

class ledger_import extends dbop
{
	
	public $m_dbConn;
	public $obj_register;
	public $obj_utility;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->obj_register = new regiser($this->m_dbConn);
		$this->obj_utility = new utility($this->m_dbConn);
	}
	
	public function CSVLedgerImport()
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
				$original_file_name='AccountMaster.csv';
				//echo $_FILES['file'] ['name'];
				if(($_FILES['file'] ['name']) != "$original_file_name") {
					  //exit("Does not match");
					  $result = '<p>File Name Does Not Match(only AccountMaster.csv file accepted)...</p>';
					  
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
						$result = '<p> Ledger Data Uploading Process Started <' . $this->getDateTime() . '> </p>';
						
						$result .= $this->UploadData($tempName);
						
						$result .= '<p> Ledger Data Uploading Process Complete <' . $this->getDateTime() . '> </p>';
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
		$sql00="select ledger_flag from `import_history` where society_id='".$_SESSION['society_id']."'";
		$res01=$this->m_dbConn->select($sql00);
		if($res01[0]['ledger_flag']==0)
		{

		$result = '';
		//$tempBankAccID = 0;
		//$tempCashAccID = 0;
				
		while (($row = fgetcsv($file)) !== FALSE)
		{
			//echo '<br/>';
			
			
			if($row[0] <> '')
				{
					$rowCount++;
					if($rowCount == 1)
					{
						$GroupId=array_search(GroupId,$row,true);
						$Category=array_search(Category,$row,true);
						$SubCategory=array_search(SubCategory,$row,true);
						$Description=array_search(Description,$row,true);
						$FCode=array_search(FCode,$row,true);
						$OpeningType=array_search(OpeningType,$row,true);
						$OpeningBalance=array_search(OpeningBalance,$row,true);
						$TaxFlag=array_search(TaxFlag,$row,true);
						$Remark=array_search(Remark,$row,true);
						$SubCategory=array_search(SubCategory,$row,true);
						$TariffTag=array_search(TariffTag,$row,true);
							
					if(!isset($GroupId) || !isset($Category)  || !isset($SubCategory) || !isset($Description) || !isset($FCode) || !isset($OpeningType) || !isset($OpeningBalance) || !isset($TaxFlag) || !isset($Remark) || !isset($SubCategory))
							{
								$result = '<p>Column Names Not Found Cant Proceed Further......</p>'.'Go Back';
								//$result.'<p>Cant Proceed Further...</p>';
								return $result;
								exit(0);
								//break;
								//return ;
								
							}
					
						
					}
			//print_r($row);
			       else
				   {
						$groupname=$row[$GroupId];
						//echo $society_code;
						$category_name=$row[$Category];
						$sub_category_name=$row[$SubCategory];
						$unit_no=$row[$FCode];
						$opening_type=$row[$OpeningType];
						$opening_balance=$row[$OpeningBalance];
						$taxable=$row[$TaxFlag];
						$show_in_bill=$row[$TariffTag];
						$note=$row[$Remark];
						$society_id = $_SESSION['society_id'];
						$sale=1;
						$purchase=1;
						$income=1;
						$expense=1;
						$payment=1;
						$receipt=1;
						
						if($unit_no=='')
						{
							$ledger_name=$row[$Description];
						}
						else
						{
						 $ledger_name='Unit '.$row[$FCode];	
						}
						
						
						if($show_in_bill=='YES')
						{
							$flag=1;
							
						}
						else
						{
							$flag=0;
						}
							
						//search sub category in account table
				$query1="select category_name,category_id from `account_category` where `category_name`='".$sub_category_name."' ";
						$data1=$this->m_dbConn->select($query1);
						
						//echo 'Sub Category : ' .$sub_category_name;
						//print_r($data1);
						//echo $query1;
						if($data1=='')
						{
							$query2="select id from `group` where groupname='".$category_name."'";
							$data2=$this->m_dbConn->select($query2);
							$parentcategory_id=0;
							if($data2=='')
							{
								$count_query = "select category_id  from account_category where category_name='".$category_name."'";	
								$res = $this->m_dbConn->select($count_query);
								
							
									if($res=='')
									{
											$query3="select id from `group` where groupname='".$groupname."'";
											$data3=$this->m_dbConn->select($query3);
											$id=$data3[0]['id'];
											//echo $query3;
																						
											$query4="insert into `account_category`(category_name,parentcategory_id,group_id) values('$category_name',0,'$id')";
											
											$data4=$this->m_dbConn->insert($query4);
											//echo $query4;
											//echo $query4;
											$parentcategory_id=$data4;
											
											/*if($category_name == 'Bank Balances')
											{
												//$result .= '<br>1. Category : ' . $category_name . ' ID : ' . $data4;
												$tempBankAccID = $data4;			
											}*/
									}
									else
									{
										
										$parentcategory_id=$res[0]['category_id'];
									}
									$group_id=$data3[0]['id'];
									$query5="insert into `account_category`(category_name,parentcategory_id,group_id) values('$sub_category_name','$parentcategory_id','$group_id')";
									$data5=$this->m_dbConn->insert($query5);
									$category_id=$data5;
									
									/*if($sub_category_name == 'Bank Balances')
									{
										//$result .= '<br>2. Category : ' . $sub_category_name . ' ID : ' . $data5;
										$tempBankAccID = $data5;			
									}*/
							}
							else
							{
								
								$group_id=$data2[0]['id'];
								
								$query6="insert into `account_category`(category_name,parentcategory_id,group_id) values('$sub_category_name','$parentcategory_id','$group_id')";
									$data6=$this->m_dbConn->insert($query6);
									$category_id=$data6;
									
									/*if($sub_category_name == 'Bank Balances')
									{
										//$result .= '<br>3. Category : ' . $sub_category_name . ' ID : ' . $data5;
										$tempBankAccID = $data6;			
									}*/
									
								}
						}
						else
						{
							$category_id=$data1[0]['category_id'];
							
							/*if($sub_category_name == 'Bank Balances')
							{
								//$result .= '<br>4. Category : ' . $sub_category_name . ' ID : ' . $category_id;
								$tempBankAccID = $category_id;			
							}*/
						}
				
						
						$insert_ledger="insert into `ledger`(society_id,categoryid,show_in_bill,ledger_name,taxable,sale,purchase,income,expense,payment,receipt,opening_type,opening_balance,note) values('$society_id','$category_id','$flag','$ledger_name','$taxable','$sale','$purchase','$income','$expense','$payment','$receipt','$opening_type','$opening_balance','$note')";
						//echo $insert_ledger;
						
						$NewLedgerID=$this->m_dbConn->insert($insert_ledger);
						$aryParent = $this->obj_utility->getParentOfLedger($NewLedgerID);
						$Date = '2015-04-01';
						
						if($aryParent['group'] == LIABILITY)
						{
							$insertLiability = $this->obj_register->SetLiabilityRegister(getDBFormatDate($Date), $NewLedgerID, 0, 0, TRANSACTION_CREDIT, $opening_balance, 1);
						}
						else if($aryParent['group'] == ASSET)
						{
							$insertAsset = $this->obj_register->SetAssetRegister(getDBFormatDate($Date), $NewLedgerID, 0, 0, TRANSACTION_DEBIT, $opening_balance, 1);
						}				
						
						//echo 'Test : ' . $aryParent['category_name'];
						//if($category_id == $tempBankAccID)
						if($aryParent['category_name'] == "Bank Balances" || $aryParent['category_name'] == "Cash Balance")
						{
							//$result .= '<br>Insert In Bank : ' . $aryParent['category'] . ':' . $tempBankAccID;
							$insertBank = $this->obj_register->SetBankRegister(getDBFormatDate($Date), $NewLedgerID, 0, 0, TRANSACTION_RECEIVED_AMOUNT, $opening_balance, 0, 0, 1);
							
							$insertBankMaster = $insert_query="insert into bank_master (`BankID`, `BankName`) values ('" . $NewLedgerID . "', '".$ledger_name."')";
							$sqlInsertResult = $this->m_dbConn->insert($insertBankMaster);
						}
						//echo $insert_ledger;
				   }
			
		}
		
	}
	
					if($NewLedgerID<> '')
						{
						$update_import_history="update `import_history` set ledger_flag=1 where society_id='".$_SESSION['society_id']."'";							
						$_SESSION['ledger_flag']=1;
						}
						
						else
						{
						$update_import_history="update `import_history` set ledger_flag=0 where society_id='".$_SESSION['society_id']."'";							
						$_SESSION['ledger_flag']=0;
						}
						$res123=$this->m_dbConn->update($update_import_history);
	//echo "file imported successfully..";
	
		}
		
		return $result;
	}
	function getDateTime()
	{
		$dateTime = new DateTime();
		$dateTimeNow = $dateTime->format('Y-m-d H:i:s');
		return $dateTimeNow;
	}
	
	
	public function combobox($query, $id, $defaultText)
	{
		 echo "inside combobox..";
		if($defaultText <> '')
		{
			$str = '<option value="0">' . $defaultText . '</option>';
		}
		
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