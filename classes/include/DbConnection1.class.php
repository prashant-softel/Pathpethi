<?php
echo "include config";
include_once("config.php");
echo "config after";
class DbConnection
{
	public $mMysqli;
	public $isConnected = true;
	public $connErrorMsg ='';
	
	function __construct($bAccessRoot = false , $dbName = "")
	{
		echo "ctr dbconn name:". $dbName ;
		if($bAccessRoot == true)
		{
			$this->mMysqli = new mysqli(DB_HOST_ROOT, DB_USER_ROOT, DB_PASSWORD_ROOT, DB_DATABASE_ROOT);
		}
		else
		{
			echo '<br> Session DB :' . DB_DATABASE;
			if($dbName <> "")
			{
				$this->mMysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, $dbName);
				if($this->mMysqli->connect_errno)
				{
					$this->isConnected = false;
					$this->connErrorMsg = $this->mMysqli->connect_error;
				}
			}
			else
			{
				$this->mMysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
			}
		}
	}
		
	function __destruct()
	{
		if(!is_null($this->mMysqli))
		{
			$bConnected = mysqli_ping($this->mMysqli) ? true : false;
			if($bConnected == true)
			{
				$this->mMysqli->close();		
				/*echo '<script>alert("Connected Closed");</script>';*/
			}
		}
	}
}
?>