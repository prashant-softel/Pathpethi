<?php	include_once("../classes/add_member_id.class.php");
		include_once("../classes/include/dbop.class.php");
	  	$dbConn = new dbop();
		$obj_add_member_id=new add_member_id($dbConn);
		$validator = $obj_add_member_id->startProcess();
?>

<html>
<body>

<form name="Goback" method="post" action="<?php echo $obj_add_member_id->actionPage; ?>">

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
<input type="hidden" name="as">
</form>
<script>
	document.Goback.submit();
</script>
</body>
</html>
