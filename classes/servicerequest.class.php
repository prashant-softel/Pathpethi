<?php

include_once ("dbconst.class.php"); 
include_once("include/dbop.class.php");
include_once("latestcount.class.php");

include_once( "include/fetch_data.php");

include_once('../swift/swift_required.php');
include_once("../ImageManipulator.php");
include_once("utility.class.php");

class servicerequest
{
	//public $actionPage = "../addnotice.php";
	public $m_dbConn;
	public $m_dbConnRoot;	
	public $objFetchData;
	public $m_objUtility;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;		
		$this->m_dbConnRoot = new dbop(true);		
		$this->objFetchData = new FetchData($dbConn);
		$this->objFetchData->GetSocietyDetails($_SESSION['society_id']);	
		$this->m_objUtility = new utility($dbConn,$this->m_dbConnRoot);
	}		
	
	public function startProcess()
	{
		$errorExists=0;
	if($_POST['insert']=='Submit' && $errorExists==0)
	{
		date_default_timezone_set('Asia/Kolkata');	
		$image_list=array(); 
		for($i=0; $i<count($_FILES['img']['name']); $i++)
			{
				//print_r($_FILES);
				$file_type=$_FILES['img']['type'][$i];
				$file_size=$_FILES['img']['size'][$i];
				$file_tmp=$_FILES['img']['tmp_name'][$i];
				list($txt, $ext) = explode(".", $file);
				$randon_name = $file.".".$ext;
				$kaboom = explode(".", $_FILES['img']['name'][$i]); // Split file name into an array using the dot
				 $fileExt = end($kaboom);
				 $random_name= rand();
				//echo $random_name;
			
				if($_FILES["img"]['name'][$i]<>'')
				{
				if ($_FILES["img"]["size"][$i] > 10240*1024) 
				{
					 $error="Sorry, your file is too large.";
					 $this->table .= "Sorry, your file is too large.";
				}
				else if (($_FILES["img"]["type"][$i] == "image/gif") || 
						($_FILES["img"]["type"][$i] == "image/jpeg") || 
						($_FILES["img"]["type"][$i]== "image/png") || 
						($_FILES["img"]["type"][$i] == "image/pjpeg")) 
				{
					//echo "2";
			
					if ($_FILES["img"]["type"][$i] == "image/jpeg")
					{ //echo"jpeg type";
						$url =$random_name.'.'.$fileExt;
					}
					else if($_FILES["img"]["type"][$i] == "image/png")
					{//echo"png type";
						$url =$random_name.'.'.$fileExt;
					}
					else if ($_FILES["img"]["type"][$i] == "image/gif")
					{
						$url =$random_name.'.'.$fileExt;
					}
					//echo $random_name.'.'.$fileExt;
		 $manipulator = new ImageManipulator($_FILES['img']['tmp_name'][$i]);
		 
       $newImage = $manipulator->resample(1024, 683);
	
        $manipulator->save('../upload/main/' . $random_name.'.'.$fileExt);
		
		array_push($image_list,$random_name.'.'.$fileExt);
			}
		}
	}
		 $image_collection = implode(',', $image_list);
		//  echo $image_collection;
		//echo "in startprocess".$_SESSION['society_id']."<br />";	
		//echo $_POST['reportedby'];
		$obj_LatestCount = new latestCount($this->m_dbConn);
		$request_no = $obj_LatestCount->getLatestRequestNo($_SESSION['society_id']);
		//$request_no = $request_no + 1;
		  $sql = "INSERT INTO `service_request` (`request_no`, `society_id`, `reportedby`, `dateofrequest`, `email`, `phone`, `priority`, `category`, `summery`,`img`, `details`, `status`, `unit_id`) VALUES ('".$request_no."', '".$_SESSION['society_id']."', '".$_POST['reported_by']."', '".getDBFormatDate(date('d-m-Y'))."', '".$_POST['email']."', '".$_POST['phone']."', '".$_POST['priority']."', '".$_POST['category']."', '".$_POST['summery']."','$image_collection', '".$_POST['details']."', 'Raised', '".$_POST['unit_no']."')";					
		//echo "query:".$sql;  	
		$result = $this->m_dbConn->insert($sql);
		$sqlSR = $this->GetCategoryDetails( $_POST['category']);
		$EmailIDOfCategory = ""; 
		if(isset($sqlSR) && sizeof($sqlSR) > 0)
		{
			$EmailIDOfCategory = $sqlSR[0]['email'];
			$CCEmailIDOfCategory = $sqlSR[0]['email_cc'];
		}
		//echo $EmailIDOfCategory;
		$this->sendEmail($request_no, $_POST['reportedby'], 'Raised', $_POST['details'], $_POST['email'], $EmailIDOfCategory,$CCEmailIDOfCategory);
		
	}
	
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			$id=$_REQUEST['request_id'];
			//echo $id;
			$image_list=array(); 
			$select = "select `img` FROM `service_request` WHERE request_id='".$id."'";
			$res2 =$this->m_dbConn->select($select);
				//print_r($res2);
				 $image=$res2[0]['img'];
				if($image <> "")
				{
					$image_list = explode(',', $image);
				}
		for($i=0; $i<count($_FILES['img']['name']); $i++)
			{
				//print_r($_FILES);
				$file_type=$_FILES['img']['type'][$i];
				$file_size=$_FILES['img']['size'][$i];
				$file_tmp=$_FILES['img']['tmp_name'][$i];
				list($txt, $ext) = explode(".", $file);
				$randon_name = $file.".".$ext;
				$kaboom = explode(".", $_FILES['img']['name'][$i]); // Split file name into an array using the dot
				 $fileExt = end($kaboom);
				 $random_name= rand();
				//echo $random_name;
			
				if($_FILES["img"]['name'][$i]<>'')
				{
				if ($_FILES["img"]["size"][$i] > 10240*1024) 
				{
					 $error="Sorry, your file is too large.";
					 $this->table .= "Sorry, your file is too large.";
				}
				else if (($_FILES["img"]["type"][$i] == "image/gif") || 
						($_FILES["img"]["type"][$i] == "image/jpeg") || 
						($_FILES["img"]["type"][$i]== "image/png") || 
						($_FILES["img"]["type"][$i] == "image/pjpeg")) 
				{
					//echo "2";
			
					if ($_FILES["img"]["type"][$i] == "image/jpeg")
					{ //echo"jpeg type";
						$url =$random_name.'.'.$fileExt;
					}
					else if($_FILES["img"]["type"][$i] == "image/png")
					{//echo"png type";
						$url =$random_name.'.'.$fileExt;
					}
					else if ($_FILES["img"]["type"][$i] == "image/gif")
					{
						$url =$random_name.'.'.$fileExt;
					}
					//echo $random_name.'.'.$fileExt;
		 $manipulator = new ImageManipulator($_FILES['img']['tmp_name'][$i]);
		 
       $newImage = $manipulator->resample(1024, 683);
	
        $manipulator->save('../upload/main/' . $random_name.'.'.$fileExt);
		
		array_push($image_list,$random_name.'.'.$fileExt);
			}
		}
	}
		 $image_collection = implode(',', $image_list);
		//print_r($_REQUEST);
		
		 $up_query="update `service_request` set `email`='".$_POST['email']."',`phone`='".$_POST['phone']."',`priority`='".$_POST['priority']."',`category`='".$_POST['category']."',`summery`='".$_POST['summery']."', `details`='".$_POST['details']."' ,`img`='$image_collection' where  `request_id`='".$id."' and `society_id`=".$_SESSION['society_id']." ";
			//die();
			$data = $this->m_dbConn->update($up_query);
			//echo $data;
			//die();
			$return_value="Update";
			//return $result;
		}
		
		
}
	public function insertComments($request_no,$email, $ccEmails)
	{
		if($_SESSION['role'] && $_SESSION['role']==ROLE_ADMIN)
		{
			$updateReqPriority="update `service_request` set `priority`='".$_POST['priority']."' where  `request_no`=".$request_no." and `society_id`=".$_SESSION['society_id']." ";
			$priority = $this->m_dbConn->update($updateReqPriority);
		}
		
		$sql = "INSERT INTO `service_request` (`request_no`, `society_id`, `reportedby` , `summery`, `status`, `unit_id`,`email`) VALUES ('".$request_no."', '".$_SESSION['society_id']."', '".$_POST['changedby']."', '".$_POST['comments']."', '".$_POST['status']."', '".$_POST['unit']."', '".$_POST['emailID']."')";						
		//echo $sql;		
		$result = $this->m_dbConn->insert($sql);
		$this->sendEmail($request_no, $_POST['changedby'], $_POST['status'], $_POST['comments'], $email, $ccEmails);
		return;		
	}
	
	public function GetCategoryDetails($sCategory)
	{
		$sqlSRQuery  = "select ID, category, email,email_cc from `servicerequest_category` where ID='". $sCategory."'";
		return $this->m_dbConn->select($sqlSRQuery);
	}
		
	public function GetMemberName($sCategory)
	{
		//select c.category, c.member_id, m.mem_other_family_id, m.other_name from `servicerequest_category` c, `mem_other_family` m where m.mem_other_family_id = c.member_id and c.ID=9
		$sqlSRQuery  = "select c.category, c.member_id, m.mem_other_family_id, m.other_name from `servicerequest_category` c, `mem_other_family` m where m.mem_other_family_id = c.member_id and c.ID='". $sCategory."'";
		return $this->m_dbConn->select($sqlSRQuery);
	}
	
	public function GetUnitNoIfZero($request_no)
	{
		$sqlSRQuery = "SELECT unit_id FROM `service_request` WHERE service_request.`society_id` = '".$_SESSION['society_id']."' AND `request_no` = '".$request_no."'  and  `visibility`='1'";	
		return $this->m_dbConn->select($sqlSRQuery);
	}
	
	public function GetUnitNoIfNZero($request_no)
	{
		//SELECT s.unit_id, u.unit_no FROM `unit` u, `service_request` s where u.unit_id = s.unit_id
		//SELECT s.unit_id, u.unit_no FROM `service_request` s, `unit` u WHERE s.unit_id=u.unit_id AND s.`society_id` = '59' AND s.`request_no` = '53' and s.`visibility`='1'
		$sqlSRQuery = "SELECT s.`unit_id`, u.`unit_no` FROM `service_request` s, `unit` u WHERE s.`unit_id` = u.`unit_id` AND s.`society_id` = '".$_SESSION['society_id']."' AND s.`request_no` = '".$request_no."'  and  s.`visibility`='1' and u.unit_id = s.unit_id";	
		return $this->m_dbConn->select($sqlSRQuery);
	}
		
	public function getDetails()
	{
		$sql = "SELECT * FROM `member_main` WHERE `unit` = '".$_SESSION['unit_id']."' AND `society_id` = '".$_SESSION['society_id']."'";
		$result = $this->m_dbConn->select($sql);
		return $result;	
	}	
	
	public function getRecords($id, $type="")
	{
		if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER))
		{
			
			$sql = "SELECT m1.* FROM service_request m1 LEFT JOIN service_request m2 ON (m1.request_no = m2.request_no AND m1.request_id < m2.request_id) WHERE  m2.request_id IS NULL  and m1.`visibility`='1'";
		}
		else
		{
			  $sql = "SELECT m1.* FROM service_request m1 LEFT JOIN service_request m2 ON (m1.request_no = m2.request_no AND m1.request_id < m2.request_id) WHERE m2.request_id IS NULL AND m1.unit_id = '".$_SESSION['unit_id']."' and m1.visibility='1' ";
				}
		if($type <> "" && $type == "resolved")
		{
			 $sql .= '  and  m1.status="Resolved" OR  m1.status="Closed" ';	
		}
		else if($type <> "resolved") 
		{
			//echo $sql .= '  and m1.status <> "Resolved" OR m1.status <> "Closed"';	
			 $sql .= '   NOT IN ( m1.status="Resolved", m1.status="Closed")';	
		}
		
		$sql .= '  ORDER BY m1.request_no DESC';
		//echo $sql;
		$result = $this->m_dbConn->select($sql);
		//print_r($result);
		for($i=0;$i<count($result);$i++)
		{
			$sql="select * from service_request where request_no='".$result[$i]['request_no']."' order by timestamp DESC";
			$res1 = $this->m_dbConn->select($sql);
			//print_r($res1);
			//die();
			$result[$i]['status']=$res1[0]['status'];
			$result[$i]['dateofrequest'] = $res1[(sizeof($res1)-1)]['dateofrequest']; 
			$result[$i]['priority'] = $res1[(sizeof($res1)-1)]['priority']; 
			$result[$i]['category'] = $res1[(sizeof($res1)-1)]['category']; 
			$result[$i]['summery'] = $res1[(sizeof($res1)-1)]['summery']; 
			$result[$i]['unit_id'] = $res1[(sizeof($res1)-1)]['unit_id']; 
			
				
		}
			
		return $result;
	}
	
	
	public function getRecordsRight($id)
	{
	if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER))
		{
			$sql="select * from `service_request` inner join (select request_no, min(timestamp) as ts from `service_request` group by request_no) maxt on (`service_request`.request_no = maxt.request_no and `service_request`.timestamp = maxt.ts)   WHERE service_request.`society_id` = ".$_SESSION['society_id']." and service_request.`visibility`='1'  ORDER BY service_request.request_no  DESC  LIMIT 5 ";
			
		}
		else
		{
			$sql="select * from `service_request` inner join (select request_no, min(timestamp) as ts from `service_request` group by request_no) maxt on (`service_request`.request_no = maxt.request_no and `service_request`.timestamp = maxt.ts)   WHERE service_request.`unit_id` = ".$_SESSION['unit_id']." and service_request.`visibility`='1'  ORDER BY service_request.request_no  DESC  LIMIT 5 ";
			
		}
		$result = $this->m_dbConn->select($sql);
		//print_r($result);
		for($i=0;$i<count($result);$i++)
		{
			$sql="select status from service_request where request_no='".$result[$i]['request_no']."' order by timestamp DESC";
			$res1 = $this->m_dbConn->select($sql);
			$result[$i]['status']=$res1[0]['status'];
		}
		return $result;
	}
	
	
	public function getViewDetails($request_no,$isview=false)
	{ 
		$fieldname='request_id';
		if($isview==true)
		{
				$fieldname='request_no';
		}
		if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER || $_SESSION['unit_id']==0))
		{
			//SELECT service_request.*,`unit`.unit_no FROM `service_request` join `unit` on `service_request`.unit_id=`unit`.unit_id WHERE service_request.`society_id` = '156' AND `request_no` = '19' and `visibility`='1'
			$sql = "SELECT service_request.* FROM `service_request` WHERE service_request.`society_id` = '".$_SESSION['society_id']."' AND `".$fieldname."` = '".$request_no."'  and  `visibility`='1'";	
		}
		else
		{
			$sql = "SELECT service_request.* FROM `service_request` WHERE service_request.`society_id` = '".$_SESSION['society_id']."' AND `".$fieldname."` = '".$request_no."'  and  `visibility`='1'";	
		}
		
		$result = $this->m_dbConn->select($sql);
		return $result;
	}
	
	public function getUpdatedStatus($requestNo)
	{
		$sql = "SELECT `status` FROM `service_request` WHERE `visibility`='1' and `request_no` = '".$requestNo."'  ";
		$result = $this->m_dbConn->select($sql);
		return $result[sizeof($result) - 1]['status'];	
	}
		
	public function comboboxEx($query)
	{ //echo "test";
		$id=0;
		//echo "<script>alert('test')<//script>";
		$str.="<option value=''>Please Select</option>";
	$data = $this->m_dbConn->select($query);
	//echo $data;
		if(!is_null($data))
		{
			$vowels = array('/', '*', '%', '&', ',', '(', ')', '"');
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
						echo $v;
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
					}
					else
					{
						//$str.=$v."</OPTION>";
						$str.= str_replace($vowels, ' ', $v)."</OPTION>";
					}
					//echo "<script>alert('".$str."')<//script>";
					$i++;
				}
			}
		}
		//return $str;
		//print_r( $str);
		//echo "<script>alert('test')<//script>";
		return $str;
	}
	
	
	
	
	public function combobox1($query, $id, $defaultText = 'Please Select', $defaultValue = '')
	{
		$str = '';
		
		if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "'>" . $defaultText . "</option>";
		}
		
		$data = $this->m_dbConn->select($query);
		//echo $data;
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
		
	public function combobox($query, $id, $defaultText = 'Please Select', $defaultValue = '')
	{
		$str = '';
		
		if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "'>" . $defaultText . "</option>";
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
	
	public function sendEmail($requestNo, $name, $status, $desc, $email,$catEmail = '',$catEmailCC = '')
	{	
		$details = $this->getViewDetails($requestNo,true);
		$CategoryDetails = $this->GetCategoryDetails( $details[0]['category']);
		
		date_default_timezone_set('Asia/Kolkata');
		
		$mailSubject = "[SR#".$requestNo."] - ".substr(strip_tags($details[0]['summery']),0,50)." - ".$status;
		$Raisename=$details[0]['reportedby'];
		$raisedtimestamp = strtotime($details[0]['timestamp']);
		$updatedtimestamp = strtotime($details[sizeof($details)-1]['timestamp']);
		//$url="<a href='http://localhost/society-shared-template/viewrequest.php?rq=".$requestNo. "'>Go to Service Request</a>";
		$url="<a href='http://way2society.com/viewrequest.php?rq=".$requestNo. "'>http://way2society.com/viewrequest.php?rq=".$requestNo. "</a>";
		
		
		if($status == 'Raised')
		{
			
			$mailBody = '<table border="black" style="border-collapse:collapse;" cellpadding="10px">
							<tr> <td colspan="3"> <b> New Service Request [SR#'.$requestNo.'] Raised: </b> </td></tr>   							
							<tr> <td style="width:30%;border-right:none;"><b>Raised By</b></td><td style="width:10%;border-left:none;"> : </td><td style="width:60%;">'.$Raisename.'<br/>'.date("d-m-Y (g:i:s a)", $raisedtimestamp).'</td></tr>
							<tr><td style="border-right:none;"><b>Category</b></td><td style="border-left:none;"> : </td><td>'.$CategoryDetails[0]['category'].'</td></tr>
							<tr><td style="border-right:none;"><b>Priority</b></td><td style="border-left:none;"> : </td><td>'.$details[0]['priority'].'</td></tr>
    						<tr><td style="border-right:none;"><b>Status</b></td><td style="border-left:none;"> : </td><td>'.$status.'</td></tr>
    						
							<tr><td style="border-right:none;"><b>Subject</b></td><td style="border-left:none;"> : </td><td>'.nl2br(htmlentities($details[0]['summery'], ENT_QUOTES, 'UTF-8')).'</td></tr>
							<tr><td style="border-right:none;"><b>Description</b></td><td style="border-left:none;"> : </td><td>'.$desc.'</td></tr>
							     
						</table><br />'	;		
		}
		else
		{
												
			$mailBody = '<table border="black" style="border-collapse:collapse;" cellpadding="10px">
							<tr> <td colspan="3"> <b>Service Request [SR#'.$requestNo.'] Updated: </b> </td></tr>   							
							<tr> <td style="width:30%;border-right:none;"><b>Updated By</b></td><td style="width:10%;border-left:none;"> : </td><td style="width:60%;">'.$name.' <br/> '.date("d-m-Y (g:i:s a)", $updatedtimestamp).'</td></tr>
							<tr> <td style="width:30%;border-right:none;"><b>Raised  By</b></td><td style="width:10%;border-left:none;"> : </td><td style="width:60%;">'.$Raisename.' <br/> '.date("d-m-Y (g:i:s a)", $raisedtimestamp).'</td></tr>
							<tr><td style="border-right:none;"><b>Category</b></td><td style="border-left:none;"> : </td><td>'.$CategoryDetails[0]['category'].'</td></tr>
							<tr><td style="border-right:none;"><b>Priority</b></td><td style="border-left:none;"> : </td><td>'.$details[0]['priority'].'</td></tr> 
    						<tr><td style="border-right:none;"><b>Status</b></td><td style="border-left:none;"> : </td><td>'.$status.'</td></tr>
    						
							<tr><td style="border-right:none;"><b>Subject</b></td><td style="border-left:none;"> : </td><td>'.nl2br(htmlentities($details[0]['summery'], ENT_QUOTES, 'UTF-8')).'</td></tr>
							<tr><td style="border-right:none;"><b>Comments</b></td><td style="border-left:none;"> : </td><td>'.$desc.'</td></tr>
							
							        
						</table><br />'	;			
		}
		
		$mailBody .="You may view or update this service request by copying below link to browser or by clicking here<br />".$url;
		// Create the mail transport configuration				
	  $societyEmail = "";	  
	  if($this->objFetchData->objSocietyDetails->sSocietyEmail <> "")
	  {
		 $societyEmail = $this->objFetchData->objSocietyDetails->sSocietyEmail;
	  }
	  else
	  {
		 $societyEmail = "techsupport@way2society.com";
	  }
	  	 
	  try
	  {	
			  $EMailIDToUse = $this->m_objUtility->GetEmailIDToUse(false, 0, 0, 0, 0, 0, $_SESSION['society_id']);
				//print_r($EMailIDToUse);
			
			if($EMailIDToUse['status'] == 0)
			{	
				$EMailID = $EMailIDToUse['email'];
				$Password = $EMailIDToUse['password'];			
				//$EMailID = "sujitkumar0304@gmail.com";
				//$Password = "9869752739";  
				$host = "cs10.webhostbox.net";
				//$host = "smtp.gmail.com";
				$transport = Swift_SmtpTransport::newInstance($host, 465, "ssl")
						->setUsername($EMailID)
						->setSourceIp('0.0.0.0')
						->setPassword($Password) ; 
																			
				// Create the message
				$message = Swift_Message::newInstance();
				$arEmails = explode(";", $catEmail);
				$arEmailsCC = explode(";", $catEmailCC);
				//send to member who created request		
				$message->setTo(array(
		   		$societyEmail => $this->objFetchData->objSocietyDetails->sSocietyName
		)); 
				
				$message->setCc(array(
					
					$email => $name
					
				));					
				if($catEmail <> '')
				{
					
					$message->setBcc($arEmails,$arEmailsCC);
					//$message->setBcc($arEmailsCC);
							
				}
																						 
				$message->setReplyTo(array(
				   $societyEmail => $this->objFetchData->objSocietyDetails->sSocietyName				   
				));
				//print_r( $societyEmail );
				$message->setSubject($mailSubject);
				$message->setBody($mailBody);
				if($status == 'Raised')
				{
					if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN ||  $_SESSION['role']==ROLE_SUPER_ADMIN))
					{
						$message->setFrom('no-reply@way2society.com', $name);
					}
					else
					{
						$from=$_SESSION['name']."[".$_SESSION['desc']."] ";
						$message->setFrom('no-reply@way2society.com', $name);
						$message->setCc($societyEmail);
					}
				}
					else
					{
							$message->setFrom("no-reply@way2society.com", $this->objFetchData->objSocietyDetails->sSocietyName);
							$message->setTo($arEmails[0],$arEmailsCC[0]);
							$message->setCc(array($societyEmail));				
					}
					
				$message->setContentType("text/html");										 
						
				// Send the email				
				$mailer = Swift_Mailer::newInstance($transport);
				$result = $mailer->send($message);											
								
				if($result > 0)
				{
					echo 'Success';
				}
				else
				{
					echo 'Failed';
				}
			}
			
	  }
		catch(Exception $exp)
		{
			echo "Error occured in email sending.".$exp;
		}
	}
	
	function getEmailFromCategory()
	{
		$sql = "SELECT `email` FROM `servicerequest_category` WHERE `id` = '".$_REQUEST['categoryId']."'";				
		$result = $this->m_dbConn->select($sql);
		return $result[0]['email'];
	}
	
		public function up_photo($name,$tmp_path,$location)
	{
		 $photo_name = $name;
		 $photo_name1 = str_replace(' ','-',$name);
		 $old_path = $tmp_path;
		 $new_path = $location.'/'.time().'_'.$photo_name1;
		 $image = move_uploaded_file($old_path,$new_path);
		
		return $new_path;
	}
	public function thumb_photo($thumbWidth,$thumbHeight,$pathToThumbs,$newpath,$exe,$image_name)
	{
		$kk = 0;
					
	  if($exe=='.jpg' || $exe=='.jpeg')
	  {
		$img = imagecreatefromjpeg($newpath);				  //die();
		if(!$img)
		{
			$kk = 1;
		?>
		<!--	<script> window.location.href = '../service_prd_reg.php?nul=nul'; </script> -->
		<?php	
		}
	  }
	  else if($exe=='.gif')
	  {
		$img = imagecreatefromgif($newpath);				  //die();				  
		if(!$img)
		{
			$kk = 1;
		?>
			<!--<script> window.location.href = '../service_prd_reg.php?nul=nul'; </script> -->
		<?php	
		}
	  }
	  else if($exe=='.png')
	  {
		$img = imagecreatefrompng($newpath);				  //die();
		if(!$img)
		{
			$kk = 1;
		?>
			<!--<script> window.location.href = '../service_prd_reg.php?nul=nul'; </script>-->
		<?php	
		}
	  }
	  else if($exe=='.bmp')
	  {
		$img = imagecreatefromwbmp($newpath);				  //die();
		if(!$img)
		{
			$kk = 1;
		?>
			<!--<script> window.location.href = '../service_prd_reg.php?nul=nul'; </script>-->
		<?php	
		}
	  }
	  else {} 
		  
	  if($kk<>1)
	  {
		  $width  = imagesx($img);
		  $height = imagesy($img);

		  $new_width  = $thumbWidth;
		  $new_height = $thumbHeight;
	
		  $tmp_img = imagecreatetruecolor($new_width,$new_height);
		  imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
		  imagejpeg($tmp_img,"{$pathToThumbs}{$image_name}");
		  
		  $thum_path = $pathToThumbs.$image_name;
		  
		  return $thum_path;
	  }
	}
	
	public function selecting($id)
	{
		$sql= "SELECT `request_id`,`reportedby`,`dateofrequest`,`email`,`phone`,`priority`,`category`,`summery`,`details`,`unit_id` FROM `service_request` WHERE  `request_id` = '".$id."'";
		//$sql = "SELECT * FROM `notices` WHERE `id` = '".$_REQUEST['noticeId']."'";		
		$res = $this->m_dbConn->select($sql);
		//print_r($res);
			if($res <> '')
		{
			$res[0]['dateofrequest'] = getDisplayFormatDate($res[0]['dateofrequest']);
		}
		return $res;
}

public function deleting($id)
	{
	 $sql = "update  `service_request` set `visibility`='0' where request_no='".$id."'";
		$res = $this->m_dbConn->update($sql);
		return $res;
	}
}
?>