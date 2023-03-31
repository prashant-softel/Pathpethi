<?php	include_once("../classes/service_prd_reg_edit.class.php");
		include_once("../classes/include/dbop.class.php");
	  	$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		$obj_service_prd_reg=new service_prd_reg($dbConn, $dbConnRoot);
		$validator = $obj_service_prd_reg->startProcess();
?>
<html>
<body>
<form name="Goback" method="post" action="<?php echo $obj_service_prd_reg->actionPage; ?>?id=<?php echo $_POST['id'];?>&srm">

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
<input type="hidden" name="id" value="<?php echo $_POST['id'];?>">
<input type="hidden" name="srm">
</form>
<script>
	document.Goback.submit();
</script>
</body>
</html>
