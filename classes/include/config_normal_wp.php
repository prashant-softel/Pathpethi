<?php
	if($_SERVER['HTTP_HOST']=="localhost")
	{
		mysql_connect('localhost','root','')or die('Connection Fail');
		mysql_select_db('wordpress')or die('Could not connect to local database');
	}
	else
	{
		$pp = 2;
	
		if($pp == 1)
		{		
			mysql_connect('localhost','attuit_docpro123','docpro@123#')or die('Connection Fail');
			mysql_select_db('attuit_docpro')or die('Could not connect to database');			
		}
		else
		{
			mysql_connect('localhost','root','aws123')or die('Connection Fail');
			mysql_select_db('hostmjbt_way2soc')or die('Could not connect to database');				
		}
	}
?>
