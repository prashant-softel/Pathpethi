<?php	//include_once("../classes/document.class.php");
		include_once "../classes/doc.class.php" ;
		include_once("../classes/include/dbop.class.php");
	 	//$dbConn = new dbop();
	 	$dbopRoot = new dbop(true);
		$soc_id = $_SESSION["society_id"];
		$sqlSelect1 = "select * from `dbname` where `society_id`='".$soc_id."'";
		$resDBName = $dbopRoot->select($sqlSelect1);
		
		$objdbop = new dbop(false, $resDBName[0]["dbname"]);
		
		$obj_document=new document($objdbop);
		//echo "4";
		$validator = $obj_document->startProcess();
		//echo "5";
?>

<html>
<body>

<form name="Goback" method="post" action="../GDrive_view.php?Mode=1<?php //echo $obj_document->actionPage; ?>">

	<?php

	if($validator=="Upload")
	{
	$ShowData="File is valid, and was successfully uploaded.";
	}
	else if($validator=="Error")
	{
	$ShowData="An error ocurred when uploading.";
	}
	else if($validator=="Exists")
	{
	$ShowData="File with that name already exists.";
	}
	else if($validator=="NotUploaded")
	{
	$ShowData="Error uploading file - check destination is writeable.";
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
