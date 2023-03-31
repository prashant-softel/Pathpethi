<?php
	
	class latestCount
	{
		public $m_dbConn;
		
		function __construct($dbConn)
		{
			$this->m_dbConn = $dbConn;
		}
		
		function getLatestVoucherNo($society_id, $bAutoIncrement = true/*Sets next voucher id to DB */, $periodID = '')
		{
			$sqlSelect = "Select voucher_no from counter where society_id='" . $society_id . "'";
			
			$sqlResult = $this->m_dbConn->select($sqlSelect);
			
			$sqlCounter = $sqlResult[0]['voucher_no'];
			
			if($bAutoIncrement == true)
			{
				$this->updateLatestVoucherNo($society_id, ($sqlCounter + 1));
			}
			
			return $sqlCounter;
		}
		
		function getLatestBillNo($society_id, $bAutoIncrement = true/*Sets next bill no to DB */, $periodID = '')
		{
			$sqlSelect = "Select bill_no from counter where society_id='" . $society_id . "'";
			$sqlResult = $this->m_dbConn->select($sqlSelect);
			
			$sqlCounter = $sqlResult[0]['bill_no'];
			
			if($bAutoIncrement == true)
			{
				$this->updateLatestBillNo($society_id, ($sqlCounter + 1));
			}
			
			return $sqlCounter;
		}
		
		function getLatestReceiptNo($periodID = '', $societyID = '')
		{
			return 1;
		}
		
		function updateLatestVoucherNo($society_id, $nextVoucherNo, $periodID = '')
		{
			$sqlUpdate = "UPDATE `counter` SET `voucher_no`='"  . $nextVoucherNo . "' WHERE society_id='" . $society_id . "'";
			$sqlResult = $this->m_dbConn->update($sqlUpdate);
		}
		
		function updateLatestBillNo($society_id, $nextBillNo, $periodID = '')
		{
			$sqlUpdate = "UPDATE `counter` SET `bill_no`='"  . $nextBillNo . "' WHERE society_id='" . $society_id . "'";
			$sqlResult = $this->m_dbConn->update($sqlUpdate);			
		}
		
		function updateLatestReceiptNo($periodID = '', $societyID = '')
		{
			
		}
		
		function getLatestRequestNo($society_id)
		{
			$sqlSelect = "select MAX(`request_no`) as max from `service_request` where `society_id`='" .$society_id."'" ;	
			$sqlResult = $this->m_dbConn->select($sqlSelect);	
			//$length = sizeof($sqlResult);
			//echo "length: ".$length;
			$sqlCounter = $sqlResult[0]['max'];			
			
			
			//echo "count: ".$sqlCounter;
				$sqlCounter = $sqlCounter + 1;
				//$sqlUpdate = "UPDATE `counter` SET `request_no`='"  . $sqlCounter . "' WHERE society_id='" . $society_id . "'";
				//echo $sqlUpdate;
				//$sqlResult = $this->m_dbConn->update($sqlUpdate);								
			//echo "count2: ".$sqlCounter;
			return $sqlCounter;
		}
	}
?>