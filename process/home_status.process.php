<?php	include_once("../classes/home_status.class.php");
		include_once("../classes/include/dbop.class.php");
	 	$dbConn = new dbop();
		$obj_home_status=new home_status($dbConn);
		$validator = $obj_home_status->startProcess();
?>
<html>
<body>
<form name="Goback" method="post" action="<?php echo $obj_home_status->actionPage; ?>">

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
