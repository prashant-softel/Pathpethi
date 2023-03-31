<?php 
	include_once ("include/dbop.class.php");
	$dbConn = new dbop();
	$dbConnRoot = new dbop(true);	
	
	include_once("utility.class.php");
	$obj_Utility = new utility($dbConn);
	
	include_once("client.class.php");
	$obj_client = new client($dbConnRoot);

	require_once('../swift/swift_required.php');									
	
	if(isset($_REQUEST['email']))
	{
		$mailToEmail = $_REQUEST['email'];
		if($mailToEmail == '')
		{
			echo 'Email ID Missing';
			exit();
		}
		
		$sql = 'SELECT `code` FROM `mapping` WHERE `id` = "'.$_REQUEST['mID'].'"';
		$code = $obj_client->m_dbConnRoot->select($sql);
		
		$clientQuery = "SELECT `email`, `client_name`,`email_footer`,`mobile` FROM `client` WHERE `id` = '".$_REQUEST['clientID']."'";
		$clientDetails = $obj_client->m_dbConnRoot->select($clientQuery);
		//print_r($clientDetails);
		$footerEmail=$clientDetails[0]['email_footer'];
		$mob=$clientDetails[0]['mobile'];
		$clientEmail=$clientDetails[0]['email'];
		
		$sqlQuery = "SELECT count(*) AS 'cnt' FROM `login` WHERE `member_id` = '".$mailToEmail."'";
		$isEmailIDReg = $obj_client->m_dbConnRoot->select($sqlQuery);
									
		$encryptedEmail = $obj_Utility->encryptData($mailToEmail);	
		if($isEmailIDReg[0]['cnt'] > 0)
		{
			$newUserUrl = "http://way2society.com/login.php?mCode=".$code[0]['code']."&u=".$encryptedEmail;
			$buttonText = 'Sign in';
			$msg = 'Please click on Sign in to login and connect to another society.';
		}
		else
		{
			$newUserUrl = "http://way2society.com/newuser.php?reg&u=".$mailToEmail."&n=''&c=".$code[0]['code']."&tkn=".$encryptedEmail;	
			$buttonText = 'Sign up';
			$msg = 'Please click on Sign up to create new account.';					
		}
		
		$mailSubject = 'Invitation To Join On way2society.com';	
		
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
									<td>Dear User,<br /><br /> You have been invited to way2society.'.$msg.'												
									</td>									
								</tr>
								<tr><td><br /></td></tr>
								<tr>
									<td align="center">
										<table width="150px" cellspacing="0" cellpadding="0" border="0">
											<tbody>
												<tr>
													<td valign="middle" bgcolor="#337AB7" height="40" align="center">
														<a target="_blank" style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif" href="'.$newUserUrl.'">'.$buttonText.'</a>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
								
								<tr><td><br /></td></tr>
								<tr>
									<td font="colr:#999999;"><br>'.$footerEmail.'
									Email : '.$clientEmail.'<br />
									Contact No : '.$mob.' <br />
									</td>
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
										
		// Create the mail transport configuration
		//$transport = Swift_SmtpTransport::newInstance('cs10.webhostbox.net', 465, "ssl")
		$transport = Swift_SmtpTransport::newInstance('cs10.webhostbox.net', 465, "ssl")
			  ->setUsername('no-reply@way2society.com')
			  ->setSourceIp('0.0.0.0')
			  ->setPassword('society123') ;	 
		// Create the message
		$message = Swift_Message::newInstance();
		$message->setTo(array(
		  $mailToEmail => ''
		 ));
		 
		 $clientEmail = $clientDetails[0]['email'];
		 if($clientEmail == '')
		 {
			 $clientEmail = "societyaccounts@pgsl.in";
		 }
		 		 				
		 $message->setReplyTo(array(
		   $clientEmail => $clientDetails[0]['client_name']
		));
		 
		$message->setSubject($mailSubject);
		$message->setBody($mailBody);
		$message->setFrom("no-reply@way2society.com", '');
		
		$message->setContentType("text/html");	
		 		
		// Send the email
		$mailer = Swift_Mailer::newInstance($transport);
		$result = $mailer->send($message);
				
		if($result >= 1)
		{		
			echo 'Success';
		}
		else
		{
			echo 'Failed';
		}
	}
	else
	{
		echo 'Missing Parameters';
	}
	
?>