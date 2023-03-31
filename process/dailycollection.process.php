<?php include_once("../classes/daily_collection.class.php");
	  include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
      $obj_daily_collection = new dailycollection($m_dbConn);
	  $validator = $obj_daily_collection->startProcess();
?>

<html>
<body>
<font color="#FF0000" size="+2">Please Wait...</font>

<form name="Goback" method="post" action="<?php echo $obj_daily_collection->actionPage; ?>">
	<?php

    if($validator=="update")
	{
	$ShowData="Record Updated Successfully";
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
