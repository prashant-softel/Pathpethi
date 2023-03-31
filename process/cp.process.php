<?php	
include_once("../classes/include/dbop.class.php");
include_once("../classes/cp.class.php");		
	$dbConn = new dbop(true);
	$obj_cp=new cp($dbConn);
	$actionPage = "../cp.php";
	$res = "";
	if(isset($_REQUEST["cu"]))
	{ 
		$res = $obj_cp->ChangeUserName();
		$actionPage .= "?res=".$res; 
	}
	
	//echo $actionPage;
	?>
	<html>
<body>
<form name="Goback" method="post" action="<?php echo $actionPage; ?>">

	<?php

	?>

<input type="hidden" name="ShowData"  id="ShowData" value="<?php echo $ShowData; ?>">
<input type="hidden" name="mm">
</form>
<script>
	document.Goback.submit();
	//window.location.href = "../viewrequest.php";
</script>
</body>
</html>
