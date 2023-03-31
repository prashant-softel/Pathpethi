<?php
	//error_reporting(7);
include_once("include/dbop.class.php");
	include_once "dbconst.class.php";

	class MemberDues
	{
		private $m_dbConn;
		public $m_dbConnRoot;
	
		function __construct($dbConn, $dbConnRoot = "")
		{
			//echo "ctor";
			$this->m_dbConn = $dbConn;
			$this->m_dbConnRoot = $dbConnRoot;
		}
		public function getDueAmountTemp($unitID, $default_StartDate = 0, $_default_EndDate = 0)
		{
			$sql = "SELECT SUM(`Debit`) as Debit , SUM(`Credit`) as Credit, (SUM(Debit) - SUM(Credit)) as Total FROM `assetregister` WHERE `LedgerID` = '".$unitID."'  ";
			if(isset($default_StartDate) && $default_StartDate <> 0  && isset($default_year_end_date) && $default_year_end_date <> 0)
			{
				//$sql .= "  and Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";	
				$sql .= "  and Date <= '".getDBFormatDate($default_year_end_date)."'";									
			}
			$sql .= " GROUP BY LedgerID ";	
			
			echo "<br>due qry:".$sql;

			//$sql = "SELECT * FROM  `commitee` ";		
			//print_r($this->m_dbConn);
			$details = $this->m_dbConn->select($sql);
			print_r($details);
			//$details[0]['Total'] = 0;
			/*$OpeningBalance = $this->getOpeningBalance($unitID , getDBFormatDate($_SESSION['default_year_start_date']));
				
			if($OpeningBalance <> "")
			{
				if($OpeningBalance['OpeningType'] == TRANSACTION_CREDIT)
				{
					$details[0]['Credit'] = $details[0]['Credit'] + $OpeningBalance['Credit']; 		
				}
				else if($OpeningBalance['OpeningType'] == TRANSACTION_DEBIT)
				{
					$details[0]['Debit'] = $details[0]['Debit'] + $OpeningBalance['Debit']; 		
				}
				$details[0]['Total'] =  $details[0]['Debit'] - $details[0]['Credit'];
			}*/
			echo "due:".$details[0]['Total'];
			return $details[0]['Total'];	
		}
	}

	