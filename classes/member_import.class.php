<?php
//include_once("include/dbop.class.php");
include_once("utility.class.php");
class member_import 
{
	
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_utility;
	
	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
		$this->obj_utility = new utility($this->m_dbConn);
	}
	
	public function CSVMemberImport()
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
				$original_file_name='OwnerID.csv';
				//echo $_FILES['file'] ['name'];
				if(($_FILES['file'] ['name']) != "$original_file_name") {
					  //exit("Does not match");
					  $result = '<p>File Name Does Not Match(only OwnerID.csv file accepted)...</p>';
					  
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
					//echo "inside uploading print <br/>";
						$result = '<p> Member Data Uploading Process Started <' . $this->getDateTime() . '> </p>';
						//echo "1";
						$result .= $this->UploadData($tempName);
						//echo "2";
						$result .= '<p> Member Data Uploading Process Complete <' . $this->getDateTime() . '> </p>';
						//echo "printed";
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
		$errormsg="[Importing OwnerID]";
		$isImportSuccess = false;
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		$sql00="select member_flag from `import_history` where society_id='".$_SESSION['society_id']."'";
		$res01=$this->m_dbConn->select($sql00);
		if($res01[0]['member_flag']==0)
		{
				while (($row = fgetcsv($file)) !== FALSE)
				{
					//echo '<br/>';
					if($row[0] <> '')
						{
							$rowCount++;
							if($rowCount == 1)
							{
								$WCode=array_search(WCode,$row,true);
								$BCode=array_search(BCode,$row,true);
								$FCode=array_search(FCode,$row,true);
								$Owner=array_search(Owner,$row,true);
								$DateOfBirth=array_search(DateOfBirth,$row,true);
								$AnnivarsaryDate=array_search(AnnivarsaryDate,$row,true);
								$BloodGroup=array_search(BloodGroup,$row,true);
								$MobileNo=array_search(MobileNo,$row,true);
								$EMail=array_search(EMail,$row,true);
								$EMail1=array_search(Email1,$row,true);
								$EmergencyPersonName=array_search(EmergencyPersonName,$row,true);
								$EmergencyMobileNo=array_search(EmergencyMobileNo,$row,true);
								$EmergencyTelephoneNo=array_search(EmergencyTelephoneNo,$row,true);
								$Gender=array_search(Gender,$row,true);
								$Occupation=array_search(Occupation,$row,true);
								$OffPhone=array_search(OffPhone,$row,true);
								$Inactive=array_search(Inactive,$row,true);
								$DisposeDate=array_search(DisposeDate,$row,true);
								$OwnerAddress=array_search(OwnerAddress,$row,true);
								$CarParkingNo=array_search(CarParkingNo,$row,true);
								$GSTINNO=array_search(GSTINNO,$row,true);
								$BikeParkingNo=array_search(BikeParkingNo,$row,true);
								
								if(!isset($BCode) || !isset($WCode)  || !isset($FCode) || !isset($Owner) || !isset($DateOfBirth) || !isset($AnnivarsaryDate) || !isset($BloodGroup) || !isset($MobileNo) ||  !isset($EMail)  || !isset($EMail1) || !isset($EmergencyPersonName) || !isset($EmergencyMobileNo) || !isset($EmergencyTelephoneNo) || !isset($Gender) || !isset($Occupation) || !isset($OffPhone))
									{
										$result = '<p>Column Names Not Found Cant Proceed Further......</p>'.'Go Back';
										$errormsg=" Column names   in file OwnerId not match";
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
								$owner_name= $this->m_dbConn->escapeString($row[$Owner]);
								$gender=$row[$Gender];
								$mob=$row[$MobileNo];
								$off_no=$row[$OffPhone];
								$desg=$row[$Occupation];
								$email=$row[$EMail];
								$alt_email=$row[$EMail1];
								$dob=$row[$DateOfBirth];
								$wed_any=$row[$AnnivarsaryDate];
								$blood_group=$row[$BloodGroup];
								$eme_rel_name=$row[$EmergencyPersonName];
								$eme_contact_1=$row[$EmergencyMobileNo];
								$eme_contact_2=$row[$EmergencyTelephoneNo];
								$owner_inactive=$row[$Inactive];
								$despdate=$row[$DisposeDate];
								$parking_no=$row[$OwnerAddress];
								$car_parking_no=$row[$CarParkingNo];
								$gstin_no=$row[$GSTINNO];				
								$bike_parking_no=$row[$BikeParkingNo];	
								
								$get_society_id="select society_id from society where society_code='".$society_code."'";
								$data2=$this->m_dbConn->select($get_society_id);
								if($data2=='')
								{
									$errormsg=" Society Code &lt;".$society_code."&gt;  not found in society table for unit: &lt;".$unit_no."&gt; and wing: &lt;".$wing_code."&gt;";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"W");
								}
								$society_id=$data2[0]['society_id'];
								$get_wing_id="select wing_id from wing where wing='".$wing_code."' and society_id = '" . $society_id . "'";
								$data3=$this->m_dbConn->select($get_wing_id);
								$wing_id=$data3[0]['wing_id'];
								if($data3=='')
								{
									$errormsg=" Wing &lt;".$wing_code."&gt; not found in wing table for unit: &lt;".$unit_no."&gt; and society &lt;".$society_code."&gt;";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"W");
								}
								
								$get_unit_id="select `unit_id` from `unit` where `unit_no` = '".$unit_no."'  and `society_id` = '" . $society_id . "' and `wing_id` = '" . $wing_id . "'";
								$data4=$this->m_dbConn->select($get_unit_id);
								$unit=$data4[0]['unit_id'];
								
								if($data4=='')
								{
									$errormsg=" Unit &lt;".$unit_no."&gt;  not found in unit table for member: &lt;".$owner_name."&gt; and wing: &lt;".$wing_code."&gt;  ";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"W");
								}
								
									$get_desg_id="select desg_id from desg where desg='".$desg."'";
									$data5=$this->m_dbConn->select($get_desg_id);
									$desg_id=$data5[0]['desg_id	'];
								
								if($desg_id=="" && $desg <> '' && $desg <> 'No' && $desg <> '0')
								{
									$insert_desg="insert into desg(desg) values('".$desg."')";
									$desg_id=$this->m_dbConn->insert($insert_desg);
								}
								else if($desg=='No' || $desg=='')
								{
									
									
									$desg_id=1;
									
								}
								
								
									$get_bg_id="select bg_id from bg where bg='".$blood_group."'";
									$data6=$this->m_dbConn->select($get_bg_id);
									$bg_id=$data6[0]['bg_id'];
								
								if($bg_id=="")
								{
									$bg_id=9;
								}
								
								if($owner_inactive=='')
								{
									
									$errormsg="Inactive flag  in ownerid is &lt;".$owner_inactive."&gt; Hence meber not added ";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");
								}
								
								if($owner_inactive=='NO' && $despdate=="" && $unit <> "" && ($society_id <> "" || $society_id <> 0) && ($wing_id <> "" || $wing_id <> 0 ))
								{
								
									$select_member_main="select Count(*) as cnt from `member_main` where `society_id`='".$society_id."' and `wing_id`='".$wing_id."' and `unit`='".$unit."' and `owner_name`='".$owner_name."'";
									$checkexistmember=$this->m_dbConn->select($select_member_main);
									
									if(trim($gstin_no) == "-")
									{
										$gstin_no_to_insert = "";
									}
									else
									{
										$gstin_no_to_insert = trim($gstin_no);
									}
									
									if($checkexistmember[0]['cnt']==0)
									{
										$insert_member_main="insert into member_main(society_id,wing_id,unit,owner_name,gender,parking_no,resd_no,mob,alt_mob,off_no,off_add,desg,email,alt_email,dob,wed_any,blood_group,eme_rel_name,eme_contact_1,eme_contact_2,status,owner_gstin_no) values('$society_id','$wing_id','$unit','$owner_name','$gender','$parking_no','$resd_no','$mob','$alt_mob','$off_no','$off_add','".$desg_id."','$email','$alt_email','$dob','$wed_any','".$bg_id."','$eme_rel_name','$eme_contact_1','$eme_contact_2','Y','$gstin_no_to_insert')";
										$data=$this->m_dbConn->insert($insert_member_main);

										$this->UpdateCoOwners($data, $owner_name, $mob, $email);
										
										//car parking
										if(trim($car_parking_no) != "")
										{
											$sql01 = "select member_id from member_main where unit = '".$unit."'";
											$sql11 = $this->m_dbConn->select($sql01);
											$mem_id = $sql11[0]['member_id'];
											
											$for_amp_car = array();
											for($z=0;$z<strlen($car_parking_no);$z++)
											{
												$for_amp_car[$z] = $car_parking_no[$z];
											}
											
											if(in_array("&",$for_amp_car))
											{
												$indivi_car_parking_no = str_replace('&',',',$car_parking_no);
												$car_parking_coll = explode(',',$indivi_car_parking_no);
												for($i=0;$i<sizeof($car_parking_coll);$i++)
												{
													$sql02 = "insert into mem_car_parking(member_id,parking_slot) values('".$mem_id."','".trim($car_parking_coll[$i])."')";
													$sql22 = $this->m_dbConn->insert($sql02);
												}
											}
											else
											{
												$sql03 = "insert into mem_car_parking(member_id,parking_slot) values('".$mem_id."','".trim($car_parking_no)."')";
												$sql33 = $this->m_dbConn->insert($sql03);
											}
										}
										
										//bike parking
										if(trim($bike_parking_no) != "")
										{
											$sql01 = "select member_id from member_main where unit = '".$unit."'";
											$sql11 = $this->m_dbConn->select($sql01);
											$mem_id = $sql11[0]['member_id'];
											
											$for_amp_bike = array();
											for($z=0;$z<strlen($bike_parking_no);$z++)
											{
												$for_amp_bike[$z] = $bike_parking_no[$z];
											}
											
											if(in_array("&",$for_amp_bike))
											{
												$indivi_bike_parking_no = str_replace('&',',',$bike_parking_no);
												$bike_parking_coll = explode(',',$indivi_bike_parking_no);
												for($i=0;$i<sizeof($bike_parking_coll);$i++)
												{
													$sql02 = "insert into mem_bike_parking(member_id,parking_slot) values('".$mem_id."','".trim($bike_parking_coll[$i])."')";
													$sql22 = $this->m_dbConn->insert($sql02);
												}
											}
											else
											{
												$sql03 = "insert into mem_bike_parking(member_id,parking_slot) values('".$mem_id."','".trim($bike_parking_no)."')";
												$sql33 = $this->m_dbConn->insert($sql03);
											}
										}

										$isImportSuccess = true;
									}
									else
									{
										$errormsg="Member &lt;".$owner_name."&gt; already exists  in this society  Hence meber not added again.";
										$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");
											
									}
								}
								else
								{
									$errormsg="Member &lt;".$owner_name."&gt; not imported check if owner is inactive &lt;" .$owner_inactive."&gt; or unit no is blank &lt;".$unit_no ."&gt; or society code &lt;".$society_code."&gt; match with BCode in BuildingID file or wing code &lt;".$wing_code." &gt;match with WCode in WingID file or dispose date not empty   &lt;".$despdate."&gt;"."in OwnerID file";
									$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");		
								}
								
								
						   }
					
				}
				
			}
	
		}
		if($isImportSuccess)
		{
			$update_import_history="update `import_history` set member_flag=1 where society_id='".$_SESSION['society_id']."'";	
			$res123=$this->m_dbConn->update($update_import_history);
		}
		else
		{
			$errormsg="member details not imported";
			$this->obj_utility->logGenerator($errorfile,'Error',$errormsg,"E");	
		}	
						
		$errormsg="[End of  OwnerID]";
		$this->obj_utility->logGenerator($errorfile,'End',$errormsg);
		
	}
	function getDateTime()
	{
		$dateTime = new DateTime();
		$dateTimeNow = $dateTime->format('Y-m-d H:i:s');
		return $dateTimeNow;
	}
	
	private function UpdateCoOwners($member_id, $owner_names, $mobile, $email)
	{
		$owner = str_replace('&', ',', $owner_names);
		$owner = str_replace('/', ',', $owner);
		$owner = str_replace(' AND ', ',', $owner);
		$owner_coll = explode(',', $owner);
		
		$updatePrimaryOwner = "Update `member_main` SET `primary_owner_name` = '" . trim($owner_coll[0]) . "' WHERE `member_id` = '" . $member_id . "'";

		$resPrimaryOwner = $this->m_dbConn->update($updatePrimaryOwner);

		for($i = 0; $i < sizeof($owner_coll); $i++)
		{
			if($i == 0)
			{
				$insertCoOwner = "INSERT INTO `mem_other_family` (`member_id`, `other_name`, `coowner`, `relation`, `other_mobile`, `other_email`, `send_commu_emails`) VALUES ('" . $member_id . "', '" . trim($owner_coll[$i]) . "', '1', 'Self', '" . $mobile . "', '" . $email . "', '1')";
			}
			else
			{
				$insertCoOwner = "INSERT INTO `mem_other_family` (`member_id`, `other_name`, `coowner`) VALUES ('" . $member_id . "', '" . trim($owner_coll[$i]) . "', '2')";
			}

			$resCoOwner = $this->m_dbConn->insert($insertCoOwner);
		}
	}
}

?>