<?php
 	
	echo "incld";
	include_once('DbConnection1.class.php');
	date_default_timezone_set('Asia/Kolkata');	
	echo "trace";
	class dbop extends DbConnection
	{	
		//public $obj_con;
		private $m_bIsTransaction;
		
		function __construct($bAccessRoot = false , $dbName = "")
		{
			DbConnection::__construct($bAccessRoot , $dbName);
			echo "trace2";
			$this->m_bIsTransaction = false;
			mysqli_autocommit($this->mMysqli, true);
			echo "trace3";
		}	
		
		
	}
?>