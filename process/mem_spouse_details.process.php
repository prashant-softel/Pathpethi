<?php	include_once("../classes/mem_spouse_details.class.php");
		include_once("../classes/include/dbop.class.php");
	 	$dbConn = new dbop();
		$obj_mem_spouse_details = new mem_spouse_details($dbConn);
		$validator = $obj_mem_spouse_details->startProcess();
?>
<html>
<body>
<form name="Goback" method="post" action="<?php echo $obj_mem_spouse_details->actionPage; ?>">

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
<input type="hidden" name="scm">
</form>
<script>
	document.Goback.submit();
</script>
</body>
</html>