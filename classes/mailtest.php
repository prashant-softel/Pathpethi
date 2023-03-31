
<?php
	require_once('swift/swift_required.php');
	
// The message
$message = "Line 1\r\nLine 2\r\nLine 3";

// In case any of our lines are larger than 70 characters, we should use wordwrap()
//$message = wordwrap($message, 70, "\r\n");
$emailContent = 
$transport = Swift_SmtpTransport::newInstance('cs10.webhostbox.net', 465, "ssl")
									->setUsername('no-reply14@way2society.com')
									->setSourceIp('0.0.0.0')
									->setPassword('society123') ; 
	
	$mailer = Swift_Mailer::newInstance($transport);
	$message = Swift_Message::newInstance();
	$emailContent = "test";
	//$message->setTo(array( 'dalvishreya106@gmail.com' => 'name'));
	$message->setTo(array("dalvishreya106@gmail.com" => "shreya"));
	$message->setSubject('Way2society Account Activation');
	//$message->setBody($emailContent);
	$message->setFrom(array('no-reply14@way2society.com' => 'no-reply'));
	$message->setContentType("text/html");	
	// Send the email
	// You can embed files from a URL if allow_url_fopen is on in php.ini
	$baseDir = dirname( dirname(__FILE__) );
			
			$fileName =  $baseDir . "/images/bank_cash.png";
			echo $fileName;
$message->attach(Swift_Attachment::fromPath($fileName)  
					->setDisposition('inline'));
	$result = $mailer->send($message);	
	if($result >= 1)
	{		
		echo 'result : ' .$result;
	}
	else
	{
		echo 'Failed';
	}
									
				 
?>

