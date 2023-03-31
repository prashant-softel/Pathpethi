<?php	include_once("../classes/include/dbop.class.php"); 
	include_once("../classes/createvoucher.class.php");
	  $validator = " ";
	  $dbConn = new dbop();
	  
	  $obj_createvoucher = new createVoucher($dbConn);
	  if($_REQUEST["method"] == "delete")
	  {
		  
		$validator = $obj_createvoucher->DeletedRecord(); 
	  }
	  else
	  {
	  $validator = $obj_createvoucher->startProcess();
	  }		
?>

<html>
<body>
<font color="#FF0000" size="+2">Please Wait...</font>

<form name="Goback" method="post" action="<?php echo $obj_createvoucher->actionPage; ?>">
	<?php

	if($validator=="Insert")
	{
		$ShowData = "Record Added Successfully";
	}
	else if($validator=="Update")
	{
		$ShowData = "Record Updated Successfully";
	}
	else if($validator=="Delete")
	{
		$ShowData = "Record Deleted Successfully";
	}
	else
	{
		foreach($_POST as $key=>$value)
		{
			echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
		}
		$ShowData = $validator;
	}
	?>

<input type="hidden" name="ShowData" value="<?php echo $ShowData; ?>">
</form>

<script>
	document.Goback.submit();
</script>

</body>
</html>