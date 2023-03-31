<?php include_once("../classes/genbill.class.php");
	  include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
	  $dbConnRoot = new dbop(true);
	  $obj_unit = new genbill($dbConn, $dbConnRoot);
	  $validator = $obj_unit->startProcess();
?>
<html>
<body>
<form name="Goback" method="post" action="<?php echo $obj_unit->actionPage; ?>">

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
	else if($validator == "Generated")
	{
		$ShowData = "Bill Generated Successfully";
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

</form>
<script>
	document.Goback.submit();
</script>
</body>
</html>
