<?php
class head_s extends dbop
{
	public $m_dbConn;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
	}
	public function GetSocietyName($SocietyID)
	{
		$RetVal = $this->m_dbConn->select("select society_name from society where society_id=". $SocietyID);
		return $RetVal[0]['society_name'];
	}
}
?>