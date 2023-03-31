<?php include_once("../classes/include/dbop.class.php");
		include_once("../classes/bank_reconciliation.class.php");
	  
	  $dbConn = new dbop();
	  $obj_bank_reco = new bank_reconciliation($dbConn);
	  //echo "reqprocess:".$_POST["ledgerID"];	  
	  $validator = $obj_bank_reco->startProcess();
	  //$validator = "Insert";	  
?>
<html>
<body>
<form name="Goback" method="post" action="../bank_reconciliation.php">

	<?php

	if($validator=="Insert")
	{		
		$ShowData="Record Added Successfully";		
	}	
	else
	{		
		foreach($_POST as $key=>$value)
		{
			if($key == "ledgerID")
			{			
				echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
			}
		}
		$ShowData=$validator;
	}
	?>

<input type="hidden" name="ShowData" value="<?php echo $ShowData; ?>">

</form>
<script>	
	//window.location.href = "../bank_reconciliation.php?LedgerID=347"; 
	document.Goback.submit();
</script>
</body>
</html>
