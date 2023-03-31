<?php
	$smsTemplate ='<?xml version="1.0"?>
							<parent>
							<child>
							<user>waysoc</user>
							<key>7009e8caf1XX</key>
							<mobile>+919820040095</mobile>
							<message>Dear Member,As per the government new circulation,all the government department and government hospital etc will accept old currency till 14th Nov 2016</message>
							<senderid>waysoc</senderid>
							<accusage>1</accusage>
							
							</child>						
							</parent>';	
							
	$URL = "http://sms.transaction.surewingroup.info/submitsms.jsp?";
		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, "$smsTemplate");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		curl_close($ch);
		echo $response;
	

?>