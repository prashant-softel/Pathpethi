<?php include_once("../classes/account_subcategory.class.php");
	  include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
	  $obj_account_subcategory = new account_subcategory($dbConn);
	  $validator = $obj_account_subcategory->startProcess();
?>

<html>
<body>
<font color="#FF0000" size="+2">Please Wait...</font>

<form name="Goback" method="post" action="<?php echo $obj_account_subcategory->actionPage; ?>">
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
	else if($validator=="LedgerUpdate")
	{?>
		<script>
			window.close();
		</script>
     <?php
		$ShowData = "Record Edited Successfully";
	}
	else
	{
		if($obj_account_subcategory->isCatError == true)
		{
			?>
        		<script>alert("<?php echo $validator; ?>");</script>
        	<?php
		}
		else
		{
			foreach($_POST as $key=>$value)
			{
				echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
			}
			$ShowData = $validator;
		}
	}
	?>

<input type="hidden" name="ShowData" value="<?php echo $ShowData; ?>">
</form>

<script>
	document.Goback.submit();
</script>

</body>
</html>
