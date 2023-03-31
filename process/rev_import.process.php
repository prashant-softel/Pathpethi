<?php	include_once("../classes/rev_import.class.php");
		include_once("../classes/include/dbop.class.php");
	  	$dbConn = new dbop();
		$obj_rev_import=new rev_import($dbConn);
		$validator = $obj_rev_import->ReverseImport();
?>
<html>
<body>
<form name="Goback" method="post" action="<?php echo $obj_rev_import->actionPage; ?>">

	<?php

	
	if($validator=="Delete")
	{
	$ShowData="Record Deleted Successfully";
	}
	else
	{
		foreach($_POST as $key=>$value)
		{
		echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
		}
		$ShowData=$validator;
	}
	?>

<input type="hidden" name="ShowData" value="<?php echo $ShowData; ?>">
<input type="hidden" name="mm">
</form>
<script>
	document.Goback.submit();
</script>
</body>
</html>
