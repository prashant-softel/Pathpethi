<?php	
//echo "try";
include_once("../classes/billdetails_import.class.php"); 
include_once("../classes/include/dbop.class.php");
		$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		$ErrorLog='';
		$actionPage="";
		$obj_opImport = new billdetails_import($dbConnRoot,$dbConn);
		//$errofile_name = 'import_openingbalance_errorlog_'.date("dmY").'_'.rand().'.html';
		//$errorfile = fopen($errofile_name, "a");
		$validator = $obj_opImport->ImportData($_SESSION['society_id']);
		//$validator = $obj_opImport->UploadData($_FILES['upload_files']['tmp_name'][0],$errofile_name);
	
		$actionPage = $obj_opImport->actionPage;
		$ErrorLog = $obj_opImport->errorLog;
		
		//echo $validator;
		
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

