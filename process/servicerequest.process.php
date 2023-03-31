<?php	
include_once("../classes/include/dbop.class.php");
include_once("../classes/servicerequest.class.php");		
	$dbConn = new dbop();
	$obj_servicerequest=new servicerequest($dbConn);
	$actionPage = "";
	if(isset($_REQUEST['vr']))
	{			
		$validator = $obj_servicerequest->insertComments($_REQUEST['vr'], $_POST['emailID'],$_POST['SREmailIDs']);
		$actionPage = "../viewrequest.php?rq=".$_REQUEST['vr'];
	}
	else
	{		
		$validator = $obj_servicerequest->startProcess();
		$actionPage = "../servicerequest.php";
	}
	//$actionPage = "../Complaints_s.php";
	//$actionPage = "../viewrequest.php?rq=".$_REQUEST['vr'];	
?>
<html>
<body>
<form name="Goback" method="post" action="<?php echo $actionPage; ?>">

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
		
	/*	foreach($_POST as $key=>$value)
		{
		echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
		}
		$ShowData=$validator;	*/	
	}
	?>

<input type="hidden" name="ShowData" value="<?php echo $ShowData; ?>">
<input type="hidden" name="mm">
</form>
<script>
	document.Goback.submit();
	//window.location.href = "../viewrequest.php";
</script>
</body>
</html>
