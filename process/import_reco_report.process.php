<?php	
echo "try";
include_once("../classes/import_reco_report.class.php"); 
//include_once("../classes/receipt_import.class.php");
include_once("../classes/include/dbop.class.php");
		$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		$ErrorLog='';
		$actionPage = "";
		$_REQUEST['TranxType'] = strtolower($_REQUEST['TranxType']);
		echo "start".$_REQUEST['TranxType'];
		//die();
		if((isset($_REQUEST['TranxType']) && strtolower($_REQUEST['TranxType']) == 'receipts' || ($_REQUEST['TranxType']) == 'payments' ))
		{
			echo "mid";
			//die();
			$obj_Import_reco = new ImportRecoReport($dbConnRoot,$dbConn);
			echo "mid2";
			$validator=$obj_Import_reco->ImportData($_POST['sid']);
			$actionPage=$obj_Import_reco->actionPage;
			$ErrorLog=$obj_Import_reco->errorLog;
		}
		echo "end";
			
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

