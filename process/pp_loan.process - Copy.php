<?php	
	include_once("../classes/pp_deposits.class.php");
	include_once("../classes/include/dbop.class.php");
  	$dbConn = new dbop();
	$dbConnRoot = new dbop(true);

	$obj_deposits = new pp_deposits($dbConn, $dbConnRoot);
	$validator = $obj_loan->startProcess();
?>
<html>
<body>

<form name="Goback" method="post" action="<?php echo $obj_deposits->actionPage; ?>">

	<?php

	if($validator=="Insert")
	{
	$ShowData="Record Added Successfully";
	}
	else if($validator=="Update")
	{
	$ShowData="Record Updated Successfully";
	}
	else if($validator=="Delete")
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