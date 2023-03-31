<?php
include_once ("dbconst.class.php");
include_once("include/dbop.class.php");
include_once("utility.class.php");
include_once('/var/www/html/swift/swift_required.php');
include_once( "include/fetch_data.php");
include_once("android.class.php");
class create_poll 
{
	public $actionPage = "../poll.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_utility;
	public $objFetchData;
	public $obj_android;
	function __construct($dbConnRoot,$dbConn)
	{
		//$this->display_pg=new display_table();
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->obj_utility=new utility($dbConn, $dbConnRoot);
		$this->objFetchData = new FetchData($dbConn,$dbConnRoot);
		//$this->objFetchData = new FetchData($dbConn);
		
		/*if(isset($SocietyID) && $SocietyID <> "")
		{
			$this->objFetchData->GetSocietyDetails($SocietyID);
		}
		else
		{
			$this->objFetchData->GetSocietyDetails($_SESSION['society_id']);
		}
	*/
	
	}

	public function startProcess()
	{
		$errorExists = 0;
//echo"hi";
		if($_REQUEST['insert']=='Submit' && $errorExists==0)
		{// echo"hi1";
		//$options = $_POST['options'];
	
	//print_r($_POST);
			if($_POST['question']<>'')
			{//echo"2";
			 $insert_query="insert into `poll_question` (`group_id`,`question`,`create_date`,`start_date`,`end_date`,`created_by`,`poll_status`,`allow_vote`,`additional_content`) values ('".$_POST['group']."','".$_POST['question']."','".date('Y-m-d')."','".getDBFormatDate($_POST['start_date'])."','".getDBFormatDate($_POST['end_date'])."','".$_SESSION['login_id']."','".$_POST['poll_status']."','".$_POST['revote']."','".$_POST['additional_content']."')";
			$result_poll_id = $this->m_dbConnRoot->insert($insert_query);
			
			//$pollToArray = array();
			 $poll_options=$_POST['poll_options'];
			$poll = explode(',', $poll_options);
			//print_r( $poll);
			
			//$question=$_POST['poll_id'];
				//$poll=$_POST['disp'];
				//print_r($pollToArray);
				if ($poll)
				{
					foreach ($poll as $key=>$value)
					{ 
						if($value<>'')
						{
						//array_push($pollToArray,$value);
						 $sqldata="insert into `poll_option`(`poll_id`,`options`) values('".$result_poll_id."','".$value."')";						
							$data=$this->m_dbConnRoot->insert($sqldata);
						}
					}
					if($_POST['mobile_notify'])
					{
					$this->sendPollMobileNotification($result_poll_id);	
					}
				}
			}
			return "Insert";
			
		}
		
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			//print_r ($_REQUEST);
			$id=$_REQUEST['poll_id'];
			//echo $id;
			 $up_query="update `poll_question` set `group_id`='".$_POST['group']."',`question`='".$_POST['question']."',`start_date`='".getDBFormatDate($_POST['start_date'])."',`end_date`='".getDBFormatDate($_POST['end_date'])."',`poll_status`='".$_POST['poll_status']."',`allow_vote`='".$_POST['revote']."',`additional_content`='".$_POST['additional_content']."' where `poll_id`='".$id."'";
			$data = $this->m_dbConnRoot->update($up_query);
			
				$poll_options=$_POST['poll_options'];
				$poll = explode(',', $poll_options);
				//print_r($poll);

				if ($poll)
				{
					$deleteQuery="delete from `poll_option` where `poll_id`='".$id."'";
					$data=$this->m_dbConnRoot->delete($deleteQuery);
							
					foreach ($poll as $key=>$value)
					{ 
						if($value<>'')
						{
							 	
							 $inserData="insert into `poll_option`(`poll_id`,`options`) values('".$id."','".$value."')";						
							 $data=$this->m_dbConnRoot->insert($inserData);
						}
					}
				}
			
			return "Update";
		}
		else
		{
			return $errString;
		}
	}
		public function combobox($query, $id)
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
		
	/*public function display1($rsas)
	{
		$thheader = array('qusetion','option','exp_date','status');
		$this->display_pg->edit		= "getcreate_poll";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "create_poll.php";

		$res = $this->display_pg->display_new($rsas);
		return $res;
	}
	public function pgnation()
	{
		$sql1 = "select id,`qusetion`,`option`,`exp_date`,`status` from  where status='Y'";
		$cntr = "select count(status) as cnt from  where status='Y'";

		$this->display_pg->sql1		= $sql1;
		$this->display_pg->cntr1	= $cntr;
		$this->display_pg->mainpg	= "create_poll.php";

		$limit	= "50";
		$page	= $_REQUEST['page'];
		$extra	= "";

		$res	= $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
	}*/
	public function selecting($p_id)
	{
		 $sql = "select `poll_id`,`group_id`,`question`,`start_date`,`end_date`,`created_by`,`poll_status`,`allow_vote`,`additional_content` from `poll_question` where `poll_id`='".$p_id."'";
		$res = $this->m_dbConnRoot->select($sql);
		if($res <> '')
		{
			
			$res[0]['start_date'] = getDisplayFormatDate($res[0]['start_date']);
			$res[0]['end_date'] = getDisplayFormatDate($res[0]['end_date']);
			$sqldata="select `poll_id`,`options` from `poll_option` where `poll_id`='".$p_id."'";						
			$res1=$this->m_dbConnRoot->select($sqldata);
			$p_id=array();
			for($i=0;$i<sizeof($res1);$i++)
			{
				array_push($p_id,$res1[$i]['options']);
			}
				$options = implode(',', $p_id);
				$res[0]['options']=$options;
			//print_r($options);
		}
			
			
		return $res;
	}
	public function deleting($p_id)
	{
		 $sql = "update `poll_question` set `status`='N' where `poll_id`='".$p_id."'";
		$res = $this->m_dbConnRoot->update($sql);
		return $res;
	}
	
	public function getRecords($id)
	{
		if($_SESSION['role'])
		{
			if( $_SESSION['role'] == ROLE_SUPER_ADMIN)
			{
		 		 $sql = "select `poll_id`, `question`, `start_date`, `end_date`, l.`name` from `poll_question` as q JOIN soc_group as s ON q.group_id = s.group_id Join society as c on s.society_id = c.society_id JOIN login as l on l.login_id = q.created_by  where c.`status`='Y' and l.client_id = '" . $_SESSION['client_id'] . "' and c.society_id='".$_SESSION['society_id']."'   ORDER BY poll_id desc";
			}
			else if($_SESSION['role'] == ROLE_ADMIN || $_SESSION['role'] == ROLE_ADMIN_MEMBER)
			{
				 	//$sql="select `poll_id`,`question`,`start_date`,`end_date`,`name` from `poll_question` as c JOIN login on c.created_by = login.login_id  where c.`status`='Y' and c.created_by = '" .  $_SESSION['login_id'] . "'  ORDER BY poll_id desc";
						 $sql = "select `poll_id`, `question`, `start_date`, `end_date`, l.`name` from `poll_question` as q JOIN soc_group as s ON q.group_id = s.group_id Join society as c on s.society_id = c.society_id JOIN login as l on l.login_id = q.created_by JOIN mapping as m ON m.society_id = s.society_id  where c.`status`='Y' and l.login_id  = '" .  $_SESSION['login_id'] . "' and s.society_id='".$_SESSION['society_id']."'  ORDER BY poll_id desc";
			}
		}
		//else
		//{
			//$sql="select * from `service_request` inner join (select request_no, min(timestamp) as ts from `service_request` group by request_no) maxt on (`service_request`.request_no = maxt.request_no and `service_request`.timestamp = maxt.ts)   WHERE service_request.`society_id` = ".$_SESSION['society_id']."AND service_request.`unit_id` = ".$_SESSION['unit_id']." and service_request.`visibility`='1' ORDER BY service_request.request_no DESC";
			//$sql = "SELECT * FROM `service_request` WHERE `society_id` = ".$_SESSION['society_id']." AND `unit_id` = ".$_SESSION['unit_id']." and  `visibility`='1' ORDER BY `request_no` DESC";
		//}
		//echo $sql;
		 $result = $this->m_dbConnRoot->select($sql);
		 		  //$date = $this->obj_utility->getDateDiff(sart_date.;
		//print_r($result);
		//for($i=0;$i<count($result);$i++)
		//{
		//	$sql="select status from service_request where request_no='".$result[$i]['request_no']."' order by timestamp DESC";
			//$res1 = $this->m_dbConn->select($sql);
			//$result[$i]['status']=$res1[0]['status'];
		//}
		return $result;
	}
	public function getViewDetails($id)
	{ 
			//$sql="SELECT a.poll_id, a.group_id,a.question,a.start_date,a.end_date,a.created_by,b.options,b.counter FROM poll_question as a join poll_option as b on a.poll_id=b.poll_id WHERE a.poll_id = '".$id."'";
			$sql="SELECT a.poll_id, a.group_id,a.question,a.start_date,a.end_date,a.created_by,b.options,b.counter,g.group_name FROM poll_question as a join poll_option as b on a.poll_id=b.poll_id JOIN `group` as g ON g.group_id=a.group_id WHERE a.poll_id ='".$id."'";
			//$sql="SELECT a.poll_id, a.group_id,a.question,a.start_date,a.end_date,a.created_by,b.options,b.counter,g.group_name FROM poll_question as a join poll_option as b on a.poll_id=b.poll_id join group as g on a.group_id=g.group_id WHERE a.poll_id = '".$id."'";
//echo 	$sql="SELECT a.poll_id, a.group_id,a.question,a.start_date,a.end_date,a.created_by,b.options,b.counter FROM poll_question a, poll_option b WHERE a.poll_id = '".$id."'";
		$res = $this->m_dbConnRoot->select($sql);
		 $date = $this->obj_utility->getDateDiff($res[0]["end_date"], $res[0]["start_date"]);
         $res[0]['date']=$date;
		return $res;
	}
	
///////////////////////////////////////////////////////////////////////////////////////Right Pannel ////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function getPollList($id)
	{
		if($id <> 0)
		{
		 $sql="SELECT a.poll_id, a.group_id,a.question,a.start_date,a.end_date,a.created_by,a.poll_status,a.allow_vote,a.additional_content ,b.options,b.counter,b.option_id FROM poll_question as a join poll_option as b on a.poll_id=b.poll_id WHERE a.poll_id = '".$id."' " ;
		// $sql="SELECT a.poll_id, a.group_id,a.question,a.start_date,a.end_date,a.created_by,b.options ,b.option_id,b.counter FROM poll_question a, poll_option b WHERE a.poll_id = '".$id."'";
		 //  $date = $obj_utility->getDateDiff(getDBFormatDate($requests[$i]["start_date"]), date("Y-m-d"));
		   
		}
		else
		{
			if($_SESSION['role'] == ROLE_MEMBER || $_SESSION['role']==ROLE_ADMIN_MEMBER)
			{
			  $sql="SELECT a.poll_id, a.group_id,a.question,a.start_date,a.end_date,a.created_by,a.poll_status,a.allow_vote,a.additional_content, sum(b.counter) as counter FROM poll_question as a join poll_option as b on a.poll_id=b.poll_id JOIN soc_group as s ON s.group_id=a.group_id WHERE a.poll_id = b.poll_id and s.society_id='".$_SESSION['society_id']."'and a.start_date <= DATE(NOW()) and a.end_date >= DATE(NOW()) group by poll_id  DESC  LIMIT 5 ";
			}
			else if( $_SESSION['role'] == ROLE_SUPER_ADMIN)
			{
		 		$sql = "select `poll_id`, `question`, `start_date`,`additional_content` ,`end_date`, l.`name` from `poll_question` as q JOIN soc_group as s ON q.group_id = s.group_id Join society as c on s.society_id = c.society_id JOIN login as l on l.login_id = q.created_by  where c.`status`='Y' and l.client_id = '" .  $_SESSION['client_id'] . "' and c.society_id='".$_SESSION['society_id']."' and q.start_date <= DATE(NOW()) and q.end_date >= DATE(NOW()) group by poll_id  DESC  LIMIT 5";
			}
			else if($_SESSION['role'] == ROLE_ADMIN)
			{
				 	//$sql="select `poll_id`,`question`,`start_date`,`end_date`,`name` from `poll_question` as c JOIN login on c.created_by = login.login_id  where c.`status`='Y' and c.created_by = '" .  $_SESSION['login_id'] . "'  ORDER BY poll_id desc";
						$sql = "select `poll_id`, `question`, `start_date`, `end_date`,`additional_content`, l.`name` from `poll_question` as q JOIN soc_group as s ON q.group_id = s.group_id Join society as c on s.society_id = c.society_id JOIN login as l on l.login_id = q.created_by JOIN mapping as m ON m.society_id = s.society_id  where c.`status`='Y' and l.login_id  = '" .  $_SESSION['login_id'] . "' and c.society_id='".$_SESSION['society_id']."' and q.start_date <= DATE(NOW()) and q.end_date >= DATE(NOW()) group by poll_id  DESC  LIMIT 5";
			}
			// $sql="SELECT a.poll_id, a.group_id,a.question,a.start_date,a.end_date,a.created_by,b.options ,b.option_id ,b.counter FROM poll_question a, poll_option b WHERE a.poll_id = b.poll_id";
			// $sql="SELECT society.society_name,group.group_name,poll_question.`question`,poll_question.`poll_id`,poll_question.`start_date`,poll_question.`end_date` FROM `soc_group` JOIN `society` ON (society.society_id = soc_group.society_id) JOIN `group` ON (group.group_id = soc_group.group_id) join `poll_question` on(soc_group.group_id=poll_question.group_id) where society.society_id='".$_SESSION['society_id']."'and start_date <= DATE(NOW()) ORDER BY poll_id desc";
		}
		$res = $this->m_dbConnRoot->select($sql);

		// $date = $this->obj_utility->getDateDiff($res[0]["end_date"], $res[0]["start_date"]);
         //$res[0]['date']=$date;
		return $res;
	}
	
	
	public function getVoteList($pollID)
	{
	  $sql="SELECT a.poll_id ,a.options ,a.option_id ,b.option_id ,b.unit_id,b.isValid FROM poll_vote as b JOIN poll_option as a ON b.option_id = a.option_id WHERE a.poll_id = '".$pollID."' and b.unit_id='".$_SESSION['unit_id']."' and b.society_id='".$_SESSION['society_id']."'  and  b.isValid='1'";
		 $res = $this->m_dbConnRoot->select($sql);
		 return $res;
	}
	
///////////////////////////////////////////////////////////////////////////////////////Show Member List////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function getRecordsForMember($id)
	{
		 $sql="SELECT society.society_name,group.group_name,poll_question.`question`,poll_question.`poll_id`,poll_question.`start_date`,poll_question.`end_date` FROM `soc_group` JOIN `society` ON (society.society_id = soc_group.society_id) JOIN `group` ON (group.group_id = soc_group.group_id) join `poll_question` on(soc_group.group_id=poll_question.group_id) where society.society_id='".$_SESSION['society_id']."'and start_date <= DATE(NOW()) ORDER BY poll_id desc";
			
			// $sql="select `poll_id`,`question`,`start_date`,`end_date`,`name` from `poll_question` as c JOIN login on c.created_by = login.login_id where c.`status`='Y' and c.start_date <= DATE(NOW()) ORDER BY poll_id desc";
	
		 $result = $this->m_dbConnRoot->select($sql);
		for($i=0; $i < sizeof($result);$i++)
		{
			 $pollVoteShow=$this->getVoteList($result[$i]['poll_id']);
			// print_r($pollVoteShow);
			$result[$i]['options'] = "";
			if(sizeof( $pollVoteShow) > 0 )
			{
				 $result[$i]['options']= $pollVoteShow[0]['options'];
			}
			// $result= $pollVoteShow;

		 }
		return $result;
	}
	
///////////////////////////////////////////////////////////////////////////////////////Send Email function////////////////////////////////////////////////////////////////////////////////////////////////////////	

	public function sendPollEmail( $question, $startDate, $endDate, $pollId,$catEmail = '')
	{	//echo "send email";
		$sql="SELECT a.poll_id ,a.question,a.group_id,a.start_date,a.end_date, a.status,a.additional_content,b.group_id,b.society_id,c.society_id,c.dbname,c.client_id,c.society_name FROM soc_group as b JOIN poll_question as a ON b.group_id = a.group_id Join society as c on b.society_id=c.society_id WHERE a.poll_id = '".$pollId."'";
		$result = $this->m_dbConnRoot->select($sql);
		
		$result[0]['start_date'] = getDisplayFormatDate($result[0]['start_date']);
		$result[0]['end_date'] = getDisplayFormatDate($result[0]['end_date']);
		$hostname = 'localhost';
		$username = 'root';
		$password = 'aws123';
		
		$newUserUrl = "";
		//$date = $this->obj_utility->getDateDiff($result[0]["end_date"], date("Y-m-d"));
		//echo '<br/>Size : ' . sizeof($result);
		for($m= 0; $m< sizeof($result); $m++)
		{
			//echo 'Inside For Loop';
			$dbName=$result[$m]['dbname'];
			$SocietyID=$result[$m]['society_id'];
			$clienID=$result[$m]['client_id'];
			$SocietyName=$result[$m]['society_name'];
			$status=$result[$m]['status'];
						
			$mMysqli = mysqli_connect($hostname, $username, $password, $dbName);
			if(!$mMysqli)
			{
				echo '<br/>Connection Failed';
			}
			else
			{
				// $selectSociety = "SELECT m.`owner_name`,m.`email`,m.`society_id`,s.`society_name`, s.`email` as soc_email FROM `member_main` as m JOIN society as s ON m.society_id = s.society_id where s.`society_id`='".$SocietyID."'";
				//$resultEmail = mysqli_query($mMysqli, $selectSociety);
				$emailIDList = $this->objFetchData->GetEmailIDToSendNotification(0);
				for($i = 0; $i < sizeof($emailIDList); $i++)
				{	
					if(($emailIDList[$i]['to_email'] <> "") && (isValidEmailID($emailIDList[$i]['to_email']) == true))
					{
						$display[$emailIDList[$i]['to_email']] = $emailIDList[$i]['to_name'];
						//$EmailIDtoUnitIDs[$noticeToArray[$i]] = $emailIDList[$i]['to_email'];
				
				
					//$count = 0;
					//while($row = $resultEmail->fetch_array(MYSQL_ASSOC))
					//{ 
						$member_email = $emailIDList[$i]['to_email'];
						if($member_email != '')
						{
							//echo 'Inside Continue';
							//continue;
						
							//echo "email".$member_email.
							$member_name = $emailIDList[$i]['to_name'];
							//////echo "name".$member_name.
							$mailSubject = "Society Poll : ".substr(strip_tags($result[0]['question']),0,100);
									
							$loginExist = $this->m_dbConnRoot->select("SELECT * FROM `login` WHERE `member_id` = '".$member_email."'");
							 //$sqlClientDetails = "SELECT * FROM `client` WHERE `id` = '".$clienID."'";
							//$ClientDetails = $this->m_dbConnRoot->select($sqlClientDetails);
							$encryptedEmail = $this->obj_utility->encryptData($member_email);	
							if(sizeof($loginExist)==0)
							{
								//echo "login required";
								$pollId=$this->obj_utility->encryptData($pollId);
								$newUserUrl = "http://way2society.com/newuser.php?reg&u=".$mailToEmail."&n=".$mailToName."&tkn=".$encryptedEmail;
								$onclickURL = $newUserUrl.'&URL=http://way2society.com/Dashboard.php?View=MEMBER';
								$userURL = $newUserUrl.'&url=http://way2society.com/poll_preview.php?rq='.$pollId.'';
								
							}
							else
							{
								//echo "login exist";
								$pollId=$this->obj_utility->encryptData($pollId);
								$userURL ='http://way2society.com/poll_preview.php?rq='.$pollId.'';	
							
							}
							if($status =='Y')
							{
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
												<td bgcolor="#ffffff" style="padding-top:20px; padding-bottom:20px; padding-left:10px; padding-right:10px;border-top:none;border-bottom:none;" >
												<table width="100%">
													<tr>
														<td><br>Dear '.$member_name.'</td>
														<td width="30%" >Start Date :&nbsp;'.$result[0]['start_date']. '</td></tr>
														 </table>
														<table width="100%">
														<tr><td><br>'.$result[0]['additional_content'].' </td> 
													</tr>
												</table>
												 <table width="100%">
													<tr><td><br /><br><br></td></tr>
													<!--<tr>
														<td align="center"  style="color:#F00;"><b>Voting line closes on :&nbsp;'.$result[0]['end_date']. '</b></td></tr> -->          
													<tr>
														<td align="center">
															<div style="width:75%;">
																<table>
																		<tr>
																			 <td bgcolor="aliceblue" width="400" align="center" style="padding-bottom: 10px;padding-top: 10px;"> '.$result[0]['question']. '</td></tr>
																 </table>
															</div>
																<table width="160px" cellspacing="0" cellpadding="0" border="0">
																	<tr><td><br /></td></tr>
																		<tbody>
																			<tr>
																				<td valign="middle" bgcolor="#337AB7" height="40" align="center">
																				<a target="_blank" style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif" href="'.$userURL.'">Click here to vote</a>
																				</td>
																			</tr>
																			<tr><td><br /></td></tr>
																			</tbody></table>
																			<table width="250px"><tbody>
																			<tr>
																				<td align="center"  style="color:#F00;"><b>Voting line closes on :&nbsp;'.$result[0]['end_date']. '</b></td></tr> 
																		</tbody>
																</table>
														</td>
													</tr>
														<tr><td><br /></td></tr>
														<tr><td></td></tr>
														<tr><td><br /></td></tr>
														<tr><td><br /></td></tr>
														<tr><td>If you are a new user, we will take you through a  simple process to create your account. </td></tr>
														<tr><td><br /></td></tr>
														<tr>
															<!--<td font="colr:#999999;">Thank You,<br>Pavitra! <br />
																	G-6, Shagun, Dindoshi, Malad East, Mumbai - 400 097 <br />
																	Tel : 022 450 44 699 &nbsp;
																	Mob : 09833765243 <br />
																	Email : info@way2society.com <br /></td>-->
														</tr>
										</table>
									 </td>
								 </tr>
									 <tr>
										 <td bgcolor="#CCCCCC" style="padding: 2px 20px 2px 20px;border-top:none;">
										   <table cellpadding="0" cellspacing="0" width="100%">           
										 <td >             
											<a href="http://way2society.com/" target="_blank"><i>Way2Society</i></a>              
										 </td>
										 <td align="center"  style="padding: 0px 50px 0px 1px;">
										 <table>
                                 		<tr>
                                 		<td><a href="https://play.google.com/store/apps/details?id=com.ionicframework.way2society869487&amp;rdid=com.ionicframework.way2society869487" target="_blank"><img src="http://way2society.com/images/app.png" width="120" height="50" style="style=" top:10px;"></a></td></tr></table>
                                 		</td>
											 <td align="right">
											  <table border="0" cellpadding="0" cellspacing="0">
											   <tr>
												<td>
													<a href="https://twitter.com/way2society" target="_blank"><img src="http://way2society.com/images/icon2.jpg" alt=""></a>                  
												</td>
												<td style="font-size: 0; line-height: 0;" width="20">&nbsp;&nbsp;</td>
												<td>
													<a href="https://www.facebook.com/way2soc" target="_blank"><img src="http://way2society.com/images/icon1.jpg" alt=""></a>                 
												</td>
											   </tr>
											  </table>
											 </td>             
										   </table>
										 </td>
									   </tr>
									 </table>   
								</body>
								</html>'	;		
			
						//$mailBody .="You may view or update this service request by copying below link to browser or by clicking here<br />".$url;
			// Create the mail transport configuration	
						
							$societyEmail = "";	  
							$societyEmail="techsupport@way2society.com";
							$EMailIDToUse = $this->obj_utility->GetEmailIDToUse(true,"","","","",$dbName ,$SocietyID,"","");
							
							$EMailID = $EMailIDToUse['email'];
							$Password = $EMailIDToUse['password'];
							//echo $EMailID ;
							try
							{		
										$transport = Swift_SmtpTransport::newInstance('cs10.webhostbox.net', 465, "ssl")
										->setUsername($EMailID)
										->setSourceIp('0.0.0.0')
										->setPassword($Password) ; 
								//echo "trace";
								$message = Swift_Message::newInstance();
				
								$message->setTo(array(
								 $member_email => $member_name
								));	 
			
								$message->setSubject($mailSubject);
								$message->setBody($mailBody);
								//$message->setFrom($EMailID,$row['society_name']);
								$message->setFrom('no-reply@way2society.com',$SocietyName);
								
								$message->setContentType("text/html");										 
					
								// Send the email				
								$mailer = Swift_Mailer::newInstance($transport);
								$resultEmailSend = $mailer->send($message);											
								//echo "email sent";
								if($resultEmailSend == 1)
								{
									echo 'Success';
								}
								else
								{
									echo 'Failed';
								}	
							}
							catch(Exception $exp)
							{
								//print_r($exp);
								echo "Error occure in email sending.";
							}
						}
				}											
			}
		}
	}
}	
}
//-----------------------------------------mobile send notification-----------------------//
public function sendPollMobileNotification($pollId)
	{
		$sql="SELECT a.poll_id ,a.question,a.group_id,a.start_date,a.end_date, a.status,a.additional_content,b.group_id,b.society_id,c.society_id,c.dbname,c.client_id,c.society_name FROM soc_group as b JOIN poll_question as a ON b.group_id = a.group_id Join society as c on b.society_id=c.society_id WHERE a.poll_id = '".$pollId."'";
		$result = $this->m_dbConnRoot->select($sql);
	
		$result[0]['start_date'] = getDisplayFormatDate($result[0]['start_date']);
		$result[0]['end_date'] = getDisplayFormatDate($result[0]['end_date']);
		$PollTitle="New Society Poll";
		$pollMassage =$result[0]['question'];
		$hostname = 'localhost';
		$username = 'root';
		$password = '';
		
		for($iCount= 0; $iCount< sizeof($result); $iCount++)
		{
			$dbName=$result[$iCount]['dbname'];
			$SocietyID=$result[$iCount]['society_id'];
			$mMysqli = mysqli_connect($hostname, $username, $password, $dbName);
			if(!$mMysqli)
			{
				echo '<br/>Connection Failed';
			}
			else
			{
				$obj_dbConn= new dbop(false,$dbName);
				$obj_bbConnRoot=new dbop(true)	;
				$obj_Fetch=new FetchData($obj_dbConn,$obj_bbConnRoot);
				$emailIDList = $obj_Fetch->GetEmailIDToSendNotification(0);
				for($i = 0; $i < sizeof($emailIDList); $i++)
				{	
				  if(($emailIDList[$i]['to_email'] <> ""))
				  {
					$unitID = $emailIDList[$i]['unit'];
					$objAndroid = new android($emailIDList[$i]['to_email'], $SocietyID, $unitID);
					$sendMobile=$objAndroid->sendPollNotification($PollTitle,$pollMassage,$pollId);
						
				  }
				}

		   }
	   }
	}
	
	public function endPollMobileNotification( $question, $startDate, $endDate, $pollId,$catEmail = '')
	{
		echo $sql="SELECT a.poll_id ,a.question,a.group_id,a.start_date,a.end_date, a.status,a.additional_content,b.group_id,b.society_id,c.society_id,c.dbname,c.client_id,c.society_name FROM soc_group as b JOIN poll_question as a ON b.group_id = a.group_id Join society as c on b.society_id=c.society_id WHERE a.poll_id = '".$pollId."'";
		$result = $this->m_dbConnRoot->select($sql);
	
		$result[0]['start_date'] = getDisplayFormatDate($result[0]['start_date']);
		$result[0]['end_date'] = getDisplayFormatDate($result[0]['end_date']);
		$PollTitle="Society Poll End Today";
		$pollMassage =$result[0]['question'];
		$hostname = 'localhost';
		$username = 'root';
		$password = '';
		
		for($iCount= 0; $iCount< sizeof($result); $iCount++)
		{
			//echo $dbName=$result[$iCount]['dbname'];
			$SocietyID=$result[$iCount]['society_id'];
			$mMysqli = mysqli_connect($hostname, $username, $password, $dbName);
			if(!$mMysqli)
			{
				echo '<br/>Connection Failed';
			}
			else
			{
				$obj_dbConn= new dbop(false,$dbName);
				$obj_bbConnRoot=new dbop(true)	;
				$obj_Fetch=new FetchData($obj_dbConn,$obj_bbConnRoot);
				$emailIDList = $obj_Fetch->GetEmailIDToSendNotification(0);
				//print_r($emailIDList);

				for($i = 0; $i < sizeof($emailIDList); $i++)
				{	
					//echo "<br/>email:".$emailIDList[$i]['to_email'];
				  if(($emailIDList[$i]['to_email'] <> ""))
				  {
					$unitID = $emailIDList[$i]['unit'];
					$objAndroid = new android($emailIDList[$i]['to_email'], $SocietyID, $unitID);
					$sendMobile=$objAndroid->sendPollNotification($PollTitle,$pollMassage,$pollId);
						
				  }
				}

		   }
	   }
	}

}
?>