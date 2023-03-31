<?php if(!isset($_SESSION)){ session_start(); }

//echo $_SERVER['HTTP_HOST'];
if($_SERVER['HTTP_HOST']=="localhost" || $_SERVER['HTTP_HOST']=="patpedhi.com")
{
	define("DB_HOST","localhost");
	define("DB_USER","root");
	define("DB_PASSWORD","");
	//define("DB_DATABASE","societies");
	define("DB_DATABASE",$_SESSION['dbname']);
	
	define("DB_HOST_ROOT","localhost");
	define("DB_USER_ROOT","root");
	define("DB_PASSWORD_ROOT","");
	define("DB_DATABASE_ROOT","hostmjbt_ppdb");
}
else
{
	$set = 2;
	
	if($set == 1)
	{
		//echo "set is 1";
		define("DB_HOST","localhost");
		define("DB_USER","attuit_societies");	
		define("DB_PASSWORD","societies@123#");	
		define("DB_DATABASE","attuit_societies");	
		
		define("DB_HOST_ROOT","localhost");
		define("DB_USER_ROOT","attuit_societies");	
		define("DB_PASSWORD_ROOT","societies@123#");	
		define("DB_DATABASE_ROOT","attuit_societies");	
	}
	else
	{
		//echo "set is else";
		define("DB_HOST","localhost");
		define("DB_USER","root");
		define("DB_PASSWORD","aws123");
		define("DB_DATABASE",$_SESSION['dbname']);
		
		define("DB_HOST_ROOT","localhost");
		define("DB_USER_ROOT","root");
		define("DB_PASSWORD_ROOT","aws123");
		define("DB_DATABASE_ROOT","hostmjbt_societydb");
	}
}
?>