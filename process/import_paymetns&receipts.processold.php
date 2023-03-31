<?php	
//echo "try";
include_once "../classes/receipt_import.class.php" ;
//echo "try2";		
		include_once("../classes/payments_import.class.php") ;
//echo "try3";		
		include_once("../classes/include/dbop.class.php");
//echo "try4";	  	
		$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		//echo "try";
		$obj_import='';
		//echo "test";
		//echo "test";
		if(isset($_POST['type']) && $_POST['type']== 'payment' )
		{
			//echo "Payments";
			//echo "SocietyID:".$_POST['sid'];
			$obj_import=new paymentImport($dbConnRoot,$dbConn);
			//echo "pend";
			$validator=$obj_import->ImportData($_POST['sid']);
		}
		else if(isset($_POST['type']) && $_POST['type']== 'receipts' )
		{
			$obj_import=new receiptImport($dbConnRoot,$dbConn);
			$validator=$obj_import->ImportData($_POST['sid']);	
		}
		//echo "end";
			
?>


<html>
<body>
<form name="Goback" method="post" action="<?php echo $obj_import->actionPage; ?>">
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
	document.Goback.submit();
</script>
</body>
</html>

