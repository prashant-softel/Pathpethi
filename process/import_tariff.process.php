<?php	
//echo "try";

include_once("../classes/tarrif_import.class.php"); 
include_once("../classes/include/dbop.class.php");
		$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		$ErrorLog='';
		$actionPage="";
		$obj_tImport = new tarrif_import($dbConnRoot,$dbConn);
		//echo "calling import:";
		$validator = $obj_tImport->CSVTarrifImport();
		//$validator = $obj_tImport->DownloadCSV();
		//echo "import done";
		$actionPage = $obj_tImport->actionPage;
		$ErrorLog = $obj_tImport->errorLog;
		echo "Error log:".$ErrorLog;
		echo $validator;
		
?>


<html>
<body>
<form name="Goback" method="post" action="<?php echo $actionPage ?>">
<?php 
foreach($_POST as $key=>$value)
{
	echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
}
$ShowData = $validator;
?>
<input type="hidden" name="ShowData" value="<?php echo $ShowData; ?>">

</form>
<script>
	window.open("<?php echo $ErrorLog ?>");
	document.Goback.submit();
</script>
</body>
</html>

