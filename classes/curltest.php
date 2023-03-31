
<?php
echo '<pre>';
var_dump(curl_version());
echo '</pre>';
?>
<?php 

/*$xml_data ='<?xml version="1.0"?>
<parent>
<child>
<user>waysoc</user>
<key>7009e8caf1XX</key>
<messageid>172984816</messageid>
</child>
</parent>';*/

/*$URL = "http://sms.transaction.surewingroup.info/getreport.jsp?"; 
echo "URL:[".$URL ."]";
			$ch = curl_init($URL);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			echo "before execute:[]";
			$output = curl_exec($ch);
			echo "after execute:[]";
			curl_close($ch);

print_r($output); */

/*$URL = "http://google.com"; 
$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $URL);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				$response = curl_exec($ch);
				curl_close($ch);
				echo $response;*/
$Template ='<?xml version="1.0"?>
							<parent>
							<child>
							<user>waysoc</user>
							<key>7009e8caf1XX</key>
							<mobile>+918898876268</mobile>
							<message>crontab sms testing.</message>
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
		curl_setopt($ch, CURLOPT_POSTFIELDS, "$Template");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		curl_close($ch);				
echo "result:".$response;
?>
