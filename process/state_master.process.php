<html>
<body>
<?php
 include_once("../classes/state_master.class.php");
 	include_once("../classes/include/dbop.class.php");
	$dbConn = new dbop();
	$obj_state_master=new state_master($dbConn);
	$validator = $obj_state_master->startProcess();
?>

<form name="Goback" method="post" action="<?php echo $obj_state_master->actionPage; ?>">

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
</form>
<script>
	document.Goback.submit();
</script>
</body>
</html>
