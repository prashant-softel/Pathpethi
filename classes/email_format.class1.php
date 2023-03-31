


<?php 
	error_reporting(E_ALL);
	$baseDir = dirname( dirname(__FILE__) );
	//echo $baseDir;
	include_once($baseDir.'/swift/swift_required.php');
	
	function sendFDEmail($name,$duedate, $email ,$data,$bankName,$socoetyName)
	{
		$baseDir = dirname( dirname(__FILE__) );
		
		$mailSubject = "Fixed Deposit Maturity Reminder [FDR No#".$data[0]['fdr_no']."]";
		$url="";
		
		$mailBody = GetEmailHeader();
		
		$mailBody .= '<tr>
									<td>Dear  "'.$socoetyName.'",<br /><br />Please be infomed that your Fixed Deposit account is nearing it maturity period. Kindly renew Fixed Deposit account to avail further benefits.<br />
									</td>                   
								</tr>
								<tr><td><br /></td></tr>';
								
		$mailBody .= '<tr>
								<td>Fixed Deposit Details are as follows :<br />
								</td>                   
							</tr>
							<tr><td><br /></td></tr>';
				
		//table headers
		$mailBody .= '<tr><td align="center" ><table style="border-collapse:collapse; border:1px solid black;" cellpadding="10px" width="100%">
							<tr height="30"  bgcolor="#337AB7"   style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif;"><th style="border:1px solid black;">Bank Name</th><th style="border:1px solid black;">FDR No</th><th  style="border:1px solid black;">Issue Date</th><th  style="border:1px solid black;">Maturity Date</th><th  style="border:1px solid black;">Maturity Amount</th></tr>';
							
		$mailBody .= '<tr> <td style="border:1px solid black;" >'.$bankName.'</td><td style="border:1px solid black;" >'.$data[0]['fdr_no'].'</td> <td style="border:1px solid black;">'. getDisplayFormatDateEx($data[0]['deposit_date']).'</td><td style="border:1px solid black;">'.getDisplayFormatDateEx($data[0]['maturity_date']).'</td><td style="border:1px solid black;">'.number_format($data[0]['maturity_amt'],2).'</td></tr> </table></td></tr>';					
	  	
		$mailBody .= ' <tr><td><br /></td></tr>
								<tr >
									<td align="center"  >
										<table width="250px" >
                                            <tbody>
                                                <tr>
                                                    <td valign="middle" bgcolor="#337AB7" height="40" align="center" style="cursor:pointer;">
													
                                                    
													<a href="localhost/beta/FixedDeposit.php" >
													<div style="cursor:pointer;" onClick=\'window.open("//localhost/beta/FixedDeposit.php");\' >
														<font  style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif;"><b>Fixed Deposit Details</b></font>
													</div>
													</a>
                                                 	
													</td> </tr>
                                            </tbody>
                                        </table>
									</td>
								</tr>';
		//$mailBody .= "'".file_get_contents($baseDir.'/may.php', true)."'";				
	  
	  	$mailBody .=   GetEmailFooter();
		
		echo $mailBody;
		
		  // Create the mail transport configuration				
		  $societyEmail = $email;	  
		  if($societyEmail == "")
		  {
			 $societyEmail = "techsupport@way2society.com";
		  }
		 echo "<br>".$societyEmail;
		   try
		  {		
		  	$transport = Swift_SmtpTransport::newInstance('cs10.webhostbox.net', 465, "ssl")
					->setUsername('no-reply@way2society.com')
					->setPassword('society123'); 
			// Create the message
			$message = Swift_Message::newInstance();
					
			$message->setTo(array(
			   $email => $name
			));	
			$message->setSubject($mailSubject);
			$message->setBody($mailBody);
			$message->setFrom("no-reply@way2society.com", $name);
			$message->setContentType("text/html");										 
					
			// Send the email				
			$mailer = Swift_Mailer::newInstance($transport);
			$result = $mailer->send($message);											
			if($result == 1)
			{
				return 'Success';
			}
			else
			{
				return 'Failed';
			}	
		  }
			catch(Exception $exp)
			{
				return "Error occure in email sending.";
			}
	}
	
	
	
	function GetEmailHeader()
	{
		$mailText = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
							   <table width="100%">';
		return $mailText;							  	
	}
	
	function GetEmailFooter()
	{
		$mailText = '<tr><td><br /></td></tr>
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
										<a href="https://twitter.com/pavitraglobal" target="_blank"><img src="http://way2society.com/images/icon2.jpg" alt=""></a>                  
									</td>
									<td style="font-size: 0; line-height: 0;" width="20">&nbsp;&nbsp;</td>
									<td>
										<a href="https://www.facebook.com/PavitraGlobalServicesLtd" target="_blank"><img src="http://way2society.com/images/icon1.jpg" alt=""></a>                 
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
		return $mailText;					
	}
	
	
	function getDisplayFormatDateEx($yyyymmdd, $seperator = '-')
	{
		$ddmmyyyy = '';
		if(strtotime($yyyymmdd) <> '' &&  $yyyymmdd <> '0000-00-00' && $yyyymmdd <> '00-00-0000')
		{
			$ddmmyyyy = date("d" . $seperator . "m" . $seperator . "Y", strtotime($yyyymmdd));
			
		}
		/*else
		{
			return '00-00-0000';
		}*/
		return $ddmmyyyy;
		
	}
	
?>	