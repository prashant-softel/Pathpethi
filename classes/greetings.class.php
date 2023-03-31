<?php

include_once ("dbconst.class.php"); 
include_once( "include/fetch_data.php");
include_once( "utility.class.php");
include_once("initialize.class.php");
include_once('../swift/swift_required.php');
include_once("android.class.php");
class greetings
{
	public $actionPage = "../addnotice.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	public $objFetchData;
	public $objInitialize;
	
	function __construct($dbConn, $dbConnRoot, $socID = "")
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->objFetchData = new FetchData($dbConn);
		//echo "soc check".$socID;
		if($socID <> "")
		{
			$this->objFetchData->GetSocietyDetails($socID);	
		}
		else
		{
			$this->objFetchData->GetSocietyDetails($_SESSION['society_id']);
		}
		$this->objInitialize = new initialize($dbConn, $dbConnRoot);
		echo "checked";
	}
	
	public function startProcess()
	{
		echo $_POST['insert'];			
						
		$errorExists=0;
		if($_POST['insert']=='Submit' && $errorExists==0)
		{
			if($_POST['notice_creation_type'] == 2)
			{
				if($_FILES['userfile']['name'] == "")
				{
					//return "Please select file to upload.";
				}
				
				$fileTempName = $_FILES['userfile']['tmp_name'];  
				$fileSize = $_FILES['userfile']['size'];
				$fileName = time().'_'.basename($_FILES['userfile']['name']);
						
				$uploaddir = $_SERVER['DOCUMENT_ROOT']."/Notices";			   
				$uploadfile = $uploaddir ."/". $fileName;	
				echo $uploadfile;	
				if(move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile))
				{
					//$_POST['description'] = "Notice Uploaded";
					$_POST['note'] = $fileName;
				}
				else
				{
					return "Error uploading file - check destination is writeable.";
				}
			}	
							
			if($_POST['issueby'] <> '' && $_POST['subject'] <> '' && $_POST['post_date'] <> '' && $_POST['exp_date'] <> '')
			{
				
				$creation_date=date('d-m-Y');
				$SendTo_Array = array();
				$notice=$_POST['post_noticeto'];
				
				if ($notice)
				{
					foreach ($notice as $value)
					{
						array_push($SendTo_Array,$value);
					}
				}
				
				$insert_notice="insert into `notices`(`notice_type_id`,`issuedby`,`subject`,`description`,`creation_date`,`post_date`,`exp_date`,`note`,`society_id`,`isNotify`) values('".$_POST['notice_type_id']."','" .$_POST['issueby']. "','" .$_POST['subject']. "','" .$_POST['description']. "','" .getDBFormatDate($creation_date). "','" .getDBFormatDate($_POST['post_date']). "','" .getDBFormatDate($_POST['exp_date']). "','" .$_POST['note']. "','" .$_SESSION['society_id']. "','".$_POST['notify']."')";
				//echo $insert_notice;
				$res=$this->m_dbConn->insert($insert_notice);
				
				for($i=0;$i<sizeof($SendTo_Array);$i++)
				{
					if($SendTo_Array[$i]==0)
					{
						$sqldata="insert into `display_notices`(`notice_id`,`unit_id`) values(".$res.",".$SendTo_Array[$i].")";						
						$data=$this->m_dbConn->insert($sqldata);
					}
					else
					{
						$sqldata="insert into `display_notices`(`notice_id`,`unit_id`) values(".$res.",".$SendTo_Array[$i].")";						
						$data=$this->m_dbConn->insert($sqldata);
					}	
					
				}
				
				$this->objFetchData->objSocietyDetails->sSocietyEmail;
				if($_POST['notify'])
				{																
					$this->sendEmail($_POST['subject'],$_POST['description'], $SendTo_Array, $fileName, $res, $SendTo_Array[$i], $_SESSION['society_id'], $_SESSION["dbname"], 0, 0);
					
				}
					
				if($_POST['mobile_notify'])
				{	
				
				 //$MobileSubject = $this->objFetchData->objSocietyDetails->sSocietyName .' : ' .$_POST['events_title'] ;
					 // $this->SendMobileNotification($_POST['subject']);																	
					$this->SendMobileNotification($_POST['subject'], $SendTo_Array, $res, $SendTo_Array[$i], $_SESSION['society_id'], $_SESSION["dbname"]);
					
				}			
				return "Insert";
			}
			else
			{
				return "Record Not Inserted";
				
			}
		}
		else if($_POST['insert']=='Update' && $errorExists==0)
		{
			if($_POST['notice_creation_type'] == 2)
			{
				if($_FILES['userfile']['name'] != "")
				{									
					$fileTempName = $_FILES['userfile']['tmp_name'];  
					$fileSize = $_FILES['userfile']['size'];
					$fileName = time().'_'.basename($_FILES['userfile']['name']);
							
					$uploaddir = $_SERVER['DOCUMENT_ROOT']."/Notices";			   
					$uploadfile = $uploaddir ."/". $fileName;	
					echo $uploadfile;	
					if(move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile))
					{						
						$_POST['note'] = $fileName;
					}
					else
					{
						return "Error uploading file - check destination is writeable.";
					}
				}
			}	

			if($_POST['note'] <> "")
			{
				$sqlUpdate = "UPDATE `notices` SET `society_id`='" .$_SESSION['society_id']. "',`notice_type_id`='".$_POST['notice_type_id']."',`issuedby`='" .$_POST['issueby']. "',`subject`='" .$_POST['subject']. "',`description`='" .$_POST['description']. "',
						`note`='" .$_POST['note']. "',`post_date`='" .getDBFormatDate($_POST['post_date']). "',`exp_date`='" .getDBFormatDate($_POST['exp_date']). "',`isNotify`='".$_POST['notify']."' WHERE `id`='".$_POST['updaterowid']."'";
			}
			else
			{
				$sqlUpdate = "UPDATE `notices` SET `society_id`='" .$_SESSION['society_id']. "',`notice_type_id`='".$_POST['notice_type_id']."',`issuedby`='" .$_POST['issueby']. "',`subject`='" .$_POST['subject']. "',`description`='" .$_POST['description']. "',
						`post_date`='" .getDBFormatDate($_POST['post_date']). "',`exp_date`='" .getDBFormatDate($_POST['exp_date']). "',`isNotify`='".$_POST['notify']."' WHERE `id`='".$_POST['updaterowid']."'";
			}						
			$result = $this->m_dbConn->update($sqlUpdate);
			
			$sqlDelete = "DELETE FROM `display_notices` WHERE `notice_id` = '".$_POST['updaterowid']."'"; 
			$this->m_dbConn->delete($sqlDelete);
			
			$SendTo_Array = array();
			$notice=$_POST['post_noticeto'];
			
			if ($notice)
			{
				foreach ($notice as $value)
				{
					array_push($SendTo_Array,$value);
				}
			}
			
			for($i=0;$i<sizeof($SendTo_Array);$i++)
			{
				if($SendTo_Array[$i]==0)
				{
					$sqldata="insert into `display_notices`(`notice_id`,`unit_id`) values(".$_POST['updaterowid'].",".$SendTo_Array[$i].")";					
					$data=$this->m_dbConn->insert($sqldata);
				}
				else
				{
					$sqldata="insert into `display_notices`(`notice_id`,`unit_id`) values(".$_POST['updaterowid'].",".$SendTo_Array[$i].")";					
					$data=$this->m_dbConn->insert($sqldata);
				}					
			}
			
			if($_POST['notify'])
			{																
				$this->sendEmail($_POST['subject'],$_POST['description'], $SendTo_Array, $fileName,$_POST['updaterowid'],$SendTo_Array[0], $_SESSION['society_id'], $_SESSION["dbname"],0,0);
			}	
			
			return "Update";			
		}
		
	}
	public function combobox($query,$id)
	{
	$str.="<option value=''>Please Select</option>";
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
						if($v==$id)
						{
							$sel = "selected";
						}
						else
						{
							$sel = "";	
						}
						$str.="<OPTION VALUE=".$v.">";
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
	
	
	public function combobox2($query,$id)
	{
	$str.="<option selected='selected' value='0'>All</option>";
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
						if($v==$id)
						{
							$sel = "selected";
						}
						else
						{
							$sel = "";	
						}
						$str.="<OPTION VALUE=".$v.">";
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
	
	
	
	public function FetchNotices($nid=0)
	{
		$todayDate=date('Y-m-d');
		if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER))
		{
			
			if($nid <> 0)
			{
			//$sql="select * from `notices` where id=".$nid." and society_id=".$_SESSION['society_id']." ";
			//$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and noticetbl.id=".$nid." and noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.exp_date < '".$todayDate."' and displaynoticetbl.unit_id IN (0) ";
			$sql = "select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and noticetbl.id=".$nid." and noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y'  and noticetbl.exp_date >='".$todayDate."' ORDER BY noticetbl.exp_date DESC"; //and noticetbl.exp_date > '".$todayDate."'";			
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
			else{
			//$sql="select * from `notices` where society_id=".$_SESSION['society_id']." ";
			//$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id  and noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.exp_date < '".$todayDate."' and displaynoticetbl.unit_id IN (0)";
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y'  and noticetbl.exp_date >='".$todayDate."' ORDER BY noticetbl.exp_date DESC"; //and noticetbl.exp_date > '".$todayDate."'";			
			//echo "nid".$nid.$sql;
			$result=$this->m_dbConn->select($sql);
			}
		}
		else
		{
			if($nid <> 0)
			{
			//$sql="select * from `notices` where id=".$nid." and society_id=".$_SESSION['society_id']."";
			//$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and noticetbl.id=".$nid." and noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.exp_date < '".$todayDate."' and  displaynoticetbl.unit_id IN (".$_SESSION['unit_id'].",0) ";
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices as noticetbl,display_notices as displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and noticetbl.id=".$nid." and noticetbl.society_id=".$_SESSION['society_id']." and  displaynoticetbl.unit_id IN (".$_SESSION['unit_id'].",0) and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y' and noticetbl.exp_date >='".$todayDate."' ORDER BY noticetbl.exp_date DESC";
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
			else{
			//$sql="select * from `notices` where society_id=".$_SESSION['society_id']." ";
			//$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.exp_date < '".$todayDate."' and  displaynoticetbl.unit_id IN (".$_SESSION['unit_id'].",0) ";			
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices as noticetbl,display_notices as displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and  displaynoticetbl.unit_id IN (".$_SESSION['unit_id'].",0) and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y' and noticetbl.exp_date >='".$todayDate."' ORDER BY noticetbl.exp_date DESC";			
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
		}
	return $result;	
	}
	public function FetchAllNotices($nid=0)
	{
		$todayDate=date('Y-m-d');
		if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER))
		{
			
			if($nid <> 0)
			{
			$sql = "select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and noticetbl.id=".$nid." and noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y'  ORDER BY noticetbl.exp_date DESC"; //and noticetbl.exp_date > '".$todayDate."'";			
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
			else{
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y' ORDER BY noticetbl.exp_date DESC"; //and noticetbl.exp_date > '".$todayDate."'";			
			//echo "nid".$nid.$sql;
			$result=$this->m_dbConn->select($sql);
			}
		}
		else
		{
			if($nid <> 0)
			{
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices as noticetbl,display_notices as displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and noticetbl.id=".$nid." and noticetbl.society_id=".$_SESSION['society_id']." and  displaynoticetbl.unit_id IN (".$_SESSION['unit_id'].",0) and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y' ORDER BY noticetbl.exp_date DESC";
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
			else{
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices as noticetbl,display_notices as displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and  displaynoticetbl.unit_id IN (".$_SESSION['unit_id'].",0) and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y'  ORDER BY noticetbl.exp_date DESC";			
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
		}
	return $result;	
	}
	
	public function getcount()
	{
		$todayDate=date('Y-m-d');
		if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER))
		{
		//$sql="select count(*) as cnt from `notices` where  society_id=".$_SESSION['society_id']." ";
		//$sql="select noticetbl.id FROM notices as noticetbl JOIN display_notices as displaynoticetbl on displaynoticetbl.notice_id=noticetbl.id where noticetbl.exp_date > '".$todayDate." and displaynoticetbl.unit_id IN (0)";
		 $sql = "select noticetbl.id FROM notices as noticetbl JOIN display_notices as displaynoticetbl on displaynoticetbl.notice_id=noticetbl.id where noticetbl.exp_date >= '".$todayDate."'";
		//echo "countQuery :".$sql;
		$result=$this->m_dbConn->select($sql);
		}
		else
		{
			//$sql="select count(*) as cnt from `notices` where  society_id=".$_SESSION['society_id']." ";
			//$sql="select noticetbl.id FROM notices as noticetbl JOIN display_notices as displaynoticetbl on displaynoticetbl.notice_id=noticetbl.id where noticetbl.exp_date < '".$todayDate."' and displaynoticetbl.unit_id IN (0,".$_SESSION['unit_id'].")";
			$sql="select noticetbl.id FROM notices as noticetbl JOIN display_notices as displaynoticetbl on displaynoticetbl.notice_id=noticetbl.id where noticetbl.exp_date >= '".$todayDate."' and displaynoticetbl.unit_id IN (0,".$_SESSION['unit_id'].")";
			$result=$this->m_dbConn->select($sql);
		}
		return $result;
		
	}
	
	public function selecting()
	{
		$sql = "SELECT * FROM `notices` WHERE `id` = '".$_REQUEST['noticeId']."'";		
		//$sql = "select noticetbl.*,displaynoticetbl.unit_id FROM notices as noticetbl,display_notices as displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.id = '".$_REQUEST['noticeId']."' ";			
		$res = $this->m_dbConn->select($sql);
		
		$sql1 = "select displaynoticetbl.unit_id FROM notices as noticetbl,display_notices as displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.id = '".$_REQUEST['noticeId']."' ";			
		$result = $this->m_dbConn->select($sql1);
		for($i = 0; $i < sizeof($result); $i++)
		{		
			//$res[0]['unit'.$i] = $result[$i]['unit_id'];		
			$res[0]['unit'] .= $result[$i]['unit_id'].",";
		}
		
		//$res[0]['unitCount'] = $i;
		return $res;
	}
	
	public function deleting()
	{
		$sql0 = "select count(*)as cnt from `notices` WHERE `id`='".$_REQUEST['noticeId']."' and status='Y'";
		$res0 = $this->m_dbConn->select($sql0);
		
		if($res0[0]['cnt']>0)
		{	
			$sql1="update `notices` set status='N' where `id`='".$_REQUEST['noticeId']."'";
			echo $sql1;
			$this->m_dbConn->update($sql1);
			
			$sql2 = "update `display_notices` set status='N' WHERE `notice_id` = '".$_REQUEST['noticeId']."'";
			$this->m_dbConn->update($sql2);
			
			echo "msg1###".$_SESSION['ssid'].'###'.$_SESSION['wwid'];
		}
		else
		{
			echo "msg";	
		}
	}
	
	public function sendGreetingsEmail($subject, $desc, $SendTo_Array, $fileName, $GreetingID, $UnitID, $SocID, $DBName, $bCronjobProcess, $sSourceTableID, $sQueueIDs,$LoginType,$unitNo /*in case of logintype 0*/)
	{
		$mailSubject = $subject ;
		$mailBody = $desc;
		//echo "<br/>Notice to display:".sizeof($SendTo_Array);														
		$display = array();
		$EmailIDtoUnitIDs = array();					
		$sEmptyOrInvalidEmails = "";
		//print_r($SendTo_Array);														
		//echo "<br/>display:".sizeof($SendTo_Array);														
		for($i=0;$i<sizeof($SendTo_Array);$i++)
		{	
			//echo "SendTo_Array:[".$SendTo_Array[$i]."]";										
			if($SendTo_Array[$i]==0)
			{			
			//echo"[test]";				
				/*$sql = 'SELECT  mem_other_family.other_email, mem_other_family.other_name, member_main.email, member_main.owner_name FROM `mem_other_family` JOIN  `member_main` on mem_other_family.member_id = member_main.member_id JOIN `unit` on unit.unit_id = member_main.unit where unit.society_id = '.$SocID.' AND mem_other_family.send_commu_emails = 1 and member_main.member_id IN (SELECT  member_main.`member_id` FROM (select  `member_id` from `member_main` where `ownership_date` <= "NOW()"  ORDER BY `ownership_date` desc) as member_id Group BY unit)';// Group BY member_main.unit ';
				$result = $this->m_dbConn->select($sql);
				//echo "<br/>result:".$result ."";
				//print_r($result);*/

				$emailIDList = $this->objFetchData->GetEmailIDToSendNotification(0);

				//echo "emails:";
				//print_r($emailIDList);

				for($i = 0; $i < sizeof($emailIDList); $i++)
				{	
					if(($emailIDList[$i]['to_email'] <> "") && (isValidEmailID($emailIDList[$i]['to_email']) == true))
					{
						$display[$emailIDList[$i]['to_email']] = $emailIDList[$i]['to_name'];
						$EmailIDtoUnitIDs[$SendTo_Array[$i]] = $emailIDList[$i]['to_email'];
					}
					else
					{
						if($sEmptyOrInvalidEmails == "")
						{
							$sEmptyOrInvalidEmails = $SendTo_Array[$i];
						}
						else
						{
							$sEmptyOrInvalidEmails .= "," .$SendTo_Array[$i];
						}
					}
				}							
				break;
			}
			else
			{	
			//echo"[test]";						
				/*$sql = "SELECT mem_other_family.other_email, mem_other_family.other_name, member_main.email, member_main.owner_name FROM `mem_other_family` JOIN  `member_main` on mem_other_family.member_id = member_main.member_id JOIN `unit` on unit.unit_id = member_main.unit where unit.society_id = '".$SocID."' AND mem_other_family.send_commu_emails = 1 AND unit.unit_id = '".$SendTo_Array[$i]."'  and member_main.member_id IN (SELECT member_main.`member_id` FROM (select  `member_id` from `member_main` where ownership_date <='NOW()' ORDER BY ownership_date desc) as member_id Group BY unit)";// Group BY member_main.unit";  																					
				//echo $sql;
				$result = $this->m_dbConn->select($sql);	*/
				//echo"[test2]<br/>";
				//print_r($result);

				$emailIDList = $this->objFetchData->GetEmailIDToSendNotification($SendTo_Array[$i]);
				//print_r($emailIDList);
				for($iResultCnt = 0; $iResultCnt < sizeof($emailIDList); $iResultCnt++)
				{
					if(($emailIDList[$iResultCnt]['to_email'] <> "") && (isValidEmailID($emailIDList[$iResultCnt]['to_email']) == true))
					{
						$display[$emailIDList[$iResultCnt]['to_email']] = $emailIDList[$iResultCnt]['to_name'];
						$EmailIDtoUnitIDs[$i] = $SendTo_Array[$i];	
					}
					else
					{
						if($sEmptyOrInvalidEmails == "")
						{
							$sEmptyOrInvalidEmails = $SendTo_Array[$i];
						}
						else
						{
							$sEmptyOrInvalidEmails .= "," .$SendTo_Array[$i];
						}
					}
				}
			}							
		}
		//echo "size:".sizeof($display);														
		//print_r($display);
		//echo "unit to email id:";
		//print_r($EmailIDtoUnitIDs);
		if(sizeof($display) == 0)
		{
			echo 'Email ID Missing';
			$sqlUpdate = "Update `emailqueue` set `Status`=1 WHERE `SourceTableID` = '".$sSourceTableID."' and `UnitID` IN (".$sEmptyOrInvalidEmails.")  and `dbName`='".$DBName."' and `Status`=0"; 
			echo $sqlUpdate;
			$this->m_dbConnRoot->update($sqlUpdate);
			//exit();
		}
		else
		{							
												
			// Create the mail transport configuration					
			  $societyEmail = "";	  
			  if($this->objFetchData->objSocietyDetails->sSocietyEmail <> "")
			  {
				 $societyEmail = $this->objFetchData->objSocietyDetails->sSocietyEmail;
			  }
			  else
			  {
				 $societyEmail = "accounts@way2society.com";
			  }	  
			  try
			  {
				  $bccArray = array();
				  $bccUnitsArray = array();
				  
				  $iLimit = 0;
				  $iCounter = 0;
				  //for($iCnt = 0; $iCnt < sizeof($display); $iCnt++)
				  
				  //echo '<br/>Main Array:' . sizeof($display) . '<br/>';
				  //print_r($display);
				  
				  $obj_utility = new utility($this->m_dbConn, $this->m_dbConnRoot);
				  //echo "<br/>units <".  print_r($EmailIDtoUnitIDs) .">";
				  foreach($display as $key => $value)
				  {
					  
						  $bccEmailForQueueArray[$iLimit] = $value;
						  //echo "counter:<".$iCounter.">";
						  //echo "iLimit:<".$iLimit.">";
					  //echo "EmailIDtoUnitIDs:<".$EmailIDtoUnitIDs[$iCounter] .">";
					  $bccUnitsArray[$iLimit]= $EmailIDtoUnitIDs[$iCounter];
					  
					  $iLimit = $iLimit + 1;
					  $iCounter = $iCounter + 1;
					  $bSendMail = false;
					  if($iLimit == 10 || $iCounter == sizeof($display))
					  {
						  $bccArray[$key] = $value;
						  $bccArray['emailtracker@way2society.com'] = $this->objFetchData->objSocietyDetails->sSocietyName;
						  $bSendMail = true;
						  $iLimit = 0;
					  }
					  else
					  {
						  $bccArray[$key] = $value;
					  }
					  
				  		if($bSendMail == true)
						{
							$UnitsToClear = "";
							foreach($EmailIDtoUnitIDs as $IndexKey => $valueUnitID)
				  			{	
				  				echo "value: " . $valueUnitID . "<br>";
								if($UnitsToClear == "")
								{
									$UnitsToClear = $valueUnitID;
								}
								else
								{
				  					$UnitsToClear .= "," . $valueUnitID;
				  				}
				  			}
				  			echo "unit to clear: " . $UnitsToClear . "<br>";
				  			//print_r($SendTo_Array);
							//echo "bcc emails: <br>";
							
							$activationContent = "";
							//$activationCode = getRandomUniqueCode();
							if($LoginType == 0)
							{
								$UnitID = $SendTo_Array[0];
								echo "bcc emails: <br>".$key;
								$activationCode = getRandomUniqueCode();
								$MemberName =$bccArray[$key];
								$NewActivationCode = $key . substr($activationCode, 0,4);
								$name = "test name";
								//$iSocietyId =  $obj_utility->GetSocietyID($unitID);
								$insert_mapping = "INSERT INTO `mapping`(`society_id`, `unit_id`, `desc`, `code`,`code_type`, `role`, `created_by`, `view`,`status`) VALUES ('" . $SocID . "', '" . $UnitID . "', '" . $unitNo . "', '" . $NewActivationCode . "','1', '" . ROLE_MEMBER . "', '1', 'MEMBER','1')";
								$result_mapping = $this->m_dbConnRoot->insert($insert_mapping);
								$activationContent = $this->objInitialize->generateActivationEmailTemplate(true,$key,$MemberName,$activationCode, 1);	
							}
							//print_r($bccArray);
							//echo "unitstobcc:".print_r($bccUnitsArray);
							$EMailIDToUse = $obj_utility->GetEmailIDToUse(true, 3, "", $UnitID, 0, $DBName, $SocID, $GreetingID, $bccUnitsArray);
							
							
							//print_r($EMailIDToUse);
							
							$EMailID = $EMailIDToUse['email'];
							$Password = $EMailIDToUse['password'];
							//echo '<br/>Email ID To Use : [' . $EMailID . '][' . $Password . ']';
							if($EMailIDToUse['status'] == 0)				
							{
								//echo '<br/><br/>Limited Array : ' . sizeof($bccArray) . ' <br/>';
								//print_r($bccArray);
								$transport = Swift_SmtpTransport::newInstance('cs10.webhostbox.net', 465, "ssl")
								//$transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
										//->setUsername('no-reply@way2society.com')
										->setUsername($EMailID)
										->setSourceIp('0.0.0.0')
										//->setPassword('society123') ; 
										->setPassword($Password) ; 
																								
								// Create the message
								$message = Swift_Message::newInstance();
								
								if($this->objFetchData->objSocietyDetails->sSocietyEmail <> "")
								{
									$message->setTo(array(
									   "no-reply@way2society.com" => "way2society.com"
									));
									//$message->setTo(array(
									//$societyEmail => $societyName
									//));
								}
								
								$message->setBcc($bccArray);															
								 
								 $message->setReplyTo(array(
								   "no-reply@way2society.com" => "way2society.com"
								));

								 //$message->setReplyTo(array(
								   //$societyEmail => $societyName
								//));
								$mailBody = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
									<html xmlns="http://www.w3.org/1999/xhtml">
									 <head>
									  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />  
									  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
									</head>
									<body style="margin: 0; padding: 0;">					 
										<table align="center" border="1" bordercolor="#CCCCCC" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;">
										   <tr>
											 <td align="center" bgcolor="#D9EDF7" style="padding: 30px 0 20px 0;border-bottom:none;">
											   <img src="http://way2society.com/images/logo.png" alt="Way2Society.com"  style="display: block;" />
											  <br />
											  <i><font color="#43729F" size="4"><b> Way2Society.com - Housing Society Social & Accounting Software </b></font></i>
											 </td>
										   </tr>
										   <tr>
											 <td align="center">
											 <img src="https://way2society.com/images/new_year_2018.png" alt="Way2Society.com"  style="display: block;" />
											 </td>
										   </tr>';
								if($LoginType == 0)
								{		   
									$mailBody .= '<tr style="background-color:#FFF094"><td colspan="1" rowspan="1" style="padding-left:12px;padding-right:12px;padding-bottom:25px;padding-top:25px">' . $activationContent . '</td></tr>';		   
								}
								$mailBody .= '<tr>
											 <td bgcolor="#CCCCCC" style="padding: 2px 20px 2px 20px;border-top:none;">
											   <table cellpadding="0" cellspacing="0" width="100%">           
												 <td >             
													<a href="http://way2society.com/" target="_blank"><i>Way2Society</i></a>              
												 </td>
												 <td align="center"  style="padding: 0px 50px 0px 1px;">
										 		<table>
		                                 		<tr>
		                                 		<td><a href="https://play.google.com/store/apps/details?id=com.ionicframework.way2society869487&amp;rdid=com.ionicframework.way2society869487" target="_blank">
												<img src="http://way2society.com/images/app.png" width="120" height="50" style="style=" top:10px;"></a></td></tr>				
												</table>
		                                	 </td>
												 <td align="right">
												  <table border="0" cellpadding="0" cellspacing="0">
												   <tr>
													<td>
														<a href="https://twitter.com/way2society" target="_blank"><img src="http://way2society.com/images/icon2.jpg" alt="">'.$LoginType.'</a>                  
													</td>
													<td style="font-size: 0; line-height: 0;" width="20">&nbsp;&nbsp;</td>
													<td>
														<a href="https://www.facebook.com/way2soc" target="_blank"><img src="http://way2society.com/images/icon1.jpg" alt=""></a>                 
													</td>
												   </tr>
												  </table>
												 </td>
										   </tr>
										 </table>   
									</body>
									</html>';		   

								$message->setSubject($mailSubject);
								$message->setBody($mailBody);
								$message->setFrom($EMailID, $this->objFetchData->objSocietyDetails->sSocietyName);
								$message->setContentType("text/html");										 
													
								if($fileName != "")
								{
									$message->attach(Swift_Attachment::fromPath('../Notices/' . $fileName));
									//$message->attach(Swift_Attachment::fromPath('../Notices/' . $fileName));
								}
								// Send the email
								$mailer = Swift_Mailer::newInstance($transport);
								echo "ready to send email<br>";
								$result = $mailer->send($message);
								//$result = 1;
								if($result <> 0)
								{
									echo "<br/>Success";
								}
								else
								{
									echo "<br/>Failed";
								}
								//echo "<br/>cron:".$bCronjobProcess ."<br/>";
								if($bCronjobProcess)
								{
									//$sqlDelete = "DELETE FROM `emailqueue` WHERE `id` = '".$QueueID."'"; 
									//echo $sqlDelete;
									//$dbConnRoot->delete($sqlDelete);
									$sqlUpdate = "Update `emailqueue` set `Status`=1 WHERE `SourceTableID` = '".$sSourceTableID."' and `id` IN (".$sQueueIDs.")  and `Status`=0"; 
									echo $sqlUpdate;
									$this->m_dbConnRoot->update($sqlUpdate);
								}	
							}
							else
							{
								if($EMailIDToUse['status'] == 2)
								{
									
								}
								echo $EMailIDToUse['msg'];
							}
							$bccArray = array();
						}
				  }
			  }
			  catch(Exception $exp)
			  {
				echo "Error occured in email sending.".$exp;
			  }
	}
	}
	
	///----------------------------------------------Mobile Notification ----------------------------///
	
	public function SendMobileNotification($subject, $SendTo_Array, $NoticeID, $UnitID, $SocID, $DBName)
	{
		$NoticeTitle="Society Notice";
		$NoticeMassage = $subject ;
		$display = array();
		$EmailIDtoUnitIDs = array();					
													
		for($i=0;$i<sizeof($SendTo_Array);$i++)
		{		
		//echo "INside for loop";	
		//print_r($SendTo_Array[$i]);							
			//if($SendTo_Array[$i]==0)
			//{	
			//echo "if condition";	
			
				$emailIDList = $this->objFetchData->GetEmailIDToSendNotification(0);

				for($i = 0; $i < sizeof($emailIDList); $i++)
				{	
					if(($emailIDList[$i]['to_email'] <> "") )
					{
						
						$UnitID = $emailIDList[$i]['unit'];
						$objAndroid = new android($emailIDList[$i]['to_email'], $SocID, $UnitID);
						$sendMobile=$objAndroid->sendNoticeNotification($NoticeTitle,$NoticeMassage,$NoticeID);
					}
				//}
			}
			
			}
		
	}
}

?>