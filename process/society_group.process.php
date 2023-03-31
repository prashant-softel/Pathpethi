<?php	include_once("../classes/society_group.class.php");
		include_once("../classes/include/dbop.class.php");
	  	$dbConn = new dbop();
		$obj_society_group=new society_group($dbConn);
		$validator = $obj_society_group->startProcess();
?>
<html>
<body>
<form name="Goback" method="post" action="<?php echo $obj_society_group->actionPage; ?>">

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
<input type="hidden" name="grp">
</form>
<script>
	document.Goback.submit();
</script>
</body>
</html>
