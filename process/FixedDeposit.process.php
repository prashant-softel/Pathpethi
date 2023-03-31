<?php include_once("../classes/FixedDeposit.class.php");
	  include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
	  $dbConnRoot = new dbop(true);
	  $obj_FixedDeposit = new FixedDeposit($dbConn,$dbConnRoot);
	  $validator = $obj_FixedDeposit->startProcess();
	  $module = $_REQUEST['module'];
	  if($module == "1")
	  {
		  $obj_FixedDeposit->actionPage = "../UpdateFDInterest.php?edt=".$_POST['id']."&fdreadonly=1&fd_id=".$_POST['ref']."&status=".$_POST['fd_status']."";
	  }
	  else if($module == "2")
	  {
		  $obj_FixedDeposit->actionPage = "../FixedDeposit.php";
	  }
?>

<html>
<body>
<font color="#FF0000" size="+2">Please Wait...</font>

<form name="Goback" method="post" action="<?php echo $obj_FixedDeposit->actionPage; ?>">
	<?php
//echo "validator:" .$validator;
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
	else if($validator=="Renew")
	{
		$ShowData = "FD  Renewed Successfully";
	}
	else if($validator=="Closed")
	{
		$ShowData = "FD  Closed Successfully";
	}
	else
	{
		$_POST['form_error'] = '1';
		foreach($_POST as $key=>$value)
		{
			echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
		}
		
		$ShowData = $validator;
		?>
        
		<script>
		</script>
	<?php }
	?>

<input type="hidden" name="ShowData" value="<?php echo $ShowData; ?>">
</form>

<script>
	document.Goback.submit();
</script>

</body>
</html>
