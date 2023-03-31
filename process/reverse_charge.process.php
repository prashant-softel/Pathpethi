<?php
/*include_once("../classes/tarrif_import.class.php"); 
include_once("../classes/include/dbop.class.php");
		$dbConn = new dbop();
		$dbConnRoot = new dbop(true);		
		$actionPage="";
		$obj_dImport = new tarrif_import($dbConnRoot,$dbConn);
		$validator = $obj_dImport->DownloadCSV();		
		$actionPage = $obj_dImport->actionPage;*/
		header("location: ../reverse_charges.php?&uid=".$_REQUEST['uid']);
?>
<html>
<body>
<form name="Goback" method="post" action="<?php //echo $actionPage ?>">
<?php 
/*foreach($_POST as $key=>$value)
{
	echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
}
$ShowData = $validator;*/
?>
<input type="hidden" name="ShowData" value="<?php //echo $ShowData; ?>">
</form>
<script>	
	//document.Goback.submit();
</script>
</body>
</html>

