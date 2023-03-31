<?php
include_once ("dbconst.class.php");
include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
//echo "dm";
include_once("activate_user_email.class.php");
		include_once("/../GDrive.php");
//echo "dm2";
include_once("../ImageManipulator.php");
include_once("../utility.class.php");
include_once("../GDrive.php");
//error_reporting(7);

class tenant 
{
	public $actionPage = "../tenant.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_utility;
	public $obj_activation ;
	public $m_bShowTrace;
	
	function __construct($dbConn, $dbConnRoot)
	{
		//echo 'Inside const tenant';
		$this->display_pg=new display_table();
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_bShowTrace = 0;
		$this->obj_utility=new utility($this->m_dbConn, $this->m_dbConnRoot);
		
		$this->obj_activation = new activation_email($dbConn, $dbConnRoot);
	}

	public function startProcess()
	{
		$errorExists = 0;
		//print_r($_SESSION);
		//$UnitID = $_SESSION["unit_id"];
		//$sqlSociety = "select GDrive_W2S_ID from `society`";
		//$objConn = new dbop($m_dbConn);
		$unit_id=$_POST['unit_id']; 
		$sqlUnit = "select unit_no from `unit` where unit_id='".$unit_id."'";
		$resUnit = $this->obj_utility->GetUnitDesc($unit_id);
		if($this->m_bShowTrace)
		{
			echo $sqlUnit;		
		}
		$UnitNo = $resUnit[0]["unit_no"];
		if($this->m_bShowTrace)
		{
			echo "unitid:".$unit_id."no".$UnitNo;
			print_r($resUnit);
		}
		$ResDocTypes = $this->m_dbConn->select("select ID from document_type where doc_type='Lease'");
		//print_r($ResDocTypes);
		$doc_type = "";
		if(isset($ResDocTypes[0]["ID"]))
		{
			$doc_type = $ResDocTypes[0]["ID"];
		}
		else
		{
			echo "<br>Lease Document type not found";
		}		
				
		if($_REQUEST['insert']=='Submit' && $errorExists==0)
		{

			$start=getDBFormatDate($_POST['start_date']);
			$end=getDBFormatDate($_POST['end_date']);
			
			//print_r($resSociety);
			

			$subFolderName = "";
			$subFolderDesc = "";
			//$subFolderName = 'Lease';
			//$subFolderDesc = 'Lease files';
			
			$mimeType="";
			$description="";
			$file_tmp_name="";
			$today = date("Y-m-d");    // 2018-01-20

			$str = $UnitNo ."//Lease//".$start;
			if($this->m_bShowTrace)
			{
				echo "path:".$str;
			}
			$parts = explode("//", $str);

			//var_dump($parts);
			//die(); 
			$fileName = "";
			$doc_id=array();				
			 $doc=$_POST['doc_count'];
			 $members=$_POST['count'];
			 $finalMemberCount = $members;
								 $insert_query="insert into `tenant_module` (`unit_id`,`tenant_name`,`agent_name`,`agent_no`,`members`,`create_date`,`start_date`,`end_date`,`note`) values ('".$unit_id."','".$_POST['t_name']."','".$_POST['agent']."','".$_POST['agent_no']."','".$members."','".date('Y-m-d')."','".$start."','".$end."','".$_POST['note']."')";
			 $result=$this->m_dbConn->insert($insert_query);
			// return "Insert";
			//die();
			 $RefId=$result;
			for($i=1; $i<=$doc; $i++)
			{
				$PostDate = $_POST['start_date'];         		                    
					//$Note = $_POST["note"];
				$docGDriveID = "";
				$random_name = "";
				$doc_name = "";
				
				$doc_name = $_POST["doc_name_".$i];
					//die();
				$resResponse = $this->obj_utility->UploadAttachment($_FILES,  $doc_type, $PostDate, "Uploaded_Documents", $i, true, $UnitNo);
				$sStatus = $resResponse["status"];
				$sMode = $resResponse["mode"];
				$sFileName = $resResponse["response"];
				$sUploadFileName = $resResponse["FileName"];

				//$doc_name = $resResponse["doc_name"];
				//$random_name = $resResponse["file_name"];
				if($sMode == "1")
				{
					$random_name = $sFileName;
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
				$sDocVersion = '2';
				if($GdriveDocID != "")
				{
					$sDocVersion = '2';
				}

				$sDocVersion = '2';
				if($GdriveDocID != "")
				{
					$sDocVersion = '2';
				}

				$insert_query="insert into `documents` (`Name`, `Unit_Id`,`refID`,`Category`, `Note`,`Document`,`source_table`,`doc_type_id`,`doc_version`,`attachment_gdrive_id`) values ('".$doc_name."', '".$unit_id."','".$RefId."','0', '','".$sUploadFileName."','1','2','".$sDocVersion."','".$docGDriveID."')";
				//echo "ins:".$insert_query;
					$data=$this->m_dbConn->insert($insert_query);
					//die();
			}
			

		   	for($i=1;$i <= $members;$i++)
			{
				$addmembers= $_POST['members_'.($i)];
				$addrelation=$_POST['relation_'.($i)];
				$memDOB=getDBFormatDate($_POST['mem_dob_'.($i)]);
				$number=$_POST['contact_'.($i)];
				$email=$_POST['email_'.($i)];
				//echo $addmembers."and ".$addrelation;

				if($i > 1)
				{
					$_POST['other_send_commu_emails'] = 0;
				}
				if($addmembers <> '')
				{
				 	$sqldata="insert into `tenant_member`(`tenant_id`,`mem_name`,`relation`,`mem_dob`,`contact_no`,`email`,`send_act_email`,`send_commu_emails`) values('".$result."','".$addmembers."','".$addrelation."','".$memDOB."','".$number."','".$email."','".$_POST['chkCreateLogin']."','".$_POST['other_send_commu_emails']."')";						
				$data=$this->m_dbConn->insert($sqldata);
				}
				else
				{
					$finalMemberCount--;
				}
				$up_query="update `tenant_module` set  `members`='".$finalMemberCount."' where tenant_id='".$result."'";
				$data = $this->m_dbConn->update($up_query);
			}
			//print_r($_POST);
			$this->SendActivationEmail();
				
			$this->actionPage = "../view_member_profile.php?id=" . $_POST['mem_id'];
			return "Insert";
	}
	
	/*-------------------------------------------------Update --------------------------------------------------------------------------------------------------------*/
	/*-----------------------------------------------------------------------------------------------------------------------------------------------------------------*/
		
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			//print_r($_REQUEST);
			$start = getDBFormatDate($_POST['start_date']);
			$folderName = $UnitNo ."//Lease//".$start;
			
			echo "hi";
				 $baseDir = dirname( dirname(__FILE__) );
				 $Tenant_id=$_POST['tenant_id'];
				 //$doc_id=$_REQUEST['doc_id'];
				 
				// print_r($_POST);
				$members=$_POST['count'];
			 	$finalMemberCount = $members;
			   $up_query="update `tenant_module` set  `tenant_name`='".$_POST['t_name']."',`agent_name`='".$_POST['agent']."',`agent_no`='".$_POST['agent_no']."',`members`='".$members."',`start_date`='".getDBFormatDate($_POST['start_date'])."',`end_date`='".getDBFormatDate($_POST['end_date'])."',`note`='".$_POST['note']."',`active`='".$_POST['varified']."' where tenant_id='".$Tenant_id."'";
			$data = $this->m_dbConn->update($up_query);

			$doc_Count=$_POST['doc_count'];				
			$fileName = "";				
			for($i=1; $i<=$doc_Count; $i++)
			{
				if( $_FILES['userfile'.$i]['name']<>"")
				{

					
					$PostDate = $_POST['start_date'];         		                    
					//$Note = $_POST["note"];
					$docGDriveID = "";
					$random_name = "";
					$doc_name = $_POST["doc_name_".$i];
					//die();
					$resResponse = $this->obj_utility->UploadAttachment($_FILES,  $doc_type, $PostDate, "Uploaded_Documents", $i, true, $UnitNo);
					$sStatus = $resResponse["status"];
					$sMode = $resResponse["mode"];
					$sFileName = $resResponse["response"];
					$sUploadFileName = $resResponse["FileName"];

					//$doc_name = $resResponse["doc_name"];
					//$random_name = $resResponse["file_name"];
					if($sMode == "1")
					{
						$random_name = $sFileName;
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
					$sDocVersion = '2';
					if($GdriveDocID != "")
					{
						$sDocVersion = '2';
					}
				 	$insert_query="insert into `documents` (`Name`, `Unit_Id`,`refID`, `Category`, `Note`,`Document`,`source_table`,`doc_type_id`,`doc_version`,`attachment_gdrive_id`) values ('".$doc_name."', '".$unit_id."','".$Tenant_id."','0', '','".$sUploadFileName."','1','2','".$sDocVersion."','".$docGDriveID."')";
					$doc_id=$this->m_dbConn->insert($insert_query);
				}
			}
			 
			  
			if($members <>'')
			{
				$del_member="delete from `tenant_member` where tenant_id='".$Tenant_id."'";
				$del_list=$this->m_dbConn->delete($del_member);
				for($i=1;$i <= $members;$i++)
				{
					$addmembers= $_POST['members_'.($i)];
					$addrelation=$_POST['relation_'.($i)];
					$Memdob=getDBFormatDate($_POST['mem_dob_'.($i)]);
					$number=$_POST['contact_'.($i)];
					$email=$_POST['email_'.($i)];

					if($i > 1)
					{
						$_POST['other_send_commu_emails'] = 0;
					}
				
					if($addmembers <> '')
					{
						$sqldata="insert into `tenant_member`(`tenant_id`,`mem_name`,`relation`,`mem_dob`,`contact_no`,`email`,`send_act_email`,`send_commu_emails`) values('".$Tenant_id."','".$addmembers."','".$addrelation."','".$Memdob."','".$number."','".$email."','".$_POST['chkCreateLogin']."','".$_POST['other_send_commu_emails']."')";						
						$data=$this->m_dbConn->insert($sqldata);
					}
					else
					{
						$finalMemberCount--;
					}
				
				}
				
				$up_query="update `tenant_module` set  `members`='".$finalMemberCount."' where tenant_id='".$Tenant_id."'";
				$data = $this->m_dbConn->update($up_query);
			}
			//print_r($_POST);
			$this->SendActivationEmail();
			//$this->actionPage = "../tenant.php?mem_id=" . $_POST['mem_id'];
			$this->actionPage = "../view_member_profile.php?id=" . $_POST['mem_id'];
		return "Update";
		}
		else
		{
			return $errString;
		}
	}
	
	public function SendActivationEmail()
	{
		if($_POST['chkCreateLogin'] == "1")
			{
				//echo "chk".$_POST['chkCreateLogin'];
				//die();
				$role = ROLE_MEMBER;
				$unit_id  = $_POST["unit_id"];
				$code  = $_POST["Code"];
				$society_id = $_SESSION['society_id'];
				$NewUserEmailID = $_REQUEST['email_1'];
				$DisplayName = $_POST['members_1'];
				//echo "unit:".$unit_id  ." code:".	$code ." email:".$NewUserEmailID ." name:".$DisplayName ;
				
				$ActivationStatus = $this->obj_activation->AddMappingAndSendActivationEmail($role, $unit_id, $society_id, $code, $NewUserEmailID, $DisplayName);
				//echo "status:".$ActivationStatus;
				
				if($ActivationStatus != "Success")
				{				
					return "Unable to Send Activation Email.";
				}
				//die();
			}
	}
	public function combobox($query)
	{
	}
	public function display1($rsas)
	{
		$thheader = array('t_name','mob','alt_mob','email','members','start_date','end_date','p_varification','upload','note');
		$this->display_pg->edit		= "gettenant";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "tenant.php";

		$res = $this->display_pg->display_new($rsas);
		return $res;
	}
	public function pgnation()
	{
		$sql1 = "select id,`t_name`,`mob`,`alt_mob`,`email`,`members`,`start_date`,`end_date`,`p_varification`,`upload`,`note` from  where status='Y'";
		$cntr = "select count(status) as cnt from  where status='Y'";

		$this->display_pg->sql1		= $sql1;
		$this->display_pg->cntr1	= $cntr;
		$this->display_pg->mainpg	= "tenant.php";

		$limit	= "50";
		$page	= $_REQUEST['page'];
		$extra	= "";

		$res	= $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
	}
	public function selecting($Tenant_Id)
	{
		//$sql = "select t.tenant_name,t.mobile_no,t.alter_no,t.email,t.start_date,t.end_date,t.p_varification,t.note, from `tenant_module` as t LEFT JOIN `Documents` as d on t.doc_id=d.doc_id where t.tenant_id='".$Tenant_Id."'"; 
		//$sql = "select  tenant_name,dob, mobile_no, alter_no, email, start_date, end_date, note  from `tenant_module` where tenant_id='".$Tenant_Id."'"; 
		//$sql = "select  tenant_id,unit_id, tenant_name,start_date, end_date,  agent_name, agent_no, note  from `tenant_module` where tenant_id='".$Tenant_Id."'"; 
		$sql = "select t.tenant_id,t.unit_id, u.unit_no, t.tenant_name,t.start_date, t.end_date, t.agent_name, t.agent_no, t.note from `tenant_module` t, `unit` u where tenant_id='".$Tenant_Id."' and t.unit_id = u.unit_id";
		$result=$this->m_dbConn->select($sql);
			$arrayTenant = array();
			if($result<>'')
			{
				//echo "hi";
			//$result[0]['dob'] = getDisplayFormatDate($result[0]['dob']);
			$result[0]['start_date'] = getDisplayFormatDate($result[0]['start_date']);
			$result[0]['end_date'] = getDisplayFormatDate($result[0]['end_date']);
			$sqldata="select `tenant_id`,`mem_name`,`relation`,`mem_dob`,`contact_no`,`email`,`send_act_email`,`send_commu_emails` from `tenant_member` where `tenant_id`='".$Tenant_Id."'";						
			$res1=$this->m_dbConn->select($sqldata);
			
			
			$result[0]['members'] = array();
			for($i=0;$i<sizeof($res1);$i++)
			{
				$res1[$i]['mem_dob'] = getDisplayFormatDate($res1[$i]['mem_dob']);
				array_push($result[0]['members'], $res1[$i]);
			}
			$result[0]['documents']=array();
			//$doc_id=$result[0]['doc_id'];
			$unit=$result[0]['unit_id'];
			$sqlDoc="select `doc_id`,`Name`,`Unit_Id`,`Document` from `documents` where `refId`='".$Tenant_Id."' and (status='Y' or status='') and Unit_Id='".$unit."'";
			$res2=$this->m_dbConn->select($sqlDoc);
			for($i=0;$i<sizeof($res2);$i++)
			{
			array_push($result[0]['documents'], $res2[$i]);
			}
			
			
	}
		
		return $result;
	}
	public function deleting($Tenant_id)
	{
		$sql = "update `tenant_module` set status='N' where tenant_id='".$Tenant_id."'";
		$res = $this->m_dbConn->update($sql);
	}
	
	
														/*--------------------------------------------  Show tenant list------------------------------------------------------*/
														
	public function getRecords()
	{
		 $sql =" select t.*,d.* from `tenant_module` as t LEFT JOIN `documents` as d on t.doc_id=d.doc_id where t.status='Y'";
		$result=$this->m_dbConn->select($sql);
		//echo $sql;
		return $result;
	}
	public function getTenantDocuments($UnitID = 0)
	{
		
		//$UnitID = $_SESSION["unit_id"];
		 $sql =" select t.*,d.* from `tenant_module` as t LEFT JOIN `documents` as d on t.tenant_id=d.refID where t.status='Y' and d.source_table=1";
		 //echo "unitid:".$UnitID ;
		 if($SESSION['role']!=ROLE_ADMIN && $_SESSION['role']!=ROLE_SUPER_ADMIN && $_SESSION['role']!=ROLE_ADMIN_MEMBER)
		 {
		 	$sql .= " and t.unit_id='".$UnitID."'";
		 }
		$result=$this->m_dbConn->select($sql);
		//echo $sql;
		return $result;
	}
	public function getTenantDocumentsNew($UnitID = 0)
	{
		
		//$UnitID = $_SESSION["unit_id"];
		 $sql =" select t.* from `tenant_module` as t where t.status='Y'";
		 //echo "unitid:".$UnitID ;
		// if($SESSION['role']!=ROLE_ADMIN && $_SESSION['role']!=ROLE_SUPER_ADMIN && $_SESSION['role']!=ROLE_ADMIN_MEMBER)
		 if($UnitID != 0)
		 {
		 	$sql .= " and t.unit_id='".$UnitID."'";
		 }
		$result=$this->m_dbConn->select($sql);
		//echo $sql;
		return $result;
	}
	
													/*-----------------------------------------------show image and document from edit ---------------------------------*/
	public function getViewDetails($TenantId)
	{
		 $sql="select t.tenant_id,t.doc_id,t.img,t.active,d.Document from `tenant_module` as t LEFT JOIN `documents` as d on d.refID=t.tenant_id where t.tenant_id='".$TenantId."' and d.status='Y'";
		$result=$this->m_dbConn->select($sql);
		return $result;
	}
	
												/*-------------------------------------------------------------- Menber profile function page--------------------------------------------------------*/
	public function getTenantRecords($unit_id)
	{	
	$sql ="select * from `tenant_module` where status='Y' and `unit_id`='".$unit_id."' and end_date >= CURDATE()";
		//
		// $sql ="select *, Count(t.unit_id) as counttotal from `tenant_module` as t LEFT JOIN `Documents` as d on t.doc_id=d.doc_id where t.status='Y' and t.`unit_id`='".$unit_id."' and t.end_date >= now()";
		//$sql =" select* from `tenant_module` as t LEFT JOIN `Documents` as d on t.doc_id=d.doc_id where t.status='Y' and t.`unit_id`='".$unit_id."'";
		 $result=$this->m_dbConn->select($sql);
		 $Tenant_Id=$result[0]['tenant_id'];
		if($Tenant_Id<>'')
			{
				$sqldata="select `tenant_id`,`mem_name`,`relation`,`mem_dob` ,`contact_no`,`email`,`send_act_email`,`send_commu_emails` from `tenant_member` where `tenant_id`='".$Tenant_Id."'";						
				$res1=$this->m_dbConn->select($sqldata);
				$result[0]['Allmembers']=$res1;
			}
			if($Tenant_Id<>'')
			{
			$sqldoc="select * from documents where refID='".$Tenant_Id."' and status in('Y','') and unit_id='".$unit_id."'";
			//echo "sql:".$sqldoc;	
			$res2=$this->m_dbConn->select($sqldoc);
			$result[0]['Alldocuments']=$res2;
			}
			
			$sqlCount ="select  Count(unit_id) as counttotal from `tenant_module`  where status='Y' and `unit_id`='".$unit_id."'" ;
			$res3=$this->m_dbConn->select($sqlCount);
			$result[0]['Count']=$res3[0]['counttotal'];
			if($Tenant_Id<>'')
			{
				$result[0]['Count'] = $result[0]['Count'] - 1;
			}
			if($this->m_bShowTrace)
			{
				echo "<pre>";
				print_r($result);
				echo "</pre>";
			}
		return $result;
	}
													
													
													/*-------------------------------------------------------------- Member List  form Unit --------------------------------------------------------*/
			public function MemberList($unit)
			{
			//	$sql =" select* from `tenant_module` as t LEFT JOIN `Documents` as d on t.doc_id=d.doc_id where t.status='Y' and t.unit_id='".$unit."'";
				$sql =" select *, u.unit_no from `tenant_module` as t LEFT JOIN `documents` as d on t.doc_id=d.doc_id join unit as u on t.unit_id=u.unit_id where t.status='Y' and t.unit_id='".$unit."'";
				$result=$this->m_dbConn->select($sql);
				return $result;
				
			}
			
			public function getViewDetailsUser($TenantId)
			{
			$data=$this->selecting($TenantId);
			
				return $data;	
		}
		
		/*-------------------------------------------------------------Notificatino alert-------------------------------------------------*/
		
	public function TenantAlert()
	{
		if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER))
		{
		//$sql="select * ,u.unit_no from `tenant_module` as t join unit as u on u.unit_id=t.unit_id where t.end_date >= TIMESTAMPADD( DAY , -30, NOW( )+ INTERVAL 5 HOUR + INTERVAL 30 MINUTE) t.status='Y' and t.active='1' order by t.tenant_id desc";
		//$sql="select *,u.unit_no from `tenant_module` as t join unit as u on u.unit_id=t.unit_id where t.end_date >= DATE(now()) and t.end_date <= DATE_ADD(DATE(now()), INTERVAL 1 Month) and t.status='Y' and t.active='1' order by t.tenant_id desc";
		$sql="select *,u.unit_no, w.wing from `tenant_module` as t join unit as u on u.unit_id=t.unit_id join wing as w on u.wing_id=w.wing_id where t.end_date >= DATE(now()) and t.end_date <= DATE_ADD(DATE(now()), INTERVAL 1 Month) and t.status='Y' and t.active='1' order by t.tenant_id desc";
		
		}
		else
		{
			$sql="select *,u.unit_no, w.wing from `tenant_module` as t join unit as u on u.unit_id=t.unit_id join wing as w on u.wing_id=w.wing_id where t.unit_id='".$_SESSION['unit_id']."' and  t.end_date >= DATE(now()) and t.end_date <= DATE_ADD(DATE(now()), INTERVAL 1 Month) and t.status='Y' and t.active='1' order by t.tenant_id desc";
		}
		$result=$this->m_dbConn->select($sql);
		return $result;
	}
}
?> 