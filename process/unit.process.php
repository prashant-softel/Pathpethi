<?php	include_once("../classes/unit.class.php");
		include_once("../classes/include/dbop.class.php");
	  	$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		$obj_unit=new unit($dbConn,$dbConnRoot);
		$validator = $obj_unit->startProcess();
?>
<html>
<body>
<form name="Goback" method="post" action="<?php echo $obj_unit->actionPage; ?>">

	<?php
	if($validator=="Insert")
	{
	$ShowData="Record Added Successfully";
	}
	else if($validator=="Update")
	{
		$uid = $_POST['uid'];
		echo "<input type=\"hidden\" name=\"uid\" value=\"$uid\" />";
		$ShowData="Record Updated Successfully";
	}
	else if($validator=="Delete")
	{
		$ShowData="Record Deleted Successfully";
	}
	else
	{
		$_POST['form_error'] = '1';
		foreach($_POST as $key=>$value)
		{
		echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
		}
		$ShowData=$validator;
	}
	?>

<input type="hidden" name="ShowData" value="<?php echo $ShowData; ?>">
<input type="hidden" name="imp">

<?php if($_POST['ssid']<>"" && $_POST['wwid']<>""){?>
<input type="hidden" name="ws">
<input type="hidden" name="ssid" value="<?php echo $_POST['ssid'];?>">
<input type="hidden" name="wwid" value="<?php echo $_POST['wwid'];?>">
<input type="hidden" name="idd" value="<?php echo time();?>">
<?php } ?>

</form>
<script>
	document.Goback.submit();
</script>
</body>
</html>
