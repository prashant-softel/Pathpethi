<?php
	include_once("include/dbop.class.php");
	
	class dbManager
	{
		private $m_dbConnRoot;
		
		function __construct()
		{
			$this->m_dbConnRoot = new dbop(true);
		}
		public function getEmptyDBName()
		{	
			$timestamp = DateTime::createFromFormat('U.u', microtime(true));
			$uniqueTime = $timestamp->format("m-d-Y H:i:s.u");
			
			$sql = "Update dbname set locked = '" . $uniqueTime . "' WHERE status = 0 and locked = 0 LIMIT 1";
			$result = $this->m_dbConnRoot->update($sql);
			
			$dbName = '';
			
			if($result > 0) //Blank DB available
			{
				$sqlSelect = "Select dbname from dbname where locked = '" . $uniqueTime . "'";
				$sqlResult = $this->m_dbConnRoot->select($sqlSelect);
				$dbName = $sqlResult[0]['dbname'];
			}
			
			return $dbName;
		}
	}
	
?>