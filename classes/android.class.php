<?php if(!isset($_SESSION)){ session_start(); }

	include_once ("include/dbop.class.php");
	include_once ("include/fetch_data.php");
	//echo "unit:".$_REQUEST['unit'];
	$arUnits = (array)json_decode($_REQUEST['unitsArray']);
	//print_r($arUnits);
	//$arUnits = explode("", $arUnits);
	//echo "unit:".$arUnits;
	foreach ($arUnits as $key => $value) 
	{
		//echo "key:".$key . " value:".$value;
		$Unit = "";
		if(isset($_REQUEST['unit']))
		{
			$Unit = $_REQUEST['unit'];
		}
		else
		{

			$Unit = $value;
		
		}
		if(isset($Unit) && isset($_REQUEST['society']))
		{
			//echo "test:";
			$dbConn = new dbop();
			//print_r($dbConn);
			$obj_fetch = new FetchData($dbConn);
			$memberDetails = $obj_fetch->GetMemberDetails($Unit);
			$mailToEmail = $obj_fetch->objMemeberDetails->sEmail;
			//echo "email:".$mailToEmail;
			if($mailToEmail != "")
			{
				//echo "<br/>email:".$mailToEmail;
			
			$objAndroid = new android($mailToEmail, $_REQUEST['society'], $Unit);
			print_r($objAndroid->sendBillNotification($_REQUEST['title'], $_REQUEST['message'], $_REQUEST['period'], $_REQUEST['billtype']));
			}
		}
	}
	class android
	{
		private $m_iMapID;
		private $m_sDeviceID;

		function __construct($sUserEmail, $iSocietyID, $iUnitID)
		{
			$this->m_iMapID = 0;
			$this->m_sDeviceID = "";

			$this->getMappingDetails($sUserEmail, $iSocietyID, $iUnitID);
		}

		private function getMappingDetails($sUserEmail, $iSocietyID, $iUnitID)
		{
			$dbConnRoot = new dbop(true);

			$getMappingDetails = "SELECT d.login_id, l.login_id, l.member_id, d.device_id, m.id as 'map_id' from `device_details` as d JOIN `login` as l on l.login_id = d.login_id JOIN `mapping` as m on m.login_id = d.login_id WHERE m.unit_id = '" . $iUnitID . "' AND m.society_id = '" . $iSocietyID . "' AND l.member_id = '" . $sUserEmail . "' AND d.device_id <> '' ORDER BY d.id DESC LIMIT 0,1";

			$result = $dbConnRoot->select($getMappingDetails);

			if($result <> '')
			{
				$this->m_iMapID = $result[0]['map_id'];
				$this->m_sDeviceID = $result[0]['device_id'];
			}
		}	

		private function sendPushNotification($sURL)
		{
			//echo '<br>URL : ' . $sURL;
			//$sURL = "http://way2society.com:8080/Way2Society/Login?Email=ankur.2088@yahoo.co.in&Password=123&deviceID=1";

			//header('Location:'.$sURL);
			$responseArray = array();
			$responseArray['status'] = 0;

			if($this->m_iMapID == 0)
			{
				$responseArray['message'] = 'Mapping Details Missing';
				return $responseArray;
			}

			if($this->m_sDeviceID == "")
			{
				$responseArray['message'] = 'Device Details Missing';
				return $responseArray;
			}

			try
			{
				//$ch = curl_init();

				$headers = array(
				    'Accept: application/json',
				    'Content-Type: application/json',
				);
				$ch = curl_init($sURL);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_getinfo($ch, CURLINFO_HEADER_OUT);
				curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($ch);

				//$sent_request = curl_getinfo($ch, CURLINFO_HEADER_OUT);
				//var_dump($sent_request);
				curl_close($ch);
				
				$responseArray['status'] = 1;
				$responseArray['message'] = $response;
			}
			catch(Exception $exp)
			{
				$responseArray['message'] = $exp;
			}

			//print_r($responseArray);

			return json_encode($responseArray);
		}

		public function sendBillNotification($sTitle, $sMessage, $iPeriodID, $iBillType)
		{
			$sURL = "http://way2society.com:8080/Way2Society/Notification?map_id=" . $this->m_iMapID . "&title=" . $sTitle . "&message=" . $sMessage . "&deviceID=" . $this->m_sDeviceID . "&page_ref=1&details=".$iPeriodID."/".$iBillType;
			$sURL = str_replace(" ",'%20',$sURL);
			return $this->sendPushNotification($sURL);
		}

		public function sendGeneralNotification($sTitle, $sMessage)
		{
			$sURL = "http://way2society.com:8080/Way2Society/Notification?map_id=" . $this->m_iMapID . "&title=" . $sTitle . "&message=" . $sMessage . "&deviceID=" . $this->m_sDeviceID . "&page_ref=0&details=";

			return $this->sendPushNotification($sURL);
		}

		public function sendNoticeNotification($sTitle, $sMessage, $iNoticeID)
		{
			$sURL = "http://way2society.com:8080/Way2Society/Notification?map_id=" . $this->m_iMapID . "&title=" . $sTitle . "&message=" . $sMessage . "&deviceID=" . $this->m_sDeviceID . "&page_ref=2&details=".$iNoticeID;
			$sURL = str_replace(" ",'%20',$sURL);
			//echo "<br>url:".$sURL;
			return $this->sendPushNotification($sURL);
		}	
		public function sendEventNotification($sTitle, $sMessage, $iEventID)
		{
			$sURL = "http://way2society.com:8080/Way2Society/Notification?map_id=" . $this->m_iMapID . "&title=" . $sTitle . "&message=" . $sMessage . "&deviceID=" . $this->m_sDeviceID . "&page_ref=3&details=".$iPollID;
			$sURL = str_replace(" ",'%20',$sURL);
			return $this->sendPushNotification($sURL);
		}
		public function sendPollNotification($sTitle, $sMessage, $iPollID)
		{

			$sURL = "http://way2society.com:8080/Way2Society/Notification?map_id=" . $this->m_iMapID . "&title=" . $sTitle . "&message=" . $sMessage . "&deviceID=" . $this->m_sDeviceID . "&page_ref=5&details=".$iPollID;
			$sURL = str_replace(" ",'%20',$sURL);
			//echo $sURL;
			return $this->sendPushNotification($sURL);
		}
	}
 		
?>