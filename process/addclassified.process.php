<?php include_once("../classes/addclassified.class.php");
	 include_once("../classes/include/dbop.class.php");
	   	$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
	    $obj_classified = new classified($dbConnRoot, $dbConn);
		
		if(isset($_REQUEST['comments']))
	{			
		$validator = $obj_classified->insertComments($_REQUEST['id']);
		$actionPage = "../my_listing_classified.php";
		$validator = "Update";
	}
	else
	{		
		$validator = $obj_classified->startProcess();
		$actionPage = "../my_listing_classified.php";
	}
  
	   // $validator = $obj_classified->startProcess();
	echo $validator;
?>

<html>
<body>
<font color="#FF0000" size="+2">Please Wait...</font>

<form name="Goback" method="post" action="<?php echo $obj_classified->actionPage; ?>">
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
		
		if( $obj_classified->errormsg <> '')
		{
			echo "<input type=\"hidden\" name=\"error\" value=\"$obj_classified->errormsg\" />";
		}
		if( $obj_classified->errormsg1 <> '')
		{
			echo "<input type=\"hidden\" name=\"error1\" value=\"$obj_classified->errormsg1\" />";
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
