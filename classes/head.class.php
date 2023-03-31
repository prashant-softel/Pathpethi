<?php
class head 
{
	
	public $m_dbConnRoot;
	public $m_dbConn;
	
	function __construct($dbConnRoot,$dbConn = '')
	{
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
	}
	public function GetSocietyName($SocietyID)
	{
		$RetVal = $this->m_dbConnRoot->select("select society_name from society where society_id=". $SocietyID);
		return $RetVal[0]['society_name'];
	}
	
	public function GetYearDesc($YearID)
	{
		$SqlVal = $this->m_dbConn->select("SELECT `YearDescription` FROM `year` where `YearID`=". $YearID);
		return $SqlVal[0]['YearDescription'];
	}
}
?>