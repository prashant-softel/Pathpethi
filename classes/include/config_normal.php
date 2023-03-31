<?php
	
	if($_SERVER['HTTP_HOST']=="localhost")
	{
		mysql_connect('localhost','root','')or die('Connection Fail');
		mysql_select_db('societies')or die('Could not connect to local database');
	}
	else
	{
		
		$set = 2;
		
		if($set == 1)
		{
			mysql_connect('localhost','attuit_societies','societies@123#')or die('Connection Fail');
			mysql_select_db('attuit_societies')or die('Could not connect to database');		
		}
		else
		{
			mysql_connect('localhost','hostmjbt_society','society123')or die('Connection Fail');
			mysql_select_db('hostmjbt_societies')or die('Could not connect to database');		
		}
	}
?>
