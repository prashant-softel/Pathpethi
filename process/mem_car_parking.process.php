<?php	include_once("../classes/include/dbop.class.php");
	  	$dbConn = new dbop();
		
		if($_POST['vehicle_type'] == '2')
		{
			include_once("../classes/mem_bike_parking.class.php");
			$obj_mem_bike_parking=new mem_bike_parking($dbConn);
			$validator = $obj_mem_bike_parking->startProcess();
		}
		else if($_POST['vehicle_type'] == '4')
		{
			include_once("../classes/mem_car_parking.class.php");
			$obj_mem_car_parking=new mem_car_parking($dbConn);
			$validator = $obj_mem_car_parking->startProcess();
		}
		echo $obj_mem_car_parking->actionPage;

?>
<html>
<body>
<form name="Goback" method="post" action="<?php echo $obj_mem_car_parking->actionPage; ?>">

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
<input type="hidden" name="scm">
</form>
<script>
	document.Goback.submit();
</script>
</body>
</html>
