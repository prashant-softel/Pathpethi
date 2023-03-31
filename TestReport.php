<?php
	include_once ("classes/include/dbop.class.php");
	include_once ("classes/Cleaner.class.php");
	$dbConn = new dbop();
	$obj_cleaner=new Cleaner($dbConn);
	
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>BANK REGISTER ENTRIES VALIDATION REPORT</title>
</head>

<body >
<center><h1>BANK REGISTER ENTRIES VALIDATION REPORT</h1></center>
<?php $obj_cleaner->FetchRecord();?>
</body>
</html>