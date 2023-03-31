<?php
	include_once "dbconst.class.php";
	include_once("include/display_table.class.php");
	
	class SmsHistory
	{
		private $m_dbConn;
		public $m_dbConnRoot;
		public $display_pg;
		
		function __construct($dbConn, $dbConnRoot = "")
		{
			$this->m_dbConn = $dbConn;
			$this->m_dbConnRoot = $dbConnRoot;
			$this->display_pg=new display_table($this->m_dbConn);
		}
		
		public function pgnation($data)
		{
			
			
			$LoginQuery = "SELECT `login_id`,`member_id` FROM `login`";
            $Details = $this->m_dbConnRoot->select($LoginQuery);
            $loginArr = array();
            for($cnt = 0; $cnt < sizeof($Details); $cnt++)
            {
                $loginArr[$Details[$cnt]['login_id']] = $Details[$cnt]['member_id'];
            }
			
			/*$memberQuery = "SELECT `unit`, `owner_name` FROM `member_main`";
			$memberDetails = $this->m_dbConn->select($memberQuery);
			$memberArr = array();
            for($cnt = 0; $cnt < sizeof($memberDetails); $cnt++)
            {
                $memberArr[$memberDetails[$cnt]['unit']] = $memberDetails[$cnt]['owner_name'];
            }*/
			
			$ledQuery = "SELECT `id`,`ledger_name` FROM `ledger`";
			$ledDetails = $this->m_dbConn->select($ledQuery);
			$ledArr = array();
            for($cnt = 0; $cnt < sizeof($ledDetails); $cnt++)
            {
                $ledArr[$ledDetails[$cnt]['id']] = $ledDetails[$cnt]['ledger_name'];
            }
			
			$history = array();
		if(sizeof($data) < 1  || (sizeof($data) > 0 && $data['sms_type'] == "0") || (sizeof($data) > 0 && $data['sms_type'] == "1")  ) 
			{
				$sql = 'SELECT gs.ID, gs.UnitID, DATE_FORMAT(gs.SentGeneralSMSDate,"%d-%m-%Y %H:%i:%s") as Date ,gs.MessageText,gs.SentBy,gs.SentReport,gs.status,gs.DeliveryStatus,gs.DeliveryReport FROM `generalsms_log` AS gs';  
				if($data <> "" && sizeof($data) > 0 ) 
				{
						if((isset($data['from_date']) && $data['from_date'] <> "" ) && (isset($data['to_date']) && $data['to_date'] <> ""))
						{
							$sql .= ' where DATE(`SentGeneralSMSDate`)  between  "'.getDBFormatDate($data['from_date']) . '" and  "'.getDBFormatDate($data['to_date']).'" ';	
						}
				}
				//echo '<br/>'.$sql;
				$genHistory = $this->m_dbConn->select($sql);
				if(sizeof($genHistory) > 0)
				{
					for($i = 0; $i < sizeof($genHistory); $i++)
					{
						$messageReport = explode(',',$genHistory[$i]['SentReport']);
						$DeliveryReport = explode(',',$genHistory[$i]['DeliveryReport']);
						$genHistory[$i]['DeliveryStatus'] = str_replace('"',"",$genHistory[$i]['DeliveryStatus']);
						
						if($genHistory[$i]['DeliveryStatus']  == "Pending DR not Found" || $genHistory[$i]['DeliveryStatus']  == "DR not Found")
						{
							$genHistory[$i]['DeliveryStatus']  = "Delivered";
						}
						
						 if (in_array('"Not Delivered"', $DeliveryReport))
						{
							$genHistory[$i]['DeliveryReport'] = "";
						}
						else if (in_array('"Pending DR not Found"', $DeliveryReport) || in_array('"DR not Found"', $DeliveryReport))
						{
							$genHistory[$i]['DeliveryReport'] = "DR Not Available";
						}
						else if (stripos($genHistory[$i]['DeliveryStatus'], 'Deliv') !== false) 
						{
							$genHistory[$i]['DeliveryReport']= date('d-m-Y H:i:s', strtotime(str_replace('"'," ",$DeliveryReport[5]) ));
						}
						else
						{
							$genHistory[$i]['DeliveryReport']= "";
						}
						
						if($genHistory[$i]['SentReport'] == "" || $genHistory[$i]['DeliveryStatus'] <> "")
						{
							$genHistory[$i]['View'] = "";
						}
					   else if($messageReport[2] == "") 
						{
							$genHistory[$i]['View'] = "";
							$genHistory[$i]['status'] =  "Failed";
						}
						else if(stripos($genHistory[$i]['SentReport'], 'Invalid ')  !== false || stripos($genHistory[$i]['SentReport'], 'Empty')  !== false)  
						{
							 $genHistory[$i]['View'] = "";
						}
						else if($messageReport[2] <> "")
						{					
							$genHistory[$i]['View'] = '<a onClick="GetDeliveryStatus(' . $messageReport[2] . ','.$genHistory[$i]['ID'].','."1".','.SMS_TYPE_GENERAL.')" style="cursor:pointer;">Refresh Status</a>';
						}
						$genHistory[$i]['SentBy'] = $loginArr[$genHistory[$i]['SentBy']];
						$genHistory[$i]['UnitID'] = $ledArr[$genHistory[$i]['UnitID']];
						unset($genHistory[$i]['SentReport']);	
						array_push($history,$genHistory[$i]);	
					}
				}
			}
			if(sizeof($data) < 1  || (sizeof($data) > 0 && $data['sms_type'] == "0") || (sizeof($data) > 0 && $data['sms_type'] == "2")  ) 
			{
				 $sql2 = 'SELECT ntftable.ID,`UnitID`,DATE_FORMAT(SentSMSReminderDate,"%d-%m-%Y %H:%i:%s") as Date,smsdetails.sms as MessageText,ntftable.SentBy,`DeliveryStatus` as status,`DeliveryStatus`,`DeliveryReport` ,ntftable.PeriodID,`SMSSentReport` as SentReport FROM `notification` as ntftable JOIN `rsmsdetails` as smsdetails on ntftable.PeriodID = smsdetails.PeriodID where `SentSMSReminderDate` <> "0000-00-00 00:00:00"  and `sms_type`=2 and smsdetails.sms <> "" ';
				if($data <> "" && sizeof($data) > 0 ) 
				{
						if((isset($data['from_date']) && $data['from_date'] <> "" ) && (isset($data['to_date']) && $data['to_date'] <> ""))
						{
							$sql2 .= '  and DATE(`SentSMSReminderDate`)   between "'.getDBFormatDate($data['from_date']) .'" and  "'.getDBFormatDate($data['to_date']).'"   ';	
						}
				}
					//echo '<br/>'.$sql2;
				
				 $renhistory = $this->m_dbConn->select($sql2);
				 
				
				 if(sizeof($renhistory) > 0 )
				 {
					   for($m = 0 ;$m < sizeof($renhistory);$m++)
					  {
							$renhistory[$m]['UnitID'] =  $ledArr[$renhistory[$m]['UnitID']];
							$mailBody = str_replace("unitfield",$renhistory[$m]['UnitID'],$renhistory[$m]['MessageText']);
						   $renhistory[$m]['MessageText'] =  $mailBody;
							$renhistory[$m]['SentBy']  = "Auto";
							 $renhistory[$m]['status'] =  "Auto";
							
							
							$DeliveryReport = explode(',', $renhistory[$m]['DeliveryReport']);
							$renhistory[$m]['DeliveryStatus'] = str_replace('"',"",$renhistory[$m]['DeliveryStatus']);
							
							if($renhistory[$m]['DeliveryStatus']  == "Pending DR not Found" || $renhistory[$m]['DeliveryStatus']  == "DR not Found")
							{
								$renhistory[$m]['DeliveryStatus']  = "Delivered";
							}
							
							 if (in_array('"Not Delivered"', $DeliveryReport))
							{
								$renhistory[$m]['DeliveryReport'] = "";
							}
							else if (stripos( $renhistory[$m]['DeliveryStatus'], 'Deliv') !== false) 
							{
								 $renhistory[$m]['DeliveryReport']= date('d-m-Y H:i:s', strtotime(str_replace('"',"",$DeliveryReport[5])));
							}
							else if (in_array('"Pending DR not Found"', $DeliveryReport) || in_array('"DR not Found"', $DeliveryReport))
							{
								$renhistory[$m]['DeliveryStatus']  = "Delivered";
								$renhistory[$m]['DeliveryReport'] = "DR Not Available";
							}
							else
							{
								 $renhistory[$m]['DeliveryReport']= "";
							}
							
							
							if($renhistory[$m]['SentReport'] <> "")
							{
								$messageReport = explode(',', $renhistory[$m]['SentReport']);
								 if(stripos($renhistory[$m]['SentReport'], 'Invalid ')  !== false) 
								{
									$renhistory[$m]['status'] =  "Invalid Mobile";
								}
								if(stripos($renhistory[$m]['SentReport'], 'Empty')  !== false) 
								{
									$renhistory[$m]['status'] =  "Empty Mobile No";
								}
								else
								{
									$renhistory[$m]['status'] =  $messageReport[1];
								}
								 
							}
							
							if( $renhistory[$m]['SentReport'] == "" || $messageReport[2] == "")
							{
								 $renhistory[$m]['View'] = " ";
							}
							else if($renhistory[$m]['DeliveryStatus'] <>  "") 
							{
								 $renhistory[$m]['View'] = "";
							}
							else if(stripos($renhistory[$m]['SentReport'], 'Invalid ')  !== false || stripos($renhistory[$m]['SentReport'], 'Empty')  !== false) 
							{
								 $renhistory[$m]['View'] = "";
							}
							else  if($messageReport[2] <> "")
							{					
								 $renhistory[$m]['View'] = '<a onClick="GetDeliveryStatus(' . $messageReport[2] . ','. $renhistory[$m]['ID'].','."0".','.SMS_TYPE_BILL_NOTIFICATION_CRON.' )" style="cursor:pointer;">Refresh Status</a>';
							}
							   
							unset($renhistory[$m]['SentReport']);		 
							unset($renhistory[$m]['PeriodID']);		 
							array_push($history,$renhistory[$m]);
					  }
					
				 }
			}
			if(sizeof($data) < 1  || (sizeof($data) > 0 && $data['sms_type'] == "0") || (sizeof($data) > 0 && $data['sms_type'] == "3")  ) 
			{
				 $sql2 = 'SELECT ntftable.ID,`UnitID`,DATE_FORMAT(SentBillSMSDate,"%d-%m-%Y %H:%i:%s") as Date,smsdetails.sms as MessageText,ntftable.SentBy,`DeliveryStatus` as status,`DeliveryStatus`,`DeliveryReport` ,ntftable.PeriodID,`SMSSentReport` as SentReport,`Bill_Amount` FROM `notification` as ntftable JOIN `rsmsdetails` as smsdetails on ntftable.PeriodID = smsdetails.PeriodID where `SentBillSMSDate` <> "0000-00-00 00:00:00" and `sms_type`=1 and smsdetails.sms <> "" ';
				if($data <> "" && sizeof($data) > 0 ) 
				{
						if((isset($data['from_date']) && $data['from_date'] <> "" ) && (isset($data['to_date']) && $data['to_date'] <> ""))
						{
							$sql2 .= '  and DATE(`SentBillSMSDate`)   between "'.getDBFormatDate($data['from_date']) .'" and  "'.getDBFormatDate($data['to_date']).'"   ';	
						}
				}
					//echo '<br/>'.$sql;
				
				 $smsNotifiyhistory = $this->m_dbConn->select($sql2);
				 
				//var_dump($smsNotifiyhistory);
				 if(sizeof($smsNotifiyhistory) > 0 )
				 {
					   for($m = 0 ;$m < sizeof($smsNotifiyhistory);$m++)
					  {
							$smsNotifiyhistory[$m]['UnitID'] =  $ledArr[$smsNotifiyhistory[$m]['UnitID']];
							$smsOrgText = array("totalBillPayableField", "unitNoField");
							$replaceSmsWith   = array($smsNotifiyhistory[$m]['Bill_Amount'],$smsNotifiyhistory[$m]['UnitID']);
							//$mailBody = str_replace("totalBillPayableField",$smsNotifiyhistory[$m]['Bill_Amount'],$smsNotifiyhistory[$m]['MessageText']);
							//$mailBody = str_replace("unitNoField",$smsNotifiyhistory[$m]['UnitID'],$smsNotifiyhistory[$m]['MessageText']);
							$mailBody  = str_replace($smsOrgText, $replaceSmsWith, $smsNotifiyhistory[$m]['MessageText']);
						   $smsNotifiyhistory[$m]['MessageText'] =  $mailBody;
							$smsNotifiyhistory[$m]['SentBy']  = $loginArr[$smsNotifiyhistory[$m]['SentBy']];
							 $smsNotifiyhistory[$m]['status'] =  "Auto";
							
							$messageReport = "";
							if(stripos($smsNotifiyhistory[$m]['SentReport'], 'Invalid ')  == false || stripos($smsNotifiyhistory[$m]['SentReport'], 'Empty') == false)
							{
								$messageReport = explode(',', $smsNotifiyhistory[$m]['SentReport']);
							}
							
							if($smsNotifiyhistory[$m]['SentReport'] <> "")
							{
								$messageReport = explode(',', $smsNotifiyhistory[$m]['SentReport']);
								if(stripos($smsNotifiyhistory[$m]['SentReport'], 'Invalid ')  !== false) 
								{
									$smsNotifiyhistory[$m]['status'] =  "Invalid Mobile";
								}
								if(stripos($smsNotifiyhistory[$m]['SentReport'], 'Empty')  !== false) 
								{
									$smsNotifiyhistory[$m]['status'] =  "Empty Mobile No";
								}
								else
								{
									$smsNotifiyhistory[$m]['status'] =  $messageReport[1];
								}
								 
							}
							$DeliveryReport = explode(',', $smsNotifiyhistory[$m]['DeliveryReport']);
							 $smsNotifiyhistory[$m]['DeliveryStatus'] = str_replace('"',"",$smsNotifiyhistory[$m]['DeliveryStatus']);
							
							if($smsNotifiyhistory[$m]['DeliveryStatus']  == "Pending DR not Found" || $smsNotifiyhistory[$m]['DeliveryStatus']  == "DR not Found")
							{
								$smsNotifiyhistory[$m]['DeliveryStatus']  = "Delivered";
							}
							
							 if (in_array('"Not Delivered"', $DeliveryReport))
							{
								$smsNotifiyhistory[$m]['DeliveryReport'] = "";
							}
							else if (stripos( $smsNotifiyhistory[$m]['DeliveryStatus'], 'Deliv') !== false) 
							{
								 $smsNotifiyhistory[$m]['DeliveryReport']= date('d-m-Y H:i:s', strtotime(str_replace('"'," ",$DeliveryReport[5])));
							}
							else if (in_array('"Pending DR not Found"', $DeliveryReport) || in_array('"DR not Found"', $DeliveryReport))
							{
								$smsNotifiyhistory[$m]['DeliveryStatus']  = "Delivered";
								$smsNotifiyhistory[$m]['DeliveryReport'] = "DR Not Available";
							}
							else
							{
								 $smsNotifiyhistory[$m]['DeliveryReport']= $smsNotifiyhistory[$m]['DeliveryReport'];
							}
							
							if( $smsNotifiyhistory[$m]['SentReport'] == "" || $messageReport[2] == "")
							{
								 $smsNotifiyhistory[$m]['View'] = " ";
							}
							else if( $smsNotifiyhistory[$m]['DeliveryStatus'] <>  "" ||  $smsNotifiyhistory[$m]['DeliveryStatus'] == "Delivered") 
							{
								 $smsNotifiyhistory[$m]['View'] = "";
							}
							else if(stripos($smsNotifiyhistory[$m]['SentReport'], 'Invalid ')  !== false || stripos($smsNotifiyhistory[$m]['SentReport'], 'Empty')  !== false) 
							{
								 $smsNotifiyhistory[$m]['View'] = "";
							}
							else  if($messageReport[2] <> "")
							{					
								 $smsNotifiyhistory[$m]['View'] = '<a onClick="GetDeliveryStatus(' . $messageReport[2] . ','. $smsNotifiyhistory[$m]['ID'].','."0".','.SMS_TYPE_BILL_NOTIFICATION_MANUALLY.' )" style="cursor:pointer;">Refresh Status</a>';
							}
							   
							unset($smsNotifiyhistory[$m]['SentReport']);		 
							unset($smsNotifiyhistory[$m]['PeriodID']);	
							unset($smsNotifiyhistory[$m]['Bill_Amount']);		 
							array_push($history,$smsNotifiyhistory[$m]);
					  }
					
				 }
			}
			
			if(isset($history))
			{
				$thheader = array('Unit','Sending Time','Message Text','Sent By', 'Sent Status', 'Delivery Status','Delivery Time','Refresh');
				 $this->display_pg->th		= $thheader;	
				$res =  $this->display_pg->display_datatable($history, false, false);
				return $res;
			}
			
		}
		

		
	}
?>