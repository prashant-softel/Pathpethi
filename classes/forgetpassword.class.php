<?php

//include_once('dbconst.class.php');
require_once('swift/swift_required.php');

class forgetpassword
{
	public $m_dbConn;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
	}
	
	function checkEmail($email)
	{
		$query = 'Select member_id, password, name, fbcode from login where member_id = "' . $email . '"';
		$result = $this->m_dbConn->select($query);
		
		if(isset($result[0]['member_id']))
		{
			$response =	$this->sendEmail($email, $result[0]['password'], $result[0]['name'],$result[0]['member_id'], $result[0]['fbcode']);
			return $response;
		}
		else
		{
			return 'E-Mail ID is not registered';
		}
	}
	
	private function sendEmail($email, $password, $name,$member_id, $fbcode)
	{
		
		$mailSubject = 'Account Password Recovery';
		$mailTxt = '';
		/*$mailBody = 'Hello <b>'.$name.'</b>,<br><br><br>';
		$mailBody .="As requested,Below is your Account login information:<br>";
		$mailBody .='User Name : ' . $member_id."<br>";
		$mailBody .='Current Password : ' . $password." [Note that password is case sensitive.]<br><br>";
		$mailBody .="Thank you,<br>way2society.com"; */
		//$mailBody = 'Dear <b>'.$name.'</b>,<br>';
		if($password == '' && $fbcode <> '')
		{
			$mailTxt .= 'We have identified that you are connected to way2society.com using your facebook account. Kindly select "Sign in with Facebook" option and use your Facebook credentials to login.<br><br>';
		}
		else
		{
			$mailTxt .="Your log-in details are as given below:<br><br>"; 
			$mailTxt .='Password : <b>' . $password."</b>";
		}
		$mailBody .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
									<td>Dear <b>'.$name.'</b>,<br /><br />'.$mailTxt.'<br />
									</td>                   
								</tr>
								<tr><td><br /></td></tr>
								<tr>
									<td align="center">
										<table width="250px" cellspacing="0" cellpadding="0" border="0">
                                            <tbody>
                                                <tr>
                                                    <td valign="middle" bgcolor="#337AB7" height="40" align="center">
                                                        <a target="_blank" style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif" href="http://way2society.com/login.php?mlog">Login To Way2Society</a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
									</td>
								</tr>
								<tr><td><br /></td></tr>
								<tr>
									<td>Always keep your username and password confidential to prevent any unauthorized access of your account.<br><br>
										If you need assistance, please write to us at cs@way2society.com <br><br>
									</td>                   
								</tr>
								<tr>
									<td font="colr:#999999;">Thank You,<br>way2society.com</td>
								</tr>
							   </table>
							 </td>
						   </tr>
						   <tr>
							 <td bgcolor="#CCCCCC" style="padding: 20px 20px 20px 20px;border-top:none;">
							   <table cellpadding="0" cellspacing="0" width="100%">           
								 <td >             
									<a href="http://way2society.com/" target="_blank"><i>Way2Society</i></a>              
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
					</html>';
		//$mailBody .='Always keep your username and password confidential to prevent any unauthorized access of your account.<br><br>';
		//$mailBody .='If you need assistance, please write to us at cs@way2society.com <br><br>';
		//$mailBody .="Thank you,<br>way2society.com";
		try
		{
			$transport = Swift_SmtpTransport::newInstance('cs10.webhostbox.net', 465, "ssl")
				  ->setUsername('cs@way2society.com')
				  ->setSourceIp('0.0.0.0')
				  ->setPassword('cs1234!') ;	 
			 
			// Create the message
			$message = Swift_Message::newInstance();
			$message->setTo(array(
			  $email => $name
			 ));
			 
			 $message->setReplyTo(array(
					   "cs@way2society.com" => "Way2Society"
					)); 
			$message->setSubject($mailSubject);
			$message->setBody($mailBody);
			$message->setFrom("cs@way2society.com", "Way2Society");
			$message->setContentType("text/html"); 
			$mailer = Swift_Mailer::newInstance($transport);
			$result = $mailer->send($message);
			
			if($result == 1)
			{
				return 'E-Mail with recovery information has been sent  to ' . $email;
			}
			else
			{
				return 'Failed to send e-mail. Verify E-Mail ID and try again...';
			}
		}
		catch(Exception $exp)
		{
			return $exp;
		}
	}
}
?>