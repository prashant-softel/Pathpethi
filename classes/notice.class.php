<?php

include_once ("dbconst.class.php"); 
include_once( "include/fetch_data.php");
include_once( "utility.class.php");
include_once('../swift/swift_required.php');
include_once("android.class.php");
//echo "include_ gdrive";
include_once("../GDrive.php");


class notice
{
	public $actionPage = "../notices.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	public $objFetchData;
	public $m_obj_utility;
	public $m_bShowTrace;
	
	function __construct($dbConn, $dbConnRoot, $socID = "")
	{  
		$dbConnRoot=new dbop(true);
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->objFetchData = new FetchData($dbConn);
		$this->m_obj_utility = new utility($dbConn, $dbConnRoot);
		$this->m_bShowTrace = true;
		//echo "soc check".$socID;
		if($socID <> "")
		{
			$this->objFetchData->GetSocietyDetails($socID);	
		}
		else
		{
			$this->objFetchData->GetSocietyDetails($_SESSION['society_id']);
		}
		//echo "checked";
	}
	
	public function AddNotice($society_id, $login_id, $dbname, $IssuedBy, $subject, $description, $note, $NoticeDate, $expDate, $noticeType, $noticeCreationType, $postToNotice, $noticeTypeID, $notify, $mobilenotify, $file)
	{
		//echo "NOtice Date : ".$postToNotice;
	
		
		if($IssuedBy <> '' && $subject <> '' && $NoticeDate <> ''  && $noticeType <> '')
		{
			
			//die();
			$docGDriveID = "";
			//echo  "Type :".$NoticeCreationType;
			if($noticeCreationType == 2)
			{
				//echo "insert:".$NoticeDate;
				$notice_type = "0000-00-00";
				$PostDate = "0000-00-00";
				if($expDate == '')
				{
					$expDate = "00-00-0000";
				}	
				if($file == "")
				{
					echo "Please select file to upload.";
					//return;
				}
				else
				{
					$notice_type = $noticeType;
					$PostDate = $NoticeDate;
					//echo "Post Date :".$PostDate."Exp Date : ".$expDate;	
					$resResponse = $this->m_obj_utility->UploadAttachment($_FILES, $notice_type, $PostDate, "Notices");
					$sStatus = $resResponse["status"];
					$sMode = $resResponse["mode"];
					$sFileName = $resResponse["response"];
					$sUploadFileName = $resResponse["FileName"];
					$note = $sUploadFileName;

					if($sMode == "1")
					{
						$uploaded_filename = $sFileName;
						//$_POST['note'] = $resResponse["note"];
					} 
					else if($sMode == "2")
					{
						$docGDriveID = $sFileName;
					}
					else
					{
						//failure or no file uploaded
					}
					//echo "gdif:".$docGDriveID;
					//die();
				}
					
			}
				
				$creation_date=date('d-m-Y');
				$noticeToArray = array();
				$notice=$postToNotice;
				
				$doc_type = $noticeType;
				
				if($notice)
				{
					
					foreach ($notice as $value)
					{
					
						array_push($noticeToArray,$value);
						
					}
				}
				
				$sNoticeVersion = '2';
				if($docGDriveID != "" && $docGDriveID != "Error")
				{
					$sNoticeVersion = '2';
				}
				
				 $insert_notice="insert into `notices`(`notice_type_id`,`issuedby`,`subject`,`description`,`creation_date`,`post_date`,`exp_date`,`note`,`society_id`,`isNotify`,`doc_id`,`notice_version`,`attachment_gdrive_id`) values('".$noticeTypeID."','" .$IssuedBy. "','" .$subject. "','" .$description. "','" .getDBFormatDate($creation_date). "','" .getDBFormatDate($NoticeDate). "','" .getDBFormatDate($expDate). "','" .$note. "','" .$society_id. "','".$notify."','".$doc_type."','".$sNoticeVersion."','".$docGDriveID."')";
				 //$insert_notice;
				//die();
				$res=$this->m_dbConn->insert($insert_notice);
				//print_r($res);
				$bEnableSaveTemplate = 0;
				if($bEnableSaveTemplate == 1)
				{
					 $sqlQry = "insert into `document_templates`(`template_subject`,`template_name`,`template_data`) values ('".$subject."','".$subject."','".$description."') ";
					$resDoc=$this->m_dbConnRoot->insert($sqlQry);
				}
				
				for($i=0;$i<sizeof($noticeToArray);$i++)
				{
					
					if($noticeToArray[$i]==0)
					{
						$sqldata="insert into `display_notices`(`notice_id`,`unit_id`) values(".$res.",".$noticeToArray[$i].")";						
						$data=$this->m_dbConn->insert($sqldata);
					}
					else
					{
						 $sqldata="insert into `display_notices`(`notice_id`,`unit_id`) values(".$res.",".$noticeToArray[$i].")";						
						$data=$this->m_dbConn->insert($sqldata);
					}	
					
				}
				$this->objFetchData->objSocietyDetails->sSocietyEmail;
				if($notify)
				{	
				//echo "Call Email Function<br>";		
				//print_r($noticeToArray[$i]);	
				//if($_SERVER['HTTP_HOST']<>"localhost")
				//{
					 $this->sendEmail($subject,$description, $noticeToArray, $fileName, $res, $noticeToArray[$i], $society_id, $dbname, 0, 0);
				//}
				
					
				
				
			}
				
				if($notifybyMobile)
				{	
				
				 															
					//$this->SendMobileNotification($subject, $noticeToArray, $res, $noticeToArray[$i], $society_id, $dbname);
					
				}
		//}
				$logMsg = "Added New Notice". " | " . " Sent to :". implode(",",$noticeToArray) ." | Notify Flag :". $notify . " | Mobile_Notify Flag :" .$_POST['mobile_notify']. " | Version : ". $sNoticeVersion. " | UploadedDocID :". $docGDriveID;
				$insertQuery = "INSERT INTO `change_log`(`ChangedLogDec`, `ChangedBy`, `ChangedTable`, `ChangedKey`) VALUES ('" . $this->m_dbConn->escapeString($logMsg) . "','".$login_id."','Notice','".$res."')";										
					$this->m_dbConn->insert($insertQuery);			
				return "Insert";
			}
	
			else
			{
				return "Record Not Inserted. Please make sure all mandatory values are enterted.";
				
			}
	
	}
	public function startProcess()
	{
		if($this->m_bShowTrace)
		{
			echo $_POST['insert'];			
		}				
		$errorExists=0;
		if($_POST['insert']=='Submit' && $errorExists==0)
		{
			$society_id = $_SESSION['society_id'];
			$login_id = $_SESSION['login_id'];
			$dbname = $_SESSION["dbname"];
			$IssuedBy = $_POST['issueby'];
			$subject = $_POST['subject'];
			$description = $_POST['description'];
			$note = $_POST['note'];
			$NoticeDate = $_POST['post_date'];
			$expDate = $_POST['exp_date'];
			$noticeType = $_POST['notice_type'];
			$noticeCreationType = $_POST['notice_creation_type'];
			$postToNotice = $_POST['post_noticeto'];
			$noticeTypeID = $_POST['notice_type_id'];
			$notify = $_POST['notify'];;
			$mobilenotify = $_POST['mobile_notify'];
			$file = $_FILES['userfile']['name'];
			
			 $this->AddNotice($society_id, $login_id, $dbname, $IssuedBy, $subject, $description, $note, $NoticeDate, $expDate, $noticeType, $noticeCreationType, $postToNotice, $noticeTypeID, $notify, $mobilenotify, $file);	
		
		}
		
		else if($_POST['insert']=='Update' && $errorExists==0)
		{
			$docGDriveID = "";
			$sMode = "";	
			//die();
			if($_POST['notice_creation_type'] == 2)
			{
				$notice_type = "0000-00-00";
					$PostDate = "0000-00-00";
					
				if($_FILES['userfile']['name'] == "")
				{
					//echo "Please select file to upload.";
					//return;
					$_POST["note"] = "";
				}
				else
				{
					$notice_type = $_POST["notice_type"];
					$PostDate = $_POST['post_date'];
					
						//echo "trace:".$notice_type.$PostDate;
					//$docGDriveID = $this->UploadAttachment($_FILES, $notice_type,$PostDate);

					//$notice_type = $_POST["doc_type"];
					//$PostDate = $_POST['post_date'];
					//echo "trace:".$notice_type.$PostDate;
					//$docGDriveID = $this->UploadAttachment($_FILES, $notice_type,$PostDate);
					//die();
					$resResponse = $this->m_obj_utility->UploadAttachment($_FILES, $notice_type, $PostDate, "Notices");
					$sStatus = $resResponse["status"];
					$sMode = $resResponse["mode"];
					$sFileName = $resResponse["response"];
					
					$sUploadFileName = $resResponse["FileName"];
					$_POST['note'] = $sUploadFileName;

					if($sMode == "1")
					{
						$uploaded_filename = $sFileName;
						$_POST['note'] = $resResponse["note"];
					} 
					else if($sMode == "2")
					{
						$docGDriveID = $sFileName;
					}
					else
					{
						//failure or no file uploaded
					}
					//die();
					
					//echo "gdif:".$docGDriveID;
					//die();
				}
			}	
			//echo "exp:".$_POST['exp_date'];
			//die();
			$sNoticeVersion = '2';
			$doc_type = $_POST['notice_type'];
			if($docGDriveID != "" && $docGDriveID != "Error")
			{
				$sNoticeVersion = '2';
			}

			//echo "<pre>";
			//print_r($_POST);
			//echo "</pre>";
			//die();
			if($sMode != "")
			{
				$sqlUpdate = "UPDATE `notices` SET `society_id`='" .$_SESSION['society_id']. "',`notice_type_id`='".$_POST['notice_type_id']."',`issuedby`='" .$_POST['issueby']. "',`subject`='" .$_POST['subject']. "',`description`='" .$_POST['description']. "',
						`note`='" .$_POST['note']. "',`post_date`='" .getDBFormatDate($_POST['post_date']). "',`exp_date`='" .getDBFormatDate($_POST['exp_date']). "',`isNotify`='".$_POST['notify']."',`doc_id`='".$doc_type."',`doc_template_id`='".$_REQUEST["notice_template"]."', `notice_version`='".$sNoticeVersion."',`attachment_gdrive_id`='".$docGDriveID."' WHERE `id`='".$_POST['updaterowid']."'";
			}
			else
			{
				$sqlUpdate = "UPDATE `notices` SET `society_id`='" .$_SESSION['society_id']. "',`notice_type_id`='".$_POST['notice_type_id']."',`issuedby`='" .$_POST['issueby']. "',`subject`='" .$_POST['subject']. "',`description`='" .$_POST['description']. "',
						`post_date`='" .getDBFormatDate($_POST['post_date']). "',`exp_date`='" .getDBFormatDate($_POST['exp_date']). "',`isNotify`='".$_POST['notify']."',`doc_id`='".$doc_type."',`doc_template_id`='".$_REQUEST["notice_template"]."',`notice_version`='".$sNoticeVersion."' WHERE `id`='".$_POST['updaterowid']."'";
			}					
			//echo "sql:".$sqlUpdate;	
			$result = $this->m_dbConn->update($sqlUpdate);
			
			$sqlDelete = "DELETE FROM `display_notices` WHERE `notice_id` = '".$_POST['updaterowid']."'"; 
			$this->m_dbConn->delete($sqlDelete);
			
			$noticeToArray = array();
			$notice=$_POST['post_noticeto'];
			
			if ($notice)
			{
				foreach ($notice as $value)
				{
					array_push($noticeToArray,$value);
				}
			}
			
			for($i=0;$i<sizeof($noticeToArray);$i++)
			{
				if($noticeToArray[$i]==0)
				{
					$sqldata="insert into `display_notices`(`notice_id`,`unit_id`) values(".$_POST['updaterowid'].",".$noticeToArray[$i].")";					
					$data=$this->m_dbConn->insert($sqldata);
				}
				else
				{
					$sqldata="insert into `display_notices`(`notice_id`,`unit_id`) values(".$_POST['updaterowid'].",".$noticeToArray[$i].")";					
					$data=$this->m_dbConn->insert($sqldata);
				}					
			}
			
			if($_POST['notify'])
			{																
				$this->sendEmail($_POST['subject'],$_POST['description'], $noticeToArray, "",$_POST['updaterowid'],$noticeToArray[0], $_SESSION['society_id'], $_SESSION["dbname"],0,0);
			}	
			
			$logMsg = "Added New Notice". " | " . " Sent to :". implode(",",$noticeToArray) ." | Notify Flag :". $_POST['notify'] . " | Mobile_Notify Flag :" .$_POST['mobile_notify']. " | Version : ". $sNoticeVersion. " | UploadedDocID :". $docGDriveID;
			$insertQuery = "INSERT INTO `change_log`(`ChangedLogDec`, `ChangedBy`, `ChangedTable`, `ChangedKey`) VALUES ('" . $this->m_dbConn->escapeString($logMsg) . "','".$_SESSION['login_id']."','Notice','".$result."')";										
				$this->m_dbConn->insert($insertQuery);			
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
	public function comboboxRoot($query,$id)
	{
	$str.="<option value=''>Please Select</option>";
	$data = $this->m_dbConnRoot->select($query);
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
	
	
	
	public function FetchNotices($nid=0, $UnitID = 0)
	{
		//echo "FetchNotices";
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
			$ReqUnitID = 0;
			if($UnitID == 0)
			{
				$ReqUnitID = $_SESSION['unit_id'];
			}
			else
			{
				$ReqUnitID = $UnitID;
			}
			if($nid <> 0)
			{
			//$sql="select * from `notices` where id=".$nid." and society_id=".$_SESSION['society_id']."";
			//$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and noticetbl.id=".$nid." and noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.exp_date < '".$todayDate."' and  displaynoticetbl.unit_id IN (".$_SESSION['unit_id'].",0) ";
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices as noticetbl,display_notices as displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and noticetbl.id=".$nid." and noticetbl.society_id=".$_SESSION['society_id']." and  displaynoticetbl.unit_id IN (".$ReqUnitID.",0) and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y' and noticetbl.exp_date >='".$todayDate."' ORDER BY noticetbl.exp_date DESC";
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
			else{
			//$sql="select * from `notices` where society_id=".$_SESSION['society_id']." ";
			//$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.exp_date < '".$todayDate."' and  displaynoticetbl.unit_id IN (".$_SESSION['unit_id'].",0) ";			
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices as noticetbl,display_notices as displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and  displaynoticetbl.unit_id IN (".$ReqUnitID.",0) and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y' and noticetbl.exp_date >='".$todayDate."' ORDER BY noticetbl.exp_date DESC";			
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
		}
		//echo sizeof($result);
	return $result;	
	}
	public function FetchAllNotices($nid=0, $UnitID = 0)
	{
		//echo "fetch all";
		$todayDate=date('Y-m-d');
		if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER))
		{
			
			if($nid <> 0)
			{
			$sql = "select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and noticetbl.id=".$nid." and noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y'  ORDER BY noticetbl.exp_date DESC"; //and noticetbl.exp_date > '".$todayDate."'";			
			// echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
			else{
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y' ORDER BY noticetbl.exp_date DESC"; //and noticetbl.exp_date > '".$todayDate."'";			
			///echo "nid".$nid.$sql;
			$result=$this->m_dbConn->select($sql);
			}
		}
		else
		{
			$ReqUnitID = 0;
			if($UnitID == 0)
			{
				$ReqUnitID = $_SESSION['unit_id'];
			}
			else
			{
				$ReqUnitID = $UnitID;
			}
			if($nid <> 0)
			{
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices as noticetbl,display_notices as displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and noticetbl.id=".$nid." and noticetbl.society_id=".$_SESSION['society_id']." and  displaynoticetbl.unit_id IN (".$ReqUnitID.",0) and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y' ORDER BY noticetbl.exp_date DESC";
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
			else{
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices as noticetbl,display_notices as displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and  displaynoticetbl.unit_id IN (".$ReqUnitID.",0) and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y'  ORDER BY noticetbl.exp_date DESC";			
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
		}
	return $result;	
	}
	public function FetchAllNoticesEx($nid=0, $UnitID = 0, $bDocsMode = false)
	{
		//echo "fetch all";
		$todayDate=date('Y-m-d');
		if($bDocsMode == false && $_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER))
		{
			
			if($nid <> 0)
			{
			$sql = "select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and noticetbl.id=".$nid." and noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y'  ORDER BY noticetbl.exp_date DESC"; //and noticetbl.exp_date > '".$todayDate."'";			
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
			else{
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices noticetbl,display_notices displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y' ORDER BY noticetbl.exp_date DESC"; //and noticetbl.exp_date > '".$todayDate."'";			
			//echo "nid".$sql;
			$result=$this->m_dbConn->select($sql);
			}
		}
		else
		{
			$ReqUnitID = 0;
			if($UnitID == 0)
			{
				$ReqUnitID = 0;
			}
			else
			{
				$ReqUnitID = $UnitID;
			}
			if($nid <> 0)
			{
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices as noticetbl,display_notices as displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and noticetbl.id=".$nid." and noticetbl.society_id=".$_SESSION['society_id']." and  displaynoticetbl.unit_id IN (".$ReqUnitID.",0) and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y' ORDER BY noticetbl.exp_date DESC";
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			}
			else{
			$sql="select noticetbl.*,displaynoticetbl.unit_id FROM notices as noticetbl,display_notices as displaynoticetbl WHERE noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and  displaynoticetbl.unit_id IN (".$ReqUnitID.",0) and noticetbl.status = 'Y' and displaynoticetbl.status = 'Y'  ORDER BY noticetbl.exp_date DESC";			
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
		//print_r($res);
		for($i = 0; $i < sizeof($result); $i++)
		{		
			//$res[0]['unit'.$i] = $result[$i]['unit_id'];		
			$res[0]['unit'] .= $result[$i]['unit_id'].",";
			$res[0]['post_date'] = getDisplayFormatDate($res[0]['post_date']);
			$res[0]['exp_date'] = getDisplayFormatDate($res[0]['exp_date']);
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
	public function GetAttachmentFileLink($NoticeID)
	{
		$arAttachment = array();
		$sql = "select * from `notices` where id='".$NoticeID."'";
		$result = $this->m_dbConn->select($sql);
		$sGDriveID = "";
		if(isset($result["0"]["attachment_gdrive_id"]) && $result["0"]["attachment_gdrive_id"] != "" || $result["0"]["attachment_gdrive_id"] != "-")
		{
			$sGDriveID = $result["0"]["attachment_gdrive_id"];
		}
		$sW2S_Uploaded_file = "";
		if(isset($result["0"]["note"]) && $result["0"]["note"] != "")
		{
			$sW2S_Uploaded_file = $result["0"]["note"];
		}
		$sNoticeVersion = $result["0"]["notice_version"];
		
		$arAttachment["notice_version"] = $sNoticeVersion;
		if($sNoticeVersion == "1")
		{
			$arAttachment["attachment_file"] = $sW2S_Uploaded_file;
			$arAttachment["Source"] = "1";//w2s
		}
		else if($sNoticeVersion == "2")
		{
			if($sGDriveID != "")
			{
				$arAttachment["attachment_file"] = "https://drive.google.com/file/d/". $sGDriveID ."/view";
				$arAttachment["Source"] = "2";//gdrive
			}
			else
			{
				$arAttachment["attachment_file"] = $sW2S_Uploaded_file;	
				//$arAttachment["attachment_file"] = "1518789971_anurag.xlsx";
				$arAttachment["Source"] = "1";//w2s
			}
		}
		else
		{

		}
		return $arAttachment;
	}
	
	public function sendEmail($subject, $desc, $noticeToArray, $fileName, $NoticeID, $UnitID, $SocID, $DBName, $bCronjobProcess, $QueueID)
	{
		$mailSubject = $subject ;
		$mailBody = $desc;
		//echo "<br/>Notice to display:".sizeof($noticeToArray);														
		$display = array();
		$EmailIDtoUnitIDs = array();

		$resAttachment = $this->GetAttachmentFileLink($NoticeID);
		//print_r($resAttachment);
		//die();
		if($resAttachment["notice_version"] == "2" && $resAttachment["Source"] == "2" && $resAttachment["attachment_file"] != "")
		{
			$mailBody .= "<br>Please find attachment :". $resAttachment["attachment_file"];
		}
		//print_r($noticeToArray);														
		//die();
		//echo "<br/>display:".sizeof($noticeToArray);														
		for($i=0;$i<sizeof($noticeToArray);$i++)
		{	
			//echo "noticeToArray:[".$noticeToArray[$i]."]";										
			if($noticeToArray[$i]==0)
			{			
			//echo"[test]";				
				/*$sql = 'SELECT  mem_other_family.other_email, mem_other_family.other_name, member_main.email, member_main.owner_name FROM `mem_other_family` JOIN  `member_main` on mem_other_family.member_id = member_main.member_id JOIN `unit` on unit.unit_id = member_main.unit where unit.society_id = '.$SocID.' AND mem_other_family.send_commu_emails = 1 and member_main.member_id IN (SELECT  member_main.`member_id` FROM (select  `member_id` from `member_main` where `ownership_date` <= "NOW()"  ORDER BY `ownership_date` desc) as member_id Group BY unit)';// Group BY member_main.unit ';
				$result = $this->m_dbConn->select($sql);
				//echo "<br/>result:".$result ."";
				//print_r($result);*/

				$emailIDList = $this->objFetchData->GetEmailIDToSendNotification(0);

				//print_r($emailIDList);

				for($i = 0; $i < sizeof($emailIDList); $i++)
				{	
					if(($emailIDList[$i]['to_email'] <> "") && (isValidEmailID($emailIDList[$i]['to_email']) == true))
					{
						$display[$emailIDList[$i]['to_email']] = $emailIDList[$i]['to_name'];
						$EmailIDtoUnitIDs[$noticeToArray[$i]] = $emailIDList[$i]['to_email'];
					}
				}							
				break;
			}
			else
			{	
			//echo"[test]";						
				/*$sql = "SELECT mem_other_family.other_email, mem_other_family.other_name, member_main.email, member_main.owner_name FROM `mem_other_family` JOIN  `member_main` on mem_other_family.member_id = member_main.member_id JOIN `unit` on unit.unit_id = member_main.unit where unit.society_id = '".$SocID."' AND mem_other_family.send_commu_emails = 1 AND unit.unit_id = '".$noticeToArray[$i]."'  and member_main.member_id IN (SELECT member_main.`member_id` FROM (select  `member_id` from `member_main` where ownership_date <='NOW()' ORDER BY ownership_date desc) as member_id Group BY unit)";// Group BY member_main.unit";  																					
				//echo $sql;
				$result = $this->m_dbConn->select($sql);	*/
				//echo"[test2]<br/>";
				//print_r($result);

				$emailIDList = $this->objFetchData->GetEmailIDToSendNotification($noticeToArray[$i]);
				//print_r($emailIDList);
				for($iResultCnt = 0; $iResultCnt < sizeof($emailIDList); $iResultCnt++)
				{
					if(($emailIDList[$iResultCnt]['to_email'] <> "") && (isValidEmailID($emailIDList[$iResultCnt]['to_email']) == true))
					{
						$display[$emailIDList[$iResultCnt]['to_email']] = $emailIDList[$iResultCnt]['to_name'];
						$EmailIDtoUnitIDs[$i] = $noticeToArray[$i];	
					}
				}
			}							
		}
		//echo "size:".sizeof($display);														
		//print_r($display);
		//die();
		if(sizeof($display) == 0)
		{
			echo '<br>Error 003: Email ID Missing.<br>';
			return;
			//exit();
		}							
												
		// Create the mail transport configuration					
	  $societyEmail = "";	  
	  if($this->objFetchData->objSocietyDetails->sSocietyEmail <> "")
	  {
		 $societyEmail = $this->objFetchData->objSocietyDetails->sSocietyEmail;
	  }
	  else
	  {
		 $societyEmail = "societyaccounts@pgsl.in";
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
				//  echo "iLimit:<".$iLimit.">";
			  //echo "EmailIDtoUnitIDs:<".$EmailIDtoUnitIDs[$iCounter] .">";
			  $bccUnitsArray[$iLimit]= $EmailIDtoUnitIDs[$iCounter];
			  //die();
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
					//echo "unitstobcc:".print_r($bccUnitsArray);

					$EMailIDToUse = $this->m_obj_utility->GetEmailIDToUse(true, 1, "", $UnitID, 0, $DBName, $SocID, $NoticeID, $bccUnitsArray);
					
					
					//print_r($EMailIDToUse);
					
					$EMailID = $EMailIDToUse['email'];
					$Password = $EMailIDToUse['password'];
					//echo '<br/>Email ID To Use : [' . $EMailID . '][' . $Password . ']';
					//die();
					if($EMailIDToUse['status'] == 0)				
					{
						//echo '<br/><br/>Limited Array : ' . sizeof($bccArray) . ' <br/>';
						//print_r($bccArray);
						$transport = Swift_SmtpTransport::newInstance('cs10.webhostbox.net', 465, "ssl")
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
							   $societyEmail => $societyName
							));
						}
						
						$message->setBcc($bccArray);															
						 
						 $message->setReplyTo(array(
						   $societyEmail => $societyName
						));
						
						$message->setSubject($mailSubject);
						$message->setBody($mailBody);
						$message->setFrom($EMailID, $this->objFetchData->objSocietyDetails->sSocietyName);
						$message->setContentType("text/html");
						//echo "src:".$resAttachment["Source"] ;
						//echo "attch:".$resAttachment["attachment_file"];	
						//die();									 
						//$sPath = "https://drive.google.com/uc?authuser=0&id=1k2xk6IQwyhzBZ58bJS6tpmlYESeyzbW2&export=download";
						if($resAttachment["Source"] == "1"  && $resAttachment["attachment_file"] != "")
						{
							echo "attaching...";
							//$message->attach(Swift_Attachment::fromPath($resAttachment["attachment_file"]));
							//$message->attach(Swift_Attachment::fromPath("https://drive.google.com/file/d/1k2xk6IQwyhzBZ58bJS6tpmlYESeyzbW2/view"));
							//$message->attach(Swift_Attachment::fromPath("https://drive.google.com/file/d/1k2xk6IQwyhzBZ58bJS6tpmlYESeyzbW2"));
							//$message->attach(Swift_Attachment::fromPath($sPath));
							$message->attach(Swift_Attachment::fromPath('../Notices/' . $resAttachment["attachment_file"]));
						}
						// Send the email
						$mailer = Swift_Mailer::newInstance($transport);
						
						$result = $mailer->send($message);
						
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
							$sqlUpdate = "Update `emailqueue` set `Status`=1 WHERE `SourceTableID` = '".$QueueID."' and `Status`=0"; 
							//echo $sqlUpdate;
							$this->m_dbConnRoot->update($sqlUpdate);
						}
						//die();	
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
		echo "Error occured in email sending.";
	  }
	}
	
	///----------------------------------------------Mobile Notification ----------------------------///
	
	public function SendMobileNotification($subject, $noticeToArray, $NoticeID, $UnitID, $SocID, $DBName)
	{
		$NoticeTitle="Society Notice";
		$NoticeMassage = $subject ;
		$display = array();
		$EmailIDtoUnitIDs = array();					
													
		for($i=0;$i<sizeof($noticeToArray);$i++)
		{		
		//echo "INside for loop";	
		//print_r($noticeToArray[$i]);							
			//if($noticeToArray[$i]==0)
			//{	
			//echo "if condition";	
				$UnitNo = $noticeToArray[$i];
				$emailIDList = $this->objFetchData->GetEmailIDToSendNotification(0);

				for($i = 0; $i < sizeof($emailIDList); $i++)
				{	
					if(($emailIDList[$i]['to_email'] <> "") )
					{
						
						$UnitID = $emailIDList[$i]['unit'];
						//echo "<br>email:".$emailIDList[$i]['to_email'];	
						//echo "<br>unit:".$emailIDList[$i]['unit'];	
						//echo "<br>unit:".$noticeToArray[$i];	
						
						if($UnitNo == $UnitID)
						{
							//echo "<br>matched";
						
							$objAndroid = new android($emailIDList[$i]['to_email'], $SocID, $UnitID);
							$sendMobile=$objAndroid->sendNoticeNotification($NoticeTitle,$NoticeMassage,$NoticeID);
						}
					}
				//}
			}
			
			}
		
	}
	public function fetch_template_details($id)
	{	
		$sqlQuery = "select * from document_templates where id='".$id."'";
		$res = $this->m_dbConnRoot->select($sqlQuery);
		return $res[0];
	}
	public function UploadAttachment($arFILES, $notice_type,$PostDate)
	{
		$docGDriveID = "";
		try
		{
			//echo "start";
			//die();
			$fileTempName = $arFILES['userfile']['tmp_name'];  
			$fileSize = $arFILES['userfile']['size'];
			$fileName = time().'_'.basename($arFILES['userfile']['name']);
			if($_SERVER['HTTP_HOST']=="localhost")
			{		
				$uploaddir = $_SERVER['DOCUMENT_ROOT']."/beta_aws_9/Notices";			   
			}
			else
			{
				$uploaddir = $_SERVER['DOCUMENT_ROOT']."/Notices";			   
			}
			$uploadfile = $uploaddir ."/". $fileName;	
			if($this->m_bShowTrace)
			{
				echo $uploadfile;	
			}
			
			//die();
			$resSociety = $this->m_obj_utility->GetGDriveDetails();
			$sGDrive_W2S_ID = $resSociety["0"]["GDrive_W2S_ID"];
			$ObjGDrive = new GDrive($this->m_dbConn, "0", $sGDrive_W2S_ID, 0);
			if($this->m_bShowTrace)
			{
				echo "uploading to gdrive from tenant:".$documentName. ".".$fileExt ."|".$random_name ."|".$uploadedfile;	
			}
			$mimeType = $arFILES['userfile']['type'];
			$documentName = time() . "_" . $arFILES['userfile']['name'] ;
			$noticeFileName = $documentName;
			$sqlDocName = "select doc_type from `document_type` where ID='".$notice_type."'";
			$resDocName = $this->m_dbConn->select($sqlDocName);
			if($this->m_bShowTrace)
			{
				echo "doctype:".$NoticeAlias = $resDocName[0]["doc_type"];
				echo "notice_type:".$notice_type;
			}
			//die();
			//$str = "Lease//".$start;
			$folderName = $NoticeAlias . "//".$PostDate; 
			if($this->m_bShowTrace)
			{
				echo "path:".$folderName;
			
			echo "filename:".$noticeFileName ." mime:". $mimeType ." tmpname:". $arFILES['userfile']['tmp_name'] ." folderName:". $folderName;
			echo "W2SGD:".$sGDrive_W2S_ID;
			}
			if($sGDrive_W2S_ID != "")
			{
			//$mimeType = 'application/vnd.google-apps.file';
				$UploadedFiles = $ObjGDrive->UploadFiles($noticeFileName , $noticeFileName, $mimeType, $arFILES['userfile']['tmp_name'], $folderName, $folderName, "", "", $sGDrive_W2S_ID, "0");
			}
			else
			{
				if(move_uploaded_file($arFILES['userfile']['tmp_name'], $uploadfile))
				{
					$_POST['note'] = $fileName;
				}
				else
				{
					echo "Error uploading file - check destination is writeable.";
					return "";
				}
			}
			if($this->m_bShowTrace)
			{
				echo "<br>uploadfile:";
				echo "<pre>";
				print_r($UploadedFiles);

				echo "</pre>";
			}
			$_POST["note"] = $noticeFileName;
			if($UploadedFiles["status"] == 1)
			{
				$docGDriveID = $UploadedFiles["response"]["id"];
				echo "file uploaded successfully to gdrive.";
			}
			else
			{
				//$docGDriveID = $UploadedFiles["status"][0][""];
				$docGDriveID = "Error";
			}
		}
		catch(Exception $exp)
		 {
			echo "Error occured in uploading document. Details are:".$exp->getMessage();
			die();
		 }
		return $docGDriveID;
	}
	public function GetUnitDescriptionsFromNotices()
	{
		$sqlQuery = "select distinct(unit_id) from display_notices";
		$res = $this->m_dbConn->select($sqlQuery);
		return $res;
	}
	public function getComment($ID)
	{
		$comment="select * from reversal_credits where ID='".$ID."' ";
		//echo $comment;
		$com=$this->m_dbConn->select($comment);
		//print_r($com);
		return $com;
	}
}

?>