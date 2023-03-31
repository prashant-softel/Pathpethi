<?php

include_once('dbconst.class.php');
include_once('include/config.php');
include_once('utility.class.php');
include_once("include/dbop.class.php");

class initialize
{
	public $m_dbConn;
	public $m_objUtility;
	public $bIsNewUserConnectedViaStoredProcedure;
	public $m_objDBRoot;
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->m_objDBRoot = new dbop(true);
		$this->m_objUtility = new utility($this->m_dbConn, $this->m_objDBRoot);
		$this->bIsNewUserConnectedViaStoredProcedure = false;
		
	}

	public function combobox($query, $id, $defaultText = 'Please Select', $defaultValue = '0')
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
	
	public function getMapCount($login_id)
	{
		$sql = "select Count(login_id) as Cnt from mapping where login_id='" . $login_id . "'";
		$result = $this->m_dbConn->select($sql);
		
		return $result[0]['Cnt'];
	}
	
	public function getDBName($mapid)
	{
		$sql = "Select maptbl.society_id, dbtbl.dbname from dbname as dbtbl JOIN mapping as maptbl ON maptbl.society_id = dbtbl.society_id where maptbl.id = '" . $mapid . "'";
		$result = $this->m_dbConn->select($sql);
		return $result[0]['dbname'];
	}
	
	public function getSocietyID($dbName)
	{
		$sql = "Select society_id from dbname where dbname = '" . $dbName . "'";
		$result = $this->m_dbConn->select($sql);
		return $result[0]['society_id'];
	}
	
	public function getMapDetails($mapid)
	{
		$sql = "Select maptbl.society_id, maptbl.unit_id, maptbl.role, maptbl.view, maptbl.profile, maptbl.code, maptbl.desc, maptbl.login_id, maptbl.status, dbtbl.dbname,societytbl.client_id from dbname as dbtbl JOIN mapping as maptbl ON maptbl.society_id = dbtbl.society_id  JOIN `society` as societytbl ON dbtbl.society_id  = societytbl.society_id where maptbl.id = '" . $mapid . "'";
		//echo $sql;
		$result = $this->m_dbConn->select($sql);
		
		return $result;
	}
	
	
	public function setProfile($profileID)
	{
		//$profileSuperAdmin = array(PROFILE_GENERATE_BILL => 1, PROFILE_CHEQUE_ENTRY => 1, PROFILE_PAYMENTS => 1, PROFILE_MANAGE_MASTER => 1, PROFILE_UPDATE_INTEREST => 1, PROFILE_REVERSE_CHARGE => 1, PROFILE_BANK_RECO => 1);
		
		//$profileAdmin = array(PROFILE_GENERATE_BILL => 1, PROFILE_CHEQUE_ENTRY => 1, PROFILE_PAYMENTS => 1, PROFILE_MANAGE_MASTER => 1, PROFILE_UPDATE_INTEREST => 1, PROFILE_REVERSE_CHARGE => 1, PROFILE_BANK_RECO => 1);
		
		//$profileAdminMember = array(PROFILE_GENERATE_BILL => 0, PROFILE_CHEQUE_ENTRY => 1, PROFILE_PAYMENTS => 1, PROFILE_MANAGE_MASTER => 0, PROFILE_UPDATE_INTEREST => 0, PROFILE_REVERSE_CHARGE => 0);
		
		/*if($_SESSION['role'] == ROLE_SUPER_ADMIN)
		{
			$_SESSION['profile'] = $profileSuperAdmin;
		}
		else if($_SESSION['role'] == ROLE_ADMIN)
		{
			$_SESSION['profile'] = $profileAdmin;
		}
		else if($_SESSION['role'] == ROLE_ADMIN_MEMBER)
		{
			$_SESSION['profile'] = $profileAdminMember;
		}*/
		
		$sql = "Select * from profile where id = '" . $profileID . "'";
		$result = $this->m_dbConn->select($sql);
		
		if($result <> '')
		{
			$_SESSION['profile'][PROFILE_GENERATE_BILL] = $result[0]['PROFILE_GENERATE_BILL'];
			$_SESSION['profile'][PROFILE_EDIT_BILL] = $result[0]['PROFILE_EDIT_BILL'];
			$_SESSION['profile'][PROFILE_CHEQUE_ENTRY] = $result[0]['PROFILE_CHEQUE_ENTRY'];
			$_SESSION['profile'][PROFILE_PAYMENTS] = $result[0]['PROFILE_PAYMENTS'];
			$_SESSION['profile'][PROFILE_UPDATE_INTEREST] = $result[0]['PROFILE_UPDATE_INTEREST'];
			$_SESSION['profile'][PROFILE_MANAGE_MASTER] = $result[0]['PROFILE_MANAGE_MASTER'];
			$_SESSION['profile'][PROFILE_BANK_RECO] = $result[0]['PROFILE_BANK_RECO'];
			$_SESSION['profile'][PROFILE_REVERSE_CHARGE] = $result[0]['PROFILE_REVERSE_CHARGE'];
			$_SESSION['profile'][PROFILE_SEND_NOTIFICATION] = $result[0]['PROFILE_SEND_NOTIFICATION'];
			$_SESSION['profile'][PROFILE_NEFT_DETAILS] = $result[0]['PROFILE_CHEQUE_ENTRY'];
			$_SESSION['profile'][PROFILE_VOUCHER_EDIT] = $result[0]['PROFILE_PAYMENTS'];
			$_SESSION['profile'][PROFILE_CREATE_VOUCHER] = $result[0]['PROFILE_PAYMENTS'];
			$_SESSION['profile'][PROFILE_DEPOSITGROUP] = $result[0]['PROFILE_CHEQUE_ENTRY'];
			$_SESSION['profile'][PROFILE_CHEQUELEAFBOOK] = $result[0]['PROFILE_PAYMENTS'];
		}
	}
	
	public function getProfile($profileID)
	{
		$sql = "Select * from profile where id = '" . $profileID . "'";
		$result = $this->m_dbConn->select($sql);
		
		$profile = array();
		
		if($result <> '')
		{
			$profile[PROFILE_GENERATE_BILL] = $result[0]['PROFILE_GENERATE_BILL'];
			$profile[PROFILE_EDIT_BILL] = $result[0]['PROFILE_EDIT_BILL'];
			$profile[PROFILE_CHEQUE_ENTRY] = $result[0]['PROFILE_CHEQUE_ENTRY'];
			$profile[PROFILE_PAYMENTS] = $result[0]['PROFILE_PAYMENTS'];
			$profile[PROFILE_UPDATE_INTEREST] = $result[0]['PROFILE_UPDATE_INTEREST'];
			$profile[PROFILE_MANAGE_MASTER] = $result[0]['PROFILE_MANAGE_MASTER'];
			$profile[PROFILE_BANK_RECO] = $result[0]['PROFILE_BANK_RECO'];
			$profile[PROFILE_REVERSE_CHARGE] = $result[0]['PROFILE_REVERSE_CHARGE'];
			$profile[PROFILE_SEND_NOTIFICATION] = $result[0]['PROFILE_SEND_NOTIFICATION'];
		}
		
		return $profile;
	}
	
	public function setCurrentMapping($mapID)
	{
		$sql = "UPDATE login SET current_mapping = '" . $mapID . "' WHERE login_id = '" . $_SESSION['login_id'] . "'";	
		$result = $this->m_dbConn->update($sql);
	}
	
	public function verifyCode($code)
	{
		$sql = "Select id, society_id, unit_id, `desc`, role, status from mapping where code = '"	. $code . "'";
		$result = $this->m_dbConn->select($sql);
		
		return $result;
	}
	
	public function setLoginIDToMap($mapID, $status = 0)
	{
		$sql = "Update mapping SET login_id = '" . $_SESSION['login_id'] . "' where id = '" . $mapID . "'";
		if($status <> 0)
		{
			$sql = "Update mapping SET login_id = '" . $_SESSION['login_id'] . "', status = '" . $status . "' where id = '" . $mapID . "'";
		}
		$result = $this->m_dbConn->update($sql);
		
		$this->setCurrentMapping($mapID);
	}
	
	public function addUser($name, $id, $pass, $fbid = '', $bSetSession = false, $sCode = '', $mob)
	{
		$result = -1;
		if($fbid == '')
		{
			$sqlCheck = "select count(member_id) as cnt from login where member_id = '" . $id . "'";
		}
		else
		{
			$sqlCheck = "select count(fbcode) as cnt from login where fbcode = '" . $fbid . "'";
		}
		
		$resultCheck = $this->m_dbConn->select($sqlCheck);
		
		if($resultCheck[0]['cnt'] <= 0)
		{
			$sql = "INSERT INTO `login`(`member_id`, `password`, `name`, `fbcode`,`mobile_number`) VALUES ('" . $id . "', '" . $pass . "', '" .$this->m_dbConn->escapeString($name). "', '" .  $fbid . "', '" .  $mob . "')";
			$result = $this->m_dbConn->insert($sql);
			
			if($bSetSession == true && $result > 0)
			{
				$_SESSION['login_id'] = $result;
				$_SESSION['current_mapping'] = 0;
				$_SESSION['society_id'] = 0;
				$_SESSION['authority'] = '';
				$_SESSION['name'] = $name;
			}
			
			if($result > 0 && $sCode <> '')
			{
				$sqlMap = "Select id from mapping where code = '" . $sCode . "' and status = '1'";
				$resultMap = $this->m_dbConn->select($sqlMap);
				
				if($resultMap <> '')
				{
					$sqlUpdateMap = "Update mapping SET login_id = '" . $result . "', status = 2 where id = '" . $resultMap[0]['id'] ."'";
					$updateMap = $this->m_dbConn->update($sqlUpdateMap);
					
					if($result > 0)
					{
						$sqlII = "SELECT `member_id`,`mobile_number`,`name`,`fbcode` FROM `login` where `login_id` = ".$result;
						$loginData = $this->m_dbConn->select($sqlII);
						
						if(count($loginData) > 0)
						{
							//send email to support team for assigning society to registered member
							$res = $this->sendEmail($loginData,false);
						}	
					}
					
				}
			}
			else if($result > 0 && $id <> "" &&   isValidEmailID($this->m_dbConn->escapeString($id)) == true)
			{
				$memberArray = $this->checkIfEmailAlreadyExists($id);
				if(sizeof($memberArray) > 0 )
				{
						$this->bIsNewUserConnectedViaStoredProcedure = true;
						for($i = 0;$i < sizeof($memberArray) ; $i++)
						{
							$sqlMap = "Select id from mapping where society_id = '" . $memberArray[$i]['society_id'] . "' and  unit_id = '" . $memberArray[$i]['unit'] . "' and status = '1'";
							$resultMap = $this->m_dbConn->select($sqlMap);
							
							if($resultMap <> '')
							{
								$sqlUpdateMap = "Update mapping SET login_id = '" . $result . "', status = 2 where id = '" . $resultMap[0]['id'] ."'";
								$updateMap = $this->m_dbConn->update($sqlUpdateMap);
							}
							else
							{
									$insert_mapping = "INSERT INTO `mapping`(`login_id`,`society_id`, `unit_id`, `desc`, `code`, `role`, `created_by`, `view`,`status`) VALUES ('" . $result . "','" . $memberArray[$i]['society_id'] . "', '" . $memberArray[$i]['unit'] . "', '" . $memberArray[$i]['unit_no'] . "', '" . getRandomUniqueCode() . "', '" . ROLE_MEMBER . "', '" . $result . "', 'MEMBER','2')";
									$result_mapping = $this->m_dbConn->insert($insert_mapping);
							}		
						}
						
						if($result > 0)
						{
							$sqlII = "SELECT `member_id`,`mobile_number`,`name`,`fbcode` FROM `login` where `login_id` = ".$result;
							$loginData = $this->m_dbConn->select($sqlII);
							
							if(count($loginData) > 0)
							{
								//send email to support team for assigning society to registered member
								$res = $this->sendEmail($loginData,false);
							}	
						}
						
							//if(isset($_REQUEST['url']))
						//{
						?>
                        <!--<script>window.location.href = "initialize.php?url=" + "<?php //echo $_REQUEST['url']?>";</script>-->
						
						<?php
						/*}
						else
						{*/
							?>
                        <!--<script>window.location.href = "initialize.php";</script>-->
						
						<?php
						//}
						
						if(isset($_REQUEST['url']))
						{
							echo '<script type="text/javascript">'.'window.location.href = "initialize.php?url=" + "'.$_REQUEST['url'].'";</script>';
						}
						else
						{
							echo  '<script>window.location.href = "initialize.php";</script>';
						}
				}
			}
			
			if($result > 0)
			{
				//$sqlII = "SELECT `member_id`,`mobile_number`,`name`,`fbcode` FROM `login` where `login_id` = ".$result;
				//$loginData = $this->m_dbConn->select($sqlII);
				
				if(count($loginData) > 0)
				{
					//send email to support team for assigning society to registered member
					//$res = $this->sendEmail($loginData);
				}	
			}
		}
		return $result;
	}
	
	public function getModuleAccess()
	{
		$query = "Select * from module where society_id = '" . $_SESSION['society_id'] . "'";
		$result = $this->m_dbConn->select($query);
		
		if(!$result)
		{
			$result = $this->setModuleAccess();
		}
		
		$_SESSION['module']['service_request'] = $result[0]['service_request'];
		$_SESSION['module']['notice'] = $result[0]['notice'];
		$_SESSION['module']['event'] = $result[0]['event'];
		$_SESSION['module']['document'] = $result[0]['document'];						
		$_SESSION['module']['service'] = $result[0]['service'];						
		$_SESSION['module']['classified'] = $result[0]['classified'];						
		$_SESSION['module']['forum'] = $result[0]['forum'];						
		$_SESSION['module']['directory'] = $result[0]['directory'];						
	}
	
	public function setModuleAccess()
	{
		$query = "Insert into module (`society_id`) values ('" . $_SESSION['society_id'] . "') ";
		$result = $this->m_dbConn->insert($query);
		
		$response = array();
		$response[0]['service_request'] = 1;
		$response[0]['notice'] = 1;
		$response[0]['event'] = 1;
		$response[0]['document'] = 1;
		$response[0]['service'] = 1;
		$response[0]['classified'] = 1;
		$response[0]['forum'] = 1;
		$response[0]['directory'] = 1;
		
		return $response;
	}
	
	public function updateModuleAccess($sr_req, $notice, $event, $document, $service, $classified, $forum, $directory)
	{
		$query = "UPDATE `module` SET `service_request`= '" . $sr_req . "', `notice`= '" . $notice . "', `event`= '" . $event . "', `document`= '" . $document . "', `service`= '" . $service . "', `classified`= '" . $classified . "', `forum`= '" . $forum . "', `directory`= '" . $directory . "' WHERE `society_id` = '" . $_SESSION['society_id'] . "'";
		$result = $this->m_dbConn->update($query);
	}
	
	private function getUserIP() 
	{		
		if( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) 
		{
			if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')>0) {
				$addr = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
				return trim($addr[0]);
			} 
			else 
			{
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
		}
		else 
		{
			return $_SERVER['REMOTE_ADDR'];
		}
	}
	
	public function saveLoginDetails()
	{
		$locdetails = json_decode(file_get_contents("http://ipinfo.io/" . $this->getUserIP() . "/json"), true);	
		$mMysqli = mysqli_connect(DB_HOST_ROOT, DB_USER_ROOT, DB_PASSWORD_ROOT, DB_DATABASE_ROOT);				
		
		$aryTimeStamp = getCurrentTimeStamp();
		
		$sqlInsert = 'INSERT INTO `LoginTrackingLog`(`UserId`, `MappingID`, `IP`, `Hostname`, `City`, `Region`, `Country`, `Location`, `ISP`, `Postal_Code`, `Timestamp`) VALUES ("'.$_SESSION['login_id'].'","'.$_SESSION['current_mapping'].'","'.$locdetails['ip'].'","'.$locdetails['hostname'].'","'.$locdetails['city'].'","'.$locdetails['region'].'","'.$locdetails['country'].'","'.$locdetails['loc'].'","'.$locdetails['org'].'","'.$locdetails['postal'].'", "' . $aryTimeStamp['DateTime'] . '")';				
		if($mMysqli)
		{
			$result = $mMysqli->query($sqlInsert);
			mysqli_close($mMysqli);	
		}									
		//$data = $this->m_dbConn->insert($sqlInsert);		
	}		
	public function sendEmail($loginData,$bIsSendSocistyDetails = false,$data = "")
	{
		include_once('email_format.class.php');
		require_once('swift/swift_required.php');
		
		try
	  {
		   $transport = Swift_SmtpTransport::newInstance('cs10.webhostbox.net', 465, "ssl")
								->setUsername('no-reply14@way2society.com')
								->setSourceIp('0.0.0.0')
								->setPassword('society123') ; 
								
																						
			//Create Email Body
			$mailBody = "Hi ,Techsupport Team<br><br>";
			$mailBody .= "A new user has registered on the www.way2society.com.<br>";
			$mailBody .= "Account details are as follows:<br><br>";
			
			
			
			if($loginData[0]['fbcode']  == "")
			{
				$mailBody .= "<table align='center' border='1'  style='border-collapse: collapse;'><tr><th style='padding: 20px 20px 20px 20px;'>Name</th><th style='padding: 20px 20px 20px 20px;'>Email ID</th><th  style='padding: 20px 20px 20px 20px;'>Mobile Number</th></tr>
        							<tr><td style='padding: 20px 20px 20px 20px;'>".$loginData[0]['name']."</td><td  style='padding: 20px 20px 20px 20px;'>".$loginData[0]['member_id']."</td><td  style='padding: 20px 20px 20px 20px;'>".$loginData[0]['mobile_number']."</td></tr></table>";
			}
			else
			{
				$mailBody .= "User has registered via fconnect:";
				$mailBody .= "<table  align='center' border='1'  style='border-collapse: collapse;'><tr><th style='padding: 20px 20px 20px 20px;'>Name</th><th style='padding: 20px 20px 20px 20px;'>Email ID / Mobile Number</th></tr>
        							<tr><td  style='padding: 20px 20px 20px 20px;'>".$loginData[0]['name']."</td><td  style='padding: 20px 20px 20px 20px;'>".$loginData[0]['member_id']."</td></tr></table>";
			}
			
			if($bIsSendSocistyDetails == true && $data <> "" && count($data) > 0)
			{
				$mailBody .= "<br><br>Society details are as follows:<br><br>";
				$mailBody .= "<table  align='center' border='1'  style='border-collapse: collapse;'><tr><th style='padding: 20px 20px 20px 20px;'>Society Name</th><th style='padding: 20px 20px 20px 20px;'>Wing</th><th style='padding: 20px 20px 20px 20px;'>Unit/Flat/Shop No</th></tr>
        							<tr><td  style='padding: 20px 20px 20px 20px;'>".$data['society_name']."</td><td  style='padding: 20px 20px 20px 20px;'>".$data['wing']."</td><td  style='padding: 20px 20px 20px 20px;'>".$data['unit']."</td></tr></table>";	
			}
			
			$array = json_decode(file_get_contents("http://ipinfo.io/" . $this->getUserIP() . "/json"), true);	
			
			//send ip details of user
			$mailBody .= '<br><br>Additional Information :<br><table border="1" cellpadding="10" style="border-collapse: collapse;" align="center">';
			 foreach($array as $key => $value)
		   {
				$mailBody .='<tr><td style="padding: 20px 20px 20px 20px;">'.$key.'</td><td style="padding: 20px 20px 20px 20px;">'.$value.'</td></tr>';
		   }
			$mailBody .='</table><br><br>';
			
			if($this->bIsNewUserConnectedViaStoredProcedure == true)
			{
					$mailBody .='<br><br><font  color="#00CC00" size="14px;">User has been successfully connected.</font>';
			}
					
			if($bIsSendSocistyDetails == false)
			{
				//sending welcome email to new member
				$memberMailSubject = "Welcome,".$loginData[0]['name']."!"; 																			
				
				//$memberMailBody = GetEmailHeader();
				$memberMailBody = "Hi ".$loginData[0]['name']."<br>Welcome to way2society.<br><br>";
				$memberMailBody .=" 	Here's your account information:<br>
													Name: ".$loginData[0]['name']."<br>
													Username: ".$loginData[0]['member_id']."<br>";
				
				// Send the email
				$result1 = false;
				//if($loginData[0]['member_id'] <> ""  && isValidEmailID($this->m_dbConn->escapeString($loginData[0]['member_id'])) == true)
				//{
					//$result1 = $this->sendSwiftMessage($transport,$memberMailSubject,$memberMailBody,$loginData[0]['member_id'] , $loginData[0]['name']);
					$result1 = $this->sendSwiftMessage($transport,$memberMailSubject,$memberMailBody,$loginData[0]['email'] , $loginData[0]['name']);
				//}
			}
			
			// Send the email
			$result2 =  $this->sendSwiftMessage($transport,'New user registered  on way2society',$mailBody, 'techsupport@way2society.com','Techsupport');
			//$result2 =  $this->sendSwiftMessage($transport,'New user registered  on way2society',$mailBody, 'dalvishreya106@gmail.com','Techsupport');
			
			if($bIsSendSocistyDetails == false)
			{
				if($result1 && $result2)
				{
					return true;
				}
				else
				{
					return false; 			
				}
			}
			else if($bIsSendSocistyDetails == true && $result2)
			{
				return true;
			}
			else
			{
				return false; 	
			}
		
			return $result;
	  }
	  catch(Exception $exp)
	  {
		echo "Error occured in email sending.";
		}
	  
	}
	
	public function fetchLoginDetails($id = 0)
	{
		if($id > 0)
		{
			$sql ="select `member_id`,`name`,`mobile_number`,`fbcode` from `login` where `login_id` = '".$id."' ";
		}
		else
		{
			$sql ="select `member_id`,`name`,`mobile_number`,`fbcode` from `login` where `login_id` = '".$_SESSION['login_id']."' ";
		}
		$result = $this->m_dbConn->select($sql);	
		
		return $result;
	}
	
	public function sendSwiftMessage($transport ,$MailSubject,$MailBody,$toId,$toName)
	{
		$emailContent = GetEmailHeader() . $MailBody . GetEmailFooter() ;
		$mailer = Swift_Mailer::newInstance($transport);
		$message = Swift_Message::newInstance();
		$message->setTo(array( $toId => $toName));
	 
		$message->setSubject($MailSubject);
		$message->setBody($emailContent);
		$message->setFrom(array('no-reply14@way2society.com' => 'no-reply'));
		//$message->setBcc(array( 'techsupport@way2society.com' => 'Tech Support'));
	
		$message->setContentType("text/html");	
	 
		// Send the email
		$result = $mailer->send($message);	
		
		if($result <> 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	public function sendNewUserActivationEmail($email ,  $name ,$sAccountActivationCode ="")
	{
		include_once('email_format1.class.php');
		require_once('../swift/swift_required.php');
		
		$EMailIDToUse = $this->m_objUtility->GetEmailIDToUse(false, 0, 0, 0, 0, 0, 0, 0, 0);
		//var_dump($EMailIDToUse);
		
		$EMailID = "";
		$Password = "";
				
		if(isset($EMailIDToUse) && $EMailIDToUse['status'] == 0)
		{
				$EMailID = $EMailIDToUse['email'];
				$Password = $EMailIDToUse['password'];
		}
		//echo "email:". $EMailID . "pwd" . $Password;
		/*$transport = Swift_SmtpTransport::newInstance('cs10.webhostbox.net', 465, "ssl")*/
		$transport = Swift_SmtpTransport::newInstance('cs10.webhostbox.net', 465, "ssl")
									->setUsername($EMailID)
									->setSourceIp('0.0.0.0')
									->setPassword($Password) ; 
		  try
		  {
			  	$MailBody = $this->generateActivationEmailTemplate(true,$email,$name,$sAccountActivationCode);							
				$emailContent = GetEmailHeader() . $MailBody . GetEmailFooter();
				$mailer = Swift_Mailer::newInstance($transport);
				$message = Swift_Message::newInstance();
			 	//$message->setTo(array( 'dalvishreya106@gmail.com' => 'name'));
				$message->setTo(array($email => $name));
			 	$message->setSubject('Way2society Account Activation');
				$message->setBody($emailContent);
				$message->setFrom(array('no-reply@way2society.com' => 'way2society'));
				//$message->setBcc(array( 'techsupport@way2society.com' => 'Tech Support'));
				$message->setContentType("text/html");	
				// Send the email
				$result = $mailer->send($message);	
				if($result >= 1)
				{		
					//echo 'result : ' .$result;
					echo $email.':Success';
				}
				else
				{
					echo $email.':Failed';
				}
					
				//return $result;
		  }
		  catch(Exception $exp)
		  {
			echo "Error occured in email sending.";
			//print_r($exp->getMessage());
		}	
	}
	
	public function getSwiftEmailObject()
	{
		
		require_once('swift/swift_required.php');
		
		$transport = Swift_SmtpTransport::newInstance('md-in-1.webhostbox.net', 465, "ssl")
									->setUsername('no-reply14@way2society.com')
									->setSourceIp('0.0.0.0')
									->setPassword('society123') ; 
		return $transport;	
		
	}
	
	public function checkIfEmailAlreadyExists($sEmailID)
	{
		$result = $this->m_dbConn->ExecStoredProcWithoutWithParameters('checkUserEmailIdAlreadyExists("'.$sEmailID.'")');
		//var_dump($result);	
		return $result;
	}
	
	public function generateActivationEmailTemplate($bShow = false,$email , $name,$sAccountActivationCode = "")
	{
		    $encryptedEmail = $this->m_objUtility->encryptData($email);	
			$loginExist = $this->m_objDBRoot->select("SELECT * FROM `login` WHERE `member_id` ='".$email."'");		
		  if(sizeof($loginExist) > 0)
		  {
			  //$Url = 'http://way2society.com/login.php?mCode='.$sAccountActivationCode.'&u='.$encryptedEmail;
				$Url = 'http://way2society.com/login.php?mCode='.$sAccountActivationCode.'&u='.$encryptedEmail;
		  }
		  else
		  {
				$newUserUrl = "http://way2society.com/newuser.php?reg&u=".$email."&n=".$name."&tkn=".$encryptedEmail;
			   $Url = $newUserUrl.'&URL=http://way2society.com/Dashboard.php?View=MEMBER';
		  }
		   
		   $mailBody = "Hi ".$name.",";
		   
		  $mailBody .="<br /><br />Thank you for registering with way2society.com.";
		  $mailBody .="<br />We have received online request for registeration for ".$email;
		  $mailBody .= "<br />Please click  on the following link to activate your account and complete registration process.";
		  if($bShow == true)
		  {
			$mailBody .= '<br /><br /><table width="200px" cellspacing="0" cellpadding="0" border="0">
										<tbody><tr><td valign="middle" bgcolor="#337AB7" height="40" align="center" style="text-align:center;">
										<a  id="act_btn" target="_blank" style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif" href="'.$Url.'">Activate</a></center></td>
										</tr></center></tbody></table>';	
		  }
		  else
		  {
			  	 $mailBody .= '<br /><br /><table width="200px" cellspacing="0" cellpadding="0" border="0">
										<tbody><tr><td valign="middle" bgcolor="#337AB7" height="40" align="center" style="text-align:center;">
										<a  id="act_btn" target="_blank" style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif;" >Activate</a></center></td>
										</tr></center></tbody></table>';	
		  }
		  	return $mailBody;
	}
	
	public function bEmailIdAlreadyExists($email)
	{
		$sqlCheck = "select count(member_id) as cnt from login where member_id = '" . $email . "'";
		$resultCheck = $this->m_dbConn->select($sqlCheck);	
		if($resultCheck[0]['cnt'] > 0)
		{
			return true;	
		}
		return false;
	}
}
?>