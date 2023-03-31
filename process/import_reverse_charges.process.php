<?php	
include_once("../classes/import_reverse_charges.class.php"); 
include_once("../classes/include/dbop.class.php");
		$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		$ErrorLog='';
		$actionPage="";
		$obj_rcImport = new import_reverse_charges($dbConnRoot,$dbConn);
		$validator = $obj_rcImport->CSV_RC_Import();
		$actionPage = $obj_rcImport->actionPage;
		$ErrorLog = $obj_rcImport->errorLog;
		echo $validator;
		
?>


<html>
<body>
<form name="Goback" method="post" action="<?php echo $actionPage ?>">
<?php 
foreach($_POST as $key => $value)
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