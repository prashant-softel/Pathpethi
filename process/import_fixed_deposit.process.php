<?php	
//echo "try";

include_once("../classes/fixed_deposit_import.class.php"); 
include_once("../classes/include/dbop.class.php");
		$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		$ErrorLog='';
		$actionPage="";
		$obj_fdImport = new fdImport($dbConnRoot,$dbConn);
		$validator = $obj_fdImport->ImportData($_SESSION['society_id']);
		$actionPage = $obj_fdImport->actionPage;
		$ErrorLog = $obj_fdImport->errorLog;
		
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

