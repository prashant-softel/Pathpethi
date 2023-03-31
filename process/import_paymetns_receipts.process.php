<?php	
//echo "try";
include_once("../classes/payments_import.class.php"); 
include_once("../classes/receipt_import.class.php");
include_once("../classes/include/dbop.class.php");
		$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		$ErrorLog='';
		$actionPage="";
		if(isset($_POST['type']) && $_POST['type']== 'payment' )
		{
			$obj_paymentImport=new paymentImport($dbConnRoot,$dbConn);
			$validator=$obj_paymentImport->ImportData($_POST['sid']);
			$actionPage=$obj_paymentImport->actionPage;
			$ErrorLog=$obj_paymentImport->errorLog;
		}
		else if(isset($_POST['type']) && $_POST['type']== 'receipts' )
		{
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
			$obj_receiptImport=new receiptImport($dbConnRoot,$dbConn);
			$validator=$obj_receiptImport->ImportData($_POST['sid']);	
			$actionPage=$obj_receiptImport->actionPage;
			$ErrorLog=$obj_receiptImport->errorLog;
		}
		//echo "end";
			
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

