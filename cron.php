<?php
$to = "rohit.shinde2010@gmail.com";
	$subject = "My new cron job";
	$txt = "Hello world!  Time : ";

echo "Hello world!  Time";
	$headers = "From: dalvishreya106@gmail.com" . "\r\n" .
	"CC: dalvishreya106@gmail.com";
	mail($to,$subject,$txt,$headers);




$sendSMS = "http://sms.transaction.surewingroup.info/submitsms.jsp?user=waysoc&key=7009e8caf1XX&mobile=9029261598&message=CronJob&senderid=waysoc&accusage=1";														
							
					$sendSMS1 = str_replace(" ", '%20', $sendSMS);
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $sendSMS1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					//$response = curl_exec($ch);
				curl_close($ch);
				//	echo $response;

?> 
