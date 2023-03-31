<?php	include_once "../classes/import.class.php" ;
		include_once("../classes/include/dbop.class.php");
	  	$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		$validator = "";
		try
		{
			$obj_import=new import($dbConnRoot,$dbConn);
			$dbConnRoot->begin_transaction();
			$dbConn->begin_transaction();
			$validator=$obj_import->ImportData();
			$ErrorLog=$obj_import->ErrorLogFile;
			$dbConnRoot->commit();
			$dbConn->commit();
		}
		catch(Exception $exp)
		{
			$dbConnRoot->rollback();
			$dbConn->rollback();
			
			$validator = $exp;
		}
		
?>


<html>
<body>
<form name="Goback" method="post" action="<?php echo $obj_import->actionPage; ?>">

	<?php
	
	//echo  "ErrorLog File Name:".$ErrorLog;
	$ShowData = "";
	if($validator=="Exist")
	{
		foreach($_POST as $key=>$value)
		{
			echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
		}
		$validator="Society Already Exist...";
		$ShowData=$validator;
		
	}
	else
	{
		$ShowData=$validator;
		//echo $ShowData;	
	}

	
	?>
<input type="hidden" name="ShowData" value="<?php echo $ShowData; ?>">
<input type="hidden" name="mm">
<input type="hidden" name="Import" value="Import">
</form>
<script>
	window.open("<?php echo $ErrorLog ?>");
	document.Goback.submit();
</script>
</body>
</html>

