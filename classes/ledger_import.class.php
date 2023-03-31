<?php
//include_once("include/dbop.class.php");
include_once("register.class.php");
include_once("utility.class.php");
include_once("dbconst.class.php");

class ledger_import 
{
	
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_register;
	public $obj_utility;
	
	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConnRoot = $dbConnRoot;
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
	
	public function UploadData($fileName,$errorfile)
	{
		$file = fopen($fileName,"r");
		$errormsg="[Importing Accountmaster]";
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		$isImportSuccess = false;
		$sql00="select ledger_flag from `import_history` where society_id='".$_SESSION['society_id']."'";
		$res01=$this->m_dbConn->select($sql00);
		if($res01[0]['ledger_flag']==0)
		{
			$Date = $this->get_date($_POST['Period']);

		$result = '';
		
		while (($row = fgetcsv($file)) !== FALSE)
		{
			
			
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
							$errormsg=" Column names in file Accountmaster not match";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							return $result;
							exit(0);
						}
					
						
					}
				 else
				   {
						$groupname=$row[$GroupId];
						$category_name=$row[$Category];
						$sub_category_name=strtolower($row[$SubCategory]);
						$unit_no=$row[$FCode];
						$opening_type=$row[$OpeningType];
						$opening_balance=$row[$OpeningBalance];
						$taxable=$row[$TaxFlag];
						$show_in_bill=$row[$TariffTag];
						$note=$row[$Remark];
						$society_id = $_SESSION['society_id'];
						$sale=0;
						$purchase=0;
						$income=0;
						$expense=0;
						$payment=0;
						$receipt=0;
						$is_ledger_unit=0;
						if($unit_no=='')
						{
							$ledger_name=$row[$Description];
							
						}
						else
						{
							$is_ledger_unit=1;
						 $ledger_name=$row[$FCode];	
						}
						
						
						if(strtolower($show_in_bill)=='yes')
						{
							$flag=1;
							
						}
						else
						{
							$flag=0;
						}
						
					if($ledger_name <> 0 || $ledger_name <> '')
					{
						$query1="select category_name,category_id from `account_category` where `category_name`='".$sub_category_name."' ";
						$data1=$this->m_dbConn->select($query1);
									if($data1=='')
										{
													$query2="select id from `group` where groupname='".$category_name."'";
													$data2=$this->m_dbConn->select($query2);
													$parentcategory_id=0;
														if($data2=='')
														{
															
															$count_query = "select category_id,group_id  from account_category where category_name='".$category_name."'";	
															$res = $this->m_dbConn->select($count_query);
															
														
																if($res=='')
																{
																		$query3="select id from `group` where groupname='".$groupname."'";
																		$data3=$this->m_dbConn->select($query3);
																		$id=$data3[0]['id'];
																		$query4="insert into `account_category`(category_name,parentcategory_id,group_id) values('$category_name','0','$id')";
																		$data4=$this->m_dbConn->insert($query4);
																		$parentcategory_id=$data4;
																		$group_id=$data3[0]['id'];
																}
																else
																{
																	
																	$parentcategory_id=$res[0]['category_id'];
																	$group_id=$res[0]['group_id'];
																}
														
														$query5="insert into `account_category`(category_name,parentcategory_id,group_id) values('$sub_category_name','$parentcategory_id','$group_id')";
														$data5=$this->m_dbConn->insert($query5);
														$category_id=$data5;
														
														
														}
														else
														{
															
															$group_id=$data2[0]['id'];
															
															$query6="insert into `account_category`(category_name,parentcategory_id,group_id) values('$sub_category_name','1','$group_id')";
																$data6=$this->m_dbConn->insert($query6);
																$category_id=$data6;
																
																
																
															}
									}
									else
									{
										$category_id=$data1[0]['category_id'];
										
										
									}
							          
									if($is_ledger_unit==1)
									{	  
									$search_ledger = "select * from  ledger where ledger_name='".$ledger_name."' and society_id='".$_SESSION['society_id']."'";
										   $search=$this->m_dbConn->select($search_ledger);
										   
										   if($search <> '')
										   {
											
											  $errormsg="ledger name <" .$ledger_name. ">  already exits in ledger table";
											  $this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg);
											  $del1="delete  from `ledger` where ledger_name='".$ledger_name."' and society_id='".$_SESSION['society_id']."'";
											  $del01=$this->m_dbConn->delete($del1);
											  $del2="delete  from `assetregister` where LedgerID='".$search[0]['id']."'  and Is_Opening_Balance=1 ";
											  $del02=$this->m_dbConn->delete($del2);
											  $del3="delete  from `liabilityregister` where LedgerID='".$search[0]['id']."' and Is_Opening_Balance=1";
											  $del03=$this->m_dbConn->delete($del3);
											  $del4="delete  from `bankregister` where LedgerID='".$search[0]['id']."' and Is_Opening_Balance=1";
											  $del04=$this->m_dbConn->delete($del4);
											}
										   
									}
									$aryCategoryParent = $this->obj_utility->getParentOfCategory($category_id);
									if(strcasecmp($aryCategoryParent['groupname'],'Liability')==0)
									{
										if(strcasecmp($opening_type,"CREDIT")==0)
										{
											$account_type=1;
										}
										else
										{
											$account_type=2;
										}
										
										$insert_ledger="insert into `ledger`(society_id,categoryid,show_in_bill,ledger_name,taxable,sale,purchase,income,expense,payment,receipt,opening_type,opening_balance,note,`opening_date`) values('$society_id','$category_id','$flag','$ledger_name',0,0,0,0,1,1,1,'$account_type','".abs($opening_balance)."','$note','".getDBFormatDate($Date)."')";
										$errormsg= "Ledger Name: &lt;".$ledger_name."&gt; :: Type: &lt; Liability &gt;";
										$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");
										$isImportSuccess = true;
									}
									
									else if(strcasecmp($aryCategoryParent['groupname'],'Asset')==0)
									{
										if(strcasecmp($opening_type,"CREDIT")==0)
										{
											$account_type=1;
										}
										else
										{
											$account_type=2;
										}
										
										$insert_ledger="insert into `ledger`(society_id,categoryid,show_in_bill,ledger_name,taxable,sale,purchase,income,expense,payment,receipt,opening_type,opening_balance,note,`opening_date`) values('$society_id','$category_id','$flag','$ledger_name',0,1,1,1,0,0,1,'$account_type','".abs($opening_balance)."','$note','".getDBFormatDate($Date)."')";
										$errormsg= "Ledger Name: &lt;".$ledger_name."&gt; :: Type: &lt; Asset &gt;";
										$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");
										$isImportSuccess = true;
									}
									
									else if(strcasecmp($aryCategoryParent['groupname'],'Income')==0)
									{
										$insert_ledger="insert into `ledger`(society_id,categoryid,show_in_bill,ledger_name,taxable,sale,purchase,income,expense,payment,receipt,opening_type,opening_balance,note,`opening_date`) values('$society_id','$category_id','$flag','$ledger_name',0,1,0,1,0,0,1,'$opening_type','".abs($opening_balance)."','$note','".getDBFormatDate($Date)."')";
										$errormsg= "Ledger Name: &lt;".$ledger_name."&gt; :: Type: &lt; Income &gt;";
										$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");
										$isImportSuccess = true;
									}
									
									
									
									else if(strcasecmp($aryCategoryParent['groupname'],'Expense')==0)
									{
										
										$insert_ledger="insert into `ledger`(society_id,categoryid,show_in_bill,ledger_name,taxable,sale,purchase,income,expense,payment,receipt,opening_type,opening_balance,note,`opening_date`) values('$society_id','$category_id','$flag','$ledger_name',0,0,0,0,1,1,0,'$opening_type','".abs($opening_balance)."','$note','".getDBFormatDate($Date)."')";
										$errormsg= "Ledger Name: &lt;".$ledger_name."&gt; :: Type: &lt; Expense &gt;";
										$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");
										$isImportSuccess = true;
									}
									$NewLedgerID=$this->m_dbConn->insert($insert_ledger);
									$aryParent = $this->obj_utility->getParentOfLedger($NewLedgerID);
									
									
									if($aryParent['group'] == LIABILITY)
									{
										if(strcasecmp($opening_type,"CREDIT")==0)
										{
											//$errormsg=implode(' || ',$row);
											//$this->obj_utility->logGenerator($errorfile,"Liability Credit",$errormsg,"I");
											$insertLiability = $this->obj_register->SetLiabilityRegister(getDBFormatDate($Date), $NewLedgerID, 0, 0, TRANSACTION_CREDIT, abs($opening_balance), 1);
										}
										else
										{
											//$errormsg=implode(' || ',$row);
											//$this->obj_utility->logGenerator($errorfile,"Liability Debit",$errormsg,"I");
											$insertLiability = $this->obj_register->SetLiabilityRegister(getDBFormatDate($Date), $NewLedgerID, 0, 0, TRANSACTION_DEBIT, abs($opening_balance), 1);
										}
									}
									else if(strcasecmp($aryParent['category_name'], "Bank Balances") == 0 || strcasecmp($aryParent['category_name'],"Cash Balance") == 0)
									{
										$insertBank = $this->obj_register->SetBankRegister(getDBFormatDate($Date), $NewLedgerID, 0, 0, TRANSACTION_RECEIVED_AMOUNT, abs($opening_balance), 0, 0, 1);
										//$errormsg=implode(' || ',$row);
										//$this->obj_utility->logGenerator($errorfile,"BAnk",$errormsg,"I");
										$insertBankMaster = $insert_query="insert into bank_master (`BankID`, `BankName`) values ('" . $NewLedgerID . "', '".$ledger_name."')";
										$sqlInsertResult = $this->m_dbConn->insert($insertBankMaster);
									}
									else if($aryParent['group'] == ASSET)
									{
										if(strcasecmp($opening_type,"CREDIT")==0)
										{
											//$errormsg=implode(' || ',$row);
											//$this->obj_utility->logGenerator($errorfile,"Asset Credit",$errormsg,"I");
											$insertAsset = $this->obj_register->SetAssetRegister(getDBFormatDate($Date), $NewLedgerID, 0, 0, TRANSACTION_CREDIT, abs($opening_balance), 1);
										
										}
										else
										{
											//$errormsg=implode(' || ',$row);
											//$this->obj_utility->logGenerator($errorfile,"Asset Debit",$errormsg,"I");
											$insertAsset = $this->obj_register->SetAssetRegister(getDBFormatDate($Date), $NewLedgerID, 0, 0, TRANSACTION_DEBIT, abs($opening_balance), 1);					//echo $insertAsset;	
										}
									}				
						}//if end
						else
						{
							$errormsg="Ledger name blank in Fcode or Description Column";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg);	
						}
				   }//else end
			
		}//if end
		
	}//while end
	
	if($isImportSuccess)
	{
		$update_import_history="update `import_history` set ledger_flag=1 where society_id='".$_SESSION['society_id']."'";							
		$res123=$this->m_dbConn->update($update_import_history);
	}
	else
	{
		$errormsg="ledger details not imported";
		$this->obj_utility->logGenerator($errorfile,'Error',$errormsg,"E");	
	}	
	
		}//main if end
		$errormsg="[End of  AcoountMaster]";
		$this->obj_utility->logGenerator($errorfile,'End',$errormsg);
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
	
	public function get_date($id)
	{
		$sql = "select `BeginingDate`- INTERVAL 1 DAY  as BeginingDate from `period` where  id=".$id." ";
		$data = $this->m_dbConn->select($sql);
		return $data[0]['BeginingDate'];
	}

}

?>